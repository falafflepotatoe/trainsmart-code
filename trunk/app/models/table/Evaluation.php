<?php
/*
 * Created on Dec 14, 2009
 *
 *  Built for web
 *  Fuse IQ -- todd@fuseiq.com
 *
 */
require_once('ITechTable.php');

class Evaluation extends ITechTable
{
  protected $_primary = 'id';
  protected $_name = 'evaluation';

  public static function insertQuestions($parent_id, $text_array, $type_array) {
      $q_table = new ITechTable(array('name'=>'evaluation_question'));
   	  foreach($text_array as $i => $q) {
  	  	if ( !empty($text_array[$i]) && !empty($type_array[$i])) {
	   	  	$q_row = $q_table->createRow();
	   	  	$q_row->evaluation_id = $parent_id;
	   	  	$q_row->question_text = $text_array[$i];
	   	  	$q_row->question_type = $type_array[$i];
	   	  	$q_row->weight = $i;
	  	  	$q_row->save();
  	  	}
  	  }
  }

  public static function fetchAllQuestions($parent_id) {
  	    $question_table = new ITechTable ( array ('name' => 'evaluation_question' ) );
        return $question_table->fetchAll('evaluation_id = '.$parent_id);

  }

  public static function fetchAssignments($evaluation_id) {
      $q_table = new ITechTable(array('name'=>'evaluation_to_training'));
      $select = $q_table->select()
                  ->from($q_table->_name)
                  ->setIntegrityCheck(false)
                  ->where("evaluation_id = $evaluation_id");

      $rows = $q_table->fetchAll($select);
      $rtn = array();
      //just return an array of training ids
      foreach($rows as $r) {
        $rtn []= $r->training_id;
      }

      return $rtn;

  }
  
  /**
   * Return evaluation_id and training_id from assignment row
   *
   * @param unknown_type $evaluation_to_training_id
   */
  public static function fetchAssignment($evaluation_to_training_id) {
      $q_table = new ITechTable(array('name'=>'evaluation_to_training'));
      $select = $q_table->select()
                  ->from($q_table->_name)
                  ->setIntegrityCheck(false)
                  ->where("id = $evaluation_to_training_id");

      $rows = $q_table->fetchAll($select);

      //just return an array of training ids
      $r = $rows->current();
      if ($r)
        return array($r->evaluation_id, $r->training_id);
  
      return array(null,null);
  	
  }
  
  
/*
  public static function addEvaluationToTraining($evaluation_id, $training_id) {
      $q_table = new ITechTable(array('name'=>'evaluation_to_training'));
      $select = $q_table->select()
                  ->from($q_table->_name, array('doesExist' => 'COUNT(*)'))
                  ->setIntegrityCheck(false)
                  ->where("evaluation_id = $evaluation_id AND training_id = $training_id");

      $row = $q_table->fetchRow($select);

      if($row->doesExist) {
        return -1;
      } else {

        $data['evaluation_id'] = $evaluation_id;
        $data['training_id'] = $training_id;

        try {
          $q_table->delete('training_id = '.$training_id, true);

          return $q_table->insert($data);
        } catch(Zend_Exception $e) {
          error_log($e);
        }
      }

  }
  */
}
