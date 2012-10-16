<?php
/*
* Created on Feb 27, 2008
*
*  Built for web
*  Fuse IQ -- todd@fuseiq.com
*
*/
require_once ('ReportFilterHelpers.php');
require_once ('models/table/Training.php');
require_once ('models/table/TrainingToTrainer.php');
require_once ('models/table/PersonToTraining.php');
require_once ('models/table/Trainer.php');
require_once ('models/table/Person.php');
require_once ('models/table/Translation.php');
require_once ('models/table/OptionList.php');
require_once ('models/table/MultiAssignList.php');
//require_once ('models/table/Course.php');


class TrainingController extends ReportFilterHelpers {

	private $NUM_PEPFAR = 5; // number of pepfars when "multiple" is allowed


	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		$this->_countrySettings = array();
		$this->_countrySettings = System::getAll();

		$this->_countrySettings['num_location_tiers'] = 2 + $this->_countrySettings['display_region_c'] + $this->_countrySettings['display_region_b']; //including city

		parent::__construct ( $request, $response, $invokeArgs );
	}

	public function init() {
	}

	public function preDispatch() {
		parent::preDispatch ();

		if (! $this->isLoggedIn ())
		$this->doNoAccessError ();

	}

	public function addAction() {
		if (! $this->hasACL ( 'edit_course' )) {
			$this->doNoAccessError ();
		}

		$this->view->assign ( 'pageTitle', t ( 'Add New Training' ) );
		return $this->doAddEditView ();
	}

	public function editAction() {
		if (! $this->hasACL ( 'edit_course' )) {
			$this->doNoAccessError ();
		}

		$this->view->assign ( 'pageTitle', t ( 'View/Edit Training' ) );
		return $this->doAddEditView ();
	}

	public function viewAction() {
		$this->view->assign ( 'pageTitle', t ( 'View/Edit Training' ) );
		return $this->doAddEditView ();
	}

	private function doAddEditView() {
		if (! $this->hasACL ( 'edit_course' )) {
			$this->view->assign ( 'viewonly', 'disabled="disabled"' );
			$this->view->assign ( 'pageTitle', t ( 'View Training' ) );
		}

		// edittable ajax (remove/update/etc)
		if ($this->_getParam ( 'edittable' )) {
			$this->ajaxeditTable ();
			return;
		}

		require_once 'models/table/MultiOptionList.php';
		require_once 'models/table/TrainingLocation.php';
		require_once 'models/table/Location.php';
		require_once 'models/table/System.php';
		require_once 'views/helpers/EditTableHelper.php';
		require_once 'views/helpers/DropDown.php';
		require_once 'views/helpers/FileUpload.php';

		// allow multiple pepfars?
		if (! $this->setting ( 'allow_multi_pepfar' )) {
			$this->NUM_PEPFAR = 1;
		}

		// get translation labels
		// $this->view->assign('labels', Translation::getAll());


		//validate
		$status = ValidationContainer::instance ();

		$request = $this->getRequest ();
		$validateOnly = $request->isXmlHttpRequest ();
		$training_id = $this->getSanParam ( 'id' );
		$is_new = ($this->getSanParam ( 'new' ) || ! $training_id); // new training -- use defaults
		$this->view->assign ( 'is_new', $is_new );

		$trainingObj = new Training ( );
		$row = $trainingObj->findOrCreate ( $training_id );
		$rowRay = $row->toArray ();

		//filter training orgs by user access
		$allowIds = false;
		if (! $this->hasACL ( 'training_organizer_option_all' )) {
			$allowIds = array ();
			$user_id = $this->isLoggedIn ();
			$training_organizer_array = MultiOptionList::choicesList ( 'user_to_organizer_access', 'user_id', $user_id, 'training_organizer_option', 'training_organizer_phrase', false, false );
			foreach ( $training_organizer_array as $orgOption ) {
				if ($orgOption ['user_id'])
				$allowIds [] = $orgOption ['id'];
			}
		}

		if (($this->_getParam ( 'action' ) != 'add') and ! $this->hasACL ( 'training_organizer_option_all' ) and ((! $allowIds) or (array_search ( $rowRay ['training_organizer_option_id'], $allowIds ) === false))) {
			$this->view->assign ( 'viewonly', 'disabled="disabled"' );
			$this->view->assign ( 'pageTitle', t ( 'View Training' ) );
		}

		if ($row->is_deleted) {
			$this->_redirect ( 'training/deleted' );
			return;
		}

		$courseRow = $trainingObj->getCourseInfo ( $training_id );
		$rowRay ['training_title'] = ($courseRow) ? $courseRow->training_title_phrase : '';
		$rowRay ['training_title_option_id'] = ($courseRow) ? $courseRow->training_title_option_id : 0;

		// does not exist
		if (! $row->id && $this->_getParam ( 'action' ) != 'add') {
			$this->_redirect ( 'training/index' );
		}

		if ($validateOnly)
		$this->setNoRenderer ();

		$age_opts = OptionList::suggestionList('age_range_option',array('id','age_range_phrase'), false, 100, false);


		if ($request->isPost () && ! $this->getSanParam ( 'edittabledelete' )) {

			//$status->checkRequired($this, 'training_title_option_id',t('Training Name'));
			//$status->checkRequired($this, 'training_category_and_title_option_id',t('Training Name'));
			$status->checkRequired ( $this, 'training_length_value', t ( 'Training Length' ) );
			$status->checkRequired ( $this, 'training_length_interval', t ( 'Training Interval' ) );
			//$status->checkRequired($this, 'training_organizer_option_id',t('Training Organizer'));
			//$status->checkRequired($this, 'training_level_option_id',t('Training Level'));
			//$status->checkRequired($this, 'training_location_id',t('Training Location'));
			//$status->checkRequired($this, 'training_topic_option_id','Training Topic');


			// May be "0" value
			if (! $this->getSanParam ( 'training_length_value' )) {
				$status->addError ( 'training_length_value', t ( 'Training length is required.' ) );
			}

			// validate score averages values
			if ($score = trim ( $this->getSanParam ( 'pre' ) )) {
				if (! is_numeric ( $score )) {
					$status->addError ( 'pre', $this->view->translation ['Pre Test Score'] . ' ' . t ( 'must be numeric.' ) );
				} elseif ($score < 0 || $score > 100) {
					$status->addError ( 'pre', $this->view->translation ['Pre Test Score'] . ' ' . t ( 'must be between 1-100.' ) );
				}
			}
			if ($score = trim ( $this->getSanParam ( 'post' ) )) {
				if (! is_numeric ( $score )) {
					$status->addError ( 'post', $this->view->translation ['Post Test Score'] . ' ' . t ( 'must be numeric.' ) );
				} elseif ($score < 0 || $score > 100) {
					$status->addError ( 'post', $this->view->translation ['Post Test Score'] . ' ' . t ( 'must be between 1-100.' ) );
				}
			}

			$training_start_date = (@$this->getSanParam ( 'start-year' )) . '-' . (@$this->getSanParam ( 'start-month' )) . '-' . (@$this->getSanParam ( 'start-day' ));
			if ($training_start_date !== '--' and $training_start_date !== '0000-00-00')
			$status->isValidDate ( $this, 'start-day', t ( 'Training start' ), $training_start_date );
			if ($this->setting ( 'display_end_date' )) {
				$training_end_date = (@$this->getSanParam ( 'end-year' )) . '-' . (@$this->getSanParam ( 'end-month' )) . '-' . (@$this->getSanParam ( 'end-day' ));
				if ($training_end_date !== '--' and $training_end_date !== '0000-00-00') {
					$status->isValidDate ( $this, 'end-day', t ( 'Training end' ), $training_end_date );
				}

				if ($training_end_date != '--') {
					if (strtotime ( $training_end_date ) < strtotime ( $training_start_date )) {
						$status->addError ( 'end-day', t ( 'End date must be after start date.' ) );
					}
				}
			}

			if ($training_id) {

				$pepfarCount = 0;
				$pepfar_array = $this->getSanParam ( 'training_pepfar_categories_option_id' );
				if ($pepfar_array) {
					foreach ( $pepfar_array as $p ) {
						if ($p)
						$pepfarCount ++;
					}
				}

				//          if (!$pepfarCount) {
				//            $status->addError('training_pepfar_categories_option',t('PEPFAR is required.'));
				//         }


				// pepfar (multiple days)
				if ($this->getSanParam ( 'pepfar_days' )) {
					$pepfarTotal = 0;
					foreach ( $this->getSanParam ( 'pepfar_days' ) as $key => $value ) {
						if (! is_numeric ( $value ))
						$value = ereg_replace ( "/^[.0-9]", "", $value );
						//$daysRay [$pepfar_array[$key]] = $value; //set the days key to  the pepfar id
						$daysRay [$key] = $value; //set the days key to  the pepfar id
						$pepfarTotal += $value;

						if ($pepfarCount > 1 && ! $value) {
							$status->addError ( 'training_pepfar_categories_option', t ( 'Number of days is required.' ) );
						}

						if ($pepfarCount == $key + 1) {
							break;
						}

					}

					// calculate days
					switch ($this->getSanParam ( 'training_length_interval' )) {
						case 'week' :
						$days = $this->getSanParam ( 'training_length_value' ) * 7;
						break;
						case 'day' :
						$days = $this->getSanParam ( 'training_length_value' ); // start day counts as a day?
						break;
						default :
						$days = 0.5;
						break;
					}

					// do days add up to match training length?
					if ($days != $pepfarTotal && $pepfarCount > 1) {
						$status->addError ( 'training_pepfar_categories_option', sprintf ( t ( "Total training length is %s, but PEPFAR category total is %d days. " ), (($days == 1) ? $days . ' ' . t ( 'day' ) : $days . ' ' . t ( 'days' )), $pepfarTotal ) );
					}

				}

				// custom fields
				if ($this->getSanParam ( 'custom1_phrase' )) {
					$tableCustom = new ITechTable ( array ('name' => 'training_custom_1_option' ) );
					$row->training_custom_1_option_id = $tableCustom->insertUnique ( 'custom1_phrase', $this->getSanParam ( 'custom1_phrase' ), true );
				}
				if ($this->getSanParam ( 'custom2_phrase' )) {
					$tableCustom = new ITechTable ( array ('name' => 'training_custom_2_option' ) );
					$row->training_custom_2_option_id = $tableCustom->insertUnique ( 'custom2_phrase', $this->getSanParam ( 'custom2_phrase' ), true );
				}

				// checkbox
				if (! $this->getSanParam ( 'is_tot' )) {
					$row->is_tot = 0;
				}
				if (! $this->getSanParam ( 'is_refresher' )) {
					$row->is_refresher = 0;
				}

			}

			if (($this->_getParam ( 'action' ) == 'add') && $this->_countrySettings ['module_unknown_participants_enabled'] && (! $this->getSanParam ( 'has_known_participants' ))) {
				$row->has_known_participants = 0;
			} else if ($this->_getParam ( 'action' ) == 'add') {
				$row->has_known_participants = 1;
			}

			//approve by default if the approvals modules is not enabled
			if (($this->_getParam ( 'action' ) == 'add') && $this->_countrySettings ['module_approvals_enabled'] && ! $this->hasACL ( 'approve_trainings' )) {
				$row->is_approved = 0;
			} else if ($this->_getParam ( 'action' ) == 'add') {
				$row->is_approved = 1;
			}

			// delete training
			if ($this->getSanParam ( 'specialAction' ) == 'delete') {
				$partys = PersonToTraining::getParticipants ( $training_id )->toArray ();
				$tranys = TrainingToTrainer::getTrainers ( $training_id )->toArray ();
				if (! $partys && ! $tranys) {
					$row->is_deleted = 1;
					$trainingObj->delete ( 'id = ' . $row->id );
				} else {
					$status->setStatusMessage ( t ( 'This training session could not be deleted. Some participants or trainers may still be attached.' ) );

				}
			}

			if ($status->hasError () && ! $row->is_deleted) {
				$status->setStatusMessage ( t ( 'This training session could not be saved.' ) );
			} else {
				$row = self::fillFromArray ( $row, $this->_getAllParams () );

				// format: categoryid_titleid
				$ct_ids = $this->getSanParam ( 'training_category_and_title_option_id' );

				// remove category id and underscore (unless dynamic title insert, which is numeric)
				$training_title_option_id = (! is_numeric ( $ct_ids )) ? substr ( $ct_ids, strpos ( $ct_ids, '_' ) + 1 ) : $ct_ids;
				$row->training_title_option_id = $training_title_option_id;

				$row->training_start_date = (@$this->getSanParam ( 'start-year' )) . '-' . (@$this->getSanParam ( 'start-month' )) . '-' . (@$this->getSanParam ( 'start-day' ));
				if ($this->setting ( 'display_end_date' )) {
					$row->training_end_date = (@$this->getSanParam ( 'end-year' )) . '-' . (@$this->getSanParam ( 'end-month' )) . '-' . (@$this->getSanParam ( 'end-day' ));
				}

				// cannot be null ... set defaults
				if (! $row->comments)
				$row->comments = '';
				if (! $row->got_comments)
				$row->got_comments = '';
				if (! $row->objectives)
				$row->objectives = '';
				if (! $row->is_tot)
				$row->is_tot = 0;
				if (! $row->is_refresher)
				$row->is_refresher = 0;

				// update related tables
				if ($training_id) {

					// funding
					$amount_extra_col = '';
					$amount_extra_vals = array ();
					//if ($this->setting ( 'display_funding_amount' ) && $this->getSanParam ( 'funding_id' )) {
					$amount_extra_col = 'funding_amount';
					if ($this->getSanParam ( 'funding_id' )) {
						foreach ( $this->getSanParam ( 'funding_id' ) as $funding_id ) {
							$amount_extra_vals [] = $this->getSanParam ( 'funding_id_amount_' . $funding_id );
						}
					}

					//}


					MultiOptionList::updateOptions ( 'training_to_training_funding_option', 'training_funding_option', 'training_id', $training_id, 'training_funding_option_id', $this->getSanParam ( 'funding_id' ), $amount_extra_col, $amount_extra_vals );

					// pepfar
					MultiOptionList::updateOptions ( 'training_to_training_pepfar_categories_option', 'training_pepfar_categories_option', 'training_id', $training_id, 'training_pepfar_categories_option_id', $this->getSanParam ( 'training_pepfar_categories_option_id' ), 'duration_days', (isset ( $daysRay ) ? $daysRay : false) );

					// method
					if ($this->setting ( 'display_training_method' )) {
						$row->training_method_option_id = $this->getSanParam ( 'training_method_option_id' );
					}

					// topics
					if (! $this->setting ( 'allow_multi_topic' )) {
						// drop-down -- set up faux checkbox array (since table schema uses multiple choices)
						$_GET ['topic_id'] [] = $this->getSanParam ( 'training_topic_option_id' );
					}
					MultiOptionList::updateOptions ( 'training_to_training_topic_option', 'training_topic_option', 'training_id', $training_id, 'training_topic_option_id', $this->getSanParam ( 'topic_id' ) );

					//Qualifications for unknown participants
					if (! $row->has_known_participants) {
						//check for duplicates
						///oooh, compound key = qual + age

						//DELETE EVERYTHING FOR THIS TRAINING
						//START OVER

						$quals = $this->getSanParam ( 'person_qualification_option_id' );
						$quantities_na = $this->getSanParam ( 'qualification_quantity_na' );
						$quantities_male = $this->getSanParam ( 'qualification_quantity_male' );
						$quantities_female = $this->getSanParam ( 'qualification_quantity_female' );
						$age_ranges = $this->getSanParam ( 'age_range_option_id' );

						$qualPlusAgeArray = array();

						//make array of qualifications + age range
						$qualRows = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false );
						foreach($qualRows as $qRow) {
							foreach(array_keys($age_opts) as $age_opt) {
								$qualPlusAgeArray [$qRow['id']][$age_opt] = array('na'=>0,'male'=>0,'female'=>0);
							}
						}

						foreach($quals as $ix => $item) {
							if ( $item) {
								$qualPlusAgeArray[$quals[$ix]][$age_ranges[$ix]]['na'] = $qualPlusAgeArray[$quals[$ix]][$age_ranges[$ix]]['na'] + $quantities_na[$ix];
								$qualPlusAgeArray[$quals[$ix]][$age_ranges[$ix]]['male'] = $qualPlusAgeArray[$quals[$ix]][$age_ranges[$ix]]['male'] + $quantities_male[$ix];
								$qualPlusAgeArray[$quals[$ix]][$age_ranges[$ix]]['female'] = $qualPlusAgeArray[$quals[$ix]][$age_ranges[$ix]]['female'] + $quantities_female[$ix];
							}
						}

						$deleteTable = new ITechTable(array ('name' => 'training_to_person_qualification_option' ));
						$deleteTable->delete('training_id = '.$training_id, true);

						foreach($qualPlusAgeArray as $qkey => $ageRay) {
							foreach($ageRay as $akey => $counts) {
								if ( $counts['na'] || $counts['male'] || $counts['female'] ) {
									MultiOptionList::insertOption('training_to_person_qualification_option', 'training_id', $training_id, 'person_qualification_option_id', $qkey,
									array ('age_range_option_id','person_count_na', 'person_count_male', 'person_count_female' ),
									array('age_range_option_id' => $akey, 'person_count_na' => $counts['na'], 'person_count_male' => $counts['male'], 'person_count_female' => $counts['female']));
								}
							}
						}
					}
				}

				//mark approval status
				$do_save_approval_history = false;
				if ($this->setting ( 'module_approvals_enabled' )) {
					if ($this->getSanParam ( 'approval_status' ) == 'approved') {
						$row->is_approved = 1;
						$rowRay ['is_approved'] = 1;
						$do_save_approval_history = true;
					} else if ($this->getSanParam ( 'approval_status' ) == 'rejected') {
						$row->is_approved = 0;
						$rowRay ['is_approved'] = 0;
						$do_save_approval_history = true;
					}
					if (($this->_getParam ( 'action' ) == 'add') or (! $this->hasACL ( 'approve_trainings' ))) {
						$do_save_approval_history = true;
					}

				}

				if ($this->_getParam ( 'action' ) == 'add') {
					$do_save_approval_history = true;
				}

				if ($row->save ()) {

					//save approval history
					if ($this->getSanParam ( 'approval_comments' ) || $do_save_approval_history) {
						require_once ('models/table/TrainingApprovalHistory.php');
						$history_table = new TrainingApprovalHistory ( );
						$approval_status = ($this->_countrySettings ['module_approvals_enabled'] ? $this->getSanParam ( 'approval_status' ) : 'approved');

						if (! $this->hasACL ( 'approve_trainings' ))
						$approval_status = 'resubmitted';
						$history_data = array ('training_id' => $row->id, 'approval_status' => $approval_status, 'message' => $this->getSanParam ( 'approval_comments' ) );
						$history_table->insert ( $history_data );
					}

					// redirects
					if ($this->_getParam ( 'action' ) == 'add') {
						$status->redirect = Settings::$COUNTRY_BASE_URL . '/training/edit/id/' . $row->id . '/new/1';
					}
					if ($this->_getParam ( 'redirectUrl' )) {
						$status->redirect = $this->_getParam ( 'redirectUrl' );
					}

					// duplicate training
					if ($this->getSanParam ( 'specialAction' ) == 'duplicate') {
						$dupId = $trainingObj->duplicateTraining ( $row->id );
						$status->redirect = Settings::$COUNTRY_BASE_URL . '/training/edit/id/' . $dupId . '/msg/duplicate';
					}

					if (! $status->redirect) {
						$status->setStatusMessage ( t ( 'This training session has been saved.' ) );
					}

				} else {
					error_log ( "Couldn't save training $training_id" );
				}

			}

			if ($validateOnly) {
				$this->sendData ( $status );
			} else {
				$this->view->assign ( 'status', $status );
			}

		}

		//
		// Init view
		//


		//split start date fields
		if (! $row->training_start_date)
		$row->training_start_date = '--'; // empty
		$parts = explode ( ' ', $row->training_start_date );
		$parts = explode ( '-', $parts [0] );
		$rowRay ['start-year'] = $parts [0];
		$rowRay ['start-month'] = $parts [1];
		$rowRay ['start-day'] = $parts [2];

		//split end date fields
		if (! $row->training_end_date)
		$row->training_end_date = '--'; // empty
		$parts = explode ( ' ', $row->training_end_date );
		$parts = explode ( '-', $parts [0] );
		$rowRay ['end-year'] = $parts [0];
		$rowRay ['end-month'] = $parts [1];
		$rowRay ['end-day'] = $parts [2];

		// Drop downs
		//$this->view->assign('dropDownTitle', DropDown::generateHtml('training_title_option','training_title_phrase',$rowRay['training_title_option_id'],($this->hasACL('training_title_option_all')?'training/insert-table':false), $this->view->viewonly,false));
		$this->view->assign ( 'dropDownOrg', DropDown::generateHtml ( 'training_organizer_option', 'training_organizer_phrase', $rowRay ['training_organizer_option_id'], ($this->hasACL ( 'training_organizer_option_all' ) ? 'training/insert-table' : false), $this->view->viewonly, ($this->view->viewonly ? false : $allowIds) ) );
		$this->view->assign ( 'dropDownLevel', DropDown::generateHtml ( 'training_level_option', 'training_level_phrase', $rowRay ['training_level_option_id'], 'training/insert-table', $this->view->viewonly ) );
		$this->view->assign ( 'dropDownGotCir', DropDown::generateHtml ( 'training_got_curriculum_option', 'training_got_curriculum_phrase', $rowRay ['training_got_curriculum_option_id'], 'training/insert-table', $this->view->viewonly ) );
		$this->view->assign ( 'dropDownMethod', DropDown::generateHtml ( 'training_method_option', 'training_method_phrase', $rowRay ['training_method_option_id'], 'training/insert-table', $this->view->viewonly ) );
		$this->view->assign ( 'dropDownPrimaryLanguage', DropDown::generateHtml ( 'trainer_language_option', 'language_phrase', $rowRay ['training_primary_language_option_id'], false, $this->view->viewonly, false, false, array ('name' => 'training_primary_language_option_id' ) ) );
		$this->view->assign ( 'dropDownSecondaryLanguage', DropDown::generateHtml ( 'trainer_language_option', 'language_phrase', $rowRay ['training_secondary_language_option_id'], false, $this->view->viewonly, false, false, array ('name' => 'training_secondary_language_option_id' ) ) );

		//$catTitleArray = OptionList::suggestionList('location_district',array('district_name','parent_province_id'),false,false);

		$this->view->assign( 'age_options', $age_opts);

		// training categories & titles
		$categoryTitle = MultiAssignList::getOptions ( 'training_title_option', 'training_title_phrase', 'training_category_option_to_training_title_option', 'training_category_option' );
		$this->view->assign ( 'categoryTitle', $categoryTitle );

		//get assigned evaluation
		$ev_id = null;
		$ev_to_t_id = null;
		if ($training_id) {
			$evtableObj = new ITechTable ( array ('name' => 'evaluation_to_training' ) );
			$evRow = $evtableObj->fetchRow ( $evtableObj->select ( array ('id', 'evaluation_id' ) )->where ( 'training_id = ' . $training_id ) );
			if ($evRow) {
				$ev_id = $evRow->evaluation_id;
				$ev_to_t_id = $evRow->id;
			}
			$this->view->assign ( 'evaluation_id', $ev_id );
			$this->view->assign ( 'evaluation_to_training_id', $ev_to_t_id );
		}

		// add title link
		if ($this->hasACL ( 'training_title_option_all' )) {
			$this->view->assign ( 'titleInsertLink', " <a href=\"#\" onclick=\"addToSelect('" . str_replace ( "'", "\\" . "'", t ( 'Please enter your new' ) ) . " " . strtolower ( $this->view->translation ['Training Name'] ) . ":', 'select_training_title_option', '" . Settings::$COUNTRY_BASE_URL . "/training/insert-table/table/training_title_option/column/training_title_phrase/outputType/json'); return false;\">" . t ( 'Insert new' ) . "</a>" );
		}

		//Qualifications for unknown participants
		if (! $row->has_known_participants) {
			//count primary qualifications.
			//add a dropdown for each
			$tableObj = new ITechTable ( array ('name' => 'person_qualification_option' ) );
			$qualRows = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false );
			//get values for this training
			$selectedObj = new ITechTable ( array ('name' => 'training_to_person_qualification_option' ) );
			$selectedRows = null;
			if ($training_id) {
				$selectedRows = $selectedObj->fetchAll ( $selectedObj->select ( array ('person_qualification_option_id', 'id', 'person_count_na', 'person_count_male', 'person_count_female', 'age_range_option_id' ) )->where ( 'training_id = ' . $training_id ) );

				$unknownQualDropDowns = array ();



				$qual_row_count = 0;
				foreach ( $selectedRows as $selectedRow ) {
					$selector = array ();

					$selector ['select'] = $this->_generate_hierarchical('select_person_qualification_option_'.$qual_row_count, $qualRows, 'qualification_phrase', $selectedRow->person_qualification_option_id) ;
					$selector ['age_range_option_id'] = $selectedRow->age_range_option_id;
					$selector ['quantity_na'] = $selectedRow->person_count_na;
					$selector ['quantity_male'] = $selectedRow->person_count_male;
					$selector ['quantity_female'] = $selectedRow->person_count_female;
					$unknownQualDropDowns [] = $selector;
					$qual_row_count ++;
				}

				$max_rows = count($qualRows)*3;//should be about 30

				for($i = $selectedRows->count (); $i < $max_rows; $i ++) {
					$selector = array ();
					$selector ['select'] = $this->_generate_hierarchical('select_person_qualification_option_'.$qual_row_count, $qualRows, 'qualification_phrase', -1);
					$selector ['age_range_option_id'] = 1;
					$selector ['quantity_na'] = 0;
					$selector ['quantity_male'] = 0;
					$selector ['quantity_female'] = 0;
					$unknownQualDropDowns [] = $selector;
					$qual_row_count ++;
				}

				$this->view->assign ( 'unknownQualDropDowns', $unknownQualDropDowns );
			}

		}

		// find category based on title
		$catId = 0;
		if ($courseRow && $courseRow->training_title_option_id) {
			foreach ( $categoryTitle as $r ) {
				if ($r ['id'] == $courseRow->training_title_option_id) {
					$catId = $r ['training_category_option_id'];
					break;
				}
			}
		}
		$this->view->assign ( 'dropDownCategory', DropDown::generateHtml ( 'training_category_option', 'training_category_phrase', $catId, false, $this->view->viewonly, false ) );

		//echo '<pre>';
		//print_r($catTitleArray); exit;


		//      $this->view->assign('dropDownProvince', DropDown::generateHtml('location_province','province_name',false,false,$this->view->viewonly));
		//      $this->view->assign('dropDownDistrict', DropDown::generateHtml('location_district','district_name',false,false,$this->view->viewonly));
		$this->viewAssignEscaped ( 'locations', Location::getAll () );

		// Topic drop-down
		if ($training_id) {
			if (! $this->setting ( 'allow_multi_topic' )) {
				$training_topic_id = $trainingObj->getTrainingSingleTopic ( $training_id );
				if ($is_new)
				$training_topic_id = false; // use default
				$this->view->assign ( 'dropDownTopic', DropDown::generateHtml ( 'training_topic_option', 'training_topic_phrase', $training_topic_id, 'training/insert-table', $this->view->viewonly ) );
			} else { // topic checkboxes
				$topicArray = MultiOptionList::choicesList ( 'training_to_training_topic_option', 'training_id', $training_id, 'training_topic_option', 'training_topic_phrase' );
				$this->view->assign ( 'topicArray', $topicArray );
				$this->view->assign ( 'topicJsonUrl', Settings::$COUNTRY_BASE_URL . '/training/insert-table/table/training_topic_option/column/training_topic_phrase/outputType/json' );
			}

		}

		// get custom phrases (custom1_phrase, custom2_phrase)
		if ($training_id) {
			$rowRay = array_merge ( $rowRay, $trainingObj->getCustom ( $training_id )->toArray () );
		}

		// location drop-down
		$tlocations = TrainingLocation::selectAllLocations ( $this->setting ( 'num_location_tiers' ) );
		$this->viewAssignEscaped ( 'tlocations', $tlocations );

		// pepfar durations
		if ($training_id) {
			$pepfarArray = MultiOptionList::choicesList ( 'training_to_training_pepfar_categories_option', 'training_id', $training_id, 'training_pepfar_categories_option', 'pepfar_category_phrase', 'duration_days' );
			foreach ( $pepfarArray as $item ) {
				if (isset ( $item ['training_id'] ) && $item ['training_id']) {
					$pepfars [] = array ('id' => $item ['id'], 'duration' => $item ['duration_days'] );
				}
			}
		}

		// pepfar
		$this->view->assign ( 'NUM_PEPFAR', $this->NUM_PEPFAR ); // number of Pepfar drop-downs to display
		for($j = 0; $j < $this->NUM_PEPFAR; $j ++) {
			$pepfarid = (isset ( $pepfars [$j] ['id'] ) ? $pepfars [$j] ['id'] : '');
			if ($is_new)
			$pepfarid = false; // use default
			$pepfarHtml = DropDown::generateHtml ( 'training_pepfar_categories_option', 'pepfar_category_phrase', $pepfarid, false, $this->view->viewonly, false, false, array (), ($j == 0) );
			$pepfarHtml = str_replace ( 'training_pepfar_categories_option_id', 'training_pepfar_categories_option_id[]', $pepfarHtml ); // use array
			$dropDownPepfars [] = $pepfarHtml;
			//$pepfarDurations[] = (isset($pepfars[$j]['duration']) && $pepfars[$j]['duration'] ? $pepfars[$j]['duration'] . ' day' . (($pepfars[$j]['duration'] <= 1) ? '' : 's') : '');
			$pepfarDurations [] = (isset ( $pepfars [$j] ['duration'] ) && $pepfars [$j] ['duration']) ? $pepfars [$j] ['duration'] : '';
		}
		$this->view->assign ( 'dropDownPepfars', $dropDownPepfars );
		$this->view->assign ( 'pepfarDurations', $pepfarDurations );

		// checkboxes
		$fundingArray = MultiOptionList::choicesList ( 'training_to_training_funding_option', 'training_id', $training_id, 'training_funding_option', array ('funding_phrase', 'is_default' ) );
		if (/*$this->setting ( 'display_funding_amount' ) &&*/ $training_id) {
			//lame to do another query, but it's easy
			$tableObj = new ITechTable ( array ('name' => 'training_to_training_funding_option' ) );
			$amountRows = $tableObj->fetchAll ( $tableObj->select ( array ('training_funding_option_id', 'id', 'funding_amount' ) )->where ( 'training_id = ' . $training_id ) );
			foreach ( $amountRows as $amt_row ) {
				foreach ( $fundingArray as $k => $funding_row ) {
					if ($funding_row ['id'] == $amt_row->training_funding_option_id) {
						$fundingArray [$k] ['funding_amount'] = $amt_row->funding_amount;
					}
				}
			}
		}
		$this->view->assign ( 'fundingArray', $fundingArray );
		$this->view->assign ( 'fundingJsonUrl', Settings::$COUNTRY_BASE_URL . '/training/insert-table/table/training_funding_option/column/funding_phrase/outputType/json' );

		/****************************************************************************************************************
		* Trainers */
		if ($training_id) {
			$trainers = TrainingToTrainer::getTrainers ( $training_id )->toArray ();
		} else {
			$trainers = array ();
		}

		if (! $this->setting ( 'display_middle_name' )) {

			$trainerFields = array ('first_name' => $this->tr ( 'First Name' ), 'last_name' => $this->tr ( 'Last Name' ) );

			$colStatic = array ('first_name', 'last_name' );

		} else if ($this->setting ( 'display_middle_name_last' )) {

			$trainerFields = array ('first_name' => $this->tr ( 'First Name' ), 'last_name' => $this->tr ( 'Last Name' ), 'middle_name' => $this->tr ( 'Middle Name' ) );

			$colStatic = array ('first_name', 'last_name', 'middle_name' );

		} else {

			$trainerFields = array ('first_name' => $this->tr ( 'First Name' ), 'middle_name' => $this->tr ( 'Middle Name' ), 'last_name' => $this->tr ( 'Last Name' ) );

			$colStatic = array ('first_name', 'middle_name', 'last_name' );
		}

		if ($this->view->viewonly) {
			$editLinkInfo ['disabled'] = 1;
			$linkInfo = array ();
			$colStatic = array_keys ( $trainerFields );
		} else {
			$linkInfo = array (// add links to datatable fields
			'linkFields' => $colStatic, 'linkId' => 'trainer_id', // use ths value in link
			'linkUrl' => Settings::$COUNTRY_BASE_URL . '/person/edit/id/%trainer_id%' );
			$linkInfo ['linkUrl'] = "javascript:submitThenRedirect('{$linkInfo['linkUrl']}/trainingredirect/$training_id');";
			$editLinkInfo = array ();
		}

		$html = EditTableHelper::generateHtmlTraining ( 'Trainer', $trainers, $trainerFields, $colStatic, $linkInfo, $editLinkInfo );
		$this->view->assign ( 'tableTrainers', $html );

		/****************************************************************************************************************
		* Participants */

		$locations = Location::getAll ();

		if ($training_id) {
			$persons = PersonToTraining::getParticipants ( $training_id )->toArray ();
			foreach ( $persons as $pid => $p ) {
				list ( $city_name, $prov_id, $dist_id, $regc_id ) = Location::getCityInfo ( $p ['location_id'], $this->setting ( 'num_location_tiers' ) );
				$persons [$pid] ['province_name'] = $locations [$prov_id] ['name'];
				if ($dist_id)
				$persons [$pid] ['district_name'] = $locations [$dist_id] ['name'];
				else
				$persons [$pid] ['district_name'] = 'unknown';
				if ($regc_id)
				$persons [$pid] ['region_c_name'] = $locations [$regc_id] ['name'];
				else
				$persons [$pid] ['region_c_name'] = 'unknown';
			}
		} else {
			$persons = array ();
		}

		if (! $this->setting ( 'display_middle_name' )) {
			$personsFields = array ('first_name' => $this->tr ( 'First Name' ), 'last_name' => $this->tr ( 'Last Name' ), 'birthdate' => t ( 'Date of Birth' ), 'facility_name' => t ( 'Facility' ) );
		} else if ($this->setting ( 'display_middle_name_last' )) {
			$personsFields = array ('first_name' => $this->tr ( 'First Name' ), 'last_name' => $this->tr ( 'Last Name' ), 'middle_name' => "..." . $this->tr ( 'Middle Name' ), 'birthdate' => t ( 'Date of Birth' ), 'facility_name' => t ( 'Facility' ) );
		} else {
			$personsFields = array ('first_name' => $this->tr ( 'First Name' ), 'middle_name' => "..." . $this->tr ( 'Middle Name' ), 'last_name' => $this->tr ( 'Last Name' ), 'birthdate' => t ( 'Date of Birth' ), 'facility_name' => t ( 'Facility' ) );
		}

		if ($this->setting ( 'display_region_c' )) {
			$personsFields ['region_c_name'] = $this->tr ( 'Region C (Local Region)' );
		} else if ($this->setting ( 'display_region_b' )) {
			$personsFields ['district_name'] = $this->tr ( 'Region B (Health District)' );
		} else {
			$personsFields ['province_name'] = $this->tr ( 'Region A (Province)' );
		}

		$colStatic = array_keys ( $personsFields ); // all


		if ($this->view->viewonly) {
			$editLinkInfo ['disabled'] = 1;
			$linkInfo = array ();
		} else {

			$linkInfo = array (// add links to datatable fields
			'linkFields' => $colStatic, 'linkId' => 'person_id', // use ths value in link
			'linkUrl' => Settings::$COUNTRY_BASE_URL . '/person/edit/id/%person_id%' );
			$linkInfo ['linkUrl'] = "javascript:submitThenRedirect('{$linkInfo['linkUrl']}/trainingredirect/$training_id');";

			$editLinkInfo = array (// add link next to "Remove"
			array ('linkName' => t ( 'Pre-Test' ), 'linkId' => 'id', // use this value in link
			'linkUrl' => "javascript:updateScore('Pre-Test', %id%, '" . Settings::$COUNTRY_BASE_URL . "/training/scores-update', '%score_pre%');" ), // do not translate label/key
			array ('linkName' => t ( 'Post-Test' ), 'linkId' => 'id', // use this value in link
			'linkUrl' => "javascript:updateScore('Post-Test', %id%, '" . Settings::$COUNTRY_BASE_URL . "/training/scores-update', '%score_post%');" ), // do not translate label/key
			array ('linkName' => t ( 'Scores' ), 'linkId' => 'id', // use this value in link
			//'linkUrl' => Settings::$COUNTRY_BASE_URL."/training/scores/training/$training_id/person/%person_id%",
			'linkUrl' => "javascript:submitThenRedirect('" . Settings::$COUNTRY_BASE_URL . "/training/scores/ptt_id/%id%');" ) );
			//$editLinkInfo['linkUrl'] = "javascript:submitThenRedirect('{$editLinkInfo['linkUrl']}');";


		}

		$html = EditTableHelper::generateHtmlTraining ( 'Persons', $persons, $personsFields, $colStatic, $linkInfo, $editLinkInfo );
		$this->view->assign ( 'tablePersons', $html );

		/****************************************************************************************************************/
		/* Attached Files */

		FileUpload::displayFiles ( $this, 'training', $row->id, (! $this->view->viewonly) );

		//$this->view->assign('files', 'x' . FileUpload::displayFiles($this, 'training', $row->id));


		// File upload form
		if (! $this->view->viewonly) {
			$this->view->assign ( 'filesForm', FileUpload::displayUploadForm ( 'training', $row->id, FileUpload::$FILETYPES ) );
		}

		/****************************************************************************************************************/

		// mode
		$this->view->assign ( 'mode', $this->_getParam ( 'action' ) );

		switch ($this->_getParam ( 'msg' )) {
			case 'duplicate' :
			$this->view->assign ( 'msg', t ( 'Training session has been duplicated.<br>You can edit the duplicate session below.' ) );
			break;
			default :
			break;
		}

		// edit variables
		if ($this->_getParam ( 'action' ) != 'add') {
			//audit history
			$creatorObj = new User ( );
			$updaterObj = new User ( );
			$creatorrow = $creatorObj->findOrCreate ( $row->created_by );
			$rowRay ['creator'] = ($creatorrow->first_name) . ' ' . ($creatorrow->last_name);
			$updaterrow = $updaterObj->findOrCreate ( $row->modified_by );
			$rowRay ['updater'] = ($updaterrow->first_name) . ' ' . ($updaterrow->last_name);
		}

		if (empty ( $trainers ) || empty ( $persons )) {
			$this->view->assign ( 'isIncomplete', true );
		}

		// row values
		$this->view->assign ( 'row', $rowRay );

	}

	/**
	* JSON request to insert a new value into an option table (used for dynamic drop-downs and adding a dynamic checkbox)
	*/
	public function insertTableAction() {
		$request = $this->getRequest ();
		if ($request->isXmlHttpRequest ()) {

			$table = $this->getSanParam ( 'table' );
			$column = $this->getSanParam ( 'column' );
			$value = $this->getSanParam ( 'value' );

			if ($table && $column && $value) {
				$tableObj = new ITechTable ( array ('name' => $table ) );

				$undelete = $this->getSanParam ( 'undelete' );
				if ($undelete) {
					try {

						$row = $tableObj->undelete ( $column, $value );
						$sendRay ['insert'] = $row->id;
						$this->sendData ( $sendRay );

					} catch ( Zend_Exception $e ) {
						$this->sendData ( array ("insert" => 0, 'error' => $e->getMessage () ) );
					}
				} else {

					// Attempt to insert new


					try {
						$insert = $tableObj->insertUnique ( $column, $value );
						$sendRay ['insert'] = "$insert";

						if ($insert == - 1)
						$sendRay ['error'] = '"%s" ' . t ( 'already exists' ) . '.';
						if ($insert == - 2)
						$sendRay ['error'] = '"%s" ' . ('already exists, but was deleted.  Would you like to undelete?');

						// associate new training title with training category
						if ($table == 'training_title_option' && ! isset ( $sendRay ['error'] ) && $cat_id = $this->getSanParam ( 'cat_id' )) {
							$tableCatObj = new ITechTable ( array ('name' => 'training_category_option_to_training_title_option' ) );
							$tableCatObj->insert ( array ('training_category_option_id' => $cat_id, 'training_title_option_id' => $insert ) );
						}

						$this->sendData ( $sendRay );

					} catch ( Zend_Exception $e ) {
						$this->sendData ( array ("insert" => 0, 'error' => $e->getMessage () ) );
						error_log ( $e );
					}

				}

			}

		}
	}

	/**
	* editTable ajax
	*/
	private function ajaxeditTable() {
		if (! $this->hasACL ( 'edit_course' )) {
			$this->doNoAccessError ();
		}

		$training_id = $this->_getParam ( 'id' );
		$do = $this->_getParam ( 'edittable' );

		if (! $training_id) { // user is adding a new session (which does not have an id yet)
			$this->sendData ( array ('0' => 0 ) );
			return;
		}

		$action = $this->_getParam ( 'a' );
		$row_id = $this->_getParam ( 'row_id' );

		if ($do == 'trainer') { // update trainer table
			require_once ('models/table/Training.php');

			if ($action == 'add') {

				$days = $this->_getParam ( 'days' );
				$result = TrainingToTrainer::addTrainerToTraining ( $row_id, $training_id, $days );
				$sendRay ['insert'] = $result;
				if ($result == - 1) {
					$sendRay ['error'] = t ( 'This trainer is already in this training session.' );
				}
				$this->sendData ( $sendRay );

			} elseif ($action == 'del') {
				$tableObj = new TrainingToTrainer ( );
				$result = $tableObj->delete ( "id=$row_id", true );

				$this->sendData ( array ('delete' => $result ) );

			} else { // update a row?


				$days = $this->_getParam ( 'duration_days' );
				if ($days) {
					$tableObj = new TrainingToTrainer ( );
					$result = $tableObj->update ( array ("duration_days" => $days ), "id=$row_id" );
					$sendRay ['update'] = $result;

					if (! $result) {
						$sendRay ['error'] = t ( 'Could not update this record.' );
					}

					$this->sendData ( $sendRay );
				}

			}

		} else if ($do == 'persons') { // update person table
			require_once ('models/table/PersonToTraining.php');
			$tableObj = new PersonToTraining ( );

			if ($action == 'add') {
				$result = $tableObj->addPersonToTraining ( $row_id, $training_id );

				$sendRay ['insert'] = $result;
				if ($result == - 1) {
					$sendRay ['error'] = t ( 'This participant is already in this training session.' );
				}
				$this->sendData ( $sendRay );

			} elseif ($action == 'del') {
				$result = $tableObj->delete ( "id=$row_id", true );
				$this->sendData ( array ('delete' => $result ) );
			}

		}

		// update "modified_by" field in training table
		$tableObj = new Training ( );
		$tableObj->update ( array (), "id = $training_id" );

	}

	/**
	* New training location
	*/
	public function locationAddAction() {
		require_once 'models/table/TrainingLocation.php';
		require_once 'models/table/Location.php';

		$request = $this->getRequest ();
		$validateOnly = $request->isXmlHttpRequest ();

		if ($validateOnly)
		$this->setNoRenderer ();

		if ($request->isPost ()) {

			$tableObj = new TrainingLocation ( );

			$location = $this->_getParam ( 'training_location_name' );

			list ( $location_params, $location_tier, $location_id ) = $this->getLocationCriteriaValues ( array (), 'location' );

			//validate
			$status = ValidationContainer::instance ();

			$districtText = $this->tr ( 'Region B (Health District)' );
			$provinceText = $this->tr ( 'Region A (Province)' );
			$localRegionText = $this->tr ( 'Region C (Local Region)' );

			$status->checkRequired ( $this, 'location_province_id', $provinceText );
			if ($this->setting ( 'display_region_b' ))
			$status->checkRequired ( $this, 'location_district_id', $districtText );
			if ($this->setting ( 'display_region_c' ))
			$status->checkRequired ( $this, 'location_region_c_id', $localRegionText );
			//$status->checkRequired ( $this, 'location_city', t ( "City is required." ) );

			$city_id = false;
			if ($this->getSanParam('location_city') && ! $this->getSanParam ( 'is_new_city' )) {
				$city_id = Location::verifyHierarchy ( $location_params ['location_city'], $location_params ['location_city_parent_id'], $this->setting ( 'num_location_tiers' ) );
				if ($city_id === false) {
					$status->addError ( 'location_city', t ( "That city does not appear to be located in the chosen region. If you want to create a new city, check the new city box." ) );
				}
			}
			// save
			if (! $status->hasError ()) {
				$location_id = null;
				if (($city_id === false) && $this->getSanParam ( 'is_new_city' )) {
					$location_id = Location::insertIfNotFound ( $location_params ['location_city'], $location_params ['location_city_parent_id'], $this->setting ( 'num_location_tiers' ) );
					if ($location_id === false)
					$status->addError ( 'location_city', t ( 'Could not save that city.' ) );
				} else {
					if ( $city_id ) {
						$location_id = $city_id;
					} else if ($this->setting ( 'display_region_c' )) {
						$location_id = $this->getSanParam('location_region_c_id');
					} else if ($this->setting ( 'display_region_b' )) {
						$location_id = $this->getSanParam('location_district_id' );
					} else {
						$location_id = $this->getSanParam('location_province_id' );
					}

					if ( strstr($location_id,'_') ) {
						$parts = explode('_',$location_id);
						$location_id = $parts[count($parts) - 1];
					}

				}

				if ($location_id) {
					// update or insert?
					if ($this->_getParam ( 'update' )) {
						$data = array ();
						$data ['location_id'] = $location_id;
						$data ['training_location_name'] = $location;

						$tableObj = new TrainingLocation ( );
						$tableObj->update ( $data, "id = " . $this->_getParam ( 'update' ) );

						$status->setStatusMessage ( t ( 'The training center has been updated.' ) );
						$_SESSION['status'] = t ( 'The training center has been updated.' );

						//refresh the page, so the picker dropdown is refreshed as well
						$status->setRedirect ( '/facility/view-location/id/' . $this->_getParam ( 'update' ) );
					} else {
						$id = TrainingLocation::insertIfNotFound ( $location, $location_id );

						if ($this->_getParam ( 'info' ) == 'extra') {
							$status->setStatusMessage ( t ( 'The training center has been saved.' ) );
							$_SESSION['status'] = t ( 'The training center has been saved.' );
							$status->setRedirect ( '/facility/view-location/id/' . $id );
						}
					}

					if ($this->_getParam ( 'info' ) == 'extra') {
						$this->sendData ( $status );
					} else {
						$this->sendData ( array ('location_id' => $id ) );
					}
				}
			} else {
				$status->setStatusMessage ( t ( 'The training center could not be saved.' ) );
				$this->sendData ( $status );
			}

		}
	}

	private function _attach_locations($rowArray) {
		if ($rowArray) {
			$num_tiers = $this->setting ( 'num_location_tiers' );
			$locations = Location::getAll ();
			foreach ( $rowArray as $id => $row ) {
				list ( $city_name, $prov_id, $dist_id, $regc_id ) = Location::getCityInfo ( $row ['location_id'], $num_tiers );
				$rowArray [$id] ['province_name'] = $locations [$prov_id] ['name'];
				$rowArray [$id] ['district_name'] = $locations [$dist_id] ['name'];
				if (isset ($locations [$regc_id])){
					$rowArray [$id] ['region_c_name'] = $locations [$regc_id] ['name'];
				} else {
					$rowArray [$id] ['region_c_name'] = "";
				}
			}
		}

		return $rowArray;
	}

	/**
	* autocomplete ajax (trainer)
	*/
	public function trainerLastListAction() {
		require_once ('models/table/Trainer.php');
		$rowArray = Trainer::suggestionList ( $this->_getParam ( 'query' ), 100, $this->setting ( 'display_middle_name_last' ) );
		$rowArray = $this->_attach_locations ( $rowArray );
		$this->sendData ( $rowArray );
	}
	/**
	* autocomplete ajax (trainer)
	*/
	public function trainerMiddleListAction() {
		require_once ('models/table/Trainer.php');
		$rowArray = Trainer::suggestionListByMiddleName ( $this->_getParam ( 'query' ), 100, $this->setting ( 'display_middle_name_last' ) );
		$rowArray = $this->_attach_locations ( $rowArray );
		$this->sendData ( $rowArray );
	}

	/**
	* autocomplete ajax (trainer)
	*/
	public function trainerFirstListAction() {
		require_once ('models/table/Trainer.php');
		$rowArray = Trainer::suggestionListByFirstName ( $this->_getParam ( 'query' ), 100, $this->setting ( 'display_middle_name_last' ) );
		$rowArray = $this->_attach_locations ( $rowArray );
		$this->sendData ( $rowArray );
	}

	/**
	* autocomplete ajax (person)
	*/
	public function personLastListAction() {
		require_once ('models/table/Person.php');
		$rowArray = Person::suggestionList ( $this->_getParam ( 'query' ), 100, $this->setting ( 'display_middle_name_last' ) );
		$rowArray = $this->_attach_locations ( $rowArray );
		$this->sendData ( $rowArray );
	}

	/**
	* autocomplete ajax (person)
	*/
	public function personMiddleListAction() {
		require_once ('models/table/Person.php');
		$rowArray = Person::suggestionListByMiddleName ( $this->_getParam ( 'query' ), 100, $this->setting ( 'display_middle_name_last' ) );
		$rowArray = $this->_attach_locations ( $rowArray );
		$this->sendData ( $rowArray );
	}
	/**
	* autocomplete ajax (person)
	*/
	public function personFirstListAction() {
		require_once ('models/table/Person.php');
		$rowArray = Person::suggestionListByFirstName ( $this->_getParam ( 'query' ), 100, $this->setting ( 'display_middle_name_last' ) );
		$rowArray = $this->_attach_locations ( $rowArray );
		$this->sendData ( $rowArray );
	}

	/**
	* autocomplete ajax (course name via people search)
	*/
	public function courseListAction() {
		require_once ('models/table/TrainingTitleOption.php');
		$rowArray = suggestionList::suggestionList ( $this->_getParam ( 'query' ) );
		$this->sendData ( $rowArray );
	}

	/**
	* Edit test scores
	*/
	public function scoresAction() {
		require_once ('models/table/Person.php');
		require_once ('models/table/PersonToTraining.php');

		$pttObj = new PersonToTraining ( );
		$personTrainingRow = $pttObj->findOrCreate ( $this->_getParam ( 'ptt_id' ) );

		$trainingObj = new Training ( );
		$personObj = new Person ( );

		$this->viewAssignEscaped ( 'courseName', $trainingObj->getCourseName ( $personTrainingRow->training_id ) );
		$this->viewAssignEscaped ( 'personRow', $personObj->getPersonName ( $personTrainingRow->person_id ) );
		$this->view->assign ( 'training_id', $personTrainingRow->training_id );

		require_once ('EditTableController.php');
		$editTable = new EditTableController ( $this );
		$editTable->table = 'score';
		$editTable->fields = array ('score_label' => t ( 'Label' ), 'score_value' => t ( 'Score' ) ); // TODO: Label translations
		$editTable->label = 'Score';
		$editTable->where = "person_to_training_id = {$personTrainingRow->id}";
		$editTable->insertExtra = array ('person_to_training_id' => $personTrainingRow->id );
		//$editTable->customColDef = array('training_date' => 'formatter:YAHOO.widget.DataTable.formatDate, editor:"date"');
		//$editTable->customColDef = array('training_date' => 'width:120');


		$editTable->execute ();
	}

	/**
	* Update test scores via Ajax
	*/
	public function scoresUpdateAction() {
		require_once ('models/table/Person.php');
		require_once ('models/table/PersonToTraining.php');

		$ptt_id = $this->getSanParam ( 'ptt_id' );
		$label = $this->getSanParam ( 'label' );
		$value = $this->getSanParam ( 'value' );

		Training::updateScore ( $ptt_id, $label, $value );
		$status = ValidationContainer::instance ();
		$this->sendData('');
	}

	public function indexAction() {
		return $this->_redirect ( 'training/search' );
	}

	public function searchAction() {
		return $this->_forward ( 'trainingSearch', 'reports' );
	}

	public function deletedAction() {

	}

	/**
	* Training Roster
	*/
	public function rosterAction() {
		$training_id = $this->_getParam ( 'id' );
		$this->view->assign ( 'url', Settings::$COUNTRY_BASE_URL . "/training/roster/id/$training_id" );

		$tableObj = new Training ( );

		$rowRay = $tableObj->getTrainingInfo ( $training_id );

		// calculate end date
		switch ($rowRay ['training_length_interval']) {
			case 'week' :
			$days = $rowRay ['training_length_value'] * 7;
			break;
			case 'day' :
			$days = $rowRay ['training_length_value'] - 1; // start day counts as a day?
			break;
			default :
			$days = false;
			break;
		}

		if ($days) {
			$rowRay ['training_end_date'] = strtotime ( "+$days day", strtotime ( $rowRay ['training_start_date'] ) );
			$rowRay ['training_end_date'] = date ( 'Y-m-d', $rowRay ['training_end_date'] );
		} else {
			$rowRay ['training_end_date'] = $rowRay ['training_start_date'];
		}

		$rowRay ['duration'] = $rowRay ['training_length_value'] . ' ' . $rowRay ['training_length_interval'] . (($rowRay ['training_length_value'] == 1) ? "" : "s");

		$this->viewAssignEscaped ( 'row', $rowRay );

		// trainer/person tables
		require_once 'views/helpers/EditTableHelper.php';

		/* Trainers */
		$trainers = TrainingToTrainer::getTrainers ( $training_id )->toArray ();

		$trainerFields = array ('last_name' => $this->tr ( 'Last Name' ), 'first_name' => $this->tr ( 'First Name' ), 'duration_days' => t ( 'Days' ) );
		$colStatic = array_keys ( $trainerFields ); // all
		$editLinkInfo = array ('disabled' => 1 ); // no edit/remove links
		$html = EditTableHelper::generateHtmlTraining ( 'Trainer', $trainers, $trainerFields, $colStatic, array (), $editLinkInfo );
		$this->view->assign ( 'tableTrainers', $html );

		/* Participants */
		$persons = PersonToTraining::getParticipants ( $training_id )->toArray ();
		$personsFields = array ('last_name' => $this->tr ( 'Last Name' ), 'first_name' => $this->tr ( 'First Name' ), 'birthdate' => t ( 'Date of Birth' ), 'facility_name' => t ( 'Facility' ) );
		//if ($this->setting ( 'display_region_b' ))
		$personsFields ['location_name'] = t ( 'Location' );
		//add location
		$locations = Location::getAll ();
		foreach ( $persons as $pid => $person ) {
			list ( $city_name, $prov_id, $dis_id, $reg_c_id ) = Location::getCityInfo ( $person ['location_id'], $this->setting ( 'num_location_tiers' ) );
			$ordered_l = array ($city_name );
			if ($reg_c_id)
			$ordered_l [] = $locations [$reg_c_id] ['name'];
			if ($dis_id && count ( $ordered_l ) > 2)
			$ordered_l [] = $locations [$dis_id] ['name'];
			if ($prov_id && count ( $ordered_l ) > 2)
			$ordered_l [] = $locations [$prov_id] ['name'];
			$persons [$pid] ['location_name'] = implode ( ', ', $ordered_l );
		}

		$colStatic = array_keys ( $personsFields ); // all
		$editLinkInfo = array ('disabled' => 1 ); // no edit/remove links
		$html = EditTableHelper::generateHtmlTraining ( 'Persons', $persons, $personsFields, $colStatic, array (), $editLinkInfo );
		$this->view->assign ( 'tablePersons', $html );

		if ($this->_getParam ( 'outputType' ) && $this->_getParam ( 'trainers' ))
		$this->sendData ( $trainers );
		if ($this->_getParam ( 'outputType' ) && $this->_getParam ( 'persons' ))
		$this->sendData ( $persons );

	}

	public function topicListAction() {
		$rowArray = OptionList::suggestionList ( 'training_topic_option', 'training_topic_phrase', $this->_getParam ( 'query' ) );
		$this->sendData ( $rowArray );
	}

	public function listByTrainerAction() {
		//training info
		$trainingObj = new Training ( );
		$rowArray = $trainingObj->findFromTrainer ( $this->_getParam ( 'id' ) );
		$this->sendData ( $rowArray );
	}

	public function listByTrainingRecommendAction() {
		// recommended classes based on primary qualification
		require_once 'models/table/TrainingRecommend.php';
		$trainingRecObj = new TrainingRecommend ( );
		$rowArray = $trainingRecObj->getRecommendedClasses ( $this->_getParam ( 'id' ) ); // qualification id
		$this->sendData ( $rowArray );
	}

	public function listByTrainingRecommendPersonAction() {
		// recommended classes based on person id
		require_once 'models/table/TrainingRecommend.php';
		$trainingRecObj = new TrainingRecommend ( );
		$rowArray = $trainingRecObj->getRecommendedClassesforPerson ( $this->_getParam ( 'id' ) ); // person id
		$this->sendData ( $rowArray );
	}

	public function listByParticipantAction() {
		//class info
		$trainingObj = new Training ( );
		$rowArray = $trainingObj->findFromParticipant ( $this->_getParam ( 'id' ) );
		$this->sendData ( $rowArray );
	}

	public function organizerListAction() {
		require_once ('models/table/OptionList.php');
		$rowArray = OptionList::suggestionList ( 'training_organizer_option', 'training_organizer_phrase', $this->_getParam ( 'query' ) );
		$this->sendData ( $rowArray );
	}

	//ugly helper; TODO refactor
	private function _generate_hierarchical($id, $data, $phrase_col, $selected) {
		$output = '<select id="'.$id.'" name="person_qualification_option_id[]">';
		$output .= '<option value="">-- '.t("select").' --</option>';
		$lastParent = null;
		foreach ($data as $vals ) {
			if ( !$vals['id'] ) {
				$lastParent = ($vals['parent_phrase']);
				$output .='<option value="'.$vals['parent_id'].'" '.($selected == $vals['parent_id'] ?'selected="selected"':'').'>'.htmlspecialchars($vals['parent_phrase']).'</option>';
			} else {
				$output .= '<option value="'.$vals['id'].'" '.($selected == $vals['id'] ?'selected="selected"':'').'>&nbsp;&nbsp;'. htmlspecialchars($vals[$phrase_col]).'</option>';
			}
		}

		$output .= '</select>';
		return $output;

	}
}
?>
