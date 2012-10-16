<?php 
require_once('ITechTable.php');

class Helper extends ITechTable
{
	#################################
	#                               #
	#   COHORT SPECIFIC FUNCTIONS   #
	#                               #
	#################################

	public function getCohorts(){
		// RETURNS A LIST OF ALL COHORST ORDERED BY NAME
		$select = $this->dbfunc()->select()
			->from("cohort")
			->order('cohortname');
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

	public function getInstitutions() {
		$select = $this->dbfunc()->select()
			->from("institution")
			->order('institutionname');
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

	public function getFacilityTypes() {
		$select = $this->dbfunc()->select()
			->from("lookup_facilitytype")
			->order('facilitytypename');
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
		// RETURNS A LIST OF ALL COURSE TYPES ORDERED BY NAME
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
		$value = $_POST['_classname'];

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
		var_dump ($param);
		exit;
	}

	public function deleteCohortLicense($pid,$param){
		var_dump ($param);
		exit;
	}

}

	
 
?>