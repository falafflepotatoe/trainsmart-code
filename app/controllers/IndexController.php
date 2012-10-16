<?php
/*
 * Created on Feb 11, 2008
 *
 *  Built for web
 *  Fuse IQ -- todd@fuseiq.com
 *
 */
require_once ('ITechController.php');

class IndexController extends ITechController {
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	}
	
	public function init() {
	}
	
	public function indexAction() {
		
		if (strstr($_SERVER['REQUEST_URI'],'index/index') === false) {
			$this->_redirect('index/index');
			exit();
		}
		
		// retrieve list of incomplete courses created by user
		if ($this->hasACL ( 'edit_course' )) {
			require_once 'models/table/Training.php';
			require_once 'models/Session.php';
			
			$uid = Session::getCurrentUserId ();
			
			// Find incomplete training and future trainings
			

			require_once 'views/helpers/EditTableHelper.php';
			
			$trainingFields = array ('training_title' => t ( 'Course Name' ), 'training_start_date' => t ( 'Start Date' ), 'training_location_name' => t ( 'Training Center' ), 'creator' => t ( 'Created By' ) );
			
			foreach ( array_keys ( $trainingFields ) as $key ) {
				$colCustom [$key] = 'sortable:true';
			}
			
			$colStatic = array_keys ( $trainingFields ); // all
			

			$editLinkInfo ['disabled'] = 1;
			
			$linkInfo = array (// add links to datatable fields
				'linkFields' => array_keys ( $trainingFields ), // all
				'linkId' => 'id', // use ths value in link
				'linkUrl' => Settings::$COUNTRY_BASE_URL . '/training/edit/id/%id%' );
			
			// Incomplete
			$tableObj = new Training ( );
			$rowsPast = $tableObj->getIncompleteTraining ( $uid, 'training_start_date < NOW()' )->toArray ();
			if ($rowsPast) {
				$html = EditTableHelper::generateHtmlTraining ( 'TrainingPast', $rowsPast, $trainingFields, $colStatic, $linkInfo, $editLinkInfo, $colCustom );
				$this->view->assign ( 'tableTrainingPast', $html );
			}
			
			// Future
			$tableObj = new Training ( );
			$rowsFuture = $tableObj->getIncompleteTraining ( $uid, 'training_start_date >= NOW()', '' )->toArray ();
			if ($rowsFuture) {
				$html = EditTableHelper::generateHtmlTraining ( 'TrainingFuture', $rowsFuture, $trainingFields, $colStatic, $linkInfo, $editLinkInfo, $colCustom );
				$this->view->assign ( 'tableTrainingFuture', $html );
			}
			
			if ($this->setting ( 'module_approvals_enabled' )) {
				$tableObj = new Training ( );
				$unapproved = $tableObj->getUnapprovedTraining ();
				if ($unapproved) {
					$linkInfo2 = $linkInfo;
					if (! $this->hasACL ( 'approve_trainings' )) {
						$linkInfo2 ['linkFields'] = array ('training_title');
					}
					
					$trainingFields2 = $trainingFields;
					$trainingFields2['message'] = t('Message');
					
					$html = EditTableHelper::generateHtmlTraining ( 'unapproved', $unapproved, $trainingFields2, $colStatic, $linkInfo2, $editLinkInfo, $colCustom );
					$this->view->assign ( 'tableUnapproved', $html );
				}
			}
			
			//YTD, start at April 1
			$ytdStart = (date ( 'Y' ) - ((date ( 'n' ) < 4) ? 1 : 0)) . '-04-01';
			$this->view->assign ( 'ytdStart', $ytdStart );
			//get total unique participants
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$sql = "SELECT COUNT(DISTINCT person_id) as \"unique_p\" from person_to_training";
			$rowArray = $db->fetchRow ( $sql );
			$this->view->assign ( 'unique_participants', $rowArray ['unique_p'] );
			
			//get participants total and by YTD
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$sql = "SELECT COUNT(person_id) as \"attendees\" from person_to_training";
			$rowArray = $db->fetchRow ( $sql );
			$this->view->assign ( 'attendees', $rowArray ['attendees'] );
			
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$sql = "SELECT COUNT(person_id) as  \"attendees\" FROM training, person_to_training as pt WHERE pt.training_id = training.id AND training_start_date >= '$ytdStart'";
			$rowArray = $db->fetchRow ( $sql );
			$this->view->assign ( 'attendees_ytd', $rowArray ['attendees'] );
			
			//get total unique trainers
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$sql = "SELECT COUNT(person_id) as \"unique_t\" from trainer";
			$rowArray = $db->fetchRow ( $sql );
			$this->view->assign ( 'unique_trainers', $rowArray ['unique_t'] );
			
			//get total trainers and by YTD
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$sql = "SELECT COUNT(trainer_id) as \"trainers\" FROM training_to_trainer";
			$rowArray = $db->fetchRow ( $sql );
			$this->view->assign ( 'trainers', $rowArray ['trainers'] );
			
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$sql = "SELECT COUNT(tt.trainer_id) as \"trainers\" FROM training, training_to_trainer as tt WHERE tt.training_id = training.id AND training_start_date >= '$ytdStart'";
			$rowArray = $db->fetchRow ( $sql );
			$this->view->assign ( 'trainers_ytd', $rowArray ['trainers'] );
			
			//get trainings
			//    total and YTD
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$sql = "SELECT COUNT(id) as \"trainings\", MIN(training_start_date) as \"min_date\",MAX(training_start_date) as \"max_date\"  from training WHERE is_deleted = 0";
			$rowArray = $db->fetchRow ( $sql );
			$this->view->assign ( 'trainings', $rowArray ['trainings'] );
			$this->view->assign ( 'min_date', $rowArray ['min_date'] );
			$this->view->assign ( 'max_date', $rowArray ['max_date'] );
			
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$sql = "SELECT COUNT(id) as \"trainings\"  from training WHERE training_start_date >= '$ytdStart' AND is_deleted = 0";
			$rowArray = $db->fetchRow ( $sql );
			$this->view->assign ( 'trainings_ytd', $rowArray ['trainings'] );
		
		}
	
    /****************************************************************************************************************/
    /* Attached Files */
    require_once 'views/helpers/FileUpload.php';
		
    $PARENT_COMPONENT = 'home';
    
    FileUpload::displayFiles ( $this, $PARENT_COMPONENT, 1, $this->hasACL ( 'admin_files' ) );
     // File upload form
     if ( $this->hasACL ( 'admin_files' ) ) {
        $this->view->assign ( 'filesForm', FileUpload::displayUploadForm ( $PARENT_COMPONENT, 1, FileUpload::$FILETYPES ) );
     }
    /****************************************************************************************************************/
	}
	
	public function testAction() {
	}
	
	public function languageAction() {
		require_once ('models/Session.php');
		require_once ('models/table/User.php');
		if ($this->isLoggedIn () and array_key_exists ( $this->getSanParam ( 'opt' ), ITechTranslate::getLanguages () )) {
			$user = new User ( );
			$userRow = $user->find ( Session::getCurrentUserId () )->current ();
			$user->updateLocale ( $this->getSanParam ( 'opt' ), Session::getCurrentUserId () );
			
			$auth = Zend_Auth::getInstance ();
			$identity = $auth->getIdentity ();
			$identity->locale = $this->getSanParam ( 'opt' );
			$auth->getStorage ()->write ( $identity );
			setcookie ( 'locale', $this->getSanParam ( 'opt' ), null, Globals::$BASE_PATH );
		}
		
		$this->_redirect ( $_SERVER ['HTTP_REFERER'] );
	
	}
	
	public function jsAggregateAction() {
		#$headers = apache_request_headers ();
		
		// Checking if the client is validating his cache and if it is current.
		/*
	    if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) > time() - 60*60*24)) {
	        // Client's cache IS current, so we just respond '304 Not Modified'.
	        header('Last-Modified: '.gmdate('D, d M Y H:i:s',  time()).' GMT', true, 304);
			$this->setNoRenderer();
	    }
echo Globals::$BASE_PATH.Globals::$WEB_FOLDER.$file;
exit;
*/
		$response = $this->getResponse ();
		$response->clearHeaders ();
		//allow cache
		#$response->setHeader ( 'Expires', gmdate ( 'D, d M Y H:i:s', time () + 60 * 60 * 30 ) . ' GMT', true );
		#$response->setHeader ( 'Cache-Control', 'max-age=7200, public', true );
		#$response->setHeader ( 'Last-Modified', '', true );
		$response->setHeader ( 'Cache-Control',  "public, must-revalidate, max-age=".(60*60*24*7), true ); // new ver TS 4.0 new JS file,wassup...
		$response->setHeader ( 'Pragma', 'public', true );
		$response->setHeader ( 'Last-Modified',''.date('D, d M Y H:i:s', strtotime('9 September 2012 04:00')).' GMT', true );

	}

}
?>