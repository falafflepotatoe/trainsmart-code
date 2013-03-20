<?php
require_once ('ITechController.php');
require_once ('models/table/Peopleadd.php');
require_once ('models/table/Tutoredit.php');
require_once ('models/table/Helper.php');

class TutoreditController extends ITechController {
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ($request, $response, $invokeArgs );
	}
	
	public function init() {
	}
	
	public function preDispatch() {
		parent::preDispatch ();
		
		if (! $this->isLoggedIn ())
			$this->doNoAccessError ();
	
	}
	
	public function tutoreditAction(){
		if(isset($_POST['addpeople'])){
			# ADDING PERSON RECORD
			$peopleadd = new Peopleadd();
			$tutorid = $peopleadd->Peopleadd($_POST);
/*
			# ADDING ADDRESS RECORD
			$query = "INSERT INTO addresses SET
				address1 = '" . addslashes($_POST[address1]) . "',
				address2 = '" . addslashes($_POST[address2]) . "',
				city = '" . addslashes($_POST[zip]) . "',
				postalcode = '" . addslashes($_POST[address1]) . "',
				id_addresstype = '" . addslashes(1) . "',
				id_geog1 = '" . addslashes($_POST[province_id] ? $_POST[province_id] : 0) . "',
				id_geog2 = '" . addslashes($_POST[district_id] ? $_POST[district_id] : 0) . "',
				id_geog3 = '" . addslashes($_POST[region_c_id] ? $_POST[region_c_id] : 0) . "'";
die($query);
			$db = $this->dbfunc()->query($query);
			$id = $db->lastInsertId();


			# ADDING ADDRESS RECORD
			$query = "INSERT INTO link_tutor_addresses SET
				id_address = '" . addslashes($id) . "',
				id_tutor = '" . addslashes($tutorid) . "'";
die($query);

			$db = $this->dbfunc()->query($query);
			$id = $db->lastInsertId();
			# LINKING TO TUTOR RECORD
			$db = $this->dbfunc(); 		 
			$linkrec = array(
				'id_address'		=>	$id,
				'id_tutor'			=>	$tutorid,
			);
			$rowArray = $db->insert("link_tutor_addresses",$linkrec); 		
			$id = $db->lastInsertId();

*/	
			$this->_redirect(Settings::$COUNTRY_BASE_URL . "/tutoredit/tutoredit/id/" . $tutorid);
		}
		
		if(isset($_POST['update'])){
			$tutoredit = new Tutoredit();
			$updateperson=$tutoredit->UpdatePerson($_POST);	
			$updateperson=$tutoredit->UpdateTutor($_POST);	

			# STORE LANGUAGES ON EDIT

#			$updateperson=$tutoredit->UpdatePermanentAddress($_POST);	

#			$studentupdate = new Tutoredit();	
#			$update = $studentupdate->UpdatePerson($_POST);						# STORING PERSON RECORD
#			$update = $studentupdate->UpdateStudent($_POST);					# STORING STUDENT RECORD
#			$update = $studentupdate->UpdateStudentCohort($_POST);				# STORING COHORT LINK
#			$update = $studentupdate->UpdatePermanentAddress($_POST);			# STORING PERMANENT ADDRESS

			
			$tutorid = $_POST['id'];
			$this->view->assign('newid',$tutorid);
		}

		$request = $this->getRequest();
		$tutorid = $request->getParam('id');

	 	$this->view->assign('id',$tutorid);

		$this->viewAssignEscaped ('locations', Location::getAll());
		$Tutoredit = new Tutoredit();
		$details=$Tutoredit->EditTutor($tutorid);
		
		#print_r ($details);

		$date = $details['person'][0]['birthdate'];
		$dob = date("d-m-Y",strtotime($date));

		$this->view->assign('id',$tutorid);
		$this->view->assign('selid',$details['person'][0]['title_option_id']);	
		$this->view->assign('firstname',$details['person'][0]['first_name']);	
		$this->view->assign('middlename',$details['person'][0]['middle_name']);
		$this->view->assign('lastname',$details['person'][0]['last_name']);	
		$this->view->assign('nationalid',$details['person'][0]['national_id']);
		$this->view->assign('gender',$details['person'][0]['gender']);
		$this->view->assign('dob',$dob);	
		
		$helper = new Helper();

		$this->view->assign('lookupnationalities',$helper->getNationalities());
		$this->view->assign('lookupfacilities',$helper->getNationalities());
		$this->view->assign('lookupdegrees',$helper->getDegrees());
		$this->view->assign('tutorlanguages',$helper->getLanguages());

		$known = array();
		$tl = $helper->getTutorLanguages($details['tutor'][0]['id']);
		foreach ($tl as $_tl){
			$known[] = $_tl['id_language'];
		}
		$this->view->assign('knownlanguages',$known);

		$this->view->assign('lookuptutortypes',$helper->getTutorTypes());

		$tt = array();
		$types = $helper->getLinkedTutorTypes($details['tutor'][0]['id']);
		foreach ($types as $type){
			$tt[] = $type['id'];
		}
		$this->view->assign('tutortypes',$tt);
		
		$this->view->assign('students', $helper->getTutorStudents($tutorid));
		$this->view->assign('classes', $helper->getTutorClasses($tutorid));

		# GET ALL STUDENTS WHERE ADVISOR = THIS ID
		# GET ALL COURSES LINKED TO THIS ID

/*
		$this->view->assign('localgeo1',$details['student'][0]['geog1']);
		$this->view->assign('localgeo2',$details['student'][0]['geog2']);
		$this->view->assign('localgeo3',$details['student'][0]['geog3']);
		$this->view->assign('address1',$details['person'][0]['home_address_1']);
		$this->view->assign('address2',$details['person'][0]['home_address_2']);
		$this->view->assign('city',$details['person'][0]['city']);
		$this->view->assign('zip',$details['person'][0]['home_postal_code']);
		$this->view->assign('enroll',$details['person'][0]['home_address_2']);
		$this->view->assign('city',$details['person'][0]['city']);
		$this->view->assign('email',$details['person'][0]['email']);
		$this->view->assign('email2',$details['person'][0]['email_secondary']);
		$this->view->assign('titid',$details['person'][0]['title_option_id']);
		$this->view->assign('phone',$details['person'][0]['phone_work']);
		$this->view->assign('cell',$details['person'][0]['phone_mobile']);
		$this->view->assign('cadre',$details['person'][0]['cadre']);
		$this->view->assign('graduated',$details['student'][0]['isgraduated']);
		$this->view->assign('cohortid',$details['link_cohort'][0]['id_cohort']);
*/

		# FACILITY INFORMATION GEO
		$this->view->assign('localgeo1',$details['tutor'][0]['geog1']);
		$this->view->assign('localgeo2',$details['tutor'][0]['geog2']);
		$this->view->assign('localgeo3',$details['tutor'][0]['geog3']);

		$this->view->assign('facility',$details['tutor'][0]['facility']);
		$this->view->assign('cadre',$details['tutor'][0]['cadre']);
		$this->view->assign('tutorsince',$details['tutor'][0]['tutorsince']);
		$this->view->assign('tutortimehere',$details['tutor'][0]['tutortimehere']);
		$this->view->assign('cadre',$details['tutor'][0]['cadre']);
		$this->view->assign('degree',$details['tutor'][0]['degree']);
		$this->view->assign('degreeinst',$details['tutor'][0]['degreeinst']);
		$this->view->assign('degreeyear',$details['tutor'][0]['degreeyear']);
		$this->view->assign('languagesspoken',$details['tutor'][0]['languagesspoken']);
		$this->view->assign('positionsheld',$details['tutor'][0]['positionsheld']);
		$this->view->assign('comments',$details['tutor'][0]['comments']);
		$this->view->assign('nationality',$details['tutor'][0]['nationalityid']);


		# PERMANENT ADDRESS
		$this->view->assign('permanentgeo1',$details['permanent_address'][0]['id_geog1']);
		$this->view->assign('permanentgeo2',$details['permanent_address'][0]['id_geog2']);
		$this->view->assign('permanentgeo3',$details['permanent_address'][0]['id_geog3']);
		$this->view->assign('permanentaddress1',$details['permanent_address'][0]['address1']);
		$this->view->assign('permanentaddress2',$details['permanent_address'][0]['address2']);
		$this->view->assign('permanentcity',$details['permanent_address'][0]['city']);
		$this->view->assign('permanentzip',$details['permanent_address'][0]['postalcode']);

/*
		# STUDENT VIEW
		$this->view->assign('studentid',$details['student'][0]['studentid']);
		$this->view->assign('studenttype',$details['student'][0]['studenttype']);

		if ($details['student'][0]['enrollmentdate'] == "0000-00-00"){
			$enrolldate = "";
		} else {
			$enrolldate = date("d-m-Y", strtotime($details['student'][0]['enrollmentdate']));
		}
		$this->view->assign('enrollmentdate',$enrolldate);
		$this->view->assign('enrollmentreason',$details['student'][0]['enrollmentreason']);

		if ($details['student'][0]['separationdate'] == "0000-00-00"){
			$separationdate = "";
		} else {
			$separationdate = date("d-m-Y", strtotime($details['student'][0]['separationdate']));
		}
		$this->view->assign('separationdate',$separationdate);
		$this->view->assign('separationreason',$details['student'][0]['separationreason']);
		$this->view->assign('cadre',$details['student'][0]['cadre']);
		$this->view->assign('advisor',$details['student'][0]['advisorid']);
		$this->view->assign('yearofstudy',($details['student'][0]['yearofstudy'] != 0 ? $details['student'][0]['yearofstudy'] : ""));

*/
		// For Title List
		$listtitle = $Tutoredit->ListTitle();
		$this->view->assign('gettitle',$listtitle); 
#
#		# GETTING COHORTS
#		$listcohort = $Tutoredit->ListCohort();
#		$this->view->assign('getcohorts',$listcohort);
#
#		# GETTING CADRES
#		$listcadre = $Tutoredit->listCadre();
#		$this->view->assign('getcadres',$listcadre);
#		
#		# GETTING TUTORS
#		$listtutors = $Tutoredit->ListTutors();
#		$this->view->assign('gettutors',$listtutors);
	}
	
}
?>