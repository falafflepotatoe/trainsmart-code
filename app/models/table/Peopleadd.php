<?php 
require_once('ITechTable.php');

class Peopleadd extends ITechTable
{
	protected $_primary = 'id';
	protected $_name = 'student';
	protected $_address = 'addresses';
	protected $_tutor = 'tutor';
	protected $_gender = 'lookup_gender';
	protected $_title = 'person_title_option';
	protected $_person = 'person';
	protected $_facility = 'facility';
	protected $_city = 'location_city';
	
	public function Peopletitle(){ 
	 
          $select = $this->dbfunc()->select()
		 ->from($this->_title);
			 
      //echo $select->__toString();
	$result = $this->dbfunc()->fetchAll($select);
	return $result;
	} 
	
	public function PeopleFacility(){ 
	 
          $select = $this->dbfunc()->select()
		 ->from($this->_facility);
			 
      //echo $select->__toString();
	$result = $this->dbfunc()->fetchAll($select);
	return $result;
	} 
	
	public function PeopleCity(){
		
		$select = $this->dbfunc()->select()
		->from($this->_city);
		
		$result = $this->dbfunc()->fetchall($select);
		return $result;
	}

	public function addTutor($param){
		$insert = array(
			"title_option_id"	=>	$param['title'],
			"facility_id"		=>	$param['facility'],
			"first_name"		=>	$param['firstname'],
			"middle_name"		=>	$param['middlename'],
			"last_name"			=>	$param['lastname'],
			"gender"			=>	$param['gender'],
			"birthdate"			=>	trim($param['dob']) != "" ? date("Y-m-d", strtotime($param['dob'])) : "0000-00-00",
			"home_address_1"	=>	$param['address1'],
			"home_address_2"	=>	$param['address2'],
			"home_postal_code"	=>	$param['zip'],
			"home_location_id"	=>	$param['city'],
		);

		$db = $this->dbfunc();
#echo "<pre>";
#print_r ($_POST);
#echo "\n\n";
#print_r($insert);
#echo "</pre>";
##die();			
		$tutor = $db->insert($this->_person,$insert);
#print_r ($tutor);
#die('ok...');
		$id = $db->lastInsertId();
		
		# ADDING TUTOR RECORD
		$tutor = array(
			"personid"			=>	$id,
		);
		$rowArray = $db->insert($this->_tutor, $tutor);
		$tutorid = $db->lastInsertId();

		# ADDING PERM ADDRESS
		$address = array(
			'address1'			=>	$param['address1'],
			'address2'			=>	$param['address2'],
			'postalcode'		=>	$param['zip'],
			"id_geog1"			=>	(isset ($param['province_id']) ? $param['province_id'] : 0),
			"id_geog2"			=>	(isset ($param['district_id']) ? $param['district_id'] : 0),
			"id_geog3"			=>	(isset ($param['region_c_id']) ? $param['region_c_id'] : 0),
			'locationid'		=>	$param['city'],
			'id_addresstype'	=>	1,
		);
		$result = $db->insert("addresses", $address);
		$addressid = $db->lastInsertId();

		# LINKING ADDRESS TO TUTOR
		$link = array(
			"id_tutor"		=>	$tutorid,
			"id_address"	=>	$addressid,
		);
		$rowArray = $db->insert("link_tutor_addresses", $link);
		

		return $id; 
	}

	public function peopleadd($param) {
		if (isset ($_POST['addpeople'])){
#print_r ($_POST);
#exit;
			if ($param['type'] == "student"){
				$db = $this->dbfunc();

				$insert=array(
					'title_option_id'	=>	$param['title'],
					'facility_id'		=>	$param['facility'],
					'first_name'		=>	$param['firstname'],
					'middle_name'		=>	$param['middlename'],
					'last_name'			=>	$param['lastname'],
					'gender'			=>	$param['gender'],
					'birthdate'			=>	trim($param['dob']) != "" ? date("Y-m-d", strtotime($param['dob'])) : "",
					'home_address_1'	=>	$param['address1'],
					'home_address_2'	=>	$param['address2'],
					'home_postal_code'	=>	$param['zip'],
					'home_location_id'	=>	$param['city'],
					'facility_id'		=>	$param['facility'],
				);
				
#echo "<pre>";
#print_r ($_POST);
#echo "\n\n";
#print_r($insert);
#echo "</pre>";
##die();			
				$rowArray = $db->insert($this->_person,$insert);
#print_r ($rowArray);
#die('ok...');
				$id = $db->lastInsertId("person");
#				echo "last insert id = " . $id . "<br>";
			
				$student=array(
					"personid"			=>	$id,
					"geog1"				=>	(isset ($param['province_id']) ? $param['province_id'] : 0),
					"geog2"				=>	(isset ($param['district_id']) ? $param['district_id'] : 0),
					"geog3"				=>	(isset ($param['region_c_id']) ? $param['region_c_id'] : 0),
				);
				$rowArray = $db->insert("student", $student);
			} elseif ($param['type'] == "tutorNORUN") {
			
				$insert=array(
					"title_option_id"	=>	$param['title'],
					"facility_id"		=>	$param['facility'],
					"first_name"		=>	$param['firstname'],
					"middle_name"		=>	$param['middlename'],
					"last_name"			=>	$param['lastname'],
					"gender"			=>	$param['gender'],
					"birthdate"			=>	trim($param['dob']) != "" ? date("Y-m-d", strtotime($param['dob'])) : "",
					"home_address_1"	=>	$param['address1'],
					"home_address_2"	=>	$param['address2'],
					"home_postal_code"	=>	$param['zip'],
					"home_location_id"	=>	$param['city'],
				);
		
				$db = $this->dbfunc();
echo "<pre>";
print_r ($_POST);
echo "\n\n";
print_r($insert);
echo "</pre>";
die();			
				$tutor = $db->insert($this->_person,$insert);
#print_r ($rowArray);
#die('ok...');
				$id = $db->lastInsertId();
				
				$tutor = array(
					"personid"			=>	$id,
				);
		
				$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
				$rowArray = $db->insert($this->_tutor	,$tutor);
				
		
			}
			return $id; 
		}
	}
	 
}
  
?>