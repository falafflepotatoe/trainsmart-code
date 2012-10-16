<?php
/*
 * Created on Feb 14, 2008
 * 
 *  Built for web  
 *  Fuse IQ -- todd@fuseiq.com
 *  
 */

require_once ('ITechTable.php');

class Facility extends ITechTable {
	protected $_name = 'facility';
	protected $_primary = 'id';
	
	/**
	 * Returns false if the name already exists
	 */
	public static function isUnique($facilityName, $id = false) {
		$fac = new Facility ( );
		$select = $fac->select ();
		$select->where ( "facility_name = ?", $facilityName );
		if ( $id )
		  $select->where ( "id != ?", $id);
		if ($fac->fetchRow ( $select ))
			return false;
		
		return true;
	
	}
	
	public static function isReferenced($id) {
		require_once ('Person.php');
		
		$person = new Person ( );
		$select = $person->select ();
		$select->where ( "facility_id = ?", $id );
		$select->where ( "is_deleted = 0" );
		if ($person->fetchRow ( $select ))
			return true;
		
		return false;
	}
	
	 /**
   * Selects all facilities along with province and district
   *
   * @param unknown_type $num_tiers
   * @return unknown
   */
  public static function selectAllFacilities($num_tiers = 4) {
    $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
    
    list($field_name,$location_sub_query) = Location::subquery($num_tiers, false, false);
           
      $sql = 'SELECT facility.id ,
                facility.facility_name,
                facility.location_id,
                 '.implode(',',$field_name).'
              FROM facility JOIN ('.$location_sub_query.') as l ON facility.location_id = l.id 
              WHERE facility.is_deleted = 0 ORDER BY province_name'.($num_tiers > 2?', district_name':'').', facility_name';
    
    return $db->fetchAll($sql);  
  }
 
	/**
	public static function suggestionList($match = false, $limit = 100) {
		$rows = self::suggestionQuery ( $match, $limit );
		$rowArray = $rows->toArray ();
		
		return $rowArray;
	}
	*/
	/*
	protected static function suggestionQuery($match = false, $limit = 100) {
		require_once ('models/table/OptionList.php');
		$facilityTable = new OptionList ( array ('name' => 'facility' ) );
		
		$select = $facilityTable->select ()->from ( 'facility', array ('facility_name', 'd.district_name', 'p.province_name', 'id', 'district_id', 'province_id' ) )->setIntegrityCheck ( false );
		$select->joinLeft ( array ('d' => 'location_district' ), 'facility.district_id = d.id', 'd.district_name' );
		$select->joinLeft ( array ('p' => 'location_province' ), 'facility.province_id = p.id', 'p.province_name' );
		
		//look for char start
		if ($match) {
			$select->where ( 'facility_name LIKE ? ', $match . '%' );
		}
		$select->where ( 'facility.is_deleted = 0' );
		$select->order ( 'facility_name ASC' );
		
		if ($limit)
			$select->limit ( $limit, 0 );
		
		$rows = $facilityTable->fetchAll ( $select );
		
		return $rows;
	}
*/

}
 