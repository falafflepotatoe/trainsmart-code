<?php
/*
 * Created on Feb 27, 2008
 *
 *  Built for web
 *  Fuse IQ -- todd@fuseiq.com
 *
 */
require_once ('ReportFilterHelpers.php');
require_once ('models/table/OptionList.php');
require_once ('models/table/Person.php');
require_once ('models/table/User.php');
require_once ('models/table/Facility.php');
require_once ('models/table/Training.php');
require_once ('models/table/TrainingRecommend.php');
require_once ('models/table/Trainer.php');

class PersonController extends ReportFilterHelpers {
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	}
	
	public function init() {
	}
	
	public function preDispatch() {
		parent::preDispatch ();
		
		if (! $this->isLoggedIn ())
			$this->doNoAccessError ();
	
	}
	
	public function indexAction() {
		return $this->_redirect ( 'person/search' );
	}
	
	public function deleteAction() {
		if (! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		$status = ValidationContainer::instance ();
		$id = $this->getSanParam ( 'id' );
		
		if ($id and ! Person::isReferenced ( $id )) {
			$trainer = new Trainer ( );
			$rows = $trainer->find ( $id );
			$row = $rows->current ();
			if ($row) {
				$trainer->delete ( 'person_id = ' . $row->person_id );
			}
			$person = new Person ( );
			$rows = $person->find ( $id );
			$row = $rows->current ();
			if ($row) {
				$person->delete ( 'id = ' . $row->id );
			}
			$status->setStatusMessage ( t ( 'That person was deleted.' ) );
		} else if (! $id) {
			$status->setStatusMessage ( t ( 'That person could not be found.' ) );
		} else {
			$status->setStatusMessage ( t ( 'That person is an active participant or trainer and could not be deleted.' ) );
		}
		
		//validate
		$this->view->assign ( 'status', $status );
	
	}
	
	public function viewAction() {
		if (! $this->hasACL ( 'view_people' ) and ! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		
		if ($this->hasACL ( 'edit_people' )) {
			//redirect to edit mode
			$this->_redirect ( str_replace ( 'view', 'edit', 'http://' . $_SERVER ['SERVER_NAME'] . $_SERVER ['REQUEST_URI'] ) );
		}
		
		$this->view->assign ( 'mode', 'view' );
		$rtn = $this->doAddEditView ();
		return $rtn;
	}
	
	public function addAction() {
		if (! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		
		$this->view->assign ( 'mode', 'add' );
		$rtn = $this->doAddEditView ();
		return $rtn;
	}
	
	public function editAction() {
		if (! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		
		$this->view->assign ( 'mode', 'edit' );
		$rtn = $this->doAddEditView ();
		return $rtn;
	}
	
	public function doAddEditView() {

		//validate
		$status = ValidationContainer::instance ();
		
		require_once ('models/table/OptionList.php');
		require_once ('models/table/MultiOptionList.php');
		require_once ('models/table/Location.php');
		
		$request = $this->getRequest ();
		$validateOnly = $request->isXmlHttpRequest ();
		$person_id = $this->getSanParam ( 'id' );
		
		$personObj = new Person ( );
		$personrow = $personObj->findOrCreate ( $person_id );
		$personArray = $personrow->toArray ();

		//locations
		$locations = Location::getAll();
		$this->viewAssignEscaped ( 'locations', $locations );
		
		if ($validateOnly){
			$this->setNoRenderer ();
		}
			
		// Figure out reason code "Other" for checking posted data,
		// and for enabling/disabling reason_other field
		$other_reason_option_id = -1;
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT * FROM person_attend_reason_option where LCASE(attend_reason_phrase) LIKE '%other%'";
		$rowArray = $db->fetchAll ( $sql );
		if ($rowArray) {
			$other_reason_option_id = $rowArray[0]['id'];
		}
		$this->viewAssignEscaped ( 'other_reason_option_id', $other_reason_option_id );
		
		if ($request->isPost ()) {
			
			if ($dupe_id = $this->getSanParam ( 'dupe_id' )) {
				// require user to add trainer info
				if ($this->getSanParam ( 'maketrainer' )) {
					$status->setRedirect ( '/person/edit/id/' . $dupe_id . '/trainingredirect/' . $this->getSanParam ( 'trainingredirect' ) . '/maketrainer/1' );
				} else if ($this->_getParam ( 'trainingredirect' )) {
					$status->setStatusMessage ( t ( 'The person was saved. Refreshing history...' ) );
					$this->trainingRedirect ( $dupe_id );
				} else {
					$status->setRedirect ( '/person/edit/id/' . $dupe_id );
				}
				return;
			}

			$status->checkRequired ( $this, 'first_name', $this->tr ( 'First Name' ) );
			$status->checkRequired ( $this, 'last_name', $this->tr ( 'Last Name' ) );
			$status->checkRequired ( $this, 'gender', t ( 'Gender' ) );
			$status->checkRequired ( $this, 'primary_qualification_option_id', t ( 'Professional qualification' ) );
			//$status->checkRequired($this, 'primary_responsibility_option_id','Primary responsibility');
			//$status->checkRequired($this, 'secondary_responsibility_option_id','Secondary responsibility');


			$birthParam = (@$this->getSanParam ( 'birth-year' )) . '-' . (@$this->getSanParam ( 'birth-month' )) . '-' . (@$this->getSanParam ( 'birth-day' ));
			if ($birthParam !== '--' and $birthParam !== '0000-00-00')
				$status->isValidDate ( $this, 'birth-day', t ( 'Birthdate' ), $birthParam );
				
			//trainer only
			if ($this->getSanParam ( 'is_trainer' ) || $this->getSanParam ( 'active_trainer_option_id' ) || $this->getSanParam ( 'trainer_type_option_id' )) {
				$status->checkRequired ( $this, 'trainer_type_option_id', t ( 'Trainer type' ) );
				// 10/02/2011 Sean Smith: Removed at client request.
				//if ($this->setting ( 'display_trainer_affiliations' )) {
				//	$status->checkRequired ( $this, 'trainer_affiliation_option_id', t ( 'Trainer affiliation' ) );
				//}
				//at least one skill
				$status->checkRequired ( $this, 'trainer_skill_id', t ( 'Trainer skill' ) );
			
			}
			
			// Check for manual reason for attending entry (if pulldown reason is 'other')
			if ( $this->setting('display_attend_reason')  ) {
				//if ($status->checkRequired ( $this, 'attend_reason_option_id', t ( 'Reason For Attending' ))) {
					$reason_id = $this->getSanParam ( 'attend_reason_option_id' );
					$other_reason = $this->getSanParam ( 'attend_reason_other' );
					if (($reason_id || $reason_id == 0) && $other_reason_option_id >= 0) // id zero is the 'Other' reason in the pulldown. 
					{
						if ($reason_id == $other_reason_option_id) {
							if ($other_reason == "") {
								$status->addError ( 'attend_reason_other', t ( 'Enter a reason, or select a different reason above.' ) );
							}
						} else {
							if ($other_reason != "") {
								$status->addError ( 'attend_reason_other', t ( 'You can not type in a reason with the reason selected above.' ) );
							}
						}
					} 
				//}
			}
			//check facility
			if ($status->checkRequired ( $this, 'facilityInput', t ( 'Facility' ) )) {
				$facility_id = $this->getSanParam ( 'facilityInput' );
				if ($strrpos = strrpos ( $facility_id, '_' )) {
					$facility_id = substr ( $facility_id, $strrpos + 1 );
				}
				
				//find by name
				$facilityByName = new Facility ( );
				$row = $facilityByName->fetchRow ( 'id = ' . $facility_id );
				//$row = $facilityByName->fetchRow($facilityByName->select()->where('facility_name = ?', $this->getSanParam('facilityInput')));
				if (@$row->id) {
					$personrow->facility_id = $row->id;
					
				} else {
					$status->addError ( 'facilityInput', t ( 'That facility name could not be found.' ) );
				}

			}

			//get home city name
		 list($cname,$prov,$dist,$regc ) = Location::getCityInfo($personrow->home_location_id, $this->setting('num_location_tiers'));
		 $personArray['home_city'] = $cname;
		 $personArray['home_region_c_id'] = $regc;
		 $personArray['home_district_id'] = $dist;
		 $personArray['home_province_id'] = $prov;
		 $city_id = false;
		 list($home_location, $home_location_tier, $home_location_id) = $this->getLocationCriteriaValues(array(), 'home');
		 if ( $home_location['home_city'] && !$this->getSanParam ( 'is_new_home_city' ) ) {
				$city_id = Location::verifyHierarchy ( $home_location['home_city'], $home_location['home_city_parent_id'], $this->setting('num_location_tiers'));
				if ( $city_id === false ) {
				 $status->addError ( 'home_city', t ( "That city does not appear to be located in the chosen region. If you want to create a new city, check the new city box." ) );
				}
			}
			
 			if ($status->hasError ()) {
				$status->setStatusMessage ( t ( 'The person could not be saved.' ) );
			} else {
				
				$personrow = self::fillFromArray ( $personrow, $this->_getAllParams () );
		 if ( ($city_id === false) && $this->getSanParam ( 'is_new_home_city' )) {
			$city_id = Location::insertIfNotFound ( $home_location['home_city'], $home_location['home_city_parent_id'], $this->setting('num_location_tiers') ); 
			if ($city_id === false)
				 $status->addError ( 'home_city', t ( 'Could not save that city.' ) );
			}
			if ( $city_id ) {
				$personrow->home_location_id = $city_id;
			} else {
					list($home_location, $home_location_tier, $home_location_id) = $this->getLocationCriteriaValues(array(), 'home');
			if ( $home_location_id)
					 $personrow->home_location_id = $home_location_id;
			}
			//these are transitionary database fields, will go away soon
		//  $personrow->home_district_id = null;
		//  $personrow->home_province_id = null;

				if ($this->getSanParam ( 'active' ))
					$personrow->active = 'active';
				else
					$personrow->active = 'inactive';
				
				$personrow->birthdate = (@$this->getSanParam ( 'birth-year' )) . '-' . (@$this->getSanParam ( 'birth-month' )) . '-' . (@$this->getSanParam ( 'birth-day' ));

				//lookup custom 1 and 2
				if ($this->getSanParam ( 'custom1Input' )) {
					$id = OptionList::insertIfNotFound ( 'person_custom_1_option', 'custom1_phrase', $this->getSanParam ( 'custom1Input' ) );
					$personrow->person_custom_1_option_id = $id;
				} else {
					$personrow->person_custom_1_option_id = null;
				}
				
				if ($this->getSanParam ( 'custom2Input' )) {
					$id = OptionList::insertIfNotFound ( 'person_custom_2_option', 'custom2_phrase', $this->getSanParam ( 'custom2Input' ) );
					$personrow->person_custom_2_option_id = $id;
				} else {
					$personrow->person_custom_2_option_id = null;
				}
				
/*				try {
				$personrow->save ()
			}
			catch (x) {
				$status->setStatusMessage ( x );
			}
			
								$status->checkRequired($this, 'secondary_responsibility_option_id','Secondary responsibility');
 			if ($status->hasError ()) {
				$status->setStatusMessage ( t ( 'The person could not be saved.' ) );
			} else {*/
				
				
				if ($personrow->save ()) {
					$status->setStatusMessage ( t ( 'The person was saved. Refreshing change history...' ) );
					
					$person_id = $personrow->id;
					
					//get trainer information
					$trainerTable = new Trainer ( );
					$trainerRow = $trainerTable->fetchRow ( 'person_id = ' . $person_id );
					if ((!$trainerRow) and ($this->getSanParam('active_trainer_option_id') or $this->getSanParam('trainer_type_option_id'))) { // add trainer
						$trainerRow = $trainerTable->createRow ();
						$trainerRow->person_id = $personrow->id;
					}
					
					if ($trainerRow) {
						//trainer info
						$trainerRow->is_active = 1; //deprecated //($this->getSanParam ( 'is_trainer' ) ? 1 : 0);
						$trainerRow->active_trainer_option_id = $this->getSanParam ( 'active_trainer_option_id' );
						$trainerRow->type_option_id = $this->getSanParam ( 'trainer_type_option_id' );
						$trainerRow->affiliation_option_id = ($this->setting ( 'display_trainer_affiliations' )?$this->getSanParam ( 'trainer_affiliation_option_id' ):null);
						if (! $trainerRow->save ()) {
							$status->setStatusMessage ( t ( 'The trainer information could not be saved.' ) );
						} else {
							MultiOptionList::updateOptions ( 'trainer_to_trainer_skill_option', 'trainer_skill_option', 'trainer_id', $person_id, 'trainer_skill_option_id', $this->getSanParam ( 'trainer_skill_id' ) );
							
							$language_ids = $this->getSanParam ( 'trainer_language_id' );
							$planguage_id = $this->getSanParam ( 'primary_language_id' );
							$primary_settings = array ();
							$found = false;
							if  ( $language_ids ) {
							foreach ( $language_ids as $lid ) {
								if ($lid == $planguage_id) {
									$primary_settings [] = 1;
									$found = true;
								} else {
									$primary_settings [] = 0;
								}
							}
							}
							if (! $found) {
								$primary_settings [] = 1;
								$language_ids [] = $planguage_id;
							}
							
							MultiOptionList::updateOptions ( 'trainer_to_trainer_language_option', 'trainer_language_option', 'trainer_id', $person_id, 'trainer_language_option_id', $language_ids, 'is_primary', $primary_settings );
						}
					}
					
					TrainingRecommend::saveRecommendedforPerson ( $person_id, $this->getSanParam ( 'training_recommend' ) );
					
					if ($this->_getParam ( 'redirectUrl' )) {
						$status->redirect = $this->_getParam ( 'redirectUrl' );
					} else if ($this->_getParam ( 'trainingredirect' )) { // redirect back to training session
						$this->trainingRedirect ( $person_id );
					} else { //if it's an add then redirect to the view page
						$status->setRedirect ( '/person/view/id/' . $person_id );
					}
				
				} else {
					$status->setStatusMessage ( t ( 'ERROR: The person could not be saved.' ) );
				}
				//}
			}
			
			if ($validateOnly) {
				$this->sendData ( $status );
			} else {
				$this->view->assign ( 'status', $status );
			}
		}
		
		//view it
		$facilityObj = new Facility ( );
		$facilityrow = $facilityObj->findOrCreate ( $personrow->facility_id );
		$personArray ['facility'] = $facilityrow->toArray ();
   //facility location 
	 list($cname,$prov,$dist,$regc ) = Location::getCityInfo($facilityrow->location_id, $this->setting('num_location_tiers'));
	 $personArray['facility_city'] = $cname;
	 $personArray['region_c_id'] = $regc;
	 $personArray['district_id'] = $dist;
	 $personArray['province_id'] = $prov;
		
		//audit history
		$creatorObj = new User ( );
		$updaterObj = new User ( );
		$creatorrow = $creatorObj->findOrCreate ( $personrow->created_by );
		$personArray ['creator'] = ($creatorrow->first_name) . ' ' . ($creatorrow->last_name);
		$updaterrow = $updaterObj->findOrCreate ( $personrow->modified_by );
		$personArray ['updater'] = ($updaterrow->first_name) . ' ' . ($updaterrow->last_name);
		
		$personArray ['birthdate-year'] = '';
		$personArray ['birthdate-month'] = '';
		$personArray ['birthdate-day'] = '';
		
		//split birthdate fields
		if ($person_id and $personrow->birthdate) {
			$parts = explode ( ' ', $personrow->birthdate );
			$parts = explode ( '-', $parts [0] );
			$personArray ['birthdate-year'] = $parts [0];
			$personArray ['birthdate-month'] = $parts [1];
			$personArray ['birthdate-day'] = $parts [2];
		}
		
		//custom fields
		if ($person_id) {
			$personArray ['custom1'] = ITechTable::getCustomValue ( 'person_custom_1_option', 'custom1_phrase', $personArray ['person_custom_1_option_id'] );
			$personArray ['custom2'] = ITechTable::getCustomValue ( 'person_custom_2_option', 'custom2_phrase', $personArray ['person_custom_2_option_id'] );
		}
		
		//qualifications
		$qualificationsArray = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false, array ('0 AS is_default', 'child.is_default' ) );
		$personQualificationId = $personArray ['primary_qualification_option_id'];
		
		// get parent qualification id, if user has sub qualification selected
		$personArray ['primary_qualification_option_id_parent'] = $personQualificationId;
		foreach ( $qualificationsArray as $k => $qualArray ) {
			if ($qualArray ['parent_phrase'] == 'unknown') {
				unset ( $qualificationsArray [$k] ); //remove unknown as an option
			}
			if ($qualArray ['id'] == $personQualificationId) {
				$personArray ['primary_qualification_option_id_parent'] = $qualArray ['parent_id'];
			}
		}
		$this->viewAssignEscaped ( 'qualifications', $qualificationsArray );
		
		// get recommended trainings class topics
		$training_recommend = TrainingRecommend::getRecommendedTrainingTopics ( $personArray ['primary_qualification_option_id_parent'] );
		$this->viewAssignEscaped ( 'training_recommend', $training_recommend );
		
		// get saved recommended trainings class titles
		$training_recommend_saved = TrainingRecommend::getRecommendedforPerson ( $person_id );
		$this->viewAssignEscaped ( 'training_recommend_saved', $training_recommend_saved );
		
		//$classes = TrainingRecommend::getRecommendedClassesforPerson ( $person_id );
		
		//responsibilities
		$primaryResponsibilitiesArray  = OptionList::suggestionList ( 'person_primary_responsibility_option', 'responsibility_phrase', false, false );
		$this->viewAssignEscaped ( 'primaryResponsibilities', $primaryResponsibilitiesArray );
		$secondaryResponsibilitiesArray = OptionList::suggestionList ( 'person_secondary_responsibility_option', 'responsibility_phrase', false, false );
		$this->viewAssignEscaped ( 'secondaryResponsibilities', $secondaryResponsibilitiesArray );



		$educationlevelsArray = OptionList::suggestionList ( 'person_education_level_option', 'education_level_phrase', false, false );
		$this->viewAssignEscaped ( 'educationlevels', $educationlevelsArray );

		$attendreasonsArray = OptionList::suggestionList ( 'person_attend_reason_option', 'attend_reason_phrase', false, false );
		$this->viewAssignEscaped ( 'attendreasons', $attendreasonsArray );

		$activeTrainerArray = OptionList::suggestionList ( 'person_active_trainer_option', 'active_trainer_phrase', false, false );
	$this->viewAssignEscaped ( 'active_trainer', $activeTrainerArray );
		
		$titlesArray = OptionList::suggestionList ( 'person_title_option', 'title_phrase', false, false );
		$this->viewAssignEscaped ( 'titles', $titlesArray );
		
		$suffixesArray = OptionList::suggestionList ( 'person_suffix_option', 'suffix_phrase', false, false );
		$this->viewAssignEscaped ( 'suffixes', $suffixesArray );
		//training types
		//attach Trainer attributes
		

		$trainerTable = new Trainer ( );
		$trainerrow = $trainerTable->findOrCreate ( $person_id );
		
		if ($this->getSanParam ( 'maketrainer' ))
			$personArray ['trainer_is_active'] = 1;
		else
			$personArray ['trainer_is_active'] = $trainerrow->is_active;
		$personArray ['active_trainer_option_id'] = $trainerrow->active_trainer_option_id;
		$personArray ['trainer_type_option_id'] = $trainerrow->type_option_id;
		$personArray ['trainer_affiliation_option_id'] = $trainerrow->affiliation_option_id;
		
	$titlesArray = OptionList::suggestionList ( 'person_title_option', 'title_phrase', false, false );
	$this->viewAssignEscaped ( 'titles', $titlesArray );

	$trainerTypesArray = OptionList::suggestionList ( 'trainer_type_option', 'trainer_type_phrase', false, false );
		$this->viewAssignEscaped ( 'trainer_types', $trainerTypesArray );
		$trainerAffiliationArray = OptionList::suggestionList ( 'trainer_affiliation_option', 'trainer_affiliation_phrase', false, false );
		$this->viewAssignEscaped ( 'trainer_affiliations', $trainerAffiliationArray );
		
		$trainerSkillsArray = MultiOptionList::choicesList ( 'trainer_to_trainer_skill_option', 'trainer_id', $person_id, 'trainer_skill_option', 'trainer_skill_phrase' );
		$this->viewAssignEscaped ( 'trainer_skills', $trainerSkillsArray );
		
		$trainerLanguagesArray = MultiOptionList::choicesList ( 'trainer_to_trainer_language_option', 'trainer_id', $person_id, 'trainer_language_option', 'language_phrase', 'is_primary' );
		$this->viewAssignEscaped ( 'trainer_languages', $trainerLanguagesArray );
		foreach ( $trainerLanguagesArray as $lang ) {
			if ($lang ['is_primary'])
				$personArray ['primary_language_id'] = $lang ['id'];
		}
		
		//has training history
		$hasTrainerHistory = false;
		if ($trainerrow->person_id) {
			$hasTrainerHistory = true;
			//we could also look up any history now, but we'll be lazy
		}
		$this->view->assign ( 'hasTrainerHistory', $hasTrainerHistory );
		
		//facility types
		$typesArray = OptionList::suggestionList ( 'facility_type_option', 'facility_type_phrase', false, false );
		$this->viewAssignEscaped ( 'facility_types', $typesArray );
		
		//get home city name
		if ( $personrow->home_location_id ) {
		 list($cname,$prov,$dist,$regc ) = Location::getCityInfo($personrow->home_location_id, $this->setting('num_location_tiers'));
		 $personArray['home_city'] = $cname;
		 $personArray['home_region_c_id'] = $regc;
		 $personArray['home_district_id'] = $dist;
		 $personArray['home_province_id'] = $prov;		 
		}
		//sponsor types
		$sponsorsArray = OptionList::suggestionList ( 'facility_sponsor_option', 'facility_sponsor_phrase', false, false );
		$this->viewAssignEscaped ( 'facility_sponsors', $sponsorsArray );
		
		$this->viewAssignEscaped ( 'person', $personArray );
		
		//facilities list
		//$rowArray = OptionList::suggestionList('facility',array('facility_name','id'),false,9999);
		$rowArray = Facility::selectAllFacilities ( $this->setting('num_location_tiers') );
		
		$this->viewAssignEscaped ( 'facilities', $rowArray );
		
		//see if it is referenced anywhere
		$this->view->assign ( 'okToDelete', ((! $person_id) or (! Person::isReferenced ( $person_id ))) );
		
		// create reference for GET paramaters
		if ($this->_getParam ( 'trainingredirect' )) {
			$this->view->assign ( 'trainingredirect', $this->_getParam ( 'trainingredirect' ) );
		}
		if ($this->_getParam ( 'maketrainer' )) {
			$this->view->assign ( 'maketrainer', $this->_getParam ( 'maketrainer' ) );
		}
		if ($this->_getParam ( 'days' )) {
			$this->view->assign ( 'days', $this->_getParam ( 'days' ) );
		}
	
	}
	/*
   public function searchAction()
   {
 		require_once('models/table/OptionList.php');
		$provinceArray = OptionList::suggestionList('location_province','province_name',false,false,false);
		$this->view->assign('provinces',$provinceArray); //not used yet
   }

   public function searchSuggestAction()
   {
   	 $last = $this->getSanParam('query');
   	 $first = $this->getSanParam('first');

   	 $personTable = new Person();
	 $select = $personTable->select();
	 if ( $last ) {
	 	$select->where("last_name LIKE ? ",$last.'%');
		}
	 if ( $first ) {
	 	$select->where("first_name LIKE ? ",$first.'%');
	 }

 	$select->order("last_name ASC");
  	$select->order("first_name ASC");

	 $select->limit(100);

	 $rows = $personTable->fetchAll($select);
	 $rowArray = $rows->toArray();
	 $this->sendData($rowArray);
   }

*/
	public function searchAction() {
		require_once ('models/table/Person.php');
		
		if (! $this->hasACL ( 'view_people' ) and ! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		
		$criteria = array ();
		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);

		$criteria ['facility_name'] = $this->getSanParam ( 'facility_name' );
		$criteria ['facilityInput'] = $this->getSanParam ( 'facilityInput' );
		$criteria ['first_name'] = $this->getSanParam ( 'first_name' );
		$criteria ['last_name'] = $this->getSanParam ( 'last_name' );
		$criteria ['training_title_option_id'] = $this->getSanParam ( 'training_title_option_id' );
		
		$criteria ['person_type'] = $this->getSanParam ( 'person_type' );
		if (! $criteria ['person_type']){
			$criteria ['person_type'] = 'is_participant';
		}
			
		//   	 $criteria['type_id'] = $this->getSanParam('trainer_type_id');
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['outputType'] = $this->getSanParam ( 'outputType' );
		//   	 $criteria['language_id'] = $this->getSanParam('trainer_language_id');
		

		$criteria ['go'] = $this->getSanParam ( 'go' );
		
		if ($criteria ['go']) {
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();

			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id);
			
			if (trim($criteria['province_id'] != "")){
				$loc = true;
			} else {
				$loc = false;
			}

			if ($loc){
				if ($criteria ['person_type'] == 'is_everyone') {
					// left join instead of inner for everyone
					$sql = '
						SELECT DISTINCT p.id, p.last_name, p.middle_name, p.first_name, p.gender, p.birthdate, q.qualification_phrase, '.implode(',',$field_name).'
						FROM person as p 
						LEFT JOIN person_qualification_option as q ON p.primary_qualification_option_id = q.id
						LEFT JOIN facility as f ON p.facility_id = f.id
						JOIN ('.$location_sub_query.') as l ON f.location_id = l.id  ';
				
				} else {
					$sql = 'select DISTINCT p.id, p.last_name, p.middle_name, p.first_name, p.gender, p.birthdate, q.qualification_phrase, '.implode(',',$field_name).' from person as p, person_qualification_option as q, facility as f, ('.$location_sub_query.') as l';
				}
			} else {
				if ($criteria ['person_type'] == 'is_everyone') {
					// left join instead of inner for everyone
					$sql = '
						SELECT DISTINCT p.id, p.last_name, p.middle_name, p.first_name, p.gender, p.birthdate, q.qualification_phrase 
						FROM person as p 
						LEFT JOIN person_qualification_option as q ON p.primary_qualification_option_id = q.id
						LEFT JOIN facility as f ON p.facility_id = f.id ';
				
				} else {
					// Removed $field_name from SQL query. person table does
					// not have province_name, district_name, or city_name columns.
					$sql = 'select DISTINCT p.id, p.last_name, p.middle_name, 
						p.first_name, p.gender, p.birthdate, q.qualification_phrase 
						from person as p, person_qualification_option as q, 
						facility as f ';
				}
			}
			
			if ($criteria ['person_type'] == 'is_participant'){
				$sql .= ', person_to_training as ptt ';
			}
			if ($criteria ['person_type'] == 'is_trainer') {
				$sql .= ', trainer as trn';
				if ( $criteria['training_title_option_id'] ){
					$sql .= ', training_to_trainer as ttt ';
				}
			}
			
			if ($loc){
				$where = array('p.is_deleted = 0', 'f.location_id = l.id');
			} else {
				$where = array('p.is_deleted = 0');
			}
			if ($criteria ['person_type'] != 'is_everyone') { // was left joined (see above)
				if ($loc){
					$where []= 'p.primary_qualification_option_id = q.id and p.facility_id = f.id and f.location_id = l.id ';
				} else {
					$where []= 'p.primary_qualification_option_id = q.id and p.facility_id = f.id ';
				}
			}
			
			if ($criteria ['person_type'] == 'is_participant'){
				$where []= 'p.id = ptt.person_id ';
			}
			if ($criteria ['person_type'] == 'is_trainer'){
				$where []= 'p.id = trn.person_id ';
			}
			
			if ($criteria ['person_type'] == 'is_unattached_person'){
				$where []= 'p.id NOT IN (SELECT person_id FROM trainer) AND  p.id NOT IN (SELECT person_id FROM person_to_training)  ';
			}
						
			if ($criteria ['facilityInput']) {
				$where []= ' p.facility_id = "' . $criteria ['facilityInput'] . '"';
			}
			
			if ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0') {
				$sql .= ', training as tr  ';
				if ($criteria ['person_type'] == 'is_participant')
					$where []= ' p.id = ptt.person_id AND ptt.training_id = tr.id AND tr.training_title_option_id = ' . ($criteria ['training_title_option_id']) . ' ';
				if ($criteria ['person_type'] == 'is_trainer')
					$where []= ' p.id = trn.person_id AND trn.person_id = ttt.trainer_id AND ttt.training_id = tr.id AND tr.training_title_option_id = ' . ($criteria ['training_title_option_id']) . ' ';
			}
			
			if ($criteria ['qualification_id'] or $criteria ['qualification_id'] === '0') {
  				$where []= '(primary_qualification_option_id = ' . $criteria ['qualification_id'] . ' OR primary_qualification_option_id IN (SELECT id FROM person_qualification_option WHERE parent_id = ' . $criteria ['qualification_id'] . ')) ';
			}
			if ($criteria ['first_name']) {
				$where []= 'p.first_name LIKE "' . $criteria ['first_name'] . '%"';
			}
			if ($criteria ['last_name']) {
				$where []= 'p.last_name LIKE "' . $criteria ['last_name'] . '%"';
			}
			
			if ($where){
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
			
			$sql .= " ORDER BY " . " `p`.`last_name` ASC, " . " `p`.`first_name` ASC";
			
			//	$db->setFetchMode(Zend_Db::FETCH_ASSOC);
			
			$rowArray = $db->fetchAll ( $sql );
			
			if ($criteria ['outputType']) {
				$this->sendData ( $rowArray );
			}
			
			foreach($rowArray as $key => $row) {
				if ( isset( $row['gender'] ) and $row['gender'] ) {
					$rowArray[$key]['gender'] = t(trim($row['gender']));
				}
			}
						
			$this->viewAssignEscaped ( 'results', $rowArray );
			$this->view->assign ( 'count', count ( $rowArray ) );
		}
		
		$this->view->assign ( 'criteria', $criteria );
		
		//locations
	$this->viewAssignEscaped ( 'locations', Location::getAll() );
		
		
		//training titles
		require_once ('models/table/TrainingTitleOption.php');
		$titleArray = TrainingTitleOption::suggestionList ( false, 10000 );
		$this->viewAssignEscaped ( 'courses', $titleArray );
		//types
		$qualificationsArray = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false );
		$this->viewAssignEscaped ( 'qualifications', $qualificationsArray );
		
		//facilities list
		$rowArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $rowArray as $key => $val ) {
			if ($val ['id'] != 0)
				$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );
	
	}
	
	public function historyAction() {
		$person_id = $this->getSanParam ( 'id' );
		//history
		require_once ('models/table/History.php');
		$history = new History ( 'person' );
		$changes = $history->fetchAllPerson ( $person_id );
		foreach ( $changes as $ck => $crow ) {
			$c = $crow ['changes'];
			$keys = array ();
			$values = array ();
		unset ( $c ['pvid']);
		unset ( $c ['vid'] );
		unset ( $c ['id'] );
		unset ( $c ['timestamp_created'] );
		unset ( $c ['timestamp_updated'] );
		unset ( $c ['created_by'] );
		unset ( $c ['modified_by'] );
		unset ( $c ['active_trainer_option_id'] );
		unset ( $c ['is_active'] );
		unset ( $c ['secondary_responsibility_option_id'] );
		unset ( $c ['primary_responsibility_option_id'] );
		unset ( $c ['primary_qualification_option_id'] );
		unset ( $c ['facility_id'] );
		unset ( $c ['affiliation_option_id']);
			foreach ( $c as $k => $v ) {
				$translated = htmlentities ( ITechTranslate::db_tr ( $k ) );
			//	if ((strstr ( $translated, '_' ) == false)) {
					$keys [] = $translated;
					$values [] = htmlentities ( $v );
			//	}
			}
			$changes [$ck] ['translated_keys'] = implode ( ', ', $keys );
			$changes [$ck] ['translated_values'] = implode ( ', ', $values );
		}
		
		$this->sendData ( $changes );
	}
	
	public function custom1ListAction() {
		require_once ('models/table/OptionList.php');
		$rowArray = OptionList::suggestionList ( 'person_custom_1_option', 'custom1_phrase', $this->getSanParam ( 'query' ) );
		
		$this->sendData ( $rowArray );
	}
	
	public function custom2ListAction() {
		require_once ('models/table/OptionList.php');
		$rowArray = OptionList::suggestionList ( 'person_custom_2_option', 'custom2_phrase', $this->getSanParam ( 'query' ) );
		
		$this->sendData ( $rowArray );
	}
	
	 private function _attach_locations($rowArray) {
	 if ( $rowArray ) {
			$num_tiers = $this->setting('num_location_tiers');
			$locations = Location::getAll();
			foreach($rowArray as $id => $row) {
			list($city_name,  $prov_id, $dist_id, $regc_id) = Location::getCityInfo($row['location_id'], $num_tiers);
			$rowArray[$id]['province_name'] = $locations[$prov_id]['name'];
			$rowArray[$id]['district_name'] = $locations[$dist_id]['name'];
			$rowArray[$id]['region_c_name'] = $locations[$regc_id]['name'];
			}
		 }
	
	return $rowArray;
  }
  
	
	/**
	 * autocomplete ajax (person)
	 */
	public function lastListAction() {
		require_once ('models/table/Person.php');
		$rowArray = Person::suggestionQuery ( $this->_getParam ( 'query' ), 100, 'last_name', array ('p.last_name' ) )->toArray ();
	//$rowArray = $this->_attach_locations($rowArray);
			
		$this->sendData ( $rowArray );
	}
	
	/**
	 * autocomplete ajax (person)
	 */
	public function firstListAction() {
		require_once ('models/table/Person.php');
		$rowArray = Person::suggestionQuery ( $this->_getParam ( 'query' ), 100, 'first_name', array ('p.first_name' ) )->toArray ();
	//$rowArray = $this->_attach_locations($rowArray);
		$this->sendData ( $rowArray );
	}
	
	/**
	 * dupe check
	 */
	public function dupeListAction() {
		require_once ('models/table/Person.php');
		$fieldAnd = array ();
		
		if ($this->_getParam ( 'first_name' )) {
			$fieldAnd ["p.first_name"] = trim ( $this->_getParam ( 'first_name' ) );
		}
		if ($this->_getParam ( 'last_name' )) {
			$fieldAnd ["p.last_name"] = trim ( $this->_getParam ( 'last_name' ) );
		}
		if ($this->_getParam ( 'gender' )) {
			$fieldAnd ["p.gender"] = $this->_getParam ( 'gender' );
		}
		
		$rowArray = Person::suggestionFindDupes ( $this->_getParam ( 'last_name' ), 50, $this->setting ( 'display_middle_name_last' ), $fieldAnd );
		//$rowArray = Person::suggestionQuery($this->_getParam('last'),100,'last_name',array('p.last_name'))->toArray();
		foreach ( $rowArray as $key => $row ) {
			$rowArray [$key] = array_merge ( array ('input' => '<input type="radio" name="dupe_id" value="' . $rowArray [$key] ['id'] . '">' ), $row );
		}
		
		$this->sendData ( $rowArray );
	}
	
	// redirect back to training session
	function trainingRedirect($person_id) {
		$training_id = $this->_getParam ( 'trainingredirect' );
		
		// first, add trainer/person to training session
		if ($this->view->mode == 'add' || $this->getSanParam ( 'maketrainer' )) {
			
			if ($this->getSanParam ( 'is_trainer' ) || $this->getSanParam ( 'trainer_type_option_id' ) || $this->getSanParam ( 'active_trainer_option_id' )) { // trainer
				

				require_once ('models/table/TrainingToTrainer.php');
				$result = TrainingToTrainer::addTrainerToTraining ( $person_id, $training_id, $days = ($this->getSanParam ( 'days' ) ? $this->getSanParam ( 'days' ) : 0) );
			
			} else { // person
				require_once ('models/table/PersonToTraining.php');
				$tableObj = new PersonToTraining ( );
				$result = $tableObj->addPersonToTraining ( $person_id, $training_id );
			}
		}
		
		$status = ValidationContainer::instance ();
		$status->setRedirect ( '/training/edit/id/' . $training_id );
	}
	
	/**
	 * List trainings and allow person to be assigned
	 */
	public function assignTrainingAction() {
		$id = $this->getSanParam ( 'id' );
		$this->view->assign ( 'id', $id );
		
		$person = new Person ( );
		$rows = $person->find ( $id );
		$row = $rows->current ();
		$this->view->assign ( 'person_row', $row );
		
		require_once 'models/table/Training.php';
		$tableObj = new Training ( );
		$trainings = $tableObj->getTrainingsAssign ( $id );
		foreach ( $trainings as $k => $r ) {
			$trainings [$k] ['input_checkbox'] = '<input type="checkbox" name="training_ids[]" value="' . $r ['training_id'] . '">';
		}
		$this->view->assign ( 'trainings', $trainings );
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$status = ValidationContainer::instance ();
			$training_ids = $this->getSanParam ( 'training_ids' );
			if ($training_ids) {
				require_once ('models/table/PersonToTraining.php');
				
				foreach ( $training_ids as $training_id ) {
					$tableObj = new PersonToTraining ( );
					$result = $tableObj->addPersonToTraining ( $id, $training_id );
				}
				
				$status->setStatusMessage ( t ( 'The trainings have been assigned.' ) );
				$status->setRedirect ( '/person/edit/id/' . $id );
			} else {
				$status->setStatusMessage ( t ( 'No trainings selected.' ) );
			}
			
			$this->sendData ( $status );
			//print_r($training_ids);
		//exit;
		

		}
	}

}
