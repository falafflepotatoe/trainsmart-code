<?php 
require_once('ITechTable.php');

class Peoplefind extends ITechTable
{
	//protected $_primary = 'id';
	protected $_name = 'person';
	
	public function peoplesearch($param) {
		$return = array();
		switch ($param['type']){
			case "every":
				$query = $this->buildquery($param,"student");
				$select = $this->dbfunc()->query($query);
				$result = $select->fetchAll();
				foreach ($result as $row){
					$newrow = array();
					foreach ($row as $key=>$value){
						$newrow[$key] = $value;
					}
					$newrow['type'] = "student";
					$newrow['link'] = Settings::$COUNTRY_BASE_URL . "/studentedit/personview/id/" . $row['id'];
					$return[] = $newrow;
				}

				$query = $this->buildquery($param,"tutor");
				$select = $this->dbfunc()->query($query);
				$result = $select->fetchAll();
				foreach ($result as $row){
					$newrow = array();
					foreach ($row as $key=>$value){
						$newrow[$key] = $value;
					}
					$newrow['type'] = "tutor";
					$newrow['link'] = Settings::$COUNTRY_BASE_URL . "/tutoredit/tutoredit/id/" . $row['id'];
					$return[] = $newrow;
				}

			break;
			case "student":
				$query = $this->buildquery($param,"student");
				$select = $this->dbfunc()->query($query);
				$result = $select->fetchAll();
				foreach ($result as $row){
					$newrow = array();
					foreach ($row as $key=>$value){
						$newrow[$key] = $value;
					}
					$newrow['type'] = "student";
					$newrow['link'] = Settings::$COUNTRY_BASE_URL . "/studentedit/personview/id/" . $row['id'];
					$return[] = $newrow;
				}
			break;
			case "tutor":
				$query = $this->buildquery($param,"tutor");
				$select = $this->dbfunc()->query($query);
				$result = $select->fetchAll();
				foreach ($result as $row){
					$newrow = array();
					foreach ($row as $key=>$value){
						$newrow[$key] = $value;
					}
					$newrow['type'] = "tutor";
					$newrow['link'] = Settings::$COUNTRY_BASE_URL . "/tutoredit/tutoredit/id/" . $row['id'];
					$return[] = $newrow;
				}
			break;
		}
		return $return;
	}

	public function buildquery($param,$output){
		$where = array();
		$joins = array();
		$joinstudent = false;
		$jointutor = false;

		switch($output){
			case"student":
				$joinstudent = true;
			break;
			case"tutor":
				$jointutor = true;
			break;
		}	 
		
		foreach ($param as $key=>$value){
			if (trim ($value) != ""){
				switch ($key){
					case "firstname":
						$where[] = "p.first_name LIKE '%" . addslashes($value) . "%'";
					break;
					case "lastname":
						$where[] = "p.last_name LIKE '%" . Addslashes($value) . "%'";
					break;
					case "cohort":
						if ($value != 0){
							if ($output == "student"){
								$where[] = "co.id = " . addslashes($value) . "";
								$joinstudent = true;
								$joins[] = "INNER JOIN link_student_cohort lsco ON lsco.id_student = s.id";
								$joins[] = "INNER JOIN cohort co ON co.id = lsco.id_cohort";
							} elseif ($output == "tutor"){
								$where[] = "co.id = " . addslashes($value) . "";
								$jointutor = true;
								$joins[] = "INNER JOIN link_tutor_institution lti ON lti.id_tutor = t.id";
								$joins[] = "INNER JOIN cohort co ON co.institutionid = lti.id_institution";
							}
						}
					break;
					case "cadre":
						if ($value != 0){
							if ($output == "student"){
								$where[] = "ca.id = " . addslashes($value) . "";
								$joinstudent = true;
								$joins[] = "INNER JOIN cadres ca ON ca.id = s.cadre";
							} elseif ($output == "tutor"){
								$where[] = "c.id = " . addslashes($value) . "";
								$jointutor = true;
								$joins[] = "INNER JOIN link_cadre_tutor lct ON lct.id_tutor = t.id";
								$joins[] = "INNER JOIN cadres c ON c.id = lct.id_cadre";
							}
						}
					break;
					case "inst":
						if ($value != 0){
							if ($output == "student"){
								$where[] = "i.id = " . addslashes($value) . "";
								$joinstudent = true;
								$joins[] = "INNER JOIN link_student_institution lsi ON lsi.id_student = s.id";
								$joins[] = "INNER JOIN institution i ON i.id = lsi.id_institution";
							} elseif ($output == "tutor"){
								$where[] = "i.institutionname LIKE '%" . addslashes($value) . "%'";
								$jointutor = true;
								$joins[] = "INNER JOIN link_tutor_institution lti ON lti.id_tutor = t.id";
								$joins[] = "INNER JOIN institution i ON i.id = lti.id_institution";
							}
						}
					break;
					case "fact":
						if ($value != 0){
							if ($output == "student"){
								$where[] = "f.id = " . addslashes($value) . "";
								$joinstudent = true;
								$joins[] = "INNER JOIN link_student_facility lsf ON lsf.id_student = s.id";
								$joins[] = "INNER JOIN facility f ON f.id = lsf.id_facility";
							}
						}
					break;
				}
			}
		}

		$query = "SELECT p.* FROM person p ";
		if ($joinstudent){
			$query .= " INNER JOIN student s ON s.personid = p.id ";
		} elseif ($jointutor){
			$query .= " INNER JOIN tutor t ON t.personid = p.id ";
		}
		if (count ($joins) > 0){
			$query .= implode ("\n", $joins);
		}
		if (count ($where) > 0){
			$query .= " WHERE " . implode (" AND ", $where);
		}
		$query .= " ORDER BY last_name, first_name";

echo $query . "<BR><BR>";

		return ($query);
	}

}

	
 
?>