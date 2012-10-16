<?php


class System extends Zend_Db_Table_Abstract
{
    protected $_name = '_system';
    protected $_primary = 'country';

    public static function getSetting($field)
    {
      $tableObj = new System();

      $select = $tableObj->select()->from($tableObj->_name, array($field));
      try {
        $row = $tableObj->fetchRow();
        return $row->__get($field);
      } catch(Zend_Exception $e) {
        error_log($e);
      }


    }

    public static function getAll() {
      $tableObj = new System();

      $select = $tableObj->select()->from($tableObj->_name);
      try {
        $row = $tableObj->fetchRow();
        $rtn = array();
    		$info = $tableObj->info();
			foreach($info['cols'] as $col ) {
				$rtn[$col] = $row->$col;
			}

			//also get the logo file
			require_once('File.php');
			$fileObj = new File();
      $select = $fileObj->select()->where('parent_id = 0')->where("parent_table = '_system'");
      $rows = $fileObj->fetchAll($select);
      foreach($rows as $row) {
        $rtn['logo_id'] = $row->id;
      }
			
        return $rtn;
      } catch(Zend_Exception $e) {
        error_log($e);
      }
    }

    public function putSetting($field, $value) {
      if ( !$value ) //convert null to 0
      	$value = 0;
      $this->update(array($field => $value),'');
    }

}
