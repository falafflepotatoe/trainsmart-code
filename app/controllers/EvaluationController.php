<?php
/*
 * Created on Dec 3, 2009
 *
 *  Built for web
 *  Fuse IQ -- todd@fuseiq.com
 *
 */
require_once ('ITechController.php');
require_once ('models/table/Evaluation.php');
require_once ('models/table/Training.php');
/**
 * Handles external classes
 *
 */
class EvaluationController extends ITechController {
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	}

	public function init() {

	}

	public function indexAction() {
		return $this->_redirect ( 'evaluation/browse' );
	}

	public function browseAction() {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT * FROM evaluation WHERE is_deleted = 0";
		$rowArray = $db->fetchAll ( $sql );
		$this->view->assign ( 'evaluations', $rowArray );

	}

	protected function _fetchQuestions($evaluation_id = false) {
		
		if ( $evaluation_id )
		  $id = $evaluation_id;
		else 
		  $id = $this->getSanParam ( 'id' );
		
		if ($id) {
			$ev = new Evaluation ( );
			$source_row = $ev->findOrCreate ( $id );
			if ($source_row->id) {
				$title = $source_row->title;
				$question_rows = $ev->fetchAllQuestions ( $id );
				$qtext = array ();
				$qtype = array ();
				$qid = array ();
				foreach ( $question_rows as $qr ) {
					$qtext [] = $qr->question_text;
					$qtype [] = $qr->question_type;
					$qid [] = $qr->id;
				}
			}
		} else {
			$this->_redirect ( 'error' );
		}

		return array ($title, $qtext, $qtype, $qid );
	}

	public function printAction() {

		list ( $title, $qtext, $qtype, $qid ) = $this->_fetchQuestions ();

		$this->view->assign ( 'title', $title );
		$this->view->assign ( 'qtext', $qtext );
		$this->view->assign ( 'qtype', $qtype );
		$this->view->assign ( 'qid', $qid );

	}
	public function dataAction() {
		$request = $this->getRequest ();
		$id = $this->getSanParam ( 'id' );
		
		//get training and evaluation ids
		list($evaluation_id, $training_id) = Evaluation::fetchAssignment($id);
		if (!$evaluation_id) {
		  $status->setStatusMessage ( t ( 'The evaluation could not be loaded.' ) );
		  return;
		}
		
		//load training
	   $trainingTable = new Training();
	   $course_name = $trainingTable->getCourseName($training_id);
	   $this->view->assign('course_name', $course_name);
		
		list ( $title, $qtext, $qtype, $qid ) = $this->_fetchQuestions ($evaluation_id);

		if ($request->isPost ()) {
			//validate
			$status = ValidationContainer::instance ();

			if ($status->hasError ()) {
				$status->setStatusMessage ( t ( 'The evaluation could not be saved.' ) );
			} else {

				//make sure we have at least one response
				$found = false;
				foreach ( $qid as $qidi ) {
					if ($this->getSanParam ( 'value_' . $qidi ) !== null && $this->getSanParam ( 'value_' . $qidi ) !== '')
						$found = true;
				}

				if ($found) {
					//save response row
					$qr_table = new ITechTable ( array ('name' => 'evaluation_response' ) );
					$qr_row = $qr_table->createRow ();
					$qr_row->evaluation_to_training_id = $id;
					$qr_id = $qr_row->save ();

					//save question rows
					$erq_table = new ITechTable ( array ('name' => 'evaluation_question_response' ) );
					foreach ( $qid as $qk => $qidi) {
						//  $q_table = new ITechTable(array('name'=>'evaluation_question'));
						$qrow = $erq_table->createRow ();
						$qrow->evaluation_response_id = $qr_id;
						$qrow->evaluation_question_id = $qidi;
						$response_value = $this->getSanParam ( 'value_' . $qidi );
						if ($qtype[$qk] == 'Text') {
							$qrow->value_text = $response_value;
						} else {
							$qrow->value_int = $response_value;
						}
						if ($response_value)
							$qrow->save ();
					}
				}

				if ($this->getSanParam ( 'go' ))
					$this->_redirect ( 'training/edit/id/'.$training_id );
			}
		}

		$this->view->assign ( 'title', $title );
		$this->view->assign ( 'qtext', $qtext );
		$this->view->assign ( 'qtype', $qtype );
		$this->view->assign ( 'qid', $qid );

	}

	public function addCopyAction() {

		$request = $this->getRequest ();

		$qtext = $this->getSanParam ( 'qtext' );
		$qtype = $this->getSanParam ( 'qtype' );
		$title = $this->getSanParam ( 'title' );

		if ($request->isPost ()) {
			//validate
			$status = ValidationContainer::instance ();
			$status->checkRequired ( $this, 'title', t ( 'Title' ) );
      foreach($qtext as $i=>$t) {
      	if ( $t and !$qtype[$i] ) {
      		$status->addError('qtype', t('You must choose a question type for each question.'));
      		break;
       	}
        if ( !$t and $qtype[$i] ) {
          $status->addError('qtype', t('You must enter question text for each question.'));
          break;
        }
      }
			
			if ($status->hasError ()) {
				$status->setStatusMessage ( t ( 'The evaluation could not be saved.' ) );
			} else {
				$ev = new Evaluation ( );
				$ev_row = $ev->createRow ( array ('title' => $title ) );

				if ($id = $ev_row->save ()) {
					$ev->insertQuestions ( $id, $qtext, $qtype );
					$status->setStatusMessage ( t ( 'The new evaluation was created.' ) );
					$this->_redirect ( 'evaluation/browse' );
				} else {
					$status->setStatusMessage ( t ( 'The evaluation could not be saved.' ) );
				}

			}

		} else if ($id = $this->getSanParam ( 'id' )) {
			list ( $title, $qtext, $qtype, $qid ) = $this->_fetchQuestions ();
		}

		$this->view->assign ( 'title', $title );
		$this->view->assign ( 'qtext', $qtext );
		$this->view->assign ( 'qtype', $qtype );

	}

	public function assignEvaluationAction() {
		$id = $this->getSanParam ( 'id' );
		$this->view->assign ( 'id', $id );

		$evaluation = new Evaluation ( );
		$rows = $evaluation->find ( $id );
		$row = $rows->current ();
		$this->view->assign ( 'evaluation', $row );

		require_once 'models/table/Training.php';
		$tableObj = new Training ( );
		$trainings = $tableObj->getTrainings();
		$assigned = $evaluation->fetchAssignments($id);

		foreach ( $trainings as $k => $r ) {
			$trainings [$k] ['input_checkbox'] = '<input type="checkbox" name="training_ids[]" value="' . $r ['training_id'] . '" '.(array_search($r['training_id'],$assigned) !== false? ' checked="checked" ': '').' >';
		}
		$this->view->assign ( 'trainings', $trainings );

		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$status = ValidationContainer::instance ();
			$training_ids = $this->getSanParam ( 'training_ids' );
			$adjusted = array();
			foreach($training_ids as $tr) {
                      $adjusted[$tr] = $tr;
			}
			require_once('models/table/MultiOptionList.php');
			MultiOptionList::updateOptions('evaluation_to_training', 'training', 'evaluation_id', $id, 'training_id', $adjusted);

			$_SESSION['status'] = ( 'The trainings have been assigned.' );
			$status->setStatusMessage ( t ( 'The trainings have been assigned.' ) );

		      $status->setRedirect ( '/evaluation/browse' );

			$this->sendData ( $status );


		}
	}

		public function assignTrainingAction() {
		$id = $this->getSanParam ( 'id' );
		$this->view->assign ( 'id', $id );

		require_once('models/table/Training.php');
		require_once('models/table/OptionList.php');
		$training = new Training ( );
		$rows = $training->find ( $id );
		$row = $rows->current ();
		$this->view->assign ( 'training', $row );
		$this->view->assign('training_name', $training->getCourseName($id));

		$evaluations = OptionList::suggestionList('evaluation', array('id','title'));
		$this->view->assign ( 'evaluations', $evaluations );

		//find currently selected
     		$evalTable = new OptionList(array('name' => 'evaluation_to_training'));
    	       $select = $evalTable->select()->from('evaluation_to_training',array('evaluation_id'))->where('training_id = '.$id);
    	       $row = $evalTable->fetchRow($select);
    	       if ( $row ) $this->view->assign('evaluation_id',$row->evaluation_id);

		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$status = ValidationContainer::instance ();
			$evaluation_id = $this->getSanParam ( 'evaluation_id' );
			$status->setStatusMessage ( t ( 'The evaluation has been assigned.' ) );
			$eval_id = $this->getSanParam ( 'evaluation_id' );
			require_once('models/table/MultiOptionList.php');
			MultiOptionList::updateOptions ( 'evaluation_to_training', 'evaluation', 'training_id', $id, 'evaluation_id', array($eval_id=>$eval_id) );
		      $status->setRedirect ( '/training/edit/id/'.$id );

			$this->sendData ( $status );


		}
	}
}
