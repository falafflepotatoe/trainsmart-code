<?php

require_once ('ITechController.php');
require_once ('models/table/peoplefind.php');
require_once ('models/table/peopleview.php');
require_once ('models/table/Helper.php');
class PeoplefindController extends ITechController {
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



	public function peoplefindAction(){
		$people = new Peoplefind();
		$peoples = $people->peoplesearch($_POST);

		$helper = new Helper();
		$cohort = $helper->getCohorts();
		$cadre = $helper->getCadres();
		$institution = $helper->getInstitutions();
		$facility = $helper->getFacilities();

		$this->view->assign('title','Trainsmart');
		$this->view->assign('cohort',$cohort);
		$this->view->assign('cadre',$cadre);
		$this->view->assign('institution',$institution);
		$this->view->assign('facility',$facility);

		$this->view->assign('getpeople',$peoples);



	}

	public function personviewAction() {

		$request = $this->getRequest();
		$getid = $request->getParam('id');



		$people = new Peopleview();
		$getpeople = $people->ViewPeople($getid);
		$this->view->Assign('title','Trainsmart');
		$this->view->Assign('view',$getpeople);


		$date =	$getpeople[0]['birthdate'];
		$dob = date("m-d-Y",strtotime($date));

		$this->view->assign('fname',$getpeople[0]['first_name']);
		$this->view->assign('middlename',$getpeople[0]['middle_name']);
		$this->view->assign('lastname',$getpeople[0]['last_name']);
		$this->view->assign('gender',$getpeople[0]['gender']);
		$this->view->assign('dob',$dob);
		$this->view->assign('address1',$getpeople[0]['home_address_1']);
		$this->view->assign('address2',$getpeople[0]['home_address_2']);
		$this->view->assign('pwork',$getpeople[0]['phone_work']);
		$this->view->assign('phone',$getpeople[0]['phone_mobile']);
		$this->view->assign('email',$getpeople[0]['email']);
		$this->view->assign('email2',$getpeople[0]['email_secondary']);
		$this->view->assign('zip',$getpeople[0]['home_postal_code']);


		#	$listtitle = $people->ListTitle();
		$listtitle = "Search people";
		$this->view->assign('gettitle',$listtitle);
	}
}
?>