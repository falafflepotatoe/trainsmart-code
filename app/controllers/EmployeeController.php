<?php
require_once ('ReportFilterHelpers.php');
require_once ('FacilityController.php');
require_once ('models/table/Person.php');
require_once ('models/table/Facility.php');
require_once ('models/table/OptionList.php');
require_once ('models/table/MultiOptionList.php');
require_once ('models/table/Location.php');
require_once ('models/table/MultiAssignList.php');
require_once ('views/helpers/FormHelper.php');
require_once ('views/helpers/DropDown.php');
require_once ('views/helpers/Location.php');
require_once ('views/helpers/CheckBoxes.php');
require_once ('views/helpers/TrainingViewHelper.php');
require_once ('models/table/Helper.php');

class EmployeeController extends ReportFilterHelpers {

	public function init() {	}

	public function preDispatch() {
		parent::preDispatch ();

		if (! $this->isLoggedIn ())
			$this->doNoAccessError ();

		if (! $this->setting('module_employee_enabled')){
			$_SESSION['status'] = t('The employee module is not enabled on this site.');
			$this->_redirect('select/select');
		}

		if (! $this->hasACL ( 'edit_employee' )) {
			$this->doNoAccessError ();
		}
	}

	public function indexAction() {
		if (! $this->hasACL ( 'edit_employee' )) {
			$this->doNoAccessError ();
		}

		require_once('models/table/dash-employee.php');
		$this->view->assign('title', $this->translation['Application Name'].space.t('Employee Tracking System'));

		// restricted access?? does this user only have acl to view some trainings or people
		$org_allowed_ids = allowed_org_access_full_list($this); // doesnt have acl 'training_organizer_option_all'?
		$allowedWhereClause = $org_allowed_ids ? " training_organizer_option_id in ($org_allowed_ids) " : "";
		// restricted access?? only show organizers that belong to this site if its a multi org site
		$site_orgs = allowed_organizer_in_this_site($this); // for sites to host multiple training organizers on one domain
		$allowedWhereClause .= $site_orgs ? " AND training_organizer_option_id in ($site_orgs) " : "";

		$institute = new DashviewEmployee();
		$details=$institute->fetchdetails($org_allowed_ids);
		$this->view->assign('getins',$details);

		/****************************************************************************************************************/
		/* Attached Files */
		require_once('views/helpers/FileUpload.php');

		$PARENT_COMPONENT = 'employee';

		FileUpload::displayFiles ( $this, $PARENT_COMPONENT, 1, $this->hasACL ( 'admin_files' ) );
		// File upload form
		if ( $this->hasACL ( 'admin_files' ) ) {
			$this->view->assign ( 'filesForm', FileUpload::displayUploadForm ( $PARENT_COMPONENT, 1, FileUpload::$FILETYPES ) );
		}
		/****************************************************************************************************************/
	}

	/**
	 * JSON course add/delete/edit function for employee_edit
	 *
	 * see: employee_course_table.phtml
	 */
	public function coursesAction()
	{
		try {
			if (! $this->hasACL ( 'edit_employee' )) {
				if($this->_getParam('outputType') == 'json') {
					$this->sendData(array('msg'=>'Not Authorized'));
					exit();
					return;
				}
				$this->doNoAccessError ();
			}

			$ret    = array();
			$params = $this->getAllParams();

			if ($params['mode'] == 'addedit') {
				// add or update a record based on $params[id]
				if( empty($params['id']) )
					unset( $params['id'] ); // unset ID (primary key) for Zend if 0 or '' (insert new record)
				$id = $this->_findOrCreateSaveGeneric('employee_to_course', $params); // wrapper for find or create
				$params['id'] = $id;
				if($id){
					// saved
					// reload the data
					$db = $this->dbfunc();
					$ret = $db->fetchRow("select * from employee_to_course where id = $id");
					$ret['msg'] = 'ok';
				} else {
					$ret['errored'] = true;
					$ret['msg']     = t('Error creating record.');
				}
			}
			else if($params['mode'] == 'delete' && $params['id']) {
				// delete a record
				try {
					$course_link_table = new ITechTable ( array ('name' => 'employee_to_course' ) );
					$num_rows = $course_link_table->delete('id = ' . $params['id']);
					if (! $num_rows )
						$ret['msg'] = t('Error finding that record in the database.');
					$ret['num_rows'] = $num_rows;				
				} catch (Exception $e) {
					$ret['errored'] = true;
					$ret['msg']     = t('Error finding that record in the database.');
				}
			}

			if(strtolower($params['outputType']) == 'json'){
				$this->sendData($ret); // probably always json no need to check for output
			}					

		}
		catch (Exception $e) {
			if($this->_getParam('outputType') == 'json') {
					$this->sendData(array('errored' => true, 'msg'=>'Error: ' . $e->getMessage()));
					return;
			} else {
				echo $e->getMessage();
			}
		}

	}

	private function getCourses($employee_id){
		if (!$employee_id)
			return;

		$db = $this->dbfunc();
		$sql = "SELECT * FROM employee_to_course WHERE employee_id = $employee_id";
		$rows = $db->fetchAll($sql);
		return $rows ? $rows : array();
	}

	public function addAction() {
		$db = $this->dbfunc();
		$this->view->assign('mode', 'add');
		$this->view->assign ( 'pageTitle', t ( 'Add New' ).' '.t( 'Employee' ) );
		return $this->editAction ();
	}

	public function editAction() {
		if (! $this->hasACL ( 'edit_employee' )) {
			$this->doNoAccessError ();
		}

		$db = $this->dbfunc();
		$status = ValidationContainer::instance ();
		$params = $this->getAllParams();
		$id = $params['id'];

		// restricted access?? only show partners by organizers that we have the ACL to view
		$org_allowed_ids = allowed_org_access_full_list($this); 
		if ($org_allowed_ids && $this->view->mode != 'add') { // doesnt have acl 'training_organizer_option_all'
			$validID = $db->fetchCol("SELECT partner.id FROM partner WHERE partner.id = $id AND partner.organizer_option_id in ($org_allowed_ids)"); // check for both
			if(empty($validID))
				$this->doNoAccessError ();
		}

		if ( $this->getRequest()->isPost() )
		{
			//validate then save
			$params['location_id'] = regionFiltersGetLastID( '', $params );
			$params['agreement_end_date'] = $this->_date_to_sql( $params['agreement_end_date'] );
			$params['site_id']            = $params['facilityInput'];
			$params['full_time']          = ($params['full_time'] && $params['full_time'] !== 'off') ? 1 : 0; // checkbox
			
			$status->checkRequired ( $this, 'first_name', t ( 'Frist Name' ) );
			$status->checkRequired ( $this, 'last_name',  t ( 'Last Name' ) );
			if ( $this->setting['display_employee_partner'] )
				$status->checkRequired ( $this, 'partner_id', t ( 'Partner' ) );

			if (! $status->hasError() ) {
				$id = $this->_findOrCreateSaveGeneric('employee', $params);
				if(!$id) {
					$status->setStatusMessage( t('That person could not be saved.') );
				} else {

					MultiOptionList::updateOptions ( 'employee_to_role', 'employee_role_option', 'employee_id', $id, 'employee_role_option_id', $params['employee_role_option_id'] );
					$status->setStatusMessage( t('The person was saved.') );
					$this->_redirect("employee/edit/id/$id");
				}
			} else {
				$status->setStatusMessage( t('That person could not be saved.') );
			}
		}

		if ( $id && !$status->hasError() ) { // read data from db
			
			$sql = 'SELECT * FROM employee WHERE employee.id = '.$id;
			$row = $db->fetchRow( $sql );
			if ($row)
				$params = $row; // reassign form data
			else
				$status->setStatusMessage ( t('Error finding that record in the database.') );

			$region_ids = Location::getCityInfo($params['location_id'], $this->setting('num_location_tiers'));
			$region_ids = Location::regionsToHash($region_ids);
			$params = array_merge($params, $region_ids);
			$params['roles'] = $db->fetchCol("SELECT employee_role_option_id FROM employee_to_role WHERE employee_id = $id");
		}

		// assign form drop downs
		$params['agreement_end_date'] = formhelperdate($params['agreement_end_date']);
		$params['courses'] = $this->getCourses($id);
		$this->viewAssignEscaped ( 'employee', $params );
		$validCHWids = $db->fetchCol("select id from person_qualification_option pqo
										inner join (select id as success from person_qualification_option where qualification_phrase in ('Community Based Worker','Community')) parentIDs 
										on (parentIDs.success = pqo.id or parentIDs.success = pqo.parent_id)");
		$this->view->assign('validCHWids', $validCHWids);
		$this->view->assign('expandCHWFields', !(array_search($params['person_qualification_option_id'],$validCHWids) === false)); // i.e $validCHWids.contains($employee[qualification])
		$this->view->assign('status', $status);
		$this->view->assign ( 'pageTitle', $this->view->mode == 'add' ? t ( 'Add Employee' ) : t( 'Edit Employee' ) );
		$this->viewAssignEscaped ( 'locations', Location::getAll () );
		$titlesArray = OptionList::suggestionList ( 'person_title_option', 'title_phrase', false, 9999);
		$this->view->assign ( 'titles',      DropDown::render('title_option_id', $this->translation['Title'], $titlesArray, 'title_phrase', 'id', $params['title_option_id'] ) );
		$this->view->assign ( 'partners',    DropDown::generateHtml   ( 'partner', 'partner', $params['partner_id'], false, $this->view->viewonly, false ) );
		$this->view->assign ( 'subpartners', DropDown::generateHtml   ( 'partner', 'partner', $params['subpartner_id'], false, $this->view->viewonly, false, false, array('name' => 'subpartner_id'), true ) );
		$this->view->assign ( 'cadres',      DropDown::qualificationsDropDown('person_qualification_option_id', $params['person_qualification_option_id']) );
		$this->view->assign ( 'categories',  DropDown::generateHtml   ( 'employee_category_option', 'category_phrase', $params['employee_category_option_id'], false, $this->view->viewonly, false ) );
		$this->view->assign ( 'roles',       CheckBoxes::generateHtml ( 'employee_role_option', 'role_phrase', $this->view, $params['roles'] ) );
		$this->view->assign ( 'transitions', DropDown::generateHtml   ( 'employee_transition_option', 'transition_phrase', $params['employee_transition_option_id'], false, $this->view->viewonly, false ) );
		$helper = new Helper();
		$this->viewAssignEscaped ( 'facilities', $helper->getFacilities() );
		$this->view->assign ( 'relationships', DropDown::generateHtml ( 'employee_relationship_option', 'relationship_phrase', $params['employee_relationship_option_id'], false, $this->view->viewonly, false ) );
		$this->view->assign ( 'referrals',     DropDown::generateHtml ( 'employee_referral_option', 'referral_phrase', $params['employee_referral_option_id'], false, $this->view->viewonly, false ) );
		$this->view->assign ( 'provided',      DropDown::generateHtml ( 'employee_training_provided_option', 'training_provided_phrase', $params['employee_training_provided_option_id'], false, $this->view->viewonly, false ) );
		$employees = OptionList::suggestionList ( 'employee', array ('first_name' ,'CONCAT(first_name, CONCAT(" ", last_name)) as name' ), false, 99999 );
		$this->view->assign ( 'supervisors',   DropDown::render('supervisor_id', $this->translation['Supervisor'], $employees, 'name', 'id', $params['supervisor_id'] ) );
	}

	public function searchAction()
	{
		if (! $this->hasACL ( 'edit_employee' )) {
			$this->doNoAccessError ();
		}
		
		$criteria = $this->getAllParams();

		if ($criteria['go'])
		{
			// process search
			$where = array();
			
			list($a, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);
			list($locationFlds, $locationsubquery) = Location::subquery($this->setting('num_location_tiers'), $location_tier, $location_id);
			
			$sql = "SELECT DISTINCT
					employee.*,".implode(',',$locationFlds)." 
					,CONCAT(supervisor.first_name, CONCAT(' ', supervisor.last_name)) as supervisor,
					pqo.qualification_phrase as staff_cadre,
					site.facility_name,
					category.category_phrase as staff_category
					FROM employee INNER JOIN ($locationsubquery) as l ON l.id = employee.location_id 
					LEFT JOIN employee supervisor ON supervisor.id = employee.supervisor_id
					LEFT JOIN facility site ON site.id = employee.site_id
					LEFT JOIN person_qualification_option pqo ON pqo.id = employee.person_qualification_option_id
					LEFT JOIN employee_category_option category on category.id = employee.employee_category_option_id
					LEFT JOIN partner ON partner.id = employee.partner_id
					";

			#if ($criteria['partner_id']) $sql    .= ' INNER JOIN partner_to_subpartner subp ON partner.id = ' . $criteria['partner_id'];     

			// restricted access?? only show partners by organizers that we have the ACL to view
			$org_allowed_ids = allowed_org_access_full_list($this); // doesnt have acl 'training_organizer_option_all'
			if($org_allowed_ids)
				$where[] = " partner.organizer_option_id in ($org_allowed_ids) ";

			$locationWhere = $this->getLocationCriteriaWhereClause($criteria, '', '');
			if ($locationWhere) {
				$where[] = $locationWhere;
			}

			if ($criteria['first_name'])                      $where[] = "employee.first_name   = '{$criteria['first_name']}'";
			if ($criteria['last_name'])                       $where[] = "employee.last_name    = '{$criteria['last_name']}'";
			if ($criteria['partner_id'])                      $where[] = 'employee.partner_id   = '.$criteria['partner_id']; //todo
			if ($criteria['facilityInput'])                   $where[] = 'employee.site_id      = '.$criteria['facilityInput'];
			if ($criteria['person_qualification_option_id'])  $where[] = 'employee.person_qualification_option_id    = '.$criteria['person_qualification_option_id'];
			if ($criteria['category_option_id'])              $where[] = 'employee.staff_category_id = '.$criteria['category_option_id'];

			if ( count ($where) )
				$sql .= ' WHERE ' . implode(' AND ', $where);

			$db = $this->dbfunc();
			$rows = $db->fetchAll( $sql );
			$this->viewAssignEscaped('results', $rows);
			$this->viewAssignEscaped('count', count($rows));
		}
		// assign form drop downs
		$helper = new Helper();
		$this->view->assign('status', $status);
		$this->viewAssignEscaped ( 'criteria', $criteria );
		$this->viewAssignEscaped ( 'locations', Location::getAll () );
		$this->view->assign ( 'partners',    DropDown::generateHtml ( 'partner', 'partner', $criteria['partner_id'], false, $this->view->viewonly, false ) );
		$this->view->assign ( 'subpartners', DropDown::generateHtml ( 'partner', 'partner', $criteria['partner_id'], false, $this->view->viewonly, false, false, array('name' => 'subpartner_id'), true ) );
		$this->view->assign ( 'cadres',      DropDown::generateHtml ( 'person_qualification_option', 'qualification_phrase', $criteria['person_qualification_option_id'], false, $this->view->viewonly, false ) );
		$this->viewAssignEscaped ( 'sites', $helper->getFacilities() );
		$this->view->assign ( 'categories',  DropDown::generateHtml ( 'employee_category_option', 'category_phrase', $criteria['employee_category_option_id'], false, $this->view->viewonly, false ) );
	}
}

?>