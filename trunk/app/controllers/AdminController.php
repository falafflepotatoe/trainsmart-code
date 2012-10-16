<?php
/*
* Created on Feb 11, 2008
*
*  Built for web
*  Fuse IQ -- todd@fuseiq.com
*
*/
require_once('UserController.php');
require_once('models/table/OptionList.php');
require_once('models/table/Helper.php');
require_once('EditTableController.php');

class AdminController extends UserController
{

	private $_csvHandle = null;

	public function init()
	{

	}

	public function preDispatch()
	{
		$rtn =	parent::preDispatch();

		if ( !$this->isLoggedIn() )
			$this->doNoAccessError();

		if ( !$this->hasACL('edit_country_options') ) {
			$this->doNoAccessError();
		}

		return $rtn;

	}

/************************************************************************************
* Country
*/

	public function indexAction()
	{
		header("Location: " . Settings::$COUNTRY_BASE_URL . "/admin/country-labels");
		exit;
	}

	private function getSetting($field)
	{
		require_once('models/table/System.php');
		$sysTable = new System();
		return $sysTable->getSetting($field);
	}

	private function putSetting($field, $value)
	{
		require_once('models/table/System.php');
		$sysTable = new System();
		return $sysTable->putSetting($field, $value);
	}

	public function countrySettingsAction()
	{

		require_once('models/table/System.php');
		$sysTable = new System();

	// For "Labels"
		require_once('models/table/Translation.php');
	$labelNames = array( // input name => key_phrase
		'label_country'   => 'Country',
		'label_regiona'   => 'Region A (Province)',
		'label_regionb'   => 'Region B (Health District)',
		'label_regionc'   => 'Region C (Local Region)',
		'label_citytown'  => 'City or Town',
		'label_application_name' => 'Application Name'
		);

	// _system settings
	$checkboxFields = array( // input name => db field
	//         'check_regionb'     => 'display_region_b',
	//         'check_regionc'     => 'display_region_c',
		'check_mod_eval'     => 'module_evaluation_enabled',
		'check_mod_approvals'     => 'module_approvals_enabled',
		'check_mod_historical'     => 'module_historical_data_enabled',
		'check_mod_unknown'     => 'module_unknown_participants_enabled',
		'display_training_partner' => 'display_training_partner'
		);


	if($this->getRequest()->isPost()) { // Update db
		$updateData = array();
		$country = $this->_getParam('country');
		$this->putSetting('country', $country);

	// update translation labels
		$tranTable = new Translation();
		foreach($labelNames as $input_key => $db_key) {

			if ( $this->_getParam($input_key) ) {
				try {
					$tranTable->update(
						array('phrase' => $this->_getParam($input_key)),
						"key_phrase = '$db_key'"
						);
					$this->viewAssignEscaped($input_key, $this->_getParam($input_key));
				} catch(Zend_Exception $e) {
					error_log($e);
				}
			}
		}

	// save locale
		$updateData['locale_enabled'] = implode(',', $this->_getParam('locales'));
		if ( $this->_getParam('locale_default') )
			$updateData['locale'] = $this->_getParam('locale_default');

	// update _system (checkboxes)
		foreach($checkboxFields as $input_key => $db_field) {
			$value = ($this->_getParam($input_key) == NULL) ? 0 : 1;
			$updateData[$db_field] = $value;
			$this->view->assign($input_key, $value);
		}
		$sysTable->update($updateData, '');

	} else { // view

	// labels
		$t = Translation::getAll();
		foreach($labelNames as $input_key => $db_key) {
			$this->viewAssignEscaped($input_key, $t[$db_key]);
		}

	// checkboxes
		$sysRows = $sysTable->fetchRow($sysTable->select()->limit(1));
		foreach($checkboxFields as $input_key => $field_key) {
			$this->view->assign($input_key, $sysRows->$field_key);
		}
	}

	// country
	$country = $this->getSetting('country');
	$this->view->assign('country', htmlspecialchars($country));

	// locale
	$this->view->assign('languages', ITechTranslate::getLanguages());
	$this->view->assign('locale', $this->getSetting('locale'));
	$this->view->assign('locale_enabled', ITechTranslate::getLocaleEnabled());

	// redirect to next page
	if($this->_getParam('saveonly')) {
		$status = ValidationContainer::instance();
		$status->setStatusMessage(t('Your settings have been updated.'));
	//reload the page
		$this->_redirect('admin/country-settings');

	} else if($this->_getParam('redirect')) {
		header("Location: " . $this->_getParam('redirect'));
		exit;
	} 

	}

	public function addRegionTopAction() {
		if ( $this->setting('num_location_tiers') < 4) {
			Location::addTier(1);
			require_once('models/table/System.php');
			$sysTable = new System();
			$sysTable->update(array(($this->setting('display_region_b')?'display_region_c':'display_region_b')=>1), '');
		}

		$this->_redirect('admin/country-settings');

	}

	public function addRegionBottomAction() {
		if ( $this->setting('num_location_tiers') < 4) {
			Location::addTier($this->setting('num_location_tiers'));
			require_once('models/table/System.php');
			$sysTable = new System();
			$sysTable->update(array(($this->setting('display_region_b')?'display_region_c':'display_region_b')=>1), '');
		}

		$this->_redirect('admin/country-settings');

	}

	public function deleteRegionAction() {
		if ( $this->setting('num_location_tiers') > 2) {
			Location::collapseTier($this->setting('num_location_tiers') - 1);     	
			require_once('models/table/System.php');
			$sysTable = new System();
			$sysTable->update(array(($this->setting('display_region_c')?'display_region_c':'display_region_b')=>0), '');
		}
		$this->_redirect('admin/country-settings');
	}

	/*
	public function countryLabelsAction()
	{

		require_once('models/table/System.php');
		$sysTable = new System();

	// For "Labels"
		require_once('models/table/Translation.php');
	$labelNames = array( // input name => key_phrase
		'label_country'   => 'Country',
		'label_regiona'   => 'Region A (Province)',
		'label_regionb'   => 'Region B (Health District)',
		'label_citytown'  => 'City or Town',
	//'label_training_title'    => 'Training Title',
		'label_training_category' => 'Training Category',
		'label_training_topic'    => 'Training Topic',
		'label_training_name'     => 'Training Name',
		'label_training_org'      => 'Training Organizer',
		'label_training_level'    => 'Training Level',
		'label_training_got_curric' => 'GOT Curriculum',
		'label_training_got_comment'=> 'GOT Comment',
		'label_training_refresher'  => 'Refresher Course',
		'label_training_comments'   => 'Comments',
		'label_pepfar'            => 'PEPFAR Category',
		'label_training_trainers' => 'Training of Trainers',
		'label_course_objectives' => 'Course Objectives',
		'label_training_pre'      => 'Pre Test Score',
		'label_training_post'     => 'Post Test Score',
		'label_training_custom1'  => 'Training Custom 1',
		'label_training_custom2'  => 'Training Custom 2',
		'label_people_active'   => 'Is Active',
		'label_people_first'   => 'First Name',
		'label_people_middle'   => 'Middle Name',
		'label_people_last'   => 'Last Name',
		'label_people_national'   => 'National ID',
		'label_people_custom1'    => 'People Custom 1',
		'label_people_custom2'    => 'People Custom 2',
		'label_people_file_num'    => 'File Number',
		);

	// _system settings
	$checkboxFields = array( // input name => db field
		'check_training_topic' => 'display_training_topic',
		'check_training_trainers' => 'display_training_trainers',
		'check_training_got_curric'  => 'display_training_got_curric',
		'check_training_got_comment' => 'display_training_got_comment',
		'check_training_refresher'   => 'display_training_refresher',
		'check_course_objectives' => 'display_course_objectives',
		'check_training_pre'      => 'display_training_pre_test',
		'check_training_post'     => 'display_training_post_test',
		'check_training_custom1'  => 'display_training_custom1',
		'check_training_custom2'  => 'display_training_custom2',
		'check_people_active'   => 'display_people_active',
		'check_people_national'   => 'display_national_id',
		'check_people_middle'   => 'display_middle_name_last',
		'check_people_custom1'    => 'display_people_custom1',
		'check_people_custom2'    => 'display_people_custom2',
		'check_regionb'     => 'display_region_b',
		'check_people_file_num'    => 'display_people_file_num',
		'check_people_home_phone'  => 'display_people_home_phone',
		'check_people_fax'         => 'display_people_fax',
		);

	if($this->getRequest()->isPost()) { // Update db
		$updateData = array();
		$country = $this->_getParam('country');
		$this->putSetting('country', $country);

	// update translation labels
		$tranTable = new Translation();
		foreach($labelNames as $input_key => $db_key) {

			if ( $this->_getParam($input_key) ) {
				try {
					$tranTable->update(
						array('phrase' => $this->_getParam($input_key)),
						"key_phrase = '$db_key'"
						);
					$this->viewAssignEscaped($input_key, $this->_getParam($input_key));
				} catch(Zend_Exception $e) {
					error_log($e);
				}
			}
		}

	// save locale
		$updateData['locale_enabled'] = implode(',', $this->_getParam('locales'));
		if ( $this->_getParam('locale_default') )
			$updateData['locale'] = $this->_getParam('locale_default');

	// update _system (checkboxes)
		foreach($checkboxFields as $input_key => $db_field) {
			if ( $input_key == 'check_people_middle')
				$value = ($this->_getParam($input_key) == NULL) ? 0 : 1;
			else
				$value = ($this->_getParam($input_key) == NULL) ? 1 : 0;
			$updateData[$db_field] = $value;
			$this->view->assign($input_key, $value);
		}
		$sysTable->update($updateData, '');

	} else { // view

	// labels
		$t = Translation::getAll();
		foreach($labelNames as $input_key => $db_key) {
			$this->viewAssignEscaped($input_key, $t[$db_key]);
		}

	// checkboxes
		$sysRows = $sysTable->fetchRow($sysTable->select()->limit(1));
		foreach($checkboxFields as $input_key => $field_key) {
			$this->view->assign($input_key, $sysRows->$field_key);
		}
	}

	// country
	$country = $this->getSetting('country');
	$this->view->assign('country', htmlspecialchars($country));

	// locale
	$this->view->assign('languages', ITechTranslate::getLanguages());
	$this->view->assign('locale', $this->getSetting('locale'));
	$this->view->assign('locale_enabled', ITechTranslate::getLocaleEnabled());

	// redirect to next page
	if($this->_getParam('redirect')) {
		header("Location: " . $this->_getParam('redirect'));
		exit;
	} else if($this->_getParam('saveonly')) {
		$status = ValidationContainer::instance();
		$status->setStatusMessage(t('Your settings have been updated.'));
	}

	}
	*/
	public function trainingSettingsAction()
	{

		require_once('models/table/System.php');
		$sysTable = new System();

	// For "Labels"
		require_once('models/table/Translation.php');
	$labelNames = array( // input name => key_phrase
		'label_training_category' => 'Training Category',
		'label_training_topic'    => 'Training Topic',
		'label_training_name'     => 'Training Name',
		'label_training_org'      => 'Training Organizer',
		'label_training_level'    => 'Training Level',
		'label_training_got_curric' => 'GOT Curriculum',
		'label_training_got_comment'=> 'GOT Comment',
		'label_training_refresher'  => 'Refresher Course',
		'label_training_comments'   => 'Training Comments',
		'label_pepfar'            => 'PEPFAR Category',
		'label_training_trainers' => 'Training of Trainers',
		'label_course_objectives' => 'Course Objectives',
		'label_training_pre'      => 'Pre Test Score',
		'label_training_post'     => 'Post Test Score',
		'label_training_custom1'  => 'Training Custom 1',
		'label_training_custom2'  => 'Training Custom 2',
		'label_training_method'  => 'Training Method',
		'label_training_funding_amt' => 'Funding Amount',
		'label_primary_language' => 'Primary Language',
		'label_secondary_language' => 'Secondary Language',
		);

	// _system settings
	$checkboxFields = array( // input name => db field
		'check_training_topic' => 'display_training_topic',
		'check_training_trainers' => 'display_training_trainers',
		'check_training_got_curric'  => 'display_training_got_curric',
		'check_training_got_comment' => 'display_training_got_comment',
		'check_training_refresher'   => 'display_training_refresher',
		'check_course_objectives' => 'display_course_objectives',
		'check_training_pre'      => 'display_training_pre_test',
		'check_training_post'     => 'display_training_post_test',
		'check_training_custom1'  => 'display_training_custom1',
		'check_training_custom2'  => 'display_training_custom2',
		'check_training_method'  => 'display_training_method',
		'check_training_primary_language'  => 'display_primary_language',
		'check_training_secondary_language'  => 'display_secondary_language',
		'check_training_end_date' => 'display_end_date',
		'check_training_funding_options' => 'display_funding_options',
		'check_training_funding_amounts' => 'display_funding_amounts'

		);

	if($this->getRequest()->isPost()) { // Update db
		$updateData = array();

	// update translation labels
		$tranTable = new Translation();
		foreach($labelNames as $input_key => $db_key) {

			if ( $this->_getParam($input_key) ) {
				try {
					$tranTable->update(
						array('phrase' => $this->_getParam($input_key)),
						"key_phrase = '$db_key'"
						);
					$this->viewAssignEscaped($input_key, $this->_getParam($input_key));
				} catch(Zend_Exception $e) {
					error_log($e);
				}
			}
		}

	// update _system (checkboxes)
		foreach($checkboxFields as $input_key => $db_field) {
			$value = ($this->_getParam($input_key) == NULL) ? 0 : 1;
			$updateData[$db_field] = $value;
			$this->view->assign($input_key, $value);
		}
		$sysTable->update($updateData, '');

	} else { // view

	// labels
		$t = Translation::getAll();
		foreach($labelNames as $input_key => $db_key) {
			$this->viewAssignEscaped($input_key, $t[$db_key]);
		}

	// checkboxes
		$sysRows = $sysTable->fetchRow($sysTable->select()->limit(1));
		foreach($checkboxFields as $input_key => $field_key) {
			$this->view->assign($input_key, $sysRows->$field_key);
		}
	}

	// redirect to next page
	if($this->_getParam('redirect')) {
		header("Location: " . $this->_getParam('redirect'));
		exit;
	} else if($this->_getParam('saveonly')) {
		$status = ValidationContainer::instance();
		$status->setStatusMessage(t('Your settings have been updated.'));
	}

	}

	/*   public function countryProvstatesAction()
	{
		if(@$_FILES['import']['tmp_name']) {
			$filename = ($_FILES['import']['tmp_name']);
			if ( $filename ) {
				$provinceObj = new ITechTable(array('name' => 'location_province'));
				$districtObj = new ITechTable(array('name' => 'location_district'));
				while ($row = $this->_csv_get_row($filename) ) {
					if ( is_array($row) ) {
	//add province
						if ( isset($row[0] ) ) {
							$prov_id = $provinceObj->insertUnique('province_name',$row[0], true);
						}
	//add district
						if ( isset($row[1] ) ) {
							$dist_id = $districtObj->insertUnique('district_name',$row[1],true,'parent_province_id',$prov_id);
						}
					}

				}
			}
	//kinda ugly, but $this->_setParam doesn't do it
			$_POST['redirect'] = null;

		}

		$editTable = new EditTableController($this);
		$editTable->table   = 'location_province';
		$editTable->fields  = array('province_name' => 'Province/State');
		$editTable->label   = 'Province';
		$editTable->dependencies = array(
			array('parent_province_id' => 'location_district'),
			array('parent_province_id' => 'location_city'),
			'location_province_id' => 'training_location',
			'province_id' => 'facility',
			);
		$editTable->allowDefault = true;
		$editTable->execute();

	}
	*/
	public function countryRegionAction()
	{
		require_once('models/table/Location.php');

	//CSV STUFF

		if(@$_FILES['import']['tmp_name']) {
			$filename = ($_FILES['import']['tmp_name']);
			if ( $filename ) {
				$location_obj = new ITechTable(array('name' => 'location'));
				while ($row = $this->_csv_get_row($filename) ) {
					if ( is_array($row) ) {
	//add province
						if ( isset($row[0] ) ) {
							$prov_id = $location_obj->insertUnique('location_name',$row[0], true, 'tier',1);
						}
	//add district
						if ( isset($row[1] ) && $this->setting('display_region_b') ) {
							$dist_id = $location_obj->insertUnique('location_name',$row[1],true,'parent_id',$prov_id, 'tier',2);
						}
	//add region c
						if ( isset($row[2] ) && $this->setting('display_region_c')  ) {
							$dist_id = $location_obj->insertUnique('location_name',$row[2],true,'parent_id',$dist_id, 'tier',3);
						}
					}

				}
			}
	//kinda ugly, but $this->_setParam doesn't do it
			$_POST['redirect'] = null;

		}

	// list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues();

	//  $this->viewAssignEscaped('criteria',$criteria);
		$location_id = $this->getSanParam('location');
		$tier = $this->getSanParam('tier');
		if (!$tier) $tier = 1;
		$this->view->assign('tier', $tier);
		if ( $tier == 1 ) {
			$location_id = null;
		}

		$locations = Location::getAll();
		$this->viewAssignEscaped('locations',$locations);
	//assign district and region filters
	//strip off leading ids
		$loc_parts = explode('_',$location_id);
		$location_id = $loc_parts[count($loc_parts) - 1];

		if ( $location_id ) {
			if ( $locations[$location_id]['tier'] == 1 ) {
				$this->view->assign('province_id', $location_id);
			} else if  ($locations[$location_id]['tier'] == 2 ) {
				$this->view->assign('district_id', $location_id);
				$this->view->assign('province_id', $locations[$location_id]['parent_id']);
			} else if ($locations[$location_id]['tier'] == 3) {
				$this->view->assign('region_c_id', $location_id);
				$this->view->assign('district_id', $locations[$location_id]['parent_id']);
				$this->view->assign('province_id', $locations[$this->view->district_id]['parent_id']);
			} else if ($locations[$location_id]['tier'] > 3) {
	$this->view->assign('region_c_id', $loc_parts[0]); //todo either way works
	$this->view->assign('district_id', $loc_parts[1]);
	$this->view->assign('province_id', $loc_parts[2]);
	}
	}

	if ( ($tier == 1) OR ($location_id && ($locations[$location_id]['tier'] + 1 == $tier))) {
		$editTable = new EditTableController($this);
		$editTable->table   = 'location';
		$editTable->fields  = array('location_name' => ($tier>3?$this->tr('City or Town') : ($tier==3?$this->tr('Region C (Local Region)') : ($tier ==2 ? @$this->tr('Region B (Health District)') : @$this->tr('Region A (Province)') ))));
		$editTable->label   = ($tier > 3 ? $this->tr('City or Town') : ($tier==3?$this->tr('Region C (Local Region)') : ($tier ==2 ? @$this->tr('Region B (Health District)') : @$this->tr('Region A (Province)') )));
		$editTable->dependencies = array(
			'parent_id' => 'self',
			'location_id' => 'training_location',
			'home_location_id' => 'person',
			'location_id' => 'facility',
			);
		$editTable->where = 'tier = '.$tier.($location_id?' AND parent_id = '.$location_id:' ');
		if ( $location_id )
			$editTable->insertExtra = array('parent_id'=>$location_id, 'tier'=>$tier); 
		else
			$editTable->insertExtra = array('tier'=>1); 

		$editTable->allowDefault = true;
		$editTable->execute();
	}
	}

	public function countryRegionMoveAction()
	{
		require_once('models/table/Location.php');

	//  $this->viewAssignEscaped('criteria',$criteria);
		$location_id = $this->getSanParam('location');
		$tier = $this->getSanParam('tier');
		if (!$tier) $tier = 1;
		$this->view->assign('tier', $tier);
		if ( $tier == 1 ) {
			$location_id = null;
		}

		$locations = Location::getAll();

	//assign district and region filters
	//strip off leading ids
		$loc_parts = explode('_',$location_id);
		$location_id = $loc_parts[count($loc_parts) - 1];
		$this->view->assign('location_id',$location_id);

		if ( $location_id ) {
			if ( $locations[$location_id]['tier'] == 1 ) {
				$this->view->assign('province_id', $location_id);
			} else if  ($locations[$location_id]['tier'] == 2 ) {
				$this->view->assign('district_id', $location_id);
				$this->view->assign('province_id', $locations[$location_id]['parent_id']);
			} else if  ($locations[$location_id]['tier'] == 3) {
				$this->view->assign('district_id', $locations[$location_id]['parent_id']);
				$this->view->assign('province_id', $locations[$locations[$location_id]['parent_id']]['parent_id']);
				$this->view->assign('region_c_id', $location_id);
			}

			if ( $this->getRequest()->isPost() && $this->getSanParam('move')) {
				$target = null;
				switch($tier) {
					case 2:
					$target = $this->getSanParam('target_province_id');
					break;
					case 3:
					$target = $this->getSanParam('target_district_id');
					break;
					case 4:
					$target = $this->getSanParam('target_region_c_id');
					break;	       	
				}
				$target_parts = explode('_',$target);
				$target_id = $target_parts[count($target_parts) - 1];

				if ( $target_id && ($target_id != $location_id)) {        
					foreach($this->getSanParam('move') as $loc) {
						Location::moveLocation($loc, $target_id);
					}
				}

	//reload locations
				$locations = Location::getAll();

			}

	//get locations
			$candidates = array();
			foreach($locations as $l) {
				if ( $l['parent_id'] == $location_id )
					$candidates []= $l;
			}

			$this->viewAssignEscaped('candidates', $candidates);
		}

		$this->viewAssignEscaped('locations',$locations);

	}

	public function facilitiesSettingsAction()
	{

		require_once('models/table/System.php');
		$sysTable = new System();

	// For "Labels"
		require_once('models/table/Translation.php');
	$labelNames = array( // input name => key_phrase
		'label_facility'   => 'Facility',
		'label_comments'   => 'Facility Comments',
		);
	$checkboxFields = array();

	if($this->getRequest()->isPost()) { // Update db
		$updateData = array();

	// update translation labels
		$tranTable = new Translation();
		foreach($labelNames as $input_key => $db_key) {

			if ( $this->_getParam($input_key) ) {
				try {
					$tranTable->update(
						array('phrase' => $this->_getParam($input_key)),
						"key_phrase = '$db_key'"
						);
					$this->viewAssignEscaped($input_key, $this->_getParam($input_key));
				} catch(Zend_Exception $e) {
					error_log($e);
				}
			}
		}
	// update _system (checkboxes)
		foreach($checkboxFields as $input_key => $db_field) {
			$value = ($this->_getParam($input_key) == NULL) ? 0 : 1;
			$updateData[$db_field] = $value;
			$this->view->assign($input_key, $value);
		}
		$sysTable->update($updateData, '');

	} else { // view
	// checkboxes
		$sysRows = $sysTable->fetchRow($sysTable->select()->limit(1));
		foreach($checkboxFields as $input_key => $field_key) {
			if ( isset($sysRows->$field_key) )
				$this->view->assign($input_key, $sysRows->$field_key);
		}
	// labels
		$t = Translation::getAll();
		foreach($labelNames as $input_key => $db_key) {
			$this->viewAssignEscaped($input_key, $t[$db_key]);
		}

	}

	// redirect to next page
	if($this->_getParam('redirect')) {
		header("Location: " . $this->_getParam('redirect'));
		exit;
	} else if($this->_getParam('saveonly')) {
		$status = ValidationContainer::instance();
		$status->setStatusMessage(t('Your settings have been updated.'));
	}
	}

	public function peopleSettingsAction()
	{

		require_once('models/table/System.php');
		$sysTable = new System();

	// For "Labels"
		require_once('models/table/Translation.php');
	$labelNames = array( // input name => key_phrase
		'label_people_active'   => 'Is Active',
		'label_people_title'   => 'Title',
		'label_people_first'   => 'First Name',
		'label_people_middle'   => 'Middle Name',
		'label_people_last'   => 'Last Name',
		'label_people_suffix'   => 'Suffix',
		'label_people_national'   => 'National ID',
		'label_people_file_num'    => 'File Number',
		'label_people_age'   => 'Age',
		'label_people_custom1'    => 'People Custom 1',
		'label_people_custom2'    => 'People Custom 2',
		'label_responsibility_me'    => 'M&E Responsibility',
		'label_highest_ed_level'    => 'Highest Education Level',
		'label_attend_reason'    => 'Reason Attending',
		'label_primary_responsibility'    => 'Primary Responsibility',
		'label_secondary_responsibility'    => 'Secondary Responsibility',
		'label_comments'    => 'Qualification Comments'
		);

	// _system settings
	$checkboxFields = array( // input name => db field
		'check_people_title'   => 'display_people_title',
		'check_people_active'   => 'display_people_active',
		'check_people_suffix'   => 'display_people_suffix',
		'check_people_national'   => 'display_national_id',
		'check_people_middle' => 'display_middle_name',
		'check_middle_last'   => 'display_middle_name_last',
		'check_people_custom1'    => 'display_people_custom1',
		'check_people_custom2'    => 'display_people_custom2',
	//      'check_regionb'     => 'display_region_b',
		'check_people_file_num'    => 'display_people_file_num',
		'check_people_age'    => 'display_people_age',
		'check_people_home_address'    => 'display_people_home_address',
		'check_people_home_phone'  => 'display_people_home_phone',
		'check_people_second_email'  => 'display_people_second_email',
		'check_people_fax'         => 'display_people_fax',
		'check_trainer_affiliations' => 'display_trainer_affiliations',
		'check_responsibility_me'    => 'display_responsibility_me',
		'check_highest_ed_level'    => 'display_highest_ed_level',
		'check_attend_reason'    => 'display_attend_reason',
		'check_external_classes'  => 'display_external_classes',
		'check_primary_responsibility'  => 'display_primary_responsibility',
		'check_secondary_responsibility'  => 'display_secondary_responsibility'       

		);

	if($this->getRequest()->isPost()) { // Update db
		$updateData = array();

	// update translation labels
		$tranTable = new Translation();
		foreach($labelNames as $input_key => $db_key) {

			if ( $this->_getParam($input_key) ) {
				try {
					$tranTable->update(
						array('phrase' => $this->_getParam($input_key)),
						"key_phrase = '$db_key'"
						);
					$this->viewAssignEscaped($input_key, $this->_getParam($input_key));
				} catch(Zend_Exception $e) {
					error_log($e);
				}
			}
		}

	// update _system (checkboxes)
		foreach($checkboxFields as $input_key => $db_field) {
			$value = ($this->_getParam($input_key) == NULL) ? 0 : 1;
			$updateData[$db_field] = $value;
			$this->view->assign($input_key, $value);
		}
		$sysTable->update($updateData, '');

	} else { // view

	// labels
		$t = Translation::getAll();
		foreach($labelNames as $input_key => $db_key) {
			$this->viewAssignEscaped($input_key, $t[$db_key]);
		}

	// checkboxes
		$sysRows = $sysTable->fetchRow($sysTable->select()->limit(1));
		foreach($checkboxFields as $input_key => $field_key) {
			$this->view->assign($input_key, $sysRows->$field_key);
		}
	}

	// redirect to next page
	if($this->_getParam('redirect')) {
		header("Location: " . $this->_getParam('redirect'));
		exit;
	} else if($this->_getParam('saveonly')) {
		$status = ValidationContainer::instance();
		$status->setStatusMessage(t('Your settings have been updated.'));
	}

	}
	/*
	public function countryDistrictsAction()
	{

		$province = $this->getSanParam('province');

		if ( $province or $this->getSanParam('redirect') ) {
			$editTable = new EditTableController($this);
			$editTable->table   = 'location_district';
			$editTable->fields  = array('district_name' => 'District');
			$editTable->label   = 'District';
			$editTable->where = 'parent_province_id = '.$province;
			$editTable->insertExtra = array('parent_province_id'=>$province);
			$editTable->allowDefault = true;

			$editTable->dependencies = array(
				'parent_district_id' => 'location_city',
				'location_district_id' => 'training_location',
				'district_id' => 'facility',
				);

			$editTable->execute();
		}

		$provinceArray = OptionList::suggestionList('location_province','province_name',false,false);
		$this->viewAssignEscaped('provinces',$provinceArray);
		$this->viewAssignEscaped('province', $province);

	}
	*/
	/************************************************************************************
	* Training
	*/

	public function trainingCategoryAction()
	{
		$editTable = new EditTableController($this);
		$editTable->table   = 'training_category_option';
		$editTable->fields  = array('training_category_phrase' => 'Training Category');
		$editTable->label   = 'Training Category';
		$editTable->execute();
	}

	public function trainingTitleAction()
	{
		$editTable = new EditTableController($this);
		$editTable->table   = 'training_title_option';
		$editTable->fields  = array('training_title_phrase' => 'Training Title');
		$editTable->label   = 'Training Title';
		$editTable->allowMerge = true;
		$editTable->dependencies = array('training');
		$editTable->execute();
	}

	public function trainingOrganizerAction()
	{
		$editTable = new EditTableController($this);
		$editTable->table   = 'training_organizer_option';
		$editTable->fields  = array('training_organizer_phrase' => 'Training Organizer');
		$editTable->label   = 'Training Organizer';
		$editTable->dependencies = array('training');
		$editTable->allowDefault = true;
		$editTable->execute();
	}

	public function trainingLevelAction()
	{
		$editTable = new EditTableController($this);
		$editTable->table   = 'training_level_option';
		$editTable->fields  = array('training_level_phrase' => 'Training Level');
		$editTable->label   = 'Training Level';
		$editTable->dependencies = array('training');
		$editTable->allowDefault = true;
		$editTable->execute();
	}

	public function trainingTopicAction()
	{
		/* checkbox */
		$fieldSystem = 'allow_multi_topic';

	if($this->getRequest()->isPost() && !$this->_getParam("id")) { // Update db
		$this->putSetting($fieldSystem, $this->_getParam($fieldSystem));
	}

	$checkbox = array(
		'name'  => $fieldSystem,
		'label' => 'Allow multiple Training topics',
		'value' => $this->getSetting($fieldSystem),
		);
	$this->view->assign('checkbox', $checkbox);

	$editTable = new EditTableController($this);
	$editTable->table   = 'training_topic_option';
	$editTable->fields  = array('training_topic_phrase' => 'Training Topic');
	$editTable->label   = 'Training Topic';
	$editTable->dependencies = array('training_to_training_topic_option');
	$editTable->allowDefault = true;
	$editTable->execute();
	}

	public function trainingPepfarAction()
	{
		/* checkbox */
		$fieldSystem = 'allow_multi_pepfar';

	if($this->getRequest()->isPost() && !$this->_getParam("id")) { // Update db
		$this->putSetting($fieldSystem, $this->_getParam($fieldSystem));
	}

	$checkbox = array(
		'name'  => $fieldSystem,
		'label' => 'Allow multiple PEPFAR categories',
		'value' => $this->getSetting($fieldSystem),
		);
	$this->view->assign('checkbox', $checkbox);

	/* edit table */
	$editTable = new EditTableController($this);
	$editTable->table   = 'training_pepfar_categories_option';
	$editTable->fields  = array('pepfar_category_phrase' => 'PEPFAR Category');
	$editTable->label   = 'PEPFAR Category';
	$editTable->dependencies = array('training_to_training_pepfar_categories_option');
	$editTable->allowDefault = true;
	$editTable->execute();
	}

	public function trainingFundingAction()
	{
		/* checkbox */
		$fieldSystem = 'display_funding_options';

	if($this->getRequest()->isPost() && !$this->_getParam("id")) { // Update db
		$this->putSetting($fieldSystem, $this->_getParam($fieldSystem));
	}

	/* edit table */
	$editTable = new EditTableController($this);
	$editTable->table   = 'training_funding_option';
	$editTable->fields  = array('funding_phrase' => 'Funding');
	$editTable->label   = 'Funding';
	$editTable->dependencies = array('training_to_training_funding_option');
	$editTable->allowDefault = true;
	$editTable->execute();
	}

	public function trainingGotcurriculumAction()
	{
		/* edit table */
		$editTable = new EditTableController($this);
		$editTable->table   = 'training_got_curriculum_option';
		$editTable->fields  = array('training_got_curriculum_phrase' => 'National Curriculum');
		$editTable->label   = 'National Curriculum';
		$editTable->dependencies = array('training');
		$editTable->execute();
	}

	public function trainingMethodAction()
	{
		/* edit table */
		$editTable = new EditTableController($this);
		$editTable->table   = 'training_method_option';
		$editTable->fields  = array('training_method_phrase' => 'Method');
		$editTable->label   = 'Training Methods';
		$editTable->dependencies = array('training_to_training_pepfar_categories_option');
		$editTable->execute();
	}

	public function trainingRecommendAction()
	{
		$NUM_TOPICS = 20;
		$this->view->assign('NUM_TOPICS', $NUM_TOPICS);

		/* checkbox */
		$fieldSystem = 'display_training_recommend';

	if($this->getRequest()->isPost() && !$this->_getParam("id")) { // Update db
		$this->putSetting($fieldSystem, $this->_getParam($fieldSystem));
	}

	$checkbox = array(
		'name'  => $fieldSystem,
		'label' => 'Display recommended trainings per individual',
		'value' => $this->getSetting($fieldSystem),
		);
	$this->view->assign('checkbox', $checkbox);

	require_once('models/table/TrainingRecommend.php');

	// Save POST
	if($this->getRequest()->isPost()) { // Update db
		if(is_numeric($this->_getParam('person_qualification_option_id'))) {
			TrainingRecommend::saveRecommendations(
				$this->_getParam('person_qualification_option_id'),
				$this->_getParam('training_topic_option_id')
				);

	// Remove current, then redirect to clean page
			if($this->_getParam('edit') && $this->_getParam('edit') != $this->_getParam('person_qualification_option_id')) {
				TrainingRecommend::saveRecommendations($this->_getParam('edit'), array());
				header("Location: " . Settings::$COUNTRY_BASE_URL . '/admin/training-recommend');
				exit;
			}
		}

	// redirect to next page
		if($this->_getParam('redirect')) {
			header("Location: " . $this->_getParam('redirect'));
			exit;
		} else if($this->_getParam('saveonly')) {
			$status = ValidationContainer::instance();
			$status->setStatusMessage('Your recommended trainings have been saved.');
		}
	}

	// Edting
	if($this->_getParam('edit') || $this->_getParam('edit') === '0' ) {
		$qualId = $this->_getParam('edit');
		$topicId = array_fill(1, $NUM_TOPICS, '');
		$topics = TrainingRecommend::getRecommendations($this->_getParam('edit'));
		$pos = 0;
		foreach($topics->ToArray() as $row) {
			$topicId[++$pos] = $row['training_topic_option_id'];
		}
	} else { // New
		$qualId = 0;
		$topicId = array_fill(1, $NUM_TOPICS, '');
	}

	// Delete
	if($delete = $this->_getParam('delete')) {
		TrainingRecommend::saveRecommendations($this->_getParam('delete'), array());
	}

	require_once 'views/helpers/DropDown.php';
	require_once 'models/table/OptionList.php';
	//$allowIds = TrainingRecommend::getQualificationIds(); // primary qualifications only
	//$this->view->assign('dropDownQuals', DropDown::generateHtml('person_qualification_option','qualification_phrase',$qualId, false, false, $allowIds));

	$qualificationsArray = OptionList::suggestionListHierarchical('person_qualification_option','qualification_phrase',false,false);
	// remove children qualifications and unknown as an option
	foreach($qualificationsArray as $k => $qualArray) {
		if  ($qualArray['id'] || $qualArray['parent_phrase'] == 'unknown') {
			unset($qualificationsArray[$k]);
		}
	}
	$this->viewAssignEscaped('qualifications',$qualificationsArray);
	$this->viewAssignEscaped('qualId',$qualId);

	for($j = 1; $j <= $NUM_TOPICS; $j++) {
		$this->view->assign('dropDownTopic' . $j, DropDown::generateHtml('training_topic_option','training_topic_phrase',$topicId[$j], false, false, false, true));
	}

	}

	public function trainingAssignTitleAction()
	{

		require_once('views/helpers/MultiAssign.php');

		$multiAssign = new multiAssign();
		$multiAssign->table = 'training_category_option_to_training_title_option';

		$multiAssign->option_table = 'training_title_option';
		$multiAssign->option_field = array('training_title_phrase' => 'Title');

		$multiAssign->parent_table = 'training_category_option';
		$multiAssign->parent_field = array('training_category_phrase' => 'Training Category');;

		$output = $multiAssign->init($this);
	if(is_array($output)) { // json
		$this->sendData($output);
	} else {
		$this->view->assign('multiAssign', $output);
	}

	if($this->getRequest()->isPost()) { // Redirect
	// redirect to next page
		if($this->_getParam('redirect')) {
			header("Location: " . $this->_getParam('redirect'));
			exit;
		} else if($this->_getParam('saveonly')) {
			$status = ValidationContainer::instance();
			$status->setStatusMessage('Your assigned categories have been saved.');
		}
	}

	return;

	$NUM_TOPICS = 20;
	$this->view->assign('NUM_TOPICS', $NUM_TOPICS);

	/* checkbox */
	$fieldSystem = 'display_training_recommend';

	if($this->getRequest()->isPost() && !$this->_getParam("id")) { // Update db
		$this->putSetting($fieldSystem, $this->_getParam($fieldSystem));
	}

	$checkbox = array(
		'name'  => $fieldSystem,
		'label' => 'Display recommended trainings per individual',
		'value' => $this->getSetting($fieldSystem),
		);
	$this->view->assign('checkbox', $checkbox);

	require_once('models/table/TrainingRecommend.php');

	// Save POST
	if($this->getRequest()->isPost()) { // Update db
		if(is_numeric($this->_getParam('person_qualification_option_id'))) {
			TrainingRecommend::saveRecommendations(
				$this->_getParam('person_qualification_option_id'),
				$this->_getParam('training_topic_option_id')
				);

	// Remove current, then redirect to clean page
			if($this->_getParam('edit') && $this->_getParam('edit') != $this->_getParam('person_qualification_option_id')) {
				TrainingRecommend::saveRecommendations($this->_getParam('edit'), array());
				header("Location: " . Settings::$COUNTRY_BASE_URL . '/admin/training-recommend');
				exit;
			}

		}

	// redirect to next page
		if($this->_getParam('redirect')) {
			header("Location: " . $this->_getParam('redirect'));
			exit;
		} else if($this->_getParam('saveonly')) {
			$status = ValidationContainer::instance();
			$status->setStatusMessage('Your recommended trainings have been saved.');
		}
	}

	// Edting
	if($this->_getParam('edit') || $this->_getParam('edit') === '0' ) {
		$qualId = $this->_getParam('edit');
		$topicId = array_fill(1, $NUM_TOPICS, '');
		$topics = TrainingRecommend::getRecommendations($this->_getParam('edit'));
		$pos = 0;
		foreach($topics->ToArray() as $row) {
			$topicId[++$pos] = $row['training_topic_option_id'];
		}
	} else { // New
		$qualId = 0;
		$topicId = array_fill(1, $NUM_TOPICS, '');
	}

	// Delete
	if($delete = $this->_getParam('delete')) {
		TrainingRecommend::saveRecommendations($this->_getParam('delete'), array());
	}

	require_once 'views/helpers/DropDown.php';
	require_once 'models/table/OptionList.php';
	//$allowIds = TrainingRecommend::getQualificationIds(); // primary qualifications only
	//$this->view->assign('dropDownQuals', DropDown::generateHtml('person_qualification_option','qualification_phrase',$qualId, false, false, $allowIds));

	$qualificationsArray = OptionList::suggestionListHierarchical('person_qualification_option','qualification_phrase',false,false);
	// remove children qualifications and unknown as an option
	foreach($qualificationsArray as $k => $qualArray) {
		if  ($qualArray['id'] || $qualArray['parent_phrase'] == 'unknown') {
			unset($qualificationsArray[$k]);
		}
	}
	$this->viewAssignEscaped('qualifications',$qualificationsArray);
	$this->viewAssignEscaped('qualId',$qualId);

	for($j = 1; $j <= $NUM_TOPICS; $j++) {
		$this->view->assign('dropDownTopic' . $j, DropDown::generateHtml('training_topic_option','training_topic_phrase',$topicId[$j], false, false, false, true));
	}

	}

	public function listByRecommendAction() {
		require_once('models/table/TrainingRecommend.php');
		$rowArray = TrainingRecommend::getRecommendedAdmin();
		foreach($rowArray as $key => $row) {
			$rowArray[$key]['edit'] = '<a href="' . Settings::$COUNTRY_BASE_URL . '/admin/training-recommend/edit/'. $row['person_qualification_option_id'] . '#edit">edit</a>&nbsp;' .
			'<a href="' . Settings::$COUNTRY_BASE_URL . '/admin/training-recommend/delete/'. $row['person_qualification_option_id'] . '" onclick="return confirm(\'Are you sure you wish to remove these recommendations?\')">delete</a>';
		}
		$this->sendData($rowArray);
	}

	/************************************************************************************
	* People (Person) / Trainer
	*/

	public function peopleQualAction()
	{
		$parent = $this->getSanParam('parent');

		if ( $parent or $this->getSanParam('redirect') ) {
			$editTable = new EditTableController($this);
			$editTable->table   = 'person_qualification_option';
			$editTable->fields  = array('qualification_phrase' => 'Qualification');
			$editTable->label   = 'Person Qualification';
			$editTable->dependencies = array('primary_qualification_option_id' => 'person');
			$editTable->where = 'parent_id = '.$parent;
			$editTable->insertExtra = array('parent_id'=>$parent);
			$editTable->allowDefault = true;
			$editTable->execute();
		}

		$parentArray = OptionList::suggestionList('person_qualification_option','qualification_phrase',false,false, true, 'parent_id IS NULL');
		$this->viewAssignEscaped('parents',$parentArray);
		$this->view->assign('parent', $parent);

	}

	public function peopleResponsibilityAction()
	{
		return $this->peoplePrimaryrespAction();
	}

	public function peoplePrimaryrespAction() // was peopleResponsibilityAction
	{
		$editTable = new EditTableController($this);
		$editTable->table   = 'person_primary_responsibility_option';
		$editTable->fields  = array('responsibility_phrase' => 'Primary Responsibility');
		$editTable->label   = 'Primary Responsibility';
		$editTable->dependencies = array('primary_responsibility_option_id' => 'person');
		$editTable->execute();
	}

	public function peopleSecondaryrespAction()
	{
		$editTable = new EditTableController($this);
		$editTable->table   = 'person_secondary_responsibility_option';
		$editTable->fields  = array('responsibility_phrase' => 'Secondary Responsibility');
		$editTable->label   = 'Secondary Responsibility';
		$editTable->dependencies = array('secondary_responsibility_option_id' => 'person');
		$editTable->execute();
	}    

	public function peopleTypesAction()
	{
		$editTable = new EditTableController($this);
		$editTable->table   = 'trainer_type_option';
		$editTable->fields  = array('trainer_type_phrase' => 'Type');
		$editTable->label   = 'Trainer Type';
		$editTable->dependencies = array('type_option_id' => 'trainer');
		$editTable->execute();
	}

	public function peopleSkillsAction()
	{

		$editTable = new EditTableController($this);
		$editTable->table   = 'trainer_skill_option';
		$editTable->fields  = array('trainer_skill_phrase' => 'Trainer Skill');
		$editTable->label   = 'Trainer Skill';
		$editTable->dependencies = array('trainer_to_trainer_skill_option');
		$editTable->execute();
	}

	public function peopleLanguagesAction()
	{

		$editTable = new EditTableController($this);
		$editTable->table   = 'trainer_language_option';
		$editTable->fields  = array('language_phrase' => 'Language');
		$editTable->label   = 'Language';
		$editTable->dependencies = array('trainer_to_trainer_language_option');
		$editTable->execute();
	}

	public function peopleAffiliationsAction()
	{

		/* edit table */

		$editTable = new EditTableController($this);
		$editTable->table   = 'trainer_affiliation_option';
		$editTable->fields  = array('trainer_affiliation_phrase' => 'Affiliation');
		$editTable->label   = 'Affiliation';
		$editTable->dependencies = array('affiliation_option_id' => 'trainer');
		$editTable->execute();
	}

	public function peopleTitleAction()
	{

		/* edit table */
		$editTable = new EditTableController($this);
		$editTable->table   = 'person_title_option';
		$editTable->fields  = array('title_phrase' => 'Title');
		$editTable->label   = 'Title';
		$editTable->dependencies = array('title_option_id' => 'person');
		$editTable->execute();
	}

	public function peopleSuffixAction()
	{

		/* edit table */
		$editTable = new EditTableController($this);
		$editTable->table   = 'person_suffix_option';
		$editTable->fields  = array('suffix_phrase' => 'Suffix');
		$editTable->label   = 'Suffix';
		$editTable->dependencies = array('suffix_option_id' => 'person');
		$editTable->execute();
	}

	public function peopleActiveTrainerAction()
	{

		/* edit table */
		$editTable = new EditTableController($this);
		$editTable->table   = 'person_active_trainer_option';
		$editTable->fields  = array('active_trainer_phrase' => 'Active Trainer');
		$editTable->label   = 'Active Trainer';
		$editTable->dependencies = array('active_trainer_option_id' => 'trainer');
		$editTable->execute();
	}

	public function peopleHighestedulevelAction()
	{

		/* edit table */
		$editTable = new EditTableController($this);
		$editTable->table   = 'person_education_level_option';
		$editTable->fields  = array('education_level_phrase' => 'Highest Education Level');
		$editTable->label   = 'Highest Education Level';
		$editTable->dependencies = array('highest_edu_level_option_id' => 'person');
		$editTable->execute();
	}        

	public function peopleAttendreasonAction()
	{

		/* edit table */
		$editTable = new EditTableController($this);
		$editTable->table   = 'person_attend_reason_option';
		$editTable->fields  = array('attend_reason_phrase' => 'Reason Attending');
		$editTable->label   = 'Reason Attending';
		$editTable->dependencies = array('attend_reason_option_id' => 'person');
		$editTable->execute();
	}

	/************************************************************************************
	* Facilities
	*/

	public function facilitiesTypesAction()
	{
		$editTable = new EditTableController($this);
		$editTable->table   = 'facility_type_option';
		$editTable->fields  = array('facility_type_phrase' => 'Facility Type');
		$editTable->label   = 'Facility Type';
		$editTable->dependencies = array('type_option_id' => 'facility');
		$editTable->execute();
	}

	public function facilitiesSponsorsAction()
	{
		$editTable = new EditTableController($this);
		$editTable->table   = 'facility_sponsor_option';
		$editTable->fields  = array('facility_sponsor_phrase' => 'Facility Sponsor');
		$editTable->label   = 'Facility Sponsor';
		$editTable->dependencies = array('sponsor_option_id' => 'facility');
		$editTable->execute();
	}

	/************************************************************************************
	* Internal
	*/

	protected function _csv_get_row($filepath, $reset = FALSE) {
		ini_set('auto_detect_line_endings',true);

		if ($filepath == '') {
			$this->_csvHandle = null;
			return FALSE;
		}

		if (!$this->_csvHandle || $reset) {
			if ($this->_csvHandle) {
				fclose($this->_csvHandle);
			}
			$this->_csvHandle = fopen($filepath, 'r');
		}

		return fgetcsv($this->_csvHandle, 10000, ',');
	}

	public function usersAddAction() {

		return parent::addAction();
	}

	public function preserviceClassesAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addClasses($_POST);
				break;
				case "update":
				$helper->updateClasses($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-classes' );
		}

		$list = $helper->AdminClasses();
		$coursetypes = $helper->AdminCourseTypes();
		$tutors = $helper->getAllTutors();
		$this->view->assign("lookup", $list);
		$this->view->assign("coursetypes", $coursetypes);
		$this->view->assign("tutors", $tutors);
		$this->view->assign("header","Classes");
	}

	public function preserviceCadresAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addCadres($_POST);
				break;
				case "update":
				$helper->updateCadres($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-cadres' );
		}

		$list = $helper->AdminCadres();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Cadres");
	}

	public function preserviceDegreesAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addDegrees($_POST);
				break;
				case "update":
				$helper->updateDegrees($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-degrees' );
		}

		$list = $helper->AdminDegrees();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Degrees");
	}

	public function preserviceCoursetypesAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addCoursetypes($_POST);
				break;
				case "update":
				$helper->updateCoursetypes($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-coursetypes' );
		}

		$list = $helper->AdminCoursetypes();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Course types");
	}

	public function preserviceFundingAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addFunding($_POST);
				break;
				case "update":
				$helper->updateFunding($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-funding' );
		}

		$list = $helper->AdminFunding();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Funding sources");
	}

	public function preserviceInstitutiontypesAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addInstitutiontypes($_POST);
				break;
				case "update":
				$helper->updateInstitutiontypes($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-institutiontypes' );
		}

		$list = $helper->AdminInstitutionTypes();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Institution types");
	}

	public function preserviceLanguagesAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addLanguages($_POST);
				break;
				case "update":
				$helper->updateLanguages($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-languages' );
		}

		$list = $helper->AdminLanguages();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Languages");
	}

	public function preserviceNationalitiesAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addNationalities($_POST);
				break;
				case "update":
				$helper->updateNationalities($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-nationalities' );
		}

		$list = $helper->AdminNationalities();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Nationalities");
	}

	public function preserviceJoindropreasonsAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addJoindropreasons($_POST);
				break;
				case "update":
				$helper->updateJoindropreasons($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-joindropreasons' );
		}

		$list = $helper->AdminJoinDropReasons();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Join & drop reasons");
	}

	public function preserviceSponsorsAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addSponsors($_POST);
				break;
				case "update":
				$helper->updateSponsors($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-sponsors' );
		}

		$list = $helper->AdminSponsors();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Sponsors");
	}

	public function preserviceStudenttypesAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addStudenttypes($_POST);
				break;
				case "update":
				$helper->updateStudenttypes($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-studenttypes' );
		}

		$list = $helper->AdminStudenttypes();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Student types");
	}

	public function preserviceTutortypesAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addTutortypes($_POST);
				break;
				case "update":
				$helper->updateTutortypes($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-tutortypes' );
		}

		$list = $helper->AdminTutortypes();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Tutor types");
	}

	public function preserviceReligionAction(){
		$helper = new Helper();

		if (isset ($_POST['_action'])){
			switch ($_POST['_action']){
				case "addnew":
				$helper->addReligion($_POST);
				break;
				case "update":
				$helper->updateReligion($_POST);
				break;
			}
			$this->_redirect ( 'admin/preservice-religion' );
		}

		$list = $helper->AdminStudenttypes();
		$this->view->assign("lookup", $list);
		$this->view->assign("header","Religious denominations");
	}

}
?>