<?php 
require_once('ITechTable.php');

class Helper extends ITechTable
{
	#################################
	#                               #
	#   COHORT SPECIFIC FUNCTIONS   #
	#                               #
	#################################

	public function getCohorts($all = false){
		if ($all){
			// RETURNS A LIST OF ALL COHORST ORDERED BY NAME
			$select = $this->dbfunc()->select()
				->from("cohort")
				->order('cohortname');
		} else {
			$ins = $this->getInstitutions(false);
			$_ins = array();
			foreach ($ins as $i){
				$_ins[] = $i['id'];
			}
			if (count ($_ins) == 0){
				$_ins = array('0');
			}
			$ins = $_ins;
	
			// RETURNS A LIST OF ALL COHORST ORDERED BY NAME
			$select = $this->dbfunc()->select()
				->from("cohort")
				->where('institutionid IN (' . implode(",", $ins) . ')')
				->order('cohortname');
		}
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getCohortStudents($cid, $tp = "all",$output = "result") {
		switch ($tp){
			case "all":
				$select = $this->dbfunc()->select()
					->from("link_student_cohort")
					->join(array("c" => "cohort"),
							"id_cohort = c.id")
					->where('id_cohort = ?', $cid);
			break;
			case "graduating":
				$select = $this->dbfunc()->select()
					->from("link_student_cohort")
					->join(array("c" => "cohort"),
							"id_cohort = c.id")
					->where("dropdate = '0000-00-00' OR dropdate = c.graddate")
					->where('id_cohort = ?', $cid);
			break;
			case "dropped":
				$select = $this->dbfunc()->select()
					->from("link_student_cohort")
					->join(array("c" => "cohort"),
							"id_cohort = c.id")
					->where("dropdate <> '0000-00-00'")
					->where("dropdate < c.graddate")
					->where('id_cohort = ?', $cid);
			break;
		}
		#die($select->__toString());
		$result = $this->dbfunc()->fetchAll($select);
		if ($output == "count"){
			return count($result);	
		} else {
			return $result;	
		}
	}

	######################################
	#                                    #
	#   INSTITUTION SPECIFIC FUNCTIONS   #
	#                                    #
	######################################

	public function getInstitutionStudents($sid, $tp = "all",$output = "result") {
		switch ($tp){
			case "all":
				$select = $this->dbfunc()->select()
					->from("link_student_cohort")
					->join(array("c" => "cohort"),
							"id_cohort = c.id")
					->where('institutionid = ?', $sid);
			break;
			case "graduating":
				$select = $this->dbfunc()->select()
					->from("link_student_cohort")
					->join(array("c" => "cohort"),
							"id_cohort = c.id")
					->where("dropdate = '0000-00-00' OR dropdate = c.graddate")
					->where('institutionid = ?', $sid);
			break;
			case "dropped":
				$select = $this->dbfunc()->select()
					->from("link_student_cohort")
					->join(array("c" => "cohort"),
							"id_cohort = c.id")
					->where("dropdate <> '0000-00-00'")
					->where("dropdate < c.graddate")
					->where('institutionid = ?', $sid);
			break;
		}
		#die($select->__toString());
		$result = $this->dbfunc()->fetchAll($select);
		if ($output == "count"){
			return count($result);	
		} else {
			return $result;	
		}
	}

	public function getUserInstitutions($uid,$full = true) {
		if ($full){
			$select = $this->dbfunc()->select()
				->from("link_user_institution")
				->where('userid = ?', $uid);
			$result = $this->dbfunc()->fetchAll($select);
		} else {
			$select = $this->dbfunc()->select()
				->from("link_user_institution")
				->where('userid = ?', $uid);
			$result = $this->dbfunc()->fetchAll($select);
			$trimmed = array();
			foreach ($result as $row){
				$trimmed[] = $row['institutionid'];
			}
			$result = $trimmed;
		}
		return $result;	
	}

	public function getInstitutions($all = true) {
		if (!$all){
			$institutions = $this->getUserInstitutions($this->myid(),false);
			if ((is_array($institutions)) && (count($institutions) > 0)){
				$insids = implode(",", $institutions);
				$select = $this->dbfunc()->select()
					->from("institution")
					->where("id IN (" . $insids . ")")
					->order('institutionname');
			} else {
				$select = $this->dbfunc()->select()
					->from("institution")
					->order('institutionname');
			}
		} else {
			$select = $this->dbfunc()->select()
				->from("institution")
				->order('institutionname');
		}
		

		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getInstitutionTypes() {
		$select = $this->dbfunc()->select()
			->from("lookup_institutiontype")
			->where('status = 1')
			->order('typename');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}
	
	public function getInstitutionsSelectedTypes($iid){
		$select = $this->dbfunc()->select()
			->from("link_institution_institutiontype",
					array("id_institutiontype"))
			->where('id_institution = ?',$iid);
#		die ("<br><br>" . $select->__toString());
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}
	
	public function getInstitutionTutorCount($iid) {
		$select = $this->dbfunc()->select()
			->from("link_tutor_institution")
			->where('id_institution = ?',$iid);
		$result = $this->dbfunc()->fetchAll($select);
		return count($result);	
	}
	
	######################################
	#                                    #
	#   NATIONALITY SPECIFIC FUNCTIONS   #
	#                                    #
	######################################

	public function getNationalities() {
		$select = $this->dbfunc()->select()
			->from("lookup_nationalities")
			->order('nationality');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	###################################
	#                                 #
	#   LANGUAGE SPECIFIC FUNCTIONS   #
	#                                 #
	###################################

	public function getLanguages() {
		$select = $this->dbfunc()->select()
			->from("lookup_languages")
			->order('language');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}
	
	public function getTutorLanguages($tid){
		$select = $this->dbfunc()->select()
			->from("link_tutor_languages")
			->where("id_tutor = ?", $tid);
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	###################################
	#                                 #
	#   FACILITY SPECIFIC FUNCTIONS   #
	#                                 #
	###################################

/*
	public function getFacilityTypes() {
		$select = $this->dbfunc()->select()
			->from("lookup_facilitytype")
			->order('facilitytypename');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}
*/
	public function getFacilityTypes() {
		$select = $this->dbfunc()->select()
			->from("facility_type_option",array('id','facilitytypename' => 'facility_type_phrase'))
			->order('facility_type_phrase');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getFacilities() {
		$select = $this->dbfunc()->select()
			->from("facility")
			->order('facility_name');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	#################################
	#                               #
	#   DEGREE SPECIFIC FUNCTIONS   #
	#                               #
	#################################

	public function getDegrees() {
		$select = $this->dbfunc()->select()
			->from("lookup_degrees")
			->order('degree');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getDegreeTypes() {
		$select = $this->dbfunc()->select()
			->from("lookup_degreetypes")
			->order('title');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function setDegree($degree) {
		$insert = array(
			'degree' => $degree,
		);
		$rowArray = $this->dbfunc()->insert("lookup_degrees", $insert);
		$id = $this->dbfunc()->lastInsertId();
		return ($id);
	}
	
	public function getInstitutionDegrees($iid){
		$select = $this->dbfunc()->select()
			->from("link_institution_degrees",
				array("id" => "id_degree"))
			->where("id_institution = ?",$iid);
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	#################################
	#                               #
	#   REASON SPECIFIC FUNCTIONS   #
	#                               #
	#################################

	public function getReasons($tp) {
		$select = $this->dbfunc()->select()
			->from("lookup_reasons")
			->where("reasontype = ?",$tp)
			->order('reason');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function setReasons($rsn,$tp) {
		$insert = array(
			'reason' => $rsn,
			'reasontype' => $tp,
		);
		$rowArray = $this->dbfunc()->insert("lookup_reasons", $insert);
		$id = $this->dbfunc()->lastInsertId();
		return ($id);
	}

	################################
	#                              #
	#   CADRE SPECIFIC FUNCTIONS   #
	#                              #
	################################

	public function getCadres(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("cadres")
			->where('status = 1')
			->order('cadrename');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getTutorCadres($tid){
		// RETURNS A LIST OF ALL CADRES TIED TO TUTOR WITH ID $tid ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from(array ("c" => "cadres"),
					array ("id","cadrename"))
			->join(array("l" => "link_cadre_tutor"),
					"l.id_cadre = c.id")
			->where('c.status = 1')
			->where('l.id_tutor = ?',$tid)
			->order('cadrename');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}
	
	public function getInstitutionCadres($iid){
		if ($iid){
			// RETURNS A LIST OF ALL CADRES TIED TO INSTITUTION WITH ID $iid ORDERED BY CADRE NAME
			$select = $this->dbfunc()->select()
				->from(array ("c" => "cadres"),
						array ("id","cadrename"))
				->join(array("l" => "link_cadre_institution"),
						"l.id_cadre = c.id")
				->where('c.status = 1')
				->where('l.id_institution = ?',$iid)
				->order('cadrename');
			$result = $this->dbfunc()->fetchAll($select);
			return $result;	
		} else {
			# RETURNS A MULTI DIMENSION ARRAY OF ALL INSTITUTIONS AND THE CADRES TIED TO THEM
			$ins = $this->getInstitutions();
			$output = array();
			foreach ($ins as $i){
				$select = $this->dbfunc()->select()
					->from(array ("c" => "cadres"),
							array ("id","cadrename"))
					->join(array("l" => "link_cadre_institution"),
							"l.id_cadre = c.id")
					->where('c.status = 1')
					->where('l.id_institution = ?',$i['id'])
					->order('cadrename');
				$result = $this->dbfunc()->fetchAll($select);
				$output[] = array(
					"institution" => $i,
					"cadres" => $result
				);
			}
			return $output;
		}
	}


	#######################################
	#                                     #
	#   STUDENT TYPE SPECIFIC FUNCTIONS   #
	#                                     #
	#######################################

	public function getStudentTypes(){
		// RETURNS A LIST OF ALL STUDENT TYPES ORDERED BY NAME
		$select = $this->dbfunc()->select()
			->from("lookup_studenttype")
			->where('status = 1')
			->order('studenttype');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	#####################################
	#                                   #
	#   TUTOR TYPE SPECIFIC FUNCTIONS   #
	#                                   #
	#####################################

	public function getTutors(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from(array ("t" => "tutor"),
					array ("id"))
			->join(array("p" => "person"),
					"t.personid = p.id",
					array("first_name","last_name"))
			->order(array('first_name','last_name'));
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getTutorTypes(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_tutortype")
			->where('status = 1')
			->order('typename');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getLinkedTutorTypes($tid){
		// RETURNS A LIST OF ALL TUTOR TYPES TIED TO TUTOR WITH ID $iid ORDERED BY TUTOR TYPE NAME
		$select = $this->dbfunc()->select()
			->from(array ("t" => "lookup_tutortype"),
					array ("id","typename"))
			->join(array("l" => "link_tutor_tutortype"),
					"l.id_tutortype = t.id",
					array())
			->where('t.status = 1')
			->where('l.id_tutor = ?',$tid)
			->order('typename');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getAllTutors(){
		// RETURNS A LIST OF ALL TUTORS ORDERED BY TUTOR NAME
		$select = $this->dbfunc()->select()
			->from(array ("p" => "person"),
					array ("first_name","middle_name","last_name"))
			->join(array("t" => "tutor"),
					"t.personid = p.id",
					array("id"))
			->order('first_name','last_name');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getTutorStudents($tid){
		// RETURNS A LIST OF ALL TUTOR TYPES TIED TO TUTOR WITH ID $iid ORDERED BY TUTOR TYPE NAME
		$select = $this->dbfunc()->select()
			->from(array ("s" => "student"),
					array("id"))
			->join(array ("t" => "tutor"),
					"s.advisorid = t.id",
					array())
			->join(array ("p2" => "person"),
					"s.personid = p2.id",
					array("first_name","last_name"))
			->where('s.isgraduated = 0')
			->where('t.personid = ?',$tid)
			->order('first_name');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}
	
	public function getTutorClasses($tid){
		$query = "SELECT * FROM classes 
				  LEFT JOIN link_cohorts_classes ON link_cohorts_classes.classid = classes.id
				  LEFT JOIN cohort ON cohort.id = link_cohorts_classes.cohortid
				  WHERE instructorid = (SELECT id FROM tutor WHERE personid = {$tid})";
		$select = $this->dbfunc()->query($query);
		$result = $select->fetchAll();
		return $result;
	}

	##################################
	#                                #
	#   SPONSOR SPECIFIC FUNCTIONS   #
	#                                #
	##################################

	public function getSponsors(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_sponsors")
			->where('status = 1')
			->order('sponsorname');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function getOldSponsors(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("facility_sponsor_option",array('id','sponsorname' => 'facility_sponsor_phrase'))
			->where('is_deleted = 0')
			->order('facility_sponsor_phrase');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}
	
	##################################
	#                                #
	#   FUNDING SPECIFIC FUNCTIONS   #
	#                                #
	##################################

	public function getFunding(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_fundingsources")
			->where('status = 1')
			->order('fundingname');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}
	
	################################
	#                              #
	#   ADMIN SPECIFIC FUNCTIONS   #
	#                              #
	################################
	
	public function AdminLabels($labels){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("translation")
			->where("key_phrase IN ('" . implode("','",$labels) . "')")
			->order('key_phrase');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminClasses(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("classes")
			->order('classname');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminCadres(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("cadres")
			->order('cadrename');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminDegrees(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_degrees")
			->order('degree');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminFunding(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_fundingsources")
			->order('fundingname');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminInstitutionTypes(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_institutiontype")
			->order('typename');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminLanguages(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_languages")
			->order('language');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminNationalities(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_nationalities")
			->order('nationality');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminJoinDropReasons(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_reasons")
			->order('reason');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminSponsors(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_sponsors")
			->where("status = 1")
			->order('sponsorname');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminStudenttypes(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_studenttype")
			->where("status = 1")
			->order('studenttype');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminTutortypes(){
		// RETURNS A LIST OF ALL ACTIVE CADRES ORDERED BY CADRE NAME
		$select = $this->dbfunc()->select()
			->from("lookup_tutortype")
			->where("status = 1")
			->order('typename');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}
	
	public function AdminCourseTypes(){
		// RETURNS A LIST OF ALL COURSE TYPES ORDERED BY NAME
		$select = $this->dbfunc()->select()
			->from("lookup_coursetype")
			->order('coursetype');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	public function AdminReligiousDenominations(){
		// RETURNS A LIST OF ALL DENOMINATION TYPES ORDERED BY NAME
		$select = $this->dbfunc()->select()
			->from("lookup_studenttype")
			->order('studenttype');
		$result = $this->dbfunc()->fetchAll($select);
		return $result;	
	}

	
	############################################################################
	
	public function setExternalValues($linktable,$maincolumn,$linkcolumn,$originalvar,$id){
		$implodedvar = implode(",", $originalvar);

		#	$linktable		= PIVOT TABLE TO BE USED
		#	$maincolumn		= LINK TO 'MAIN' OBJECT - SHOULD BE SAME FOR ALL ENTRIES HERE
		#	$linkcolumn		= LINK TO 'EXTERNAL' OBJECT - SHOULD BE DIFFERENT FOR EVERY ENTRY
		#	$originalvar	= ORIGINAL POST VARIABLE, OR FALSE IF NOT SET
		#	$id				= ID OF 'MAIN' OBJECT

		if ((!$originalvar) || (!is_array ($originalvar)) || (count ($originalvar) == 0)){
			# REMOVING ALL INSTITUTION TYPE LINKS
			$query = "DELETE FROM " . $linktable . " WHERE " . $maincolumn . " = " . $id;
			$this->dbfunc()->query($query);
		} else {
			$languagesspoken = implode(",", $param['languagesspoken']);

			# REMOVING OLD LINKS NO LONGER SELECTED
			$query = "DELETE FROM " . $linktable . " WHERE " . $maincolumn . " = " . $id . " AND " . $linkcolumn . " NOT IN (" . $implodedvar . ")";
			$this->dbfunc()->query($query);

			# ADDING NEW LINKS THAT WERE ADDED
			foreach ($originalvar as $key=>$val){
				$select = $this->dbfunc()->select()
					->from($linktable)
					->where($maincolumn . ' = ?', $id)
					->where($linkcolumn . ' = ?', $val);
				$result = $this->dbfunc()->fetchAll($select);
				if (count ($result) == 0){
					# LINK NOT FOUND - ADDING
					$i_arr = array(
						$linkcolumn => $val,
						$maincolumn	=> $id
					);
					$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
				}
			}
		}
	}

	########################
	#                      #
	#   UPDATE FUNCTIONS   #
	#                      #
	########################

	public function updateClasses($params){
		$linktable = "classes";
		$maincolumn = "classname";
		$col2 = "startdate";
		$col3 = "enddate";
		$col4 = "instructorid";
		$col5 = "coursetypeid";
		$col6 = "coursetopic";
		$id = $_POST["_id"];
		$value = $_POST['_classname'];
		$value2 = date("Y-m-d", strtotime($_POST['_startdate']));
		$value3 = date("Y-m-d", strtotime($_POST['_enddate']));
		$value4 = $_POST['_instructorid'];
		$value5 = $_POST['_coursetypeid'];
		$value6 = $_POST['_coursetopic'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);


		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value,
				$col2		=> $value2,
				$col3		=> $value3,
				$col4		=> $value4,
				$col5		=> $value5,
				$col6		=> $value6
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateCadres($params){
		$linktable = "cadres";
		$maincolumn = "cadrename";
		$id = $_POST["_id"];
		$value = $_POST['_cadrename'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);


		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value,
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateCoursetypes($params){
		$linktable = "lookup_coursetype";
		$maincolumn = "coursetype";
		$id = $_POST["_id"];
		$value = $_POST['_coursetype'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateDegrees($params){
		$linktable = "lookup_degrees";
		$maincolumn = "degree";
		$id = $_POST["_id"];
		$value = $_POST['_degree'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateReligion($params){
		$linktable = "lookup_studenttype";
		$maincolumn = "studenttype";
		$id = $_POST["_id"];
		$value = $_POST['_denom'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateFunding($params){
		$linktable = "lookup_fundingsources";
		$maincolumn = "fundingname";
		$id = $_POST["_id"];
		$value = $_POST['_fundingname'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateInstitutiontypes($params){
		$linktable = "lookup_institutiontype";
		$maincolumn = "typename";
		$id = $_POST["_id"];
		$value = $_POST['_typename'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateLanguages($params){
		$linktable = "lookup_languages";
		$maincolumn = "language";
		$secondcolumn = "abbreviation";
		$id = $_POST["_id"];
		$value = $_POST['_language'];
		$value2 = $_POST['_abbreviation'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value,
				$secondcolumn	=> $value2
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateNationalities($params){
		$linktable = "lookup_nationalities";
		$maincolumn = "nationality";
		$id = $_POST["_id"];
		$value = $_POST['_nationality'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateJoindropreasons($params){
		$linktable = "lookup_reasons";
		$maincolumn = "reason";
		$secondcolumn = "reasontype";
		$id = $_POST["_id"];
		$value = $_POST['_reason'];
		$value2 = $_POST['_reasontype'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value,
				$secondcolumn	=> $value2
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateSponsors($params){
		$linktable = "lookup_sponsors";
		$maincolumn = "sponsorname";
		$id = $_POST["_id"];
		$value = $_POST['_sponsor'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateStudenttypes($params){
		$linktable = "lookup_studenttype";
		$maincolumn = "studenttype";
		$id = $_POST["_id"];
		$value = $_POST['_studenttype'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	public function updateTutortypes($params){
		$linktable = "lookup_tutortype";
		$maincolumn = "typename";
		$id = $_POST["_id"];
		$value = $_POST['_tutortype'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('id <> ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}
	
	
	#####################
	#                   #
	#   ADD FUNCTIONS   #
	#                   #
	#####################

	public function addClasses($params){
		$linktable = "classes";
		$maincolumn = "classname";
		$col2 = "startdate";
		$col3 = "enddate";
		$col4 = "instructorid";
		$col5 = "coursetypeid";
		$col6 = "coursetopic";
		$id = $_POST["_id"];
		$value = $_POST['_classname'];
		$value2 = date("Y-m-d", strtotime($_POST['_startdate']));
		$value3 = date("Y-m-d", strtotime($_POST['_enddate']));
		$value4 = $_POST['_instructorid'];
		$value5 = $_POST['_coursetypeid'];
		$value6 = $_POST['_coursetopic'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));


		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value,
				$col2		=> $value2,
				$col3		=> $value3,
				$col4		=> $value4,
				$col5		=> $value5,
				$col6		=> $value6
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}

	public function addCadres($params){
		$linktable = "cadres";
		$maincolumn = "cadrename";
		$id = $_POST["_id"];
		$value = $_POST['_cadrename'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));


		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value,
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addDegrees($params){
		$linktable = "lookup_degrees";
		$maincolumn = "degree";
		$id = $_POST["_id"];
		$value = $_POST['_degree'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addCoursetypes($params){
		$linktable = "lookup_coursetype";
		$maincolumn = "coursetype";
		$id = $_POST["_id"];
		$value = $_POST['_coursetype'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addReligion($params){
		$linktable = "lookup_studenttype";
		$maincolumn = "studenttype";
		$id = $_POST["_id"];
		$value = $_POST['_denom'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addFunding($params){
		$linktable = "lookup_fundingsources";
		$maincolumn = "fundingname";
		$id = $_POST["_id"];
		$value = $_POST['_fundingname'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addInstitutiontypes($params){
		$linktable = "lookup_institutiontype";
		$maincolumn = "typename";
		$id = $_POST["_id"];
		$value = $_POST['_typename'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addLanguages($params){
		$linktable = "lookup_languages";
		$maincolumn = "language";
		$secondcolumn = "abbreviation";
		$id = $_POST["_id"];
		$value = $_POST['_language'];
		$value2 = $_POST['_abbreviation'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value,
				$secondcolumn	=> $value2
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addNationalities($params){
		$linktable = "lookup_nationalities";
		$maincolumn = "nationality";
		$id = $_POST["_id"];
		$value = $_POST['_nationality'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addJoindropreasons($params){
		$linktable = "lookup_reasons";
		$maincolumn = "reason";
		$secondcolumn = "reasontype";
		$id = $_POST["_id"];
		$value = $_POST['_reason'];
		$value2 = $_POST['_reasontype'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value,
				$secondcolumn	=> $value2
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addSponsors($params){
		$linktable = "lookup_sponsors";
		$maincolumn = "sponsorname";
		$id = $_POST["_id"];
		$value = $_POST['_sponsor'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addStudenttypes($params){
		$linktable = "lookup_studenttype";
		$maincolumn = "studenttype";
		$id = $_POST["_id"];
		$value = $_POST['_studenttype'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}
	
	public function addTutortypes($params){
		$linktable = "lookup_tutortype";
		$maincolumn = "typename";
		$id = $_POST["_id"];
		$value = $_POST['_tutortype'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}

	public function updateCohortLicense($cid,$param){
		if ($param['licenseid'] == 0){
			$insert = array(
				'licensename'	=> $param['licensename'],
				'licensedate'	=> $param['licensedate'],
				'cohortid'		=> $cid,
			);
			$this->dbfunc()->insert("licenses", $insert);
		} else {
			$insert = array(
				'licensename'	=> $param['licensename'],
				'licensedate'	=> $param['licensedate'],
			);
			$this->dbfunc()->update("licenses",$insert,'id = ' . $param['licenseid']);
		}		
	}

	public function updateCohortStudents($cid,$param){
		$db = $this->dbfunc();
		$ids = array();
		foreach ($param['students'] as $student){
			$id = $student;
			$ids[] = $id;
			
			$query = "SELECT * FROM link_student_cohort WHERE id_cohort = " . $cid . " AND id_student = " . $id;
			$select = $db->query($query);
			$result = $select->fetchAll();
			if (count ($result) == 0){
				# ADD
				$insert = array(
					'id_student'	=> $id,
					'id_cohort'		=> $cid,
					'joindate'		=> date("Y-m-d H:i:s"),
				);
				$this->dbfunc()->insert("link_student_cohort", $insert);
			}
			$this->updatePersonInstitution("student",$id,$cid);
			
			// update cadre in student record
			$cadre_sql = "UPDATE student SET cadre = (SELECT cadreid FROM cohort WHERE id = {$cid}) WHERE id = {$id}";
			$db->query($cadre_sql);
		}
		if (count ($ids) == 0){
			# DELETED ALL LINKS
			$query = "DELETE FROM link_student_cohort WHERE id_cohort = '" . $cid . "'";
			$db->query($query);
		} else {
			# REMOVE ANYTHING THAT'S NOT SELECTED
			$query = "DELETE FROM link_student_cohort WHERE id_cohort = " . $cid . " AND id_student NOT IN (" . implode(",", $ids) . ")";
			$select = $db->query($query);
		}
	}

	public function updateCohortClasses($cid,$param){
		$db = $this->dbfunc();
		$ids = array();
		foreach ($param['class'] as $class){
			$id = $class['id'];
			$ids[] = $id;
			
			$query = "SELECT * FROM link_cohorts_classes WHERE cohortid = " . $cid . " AND classid = " . $id;
			$select = $db->query($query);
			$result = $select->fetchAll();
			if (count ($result) == 0){
				# ADD
				$insert = array(
					'classid'	=> $id,
					'cohortid'	=> $cid,
				);
				$this->dbfunc()->insert("link_cohorts_classes", $insert);
			}
		}
		if (count ($ids) == 0){
			# DELETED ALL LINKS
			$query = "DELETE FROM link_cohorts_classes WHERE cohortid = '" . $cid . "'";
			$db->query($query);
		} else {
			# REMOVE ANYTHING THAT'S NOT SELECTED
			$query = "DELETE FROM link_cohorts_classes WHERE cohortid = " . $cid . " AND classid NOT IN (" . implode(",", $ids) . ")";
			$select = $db->query($query);
		}
	}

	public function updateCohortPracticums($cid,$param){
		if ($param['practicumid'] == 0){
			$insert = array(
				'practicumname'		=> $param['practicumname'],
				'practicumdate'		=> date("Y-m-d", strtotime($param['practicumdate'])),
				'facilityid'		=> $param['facilityid'],
				'advisorid'			=> $param['advisorid'],
				'hoursrequired'		=> is_numeric($param['hoursrequired']) ? $param['hoursrequired'] : "0.00",
				'cohortid'			=> $cid,
			);
			$this->dbfunc()->insert("practicum", $insert);
		} else {
			$insert = array(
				'practicumname'		=> $param['practicumname'],
				'practicumdate'		=> date("Y-m-d", strtotime($param['practicumdate'])),
				'facilityid'		=> $param['facilityid'],
				'advisorid'			=> $param['advisorid'],
				'hoursrequired'		=> is_numeric($param['hoursrequired']) ? $param['hoursrequired'] : "0.00",
			);
			$this->dbfunc()->update("practicum",$insert,'id = ' . $param['practicumid']);
		}		
	}

	public function ListCurrentClasses($cid,$pid = false) {
		# SHOW ALL CLASSES BASED ON COHORT ID AND POTENTIALLY CROSS WITH STUDENT ID
		$db = $this->dbfunc();

		if (!$pid){
			$query = "SELECT c.*, p.first_name, p.last_name, lct.coursetype
				FROM classes c
				INNER JOIN link_cohorts_classes lcc ON lcc.classid = c.id
				INNER JOIN tutor t ON t.id = c.instructorid
				INNER JOIN person p ON t.personid = p.id 
				LEFT JOIN lookup_coursetype lct ON lct.id = c.coursetypeid
				WHERE lcc.cohortid = '" . $cid . "'
				ORDER BY c.classname, p.first_name, p.last_name";
			$select = $db->query($query);
			$result = $select->fetchAll();
			return $result;
		} else {
			$query = "SELECT c.*, p.first_name, p.last_name, lct.coursetype
				FROM classes c
				INNER JOIN link_cohorts_classes lcc ON lcc.classid = c.id
				INNER JOIN tutor t ON t.id = c.instructorid
				INNER JOIN person p ON t.personid = p.id 
				LEFT JOIN lookup_coursetype lct ON lct.id = c.coursetypeid
				WHERE lcc.cohortid = '" . $cid . "'
				ORDER BY c.classname, p.first_name, p.last_name";
			$select = $db->query($query);
			$result = $select->fetchAll();
			
			$output = array();

			foreach ($result as $row){
				$newclass = $row;
				$query = "SELECT * FROM link_student_classes WHERE
					studentid = " . $pid . " AND
					classid = " . $row['id'] . " AND
					cohortid = " . $cid;
				$select = $db->query($query);
				$result = $select->fetchAll();
				if (count ($result) > 0){
					$row = $result[0];
					$newclass['linkid'] = $row['linkclasscohortid'];
					$newclass['grade'] = $row['grade'];
				} else {
					$newclass['linkid'] = 0;
					$newclass['grade'] = "N/A";
				}
					
				$output[] = $newclass;
			}

			return $output;
		}
	}

	public function ListCurrentPracticum($cid,$pid = false){
		# SHOW ALL CLASSES BASED ON COHORT ID AND POTENTIALLY CROSS WITH STUDENT ID
		$db = $this->dbfunc();

		if (!$pid){
			$query = "SELECT p.*, f.facility_name, pe.first_name, pe.last_name, pe.id AS personid 
				FROM practicum p
				INNER JOIN facility f ON f.id = p.facilityid
				INNER JOIN tutor t ON t.id = p.advisorid
				INNER JOIN person pe ON pe.id = t.personid
				WHERE cohortid = " . $cid . "
				ORDER BY practicumdate DESC, practicumname";
			$select = $db->query($query);
			$result = $select->fetchAll();
			return $result;
		} else {
			$query = "SELECT p.*, f.facility_name, pe.first_name, pe.last_name, pe.id AS personid 
				FROM practicum p
				INNER JOIN facility f ON f.id = p.facilityid
				INNER JOIN tutor t ON t.id = p.advisorid
				INNER JOIN person pe ON pe.id = t.personid
				WHERE cohortid = " . $cid . "
				ORDER BY practicumdate DESC, practicumname";
			$select = $db->query($query);
			$result = $select->fetchAll();
			
			$output = array();

			foreach ($result as $row){
				$newclass = $row;
				$query = "SELECT * FROM link_student_practicums WHERE
					studentid = " . $pid . " AND
					practicumid = " . $row['id'] . " AND
					cohortid = " . $cid;
				$select = $db->query($query);
				$result = $select->fetchAll();
				if (count ($result) > 0){
					$row = $result[0];
					$newclass['linkid'] = $row['linkcohortpracticumid'];
					$newclass['hourscompleted'] = $row['hourscompleted'];
					$newclass['grade'] = $row['grade'];
				} else {
					$newclass['linkid'] = $row['linkcohortpracticumid'];
					$newclass['hourscompleted'] = "0";
					$newclass['grade'] = "N/A";
				}
					
				$output[] = $newclass;
			}

			return $output;
		}
	}

	public function ListCurrentLicenses($cid,$pid = false){
		# SHOW ALL CLASSES BASED ON COHORT ID AND POTENTIALLY CROSS WITH STUDENT ID
		$db = $this->dbfunc();

		if (!$pid){
			$query = "SELECT id, licensename, licensedate
				FROM licenses
				WHERE cohortid = " . $cid . "
				ORDER BY licensedate, licensename";
			$select = $db->query($query);
			$result = $select->fetchAll();
			return $result;
		} else {
			$query = "SELECT id, licensename, licensedate
				FROM licenses
				WHERE cohortid = " . $cid . "
				ORDER BY licensedate, licensename";
			$select = $db->query($query);
			$result = $select->fetchAll();
			
			$output = array();

			foreach ($result as $row){
				$newclass = $row;
				$query = "SELECT * FROM link_student_licenses WHERE
					studentid = " . $pid . " AND
					licenseid = " . $row['id'] . " AND
					cohortid = " . $cid;
				$select = $db->query($query);
				$result = $select->fetchAll();
				if (count ($result) > 0){
					$row = $result[0];
					$newclass['linkid'] = $row['linkclasslicenseid'];
					$newclass['grade'] = $row['grade'];
				} else {
					$newclass['linkid'] = $row['linkclasslicenseid'];
					$newclass['grade'] = "N/A";
				}
					
				$output[] = $newclass;
			}

			return $output;
		}
	}


	function updateStudentLicense($pid,$param){
		$db = $this->dbfunc();
		foreach ($param['grade'] as $key=>$value){
			$query = "SELECT * FROM link_student_licenses WHERE
				studentid = " . $pid . " AND
				licenseid = " . $key . " AND
				cohortid = " . $param['cohortid'];
			$select = $db->query($query);
			$result = $select->fetchAll();
			if (count ($result) == 0){
				$insert = array(
					'studentid'		=> $pid,
					'licenseid'		=> $key,
					'cohortid'		=> $param['cohortid'],
					'grade'			=> $value,
				);
				$db->insert("link_student_licenses", $insert);
				
			} else {
				$row = $result[0];
				$insert = array(
					'grade'			=> $value,
				);
				$db->update("link_student_licenses", $insert,'id = ' . $row['id']);
			}
		}
	}

	function updateStudentClass($pid,$param){
		$db = $this->dbfunc();
		foreach ($param['grade'] as $key=>$value){
			$query = "SELECT * FROM link_student_classes WHERE
				studentid = " . $pid . " AND
				classid = " . $key . " AND
				cohortid = " . $param['cohortid'];
			$select = $db->query($query);
			$result = $select->fetchAll();
			if (count ($result) == 0){
				$insert = array(
					'studentid'		=> $pid,
					'classid'		=> $key,
					'cohortid'		=> $param['cohortid'],
					'grade'			=> $value,
				);
				$db->insert("link_student_classes", $insert);
			} else {
				$row = $result[0];
				$insert = array(
					'grade'			=> $value,
				);
				$db->update("link_student_classes", $insert,'id = ' . $row['id']);
			}		
		}
	}

	function updateStudentPracticum($pid,$param){
		$db = $this->dbfunc();
		foreach ($param['practicum'] as $key=>$value){
			$query = "SELECT * FROM link_student_practicums WHERE
				studentid = " . $pid . " AND
				practicumid = " . $key . " AND
				cohortid = " . $param['cohortid'];
			$select = $db->query($query);
			$result = $select->fetchAll();
			if (count ($result) == 0){
				$insert = array(
					'studentid'			=> $pid,
					'practicumid'		=> $key,
					'cohortid'			=> $param['cohortid'],
					'hourscompleted'	=> $value['completed'],
					'grade'				=> $value['grade'],
				);
				$db->insert("link_student_practicums", $insert);
			} else {
				$row = $result[0];
				$insert = array(
					'hourscompleted'	=> $value['completed'],
					'grade'				=> $value['grade'],
				);
				$db->update("link_student_practicums", $insert,'id = ' . $row['id']);
			}		
		}
	}

	public function deleteCohortPracticum($pid,$param){
		$db = $this->dbfunc();
		$query = "DELETE FROM practicum WHERE id = " . addslashes($param['delpracticum']);
		$db->query($query);
	}

	public function deleteCohortLicense($pid,$param){
		$db = $this->dbfunc();
		$query = "DELETE FROM licenses WHERE id = " . addslashes($param['dellicense']);
		$db->query($query);
	}

	public function saveLabels($param){
		$db = $this->dbfunc();
		foreach ($param['fields'] as $field=>$value){

			$select = $db->select()
				->from("translation")
				->where("key_phrase = ?",$field)
				->where("is_deleted = 0");
			$result = $db->fetchAll($select);
			if (count ($result) > 0){
				# UPDATE
				$row = $result[0];
				if (trim($value) != ""){
					# SETTING NEW VALUE
					$insert = array(
						'phrase'		=> $value,
					);
				} else {
					# DELETING
					$insert = array(
						'is_deleted'	=> 1,
					);
				}
				$db->update("translation", $insert,'id = ' . $row['id']);
			} else {
				# INSERT
				if (trim($value) != ""){
					$insert = array(
						'key_phrase'	=> $field,
						'phrase'		=> $value,
					);
					$db->insert("translation", $insert);
				}
			}
#			echo $select->__toString() . "<br>";
#			$result = $this->dbfunc()->fetchAll($select);
#			return $result;	

		}
	}

	public function getCohortInstitution($pid, $tp){
		$db = $this->dbfunc();
		$cohort = array();
		$institution = array();
		switch ($tp){
			case "student":
				
				// GETTING COHORT
				$select = $db->select()
					->from("link_student_cohort")
					->join(array("c" => "cohort"),
							"id_cohort = c.id")
					->where('id_student = ?', $pid);
					
				$result = $db->fetchAll($select);
				if (count ($result) > 0){
					$cohort = $result[0];

					// GETTING INSTITUTION
					$select = $db->select()
						->from("institution")
						->where('id = ?', $cohort['institutionid']);
						
					$result = $db->fetchAll($select);
					if (count ($result) > 0){
						$institution = $result[0];
					}
				}
				
				if( count($result) == 0 ){
					$select = $db->select()
						->from("institution")
						->join(array("s" => "student"),
								"s.institutionid = institution.id")
						->where('s.id = ?', $pid);
					$result = $db->fetchAll($select);
					if (count ($result) > 0){
						$institution = $result[0];
					}
				}
				
			break;
			case "tutor":
				
				$select = $db->select()
					->from("institution")
					->join(array("l" => "link_tutor_institution"),
							"l.id_institution = institution.id")
					->where('l.id_tutor = ?', $pid);
					
				$result = $db->fetchAll($select);
				
				//echo '<pre>'; print_r($result); echo '</pre>';
				if (count ($result) > 0){
					$institution = $result[0];
				} else {
					$select = $db->select()
						->from("institution")
						->join(array("t" => "tutor"),
								"t.institutionid = institution.id")
						->where('t.id = ?', $pid);
					$result = $db->fetchAll($select);
					if (count ($result) > 0){
						$institution = $result[0];
					}
				}
				
			break;
		}
		
		return array($cohort, $institution);
	}
	
	public function getPersonTraining($pid){
		$db = $this->dbfunc();
		$select = $db->select()
			->from("link_person_training")
			->where('personid = ?', $pid);
		$result = $db->fetchAll($select);
		$return = array();
		if (count ($result) > 0){
			foreach ($result as $row){
				// ADDING GROUPING, IF NOT EXIST
				$return[] = array(
					"id"			=> $row['id'],
					"personid"		=> $row['personid'],
					"trainingid"	=> $row['trainingid'],
					"year"			=> $row['year'],
					"institution"	=> $row['institution'],
					"othername"		=> $row['othername'],
				);
			}
		}
		return $return;
	}


	public function getSkillSmartLookups(){
		$db = $this->dbfunc();
		$select = $db->select()
			->from("lookup_skillsmart")
			->where('status = ?', 1)
			->order('lookupgroup')
			->order('lookupvalue');
		$result = $db->fetchAll($select);
		$return = array();
		if (count ($result) > 0){
			foreach ($result as $row){
				// ADDING GROUPING, IF NOT EXIST
				if (!isset ($return[$row['lookupgroup']])){
					$return[$row['lookupgroup']] = array();
				}

				// ADDING GROUPING, IF NOT EXIST
				$return[$row['lookupgroup']][] = array(
					"id"	=> $row['id'],
					"label"	=> $row['lookupvalue'],
				);
			}
		}
		return $return;
		
	}

	public function addSkillsmartLookup($params,$group){
		$linktable		= "lookup_skillsmart";
		$maincolumn		= "lookupvalue";
		$groupcolumn	= "lookupgroup";
		$id				= $_POST["_id"];
		$value			= $_POST['_fieldtoupdate'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $groupcolumn . ')) = ?', trim(strtolower($group)))
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$groupcolumn	=> $group,
				$maincolumn		=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}

	public function updateSkillsmartLookup($params){
		$linktable		= "lookup_skillsmart";
		$maincolumn		= "lookupvalue";
		$groupcolumn	= "lookupgroup";
		$id				= $_POST["_id"];
		$value			= $_POST['_fieldtoupdate'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $groupcolumn . ')) = ?', trim(strtolower($group)))
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('status = ?', 1)
			->where('id = ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}


	public function getSkillSmartCompetencies($cid = 0){
		if (!$cid){
			$db = $this->dbfunc();
			$select = $db->select()
				->from("competencies")
				->where('status = ?', 1)
				->order('competencyname');
			$result = $db->fetchAll($select);
			$return = array();
			if (count ($result) > 0){
				foreach ($result as $row){
					// ADDING GROUPING, IF NOT EXIST
					$return[] = array(
						"id"	=> $row['id'],
						"label"	=> $row['competencyname'],
					);
				}
			}
		} else {
			$db = $this->dbfunc();
			$select = $db->select()
				->from("competencies")
				->where('status = ?', 1)
				->where('id = ?', $cid)
				->order('competencyname');
			$result = $db->fetchAll($select);
			$return = array();
			if (count ($result) > 0){
				foreach ($result as $row){
					// ADDING GROUPING, IF NOT EXIST
					$return = array(
						"id"	=> $row['id'],
						"label"	=> $row['competencyname'],
					);
				}
			}
		}
		return $return;
		
	}

	public function addSkillsmartCompetency($params){
		$linktable		= "competencies";
		$maincolumn		= "competencyname";
		$id				= $_POST["_id"];
		$value			= $_POST['_fieldtoupdate'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)));
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn		=> $value
			);
			$instypeinsert = $this->dbfunc()->insert($linktable,$i_arr);
		}
	}

	public function updateSkillsmartCompetency($params){
		$linktable		= "competencies";
		$maincolumn		= "competencyname";
		$id				= $_POST["_id"];
		$value			= $_POST['_fieldtoupdate'];

		$select = $this->dbfunc()->select()
			->from($linktable)
			->where('LOWER(TRIM(' . $maincolumn . ')) = ?', trim(strtolower($value)))
			->where('status = ?', 1)
			->where('id = ?', $id);
		$result = $this->dbfunc()->fetchAll($select);
		if (count ($result) == 0){
			# LINK NOT FOUND - ADDING
			$i_arr = array(
				$maincolumn	=> $value
			);
			$instypeinsert = $this->dbfunc()->update($linktable,$i_arr,'id = ' . $id);
		}
	}

	public function getSkillSmartCompetenciesQuestions($cid = 0){
		$db = $this->dbfunc();
		$select = $db->select()
			->from("competencies_questions")
			->where('status = ?', 1)
			->where('competencyid = ?', $cid)
			->order('itemorder')
			->order('question');
		$result = $db->fetchAll($select);
		$return = array();
		if (count ($result) > 0){
			foreach ($result as $row){
				// ADDING GROUPING, IF NOT EXIST
				$return[] = array(
					"id"			=> $row['id'],
					"competencyid"	=> $row['competencyid'],
					"question"		=> $row['question'],
					"itemorder"		=> $row['itemorder'],
					"itemtype"		=> $row['itemtype'],
					"status"		=> $row['status'],
					"questiontype"	=> $row['questiontype'],
				);
			}
		}
		return $return;
		
	}

	public function addSkillsmartCompetencyQuestion($params,$compid){
		$db = $this->dbfunc();

		$id = $_POST['_iddetail'];
		$question = $_POST['_fieldtoupdatedetail'];
		$itemorder = $_POST['_orderdetail'];
		$itemtype = $_POST['_qtypedetail'];

		$query = "SELECT * FROM competencies_questions WHERE competencyid = " . $compid . " AND itemorder >= " . $itemorder;
#die($query);
		$select = $db->query($query);
		$itemstomove = $select->fetchAll();

		foreach ($itemstomove as $itm){
			// MOVING ITEMS BACK BY ONE TO OPEN A SPOT
			$upd = "UPDATE competencies_questions SET itemorder = " . ($itm['itemorder'] + 1) . " WHERE id = " . $itm['id'];
#die($upd);
			$db->query($upd);
		}

		$query = "INSERT INTO competencies_questions SET
			competencyid = '" . addslashes($compid) . "',
			question = '" . addslashes($question) . "',
			itemorder = '" . addslashes($itemorder) . "',
			itemtype = '" . addslashes($itemtype) . "',
			status = 1";
#die($query);
		$db->query($query);
		$this->skillsmartCompetencyCloseGaps($compid);
	}

	public function updateSkillsmartCompetencyQuestion($params,$compid){
		$db = $this->dbfunc();

		$id = $_POST['_iddetail'];
		$question = $_POST['_fieldtoupdatedetail'];
		$itemorder = $_POST['_orderdetail'];
		$itemtype = $_POST['_qtypedetail'];

		// MAKING A GAP IN THE NUMBERING FOR THE NEW ITEM
		$query = "SELECT * FROM competencies_questions WHERE competencyid = " . $compid . " AND id <> " . $id . " AND itemorder >= " . $itemorder;

#die($query);

		$select = $db->query($query);
		$itemstomove = $select->fetchAll();

		foreach ($itemstomove as $itm){
			// MOVING ITEMS BACK BY ONE TO OPEN A SPOT
			$upd = "UPDATE competencies_questions SET itemorder = " . ($itm['itemorder'] + 1) . " WHERE id = " . $itm['id'];
#die($upd);
			$db->query($upd);
		}

		$query = "UPDATE competencies_questions SET
			question = '" . addslashes($question) . "',
			itemorder = '" . addslashes($itemorder) . "',
			itemtype = '" . addslashes($itemtype) . "'
			WHERE id = " . $id;
#die($query);
		$db->query($query);
		$this->skillsmartCompetencyCloseGaps($compid);
	}
	
	function skillsmartCompetencyCloseGaps($compid){
		$db = $this->dbfunc();
		// CLOSING ANY NUMBERING GAPS AFTER MOVING/ADDING/UPDATING/DELETING ACTIONS
		$query = "SELECT * FROM competencies_questions WHERE competencyid = " . $compid . " AND status = 1 ORDER BY itemorder";
		$select = $db->query($query);
		$itemstomove = $select->fetchAll();
		$counter = 1;

		foreach ($itemstomove as $itm){
			// MOVING ITEMS TO COUNTER POSITION
			$upd = "UPDATE competencies_questions SET itemorder = " . $counter . " WHERE id = " . $itm['id'];
			$db->query($upd);
			$counter++;
		}
	}

	function skillsmartGetQualifications(){
		$db = $this->dbfunc();
		$query = "SELECT * FROM person_qualification_option WHERE parent_id IS NULL AND is_deleted = 0 ORDER BY qualification_phrase";
		$result = $db->query($query);
		$parentitems = $result->fetchAll();
		$return = array();
		foreach ($parentitems as $parent){
			$return[] = array("id" => $parent['id'], "label" => $parent['qualification_phrase']);
/*
		
			$subquery = "SELECT * FROM person_qualification_option WHERE parent_id = " . $parent['id'] . " AND is_deleted = 0 ORDER BY qualification_phrase";
			$subresult = $db->query($subquery);
			$childitems = $subresult->fetchAll();
			foreach ($childitems as $child){
				$return[$parent['qualification_phrase']][] = array(
					"id" => $child['id'],
					"label" => $child['qualification_phrase'],
				);
			}
*/
		}
		return $return;
	}

	function skillsmartLinkQualComp($params){
		$db = $this->dbfunc();
		$compid = $params['compid'];
		$parsed = array();
		foreach ($params['qual'] as $k=>$v){
			$parsed[] = $v;
			$query = "SELECT * FROM link_qualification_competency WHERE
				competencyid = '" . addslashes($compid) . "' AND qualificationid = '" . addslashes($v) . "'";
			$result = $db->query($query);
			$rows = $result->fetchAll();
			if (count ($rows) == 0){
				$query = "INSERT INTO link_qualification_competency SET
					competencyid = '" . addslashes($compid) . "',
					qualificationid = '" . addslashes($v) . "'";
				$db->query($query);
			}
		}
		
		// Removing any remaining items if they are no longer checked
		$query = "DELETE FROM link_qualification_competency WHERE competencyid = '" . addslashes($compid) . "' AND qualificationid NOT IN (" . implode(",", $parsed) . ")";

		$db->query ($query);
	}

	function skillsmartGetCompetencyLinks($compid){
		$return = array();
		$db = $this->dbfunc();
		$query = "SELECT * FROM link_qualification_competency WHERE
			competencyid = '" . addslashes($compid) . "'";
		$result = $db->query($query);
		$rows = $result->fetchAll();
		if (count ($rows) != 0){
			foreach ($rows as $row){
				$return[] = $row['qualificationid'];
			}
		}
		return $return;
	}

	function getPersonCompetencies($pid, $get_secondary = false){
		$db = $this->dbfunc();
		$query = "SELECT * FROM person WHERE id = " . $pid;
#echo $query . "<br>";
		$result = $db->query($query);
		$rows = $result->fetchAll();
		$return = false;
		if (count ($rows) > 0){
			$return = array();
			$personrow = $rows[0];
			$qualification = $personrow['primary_qualification_option_id'];

			$qualification_ids = array();
			$qualification_ids[] = $qualification;
			if($get_secondary){
				$secondary_sql = "SELECT id FROM person_qualification_option WHERE parent_id = {$qualification}";
				$res = $db->query($secondary_sql);
				$qualrows = $res->fetchAll();
				foreach($qualrows as $row){
					$qualification_ids[] = $row['id'];
				}
			}
			
			$qualquery = "SELECT * FROM link_qualification_competency WHERE qualificationid IN (".implode(',', $qualification_ids).")";
			#echo $qualquery . "<br>";
			$qualresult = $db->query($qualquery);
			$qualrows = $qualresult->fetchAll();
			if (count ($qualrows) > 0){
				$comps = array();
				foreach ($qualrows as $qr){
					$comps[] = $qr['competencyid'];
				}

				// GETTING QUESTIONS FOR THIS COMP
				if (count ($comps) > 0){
					$compids = implode(",", $comps);

					// Getting all questions
					$qquery = "SELECT * FROM competencies_questions WHERE competencyid IN (" . $compids . ") AND status = 1";
#echo $qquery . "<br>";
					$qresult = $db->query($qquery);
					$qrows = $qresult->fetchAll();
					$questions = array();
					$required = 0;
					$qids = array();
					foreach ($qrows as $q){
						$questions[] = $q;
						if (substr($q['itemtype'],0,8) == "question"){
							$required++;
							$qids[] = $q['id'];
						}
					}

					// Getting all answers
					$aquery = "SELECT * FROM comp WHERE `question` IN (" . (count($qids) > 0 ? implode(",", $qids) : 0) . ") AND `person` = " . $pid . " AND `active` = 'Y'";
					$aresult = $db->query($aquery);
					$arows = $aresult->fetchAll();
					$answers = array();
					$unanswered = 0;
					foreach ($arows as $a){
						$answers[] = $a;
						if ($a['answer'] == "F"){
							$unanswered++;
						}
					}
					
					$return['competencies'] = $comps;
					$return['questions'] = $questions;
					$return['answers'] = $answers;
					$return['required'] = $required;
					$return['unanswered'] = $unanswered;
				}
			}
		}
		return $return;
	}

	function getPersonCompetenciesDetailed($pid){
		$db = $this->dbfunc();
		$query = "SELECT * FROM person WHERE id = " . $pid;
		$result = $db->query($query);
		$rows = $result->fetchAll();
		$return = false;
		if (count ($rows) > 0){
			$return = array();
			$personrow = $rows[0];
			$qualification = $personrow['primary_qualification_option_id'];

			$qualquery = "SELECT * FROM link_qualification_competency WHERE qualificationid = " . $qualification;
			$qualresult = $db->query($qualquery);
			$qualrows = $qualresult->fetchAll();
			if (count ($qualrows) > 0){
				$comps = array();
				foreach ($qualrows as $qr){
					$comps[] = $qr['competencyid'];
				}

				$structure = array();

				// GETTING QUESTIONS FOR THIS COMP
				if (count ($comps) > 0){
					foreach ($comps as $compid){
						$topquery = "SELECT * FROM competencies WHERE id = " . $compid;
						$topresult = $db->query($topquery);
						$toprows = $topresult->fetchAll();
						$toprow = $toprows[0];
						$_str = array(
							"name" => $toprow['competencyname'],
							"questions" => array(),
						);

						// Get all questions
						$qids = array();
						$qquery = "SELECT * FROM competencies_questions WHERE competencyid = " . $compid . " AND status = 1 ORDER BY itemorder";
						$qresult = $db->query($qquery);
						$qrows = $qresult->fetchAll();
						$required = 0;
						foreach ($qrows as $q){
							$_str['questions'][] = $q;
							$qids[] = $q['id'];
							if (substr($q['itemtype'],0,8) == "question"){
								$required++;
							}
						}
						$_str['required'] = $required;

						// Getting all answers
						$aquery = "SELECT * FROM comp WHERE `question` IN (" . (count($qids) > 0 ? implode(",", $qids) : 0) . ") AND `person` = " . $pid . " AND `active` = 'Y'";
						$aresult = $db->query($aquery);
						$arows = $aresult->fetchAll();
						$_str['answers'] = array();
						$unanswered = 0;
						foreach ($arows as $a){
							$_str['answers'][] = $a;
							if ($a['answer'] == "F"){
								$unanswered++;
							}
						}
						$_str['unanswered'] = $unanswered;
						$structure[] = $_str;
					}
				}
				$return = $structure;
			}
		}
		return $return;
	}

	public function saveCompetencyAnswers($questions,$pid){
		$db = $this->dbfunc();
		$parsed = array();
		foreach ($questions as $k=>$v){
			if (trim ($v) != ""){
				$query = "SELECT * FROM comp WHERE
					`question` = '" . addslashes($k) . "' AND 
					`person` = '" . addslashes($pid) . "'";
				$result = $db->query($query);
				$rows = $result->fetchAll();
				if (count ($rows) == 0){
					$query = "INSERT INTO comp SET
						`person` = '" . addslashes($pid) . "',
						`question` = '" . addslashes($k) . "',
						`option` = '" . addslashes($v) . "',
						`active` = 'Y'";
					$db->query($query);
					$parsed[] = $this->dbfunc()->lastInsertId();
				} else {
					$row = $rows[0];
					$query = "UPDATE comp SET
						`option` = '" . addslashes($v) . "'
						WHERE id = " . $row['id'];
					$db->query($query);
					$parsed[] = $row['id'];
				}
			}
		}
		
		// Deactivating any remaining items if they are no longer checked
		$query = "UPDATE comp SET active = 'N' WHERE person = " . $pid . " AND id NOT IN (" . implode(",", $parsed) . ")";
		$db->query ($query);
	}
	public function getCompetencies(){
		$db = $this->dbfunc();
		$query = "SELECT * FROM competencies WHERE
			status = 1
			ORDER BY competencyname";
		$select = $db->query($query);
		$result = $select->fetchAll();
		return $result;
	}

	public function getQualificationCompetencies($cid = false){
		$db = $this->dbfunc();
		if ($cid !== false){
			$query = "SELECT * FROM person_qualification_option WHERE
				parent_id IS NULL AND
				id = " . $cid . " AND
				is_deleted = 0
				ORDER BY qualification_phrase";
		} else {
			$query = "SELECT * FROM person_qualification_option WHERE
				parent_id IS NULL AND
				is_deleted = 0
				ORDER BY qualification_phrase";
		}

		$return = array();

		$select = $db->query($query);
		$result = $select->fetchAll();
		if (count ($result) > 0){
			foreach ($result as $row){
				$_arr = array(
					"id"			=> $row['id'],
					"name"			=> $row['qualification_phrase'],
					"competencies"	=> array(),
				);
				
				$subquery = "SELECT c.*, lqc.id AS linkid
					FROM competencies c
					INNER JOIN link_qualification_competency lqc ON lqc.competencyid = c.id
					WHERE lqc.qualificationid = " . $row['id'] . " ORDER BY competencyname";
				$subselect = $db->query($subquery);
				$subresult = $subselect->fetchAll();
				foreach ($subresult as $subrow){
					$_arr['competencies'][] = array(
						"id"		=> $subrow['id'],
						"name"		=> $subrow['competencyname'],
						"linkid"	=> $subrow['linkid'],
					);
				}
				
				$return[] = $_arr;
			}
		}
		return $return;
	}
	
	
	public function saveUserInstitutions($uid,$ins){
		$db = $this->dbfunc();
		if (count ($ins) == 0){
			$query = "DELETE FROM link_user_institution WHERE userid = " . $uid;
			$this->dbfunc()->query($query);
		} else {
			$parsed = array();
			foreach ($ins as $k=>$v){
				if (trim ($v) != ""){
					$query = "SELECT * FROM link_user_institution WHERE
						`userid` = '" . addslashes($uid) . "' AND 
						`institutionid` = '" . addslashes($v) . "'";
					$result = $db->query($query);
					$rows = $result->fetchAll();
					if (count ($rows) == 0){
						$query = "INSERT INTO link_user_institution SET
							`userid` = '" . addslashes($uid) . "',
							`institutionid` = '" . addslashes($v) . "'";
						$db->query($query);
					}
					$parsed[] = $v;
				}
			}
			
			// Deactivating any remaining items if they are no longer checked
			$query = "DELETE FROM link_user_institution WHERE
				`userid` = '" . addslashes($uid) . "' AND 
				`institutionid` NOT IN (" . implode(",", $parsed) . ")";
			$db->query ($query);
		}
	}
	
	public function addUserInstitutionRights($uid,$id){
		$db = $this->dbfunc();
		// Checking if any rights exist. If blank, show all institutions
		$query = "SELECT * FROM link_user_institution WHERE
			`userid` = '" . addslashes($uid) . "'";
		$result = $db->query($query);
		$rows = $result->fetchAll();
		if (count ($rows) > 0){
			// Check if this institution is already linked
			$query = "SELECT * FROM link_user_institution WHERE
				`userid` = '" . addslashes($uid) . "' AND 
				`institutionid` = '" . addslashes($id) . "'";
			$result = $db->query($query);
			$rows = $result->fetchAll();
			if (count ($rows) == 0){
				// Not linked yet. Adding a link
				$query = "INSERT INTO link_user_institution SET
					`userid` = '" . addslashes($uid) . "',
					`institutionid` = '" . addslashes($id) . "'";
				$db->query($query);
			}
		}
	}

	public function myid(){
		// Getting current credentials
		$auth = Zend_Auth::getInstance ();
		$identity = $auth->getIdentity ();
		return $identity->id;
	}

	public function updatePersonInstitution($tp = "student",$pid,$objectid){
		$db = $this->dbfunc();
		switch ($tp){
			default:
				// GETTING INSTITUTION ID
				$query = "SELECT * FROM cohort WHERE id = " . $objectid;
				$result = $db->fetchAll($query);
				if (count($result) > 0){
					$id = $result[0]['institutionid'];
				} else {
					$id = 0;
				}

				// UPDATING STUDENT RECORD
				$query = "UPDATE student SET institutionid = " . $id . " WHERE id = " . $pid;
				$db->query($query);
			break;
		}
	}

	public function getCompQuestions($compids){
		$db = $this->dbfunc();
		$ret = array();
		$query = "SELECT id FROM competencies_questions WHERE competencyid IN (" . implode(",", $compids) . ")";
		$result = $db->fetchAll($query);
		if (count ($result) > 0){
			foreach ($result as $row){
				$ret[] = $row['id'];
			}
		}
		return $ret;
	}

}

	
 
?>