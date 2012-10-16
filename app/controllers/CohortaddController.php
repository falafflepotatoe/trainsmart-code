<?php
require_once ('ITechController.php');


class CohortaddController extends ITechController {
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
	
	public function cohortaddAction()
 	{
	
	$this->view->assign('title','Trainsmart');
	$this->view->assign('action','../cohortedit/cohortedit/');

	}
	
	public function cohortstudentAction()
	{
	$this->view->assign('title','Trainsmart');
	}
	
	public function cohortpracticumAction()
	{
	$this->view->assign('title','Trainsmart');
	
	}
	
	public function cohortclassAction()
	{
	$this->view->assign('title','Trainsmart');
	
	}
	
	public function cohortasspracticumAction()
	{
	$this->view->assign('title','Trainsmart');
	
	}
	
	public function cohortexamaddAction()
	{
	$this->view->assign('title','Trainsmart');
	
	}
}
?>