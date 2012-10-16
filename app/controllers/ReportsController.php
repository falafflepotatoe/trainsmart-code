<?php
/*
* Created on Feb 27, 2008
*
*  Built for web
*  Fuse IQ -- todd@fuseiq.com
*
*/
require_once ('ReportFilterHelpers.php');
require_once ('models/table/OptionList.php');
//require_once('models/table/Course.php');
require_once ('views/helpers/CheckBoxes.php');
require_once ('models/table/MultiAssignList.php');
require_once ('models/table/TrainingTitleOption.php');
require_once ('models/table/Helper.php');

class ReportsController extends ReportFilterHelpers {

	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	}

	public function init() {
	}

	public function indexAction() {

		#		$itc = new ITechController();
		#		var_dump ($itc);
		#		$this->view->assign('ps',$this->hasACL('pre_service'));
	}

	public function preDispatch() {
		$rtn = parent::preDispatch ();
		$allowActions = array ('trainingSearch' );

		if (! $this->isLoggedIn ())
		$this->doNoAccessError ();

		if (! $this->hasACL ( 'view_create_reports' ) && ! in_array ( $this->getRequest ()->getActionName (), $allowActions )) {
			$this->doNoAccessError ();
		}

		return $rtn;

	}

	public function dataAction() {

	}

	/**
	* Converts or returns header labels. Since the export CSV must use header
	* labels instead of database fields, define headers here.
	*
	* @param $fieldname = database field name to convert
	* @param $rowRay = will add CSV headers to array
	*
	* @todo modify all report phtml files to use these headers
	*/
	public function reportHeaders($fieldname = false, $rowRay = false) {

		require_once ('models/table/Translation.php');
		$translation = Translation::getAll ();

		if ($this->setting('display_mod_skillsmart')){
			$headers = array (// fieldname => label
			'id' => 'ID',
			'pcnt' => 'Participants',
			'has_known_participants' => 'Known participants',
			'region_c_name' => 'Sub-district',
			'training_method_phrase' => 'Training method',
			'is_refresher' => 'Refresher',
			'secondary_language_phrase' => 'Secondary language',
			'primary_language_phrase' => 'Primary language',
			'got_comments' => 'National curriculum comment',
			'training_got_curriculum_phrase' => 'National curriculum',
			'training_category_phrase' => 'Training category',
			'age' => 'Age',
			'comments1' => 'Professional registration number',
			'comments2' => 'Race',
			'comments3' => 'Experience',
			'score_pre' => 'Pre-test',
			'score_post' => 'Post-test',
			'score_percent_change' => '% change',
			'custom1_phrase' => 'Professional registration number',
			'city_name' => 'City',
			'cnt' => t ( 'Count' ), 'active' => @$translation ['Is Active'], 'first_name' => @$translation ['First Name'], 'middle_name' => @$translation ['Middle Name'], 'last_name' => @$translation ['Last Name'], 'training_title' => @$translation ['Training Name'], 'province_name' => @$translation ['Region A (Province)'], 'district_name' => @$translation ['Region B (Health District)'], 'pepfar_category_phrase' => @$translation ['PEPFAR Category'], 'training_organizer_phrase' => @$translation ['Training Organizer'], 'training_level_phrase' => @$translation ['Training Level'], 'trainer_language_phrase' => t ( 'Language' ), 'training_location_name' => t ( 'Location' ), 'training_start_date' => t ( 'Date' ), 'training_topic_phrase' => t ( 'Training topic' ), 'funding_phrase' => t ( 'Funding' ), 'is_tot' => t ( 'TOT' ), 'facility_name' => t ( 'Facility name' ), 'facility_type_phrase' => t ( 'Facility type' ), 'facility_sponsor_phrase' => t ( 'Facility sponsor' ), 'course_recommended' => t ( 'Recommended classes' ), 'recommended' => t ( 'Recommended' ), 'qualification_phrase' => t ( 'Qualification' ) . ' ' . t ( '(primary)' ), 'qualification_secondary_phrase' => t ( 'Qualification' ) . ' ' . t ( '(secondary)' ), 'gender' => t ( 'Gender' ), 'name' => t ( 'Name' ), 'email' => t ( 'Email' ), 'phone' => t ( 'Phone' ), 'cat' => t ( 'Category' ), 'language_phrase' => t ( 'Language' ), 'trainer_type_phrase' => t ( 'Type' ), 'trainer_skill_phrase' => t ( 'Skill' ), 'trainer_language_phrase' => t ( 'Language' ), 'trainer_topic_phrase' => t ( 'Topics taught' ), 'phone_work' => t ( 'Work phone' ), 'phone_home' => t ( 'Home phone' ), 'phone_mobile' => t ( 'Mobile phone' ), 'type_option_id' => 'Type' );

			// action => array(field => label)
			$headersSpecific = array ('peopleByFacility' => array ('qualification_phrase' => t ( 'Qualification' ) ), 'participantsByCategory' => array ('cnt' => t ( 'Participants' ), 'person_cnt' => t ( 'Unique participants' ) ) );
		} else {
			$headers = array (// fieldname => label
			'id' => 'ID', 'cnt' => t ( 'Count' ), 'active' => @$translation ['Is Active'], 'first_name' => @$translation ['First Name'], 'middle_name' => @$translation ['Middle Name'], 'last_name' => @$translation ['Last Name'], 'training_title' => @$translation ['Training Name'], 'province_name' => @$translation ['Region A (Province)'], 'district_name' => @$translation ['Region B (Health District)'], 'pepfar_category_phrase' => @$translation ['PEPFAR Category'], 'training_organizer_phrase' => @$translation ['Training Organizer'], 'training_level_phrase' => @$translation ['Training Level'], 'trainer_language_phrase' => t ( 'Language' ), 'training_location_name' => t ( 'Location' ), 'training_start_date' => t ( 'Date' ), 'training_topic_phrase' => t ( 'Training Topic' ), 'funding_phrase' => t ( 'Funding' ), 'is_tot' => t ( 'TOT' ), 'facility_name' => t ( 'Facility Name' ), 'facility_type_phrase' => t ( 'Facility Type' ), 'facility_sponsor_phrase' => t ( 'Facility Sponsor' ), 'course_recommended' => t ( 'Recommended classes' ), 'recommended' => t ( 'Recommended' ), 'qualification_phrase' => t ( 'Qualification' ) . ' ' . t ( '(primary)' ), 'qualification_secondary_phrase' => t ( 'Qualification' ) . ' ' . t ( '(secondary)' ), 'gender' => t ( 'Gender' ), 'name' => t ( 'Name' ), 'email' => t ( 'Email' ), 'phone' => t ( 'Phone' ), 'cat' => t ( 'Category' ), 'language_phrase' => t ( 'Language' ), 'trainer_type_phrase' => t ( 'Type' ), 'trainer_skill_phrase' => t ( 'Skill' ), 'trainer_language_phrase' => t ( 'Language' ), 'trainer_topic_phrase' => t ( 'Topics Taught' ), 'phone_work' => t ( 'Work Phone' ), 'phone_home' => t ( 'Home Phone' ), 'phone_mobile' => t ( 'Mobile Phone' ), 'type_option_id' => 'Type' );

			// action => array(field => label)
			$headersSpecific = array ('peopleByFacility' => array ('qualification_phrase' => t ( 'Qualification' ) ), 'participantsByCategory' => array ('cnt' => t ( 'Participants' ), 'person_cnt' => t ( 'Unique Participants' ) ) );
		}

		if ($rowRay) {
			$keys = array_keys ( reset ( $rowRay ) );
			foreach ( $keys as $k ) {
				$csvheaders [] = $this->reportHeaders ( $k );
			}

			return array_merge ( array ('csvheaders' => $csvheaders ), $rowRay );

		} elseif ($fieldname) {

			// check report specific headers first
			$action = $this->getRequest ()->getActionName ();
			if (isset ( $headersSpecific [$action] ) && isset ( $headersSpecific [$action] [$fieldname] )) {
				return $headersSpecific [$action] [$fieldname];
			}

			return (isset ( $headers [$fieldname] )) ? $headers [$fieldname] : $fieldname;
		} else {
			return $headers;
		}

	}

	public function compcsvAction() {
		$v1=split("~",$this->getSanParam ( 'v1' ));
		$v2=split("~",$this->getSanParam ( 'v2' ));
		$p=$this->getSanParam ( 'p' );
		$d=$this->getSanParam ( 'd' );
		$s=$this->getSanParam ( 's' );
		$f=$this->getSanParam ( 'f' );
		$this->viewAssignEscaped ( 'v1', $v1 );
		$this->viewAssignEscaped ( 'v2', $v2 );
		$this->viewAssignEscaped ( 'p',  $p);
		$this->viewAssignEscaped ( 'd',  $d);
		$this->viewAssignEscaped ( 's',  $s);
		$this->viewAssignEscaped ( 'f',  $f);
	}

	public function profcsvAction() {
		$v1=split("~",$this->getSanParam ( 'v1' ));
		$v2=split("~",$this->getSanParam ( 'v2' ));
		$v3=split("~",$this->getSanParam ( 'v3' ));
		$v4=split("~",$this->getSanParam ( 'v4' ));
		$v5=split("~",$this->getSanParam ( 'v5' ));
		$v6=split("~",$this->getSanParam ( 'v6' ));
		$p=$this->getSanParam ( 'p' );
		$d=$this->getSanParam ( 'd' );
		$s=$this->getSanParam ( 's' );
		$f=$this->getSanParam ( 'f' );
		$this->viewAssignEscaped ( 'v1', $v1 );
		$this->viewAssignEscaped ( 'v2', $v2 );
		$this->viewAssignEscaped ( 'v3', $v3 );
		$this->viewAssignEscaped ( 'v4', $v4 );
		$this->viewAssignEscaped ( 'v5', $v5 );
		$this->viewAssignEscaped ( 'v6', $v6 );
		$this->viewAssignEscaped ( 'p',  $p);
		$this->viewAssignEscaped ( 'd',  $d);
		$this->viewAssignEscaped ( 's',  $s);
		$this->viewAssignEscaped ( 'f',  $f);
	}

	public function detailAction() {
		if (! $this->hasACL ( 'view_people' ) and ! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		$criteria = array ();
		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);
		$criteria ['facilityInput'] = $this->getSanParam ( 'facilityInput' );
		$criteria ['training_title_option_id'] = $this->getSanParam ( 'training_title_option_id' );
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['ques'] = $this->getSanParam ( 'ques' );
		$criteria ['score_id'] = $this->getSanParam ( 'score_id' );
		$criteria ['primarypatients'] = $this->getSanParam ( 'primarypatients' );
		$criteria ['hivInput'] = $this->getSanParam ( 'hivInput' );
		$criteria ['trainer_type_option_id1'] = $this->getSanParam ( 'trainer_type_option_id1' );
		$criteria ['grp1'] = $this->getSanParam ( 'grp1' );
		$criteria ['grp2'] = $this->getSanParam ( 'grp2' );
		$criteria ['grp3'] = $this->getSanParam ( 'grp3' );
		$criteria ['go'] = $this->getSanParam ( 'go' );
		if ($criteria ['go']) {
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id);
			$sql = 'select DISTINCT cmp.person, cmp.question, cmp.option from person as p, person_qualification_option as q, facility as f, ('.$location_sub_query.') as l, comp as cmp, compres as cmpr';
			if ( $criteria['training_title_option_id'] ) {
				$sql .= ', person_to_training as ptt ';
				$sql .= ', training as tr  ';
			}
			$where = array('p.is_deleted = 0');
			$whr = array();
			$where []= 'cmpr.person = p.id';
			$where []= 'cmp.person = p.id';
			$where []= ' p.primary_qualification_option_id = q.id and p.facility_id = f.id and f.location_id = l.id ';
			if ($criteria ['facilityInput']) {
				$where []= ' p.facility_id = "' . $criteria ['facilityInput'] . '"';
			}
			if ( $criteria['training_title_option_id'] ) {
				$where []= ' p.id = ptt.person_id AND ptt.training_id = tr.id AND tr.training_title_option_id = ' . ($criteria ['training_title_option_id']) . ' ';
			}
			$where []= ' primary_qualification_option_id IN (SELECT id FROM person_qualification_option WHERE parent_id = ' . $criteria ['qualification_id'] . ') ';
			$where []= 'cmpr.active = \'Y\'';
			$where []= 'cmpr.res = 1';
			$where []= 'cmp.active = \'Y\'';
			if($criteria ['qualification_id']=="6")
			{
				$whr []= 'cmp.question IN ('."'".str_replace(",","','",$this->getSanParam ( 'listcq' ))."'".')';
			}
			if($criteria ['qualification_id']=="7")
			{
				$qs=split(",",$this->getSanParam ( 'ques' ));
				$nms=split("~",$this->getSanParam ( 'listdq' ));
				foreach ( $qs as $kys => $vls ) {
					$whr []= 'cmp.question IN ('."'".str_replace(",","','",$nms[$vls])."'".')';
				}
			}
			if($criteria ['qualification_id']=="8")
			{
				$qs=split(",",$this->getSanParam ( 'ques' ));
				$nms=split("~",$this->getSanParam ( 'listnq' ));
				foreach ( $qs as $kys => $vls ) {
					$whr []= 'cmp.question IN ('."'".str_replace(",","','",$nms[$vls])."'".')';
				}
			}
			if($criteria ['qualification_id']=="9")
			{
				$whr []= 'cmp.question IN ('."'".str_replace(",","','",$this->getSanParam ( 'listpq' ))."'".')';
			}
			$sql .= ' WHERE ' . implode(' AND ', $where);
			$sql .= ' AND (' . implode(' OR ', $whr) . ')';
			$rowArray = $db->fetchAll ( $sql );
			$qss=array();
			$nmss=array();
			if($criteria ['qualification_id']=="6")
			{
				$qss=split(",",$this->getSanParam ( 'ques' ));
				$nmss=split("~",$this->getSanParam ( 'listcq' ));
			}
			if($criteria ['qualification_id']=="7")
			{
				$qss=split(",",$this->getSanParam ( 'ques' ));
				$nmss=split("~",$this->getSanParam ( 'listdq' ));
			}
			if($criteria ['qualification_id']=="8")
			{
				$qss=split(",",$this->getSanParam ( 'ques' ));
				$nmss=split("~",$this->getSanParam ( 'listnq' ));
			}
			if($criteria ['qualification_id']=="9")
			{
				$qss=split(",",$this->getSanParam ( 'ques' ));
				$nmss=split("~",$this->getSanParam ( 'listpq' ));
			}
			$ct;
			$ct=0;
			$rss=array();
			$ctt;
			foreach ( $qss as $kys => $vls ) {
				$rss[$ct]=0;
				$ctt=0;
				$wss=split(",",$nmss[$vls]);
				foreach ( $wss as $kyss => $vlss ) {
					foreach ( $rowArray as $kss => $vss ) {
						if($vlss." " == $vss['question']." ")
						{
							if($vss['option']=="A")
							{
								$rss[$ct]=$rss[$ct]+4;
							}
							else
							{
								if($vss['option']=="B")
								{
									$rss[$ct]=$rss[$ct]+3;
								}
								else
								{
									if($vss['option']=="C")
									{
										$rss[$ct]=$rss[$ct]+2;
									}
									else
									{
										if($vss['option']=="D")
										{
											$rss[$ct]=$rss[$ct]+1;
										}
									}
								}
							}
							$ctt=$ctt+1;
						}
					}
				}
				if($ctt>0)
				$rss[$ct]=number_format((($rss[$ct]/(4*$ctt))*100),2);
				$ct=$ct+1;
			}
			$this->viewAssignEscaped ( 'results', $rowArray );
			$this->viewAssignEscaped ( 'rss', $rss );
		}
		$this->view->assign ( 'criteria', $criteria );
		$this->viewAssignEscaped ( 'locations', Location::getAll() );
		require_once ('models/table/TrainingTitleOption.php');
		$titleArray = TrainingTitleOption::suggestionList ( false, 10000 );
		$this->viewAssignEscaped ( 'courses', $titleArray );
		$qualificationsArray = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false );
		$this->viewAssignEscaped ( 'qualifications', $qualificationsArray );
		$rowArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $rowArray as $key => $val ) {
			if ($val ['id'] != 0)
			$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );
	}

	public function compAction() {
		if (! $this->hasACL ( 'view_people' ) and ! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		$criteria = array ();
		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);
		$criteria ['facilityInput'] = $this->getSanParam ( 'facilityInput' );
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['ques'] = $this->getSanParam ( 'ques' );
		$criteria ['go'] = $this->getSanParam ( 'go' );
		if ($criteria ['go']) {
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id);
			$sql = 'select DISTINCT cmp.person, cmp.question, cmp.option from person as p, person_qualification_option as q, facility as f, ('.$location_sub_query.') as l, comp as cmp, compres as cmpr';
			$where = array('p.is_deleted = 0');
			$whr = array();
			$where []= 'cmpr.person = p.id';
			$where []= 'cmp.person = p.id';
			$where []= ' p.primary_qualification_option_id = q.id and p.facility_id = f.id and f.location_id = l.id ';
			if ($criteria ['facilityInput']) {
				$where []= ' p.facility_id = "' . $criteria ['facilityInput'] . '"';
			}
			$where []= ' primary_qualification_option_id IN (SELECT id FROM person_qualification_option WHERE parent_id = ' . $criteria ['qualification_id'] . ') ';
			$where []= 'cmpr.active = \'Y\'';
			$where []= 'cmpr.res = 1';
			$where []= 'cmp.active = \'Y\'';
			if($criteria ['qualification_id']=="6")
			{
				$whr []= 'cmp.question IN ('."'".str_replace(",","','",$this->getSanParam ( 'listcq' ))."'".')';
			}
			if($criteria ['qualification_id']=="7")
			{
				$qs=split(",",$this->getSanParam ( 'ques' ));
				$nms=split("~",$this->getSanParam ( 'listdq' ));
				foreach ( $qs as $kys => $vls ) {
					$whr []= 'cmp.question IN ('."'".str_replace(",","','",$nms[$vls])."'".')';
				}
			}
			if($criteria ['qualification_id']=="8")
			{
				$qs=split(",",$this->getSanParam ( 'ques' ));
				$nms=split("~",$this->getSanParam ( 'listnq' ));
				foreach ( $qs as $kys => $vls ) {
					$whr []= 'cmp.question IN ('."'".str_replace(",","','",$nms[$vls])."'".')';
				}
			}
			if($criteria ['qualification_id']=="9")
			{
				$whr []= 'cmp.question IN ('."'".str_replace(",","','",$this->getSanParam ( 'listpq' ))."'".')';
			}
			$sql .= ' WHERE ' . implode(' AND ', $where);
			$sql .= ' AND (' . implode(' OR ', $whr) . ')';
			$rowArray = $db->fetchAll ( $sql );
			$qss=array();
			$nmss=array();
			if($criteria ['qualification_id']=="6")
			{
				$qss=split(",",$this->getSanParam ( 'ques' ));
				$nmss=split("~",$this->getSanParam ( 'listcq' ));
			}
			if($criteria ['qualification_id']=="7")
			{
				$qss=split(",",$this->getSanParam ( 'ques' ));
				$nmss=split("~",$this->getSanParam ( 'listdq' ));
			}
			if($criteria ['qualification_id']=="8")
			{
				$qss=split(",",$this->getSanParam ( 'ques' ));
				$nmss=split("~",$this->getSanParam ( 'listnq' ));
			}
			if($criteria ['qualification_id']=="9")
			{
				$qss=split(",",$this->getSanParam ( 'ques' ));
				$nmss=split("~",$this->getSanParam ( 'listpq' ));
			}
			$ct;
			$ct=0;
			$rss=array();
			$ctt;
			foreach ( $qss as $kys => $vls ) {
				$rss[$ct]=0;
				$ctt=0;
				$wss=split(",",$nmss[$vls]);
				foreach ( $wss as $kyss => $vlss ) {
					foreach ( $rowArray as $kss => $vss ) {
						if($vlss." " == $vss['question']." ")
						{
							if($vss['option']=="A")
							{
								$rss[$ct]=$rss[$ct]+4;
							}
							else
							{
								if($vss['option']=="B")
								{
									$rss[$ct]=$rss[$ct]+3;
								}
								else
								{
									if($vss['option']=="C")
									{
										$rss[$ct]=$rss[$ct]+2;
									}
									else
									{
										if($vss['option']=="D")
										{
											$rss[$ct]=$rss[$ct]+1;
										}
									}
								}
							}
							$ctt=$ctt+1;
						}
					}
				}
				if($ctt>0)
				$rss[$ct]=number_format((($rss[$ct]/(4*$ctt))*100),2);
				$ct=$ct+1;
			}
			$this->viewAssignEscaped ( 'results', $rowArray );
			$this->viewAssignEscaped ( 'rss', $rss );
		}
		$this->view->assign ( 'criteria', $criteria );
		$this->viewAssignEscaped ( 'locations', Location::getAll() );
		$qualificationsArray = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false );
		$this->viewAssignEscaped ( 'qualifications', $qualificationsArray );
		$rowArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $rowArray as $key => $val ) {
			if ($val ['id'] != 0)
			$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );
	}
	public function profAction() {
		if (! $this->hasACL ( 'view_people' ) and ! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		$criteria = array ();
		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);
		$criteria ['facilityInput'] = $this->getSanParam ( 'facilityInput' );
		$criteria ['training_title_option_id'] = $this->getSanParam ( 'training_title_option_id' );
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['ques'] = $this->getSanParam ( 'ques' );
		$criteria ['go'] = $this->getSanParam ( 'go' );
		$criteria ['all'] = $this->getSanParam ( 'all' );
		if ($criteria ['go']) {
			if ($criteria ['all']) {
				$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
				$num_locs = $this->setting('num_location_tiers');
				list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id);
				$sql = 'select DISTINCT cmp.person, cmp.question, cmp.option from person as p, person_qualification_option as q, facility as f, ('.$location_sub_query.') as l, comp as cmp, compres as cmpr';
				if ( $criteria['training_title_option_id'] ) {
					$sql .= ', person_to_training as ptt ';
					$sql .= ', training as tr  ';
				}
				$where = array('p.is_deleted = 0');
				$where []= 'cmpr.person = p.id';
				$where []= 'cmp.person = p.id';
				$where []= ' p.primary_qualification_option_id = q.id and p.facility_id = f.id and f.location_id = l.id ';
				if ($criteria ['facilityInput']) {
					$where []= ' p.facility_id = "' . $criteria ['facilityInput'] . '"';
				}
				if ( $criteria['training_title_option_id'] ) {
					$where []= ' p.id = ptt.person_id AND ptt.training_id = tr.id AND tr.training_title_option_id = ' . ($criteria ['training_title_option_id']) . ' ';
				}
				$where []= ' primary_qualification_option_id IN (SELECT id FROM person_qualification_option WHERE parent_id IN (6, 7, 8, 9) ) ';
				$where []= 'cmpr.active = \'Y\'';
				$where []= 'cmpr.res = 1';
				$where []= 'cmp.active = \'Y\'';
				$sql .= ' WHERE ' . implode(' AND ', $where);
				$rowArray = $db->fetchAll ( $sql );
				$qss=array();
				$nmss=array();
				$qss=split(",","0,1,2,3,4,5,6,7");
				$nmss=split("~","1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,200~01,02,03,04,05,06,07,08,09~31,32,33,34,35,36,37,38~41,42,43,44,45~51,52,53,54,55,56,57,58,59,510,511,512,513,514,515,516,517,518~61,62,63,64,65,66,67~71,72,73,74,75,76,77,78,79,710,711~21,22,23");
				$ct;
				$ct=0;
				$rssA=array();
				$rssB=array();
				$rssC=array();
				$rssD=array();
				$rssE=array();
				$ctt;
				foreach ( $qss as $kys => $vls ) {
					$rssA[$ct]=0;
					$rssB[$ct]=0;
					$rssC[$ct]=0;
					$rssD[$ct]=0;
					$rssE[$ct]=0;
					$ctt=0;
					$wss=split(",",$nmss[$vls]);
					foreach ( $wss as $kyss => $vlss ) {
						foreach ( $rowArray as $kss => $vss ) {
							if($vlss." " == $vss['question']." ")
							{
								if($vss['option']=="A")
								{
									$rssA[$ct]=$rssA[$ct]+1;
								}
								else
								{
									if($vss['option']=="B")
									{
										$rssB[$ct]=$rssB[$ct]+1;
									}
									else
									{
										if($vss['option']=="C")
										{
											$rssC[$ct]=$rssC[$ct]+1;
										}
										else
										{
											if($vss['option']=="D")
											{
												$rssD[$ct]=$rssD[$ct]+1;
											}
											else
											{
												if($vss['option']=="E")
												{
													$rssE[$ct]=$rssE[$ct]+1;
												}
											}
										}
									}
								}
								$ctt=$ctt+1;
							}
						}
					}
					if($ctt>0) {
						$rssA[$ct]=number_format((($rssA[$ct]/$ctt)*100),2);
						$rssB[$ct]=number_format((($rssB[$ct]/$ctt)*100),2);
						$rssC[$ct]=number_format((($rssC[$ct]/$ctt)*100),2);
						$rssD[$ct]=number_format((($rssD[$ct]/$ctt)*100),2);
						$rssE[$ct]=number_format((($rssE[$ct]/$ctt)*100),2);
					}
					$ct=$ct+1;
				}
				$this->viewAssignEscaped ( 'results', $rowArray );
				$this->viewAssignEscaped ( 'rssA', $rssA );
				$this->viewAssignEscaped ( 'rssB', $rssB );
				$this->viewAssignEscaped ( 'rssC', $rssC );
				$this->viewAssignEscaped ( 'rssD', $rssD );
				$this->viewAssignEscaped ( 'rssE', $rssE );
			}
			else
			{
				$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
				$num_locs = $this->setting('num_location_tiers');
				list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id);
				$sql = 'select DISTINCT cmp.person, cmp.question, cmp.option from person as p, person_qualification_option as q, facility as f, ('.$location_sub_query.') as l, comp as cmp, compres as cmpr';
				if ( $criteria['training_title_option_id'] ) {
					$sql .= ', person_to_training as ptt ';
					$sql .= ', training as tr  ';
				}
				$where = array('p.is_deleted = 0');
				$whr = array();
				$where []= 'cmpr.person = p.id';
				$where []= 'cmp.person = p.id';
				$where []= ' p.primary_qualification_option_id = q.id and p.facility_id = f.id and f.location_id = l.id ';
				if ($criteria ['facilityInput']) {
					$where []= ' p.facility_id = "' . $criteria ['facilityInput'] . '"';
				}
				if ( $criteria['training_title_option_id'] ) {
					$where []= ' p.id = ptt.person_id AND ptt.training_id = tr.id AND tr.training_title_option_id = ' . ($criteria ['training_title_option_id']) . ' ';
				}
				$where []= ' primary_qualification_option_id IN (SELECT id FROM person_qualification_option WHERE parent_id = ' . $criteria ['qualification_id'] . ') ';
				$where []= 'cmpr.active = \'Y\'';
				$where []= 'cmpr.res = 1';
				$where []= 'cmp.active = \'Y\'';
				if($criteria ['qualification_id']=="6")
				{
					$whr []= 'cmp.question IN ('."'".str_replace(",","','",$this->getSanParam ( 'listcq' ))."'".')';
				}
				if($criteria ['qualification_id']=="7")
				{
					$qs=split(",",$this->getSanParam ( 'ques' ));
					$nms=split("~",$this->getSanParam ( 'listdq' ));
					foreach ( $qs as $kys => $vls ) {
						$whr []= 'cmp.question IN ('."'".str_replace(",","','",$nms[$vls])."'".')';
					}
				}
				if($criteria ['qualification_id']=="8")
				{
					$qs=split(",",$this->getSanParam ( 'ques' ));
					$nms=split("~",$this->getSanParam ( 'listnq' ));
					foreach ( $qs as $kys => $vls ) {
						$whr []= 'cmp.question IN ('."'".str_replace(",","','",$nms[$vls])."'".')';
					}
				}
				if($criteria ['qualification_id']=="9")
				{
					$whr []= 'cmp.question IN ('."'".str_replace(",","','",$this->getSanParam ( 'listpq' ))."'".')';
				}
				$sql .= ' WHERE ' . implode(' AND ', $where);
				$sql .= ' AND (' . implode(' OR ', $whr) . ')';
				$rowArray = $db->fetchAll ( $sql );
				$qss=array();
				$nmss=array();
				if($criteria ['qualification_id']=="6")
				{
					$qss=split(",",$this->getSanParam ( 'ques' ));
					$nmss=split("~",$this->getSanParam ( 'listcq' ));
				}
				if($criteria ['qualification_id']=="7")
				{
					$qss=split(",",$this->getSanParam ( 'ques' ));
					$nmss=split("~",$this->getSanParam ( 'listdq' ));
				}
				if($criteria ['qualification_id']=="8")
				{
					$qss=split(",",$this->getSanParam ( 'ques' ));
					$nmss=split("~",$this->getSanParam ( 'listnq' ));
				}
				if($criteria ['qualification_id']=="9")
				{
					$qss=split(",",$this->getSanParam ( 'ques' ));
					$nmss=split("~",$this->getSanParam ( 'listpq' ));
				}
				$ct;
				$ct=0;
				$rssA=array();
				$rssB=array();
				$rssC=array();
				$rssD=array();
				$rssE=array();
				$ctt;
				foreach ( $qss as $kys => $vls ) {
					$rssA[$ct]=0;
					$rssB[$ct]=0;
					$rssC[$ct]=0;
					$rssD[$ct]=0;
					$rssE[$ct]=0;
					$ctt=0;
					$wss=split(",",$nmss[$vls]);
					foreach ( $wss as $kyss => $vlss ) {
						foreach ( $rowArray as $kss => $vss ) {
							if($vlss." " == $vss['question']." ")
							{
								if($vss['option']=="A")
								{
									$rssA[$ct]=$rssA[$ct]+1;
								}
								else
								{
									if($vss['option']=="B")
									{
										$rssB[$ct]=$rssB[$ct]+1;
									}
									else
									{
										if($vss['option']=="C")
										{
											$rssC[$ct]=$rssC[$ct]+1;
										}
										else
										{
											if($vss['option']=="D")
											{
												$rssD[$ct]=$rssD[$ct]+1;
											}
											else
											{
												if($vss['option']=="E")
												{
													$rssE[$ct]=$rssE[$ct]+1;
												}
											}
										}
									}
								}
								$ctt=$ctt+1;
							}
						}
					}
					if($ctt>0) {
						$rssA[$ct]=number_format((($rssA[$ct]/$ctt)*100),2);
						$rssB[$ct]=number_format((($rssB[$ct]/$ctt)*100),2);
						$rssC[$ct]=number_format((($rssC[$ct]/$ctt)*100),2);
						$rssD[$ct]=number_format((($rssD[$ct]/$ctt)*100),2);
						$rssE[$ct]=number_format((($rssE[$ct]/$ctt)*100),2);
					}
					$ct=$ct+1;
				}
				$this->viewAssignEscaped ( 'results', $rowArray );
				$this->viewAssignEscaped ( 'rssA', $rssA );
				$this->viewAssignEscaped ( 'rssB', $rssB );
				$this->viewAssignEscaped ( 'rssC', $rssC );
				$this->viewAssignEscaped ( 'rssD', $rssD );
				$this->viewAssignEscaped ( 'rssE', $rssE );
			}
		}
		$this->view->assign ( 'criteria', $criteria );
		$this->viewAssignEscaped ( 'locations', Location::getAll() );
		require_once ('models/table/TrainingTitleOption.php');
		$titleArray = TrainingTitleOption::suggestionList ( false, 10000 );
		$this->viewAssignEscaped ( 'courses', $titleArray );
		$qualificationsArray = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false );
		$this->viewAssignEscaped ( 'qualifications', $qualificationsArray );
		$rowArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $rowArray as $key => $val ) {
			if ($val ['id'] != 0)
			$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );
	}
	public function compcompAction() {
		if (! $this->hasACL ( 'view_people' ) and ! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		$criteria = array ();
		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);
		$criteria ['facilityInput'] = $this->getSanParam ( 'facilityInput' );
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['Questions'] = $this->getSanParam ( 'Questions' );
		$criteria ['outputType'] = $this->getSanParam ( 'outputType' );
		$criteria ['go'] = $this->getSanParam ( 'go' );
		if ($criteria ['go']) {
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$prsns=array();
			$prsnscnt=0;
			if($criteria ['qualification_id']=="6")
			{
				$sql='SELECT `person`, SUM(-(ASCII(`option`)-69)) `sm` FROM `comp`';
				$whr = array();
				$whr []= '`question` IN ('."'".str_replace(",","','",$this->getSanParam ( 'listcq' ))."'".')';
				$sql .= ' WHERE `active` = \'Y\' AND `option` <> \'E\' AND `option` <> \'F\' AND (' . implode(' OR ', $whr) . ')';
				$sql .= ' GROUP BY `person`';
				$rowArray = $db->fetchAll ( $sql );
				$tlques=split(",",$this->getSanParam ( 'listcq' ));
				$ttlques=count($tlques);
				$qs=split('\$',$this->getSanParam ( 'Questions' ));
				foreach ( $qs as $kys => $vls ) {
					$fr=split('\^',$vls);
					$min=0;
					$max=0;
					if($fr[2]=="100")
					{
						$min=90;
						$max=100;
					}
					else
					{
						if($fr[2]=="89")
						{
							$min=75;
							$max=90;
						}
						else
						{
							if($fr[2]=="74")
							{
								$min=60;
								$max=75;
							}
							else
							{
								$min=1;
								$max=60;
							}
						}
					}
					foreach ( $rowArray as $prsn => $mrk ) {
						$prcnt=number_format((($mrk['sm']/(4*$ttlques))*100),2);
						if($prcnt>$min && $prcnt<=$max)
						{
							$prsns[$prsnscnt]=$mrk['person'];
							$prsnscnt=$prsnscnt+1;
						}
					}
				}
			}
			if($criteria ['qualification_id']=="7")
			{
				$qs=split('\$',$this->getSanParam ( 'Questions' ));
				$nms=split("~",$this->getSanParam ( 'listdq' ));
				foreach ( $qs as $kys => $vls ) {
					$sql='SELECT `person`, SUM(-(ASCII(`option`)-69)) `sm` FROM `comp`';
					$whr = array();
					$fr=split('\^',$vls);
					$whr []= '`question` IN ('."'".str_replace(",","','",$nms[$fr[1]])."'".')';
					$sql .= ' WHERE `active` = \'Y\' AND `option` <> \'E\' AND `option` <> \'F\' AND (' . implode(' OR ', $whr) . ')';
					$sql .= ' GROUP BY `person`';
					$rowArray = $db->fetchAll ( $sql );
					$tlques=split(",",$nms[$fr[1]]);
					$ttlques=count($tlques);
					$min=0;
					$max=0;
					if($fr[2]=="100")
					{
						$min=90;
						$max=100;
					}
					else
					{
						if($fr[2]=="89")
						{
							$min=75;
							$max=90;
						}
						else
						{
							if($fr[2]=="74")
							{
								$min=60;
								$max=75;
							}
							else
							{
								$min=1;
								$max=60;
							}
						}
					}
					foreach ( $rowArray as $prsn => $mrk ) {
						$prcnt=number_format((($mrk['sm']/(4*$ttlques))*100),2);
						if($prcnt>$min && $prcnt<=$max)
						{
							$prsns[$prsnscnt]=$mrk['person'];
							$prsnscnt=$prsnscnt+1;
						}
					}
				}
			}
			if($criteria ['qualification_id']=="8")
			{
				$qs=split('\$',$this->getSanParam ( 'Questions' ));
				$nms=split("~",$this->getSanParam ( 'listnq' ));
				foreach ( $qs as $kys => $vls ) {
					$sql='SELECT `person`, SUM(-(ASCII(`option`)-69)) `sm` FROM `comp`';
					$whr = array();
					$fr=split('\^',$vls);
					$whr []= '`question` IN ('."'".str_replace(",","','",$nms[$fr[1]])."'".')';
					$sql .= ' WHERE `active` = \'Y\' AND `option` <> \'E\' AND `option` <> \'F\' AND (' . implode(' OR ', $whr) . ')';
					$sql .= ' GROUP BY `person`';
					$rowArray = $db->fetchAll ( $sql );
					$tlques=split(",",$nms[$fr[1]]);
					$ttlques=count($tlques);
					$min=0;
					$max=0;
					if($fr[2]=="100")
					{
						$min=90;
						$max=100;
					}
					else
					{
						if($fr[2]=="89")
						{
							$min=75;
							$max=90;
						}
						else
						{
							if($fr[2]=="74")
							{
								$min=60;
								$max=75;
							}
							else
							{
								$min=1;
								$max=60;
							}
						}
					}
					foreach ( $rowArray as $prsn => $mrk ) {
						$prcnt=number_format((($mrk['sm']/(4*$ttlques))*100),2);
						if($prcnt>$min && $prcnt<=$max)
						{
							$prsns[$prsnscnt]=$mrk['person'];
							$prsnscnt=$prsnscnt+1;
						}
					}
				}
			}
			if($criteria ['qualification_id']=="9")
			{
				$sql='SELECT `person`, SUM(-(ASCII(`option`)-69)) `sm` FROM `comp`';
				$whr = array();
				$whr []= '`question` IN ('."'".str_replace(",","','",$this->getSanParam ( 'listpq' ))."'".')';
				$sql .= ' WHERE `active` = \'Y\' AND `option` <> \'E\' AND `option` <> \'F\' AND (' . implode(' OR ', $whr) . ')';
				$sql .= ' GROUP BY `person`';
				$rowArray = $db->fetchAll ( $sql );
				$tlques=split(",",$this->getSanParam ( 'listpq' ));
				$ttlques=count($tlques);
				$qs=split('\$',$this->getSanParam ( 'Questions' ));
				foreach ( $qs as $kys => $vls ) {
					$fr=split('\^',$vls);
					$min=0;
					$max=0;
					if($fr[2]=="100")
					{
						$min=90;
						$max=100;
					}
					else
					{
						if($fr[2]=="89")
						{
							$min=75;
							$max=90;
						}
						else
						{
							if($fr[2]=="74")
							{
								$min=60;
								$max=75;
							}
							else
							{
								$min=1;
								$max=60;
							}
						}
					}
					foreach ( $rowArray as $prsn => $mrk ) {
						$prcnt=number_format((($mrk['sm']/(4*$ttlques))*100),2);
						if($prcnt>$min && $prcnt<=$max)
						{
							$prsns[$prsnscnt]=$mrk['person'];
							$prsnscnt=$prsnscnt+1;
						}
					}
				}
			}
			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id);
			$sql = 'SELECT  DISTINCT p.`id`, p.`first_name` ,  p.`last_name` ,  p.`gender` FROM `person` as p, facility as f, ('.$location_sub_query.') as l, `person_qualification_option` as q WHERE p.`primary_qualification_option_id` = q.`id` and p.facility_id = f.id and f.location_id = l.id AND p.`primary_qualification_option_id` IN (SELECT `id` FROM `person_qualification_option` WHERE `parent_id` = ' . $criteria ['qualification_id'] . ') AND p.`is_deleted` = 0 AND p.`id` IN (';
			if(count($prsns)>0)
			{
				foreach ( $prsns as $k => $v ) {
					$sql = $sql . $v . ',';
				}
			}
			$sql = $sql . '0';
			if ($criteria ['facilityInput']) {
				$sql = $sql . ') AND p.facility_id = "' . $criteria ['facilityInput'] . '";';
			}
			else {
				$sql = $sql . ');';
			}
			$rowArray = $db->fetchAll ( $sql );
			if ($criteria ['outputType']) {
				$this->sendData ( $this->reportHeaders ( false, $rowArray ) );
			}
			$this->viewAssignEscaped ( 'results', $rowArray );
		}
		$this->view->assign ( 'criteria', $criteria );
		$this->viewAssignEscaped ( 'locations', Location::getAll() );
		$qualificationsArray = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false );
		$this->viewAssignEscaped ( 'qualifications', $qualificationsArray );
		$rowArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $rowArray as $key => $val ) {
			if ($val ['id'] != 0)
			$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );
	}
	public function quescompAction() {
		if (! $this->hasACL ( 'view_people' ) and ! $this->hasACL ( 'edit_people' )) {
			$this->doNoAccessError ();
		}
		$criteria = array ();
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['Questions'] = $this->getSanParam ( 'Questions' );
		$criteria ['outputType'] = $this->getSanParam ( 'outputType' );
		$criteria ['go'] = $this->getSanParam ( 'go' );
		if ($criteria ['go']) {
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$qs=split('\$',$criteria ['Questions']);
			$sql='SELECT `person` FROM `comp`';
			$sql .= ' WHERE `active` = \'Y\'';
			$whr = array();
			foreach ( $qs as $k => $v ) {
				$qss=split('\^',$v);
				$whr[]='(`question`=\''.$qss[2].'\' AND `option`=\''.$qss[3].'\')';
			}
			$sql .= ' AND (' . implode(' OR ', $whr) . ')';
			$rowArray = $db->fetchAll ( $sql );
			$sql = 'SELECT  DISTINCT p.`id`, p.`first_name` ,  p.`last_name` ,  p.`gender` FROM `person` as p, `person_qualification_option` as q WHERE p.`primary_qualification_option_id` = q.`id` AND p.`primary_qualification_option_id` IN (SELECT `id` FROM `person_qualification_option` WHERE `parent_id` = ' . $criteria ['qualification_id'] . ') AND p.`is_deleted` = 0 AND p.`id` IN (';
			foreach ( $rowArray as $k => $v ) {
				$sql = $sql . $v['person'] . ',';
			}
			$sql = $sql . '0);';
			$rowArray = $db->fetchAll ( $sql );
			if ($criteria ['outputType']) {
				$this->sendData ( $this->reportHeaders ( false, $rowArray ) );
			}
			$this->viewAssignEscaped ( 'results', $rowArray );
		}
		$this->view->assign ( 'criteria', $criteria );
		$qualificationsArray = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false );
		$this->viewAssignEscaped ( 'qualifications', $qualificationsArray );
	}

	public function trainingsAction() {
		$this->view->assign ( 'mode', 'id' );

		return $this->trainingReport ();
	}

	public function trainingSearchAction() {
		$this->_countrySettings = array();
		$this->_countrySettings = System::getAll();

		$this->_countrySettings['num_location_tiers'] = 2 + $this->_countrySettings['display_region_c'] + $this->_countrySettings['display_region_b']; //including city
		$this->view->assign ( 'mode', 'search' );

		return $this->trainingReport ();
	}

	public function trainingbyParticipantsAction() {
		$this->view->assign ( 'mode', 'count' );

		return $this->trainingReport ();
	}

	public function trainingReport() {
		$this->_countrySettings = array();
		$this->_countrySettings = System::getAll();

		$this->_countrySettings['num_location_tiers'] = 2 + $this->_countrySettings['display_region_c'] + $this->_countrySettings['display_region_b']; //including city

		$display_training_partner = false;
		if ( isset($this->_countrySettings['display_training_partner']) && $this->_countrySettings['display_training_partner'] == 1 )
		$display_training_partner = true;

		require_once ('models/table/TrainingLocation.php');

		$criteria = array ();

		//find the first date in the database
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT MIN(training_start_date) as \"start\" FROM training WHERE is_deleted = 0";
		$rowArray = $db->fetchAll ( $sql );
		$start_default = $rowArray [0] ['start'];
		$parts = explode ( '-', $start_default );
		$criteria ['start-year'] = @$parts [0];
		$criteria ['start-month'] = @$parts [1];
		$criteria ['start-day'] = @$parts [2];

		if ($this->getSanParam ( 'start-year' ))
		$criteria ['start-year'] = $this->getSanParam ( 'start-year' );
		if ($this->getSanParam ( 'start-month' ))
		$criteria ['start-month'] = $this->getSanParam ( 'start-month' );
		if ($this->getSanParam ( 'start-day' ))
		$criteria ['start-day'] = $this->getSanParam ( 'start-day' );
		if ($this->view->mode == 'search') {
			$sql = "SELECT MAX(training_start_date) as \"start\" FROM training ";
			$rowArray = $db->fetchAll ( $sql );
			$end_default = $rowArray [0] ['start'];
			$parts = explode ( '-', $end_default );
			$criteria ['end-year'] = @$parts [0];
			$criteria ['end-month'] = @$parts [1];
			$criteria ['end-day'] = @$parts [2];
		} else {
			$criteria ['end-year'] = date ( 'Y' );
			$criteria ['end-month'] = date ( 'm' );
			$criteria ['end-day'] = date ( 'd' );
		}

		if ($this->getSanParam ( 'end-year' ))
		$criteria ['end-year'] = $this->getSanParam ( 'end-year' );
		if ($this->getSanParam ( 'end-month' ))
		$criteria ['end-month'] = $this->getSanParam ( 'end-month' );
		if ($this->getSanParam ( 'end-day' ))
		$criteria ['end-day'] = $this->getSanParam ( 'end-day' );

		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);

		//	$criteria['training_title_option_id'] = $this->getSanParam('training_title_option_id'); // legacy


		// find training name from new category/title format: categoryid_titleid
		$ct_ids = $criteria ['training_category_and_title_id'] = $this->getSanParam ( 'training_category_and_title_id' );
		$criteria ['training_title_option_id'] = substr ( $ct_ids, strpos ( $ct_ids, '_' ) + 1 );

		$criteria ['training_location_id'] = $this->getSanParam ( 'training_location_id' );
		$criteria ['training_organizer_id'] = $this->getSanParam ( 'training_organizer_id' );
		//$criteria['training_organizer_option_id'] = $this->getSanParam('training_organizer_option_id');
		$criteria ['training_pepfar_id'] = $this->getSanParam ( 'training_pepfar_id' );
		$criteria ['training_method_id'] = $this->getSanParam ( 'training_method_id' );
		$criteria ['mechanism_id'] = $this->getSanParam ( 'mechanism_id' );
		$criteria ['training_topic_id'] = $this->getSanParam ( 'training_topic_id' );
		$criteria ['training_level_id'] = $this->getSanParam ( 'training_level_id' );
		$criteria ['training_primary_language_option_id'] = $this->getSanParam ( 'training_primary_language_option_id' );
		$criteria ['training_secondary_language_option_id'] = $this->getSanParam ( 'training_secondary_language_option_id' );
		$criteria ['training_category_id'] = $this->getSanParam ( 'training_category_id' ); //reset(explode('_',$ct_ids));//
		$criteria ['training_got_curric_id'] = $this->getSanParam ( 'training_got_curric_id' );
		$criteria ['is_tot'] = $this->getSanParam ( 'is_tot' );
		$criteria ['funding_id'] = $this->getSanParam ( 'funding_id' );
		$criteria ['custom_1_id'] = $this->getSanParam ( 'custom_1_id' );

		$criteria ['funding_min'] = $this->getSanParam ( 'funding_min' );
		$criteria ['funding_max'] = $this->getSanParam ( 'funding_max' );

		$criteria ['go'] = $this->getSanParam ( 'go' );
		$criteria ['doCount'] = ($this->view->mode == 'count');
		$criteria ['showProvince'] = ($this->getSanParam ( 'showProvince' ) or ($criteria ['doCount'] and ($criteria ['province_id'] or ! empty ( $criteria ['province_id'] ))));
		$criteria ['showDistrict'] = ($this->getSanParam ( 'showDistrict' ) or ($criteria ['doCount'] and ($criteria ['district_id'] or ! empty ( $criteria ['district_id'] ))));
		$criteria ['showRegionC'] = ($this->getSanParam ( 'showRegionC' ) or ($criteria ['doCount'] and ($criteria ['region_c_id'] or ! empty ( $criteria ['region_c_id'] ))));
		$criteria ['showTrainingTitle'] = ($this->getSanParam ( 'showTrainingTitle' ) or ($criteria ['doCount'] and ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0')));
		$criteria ['showLocation'] = ($this->getSanParam ( 'showLocation' ) or ($criteria ['doCount'] and $criteria ['training_location_id']));
		$criteria ['showOrganizer'] = ($this->getSanParam ( 'showOrganizer' ) or ($criteria ['doCount'] and ($criteria ['training_organizer_id'])));
		$criteria ['showMechanism'] = ($this->getSanParam ( 'showMechanism' ) or ($criteria ['doCount'] and $criteria ['mechanism_id']));

		if ($_SERVER['REMOTE_ADDR'] == "24.18.118.14"){
			#	var_dump ($criteria);
			#	if ($this->getSanParam ( 'showPepfar' ));
			#	var_dump ($criteria ['doCount']);
			#	var_dump ($criteria ['training_pepfar_id']);
			#	var_dump ($criteria ['training_pepfar_id']);
		}

		$criteria ['showPepfar'] = ($this->getSanParam ( 'showPepfar' ) or ($criteria ['doCount'] and ($criteria ['training_pepfar_id'] or $criteria ['training_pepfar_id'] === '0')));
		$criteria ['showMethod'] = ($this->getSanParam ( 'showMethod' ) or ($criteria ['doCount'] and ($criteria ['training_method_id'] or $criteria ['training_method_id'] === '0')));
		$criteria ['showTopic'] = ($this->getSanParam ( 'showTopic' ) or ($criteria ['doCount'] and ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0')));
		$criteria ['showLevel'] = ($this->getSanParam ( 'showLevel' ) or ($criteria ['doCount'] and $criteria ['training_level_id']));
		$criteria ['showTot'] = ($this->getSanParam ( 'showTot' ) or ($criteria ['doCount'] and $criteria ['is_tot'] or $criteria ['is_tot'] === '0'));
		$criteria ['showRefresher'] = ($this->getSanParam ( 'showRefresher' ));
		$criteria ['showGotComment'] = ($this->getSanParam ( 'showGotComment' ));
		$criteria ['showPrimaryLanguage'] = ($this->getSanParam ( 'showPrimaryLanguage' ) or ($criteria ['doCount'] and $criteria ['training_primary_language_option_id'] or $criteria ['training_primary_language_option_id'] === '0'));
		$criteria ['showSecondaryLanguage'] = ($this->getSanParam ( 'showSecondaryLanguage' ) or ($criteria ['doCount'] and $criteria ['training_secondary_language_option_id'] or $criteria ['training_secondary_language_option_id'] === '0'));
		$criteria ['showFunding'] = ($this->getSanParam ( 'showFunding' ) or ($criteria ['doCount'] and $criteria ['funding_id'] or $criteria ['funding_id'] === '0' or $criteria ['funding_min'] or $criteria ['funding_max']));
		$criteria ['showCategory'] = ($this->getSanParam ( 'showCategory' ) or ($criteria ['doCount'] and $criteria ['training_category_id'] or $criteria ['training_category_id'] === '0'));
		$criteria ['showGotCurric'] = ($this->getSanParam ( 'showGotCurric' ) or ($criteria ['doCount'] and $criteria ['training_got_curric_id'] or $criteria ['training_got_curric_id'] === '0'));
		$criteria ['showCustom1'] = ($this->getSanParam ( 'showCustom1' ));
		$criteria ['showEndDate'] = ($this->getSanParam('showEndDate'));

		$criteria ['showRespPrim'] = ($this->getSanParam ( 'showRespPrim' ));
		$criteria ['showRespSecond'] = ($this->getSanParam ( 'showRespSecond' ));
		$criteria ['showHighestEd'] = ($this->getSanParam ( 'showHighestEd' ));
		//$criteria ['showReason'] = ($this->getSanParam ( 'showReason' ));

		$criteria ['primary_responsibility_option_id'] = $this->getSanParam ( 'primary_responsibility_option_id' );
		$criteria ['secondary_responsibility_option_id'] = $this->getSanParam ( 'secondary_responsibility_option_id' );
		$criteria ['highest_edu_level_option_id'] = $this->getSanParam ( 'highest_edu_level_option_id' );
		//$criteria ['attend_reason_option_id'] = $this->getSanParam ( 'attend_reason_option_id' );

		$criteria ['training_participants_type'] = $this->getSanParam ( 'training_participants_type' );

		// defaults
		if (! $criteria ['go']) {
			$criteria ['showTrainingTitle'] = 1;
		}

		if ($criteria ['go']) {

			$sql = 'SELECT ';

			if ($criteria ['doCount']) {
				$sql .= ' COUNT(pt.person_id) as "cnt" ';
			} else {
				$sql .= ' DISTINCT pt.id as "id", ptc.pcnt, pt.training_start_date, pt.training_end_date, pt.has_known_participants  ';
			}

			if ($criteria ['showTrainingTitle']) {
				$sql .= ', training_title ';
			}

			if ($criteria ['showRegionC']) {
				$sql .= ', pt.region_c_name ';
			}
			if ($criteria ['showDistrict']) {
				$sql .= ', pt.district_name ';
			}
			if ($criteria ['showProvince']) {
				$sql .= ', pt.province_name ';
			}

			if ($criteria ['showLocation']) {
				$sql .= ', pt.training_location_name ';
			}

			if ($criteria ['showOrganizer']) {
				$sql .= ', torg.training_organizer_phrase ';
			}

			if ($criteria ['showMechanism'] && $display_training_partner) {
				$sql .= ', torg.id, organizer_partners.mechanism_id ';
			}

			if ($criteria ['showLevel']) {
				$sql .= ', tlev.training_level_phrase ';
			}

			if ($criteria ['showCategory']) {
				$sql .= ', tcat.training_category_phrase ';
			}

			if ($criteria ['showPepfar'] || $criteria ['training_pepfar_id'] || $criteria ['training_pepfar_id'] === '0') {
				if ($criteria ['doCount']) {
					$sql .= ', tpep.pepfar_category_phrase ';
				} else {
					$sql .= ', GROUP_CONCAT(DISTINCT tpep.pepfar_category_phrase) as "pepfar_category_phrase" ';
				}
			}

			if ($criteria ['showMethod']) {
				$sql .= ', tmeth.training_method_phrase ';
			}

			if ($criteria ['showTopic']) {
				if ($criteria ['doCount']) {
					$sql .= ', ttopic.training_topic_phrase ';
				} else {
					$sql .= ', GROUP_CONCAT(DISTINCT ttopic.training_topic_phrase ORDER BY training_topic_phrase) AS "training_topic_phrase" ';
				}
			}

			if ($criteria ['showTot']) {
				$sql .= ", IF(is_tot,'" . t ( 'Yes' ) . "','" . t ( 'No' ) . "') AS is_tot";
			}

			if ($criteria ['showRefresher']) {
				$sql .= ", IF(is_refresher,'" . t ( 'Yes' ) . "','" . t ( 'No' ) . "') AS is_refresher";
			}

			if ($criteria ['showSecondaryLanguage']) {
				$sql .= ', tlos.language_phrase as "secondary_language_phrase" ';
			}
			if ($criteria ['showPrimaryLanguage']) {
				$sql .= ', tlop.language_phrase as "primary_language_phrase" ';
			}
			if ($criteria ['showGotComment']) {
				$sql .= ", pt.got_comments";
			}
			if ($criteria ['showGotCurric']) {
				$sql .= ', tgotc.training_got_curriculum_phrase ';
			}

			if ($criteria ['showFunding']) {
				if ($criteria ['doCount']) {
					$sql .= ', tfund.funding_phrase ';
				} else {
					$sql .= ', GROUP_CONCAT(DISTINCT tfund.funding_phrase ORDER BY funding_phrase) as "funding_phrase" ';
				}
			}
			if ( $criteria['showCustom1'] ) {
				$sql .= ', tqc.custom1_phrase ';
			}

			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id, true);

			//if we're doing a participant count, then LEFT JOIN with the participants
			//otherwise just select the core training info


			if ($criteria ['doCount']) {
				$sql .= ' FROM (SELECT training.*, pers.person_id as "person_id", tto.training_title_phrase AS training_title, training_location.training_location_name, '.implode(',',$field_name).
				'       FROM training ' .
				'         LEFT JOIN training_title_option tto ON (`training`.training_title_option_id = tto.id)' .
				'         LEFT JOIN training_location ON training.training_location_id = training_location.id ' .
				'         LEFT JOIN ('.$location_sub_query.') as l ON training_location.location_id = l.id ' .
				'         LEFT JOIN (SELECT person_id,training_id FROM person JOIN person_to_training ON person_to_training.person_id = person.id) as pers ON training.id = pers.training_id WHERE training.is_deleted=0) as pt ';
			} else {
				$sql .= ' FROM (SELECT training.*, tto.training_title_phrase AS training_title,training_location.training_location_name, '.implode(',',$field_name).
				'       FROM training  ' .
				'         LEFT JOIN training_title_option tto ON (`training`.training_title_option_id = tto.id) ' .
				'         LEFT JOIN training_location ON training.training_location_id = training_location.id ' .
				'         LEFT JOIN ('.$location_sub_query.') as l ON training_location.location_id = l.id ' .
				'  WHERE training.is_deleted=0) as pt ';

				$sql .= ' LEFT JOIN (SELECT COUNT(id) as "pcnt",training_id FROM person_to_training GROUP BY training_id) as ptc ON ptc.training_id = pt.id ';
			}
			/*
			if ($criteria ['showDistrict'] || $criteria ['district_id']) {
			$sql .= '	LEFT JOIN location_district as tld ON pt.district_id = tld.id ';
			}
			if ($criteria ['showProvince'] or ! empty ( $criteria ['province_id'] )) {
			$sql .= '	JOIN location_province as tlp ON pt.province_id = tlp.id ';
			}
			*/
			if ($criteria ['showOrganizer'] or $criteria ['training_organizer_id'] || $criteria ['showMechanism']  || $criteria ['mechanism_id']) {
				$sql .= '	JOIN training_organizer_option as torg ON torg.id = pt.training_organizer_option_id ';

			}

			if ($criteria ['showMechanism']  || $criteria ['mechanism_id']  && $display_training_partner) {
				$sql .= ' LEFT JOIN organizer_partners ON organizer_partners.organizer_id = torg.id';
			}

			if ($criteria ['showLevel'] || $criteria ['training_level_id']) {
				$sql .= '	JOIN training_level_option as tlev ON tlev.id = pt.training_level_option_id ';
			}

			if ($criteria ['showMethod'] || $criteria ['training_method_id']) {
				$sql .= ' JOIN training_method_option as tmeth ON tmeth.id = pt.training_method_option_id ';
			}

			if ($criteria ['showPepfar'] || $criteria ['training_pepfar_id'] || $criteria ['training_pepfar_id'] === '0') {
				$sql .= '	LEFT JOIN (SELECT training_id, ttpco.training_pepfar_categories_option_id, pepfar_category_phrase FROM training_to_training_pepfar_categories_option as ttpco JOIN training_pepfar_categories_option as tpco ON ttpco.training_pepfar_categories_option_id = tpco.id) as tpep ON tpep.training_id = pt.id ';
			}

			if ($criteria ['showTopic'] || $criteria ['training_topic_id']) {
				$sql .= '	LEFT JOIN (SELECT training_id, ttto.training_topic_option_id, training_topic_phrase FROM training_to_training_topic_option as ttto JOIN training_topic_option as tto ON ttto.training_topic_option_id = tto.id) as ttopic ON ttopic.training_id = pt.id ';
			}

			if ($criteria ['showPrimaryLanguage'] || $criteria ['training_primary_language_option_id']) {
				$sql .= ' LEFT JOIN trainer_language_option as tlop ON tlop.id = pt.training_primary_language_option_id ';
			}

			if ($criteria ['showSecondaryLanguage'] || $criteria ['training_secondary_language_option_id']) {
				$sql .= ' LEFT JOIN trainer_language_option as tlos ON tlos.id = pt.training_secondary_language_option_id ';
			}

			if ($criteria ['showFunding'] || (intval ( $criteria ['funding_min'] ) or intval ( $criteria ['funding_max'] ))) {
				$sql .= '	LEFT JOIN (SELECT training_id, ttfo.training_funding_option_id, funding_phrase, ttfo.funding_amount FROM training_to_training_funding_option as ttfo JOIN training_funding_option as tfo ON ttfo.training_funding_option_id = tfo.id) as tfund ON tfund.training_id = pt.id ';
			}

			if ($criteria ['showGotCurric'] || $criteria ['training_got_curric_id']) {
				$sql .= '	LEFT JOIN training_got_curriculum_option as tgotc ON tgotc.id = pt.training_got_curriculum_option_id';
			}

			if ($criteria ['showCategory'] or ! empty ( $criteria ['training_category_id'] )) {
				$sql .= '
				LEFT JOIN training_category_option_to_training_title_option tcotto ON (tcotto.training_title_option_id = pt.training_title_option_id)
				LEFT JOIN training_category_option tcat ON (tcotto.training_category_option_id = tcat.id)
				';
			}
			if ( $criteria['showCustom1'] ) {
				$sql .= ' LEFT JOIN training_custom_1_option as tqc ON pt.training_custom_1_option_id = tqc.id  ';
			}

			$where = array ();
			$where [] = ' pt.is_deleted=0 ';

			if ($criteria ['training_participants_type']) {
				if ($criteria ['training_participants_type'] == 'has_known_participants') {
					$where [] = ' pt.has_known_participants = 1 ';
				}
				if ($criteria ['training_participants_type'] == 'has_unknown_participants') {
					$where [] = ' pt.has_known_participants = 0 ';

				}
			}

			if ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0') {
				$where [] = 'pt.training_title_option_id = ' . $criteria ['training_title_option_id'];
			}

			if ($criteria ['training_location_id']) {
				$where [] = ' pt.training_location_id = \'' . $criteria ['training_location_id'] . '\'';
			}

			if ($criteria ['training_organizer_id'] or $criteria ['training_organizer_id'] === '0') {
				$where [] = ' pt.training_organizer_option_id = \'' . $criteria ['training_organizer_id'] . '\'';
			}

			if ($criteria ['mechanism_id'] or $criteria ['mechanism_id'] === '0' && $display_training_partner) {
				$where [] = ' organizer_partners.mechanism_id = \'' . $criteria ['mechanism_id'] . '\'';
			}

			if ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0') {
				$where [] = ' ttopic.training_topic_option_id = \'' . $criteria ['training_topic_id'] . '\'';
			}

			if ($criteria ['training_level_id']) {
				$where [] = ' pt.training_level_option_id = \'' . $criteria ['training_level_id'] . '\'';
			}

			if ($criteria ['training_pepfar_id'] or $criteria ['training_pepfar_id'] === '0') {
				$where [] = ' tpep.training_pepfar_categories_option_id = \'' . $criteria ['training_pepfar_id'] . '\'';
			}

			if ($criteria ['training_method_id'] or $criteria ['training_method_id'] === '0') {
				$where [] = ' tmeth.id = \'' . $criteria ['training_method_id'] . '\'';
			}

			if ($criteria ['training_primary_language_option_id'] or $criteria ['training_primary_language_option_id'] === '0') {
				$where [] = ' pt.training_primary_language_option_id = \'' . $criteria ['training_primary_language_option_id'] . '\'';
			}

			if ($criteria ['training_secondary_language_option_id'] or $criteria ['training_secondary_language_option_id'] === '0') {
				$where [] = ' pt.training_secondary_language_option_id = \'' . $criteria ['training_secondary_language_option_id'] . '\'';
			}
			/*
			if ($criteria ['district_id'] or ! empty ( $criteria ['district_id'] )) {
			$where [] = ' pt.district_id IN (' . implode ( ',', $criteria ['district_id'] ) . ')';
			} else if ($criteria ['province_id'] or ! empty ( $criteria ['province_id'] )) {
			$where [] = ' pt.province_id IN (' . implode ( ',', $criteria ['province_id'] ) . ')';
			}
			*/
			if (intval ( $criteria ['end-year'] ) and $criteria ['start-year']) {
				$startDate = $criteria ['start-year'] . '-' . $criteria ['start-month'] . '-' . $criteria ['start-day'];
				$endDate = $criteria ['end-year'] . '-' . $criteria ['end-month'] . '-' . $criteria ['end-day'];
				$where [] = ' training_start_date >= \'' . $startDate . '\'  AND training_start_date <= \'' . $endDate . '\'  ';
			}

			if (intval ( $criteria ['funding_min'] ) or intval ( $criteria ['funding_max'] )) {
				if (intval ( $criteria ['funding_min'] ))
				$where [] = 'tfund.funding_amount >= \'' . $criteria ['funding_min'] . '\' ';
				if (intval ( $criteria ['funding_max'] ))
				$where [] = 'tfund.funding_amount <= \'' . $criteria ['funding_max'] . '\' ';
			}

			if (intval ( $criteria ['is_tot'] )) {
				$where [] = ' is_tot = ' . $criteria ['is_tot'];
			}

			if ($criteria ['funding_id'] or $criteria ['funding_id'] === '0') {
				$where [] = ' tfund.training_funding_option_id = \'' . $criteria ['funding_id'] . '\'';
			}

			if ($criteria ['training_category_id'] or $criteria ['training_category_id'] === '0') {
				$where [] = ' tcat.id = \'' . $criteria ['training_category_id'] . '\'';
			}

			if ($criteria ['training_got_curric_id'] or $criteria ['training_got_curric_id'] === '0') {
				$where [] = ' tgotc.id = \'' . $criteria ['training_got_curric_id'] . '\'';
			}

			if ($criteria ['custom_1_id'] or $criteria ['custom_1_id'] === '0') {
				$where [] = ' pt.training_custom_1_option_id = \'' . $criteria ['custom_1_id'] . '\'';
			}
			if ($where)
			$sql .= ' WHERE ' . implode ( ' AND ', $where );

			if ($criteria ['doCount']) {

				$groupBy = array();

				if ($criteria ['showTrainingTitle']) $groupBy []= 'pt.training_title_option_id';
				if ($criteria ['showProvince']) $groupBy []= ' pt.province_id';
				if ($criteria ['showDistrict']) $groupBy []=  '  pt.district_id';
				if ($criteria ['showRegionC']) $groupBy []=  '  pt.region_c_id';
				if ($criteria ['showLocation']) $groupBy []=  '  pt.training_location_id';
				if ($criteria ['showOrganizer']) $groupBy []=  '  pt.training_organizer_option_id';
				if ($criteria ['showMechanism'] && $display_training_partner) $groupBy []=  '  organizer_partners.mechanism_id';
				if ($criteria ['showCustom1']) $groupBy []=  '  pt.training_custom_1_option_id';
				if ($criteria ['showTopic']) $groupBy []=  '  ttopic.training_topic_option_id';
				if ($criteria ['showLevel']) $groupBy []=  '  pt.training_level_option_id';
				if ($criteria ['showPepfar']) $groupBy []=  '  tpep.training_pepfar_categories_option_id';
				if ($criteria ['showMethod']) $groupBy []=  '  tmeth.id';
				if ($criteria ['showTot']) $groupBy []=  '  pt.is_tot';
				if ($criteria ['showRefresher']) $groupBy []=  '  pt.is_refresher';
				if ($criteria ['showGotCurric']) $groupBy []=  '  pt.training_got_curriculum_option_id';
				if ($criteria ['showPrimaryLanguage']) $groupBy []=  '  pt.training_primary_language_option_id';
				if ($criteria ['showSecondaryLanguage']) $groupBy []=  '  pt.training_secondary_language_option_id';
				if ($criteria ['showFunding']) $groupBy []=  '  tfund.training_funding_option_id';

				if ($groupBy) {
					$sql .= ' GROUP BY ' . implode(',',$groupBy);
				}
			} else {
				//		if ($criteria ['showPepfar'] || $criteria ['showMethod'] || $criteria ['showTopic'] || $criteria ['showFunding'] || $criteria ['showCategory']) {
				$sql .= ' GROUP BY pt.id';
				//		}


			}

			if ($this->view->mode == 'search') {
				$sql .= ' ORDER BY training_start_date DESC';
			}

			$rowArray = $db->fetchAll ( $sql );

			if ($criteria ['doCount']) {
				$count = 0;
				foreach ( $rowArray as $row ) {
					$count += $row ['cnt'];
				}
			} else {
				$count = count ( $rowArray );
			}

			if ($this->_getParam ( 'outputType' ))
			$this->sendData ( $this->reportHeaders ( false, $rowArray ) );

		} else {
			$count = 0;
			$rowArray = array ();
		}

		$criteria ['go'] = $this->getSanParam ( 'go' );

		$this->viewAssignEscaped ( 'results', $rowArray );
		$this->view->assign ( 'count', $count );
		$this->view->assign ( 'criteria', $criteria );

		$locations = Location::getAll();
		$this->viewAssignEscaped('locations', $locations);
		//course
		$courseArray = TrainingTitleOption::suggestionList ( false, 10000 );
		$this->viewAssignEscaped ( 'courses', $courseArray );
		//location
		// location drop-down
		$tlocations = TrainingLocation::selectAllLocations ($this->setting('num_location_tiers'));
		$this->viewAssignEscaped ( 'tlocations', $tlocations );
		//organizers
		$organizersArray = OptionList::suggestionList ( 'training_organizer_option', 'training_organizer_phrase', false, false, false );
		$this->viewAssignEscaped ( 'organizers', $organizersArray );
		//topics
		$topicsArray = OptionList::suggestionList ( 'training_topic_option', 'training_topic_phrase', false, false, false );
		$this->viewAssignEscaped ( 'topics', $topicsArray );
		//levels
		$levelArray = OptionList::suggestionList ( 'training_level_option', 'training_level_phrase', false, false );
		$this->viewAssignEscaped ( 'levels', $levelArray );
		//pepfar
		$organizersArray = OptionList::suggestionList ( 'training_pepfar_categories_option', 'pepfar_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'pepfars', $organizersArray );
		//funding
		$fundingArray = OptionList::suggestionList ( 'training_funding_option', 'funding_phrase', false, false, false );
		$this->viewAssignEscaped ( 'funding', $fundingArray );
		//category
		$categoryArray = OptionList::suggestionList ( 'training_category_option', 'training_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'category', $categoryArray );
		//primary language
		$langArray = OptionList::suggestionList ( 'trainer_language_option', 'language_phrase', false, false, false );
		$this->viewAssignEscaped ( 'language', $langArray );
		//category
		$categoryArray = OptionList::suggestionList ( 'training_category_option', 'training_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'category', $categoryArray );
		//category+titles
		$categoryTitle = MultiAssignList::getOptions ( 'training_title_option', 'training_title_phrase', 'training_category_option_to_training_title_option', 'training_category_option' );
		$this->view->assign ( 'categoryTitle', $categoryTitle );
		//training methods
		$methodTitle = OptionList::suggestionList ( 'training_method_option', 'training_method_phrase', false, false, false );
		$this->view->assign ( 'methods', $methodTitle );

		$customArray = OptionList::suggestionList ( 'training_custom_1_option', 'custom1_phrase', false, false, false );
		$this->viewAssignEscaped ( 'custom1', $customArray );

		$qualsArray = OptionList::suggestionList ( 'person_primary_responsibility_option', 'responsibility_phrase', false, false, false );
		$this->viewAssignEscaped ( 'responsibility_primary', $qualsArray );

		$qualsArray = OptionList::suggestionList ( 'person_secondary_responsibility_option', 'responsibility_phrase', false, false, false );
		$this->viewAssignEscaped ( 'responsibility_secondary', $qualsArray );


		$qualsArray = OptionList::suggestionList ( 'person_attend_reason_option', 'attend_reason_phrase', false, false, false );
		$this->viewAssignEscaped ( 'attend_reason', $qualsArray );

		$qualsArray = OptionList::suggestionList ( 'person_education_level_option', 'education_level_phrase', false, false, false);
		$this->viewAssignEscaped ( 'highest_education_level', $qualsArray );

		//mechanisms (aka training partners, organizer_partners table)
		$mechanismArray = array();
		if($display_training_partner){
			$mechanismArray = OptionList::suggestionList ( 'organizer_partners', 'mechanism_id', false, false, false, "mechanism_id != ''");
		}
		$this->viewAssignEscaped ( 'mechanisms', $mechanismArray );

		// find category based on title
		$catId = NULL;
		if ($criteria ['training_category_id']) {
			foreach ( $categoryTitle as $r ) {
				if ($r ['id'] == $criteria ['training_category_id']) {
					$catId = $r ['training_category_option_id'];
					break;
				}
			}
		}
		$this->view->assign ( 'catId', $catId );

		//got curric
		$gotCuriccArray = OptionList::suggestionList ( 'training_got_curriculum_option', 'training_got_curriculum_phrase', false, false, false );
		$this->viewAssignEscaped ( 'gotcurric', $gotCuriccArray );

	}

	public function trainingUnknownAction() {

		require_once ('models/table/TrainingLocation.php');

		$this->view->assign('mode', 'unknown');

		$criteria = array ();

		//find the first date in the database
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT MIN(training_start_date) as \"start\" FROM training WHERE is_deleted = 0";
		$rowArray = $db->fetchAll ( $sql );
		$start_default = $rowArray [0] ['start'];
		$parts = explode ( '-', $start_default );
		$criteria ['start-year'] = @$parts [0];
		$criteria ['start-month'] = @$parts [1];
		$criteria ['start-day'] = @$parts [2];

		if ($this->getSanParam ( 'start-year' ))
		$criteria ['start-year'] = $this->getSanParam ( 'start-year' );
		if ($this->getSanParam ( 'start-month' ))
		$criteria ['start-month'] = $this->getSanParam ( 'start-month' );
		if ($this->getSanParam ( 'start-day' ))
		$criteria ['start-day'] = $this->getSanParam ( 'start-day' );
		if ($this->view->mode == 'search') {
			$sql = "SELECT MAX(training_start_date) as \"start\" FROM training ";
			$rowArray = $db->fetchAll ( $sql );
			$end_default = $rowArray [0] ['start'];
			$parts = explode ( '-', $end_default );
			$criteria ['end-year'] = @$parts [0];
			$criteria ['end-month'] = @$parts [1];
			$criteria ['end-day'] = @$parts [2];
		} else {
			$criteria ['end-year'] = date ( 'Y' );
			$criteria ['end-month'] = date ( 'm' );
			$criteria ['end-day'] = date ( 'd' );
		}

		if ($this->getSanParam ( 'end-year' ))
		$criteria ['end-year'] = $this->getSanParam ( 'end-year' );
		if ($this->getSanParam ( 'end-month' ))
		$criteria ['end-month'] = $this->getSanParam ( 'end-month' );
		if ($this->getSanParam ( 'end-day' ))
		$criteria ['end-day'] = $this->getSanParam ( 'end-day' );

		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);

		//  $criteria['training_title_option_id'] = $this->getSanParam('training_title_option_id'); // legacy


		// find training name from new category/title format: categoryid_titleid
		$ct_ids = $criteria ['training_category_and_title_id'] = $this->getSanParam ( 'training_category_and_title_id' );
		$criteria ['training_title_option_id'] = substr ( $ct_ids, strpos ( $ct_ids, '_' ) + 1 );

		$criteria ['training_location_id'] = $this->getSanParam ( 'training_location_id' );
		$criteria ['training_organizer_id'] = $this->getSanParam ( 'training_organizer_id' );
		//$criteria['training_organizer_option_id'] = $this->getSanParam('training_organizer_option_id');
		$criteria ['training_pepfar_id'] = $this->getSanParam ( 'training_pepfar_id' );
		$criteria ['training_method_id'] = $this->getSanParam ( 'training_method_id' );
		$criteria ['training_topic_id'] = $this->getSanParam ( 'training_topic_id' );
		$criteria ['training_level_id'] = $this->getSanParam ( 'training_level_id' );
		$criteria ['training_primary_language_option_id'] = $this->getSanParam ( 'training_primary_language_option_id' );
		$criteria ['training_secondary_language_option_id'] = $this->getSanParam ( 'training_secondary_language_option_id' );
		$criteria ['training_category_id'] = $this->getSanParam ( 'training_category_id' ); //reset(explode('_',$ct_ids));//
		$criteria ['training_got_curric_id'] = $this->getSanParam ( 'training_got_curric_id' );
		$criteria ['is_tot'] = $this->getSanParam ( 'is_tot' );
		$criteria ['funding_id'] = $this->getSanParam ( 'funding_id' );
		$criteria ['custom_1_id'] = $this->getSanParam ( 'custom_1_id' );
		$criteria ['qualification_option_id'] = $this->getSanParam ( 'qualification_option_id' );
		$criteria ['age_range_option_id'] = $this->getSanParam ( 'age_range_option_id' );
		$criteria ['gender_option_id'] = $this->getSanParam ( 'gender_option_id' );

		$criteria ['funding_min'] = $this->getSanParam ( 'funding_min' );
		$criteria ['funding_max'] = $this->getSanParam ( 'funding_max' );

		$criteria ['go'] = $this->getSanParam ( 'go' );
		$criteria ['doCount'] = ($this->view->mode == 'count');
		$criteria ['showProvince'] = ($this->getSanParam ( 'showProvince' ) or ($criteria ['doCount'] and ($criteria ['province_id'] or ! empty ( $criteria ['province_id'] ))));
		$criteria ['showDistrict'] = ($this->getSanParam ( 'showDistrict' ) or ($criteria ['doCount'] and ($criteria ['district_id'] or ! empty ( $criteria ['district_id'] ))));
		$criteria ['showRegionC'] = ($this->getSanParam ( 'showRegionC' ) or ($criteria ['doCount'] and ($criteria ['region_c_id'] or ! empty ( $criteria ['region_c_id'] ))));
		$criteria ['showTrainingTitle'] = ($this->getSanParam ( 'showTrainingTitle' ) or ($criteria ['doCount'] and ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0')));
		$criteria ['showLocation'] = ($this->getSanParam ( 'showLocation' ) or ($criteria ['doCount'] and $criteria ['training_location_id']));
		$criteria ['showOrganizer'] = ($this->getSanParam ( 'showOrganizer' ) or ($criteria ['doCount'] and ($criteria ['training_organizer_id'])));
		$criteria ['showPepfar'] = ($this->getSanParam ( 'showPepfar' ) or ($criteria ['doCount'] and ($criteria ['training_pepfar_id'] or $criteria ['training_pepfar_id'] === '0')));
		$criteria ['showMethod'] = ($this->getSanParam ( 'showMethod' ) or ($criteria ['doCount'] and ($criteria ['training_method_id'] or $criteria ['training_method_id'] === '0')));
		$criteria ['showTopic'] = ($this->getSanParam ( 'showTopic' ) or ($criteria ['doCount'] and ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0')));
		$criteria ['showLevel'] = ($this->getSanParam ( 'showLevel' ) or ($criteria ['doCount'] and $criteria ['training_level_id']));
		$criteria ['showTot'] = ($this->getSanParam ( 'showTot' ) or ($criteria ['doCount'] and $criteria ['is_tot'] or $criteria ['is_tot'] === '0'));
		$criteria ['showRefresher'] = ($this->getSanParam ( 'showRefresher' ));
		$criteria ['showGotComment'] = ($this->getSanParam ( 'showGotComment' ));
		$criteria ['showPrimaryLanguage'] = ($this->getSanParam ( 'showPrimaryLanguage' ) or ($criteria ['doCount'] and $criteria ['training_primary_language_option_id'] or $criteria ['training_primary_language_option_id'] === '0'));
		$criteria ['showSecondaryLanguage'] = ($this->getSanParam ( 'showSecondaryLanguage' ) or ($criteria ['doCount'] and $criteria ['training_secondary_language_option_id'] or $criteria ['training_secondary_language_option_id'] === '0'));
		$criteria ['showFunding'] = ($this->getSanParam ( 'showFunding' ) or ($criteria ['doCount'] and $criteria ['funding_id'] or $criteria ['funding_id'] === '0' or $criteria ['funding_min'] or $criteria ['funding_max']));
		$criteria ['showCategory'] = ($this->getSanParam ( 'showCategory' ) or ($criteria ['doCount'] and $criteria ['training_category_id'] or $criteria ['training_category_id'] === '0'));
		$criteria ['showGotCurric'] = ($this->getSanParam ( 'showGotCurric' ) or ($criteria ['doCount'] and $criteria ['training_got_curric_id'] or $criteria ['training_got_curric_id'] === '0'));
		$criteria ['showCustom1'] = ($this->getSanParam ( 'showCustom1' ));
		$criteria ['showEndDate'] = ($this->getSanParam('showEndDate'));
		$criteria ['showQualification'] = ($this->getSanParam('showQualification'));
		$criteria ['showAgeRange'] = ($this->getSanParam('showAgeRange'));
		$criteria ['showGender'] = ($this->getSanParam('showGender'));

		$criteria ['training_participants_type'] = $this->getSanParam ( 'training_participants_type' );

		// defaults
		if (! $criteria ['go']) {
			$criteria ['showTrainingTitle'] = 1;
		}

		if ($criteria ['go']) {

			$sql = 'SELECT ';

			if ($criteria ['doCount']) {
				$sql .= ' COUNT(pt.person_id) as "cnt" ';
			} else {

				$sql .= ' DISTINCT pt.id as "id", SUM(person_count_male + person_count_female + person_count_na) as pcnt, SUM(person_count_male) as male_pcnt, SUM(person_count_female) as female_pcnt, SUM(person_count_na) as na_pcnt, pt.training_start_date, pt.training_end_date, pt.has_known_participants  ';
			}

			if ($criteria ['showTrainingTitle']) {
				$sql .= ', training_title ';
			}

			if ($criteria ['showRegionC']) {
				$sql .= ', pt.region_c_name ';
			}
			if ($criteria ['showDistrict']) {
				$sql .= ', pt.district_name ';
			}
			if ($criteria ['showProvince']) {
				$sql .= ', pt.province_name ';
			}

			if ($criteria ['showLocation']) {
				$sql .= ', pt.training_location_name ';
			}

			if ( $criteria ['showQualification'] ) {
				$sql .= ', pqo.qualification_phrase';
				$sql .= ', ppqo.qualification_phrase as parent_qualification_phrase';
			}

			if ( $criteria ['showAgeRange'] ) {
				$sql .= ', aro.age_range_phrase';
			}

			if ($criteria ['showOrganizer']) {
				$sql .= ', torg.training_organizer_phrase ';
			}

			if ($criteria ['showLevel']) {
				$sql .= ', tlev.training_level_phrase ';
			}

			if ($criteria ['showCategory']) {
				$sql .= ', tcat.training_category_phrase ';
			}

			if ($criteria ['showPepfar'] || $criteria ['training_pepfar_id'] || $criteria ['training_pepfar_id'] === '0') {
				if ($criteria ['doCount']) {
					$sql .= ', tpep.pepfar_category_phrase ';
				} else {
					$sql .= ', GROUP_CONCAT(DISTINCT tpep.pepfar_category_phrase) as "pepfar_category_phrase" ';
				}
			}

			if ($criteria ['showMethod']) {
				$sql .= ', tmeth.training_method_phrase ';
			}

			if ($criteria ['showTopic']) {
				if ($criteria ['doCount']) {
					$sql .= ', ttopic.training_topic_phrase ';
				} else {
					$sql .= ', GROUP_CONCAT(DISTINCT ttopic.training_topic_phrase ORDER BY training_topic_phrase) AS "training_topic_phrase" ';
				}
			}

			if ($criteria ['showTot']) {
				$sql .= ", IF(is_tot,'" . t ( 'Yes' ) . "','" . t ( 'No' ) . "') AS is_tot";
			}

			if ($criteria ['showRefresher']) {
				$sql .= ", IF(is_refresher,'" . t ( 'Yes' ) . "','" . t ( 'No' ) . "') AS is_refresher";
			}

			if ($criteria ['showSecondaryLanguage']) {
				$sql .= ', tlos.language_phrase as "secondary_language_phrase" ';
			}
			if ($criteria ['showPrimaryLanguage']) {
				$sql .= ', tlop.language_phrase as "primary_language_phrase" ';
			}
			if ($criteria ['showGotComment']) {
				$sql .= ", pt.got_comments";
			}
			if ($criteria ['showGotCurric']) {
				$sql .= ', tgotc.training_got_curriculum_phrase ';
			}

			if ($criteria ['showFunding']) {
				if ($criteria ['doCount']) {
					$sql .= ', tfund.funding_phrase ';
				} else {
					$sql .= ', GROUP_CONCAT(DISTINCT tfund.funding_phrase ORDER BY funding_phrase) as "funding_phrase" ';
				}
			}
			if ( $criteria['showCustom1'] ) {
				$sql .= ', tqc.custom1_phrase ';
			}

			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id, true);

			//if we're doing a participant count, then LEFT JOIN with the participants
			//otherwise just select the core training info


			if ($criteria ['doCount']) {
				$sql .= ' FROM (SELECT training.*, pers.person_id as "person_id", tto.training_title_phrase AS training_title, training_location.training_location_name, '.implode(',',$field_name).
				'       FROM training ' .
				'         JOIN training_title_option tto ON (`training`.training_title_option_id = tto.id)' .
				'         JOIN training_location ON training.training_location_id = training_location.id ' .
				'         JOIN ('.$location_sub_query.') as l ON training_location.location_id = l.id ' .
				'         LEFT JOIN (SELECT person_id,training_id FROM person JOIN person_to_training ON person_to_training.person_id = person.id) as pers ON training.id = pers.training_id WHERE training.is_deleted=0  AND training.has_known_participants = 0) as pt ';
			} else {
				$sql .= ' FROM (SELECT training.*, tto.training_title_phrase AS training_title,training_location.training_location_name, '.implode(',',$field_name).
				'       FROM training  ' .
				'         JOIN training_title_option tto ON (`training`.training_title_option_id = tto.id) ' .
				'         LEFT JOIN training_location ON training.training_location_id = training_location.id ' .
				'         LEFT JOIN ('.$location_sub_query.') as l ON training_location.location_id = l.id ' .
				'  WHERE training.is_deleted=0 AND training.has_known_participants = 0) as pt ';

				$sql .= ' LEFT JOIN training_to_person_qualification_option as ttpqo ON ttpqo.training_id = pt.id ';
			}

			if ($criteria ['showQualification'] ) {
				$sql .= ' LEFT JOIN person_qualification_option as pqo ON ttpqo.person_qualification_option_id = pqo.id';
				$sql .= ' LEFT JOIN person_qualification_option as ppqo ON pqo.parent_id = ppqo.id';
			}

			if ($criteria ['showAgeRange'] ) {
				$sql .= ' LEFT JOIN age_range_option as aro ON ttpqo.age_range_option_id = aro.id';
			}

			if ($criteria ['showOrganizer'] or $criteria ['training_organizer_id']) {
				$sql .= ' JOIN training_organizer_option as torg ON torg.id = pt.training_organizer_option_id ';
			}
			if ($criteria ['showLevel'] || $criteria ['training_level_id']) {
				$sql .= ' JOIN training_level_option as tlev ON tlev.id = pt.training_level_option_id ';
			}

			if ($criteria ['showMethod'] || $criteria ['training_method_id']) {
				$sql .= ' JOIN training_method_option as tmeth ON tmeth.id = pt.training_method_option_id ';
			}

			if ($criteria ['showPepfar'] || $criteria ['training_pepfar_id'] || $criteria ['training_pepfar_id'] === '0') {
				$sql .= ' LEFT JOIN (SELECT training_id, ttpco.training_pepfar_categories_option_id, pepfar_category_phrase FROM training_to_training_pepfar_categories_option as ttpco JOIN training_pepfar_categories_option as tpco ON ttpco.training_pepfar_categories_option_id = tpco.id) as tpep ON tpep.training_id = pt.id ';
			}

			if ($criteria ['showTopic'] || $criteria ['training_topic_id']) {
				$sql .= ' LEFT JOIN (SELECT training_id, ttto.training_topic_option_id, training_topic_phrase FROM training_to_training_topic_option as ttto JOIN training_topic_option as tto ON ttto.training_topic_option_id = tto.id) as ttopic ON ttopic.training_id = pt.id ';
			}

			if ($criteria ['showPrimaryLanguage'] || $criteria ['training_primary_language_option_id']) {
				$sql .= ' LEFT JOIN trainer_language_option as tlop ON tlop.id = pt.training_primary_language_option_id ';
			}

			if ($criteria ['showSecondaryLanguage'] || $criteria ['training_secondary_language_option_id']) {
				$sql .= ' LEFT JOIN trainer_language_option as tlos ON tlos.id = pt.training_secondary_language_option_id ';
			}

			if ($criteria ['showFunding'] || (intval ( $criteria ['funding_min'] ) or intval ( $criteria ['funding_max'] ))) {
				$sql .= ' LEFT JOIN (SELECT training_id, ttfo.training_funding_option_id, funding_phrase, ttfo.funding_amount FROM training_to_training_funding_option as ttfo JOIN training_funding_option as tfo ON ttfo.training_funding_option_id = tfo.id) as tfund ON tfund.training_id = pt.id ';
			}

			if ($criteria ['showGotCurric'] || $criteria ['training_got_curric_id']) {
				$sql .= ' LEFT JOIN training_got_curriculum_option as tgotc ON tgotc.id = pt.training_got_curriculum_option_id';
			}

			if ($criteria ['showCategory'] or ! empty ( $criteria ['training_category_id'] )) {
				$sql .= '
				LEFT JOIN training_category_option_to_training_title_option tcotto ON (tcotto.training_title_option_id = pt.training_title_option_id)
				LEFT JOIN training_category_option tcat ON (tcotto.training_category_option_id = tcat.id)
				';
			}
			if ( $criteria['showCustom1'] ) {
				$sql .= ' LEFT JOIN training_custom_1_option as tqc ON pt.training_custom_1_option_id = tqc.id  ';
			}

			$where = array ();
			$where [] = ' pt.is_deleted=0 ';


			if ( $criteria['qualification_option_id']) {
				$where []= ' ttpqo.person_qualification_option_id = '.$criteria['qualification_option_id'];
			}
			if ( $criteria['age_range_option_id']) {
				$where []= ' ttpqo.age_range_option_id = '.$criteria['age_range_option_id'];
			}

			if ($criteria ['training_participants_type']) {
				if ($criteria ['training_participants_type'] == 'has_known_participants') {
					$where [] = ' pt.has_known_participants = 1 ';
				}
				if ($criteria ['training_participants_type'] == 'has_unknown_participants') {
					$where [] = ' pt.has_known_participants = 0 ';

				}
			}

			if ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0') {
				$where [] = 'pt.training_title_option_id = ' . $criteria ['training_title_option_id'];
			}

			if ($criteria ['training_location_id']) {
				$where [] = ' pt.training_location_id = \'' . $criteria ['training_location_id'] . '\'';
			}

			if ($criteria ['training_organizer_id'] or $criteria ['training_organizer_id'] === '0') {
				$where [] = ' pt.training_organizer_option_id = \'' . $criteria ['training_organizer_id'] . '\'';
			}

			if ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0') {
				$where [] = ' ttopic.training_topic_option_id = \'' . $criteria ['training_topic_id'] . '\'';
			}

			if ($criteria ['training_level_id']) {
				$where [] = ' pt.training_level_option_id = \'' . $criteria ['training_level_id'] . '\'';
			}

			if ($criteria ['training_pepfar_id'] or $criteria ['training_pepfar_id'] === '0') {
				$where [] = ' tpep.training_pepfar_categories_option_id = \'' . $criteria ['training_pepfar_id'] . '\'';
			}

			if ($criteria ['training_method_id'] or $criteria ['training_method_id'] === '0') {
				$where [] = ' tmeth.id = \'' . $criteria ['training_method_id'] . '\'';
			}

			if ($criteria ['training_primary_language_option_id'] or $criteria ['training_primary_language_option_id'] === '0') {
				$where [] = ' pt.training_primary_language_option_id = \'' . $criteria ['training_primary_language_option_id'] . '\'';
			}

			if ($criteria ['training_secondary_language_option_id'] or $criteria ['training_secondary_language_option_id'] === '0') {
				$where [] = ' pt.training_secondary_language_option_id = \'' . $criteria ['training_secondary_language_option_id'] . '\'';
			}

			if (intval ( $criteria ['end-year'] ) and $criteria ['start-year']) {
				$startDate = $criteria ['start-year'] . '-' . $criteria ['start-month'] . '-' . $criteria ['start-day'];
				$endDate = $criteria ['end-year'] . '-' . $criteria ['end-month'] . '-' . $criteria ['end-day'];
				$where [] = ' training_start_date >= \'' . $startDate . '\'  AND training_start_date <= \'' . $endDate . '\'  ';
			}

			if (intval ( $criteria ['funding_min'] ) or intval ( $criteria ['funding_max'] )) {
				if (intval ( $criteria ['funding_min'] ))
				$where [] = 'tfund.funding_amount >= \'' . $criteria ['funding_min'] . '\' ';
				if (intval ( $criteria ['funding_max'] ))
				$where [] = 'tfund.funding_amount <= \'' . $criteria ['funding_max'] . '\' ';
			}

			if (intval ( $criteria ['is_tot'] )) {
				$where [] = ' is_tot = ' . $criteria ['is_tot'];
			}

			if ($criteria ['funding_id'] or $criteria ['funding_id'] === '0') {
				$where [] = ' tfund.training_funding_option_id = \'' . $criteria ['funding_id'] . '\'';
			}

			if ($criteria ['training_category_id'] or $criteria ['training_category_id'] === '0') {
				$where [] = ' tcat.id = \'' . $criteria ['training_category_id'] . '\'';
			}

			if ($criteria ['training_got_curric_id'] or $criteria ['training_got_curric_id'] === '0') {
				$where [] = ' tgotc.id = \'' . $criteria ['training_got_curric_id'] . '\'';
			}

			if ($criteria ['custom_1_id'] or $criteria ['custom_1_id'] === '0') {
				$where [] = ' pt.training_custom_1_option_id = \'' . $criteria ['custom_1_id'] . '\'';
			}
			if ($where)
			$sql .= ' WHERE ' . implode ( ' AND ', $where );

			if ($criteria ['doCount']) {

				$groupBy = array();

				if ($criteria ['showTrainingTitle']) $groupBy []= 'pt.training_title_option_id';
				if ($criteria ['showProvince']) $groupBy []= ' pt.province_id';
				if ($criteria ['showDistrict']) $groupBy []=  '  pt.district_id';
				if ($criteria ['showRegionC']) $groupBy []=  '  pt.region_c_id';
				if ($criteria ['showLocation']) $groupBy []=  '  pt.training_location_id';
				if ($criteria ['showOrganizer']) $groupBy []=  '  pt.training_organizer_option_id';
				if ($criteria ['showCustom1']) $groupBy []=  '  pt.training_custom_1_option_id';
				if ($criteria ['showTopic']) $groupBy []=  '  ttopic.training_topic_option_id';
				if ($criteria ['showLevel']) $groupBy []=  '  pt.training_level_option_id';
				if ($criteria ['showPepfar']) $groupBy []=  '  tpep.training_pepfar_categories_option_id';
				if ($criteria ['showMethod']) $groupBy []=  '  tmeth.id';
				if ($criteria ['showTot']) $groupBy []=  '  pt.is_tot';
				if ($criteria ['showRefresher']) $groupBy []=  '  pt.is_refresher';
				if ($criteria ['showGotCurric']) $groupBy []=  '  pt.training_got_curriculum_option_id';
				if ($criteria ['showPrimaryLanguage']) $groupBy []=  '  pt.training_primary_language_option_id';
				if ($criteria ['showSecondaryLanguage']) $groupBy []=  '  pt.training_secondary_language_option_id';
				if ($criteria ['showFunding']) $groupBy []=  '  tfund.training_funding_option_id';

				if ($groupBy) {
					$sql .= ' GROUP BY ' . implode(',',$groupBy);
				}

			} else {
				$groupBy = array();
				$groupBy []= 'pt.id';

				if ($criteria ['showQualification']) $groupBy []= ' ttpqo.person_qualification_option_id';
				if ($criteria ['showAgeRange']) $groupBy []= ' ttpqo.age_range_option_id';

				$sql .= ' GROUP BY '.implode(',',$groupBy);

			}

			if ($this->view->mode == 'search') {
				$sql .= ' ORDER BY training_start_date DESC';
			}

			$rowArray = $db->fetchAll ( $sql );

			if ($criteria ['doCount']) {
				$count = 0;
				foreach ( $rowArray as $row ) {
					$count += $row ['cnt'];
				}
			} else {
				$count = count ( $rowArray );
			}

			if ($this->_getParam ( 'outputType' ))
			$this->sendData ( $this->reportHeaders ( false, $rowArray ) );

		} else {
			$count = 0;
			$rowArray = array ();
		}

		$criteria ['go'] = $this->getSanParam ( 'go' );

		$this->viewAssignEscaped ( 'results', $rowArray );
		$this->view->assign ( 'count', $count );
		$this->view->assign ( 'criteria', $criteria );

		$locations = Location::getAll();
		$this->viewAssignEscaped('locations', $locations);
		//course
		//$courseArray = Course::suggestionList(false,10000);
		//$this->viewAssignEscaped('courses',$courseArray);
		//location
		// location drop-down
		$tlocations = TrainingLocation::selectAllLocations ($this->setting('num_location_tiers'));
		$this->viewAssignEscaped ( 'tlocations', $tlocations );
		//organizers
		$organizersArray = OptionList::suggestionList ( 'training_organizer_option', 'training_organizer_phrase', false, false, false );
		$this->viewAssignEscaped ( 'organizers', $organizersArray );
		//topics
		$topicsArray = OptionList::suggestionList ( 'training_topic_option', 'training_topic_phrase', false, false, false );
		$this->viewAssignEscaped ( 'topics', $topicsArray );
		//levels
		$levelArray = OptionList::suggestionList ( 'training_level_option', 'training_level_phrase', false, false );
		$this->viewAssignEscaped ( 'levels', $levelArray );
		//pepfar
		$organizersArray = OptionList::suggestionList ( 'training_pepfar_categories_option', 'pepfar_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'pepfars', $organizersArray );
		//funding
		$fundingArray = OptionList::suggestionList ( 'training_funding_option', 'funding_phrase', false, false, false );
		$this->viewAssignEscaped ( 'funding', $fundingArray );
		//category
		$categoryArray = OptionList::suggestionList ( 'training_category_option', 'training_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'category', $categoryArray );
		//primary language
		$langArray = OptionList::suggestionList ( 'trainer_language_option', 'language_phrase', false, false, false );
		$this->viewAssignEscaped ( 'language', $langArray );
		//category
		$categoryArray = OptionList::suggestionList ( 'training_category_option', 'training_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'category', $categoryArray );
		//category+titles
		$categoryTitle = MultiAssignList::getOptions ( 'training_title_option', 'training_title_phrase', 'training_category_option_to_training_title_option', 'training_category_option' );
		$this->view->assign ( 'categoryTitle', $categoryTitle );
		//training methods
		$methodTitle = OptionList::suggestionList ( 'training_method_option', 'training_method_phrase', false, false, false );
		$this->view->assign ( 'methods', $methodTitle );

		$customArray = OptionList::suggestionList ( 'training_custom_1_option', 'custom1_phrase', false, false, false );
		$this->viewAssignEscaped ( 'custom1', $customArray );

		$customArray = OptionList::suggestionList ( 'age_range_option', 'age_range_phrase', false, false, false );
		$this->viewAssignEscaped ( 'age_range', $customArray );

		//$customArray = OptionList::suggestionList ( 'person_qualification_option', 'qualification_phrase', false, false, false, 'parent_id IS NULL');
		$qualificationsArray = OptionList::suggestionListHierarchical ( 'person_qualification_option', 'qualification_phrase', false, false, array ('0 AS is_default', 'child.is_default' ) );
		$this->viewAssignEscaped ( 'qualifications', $qualificationsArray );

		$this->viewAssignEscaped ( 'gender', array(
		array('id'=>1,'name'=>t('Unknown')),
		array('id'=>2,'name'=>t('Female')),
		array('id'=>3,'name'=>t('Male')) ));

		// find category based on title
		$catId = NULL;
		if ($criteria ['training_category_id']) {
			foreach ( $categoryTitle as $r ) {
				if ($r ['id'] == $criteria ['training_category_id']) {
					$catId = $r ['training_category_option_id'];
					break;
				}
			}
		}
		$this->view->assign ( 'catId', $catId );

		//got curric
		$gotCuriccArray = OptionList::suggestionList ( 'training_got_curriculum_option', 'training_got_curriculum_phrase', false, false, false );
		$this->viewAssignEscaped ( 'gotcurric', $gotCuriccArray );

	}


	public function trainersByNameAction() {
		$this->view->assign('is_trainers', true);
		return $this->peopleReport();

	}


	public function peopleReport() {

		$criteria = array ();

		//find the first date in the database
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT MIN(training_start_date) as \"start\" FROM training WHERE is_deleted = 0";
		$rowArray = $db->fetchAll ( $sql );
		$start_default = '0000-00-00';
		if ($rowArray and $rowArray [0] ['start'])
		$start_default = $rowArray [0] ['start'];
		$parts = explode ( '-', $start_default );
		$criteria ['start-year'] = $parts [0];
		$criteria ['start-month'] = $parts [1];
		$criteria ['start-day'] = $parts [2];

		$criteria ['showAge'] = $this->getSanParam ( 'showAge' );
		$criteria ['age_min'] = $this->getSanParam ( 'age_min' );
		$criteria ['age_max'] = $this->getSanParam ( 'age_max' );

		if ($this->getSanParam ( 'start-year' ))
		$criteria ['start-year'] = $this->getSanParam ( 'start-year' );
		if ($this->getSanParam ( 'start-month' ))
		$criteria ['start-month'] = $this->getSanParam ( 'start-month' );
		if ($this->getSanParam ( 'start-day' ))
		$criteria ['start-day'] = $this->getSanParam ( 'start-day' );
		$criteria ['end-year'] = date ( 'Y' );
		$criteria ['end-month'] = date ( 'm' );
		$criteria ['end-day'] = date ( 'd' );
		if ($this->getSanParam ( 'end-year' ))
		$criteria ['end-year'] = $this->getSanParam ( 'end-year' );
		if ($this->getSanParam ( 'end-month' ))
		$criteria ['end-month'] = $this->getSanParam ( 'end-month' );
		if ($this->getSanParam ( 'end-day' ))
		$criteria ['end-day'] = $this->getSanParam ( 'end-day' );

		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);

		$criteria ['training_gender'] = $this->getSanParam ( 'training_gender' );
		$criteria ['training_active'] = $this->getSanParam ( 'training_active' );
		$criteria ['concatNames'] = $this->getSanParam ( 'concatNames' );
		$criteria ['training_title_option_id'] = $this->getSanParam ( 'training_title_option_id' );
		$criteria ['training_title_id'] = $this->getSanParam ( 'training_title_id' );
		$criteria ['training_pepfar_id'] = $this->getSanParam ( 'training_pepfar_id' );
		$criteria ['training_topic_id'] = $this->getSanParam ( 'training_topic_id' );
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['qualification_secondary_id'] = $this->getSanParam ( 'qualification_secondary_id' );


		$criteria ['facilityInput'] = $this->getSanParam ( 'facilityInput' );
		$criteria ['is_tot'] = $this->getSanParam ( 'is_tot' );
		$criteria ['training_organizer_id'] = $this->getSanParam ( 'training_organizer_id' );
		$criteria ['training_organizer_option_id'] = $this->getSanParam ( 'training_organizer_option_id' );
		$criteria ['funding_id'] = $this->getSanParam ( 'funding_id' );
		$criteria ['custom_1_id'] = $this->getSanParam ( 'custom_1_id' );
		$criteria ['distinctCount'] = $this->getSanParam ( 'distinctCount' );

		if ($this->view->isScoreReport) {
			$criteria ['score_min'] = (is_numeric ( trim ( $this->getSanParam ( 'score_min' ) ) )) ? trim ( $this->getSanParam ( 'score_min' ) ) : '';
			$criteria ['score_percent_min'] = (is_numeric ( trim ( $this->getSanParam ( 'score_percent_min' ) ) )) ? trim ( $this->getSanParam ( 'score_percent_min' ) ) : '';
		}

		$criteria ['doCount'] = ($this->view->mode == 'count');
		$criteria ['showProvince'] = ($this->getSanParam ( 'showProvince' ) or ($criteria ['doCount'] and ($criteria ['province_id'] or ! empty ( $criteria ['province_id'] ))));
		$criteria ['showDistrict'] = ($this->getSanParam ( 'showDistrict' ) or ($criteria ['doCount'] and ($criteria ['district_id'] or ! empty ( $criteria ['district_id'] ))));
		$criteria ['showRegionC'] = ($this->getSanParam ( 'showRegionC' ) or ($criteria ['doCount'] and ($criteria ['region_c_id'] or ! empty ( $criteria ['region_c_id'] ))));
		$criteria ['showTrainingTitle'] = ($this->getSanParam ( 'showTrainingTitle' ) or ($criteria ['doCount'] and ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0' or $criteria ['training_title_id'])));
		$criteria ['showPepfar'] = ($this->getSanParam ( 'showPepfar' ) or ($criteria ['doCount'] and ($criteria ['training_title_option_id'] or $criteria ['training_pepfar_id'] === '0')));
		$criteria ['showQualification'] = false; // ($this->getSanParam('showQualification') OR ($criteria['doCount']  and ($criteria['qualification_id'] or $criteria['qualification_id'] === '0') ));
		$criteria ['showTopic'] = ($this->getSanParam ( 'showTopic' ) or ($criteria ['doCount'] and ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0')));
		$criteria ['showFacility'] = ($this->getSanParam ( 'showFacility' ) or ($criteria ['doCount'] and $criteria ['facilityInput']));
		$criteria ['showGender'] = ($this->getSanParam ( 'showGender' ) or ($criteria ['doCount'] and $criteria ['training_gender']));
		$criteria ['showActive'] = ($this->getSanParam ( 'showActive' ) or ($criteria ['doCount'] and $criteria ['training_active']));
		$criteria ['showSuffix'] = ($this->getSanParam ( 'showSuffix' ));
		$criteria ['showEmail'] = ($this->getSanParam ( 'showEmail' ));
		$criteria ['showPhone'] = ($this->getSanParam ( 'showPhone' ));
		$criteria ['showTot'] = ($this->getSanParam ( 'showTot' ) or ($criteria ['doCount'] and $criteria ['is_tot'] !== '' or $criteria ['is_tot'] === '0'));
		$criteria ['showOrganizer'] = ($this->getSanParam ( 'showOrganizer' ) or ($criteria ['doCount'] and ($criteria ['training_organizer_option_id'])));
		$criteria ['showFunding'] = ($this->getSanParam ( 'showFunding' ) or ($criteria ['doCount'] and $criteria ['funding_id'] or $criteria ['funding_id'] === '0'));
		$criteria ['showQualPrim'] = ($this->getSanParam ( 'showQualPrim' ) or ($criteria ['doCount'] and ($criteria ['qualification_id'] or $criteria ['qualification_id'] === '0')));
		$criteria ['showQualSecond'] = ($this->getSanParam ( 'showQualSecond' ) or ($criteria ['doCount'] and ($criteria ['qualification_secondary_id'] or $criteria ['qualification_secondary_id'] === '0')));
		$criteria ['showCustom1'] = ($this->getSanParam ( 'showCustom1' ));

		$criteria ['showRespPrim'] = ($this->getSanParam ( 'showRespPrim' ));
		$criteria ['showRespSecond'] = ($this->getSanParam ( 'showRespSecond' ));
		$criteria ['showHighestEd'] = ($this->getSanParam ( 'showHighestEd' ));
		$criteria ['showReason'] = ($this->getSanParam ( 'showReason' ));

		$criteria ['primary_responsibility_option_id'] = $this->getSanParam ( 'primary_responsibility_option_id' );
		$criteria ['secondary_responsibility_option_id'] = $this->getSanParam ( 'secondary_responsibility_option_id' );

		$criteria ['highest_edu_level_option_id'] = $this->getSanParam ( 'highest_edu_level_option_id' );
		$criteria ['attend_reason_option_id'] = $this->getSanParam ( 'attend_reason_option_id' );


		$criteria ['go'] = $this->getSanParam ( 'go' );
		if ($criteria ['go']) {

			$sql = 'SELECT ';

			if ($criteria ['doCount']) {
				$distinct = ($criteria ['distinctCount']) ? 'DISTINCT ' : '';
				$sql .= ' COUNT(' . $distinct . 'person_id) as "cnt" ';
			} else {
				if ($criteria ['concatNames'])
				$sql .= ' DISTINCT person_id as "id", CONCAT(first_name, ' . "' '" . ',last_name, ' . "' '" . ', IFNULL(suffix_phrase, ' . "' '" . ')) as "name", IFNULL(suffix_phrase, ' . "' '" . ') as suffix_phrase, pt.training_start_date  ';
				else
				$sql .= ' DISTINCT person_id as "id", IFNULL(suffix_phrase, ' . "' '" . ') as suffix_phrase, last_name, first_name, middle_name, pt.training_start_date  ';
			}
			if ($criteria ['showPhone']) {
				$sql .= ", CASE WHEN (pt.phone_work IS NULL OR pt.phone_work = '') THEN NULL ELSE pt.phone_work END as \"phone_work\", CASE WHEN (pt.phone_home IS NULL OR pt.phone_home = '') THEN NULL ELSE pt.phone_home END as \"phone_home\", CASE WHEN (pt.phone_mobile IS NULL OR pt.phone_mobile = '') THEN NULL ELSE pt.phone_mobile END as \"phone_mobile\" ";
			}
			if ($criteria ['showEmail']) {
				$sql .= ', pt.email ';
			}
			if ($criteria ['showAge']) {
				$sql .= ', pt.age ';
			}
			if ($criteria ['showGender']) {
				$sql .= ', pt.gender ';
			}
			if ($criteria ['showActive']) {
				$sql .= ', pt.active ';
			}
			if ($criteria ['showTrainingTitle']) {
				$sql .= ', pt.training_title ';
			}
			if ($criteria ['showDistrict']) {
				$sql .= ', pt.district_name ';
			}
			if ($criteria ['showProvince']) {
				$sql .= ', pt.province_name ';
			}

			if ($criteria ['showRegionC']) {
				$sql .= ', pt.region_c_name ';
			}

			if ($criteria ['showPepfar'] || $criteria ['training_pepfar_id'] || $criteria ['training_pepfar_id'] === '0') {
				$sql .= ', tpep.pepfar_category_phrase ';
			}

			if ($criteria ['showTopic']) {
				$sql .= ', ttopic.training_topic_phrase ';
			}

			if ($criteria ['showFacility']) {
				$sql .= ', pt.facility_name ';
			}

			if ($criteria ['showTot']) {
				$sql .= ", IF(pt.is_tot,'" . t ( 'Yes' ) . "','" . t ( 'No' ) . "') AS is_tot";
			}

			if ($criteria ['showOrganizer']) {
				$sql .= ', torg.training_organizer_phrase ';
			}

			if ($criteria ['showFunding']) {
				if ($criteria ['doCount']) {
					$sql .= ', tfund.funding_phrase ';
				} else {
					$sql .= ', GROUP_CONCAT(DISTINCT tfund.funding_phrase ORDER BY funding_phrase) as "funding_phrase" ';
				}
			}

			if ($criteria ['showQualification']) {
				$sql .= ', pq.qualification_phrase ';
			}

			if ($criteria ['showQualPrim']) {
				$sql .= ', pq.qualification_phrase ';
			}
			if ($criteria ['showQualSecond']) {
				$sql .= ', pqs.qualification_phrase AS qualification_secondary_phrase';
			}

			if ($criteria ['showRespPrim']) {
				$sql .= ', pr.responsibility_phrase as primaryResponsibility';
			}
			if ($criteria ['showRespSecond']) {
				$sql .= ', sr.responsibility_phrase  as secondaryResponsibility';
			}


			if ($criteria ['showHighestEd']) {
				$sql .= ', ed.education_level_phrase ';
			}
			if ($criteria ['showReason']) {
				$sql .= ', CASE WHEN attend_reason_other IS NOT NULL THEN attend_reason_other ELSE attend_reason_phrase END AS attend_reason_phrase ';
			}



			if ( $criteria['showCustom1'] ) {
				$sql .= ', pqc.custom1_phrase ';
			}

			if ($this->view->isScoreReport) {
				$sql .= ', spre.score_value AS score_pre, spost.score_value AS score_post, ' . 'ROUND((spost.score_value - spre.score_value) / spre.score_value * 100) AS score_percent_change';
			}

			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id, true);


			$intersection_table = 'person_to_training';
			$intersection_person_id = 'person_id';

			if ( $this->view->is_trainers ) {
				$intersection_table = 'training_to_trainer';
				$intersection_person_id = 'trainer_id';
			}

			// Sean Smith: Had to rework to add suffix. No old *= syntax for left joins in Mysql
			/*	$sql .= ' FROM (SELECT training.*, person.facility_id as "facility_id", person.id as "person_id", person.last_name, person.first_name, person.middle_name, person.person_custom_1_option_id, CASE WHEN birthdate  IS NULL OR birthdate = \'0000-00-00\' THEN NULL ELSE ((date_format(now(),\'%Y\') - date_format(birthdate,\'%Y\')) -
			(date_format(now(),\'00-%m-%d\') < date_format(birthdate,\'00-%m-%d\')) ) END as "age", person.phone_work, person.phone_home, person.phone_mobile, person.email, CASE WHEN person.active = \'deceased\' THEN \'inactive\' ELSE person.active END as "active", CASE WHEN person.gender IS NULL THEN \'na\' WHEN person.gender = \'\' THEN \'na\' ELSE person.gender END as "gender", primary_qualification_option_id, tto.training_title_phrase AS training_title,facility.facility_name, '.$intersection_table.'.id AS ptt_id, l.'.implode(', l.',$field_name).
			' FROM training,'.$intersection_table.',facility,person,person_suffix_option suff,training_title_option tto
			, ('.$location_sub_query.') as l
			WHERE  facility.location_id = l.id
			AND `training`.training_title_option_id = tto.id
			AND training.id = '.$intersection_table.'.training_id
			AND person.id = '.$intersection_table.'.'.$intersection_person_id.'
			AND person.facility_id = facility.id
			AND person.suffix_option_id *= suff.id
			*/

			$sql .= ' FROM (';
			$sql .= 'SELECT training.*, person.facility_id as "facility_id", person.id as "person_id", person.last_name, IFNULL(suffix_phrase, ' . "' '" . ') as suffix_phrase, ';
			$sql .= 'person.first_name, person.middle_name, person.person_custom_1_option_id, ';
			$sql .= 'CASE WHEN birthdate  IS NULL OR birthdate = \'0000-00-00\' THEN NULL ELSE ((date_format(now(),\'%Y\') - date_format(birthdate,\'%Y\')) - (date_format(now(),\'00-%m-%d\') < date_format(birthdate,\'00-%m-%d\')) ) END as "age", ';
			$sql .= 'person.phone_work, person.phone_home, person.phone_mobile, person.email, ';
			$sql .= 'CASE WHEN person.active = \'deceased\' THEN \'inactive\' ELSE person.active END as "active", ';
			$sql .= 'CASE WHEN person.gender IS NULL THEN \'na\' WHEN person.gender = \'\' THEN \'na\' ELSE person.gender END as "gender", ';
			$sql .= 'primary_qualification_option_id, primary_responsibility_option_id, secondary_responsibility_option_id, highest_edu_level_option_id, attend_reason_option_id, attend_reason_other, tto.training_title_phrase AS training_title,facility.facility_name, ';
			$sql .= $intersection_table.'.id AS ptt_id, l.'.implode(', l.',$field_name);
			$sql .= ' FROM training INNER JOIN training_title_option tto ON training.training_title_option_id = tto.id ';
			$sql .= '    INNER JOIN '.$intersection_table.' ON training.id = '.$intersection_table.'.training_id ';
			$sql .= '    INNER JOIN person ON person.id = '.$intersection_table.'.'.$intersection_person_id;
			$sql .= '    INNER JOIN facility ON person.facility_id = facility.id ';
			$sql .= '    LEFT JOIN ('.$location_sub_query.') AS l ON facility.location_id = l.id ';
			$sql .= '    LEFT  JOIN person_suffix_option suffix ON person.suffix_option_id = suffix.id ';
			$sql .= ') as pt ';

			if ($criteria ['showPepfar'] || $criteria ['training_pepfar_id'] || $criteria ['training_pepfar_id'] === '0') {
				$sql .= '	JOIN (SELECT training_id, ttpco.training_pepfar_categories_option_id, pepfar_category_phrase FROM training_to_training_pepfar_categories_option as ttpco JOIN training_pepfar_categories_option as tpco ON ttpco.training_pepfar_categories_option_id = tpco.id) as tpep ON tpep.training_id = pt.id ';
			}

			if ($criteria ['showTopic'] || $criteria ['training_topic_id']) {
				$sql .= ' LEFT JOIN (SELECT training_id, ttto.training_topic_option_id, training_topic_phrase FROM training_to_training_topic_option as ttto JOIN training_topic_option as tto ON ttto.training_topic_option_id = tto.id) as ttopic ON ttopic.training_id = pt.id ';
			}
			/*
			if ( $criteria['showQualification'] ) {
			$sql .= '	JOIN person_qualification_option as pq ON pq.id = pt.primary_qualification_option_id ';
			}
			*/

			if ($criteria ['showOrganizer']) {
				$sql .= '	JOIN training_organizer_option as torg ON torg.id = pt.training_organizer_option_id ';
			}

			if ($criteria ['showFunding']) {
				$sql .= '	LEFT JOIN (SELECT training_id, ttfo.training_funding_option_id, funding_phrase FROM training_to_training_funding_option as ttfo JOIN training_funding_option as tfo ON ttfo.training_funding_option_id = tfo.id) as tfund ON tfund.training_id = pt.id ';
			}

			if ( $criteria['showCustom1'] ) {
				$sql .= ' LEFT JOIN person_custom_1_option as pqc ON pt.person_custom_1_option_id = pqc.id  ';
			}

			if ($criteria ['showQualPrim'] || $criteria ['showQualSecond'] || $criteria ['qualification_id']  || $criteria ['qualification_secondary_id']) {
				// primary qualifications
				$sql .= '
				LEFT JOIN person_qualification_option as pq ON (
				(pt.primary_qualification_option_id = pq.id AND pq.parent_id IS NULL)
				OR
				pq.id = (SELECT parent_id FROM person_qualification_option WHERE id = pt.primary_qualification_option_id LIMIT 1)
				)';

				// secondary qualifications
				$sql .= '
				LEFT JOIN person_qualification_option as pqs ON (
				pt.primary_qualification_option_id = pqs.id AND pqs.parent_id IS NOT NULL
				)';
			}

			if ( $criteria['showRespPrim'] ) {
				$sql .= ' LEFT JOIN person_primary_responsibility_option as pr ON pt.primary_responsibility_option_id = pr.id  ';
			}
			if ( $criteria['showRespSecond'] ) {
				$sql .= ' LEFT JOIN person_secondary_responsibility_option as sr ON pt.secondary_responsibility_option_id = sr.id  ';
			}



			if ( $criteria['showHighestEd'] ) {
				$sql .= ' LEFT JOIN person_education_level_option as ed ON pt.highest_edu_level_option_id = ed.id  ';
			}

			if ( $criteria['showReason'] ) {
				$sql .= ' LEFT JOIN person_attend_reason_option as ra ON pt.attend_reason_option_id = ra.id  ';
			}


			if ($this->view->isScoreReport) {
				$sql .= "
				LEFT JOIN score AS spre ON (spre.person_to_training_id = pt.ptt_id AND spre.score_label = 'Pre-Test')
				LEFT JOIN score AS spost ON (spost.person_to_training_id = pt.ptt_id AND spost.score_label = 'Post-Test')
				";
			}

			$where = array ();

			if ($criteria ['age_min']) {
				$where []= ' pt.age >= '.$criteria['age_min'];
			}
			if ($criteria ['age_max']) {
				$where []= ' pt.age <= '.$criteria['age_max'];
			}

			// legacy
			if ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0') {
				$where [] = ' pt.training_title_option_id = ' . $criteria ['training_title_option_id'];
			}

			if ($criteria ['training_title_id'] or $criteria ['training_title_id'] === '0') {
				$where [] = ' pt.training_title_option_id = ' . $criteria ['training_title_id'];
			}

			if ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0') {
				$where [] = ' ttopic.training_topic_option_id = \'' . $criteria ['training_topic_id'] . '\'';
			}

			if ($criteria ['custom_1_id'] or $criteria ['custom_1_id'] === '0') {
				$where [] = ' pt.person_custom_1_option_id = \'' . $criteria ['custom_1_id'] . '\'';
			}
			/*
			if ( $criteria['qualification_id'] or $criteria['qualification_id'] === '0'  ) {
			if ( strlen($where) ) $where .= ' AND ';
			$where .= ' pt.primary_qualification_option_id = \''.$criteria['qualification_id'].'\'' ;
			}
			*/
			if ($criteria ['qualification_id']) {
				$where [] = ' (pq.id = ' . $criteria ['qualification_id'] . ' OR pqs.parent_id = ' . $criteria ['qualification_id'] . ') ';
			}
			if ($criteria ['qualification_secondary_id']) {
				$where [] = ' pqs.id = ' . $criteria ['qualification_secondary_id'];
			}

			if ($criteria ['primary_responsibility_option_id']) {
				$where [] = ' pt.primary_responsibility_option_id = ' . $criteria ['primary_responsibility_option_id'] . ' ';
			}
			if ($criteria ['secondary_responsibility_option_id']) {
				$where [] = ' pt.secondary_responsibility_option_id = ' . $criteria ['secondary_responsibility_option_id'] . ' ';
			}
			if ($criteria ['highest_edu_level_option_id']) {
				$where [] = ' pt.highest_edu_level_option_id = ' . $criteria ['highest_edu_level_option_id'] . ' ';
			}
			if ($criteria ['attend_reason_option_id']) {
				$where [] = ' pt.attend_reason_option_id = ' . $criteria ['attend_reason_option_id'] . ' ';
			}


			if ($criteria ['training_pepfar_id'] or $criteria ['training_pepfar_id'] === '0') {
				$where [] = ' tpep.training_pepfar_categories_option_id = \'' . $criteria ['training_pepfar_id'] . '\'';
			}

			if ($criteria ['facilityInput']) {
				$where [] = ' pt.facility_id = \'' . $criteria ['facilityInput'] . '\'';
			}

			if ($criteria ['training_gender']) {
				$where [] = ' pt.gender = \'' . $criteria ['training_gender'] . '\'';
			}

			if ($criteria ['training_active']) {
				$where [] = ' pt.active = \'' . $criteria ['training_active'] . '\'';
			}

			if ($criteria ['is_tot'] or $criteria ['is_tot'] === '0') {
				$where [] = ' pt.is_tot = ' . $criteria ['is_tot'];
			}

			if ($criteria ['training_organizer_id'] or $criteria ['training_organizer_id'] === '0') {
				$where [] = ' pt.training_organizer_option_id = \'' . $criteria ['training_organizer_id'] . '\'';
			}

			if ($criteria ['training_organizer_option_id'] && is_array ( $criteria ['training_organizer_option_id'] )) {
				$where [] = ' pt.training_organizer_option_id IN (' . implode ( ',', $criteria ['training_organizer_option_id'] ) . ')';
			}

			if ($criteria ['funding_id'] or $criteria ['funding_id'] === '0') {
				$where [] = ' tfund.training_funding_option_id = \'' . $criteria ['funding_id'] . '\'';
			}

			if (intval ( $criteria ['end-year'] ) and $criteria ['start-year']) {
				$startDate = $criteria ['start-year'] . '-' . $criteria ['start-month'] . '-' . $criteria ['start-day'];
				$endDate = $criteria ['end-year'] . '-' . $criteria ['end-month'] . '-' . $criteria ['end-day'];
				$where [] = ' training_start_date >= \'' . $startDate . '\'  AND training_start_date <= \'' . $endDate . '\'  ';
			}

			if ($this->view->isScoreReport) {
				$where [] = ' (spre.score_value != "" OR spost.score_value != "")'; // require a score to be present


				if ($criteria ['score_min']) {
					$where [] = ' spost.score_value > ' . $criteria ['score_min'];
				}
			}

			if ($where)
			$sql .= ' WHERE ' . implode ( ' AND ', $where );

			if ($this->view->isScoreReport && $criteria ['score_percent_min']) {
				$sql .= ' HAVING score_percent_change > ' . $criteria ['score_percent_min'];
			}

			if ($criteria ['doCount']) {

				$groupBy = array();

				if ( $criteria['showAge']) $groupBy []= ' pt.age ';

				if ($criteria ['showTrainingTitle']) {
					$groupBy []= ' pt.training_title_option_id';
				}
				if ($criteria ['showGender']) {
					$groupBy []= ' pt.gender';
				}
				if ($criteria ['showActive']) {
					$groupBy []= ' pt.active';
				}
				if ($criteria ['showProvince']) {
					$groupBy []= ' pt.province_id';
				}
				if ($criteria ['showDistrict']) {
					$groupBy []= ' pt.district_id';
				}
				if ($criteria ['showRegionC']) {
					$groupBy []= ' pt.region_c_id';
				}

				if ( $criteria['showRespPrim'] ) {
					$groupBy []= ' pt.primary_responsibility_option_id';
				}
				if ( $criteria['showRespSecond'] ) {
					$groupBy []= ' pt.secondary_responsibility_option_id';
				}

				if ($criteria ['showCustom1']) {
					$groupBy []= '  pt.person_custom_1_option_id';
				}
				if (isset ( $criteria ['showLocation'] ) and $criteria ['showLocation']) {
					$groupBy []= '  pt.training_location_id';
				}
				if ($criteria ['showTopic']) {
					$groupBy []= '  ttopic.training_topic_option_id';
				}
				if ($criteria ['showQualification']) {
					$groupBy []= '  pt.primary_qualification_option_id';
				}
				if ($criteria ['showPepfar'] || $criteria ['training_pepfar_id'] || $criteria ['training_pepfar_id'] === '0') {
					$groupBy []= '  tpep.training_pepfar_categories_option_id';
				}

				if ($criteria ['showFacility']) {
					$groupBy []= '  pt.facility_id';
				}

				if ($criteria ['showTot']) {
					$groupBy []= '  pt.is_tot';
				}

				if ($criteria ['showOrganizer']) {
					$groupBy []= '  pt.training_organizer_option_id';
				}

				if ($criteria ['showFunding']) {
					$groupBy []= '  tfund.training_funding_option_id';
				}
				if ($criteria ['showQualPrim']) {
					$groupBy []= '  pq.id ';
				}
				if ($criteria ['showQualSecond']) {
					$groupBy []= '  pqs.id ';
				}



				if ($groupBy ) {
					$groupBy = ' GROUP BY ' . implode(', ',$groupBy);
					$sql .= $groupBy;
				}
			} else {
				if ($criteria ['showPepfar'] || $criteria ['showTopic'] || $criteria ['showFunding']) {
					$sql .= ' GROUP BY person_id, pt.id';
				}
			}
			//echo $sql; exit;


			$rowArray = $db->fetchAll ( $sql );

			if ($criteria ['doCount']) {
				$count = 0;
				foreach ( $rowArray as $row ) {
					$count += $row ['cnt'];
				}
			} else {
				$count = count ( $rowArray );
			}
			if ($this->_getParam ( 'outputType' ))
			$this->sendData ( $this->reportHeaders ( false, $rowArray ) );

		} else {
			$count = 0;
			$rowArray = array ();
		}

		$criteria ['go'] = $this->getSanParam ( 'go' );

		$this->viewAssignEscaped ( 'results', $rowArray );
		if ($rowArray) {
			$first = reset ( $rowArray );
			if (isset ( $first ['phone_work'] )) {
				foreach ( $rowArray as $key => $val ) {
					$phones = array ();
					if ($val ['phone_work'])
					$phones [] = str_replace ( ' ', '&nbsp;', trim ( $val ['phone_work'] ) ) . '&nbsp;(w)';
					if ($val ['phone_home'])
					$phones [] = str_replace ( ' ', '&nbsp;', trim ( $val ['phone_home'] ) ) . '&nbsp;(h)';
					if ($val ['phone_mobile'])
					$phones [] = str_replace ( ' ', '&nbsp;', trim ( $val ['phone_mobile'] ) ) . '&nbsp;(m)';
					$rowArray [$key] ['phone'] = implode ( ', ', $phones );
				}
				$this->view->assign ( 'results', $rowArray );
			}
		}

		$this->view->assign ( 'count', $count );
		$this->view->assign ( 'criteria', $criteria );

		//location
		$locations = Location::getAll();
		$this->viewAssignEscaped ( 'locations', $locations );

		//trainingTitle
		$courseArray = TrainingTitleOption::suggestionList ( false, 10000 );
		$this->viewAssignEscaped ( 'courses', $courseArray );
		//topics
		$topicsArray = OptionList::suggestionList ( 'training_topic_option', 'training_topic_phrase', false, false, false );
		$this->viewAssignEscaped ( 'topics', $topicsArray );
		//topics
		$qualsArray = OptionList::suggestionList ( 'person_qualification_option', 'qualification_phrase', false, false, false );
		$this->viewAssignEscaped ( 'qualifications', $qualsArray );
		//qualifications (primary)
		$qualsArray = OptionList::suggestionList ( 'person_qualification_option', 'qualification_phrase', false, false, false, 'parent_id IS NULL' );
		$this->viewAssignEscaped ( 'qualifications_primary', $qualsArray );
		//qualifications (secondary)
		$qualsArray = OptionList::suggestionList ( 'person_qualification_option', 'qualification_phrase', false, false, false, 'parent_id IS NOT NULL' );
		$this->viewAssignEscaped ( 'qualifications_secondary', $qualsArray );


		$qualsArray = OptionList::suggestionList ( 'person_primary_responsibility_option', 'responsibility_phrase', false, false, false );
		$this->viewAssignEscaped ( 'responsibility_primary', $qualsArray );

		$qualsArray = OptionList::suggestionList ( 'person_secondary_responsibility_option', 'responsibility_phrase', false, false, false );
		$this->viewAssignEscaped ( 'responsibility_secondary', $qualsArray );

		$qualsArray = OptionList::suggestionList ( 'person_attend_reason_option', 'attend_reason_phrase', false, false, false );
		$this->viewAssignEscaped ( 'attend_reason', $qualsArray );

		$qualsArray = OptionList::suggestionList ( 'person_education_level_option', 'education_level_phrase', false, false, false);
		$this->viewAssignEscaped ( 'highest_education_level', $qualsArray );


		//pepfar
		$organizersArray = OptionList::suggestionList ( 'training_pepfar_categories_option', 'pepfar_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'pepfars', $organizersArray );
		//organizers
		//$organizersArray = OptionList::suggestionList('training_organizer_option','training_organizer_phrase',false,false,false);
		//$this->viewAssignEscaped('organizers',$organizersArray);
		//funding
		$fundingArray = OptionList::suggestionList ( 'training_funding_option', 'funding_phrase', false, false, false );
		$this->viewAssignEscaped ( 'funding', $fundingArray );

		$customArray = OptionList::suggestionList ( 'person_custom_1_option', 'custom1_phrase', false, false, false );
		$this->viewAssignEscaped ( 'custom1', $customArray );

		//organizers
		$this->view->assign ( 'organizers_checkboxes', Checkboxes::generateHtml ( 'training_organizer_option', 'training_organizer_phrase', $this->view ) );

		//facilities list
		$rowArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $rowArray as $key => $val ) {
			if ($val ['id'] != 0)
			$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );

	}

	public function participantsByTrainingAction() {
		$this->view->assign ( 'mode', 'count' );
		return $this->peopleReport ();
	}

	public function participantsScoresAction() {
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'isScoreReport', TRUE );
		return $this->peopleReport ();
	}

	public function participantsByNameAction() {
		$this->view->assign ( 'mode', 'id' );
		return $this->peopleReport ();
	}

	public function participantsByFacilityTypeAction() {
		$this->view->assign ( 'mode', 'id' );
		$criteria = array ();

		//find the first date in the database
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT MIN(training_start_date) as \"start\" FROM training WHERE is_deleted = 0";
		$rowArray = $db->fetchAll ( $sql );
		$start_default = '0000-00-00';
		if ($rowArray and $rowArray [0] ['start'])
		$start_default = $rowArray [0] ['start'];
		$parts = explode ( '-', $start_default );
		$criteria ['start-year'] = $parts [0];
		$criteria ['start-month'] = $parts [1];
		$criteria ['start-day'] = $parts [2];

		$criteria ['showAge'] = $this->getSanParam ( 'showAge' );
		$criteria ['age_min'] = $this->getSanParam ( 'age_min' );
		$criteria ['age_max'] = $this->getSanParam ( 'age_max' );

		if ($this->getSanParam ( 'start-year' ))
		$criteria ['start-year'] = $this->getSanParam ( 'start-year' );
		if ($this->getSanParam ( 'start-month' ))
		$criteria ['start-month'] = $this->getSanParam ( 'start-month' );
		if ($this->getSanParam ( 'start-day' ))
		$criteria ['start-day'] = $this->getSanParam ( 'start-day' );
		$criteria ['end-year'] = date ( 'Y' );
		$criteria ['end-month'] = date ( 'm' );
		$criteria ['end-day'] = date ( 'd' );
		if ($this->getSanParam ( 'end-year' ))
		$criteria ['end-year'] = $this->getSanParam ( 'end-year' );
		if ($this->getSanParam ( 'end-month' ))
		$criteria ['end-month'] = $this->getSanParam ( 'end-month' );
		if ($this->getSanParam ( 'end-day' ))
		$criteria ['end-day'] = $this->getSanParam ( 'end-day' );

		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);

		$criteria ['training_gender'] = $this->getSanParam ( 'training_gender' );
		$criteria ['training_active'] = $this->getSanParam ( 'training_active' );
		$criteria ['concatNames'] = $this->getSanParam ( 'concatNames' );
		$criteria ['training_title_option_id'] = $this->getSanParam ( 'training_title_option_id' );
		$criteria ['training_title_id'] = $this->getSanParam ( 'training_title_id' );
		$criteria ['training_pepfar_id'] = $this->getSanParam ( 'training_pepfar_id' );
		$criteria ['training_topic_id'] = $this->getSanParam ( 'training_topic_id' );
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['facility_type_id'] = $this->getSanParam ( 'facility_type_id' );
		$criteria ['facility_sponsor_id'] = $this->getSanParam ( 'facility_sponsor_id' );
		$criteria ['facilityInput'] = $this->getSanParam ( 'facilityInput' );
		$criteria ['is_tot'] = $this->getSanParam ( 'is_tot' );
		$criteria ['funding_id'] = $this->getSanParam ( 'funding_id' );
		$criteria ['training_organizer_id'] = $this->getSanParam ( 'training_organizer_id' );
		$criteria ['training_organizer_option_id'] = $this->getSanParam ( 'training_organizer_option_id' );
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['qualification_secondary_id'] = $this->getSanParam ( 'qualification_secondary_id' );

		$criteria ['doCount'] = ($this->view->mode == 'count');
		$criteria ['showProvince'] = ($this->getSanParam ( 'showProvince' ) or ($criteria ['doCount'] and ($criteria ['province_id'] or $criteria ['province_id'] === '0')));
		$criteria ['showDistrict'] = ($this->getSanParam ( 'showDistrict' ) or ($criteria ['doCount'] and ($criteria ['district_id'] or $criteria ['district_id'] === '0')));
		$criteria ['showRegionC'] = ($this->getSanParam ( 'showRegionC' ) or ($criteria ['doCount'] and ($criteria ['region_c_id'] or ! empty ( $criteria ['region_c_id'] ))));
		$criteria ['showTrainingTitle'] = ($this->getSanParam ( 'showTrainingTitle' ) or ($criteria ['doCount'] and ($criteria ['training_title_option_id'] or $criteria ['training_title_id'] or $criteria ['training_title_option_id'] === '0')));
		$criteria ['showPepfar'] = ($this->getSanParam ( 'showPepfar' ) or ($criteria ['doCount'] and ($criteria ['training_title_option_id'] or $criteria ['training_title_id'] or $criteria ['training_pepfar_id'] === '0')));
		$criteria ['showTopic'] = ($this->getSanParam ( 'showTopic' ) or ($criteria ['doCount'] and ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0')));
		$criteria ['showFacility'] = ($this->getSanParam ( 'showFacility' ) or ($criteria ['doCount'] and $criteria ['facilityInput']));
		$criteria ['showGender'] = ($this->getSanParam ( 'showGender' ) or ($criteria ['doCount'] and $criteria ['training_gender']));
		$criteria ['showActive'] = ($this->getSanParam ( 'showActive' ) or ($criteria ['doCount'] and $criteria ['training_active']));
		$criteria ['showEmail'] = ($this->getSanParam ( 'showEmail' ));
		$criteria ['showPhone'] = ($this->getSanParam ( 'showPhone' ));
		$criteria ['showType'] = ($this->getSanParam ( 'showType' ) or ($criteria ['doCount'] and ($criteria ['facility_type_id'] or $criteria ['facility_type_id'] === '0')));
		$criteria ['showSponsor'] = ($this->getSanParam ( 'showSponsor' ) or ($criteria ['doCount'] and $criteria ['facility_sponsor_id']));
		$criteria ['showTot'] = ($this->getSanParam ( 'showTot' ) or ($criteria ['doCount'] and $criteria ['is_tot'] !== '' or $criteria ['is_tot'] === '0'));
		$criteria ['showFunding'] = ($this->getSanParam ( 'showFunding' ) or ($criteria ['doCount'] and $criteria ['funding_id'] or $criteria ['funding_id'] === '0'));
		$criteria ['showOrganizer'] = ($this->getSanParam ( 'showOrganizer' ) or ($criteria ['doCount'] and ($criteria ['training_organizer_option_id'])));
		$criteria ['showQualPrim'] = ($this->getSanParam ( 'showQualPrim' ) or ($criteria ['doCount'] and ($criteria ['qualification_id'] or $criteria ['qualification_id'] === '0')));
		$criteria ['showQualSecond'] = ($this->getSanParam ( 'showQualSecond' ) or ($criteria ['doCount'] and ($criteria ['qualification_secondary_id'] or $criteria ['qualification_secondary_id'] === '0')));

		$criteria ['go'] = $this->getSanParam ( 'go' );
		if ($criteria ['go']) {

			$sql = 'SELECT ';

			if ($criteria ['doCount']) {
				$sql .= ' COUNT(person_id) as "cnt" ';
			} else {
				if ($criteria ['concatNames'])
				$sql .= ' DISTINCT person_id as "id", CONCAT(first_name, ' . "' '" . ',last_name, ' . "' '" . ', IFNULL(suffix_phrase, ' . "' '" . ')) as "name" ';
				else
				$sql .= ' DISTINCT person_id as "id", IFNULL(suffix_phrase, ' . "' '" . ') as suffix_phrase, last_name, first_name, middle_name  ';
			}
			if ($criteria ['showPhone']) {
				$sql .= ", CASE WHEN (pt.phone_work IS NULL OR pt.phone_work = '') THEN NULL ELSE pt.phone_work END as \"phone_work\", CASE WHEN (pt.phone_home IS NULL OR pt.phone_home = '') THEN NULL ELSE pt.phone_home END as \"phone_home\", CASE WHEN (pt.phone_mobile IS NULL OR pt.phone_mobile = '') THEN NULL ELSE pt.phone_mobile END as \"phone_mobile\" ";
			}
			if ($criteria ['showEmail']) {
				$sql .= ', pt.email ';
			}
			if ($criteria ['showGender']) {
				$sql .= ', pt.gender ';
			}
			if ($criteria ['showAge']) {
				$sql .= ', pt.age ';
			}
			if ($criteria ['showActive']) {
				$sql .= ', pt.active ';
			}
			if ($criteria ['showTrainingTitle']) {
				$sql .= ', pt.training_title, pt.training_start_date ';
			}
			if ($criteria ['showRegionC']) {
				$sql .= ', pt.region_c_name ';
			}
			if ($criteria ['showDistrict']) {
				$sql .= ', pt.district_name ';
			}
			if ($criteria ['showProvince']) {
				$sql .= ', pt.province_name ';
			}


			if ($criteria ['showPepfar']) {
				$sql .= ', tpep.pepfar_category_phrase ';
			}

			if ($criteria ['showOrganizer']) {
				$sql .= ', torg.training_organizer_phrase ';
			}

			if ($criteria ['showFunding']) {
				//if ( $criteria['doCount'] ) {
				$sql .= ', tfund.funding_phrase ';
				//} else {
				//	$sql .= ', GROUP_CONCAT(DISTINCT tfund.funding_phrase ORDER BY funding_phrase) as "funding_phrase" ';
				//}
			}

			if ($criteria ['showTopic']) {
				$sql .= ', ttopic.training_topic_phrase ';
			}

			if ($criteria ['showType']) {
				$sql .= ', fto.facility_type_phrase ';
			}

			if ($criteria ['showSponsor']) {
				$sql .= ', fso.facility_sponsor_phrase ';
			}
			if ($criteria ['showFacility']) {
				$sql .= ', pt.facility_name ';
			}

			if ($criteria ['showTot']) {
				//$sql .= ', pt.is_tot ';
				$sql .= ", IF(pt.is_tot,'" . t ( 'Yes' ) . "','" . t ( 'No' ) . "') AS is_tot";
			}

			if ($criteria ['showQualPrim']) {
				$sql .= ', pq.qualification_phrase ';
			}
			if ($criteria ['showQualSecond']) {
				$sql .= ', pqs.qualification_phrase AS qualification_secondary_phrase';
			}

			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id, true);

			/* Reworked to include suffix
			$sql .= ' FROM (
			SELECT training.*, person.facility_id as "facility_id", person.id as "person_id", person.last_name, person.first_name, person.middle_name, person.phone_work, person.phone_home, person.phone_mobile, person.email, CASE WHEN birthdate  IS NULL OR birthdate = \'0000-00-00\' THEN NULL ELSE ((date_format(now(),\'%Y\') - date_format(birthdate,\'%Y\')) -
			(date_format(now(),\'00-%m-%d\') < date_format(birthdate,\'00-%m-%d\')) ) END as "age", CASE WHEN person.active = \'deceased\' THEN \'inactive\' ELSE person.active END as "active", CASE WHEN person.gender IS NULL THEN \'na\' WHEN person.gender = \'\' THEN \'na\' ELSE person.gender END as "gender", primary_qualification_option_id, tto.training_title_phrase AS training_title,facility.facility_name, facility.type_option_id, facility.sponsor_option_id, l.'.implode(', l.',$field_name).
			' FROM training,person_to_training,facility,person,training_title_option tto, ('.$location_sub_query.') as l WHERE  facility.location_id = l.id AND
			`training`.training_title_option_id = tto.id AND training.id = person_to_training.training_id AND person.id = person_to_training.person_id  AND person.facility_id = facility.id
			) as pt ';
			*/
			$sql .= ' FROM ( SELECT training.*, person.facility_id as "facility_id", person.id as "person_id", person.last_name, IFNULL(suffix_phrase, ' . "' '" . ') as suffix_phrase, ';
			$sql .= '      person.first_name, person.middle_name, person.phone_work, person.phone_home, person.phone_mobile, person.email, ';
			$sql .= '      CASE WHEN birthdate  IS NULL OR birthdate = \'0000-00-00\' THEN NULL ELSE ((date_format(now(),\'%Y\') - date_format(birthdate,\'%Y\')) - (date_format(now(),\'00-%m-%d\') < date_format(birthdate,\'00-%m-%d\')) ) END as "age", ';
			$sql .= '      CASE WHEN person.active = \'deceased\' THEN \'inactive\' ELSE person.active END as "active", ';
			$sql .= '      CASE WHEN person.gender IS NULL THEN \'na\' WHEN person.gender = \'\' THEN \'na\' ELSE person.gender END as "gender", ';
			$sql .= '      primary_qualification_option_id, tto.training_title_phrase AS training_title,facility.facility_name, facility.type_option_id, facility.sponsor_option_id, ';
			$sql .= '      l.'.implode(', l.',$field_name);
			$sql .= ' FROM training INNER JOIN training_title_option tto ON training.training_title_option_id = tto.id ';
			$sql .= ' INNER JOIN person_to_training ON training.id = person_to_training.training_id ';
			$sql .= ' INNER JOIN person ON person.id = person_to_training.person_id ';
			$sql .= ' INNER JOIN facility ON person.facility_id = facility.id ';
			$sql .= ' LEFT JOIN ('.$location_sub_query.') as l ON facility.location_id = l.id';
			$sql .= ' LEFT  JOIN person_suffix_option suffix ON person.suffix_option_id = suffix.id ';
			$sql .= ' ) as pt ';


			if ($criteria ['showPepfar']) {
				$sql .= '	JOIN (SELECT training_id, ttpco.training_pepfar_categories_option_id, pepfar_category_phrase FROM training_to_training_pepfar_categories_option as ttpco JOIN training_pepfar_categories_option as tpco ON ttpco.training_pepfar_categories_option_id = tpco.id) as tpep ON tpep.training_id = pt.id ';
			}

			if ($criteria ['showTopic']) {
				$sql .= '	LEFT JOIN (SELECT training_id, ttto.training_topic_option_id, training_topic_phrase FROM training_to_training_topic_option as ttto JOIN training_topic_option as tto ON ttto.training_topic_option_id = tto.id) as ttopic ON ttopic.training_id = pt.id ';
				//$sql .= '	LEFT JOIN training_topic_option as ttopic ON ttopic.id = ttopic.training_topic_option_id ';
			}

			if ($criteria ['showType']) {
				$sql .= '	JOIN facility_type_option as fto ON fto.id = pt.type_option_id ';
			}

			if ($criteria ['showSponsor']) {
				$sql .= '	JOIN facility_sponsor_option as fso ON fso.id = pt.sponsor_option_id ';
			}

			if ($criteria ['showOrganizer']) {
				$sql .= '	JOIN training_organizer_option as torg ON torg.id = pt.training_organizer_option_id ';
			}

			if ($criteria ['showFunding']) {
				$sql .= '	LEFT JOIN (SELECT training_id, ttfo.training_funding_option_id, funding_phrase FROM training_to_training_funding_option as ttfo JOIN training_funding_option as tfo ON ttfo.training_funding_option_id = tfo.id) as tfund ON tfund.training_id = pt.id ';
			}

			if ($criteria ['showQualPrim'] || $criteria ['showQualSecond']) {
				// primary qualifications
				$sql .= '
				LEFT JOIN person_qualification_option as pq ON (
				(pt.primary_qualification_option_id = pq.id AND pq.parent_id IS NULL)
				OR
				pq.id = (SELECT parent_id FROM person_qualification_option WHERE id = pt.primary_qualification_option_id LIMIT 1)
				)';

				// secondary qualifications
				$sql .= '
				LEFT JOIN person_qualification_option as pqs ON (
				pt.primary_qualification_option_id = pqs.id AND pqs.parent_id IS NOT NULL
				)';
			}

			$where = array();

			$where []= 'pt.is_deleted=0 ';

			if ($criteria ['age_min']) {
				$where []= ' pt.age >= '.$criteria['age_min'];
			}
			if ($criteria ['age_max']) {
				$where []= ' pt.age <= '.$criteria['age_max'];
			}

			// legacy
			if ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0') {
				$where []= ' pt.training_title_option_id = ' . $criteria ['training_title_option_id'];
			}

			if ($criteria ['training_title_id'] or $criteria ['training_title_id'] === '0') {
				$where []= ' pt.training_title_option_id = ' . $criteria ['training_title_id'];
			}

			if ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0') {
				$where []= ' ttopic.training_topic_option_id = \'' . $criteria ['training_topic_id'] . '\'';
			}
			/*
			if ( $criteria['qualification_id'] or $criteria['qualification_id'] === '0'  ) {
			if ( strlen($where) ) $where .= ' AND ';
			$where .= ' pt.primary_qualification_option_id = \''.$criteria['qualification_id'].'\'' ;
			}
			*/
			if ($criteria ['qualification_id']) {
				$where []= ' (pq.id = ' . $criteria ['qualification_id'] . ' OR pqs.parent_id = ' . $criteria ['qualification_id'] . ') ';
			}
			if ($criteria ['qualification_secondary_id']) {
				$where []= ' pqs.id = ' . $criteria ['qualification_secondary_id'];
			}

			if ($criteria ['training_pepfar_id'] or $criteria ['training_pepfar_id'] === '0') {
				$where []= ' tpep.training_pepfar_categories_option_id = \'' . $criteria ['training_pepfar_id'] . '\'';
			}

			if ($criteria ['facilityInput']) {
				$where []= ' pt.facility_id = \'' . $criteria ['facilityInput'] . '\'';
			}

			if ($criteria ['facility_type_id'] or $criteria ['facility_type_id'] === '0') {
				$where []= ' pt.type_option_id = \'' . $criteria ['facility_type_id'] . '\'';
			}
			if ($criteria ['facility_sponsor_id'] or $criteria ['facility_sponsor_id'] === '0') {
				$where []= ' pt.sponsor_option_id = \'' . $criteria ['facility_sponsor_id'] . '\'';
			}

			if ($criteria ['training_gender']) {
				$where []= ' pt.gender = \'' . $criteria ['training_gender'] . '\'';
			}

			if ($criteria ['training_active']) {
				$where []= ' pt.active = \'' . $criteria ['training_active'] . '\'';
			}

			if ($criteria ['training_organizer_id'] or $criteria ['training_organizer_id'] === '0') {
				$where []= ' pt.training_organizer_option_id = \'' . $criteria ['training_organizer_id'] . '\'';
			}

			if ($criteria ['training_organizer_option_id'] && is_array ( $criteria ['training_organizer_option_id'] )) {
				$where []= ' pt.training_organizer_option_id IN (' . implode ( ',', $criteria ['training_organizer_option_id'] ) . ')';
			}

			if ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0') {
				$where []= ' ttopic.training_topic_option_id = \'' . $criteria ['training_topic_id'] . '\'';
			}

			if ($criteria ['is_tot'] or $criteria ['is_tot'] === '0') {
				$where []= ' pt.is_tot = ' . $criteria ['is_tot'];
			}

			if ($criteria ['training_organizer_id'] or $criteria ['training_organizer_id'] === '0') {
				$where []= ' pt.training_organizer_option_id = \'' . $criteria ['training_organizer_id'] . '\'';
			}

			if ($criteria ['funding_id'] or $criteria ['funding_id'] === '0') {
				$where []= ' tfund.training_funding_option_id = \'' . $criteria ['funding_id'] . '\'';
			}

			if (intval ( $criteria ['end-year'] ) and $criteria ['start-year']) {
				$startDate = $criteria ['start-year'] . '-' . $criteria ['start-month'] . '-' . $criteria ['start-day'];
				$endDate = $criteria ['end-year'] . '-' . $criteria ['end-month'] . '-' . $criteria ['end-day'];
				$where []= ' training_start_date >= \'' . $startDate . '\'  AND training_start_date <= \'' . $endDate . '\'  ';
			}

			if ($where)
			$sql .= ' WHERE ' . implode(' AND ',$where);

			$rowArray = $db->fetchAll ( $sql );

			if ($criteria ['doCount']) {
				$count = 0;
				foreach ( $rowArray as $row ) {
					$count += $row ['cnt'];
				}
			} else {
				$count = count ( $rowArray );
			}
			if ($this->_getParam ( 'outputType' ))
			$this->sendData ( $this->reportHeaders ( false, $rowArray ) );

		} else {
			$count = 0;
			$rowArray = array ();
		}

		$criteria ['go'] = $this->getSanParam ( 'go' );

		$this->viewAssignEscaped ( 'results', $rowArray );
		if ($rowArray) {
			$first = reset ( $rowArray );
			if (isset ( $first ['phone_work'] )) {
				foreach ( $rowArray as $key => $val ) {
					$phones = array ();
					if ($val ['phone_work'])
					$phones [] = str_replace ( ' ', '&nbsp;', trim ( $val ['phone_work'] ) ) . '&nbsp;(w)';
					if ($val ['phone_home'])
					$phones [] = str_replace ( ' ', '&nbsp;', trim ( $val ['phone_home'] ) ) . '&nbsp;(h)';
					if ($val ['phone_mobile'])
					$phones [] = str_replace ( ' ', '&nbsp;', trim ( $val ['phone_mobile'] ) ) . '&nbsp;(m)';
					$rowArray [$key] ['phone'] = implode ( ', ', $phones );
				}
				$this->view->assign ( 'results', $rowArray );
			}
		}

		$this->view->assign ( 'count', $count );
		$this->view->assign ( 'criteria', $criteria );

		//locations
		$locations = Location::getAll();
		$this->viewAssignEscaped('locations',$locations);
		//courses
		$courseArray = TrainingTitleOption::suggestionList ( false, 10000 );
		$this->viewAssignEscaped ( 'courses', $courseArray );
		//topics
		$topicsArray = OptionList::suggestionList ( 'training_topic_option', 'training_topic_phrase', false, false, false );
		$this->viewAssignEscaped ( 'topics', $topicsArray );
		//qualifications (primary)
		$qualsArray = OptionList::suggestionList ( 'person_qualification_option', 'qualification_phrase', false, false, false, 'parent_id IS NULL' );
		$this->viewAssignEscaped ( 'qualifications_primary', $qualsArray );
		//qualifications (secondary)
		$qualsArray = OptionList::suggestionList ( 'person_qualification_option', 'qualification_phrase', false, false, false, 'parent_id IS NOT NULL' );
		$this->viewAssignEscaped ( 'qualifications_secondary', $qualsArray );

		//pepfar
		$organizersArray = OptionList::suggestionList ( 'training_pepfar_categories_option', 'pepfar_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'pepfars', $organizersArray );
		//facility types
		$typesArray = OptionList::suggestionList ( 'facility_type_option', 'facility_type_phrase', false, false );
		$this->viewAssignEscaped ( 'facility_types', $typesArray );
		//sponsor types
		$sponsorsArray = OptionList::suggestionList ( 'facility_sponsor_option', 'facility_sponsor_phrase', false, false );
		$this->viewAssignEscaped ( 'facility_sponsors', $sponsorsArray );
		//organizers
		$organizersArray = OptionList::suggestionList ( 'training_organizer_option', 'training_organizer_phrase', false, false, false );
		$this->viewAssignEscaped ( 'organizers', $organizersArray );
		//funding
		$fundingArray = OptionList::suggestionList ( 'training_funding_option', 'funding_phrase', false, false, false );
		$this->viewAssignEscaped ( 'funding', $fundingArray );
		//organizers
		$this->view->assign ( 'organizers_checkboxes', Checkboxes::generateHtml ( 'training_organizer_option', 'training_organizer_phrase', $this->view ) );
		//facilities list
		$rowArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $rowArray as $key => $val ) {
			if ($val ['id'] != 0)
			$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );

	}

	public function trainersAction() {
		require_once ('models/table/Person.php');
		require_once ('models/table/Trainer.php');

		$criteria = array ();
		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);

		$criteria ['skill_id'] = $this->getSanParam ( 'trainer_skill_id' );
		if (is_array ( $criteria ['skill_id'] ) && $criteria ['skill_id'] [0] === "") { // "All"
			$criteria ['skill_id'] = array ();
		}

		$criteria ['concatNames'] = $this->getSanParam ( 'concatNames' );
		$criteria ['type_id'] = $this->getSanParam ( 'trainer_type_id' );
		$criteria ['language_id'] = $this->getSanParam ( 'trainer_language_id' );
		$criteria ['training_topic_option_id'] = $this->getSanParam ( 'training_topic_option_id' ); // checkboxes
		$criteria ['go'] = $this->getSanParam ( 'go' );

		if ($criteria ['go']) {

			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$sql = " SELECT DISTINCT " . " `person`.`id`, ";
			if ($criteria ['concatNames']) {
				$sql .= " CONCAT( `person`.`first_name`, ' ',`person`.`last_name`) as \"name\", ";
			} else {
				$sql .= " `person`.`first_name`, ";
				$sql .= " `person`.`last_name`, ";
				$sql .= " `person`.`middle_name`, ";
				$sql .= " IFNULL(`person_suffix_option`.`suffix_phrase`, ' ') as `suffix_phrase`, ";
			}

			// get training topics taught
			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id);


			$sql .= " `t`.`type_option_id`, " . " `tt`.`trainer_type_phrase`, " . " `ts`.`trainer_skill_phrase`,	 `tl`.`language_phrase`, " . " topics.training_topic_phrase, l.".implode(', l.',$field_name).
			" FROM `person` " . " JOIN `trainer` AS `t` ON t.person_id = person.id " .
			" JOIN `facility` as f ON person.facility_id = f.id  JOIN (".$location_sub_query.") as l ON f.location_id = l.id " .
			" LEFT JOIN `trainer_type_option` AS `tt` ON t.type_option_id = tt.id " .
			" LEFT JOIN (SELECT trainer_id, trainer_skill_phrase, trainer_skill_option_id FROM trainer_to_trainer_skill_option JOIN trainer_skill_option ON trainer_to_trainer_skill_option.trainer_skill_option_id = trainer_skill_option.id) as ts ON t.person_id = ts.trainer_id " .
			" LEFT JOIN (SELECT trainer_id, language_phrase, trainer_language_option_id FROM trainer_to_trainer_language_option JOIN trainer_language_option ON trainer_to_trainer_language_option.trainer_language_option_id = trainer_language_option.id) as tl ON t.person_id = tl.trainer_id " .
			" LEFT JOIN `person_suffix_option` ON `person`.`suffix_option_id` = `person_suffix_option`.id ";

			$sql .= " LEFT JOIN (

			SELECT ttt.trainer_id, training_topic_phrase, training_topic_option_id
			FROM training_to_trainer ttt
			JOIN training_to_training_topic_option ttto ON ttto.training_id = ttt.training_id
			JOIN training_topic_option tto ON tto.id = ttto.training_topic_option_id
			) as topics ON t.person_id = topics.trainer_id ";

			$where = array();

			if ($criteria ['type_id'] or ($criteria ['type_id'] === '0')) {
				$where []= ' t.type_option_id = ' . $criteria ['type_id'];
			}

			if (! empty ( $criteria ['skill_id'] )) {
				$where []= ' trainer_skill_option_id IN (' . implode ( ',', $criteria ['skill_id'] ) . ')';
			}

			if ($criteria ['language_id']) {
				$where []= ' trainer_language_option_id = ' . $criteria ['language_id'];
			}

			if ($criteria ['training_topic_option_id']) {
				$where []= ' training_topic_option_id IN (' . implode ( ',', $criteria ['training_topic_option_id'] ) . ')';
			}

			if ($where)
			$sql .= ' WHERE ' . implode(' AND ',$where);

			//$sql .= ' GROUP BY person.id ';


			$sql .= " ORDER BY " . " `person`.`last_name` ASC, " . " `person`.`first_name` ASC ";

			//	echo $sql; exit;


			$rowArray = $db->fetchAll ( $sql );
			if ($this->_getParam ( 'outputType' ))
			$this->sendData ( $this->reportHeaders ( false, $rowArray ) );

		} else {
			$rowArray = array ();
		}

		$this->viewAssignEscaped ( 'results', $rowArray );
		$this->view->assign ( 'count', count ( $rowArray ) );
		$this->view->assign ( 'criteria', $criteria );

		//locations
		$locations = Location::getAll();
		$this->viewAssignEscaped ( 'locations', $locations );
		//types
		$trainerTypesArray = OptionList::suggestionList ( 'trainer_type_option', 'trainer_type_phrase', false, false, false );
		$this->viewAssignEscaped ( 'types', $trainerTypesArray );
		//skillz
		$trainerSkillsArray = OptionList::suggestionList ( 'trainer_skill_option', 'trainer_skill_phrase', false, false );
		$this->viewAssignEscaped ( 'skills', $trainerSkillsArray );
		//languages
		$trainerLanguagesArray = OptionList::suggestionList ( 'trainer_language_option', 'language_phrase', false, false );
		$this->viewAssignEscaped ( 'language', $trainerLanguagesArray );
		//topics
		$this->view->assign ( 'topic_checkboxes', Checkboxes::generateHtml ( 'training_topic_option', 'training_topic_phrase', $this->view ) );

	}
	/*
	public function participantsByCategoryAction() {

	$criteria = array();
	$criteria['start-year'] = date('Y');
	$criteria['start-month'] = date('m');
	$criteria['start-day'] = '01';

	$criteria['cat'] = $this->getSanParam('cat');
	if ( $this->getSanParam('start-year') )
	$criteria['start-year'] = $this->getSanParam('start-year');
	if ( $this->getSanParam('start-month') )
	$criteria['start-month'] = $this->getSanParam('start-month');
	if ( $this->getSanParam('start-day') )
	$criteria['start-day'] = $this->getSanParam('start-day');

	//province
	$provinceArray = OptionList::suggestionList('location_province','province_name',false,false);
	$this->view->assign('provinces',$provinceArray);
	//district
	$districtArray = OptionList::suggestionList('location_district',array('district_name','parent_province_id'),false,false);
	$this->view->assign('districts',$districtArray);
	//http://localhost/itech/web/html/reports/participantsByCategory/cat/pepfar?province_id=&district_id=&start-month=&start-day=&start-year=&go=Preview

	$criteria['district_id'] = $this->getSanParam('district_id');
	$criteria['province_id'] = $this->getSanParam('province_id');

	//Q1 query UNION
	//Q2 query UNION
	//Q3 query UNION
	//Q4 query

	//make sure the date doesn't go back too far
	if ( $criteria['start-year'] < 2000 ) {
	$criteria['start-year'] = 2000;
	}

	$qDate = $criteria['start-year'].'-'.$criteria['start-month'].'-'.$criteria['start-day'];
	$results = array();
	if ( $this->getSanParam('go') ) {
	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
	$grandTotal = 0;
	while( $qDate != '' AND (strtotime($qDate) < time()) ) {
	switch ( $criteria['cat'] ) {
	case 'level':
	$sql = 'SELECT count(DISTINCT ptt.id) as "cnt", tlo.training_level_phrase as "cat", \''.$qDate.'\'+ INTERVAL 3 MONTH - INTERVAL 1 DAY as "quarter_end",  \''.$qDate.'\'+ INTERVAL 3 MONTH as "next_quarter_start" FROM '.
	' person_to_training as ptt JOIN training as t ON ptt.training_id = t.id ';
	$sql .= ' LEFT JOIN training_level_option as tlo ON t.training_level_option_id = tlo.id ';
	break;
	case 'qualification':
	$sql = 'SELECT count(DISTINCT ptt.id) as "cnt", pqo.qualification_phrase as "cat", \''.$qDate.'\'+ INTERVAL 3 MONTH - INTERVAL 1 DAY as "quarter_end",  \''.$qDate.'\'+ INTERVAL 3 MONTH as "next_quarter_start" FROM '.
	' person_to_training as ptt JOIN training as t ON ptt.training_id = t.id ';
	$sql .= '  JOIN person as p ON ptt.person_id = p.id ';
	$sql .= ' LEFT JOIN person_qualification_option as pqo ON p.primary_qualification_option_id = pqo.id ';
	break;
	case 'pepfar':
	$sql = 'SELECT count(DISTINCT ptt.id) as "cnt", pfr.pepfar_category_phrase as "cat", \''.$qDate.'\'+ INTERVAL 3 MONTH - INTERVAL 1 DAY as "quarter_end",  \''.$qDate.'\'+ INTERVAL 3 MONTH as "next_quarter_start" FROM '.
	' person_to_training as ptt JOIN training as t ON ptt.training_id = t.id ';
	$sql .= ' LEFT JOIN training_to_training_pepfar_categories_option as tpfr ON t.id = tpfr.training_id ';
	$sql .= ' LEFT JOIN training_pepfar_categories_option as pfr ON tpfr.training_pepfar_categories_option_id = pfr.id ';
	break;
	}

	if ( $district_id =  $this->getSanParam('district_id')) {
	$sql .= '  JOIN training_location as tl ON t.training_location_id = tl.id AND tl.location_district_id = '.$district_id;
	}
	else if ( $province_id = $this->getSanParam('province_id') ) {
	$sql .= '  JOIN training_location as tl ON t.training_location_id = tl.id AND tl.location_province_id = '.$province_id;
	}

	$sql .= ' WHERE training_start_date >= \''.$qDate.'\'  AND training_start_date < \''.$qDate.'\'+ INTERVAL 3 MONTH ';
	$sql .=	 ' GROUP BY cat ORDER BY cat ASC ';

	$rowArray = $db->fetchAll($sql);

	//add a total row
	if ( !$rowArray ) { //we always want the next start date
	$sql = 'SELECT 0 as "cnt", \'total\' as "cat", \''.$qDate.'\'+ INTERVAL 3 MONTH - INTERVAL 1 DAY as "quarter_end",  \''.$qDate.'\'+ INTERVAL 3 MONTH as "next_quarter_start"';
	$rowArray = $db->fetchAll($sql);
	} else {
	$total = 0;
	foreach($rowArray as $row) {
	$total += $row['cnt'];
	}

	$rowArray []= array('cat'=>'total', 'cnt'=>$total);
	$grandTotal += $total;
	}


	$results[$qDate] = $rowArray;
	$qDate = $rowArray[0]['next_quarter_start'];

	}
	if ( $this->_getParam('outputType')  ) $this->sendData($results);
	}

	$this->view->assign('count',(isset($grandTotal)?$grandTotal:0) );
	$this->view->assign('results', $results);
	$this->view->assign('criteria',$criteria);
	}
	*/

	public function participantsByCategoryAction() {

		$criteria = array ();

		//find the first date in the database
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT MIN(training_start_date) as \"start\" FROM training WHERE is_deleted = 0 ";
		$rowArray = $db->fetchAll ( $sql );
		$start_default = $rowArray [0] ['start'];
		$parts = explode ( '-', $start_default );
		$criteria ['start-year'] = $parts [0];
		$criteria ['start-month'] = $parts [1];
		$criteria ['start-day'] = $parts [2];

		$criteria ['end-year'] = date ( 'Y' );
		$criteria ['end-month'] = date ( 'm' );
		$criteria ['end-day'] = date ( 'd' );

		$criteria ['cat'] = $this->getSanParam ( 'cat' );
		if ($this->getSanParam ( 'start-year' ))
		$criteria ['start-year'] = $this->getSanParam ( 'start-year' );
		if ($this->getSanParam ( 'start-month' ))
		$criteria ['start-month'] = $this->getSanParam ( 'start-month' );
		if ($this->getSanParam ( 'start-day' ))
		$criteria ['start-day'] = $this->getSanParam ( 'start-day' );
		if ($this->getSanParam ( 'end-year' ))
		$criteria ['end-year'] = $this->getSanParam ( 'end-year' );
		if ($this->getSanParam ( 'end-month' ))
		$criteria ['end-month'] = $this->getSanParam ( 'end-month' );
		if ($this->getSanParam ( 'end-day' ))
		$criteria ['end-day'] = $this->getSanParam ( 'end-day' );

		//locations
		$locations = Location::getAll();
		$this->view->assign('locations', $locations);

		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);

		$criteria ['training_organizer_option_id'] = $this->getSanParam ( 'training_organizer_option_id' );

		//Q1 query UNION
		//Q2 query UNION
		//Q3 query UNION
		//Q4 query


		//make sure the date doesn't go back too far
		//if ( $criteria['start-year'] < 2000 ) {
		//	$criteria['start-year'] = 2000;
		//}


		$qDate = $criteria ['start-year'] . '-' . $criteria ['start-month'] . '-' . $criteria ['start-day'];
		$endDate = $criteria ['end-year'] . '-' . $criteria ['end-month'] . '-' . $criteria ['end-day'];

		$selectFields = array ();
		if ($criteria ['training_organizer_option_id']) {
			$selectFields [] = 'training_organizer_phrase';
		}

		if ($selectFields) {
			$selectFields = ', ' . implode ( ',', $selectFields );
		} else {
			$selectFields = '';
		}


		$rowArray = array ();
		if ($this->getSanParam ( 'go' )) {
			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			switch ($criteria ['cat']) {
				case 'level' :
				$sql = 'SELECT count(DISTINCT ptt.id) as "cnt", count(DISTINCT ptt.person_id) as "person_cnt", tlo.training_level_phrase as "cat" ' . $selectFields . ' FROM ' . ' person_to_training as ptt JOIN training as t ON ptt.training_id = t.id ';
				$sql .= '  JOIN person as p ON ptt.person_id = p.id ';
				$sql .= ' LEFT JOIN training_level_option as tlo ON t.training_level_option_id = tlo.id ';
				break;
				case 'qualification' :
				$sql = 'SELECT count(DISTINCT ptt.id) as "cnt", count(DISTINCT ptt.person_id) as "person_cnt", pqo.qualification_phrase as "cat" ' . $selectFields . ' FROM ' . ' person_to_training as ptt JOIN training as t ON ptt.training_id = t.id ';
				$sql .= '  JOIN person as p ON ptt.person_id = p.id ';
				//$sql .= ' LEFT JOIN person_qualification_option as pqo ON p.primary_qualification_option_id = pqo.id ';


				// primary qualifications only
				$sql .= '
				LEFT JOIN person_qualification_option as pqo ON (
				(p.primary_qualification_option_id = pqo.id AND pqo.parent_id IS NULL)
				OR
				pqo.id = (SELECT parent_id FROM person_qualification_option WHERE id = p.primary_qualification_option_id LIMIT 1)
				)';
				break;

				case 'pepfar' :
				$sql = 'SELECT count(DISTINCT ptt.id) as "cnt", count(DISTINCT ptt.person_id) as "person_cnt", pfr.pepfar_category_phrase as "cat" ' . $selectFields . ' FROM ' . ' person_to_training as ptt JOIN training as t ON ptt.training_id = t.id ';
				$sql .= '  JOIN person as p ON ptt.person_id = p.id ';
				$sql .= ' LEFT JOIN training_to_training_pepfar_categories_option as tpfr ON t.id = tpfr.training_id ';
				$sql .= ' LEFT JOIN training_pepfar_categories_option as pfr ON tpfr.training_pepfar_categories_option_id = pfr.id ';
				break;
			}

			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id, true);

			if ($criteria ['district_id'] OR ! empty ( $criteria ['province_id'] ) OR ! empty ( $criteria ['region_c_id'] )) {
				$sql .= '  JOIN facility as f ON p.facility_id = f.id JOIN (' . $location_sub_query . ') as l ON  f.location_id = l.id ';
			}

			if ($criteria ['training_organizer_option_id']) {
				$sql .= '	JOIN training_organizer_option as torg ON torg.id = t.training_organizer_option_id ';
			}

			$sql .= ' WHERE training_start_date >= \'' . $qDate . '\'  AND training_start_date <= \'' . $endDate . '\' ';

			if ($criteria ['training_organizer_option_id'] && is_array ( $criteria ['training_organizer_option_id'] )) {
				$sql .= ' AND t.training_organizer_option_id IN (' . implode ( ',', $criteria ['training_organizer_option_id'] ) . ')';
			}

			$sql .= ' GROUP BY cat ';

			if ($criteria ['training_organizer_option_id'] && is_array ( $criteria ['training_organizer_option_id'] )) {
				$sql .= ', t.training_organizer_option_id ';
			}

			$sql .= ' ORDER BY cat ASC ';

			$rowArray = $db->fetchAll ( $sql );

			//add a total row
			$total = 0;
			foreach ( $rowArray as $row ) {
				$total += $row ['cnt'];
			}

			if ($this->_getParam ( 'outputType' ))
			$this->sendData ( $this->reportHeaders ( false, $rowArray ) );
		}

		$this->view->assign ( 'count', (isset ( $total ) ? $total : 0) );
		$this->viewAssignEscaped ( 'results', $rowArray );
		$this->view->assign ( 'criteria', $criteria );

		//organizers
		$this->view->assign ( 'organizers_checkboxes', Checkboxes::generateHtml ( 'training_organizer_option', 'training_organizer_phrase', $this->view ) );

	}

	public function trainingByFacilityAction() {
		$this->view->assign ( 'mode', 'id' );
		$this->facilityReport ();
	}

	public function trainingByFacilityCountAction() {
		$this->view->assign ( 'mode', 'count' );
		$this->facilityReport ();
	}

	public function facilityReport() {

		require_once ('models/table/TrainingLocation.php');

		$criteria = array ();

		//find the first date in the database
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT MIN(training_start_date) as \"start\" FROM training WHERE is_deleted = 0";
		$rowArray = $db->fetchAll ( $sql );
		$start_default = $rowArray [0] ['start'];
		$parts = explode ( '-', $start_default );
		$criteria ['start-year'] = $parts [0];
		$criteria ['start-month'] = $parts [1];
		$criteria ['start-day'] = $parts [2];

		if ($this->getSanParam ( 'start-year' ))
		$criteria ['start-year'] = $this->getSanParam ( 'start-year' );
		if ($this->getSanParam ( 'start-month' ))
		$criteria ['start-month'] = $this->getSanParam ( 'start-month' );
		if ($this->getSanParam ( 'start-day' ))
		$criteria ['start-day'] = $this->getSanParam ( 'start-day' );
		if ($this->view->mode == 'search') {
			$sql = "SELECT MAX(training_start_date) as \"start\" FROM training ";
			$rowArray = $db->fetchAll ( $sql );
			$end_default = $rowArray [0] ['start'];
			$parts = explode ( '-', $end_default );
			$criteria ['end-year'] = $parts [0];
			$criteria ['end-month'] = $parts [1];
			$criteria ['end-day'] = $parts [2];
		} else {
			$criteria ['end-year'] = date ( 'Y' );
			$criteria ['end-month'] = date ( 'm' );
			$criteria ['end-day'] = date ( 'd' );
		}

		if ($this->getSanParam ( 'end-year' ))
		$criteria ['end-year'] = $this->getSanParam ( 'end-year' );
		if ($this->getSanParam ( 'end-month' ))
		$criteria ['end-month'] = $this->getSanParam ( 'end-month' );
		if ($this->getSanParam ( 'end-day' ))
		$criteria ['end-day'] = $this->getSanParam ( 'end-day' );

		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);

		$criteria ['training_title_option_id'] = $this->getSanParam ( 'training_title_option_id' );
		$criteria ['training_title_id'] = $this->getSanParam ( 'training_title_id' );
		$criteria ['training_location_id'] = $this->getSanParam ( 'training_location_id' );
		$criteria ['training_organizer_id'] = $this->getSanParam ( 'training_organizer_id' );
		$criteria ['training_pepfar_id'] = $this->getSanParam ( 'training_pepfar_id' );
		$criteria ['training_topic_id'] = $this->getSanParam ( 'training_topic_id' );
		$criteria ['training_level_id'] = $this->getSanParam ( 'training_level_id' );
		$criteria ['facility_type_id'] = $this->getSanParam ( 'facility_type_id' );
		$criteria ['facility_sponsor_id'] = $this->getSanParam ( 'facility_sponsor_id' );
		$criteria ['facilityInput'] = $this->getSanParam ( 'facilityInput' );
		$criteria ['is_tot'] = $this->getSanParam ( 'is_tot' );

		$criteria ['go'] = $this->getSanParam ( 'go' );
		$criteria ['doCount'] = ($this->view->mode == 'count');
		$criteria ['showProvince'] = ($this->getSanParam ( 'showProvince' ) or ($criteria ['doCount'] and ($criteria ['province_id'] or $criteria ['province_id'] === '0')));
		$criteria ['showDistrict'] = ($this->getSanParam ( 'showDistrict' ) or ($criteria ['doCount'] and ($criteria ['district_id'] or $criteria ['district_id'] === '0')));
		$criteria ['showRegionC'] = ($this->getSanParam ( 'showRegionC' ) or ($criteria ['doCount'] and ($criteria ['region_c_id'] or $criteria ['region_c_id'] === '0')));
		$criteria ['showTrainingTitle'] = ($this->getSanParam ( 'showTrainingTitle' ) or ($criteria ['doCount'] and ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0' or $criteria ['training_title_id'])));
		$criteria ['showLocation'] = ($this->getSanParam ( 'showLocation' ) or ($criteria ['doCount'] and $criteria ['training_location_id']));
		$criteria ['showOrganizer'] = ($this->getSanParam ( 'showOrganizer' ) or ($criteria ['doCount'] and ($criteria ['training_organizer_id'] or $criteria ['training_organizer_id'] === '0')));
		$criteria ['showPepfar'] = ($this->getSanParam ( 'showPepfar' ) or ($criteria ['doCount'] and ($criteria ['training_pepfar_id'] or $criteria ['training_pepfar_id'] === '0')));
		$criteria ['showTopic'] = ($this->getSanParam ( 'showTopic' ) or ($criteria ['doCount'] and ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0')));
		$criteria ['showLevel'] = ($this->getSanParam ( 'showLevel' ) or ($criteria ['doCount'] and $criteria ['training_level_id']));
		$criteria ['showType'] = ($this->getSanParam ( 'showType' ) or ($criteria ['doCount'] and ($criteria ['facility_type_id'] or $criteria ['facility_type_id'] === '0')));
		$criteria ['showSponsor'] = ($this->getSanParam ( 'showSponsor' ) or ($criteria ['doCount'] and $criteria ['facility_sponsor_id']));
		$criteria ['showFacility'] = true; //($this->getSanParam('showFacility') OR ($criteria['doCount'] and $criteria['facility_name']));
		$criteria ['showTot'] = ($this->getSanParam ( 'showTot' ) or ($criteria ['doCount'] and $criteria ['is_tot'] !== '' or $criteria ['is_tot'] === '0'));

		if ($criteria ['go']) {

			$sql = 'SELECT '; //todo test

			if ($criteria ['doCount']) {
				$sql .= ' COUNT(pt.person_id) as "cnt", pt.facility_name ';
			} else {
				$sql .= ' DISTINCT pt.id as "id", pt.facility_name, pt.training_start_date  ';
			}
			if ($criteria ['showFacility']) {
				$sql .= ', pt.facility_name ';
			}

			if ($criteria ['showTrainingTitle'] or ($this->view->mode == 'search')) {
				$sql .= ', pt.training_title ';
			}
			if ($criteria ['showDistrict']) {
				$sql .= ', pt.district_name, pt.district_id ';
			}
			if ($criteria ['showProvince']) {
				$sql .= ', pt.province_name, pt.province_id ';
			}
			if ($criteria ['showRegionC']) {
				$sql .= ', pt.region_c_name, pt.region_c_id ';
			}
			if ($criteria ['showLocation']) {
				$sql .= ', pt.training_location_name ';
			}

			if ($criteria ['showOrganizer']) {
				$sql .= ', torg.training_organizer_phrase ';
			}

			if ($criteria ['showLevel']) {
				$sql .= ', tlev.training_level_phrase ';
			}

			if ($criteria ['showType']) {
				$sql .= ', fto.facility_type_phrase ';
			}

			if ($criteria ['showSponsor']) {
				$sql .= ', fso.facility_sponsor_phrase ';
			}

			if ($criteria ['showPepfar']) {
				if ($criteria ['doCount']) {
					$sql .= ', tpep.pepfar_category_phrase ';
				} else {
					$sql .= ', GROUP_CONCAT(DISTINCT tpep.pepfar_category_phrase) as "pepfar_category_phrase" ';
				}
			}

			if ($criteria ['showTopic']) {
				if ($criteria ['doCount']) {
					$sql .= ', ttopic.training_topic_phrase ';
				} else {
					$sql .= ', GROUP_CONCAT(DISTINCT ttopic.training_topic_phrase ORDER BY training_topic_phrase) AS "training_topic_phrase" ';
				}
			}

			if ($criteria ['showTot']) {
				//$sql .= ', pt.is_tot ';
				$sql .= ", IF(pt.is_tot,'" . t ( 'Yes' ) . "','" . t ( 'No' ) . "') AS is_tot";
			}

			//JOIN with the participants to get facility

			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id, true);

			if ($criteria ['doCount']) {
				$sql .= ' FROM (SELECT training.*, fac.person_id as "person_id", fac.facility_id as "facility_id", fac.type_option_id, fac.sponsor_option_id, fac.facility_name as "facility_name" , tto.training_title_phrase AS training_title,training_location.training_location_name, l.'.implode(', l.',$field_name).
				'       FROM training  ' .
				'         JOIN training_title_option tto ON (`training`.training_title_option_id = tto.id)' .
				'         JOIN training_location ON training.training_location_id = training_location.id ' . '         JOIN (SELECT person_id, facility_name, facility_id, location_id, type_option_id, sponsor_option_id,training_id FROM person JOIN person_to_training ON person_to_training.person_id = person.id '.
				'         JOIN facility as f ON person.facility_id = f.id) as fac ON training.id = fac.training_id JOIN ('.$location_sub_query.') as l ON fac.location_id = l.id WHERE training.is_deleted=0) as pt ';
			} else {
				$sql .= ' FROM (SELECT training.*, fac.facility_id as "facility_id", fac.type_option_id, fac.sponsor_option_id ,fac.facility_name as "facility_name" , tto.training_title_phrase AS training_title,training_location.training_location_name, l.'.implode(', l.',$field_name).
				'       FROM training  ' .
				'         JOIN training_title_option tto ON (`training`.training_title_option_id = tto.id) ' .
				'         JOIN training_location ON training.training_location_id = training_location.id ' . '         JOIN (SELECT DISTINCT facility_name, facility_id, location_id, training_id, type_option_id, sponsor_option_id FROM person JOIN person_to_training ON person_to_training.person_id = person.id '.
				'         JOIN facility as f ON person.facility_id = f.id) as fac ON training.id = fac.training_id JOIN ('.$location_sub_query.') as l ON fac.location_id = l.id  WHERE training.is_deleted=0) as pt ';
			}

			if ($criteria ['showOrganizer']) {
				$sql .= '	JOIN training_organizer_option as torg ON torg.id = pt.training_organizer_option_id ';
			}
			if ($criteria ['showLevel']) {
				$sql .= '	JOIN training_level_option as tlev ON tlev.id = pt.training_level_option_id ';
			}

			if ($criteria ['showType']) {
				$sql .= '	JOIN facility_type_option as fto ON fto.id = pt.type_option_id ';
			}

			if ($criteria ['showSponsor']) {
				$sql .= '	JOIN facility_sponsor_option as fso ON fso.id = pt.sponsor_option_id ';
			}

			if ($criteria ['showPepfar']) {
				$sql .= '	LEFT JOIN (SELECT training_id, ttpco.training_pepfar_categories_option_id, pepfar_category_phrase FROM training_to_training_pepfar_categories_option as ttpco JOIN training_pepfar_categories_option as tpco ON ttpco.training_pepfar_categories_option_id = tpco.id) as tpep ON tpep.training_id = pt.id ';
			}

			if ($criteria ['showTopic']) {
				//$sql .= '	LEFT JOIN training_topic_option as ttopic ON ttopic.id = ttopic.training_topic_option_id ';
				$sql .= '	LEFT JOIN (SELECT training_id, ttto.training_topic_option_id, training_topic_phrase FROM training_to_training_topic_option as ttto JOIN training_topic_option as tto ON ttto.training_topic_option_id = tto.id) as ttopic ON ttopic.training_id = pt.id ';
			}

			$where = array(' pt.is_deleted=0 ');

			if ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0') {
				$where []= ' pt.training_title_option_id = ' . $criteria ['training_title_option_id'];
			}

			if ($criteria ['training_title_id'] or $criteria ['training_title_id'] === '0') {
				$where []= ' pt.training_title_option_id = ' . $criteria ['training_title_id'];
			}

			if ($criteria ['facilityInput']) {
				$where []= ' pt.facility_id = \'' . $criteria ['facilityInput'] . '\'';
			}

			if ($criteria ['training_location_id']) {
				$where []= ' pt.training_location_id = \'' . $criteria ['training_location_id'] . '\'';
			}

			if ($criteria ['training_organizer_id'] or $criteria ['training_organizer_id'] === '0') {
				$where []= ' pt.training_organizer_option_id = \'' . $criteria ['training_organizer_id'] . '\'';
			}

			if ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0') {
				$where []= ' ttopic.training_topic_option_id = \'' . $criteria ['training_topic_id'] . '\'';
			}

			if ($criteria ['facility_type_id'] or $criteria ['facility_type_id'] === '0') {
				$where []= ' pt.type_option_id = \'' . $criteria ['facility_type_id'] . '\'';
			}
			if ($criteria ['facility_sponsor_id'] or $criteria ['facility_sponsor_id'] === '0') {
				$where []= ' pt.sponsor_option_id = \'' . $criteria ['facility_sponsor_id'] . '\'';
			}

			if ($criteria ['training_level_id']) {
				$where []= ' pt.training_level_option_id = \'' . $criteria ['training_level_id'] . '\'';
			}

			if ($criteria ['training_pepfar_id'] or $criteria ['training_pepfar_id'] === '0') {
				$where []= ' tpep.training_pepfar_categories_option_id = \'' . $criteria ['training_pepfar_id'] . '\'';
			}

			if (intval ( $criteria ['end-year'] ) and $criteria ['start-year']) {
				$startDate = $criteria ['start-year'] . '-' . $criteria ['start-month'] . '-' . $criteria ['start-day'];
				$endDate = $criteria ['end-year'] . '-' . $criteria ['end-month'] . '-' . $criteria ['end-day'];
				$where []= ' training_start_date >= \'' . $startDate . '\'  AND training_start_date <= \'' . $endDate . '\'  ';
			}

			if ($criteria ['is_tot'] or $criteria ['is_tot'] === '0') {
				$where []= ' pt.is_tot = ' . $criteria ['is_tot'];
			}

			if ($where)
			$sql .= ' WHERE ' . implode(' AND ', $where);

			if ($criteria ['doCount']) {

				$groupBy = array();

				if ($criteria ['showFacility']) {
					$groupBy []= '  pt.facility_id';
				}

				if ($criteria ['showTrainingTitle']) {
					$groupBy []= ' pt.training_title_option_id';
				}
				if ($criteria ['showProvince']) {
					$groupBy []= ' pt.province_id';
				}
				if ($criteria ['showDistrict']) {
					$groupBy []= '  pt.district_id';
				}
				if ($criteria ['showRegionC']) {
					$groupBy []= '  pt.region_c_id';
				}
				if ($criteria ['showLocation']) {
					$groupBy []= '  pt.training_location_id';
				}
				if ($criteria ['showOrganizer']) {
					$groupBy []= '  pt.training_organizer_option_id';
				}
				if ($criteria ['showTopic']) {
					$groupBy []= '  ttopic.training_topic_option_id';
				}
				if ($criteria ['showLevel']) {
					$groupBy []= '  pt.training_level_option_id';
				}
				if ($criteria ['showPepfar']) {
					$groupBy []= '  tpep.training_pepfar_categories_option_id';
				}

				if ($criteria ['showType']) {
					$groupBy []= '  pt.type_option_id';
				}
				if ($criteria ['showSponsor']) {
					$groupBy []= '  pt.sponsor_option_id';
				}
				if ($criteria ['showTot']) {
					$groupBy []= '  pt.is_tot';
				}

				if ($groupBy)
				$groupBy = ' GROUP BY ' . implode(', ',$groupBy);
				$sql .= $groupBy;
			} else {
				if ($criteria ['showPepfar'] || $criteria ['showTopic']) {
					$sql .= ' GROUP BY pt.id';
				}
			}

			$rowArray = $db->fetchAll ( $sql . ' ORDER BY facility_name ASC ' );

			if ($criteria ['doCount']) {
				$count = 0;
				foreach ( $rowArray as $row ) {
					$count += $row ['cnt'];
				}
			} else {
				$count = count ( $rowArray );
			}

			if ($this->_getParam ( 'outputType' ))
			$this->sendData ( $this->reportHeaders ( false, $rowArray ) );

		} else {
			$count = 0;
			$rowArray = array ();
		}

		$criteria ['go'] = $this->getSanParam ( 'go' );

		//not sure why we are getting multiple PEPFARS
		foreach ( $rowArray as $key => $row ) {
			if (isset ( $row ['pepfar_category_phrase'] )) {
				$rowArray [$key] ['pepfar_category_phrase'] = implode ( ',', array_unique ( explode ( ',', $row ['pepfar_category_phrase'] ) ) );
			}
		}

		$this->viewAssignEscaped ( 'results', $rowArray );
		$this->view->assign ( 'count', $count );
		$this->view->assign ( 'criteria', $criteria );

		//facilities list
		$fArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $fArray as $key => $val ) {
			if ($val ['id'] != 0)
			$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );

		//locations
		$locations = Location::getAll();
		$this->viewAssignEscaped ( 'locations', $locations );
		//facility types
		$typesArray = OptionList::suggestionList ( 'facility_type_option', 'facility_type_phrase', false, false );
		$this->viewAssignEscaped ( 'facility_types', $typesArray );
		//sponsor types
		$sponsorsArray = OptionList::suggestionList ( 'facility_sponsor_option', 'facility_sponsor_phrase', false, false );
		$this->viewAssignEscaped ( 'facility_sponsors', $sponsorsArray );

		//course
		$courseArray = TrainingTitleOption::suggestionList ( false, 10000 );
		$this->viewAssignEscaped ( 'courses', $courseArray );

		//location
		// location drop-down
		$tlocations = TrainingLocation::selectAllLocations ($this->setting('num_location_tiers'));
		$this->viewAssignEscaped ( 'tlocations', $tlocations );
		//organizers
		$organizersArray = OptionList::suggestionList ( 'training_organizer_option', 'training_organizer_phrase', false, false, false );
		$this->viewAssignEscaped ( 'organizers', $organizersArray );
		//topics
		$topicsArray = OptionList::suggestionList ( 'training_topic_option', 'training_topic_phrase', false, false, false );
		$this->viewAssignEscaped ( 'topics', $topicsArray );
		//levels
		$levelArray = OptionList::suggestionList ( 'training_level_option', 'training_level_phrase', false, false );
		$this->viewAssignEscaped ( 'levels', $levelArray );
		//pepfar
		$organizersArray = OptionList::suggestionList ( 'training_pepfar_categories_option', 'pepfar_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'pepfars', $organizersArray );

		//facilities list
		$rowArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $rowArray as $key => $val ) {
			if ($val ['id'] != 0)
			$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );
	}

	//
	// Recommended Classes
	//


	public function needsReport() {
		require_once ('models/table/TrainingRecommend.php');
		require_once ('models/table/TrainingTitleOption.php');

		$criteria = array ();

		//find the first date in the database
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT MIN(training_start_date) as \"start\" FROM training WHERE is_deleted = 0";
		$rowArray = $db->fetchAll ( $sql );
		$start_default = '0000-00-00';
		if ($rowArray and $rowArray [0] ['start'])
		$start_default = $rowArray [0] ['start'];
		$parts = explode ( '-', $start_default );
		$criteria ['start-year'] = $parts [0];
		$criteria ['start-month'] = $parts [1];
		$criteria ['start-day'] = $parts [2];

		if ($this->getSanParam ( 'start-year' ))
		$criteria ['start-year'] = $this->getSanParam ( 'start-year' );
		if ($this->getSanParam ( 'start-month' ))
		$criteria ['start-month'] = $this->getSanParam ( 'start-month' );
		if ($this->getSanParam ( 'start-day' ))
		$criteria ['start-day'] = $this->getSanParam ( 'start-day' );
		$criteria ['end-year'] = date ( 'Y' );
		$criteria ['end-month'] = date ( 'm' );
		$criteria ['end-day'] = date ( 'd' );
		if ($this->getSanParam ( 'end-year' ))
		$criteria ['end-year'] = $this->getSanParam ( 'end-year' );
		if ($this->getSanParam ( 'end-month' ))
		$criteria ['end-month'] = $this->getSanParam ( 'end-month' );
		if ($this->getSanParam ( 'end-day' ))
		$criteria ['end-day'] = $this->getSanParam ( 'end-day' );

		list($criteria, $location_tier, $location_id) = $this->getLocationCriteriaValues($criteria);

		$criteria ['training_gender'] = $this->getSanParam ( 'training_gender' );
		$criteria ['training_active'] = $this->getSanParam ( 'training_active' );
		$criteria ['concatNames'] = $this->getSanParam ( 'concatNames' );
		$criteria ['training_title_option_id'] = $this->getSanParam ( 'training_title_option_id' );
		$criteria ['training_title_id'] = $this->getSanParam ( 'training_title_id' );
		$criteria ['course_recommend_id'] = $this->getSanParam ( 'course_recommend_id' );
		$criteria ['training_pepfar_id'] = $this->getSanParam ( 'training_pepfar_id' );
		$criteria ['training_topic_id'] = $this->getSanParam ( 'training_topic_id' );
		$criteria ['training_topic_recommend_id'] = $this->getSanParam ( 'training_topic_recommend_id' );
		$criteria ['qualification_id'] = $this->getSanParam ( 'qualification_id' );
		$criteria ['qualification_secondary_id'] = $this->getSanParam ( 'qualification_secondary_id' );
		$criteria ['upcoming_id'] = $this->getSanParam ( 'upcoming_id' );
		$criteria ['facilityInput'] = $this->getSanParam ( 'facilityInput' );
		$criteria ['first_name'] = $this->getSanParam ( 'first_name' );
		$criteria ['last_name'] = $this->getSanParam ( 'last_name' );

		$criteria ['doCount'] = ($this->view->mode == 'count');
		$criteria ['showProvince'] = ($this->getSanParam ( 'showProvince' ) or ($criteria ['doCount'] and ($criteria ['province_id'] or $criteria ['province_id'] === '0')));
		$criteria ['showDistrict'] = ($this->getSanParam ( 'showDistrict' ) or ($criteria ['doCount'] and ($criteria ['district_id'] or $criteria ['district_id'] === '0')));
		$criteria ['showRegionC'] = ($this->getSanParam ( 'showRegionC' ) or ($criteria ['doCount'] and ($criteria ['region_c_id'] or $criteria ['region_c_id'] === '0')));
		$criteria ['showTrainingTitle'] = ($this->getSanParam ( 'showTrainingTitle' ) or ($criteria ['doCount'] and ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0' or $criteria ['training_title_id'])));
		$criteria ['showPepfar'] = ($this->getSanParam ( 'showPepfar' ) or ($criteria ['doCount'] and ($criteria ['training_title_option_id'] or $criteria ['training_pepfar_id'] === '0' or $criteria ['training_title_id'])));
		$criteria ['showQualPrim'] = ($this->getSanParam ( 'showQualPrim' ) or ($criteria ['doCount'] and ($criteria ['qualification_id'] or $criteria ['qualification_id'] === '0')));
		$criteria ['showQualSecond'] = ($this->getSanParam ( 'showQualSecond' ) or ($criteria ['doCount'] and ($criteria ['qualification_secondary_id'] or $criteria ['qualification_secondary_id'] === '0')));
		$criteria ['showTopic'] = ($this->getSanParam ( 'showTopic' ) or ($criteria ['doCount'] and ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0')));
		$criteria ['showTopicRecommend'] = ($this->getSanParam ( 'showTopicRecommend' ) or ($criteria ['doCount'] and ($criteria ['training_topic_recommend_id'] or $criteria ['training_topic_recommend_id'] === '0')));
		$criteria ['showCourseRecommend'] = ($this->getSanParam ( 'showCourseRecommend' ) or ($criteria ['doCount'] and ($criteria ['course_recommend_id'] or $criteria ['course_recommend_id'] === '0')));
		$criteria ['showFacility'] = ($this->getSanParam ( 'showFacility' ) or ($criteria ['doCount'] and $criteria ['facility_name']));
		$criteria ['showGender'] = ($this->getSanParam ( 'showGender' ) or ($criteria ['doCount'] and $criteria ['training_gender']));
		$criteria ['showActive'] = ($this->getSanParam ( 'showActive' ) or ($criteria ['doCount'] and $criteria ['training_active']));
		$criteria ['showEmail'] = ($this->getSanParam ( 'showEmail' ));
		$criteria ['showPhone'] = ($this->getSanParam ( 'showPhone' ));
		$criteria ['showClasses'] = ($this->getSanParam ( 'showPhone' ));
		$criteria ['showUpcoming'] = ($this->getSanParam ( 'showUpcoming' ));

		$criteria ['showFirstName'] = ($this->getSanParam ( 'showFirstName' ));
		$criteria ['showLastName'] = ($this->getSanParam ( 'showLastName' ));

		$criteria ['go'] = $this->getSanParam ( 'go' );
		if ($criteria ['go']) {

			$num_locs = $this->setting('num_location_tiers');
			list($field_name,$location_sub_query) = Location::subquery($num_locs, $location_tier, $location_id, true);

			$sql = 'SELECT ';

			if ($criteria ['doCount']) {
				$sql .= ' COUNT(pt.person_id) as "cnt" ';
			} else {
				if ($criteria ['concatNames'])
				$sql .= ' DISTINCT pt.person_id as "id", CONCAT(first_name, ' . "' '" . ',last_name, ' . "' '" . ', IFNULL(suffix_phrase, ' . "' '" . ')) as "name", IFNULL(suffix_phrase, ' . "' '" . ') as suffix_phrase, pt.training_start_date  ';
				else
				$sql .= ' DISTINCT pt.person_id as "id", IFNULL(suffix_phrase, ' . "' '" . ') as suffix_phrase, last_name, first_name, middle_name, pt.training_start_date  ';
			}
			if ($criteria ['showPhone']) {
				$sql .= ", CASE WHEN (pt.phone_work IS NULL OR pt.phone_work = '') THEN NULL ELSE pt.phone_work END as \"phone_work\", CASE WHEN (pt.phone_home IS NULL OR pt.phone_home = '') THEN NULL ELSE pt.phone_home END as \"phone_home\", CASE WHEN (pt.phone_mobile IS NULL OR pt.phone_mobile = '') THEN NULL ELSE pt.phone_mobile END as \"phone_mobile\" ";
			}
			if ($criteria ['showEmail']) {
				$sql .= ', pt.email ';
			}
			if ($criteria ['showGender']) {
				$sql .= ', pt.gender ';
			}
			if ($criteria ['showActive']) {
				$sql .= ', pt.active ';
			}
			if ($criteria ['showTrainingTitle']) {
				$sql .= ', pt.training_title, pt.training_title_option_id '; // already GROUP_CONCAT'ed in main SQL
			}
			if ($criteria ['showDistrict']) {
				$sql .= ', pt.district_name ';
			}
			if ($criteria ['showProvince']) {
				$sql .= ', pt.province_name ';
			}
			if ($criteria ['showRegionC']) {
				$sql .= ', pt.region_c_name ';
			}

			if ($criteria ['showPepfar']) {
				$sql .= ', tpep.pepfar_category_phrase ';
			}

			if ($criteria ['showTopic']) {
				$sql .= ', ttopic.training_topic_phrase ';
			}

			if ($criteria ['showFacility']) {
				$sql .= ', pt.facility_name ';
			}

			if ($criteria ['showQualPrim']) {
				$sql .= ', pq.qualification_phrase ';
			}
			if ($criteria ['showQualSecond']) {
				$sql .= ', pqs.qualification_phrase AS qualification_secondary_phrase';
			}

			// NOT USED! (recommended topics are, though)
			if ((! $criteria ['doCount']) and $criteria ['showUpcoming']) {
				//$sql .= ', precommend.training_title_phrase AS recommended';
				$sql .= ", GROUP_CONCAT(DISTINCT CONCAT(recommend.training_title_phrase ) ORDER BY training_title_phrase SEPARATOR ', ') AS upcoming ";

			}

			if ($criteria ['showTopicRecommend']) {
				//$sql .= ', ptopic.training_topic_phrase AS recommended ';


				$sql .= ", GROUP_CONCAT(DISTINCT CONCAT(ptopic.training_topic_phrase) ORDER BY training_topic_phrase SEPARATOR ', ') AS recommended ";

				// same training location? --  AND t.training_location_id = pt.training_location_id


			}

			// select everyone, not just participants
			$sql .= ' FROM (
			SELECT training.*, person.facility_id as "facility_id", person.id as "person_id", person.last_name, IFNULL(suffix_phrase, ' . "' '" . ') as suffix_phrase, person.first_name, person.middle_name, person.phone_work, person.phone_home, person.phone_mobile, person.email, CASE WHEN person.active = \'deceased\' THEN \'inactive\' ELSE person.active END as "active", CASE WHEN person.gender IS NULL THEN \'na\' WHEN person.gender = \'\' THEN \'na\' ELSE person.gender END as "gender",
			primary_qualification_option_id,facility.facility_name, l.'.implode(', l.',$field_name).
			', GROUP_CONCAT(DISTINCT CONCAT(training_title_phrase) ORDER BY training_title_phrase SEPARATOR \', \') AS training_title
			FROM person
			LEFT JOIN person_to_training ON person.id = person_to_training.person_id
			LEFT JOIN training ON training.id = person_to_training.training_id
			LEFT JOIN facility ON person.facility_id = facility.id
			LEFT JOIN ('.$location_sub_query.') as l ON facility.location_id = l.id
			LEFT JOIN training_title_option tto ON `training`.training_title_option_id = tto.id
			LEFT  JOIN person_suffix_option suffix ON person.suffix_option_id = suffix.id
			GROUP BY person.id
			) as pt ';

			if ($criteria ['showPepfar']) {
				$sql .= '	JOIN (SELECT training_id, ttpco.training_pepfar_categories_option_id, pepfar_category_phrase FROM training_to_training_pepfar_categories_option as ttpco JOIN training_pepfar_categories_option as tpco ON ttpco.training_pepfar_categories_option_id = tpco.id) as tpep ON tpep.training_id = pt.id ';
			}

			if ($criteria ['showTopic']) {
				$sql .= '	LEFT JOIN (SELECT training_id, ttto.training_topic_option_id, training_topic_phrase FROM training_to_training_topic_option as ttto JOIN training_topic_option as tto ON ttto.training_topic_option_id = tto.id) as ttopic ON ttopic.training_id = pt.id ';
			}
			// Recommended classes
			if ((! $criteria ['doCount']) and ($criteria ['showUpcoming'] or $criteria ['upcoming_id'])) {
				// not tested
				$sql .= ($criteria ['upcoming_id'] ? "INNER" : "LEFT") . " JOIN (SELECT training_title_phrase, person_id
				FROM person_to_training_topic_option as ptto
				JOIN training_to_training_topic_option ttt ON (ttt.training_topic_option_id = ptto.training_topic_option_id)
				JOIN training t ON (t.id = ttt.training_id)
				JOIN training_title_option tt ON (tt.id = t.training_title_option_id)
				WHERE
				t.is_deleted = 0 AND tt.training_title_phrase != 'unknown' AND t.training_start_date > NOW()
				" . (($criteria ['upcoming_id']) ? ' AND t.training_title_option_id = ' . $criteria ['upcoming_id'] : '') . "
				) AS recommend ON recommend.person_id = pt.person_id ";

				//$sql .= ' JOIN person_to_training_topic_option ptto ON ptto.person_id = pt.person_id';
			}

			if ($criteria ['showTopicRecommend'] or $criteria ['training_topic_recommend_id'] or $criteria ['training_topic_id'] or ($criteria ['training_topic_id'] === '0')) {

				$sql .= '
				INNER JOIN (
				SELECT person_id, topicid.id, training_topic_phrase
				FROM person_to_training_topic_option ptto
				INNER JOIN training_topic_option topicid ON (topicid.id = ptto.training_topic_option_id)
				) AS ptopic ON ptopic.person_id = pt.person_id
				';


			}
			if ($criteria ['showQualPrim'] || $criteria ['showQualSecond'] || $criteria ['qualification_id'] || $criteria ['qualification_secondary_id']) {
				// primary qualifications
				$sql .= '
				LEFT JOIN person_qualification_option as pq ON (
				(pt.primary_qualification_option_id = pq.id AND pq.parent_id IS NULL)
				OR
				pq.id = (SELECT parent_id FROM person_qualification_option WHERE id = pt.primary_qualification_option_id LIMIT 1)
				)';

				// secondary qualifications
				$sql .= '
				LEFT JOIN person_qualification_option as pqs ON (
				pt.primary_qualification_option_id = pqs.id AND pqs.parent_id IS NOT NULL
				)';
			}

			$where = '';

			// legacy
			if ($criteria ['training_title_option_id'] or $criteria ['training_title_option_id'] === '0') {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= ' pt.training_title_option_id = ' . $criteria ['training_title_option_id'];
			}

			if ($criteria ['training_title_id'] or $criteria ['training_title_id'] === '0') {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= ' pt.training_title_option_id = ' . $criteria ['training_title_id'];
			}

			if ($criteria ['training_topic_id'] or $criteria ['training_topic_id'] === '0') {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= ' ttopic.training_topic_option_id = \'' . $criteria ['training_topic_id'] . '\'';
			}

			if ($criteria ['training_pepfar_id'] or $criteria ['training_pepfar_id'] === '0') {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= ' tpep.training_pepfar_categories_option_id = \'' . $criteria ['training_pepfar_id'] . '\'';
			}

			if ($criteria ['facilityInput']) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= ' pt.facility_id = \'' . $criteria ['facilityInput'] . '\'';
			}

			if ($criteria ['training_gender']) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= ' pt.gender = \'' . $criteria ['training_gender'] . '\'';
			}

			if ($criteria ['training_active']) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= ' pt.active = \'' . $criteria ['training_active'] . '\'';
			}

			if ($criteria ['qualification_id']) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= ' (pq.id = ' . $criteria ['qualification_id'] . ' OR pqs.parent_id = ' . $criteria ['qualification_id'] . ') ';
			}
			if ($criteria ['qualification_secondary_id']) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= ' pqs.id = ' . $criteria ['qualification_secondary_id'];
			}

			if ($criteria ['training_topic_recommend_id']) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= '  ptopic.id = ' . $criteria ['training_topic_recommend_id'];
			}
			if ($criteria ['first_name']) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= " first_name = '" . mysql_escape_string ( $criteria ['first_name'] ) . "'";
			}
			if ($criteria ['last_name']) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$where .= " last_name = '" . mysql_escape_string ( $criteria ['last_name'] ) . "'";
			}

			if (intval ( $criteria ['end-year'] ) and $criteria ['start-year']) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$startDate = $criteria ['start-year'] . '-' . $criteria ['start-month'] . '-' . $criteria ['start-day'];
				$endDate = $criteria ['end-year'] . '-' . $criteria ['end-month'] . '-' . $criteria ['end-day'];
				$where .= ' training_start_date >= \'' . $startDate . '\'  AND training_start_date <= \'' . $endDate . '\'  ';
			}

			if ($where)
			$sql .= ' WHERE ' . $where;

			if ($criteria ['doCount']) {

				$groupBy = '';

				if ($criteria ['showTrainingTitle']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= ' pt.training_title_option_id';
				}
				if ($criteria ['showGender']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= ' pt.gender';
				}
				if ($criteria ['showActive']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= ' pt.active';
				}
				if ($criteria ['showProvince']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= ' pt.province_id';
				}
				if ($criteria ['showDistrict']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= '  pt.district_id';
				}
				if (isset ( $criteria ['showLocation'] ) and $criteria ['showLocation']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= '  pt.training_location_id';
				}
				if ($criteria ['showTopic']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= '  ttopic.training_topic_option_id';
				}

				if ($criteria ['showTopicRecommend']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= '  ptopic.id';
				}

				if ($criteria ['showQualPrim'] and ! $criteria ['showQualSecond']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= '  pq.id'; //added ToddW 090827
				} else if ($criteria ['showQualPrim'] || $criteria ['showQualSecond']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= '  pt.primary_qualification_option_id';
				}

				/*
				if ( $criteria['showQualPrim']) {
				if ( strlen($groupBy) ) $groupBy .= ' , ';
				//$groupBy .=	'  pq.id ';
				}
				if ( $criteria['showQualSecond']) {
				if ( strlen($groupBy) ) $groupBy .= ' , ';
				//$groupBy .=	'  pqs.id ';
				}
				*/

				if ($criteria ['showPepfar']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= '  tpep.training_pepfar_categories_option_id';
				}

				if ($criteria ['showFacility']) {
					if (strlen ( $groupBy ))
					$groupBy .= ' , ';
					$groupBy .= '  pt.facility_id';
				}

				if ($groupBy != '')
				$groupBy = ' GROUP BY ' . $groupBy;
				$sql .= $groupBy;
			} else {
				//if ( $criteria['showTopicRecommend'] || $criteria['showCourseRecommend']) {
				$sql .= ' GROUP BY pt.person_id, pt.id'; //added ToddW 090827 -- always group by person
				//}
			}

			//echo $sql; exit;


			$rowArray = $db->fetchAll ( $sql );

			if ($criteria ['doCount']) {
				$count = 0;
				foreach ( $rowArray as $row ) {
					$count += $row ['cnt'];
				}
			} else {
				$count = count ( $rowArray );
				//cheezy
				//get the count of people, now group by topic and run the query again
				//so we get a line for each topic
				if ($criteria ['showTopicRecommend']) {
					$sql .= ',ptopic.training_topic_phrase';
					$rowArray = $db->fetchAll ( $sql );
				}
			}
			if ($this->_getParam ( 'outputType' ))
			$this->sendData ( $this->reportHeaders ( false, $rowArray ) );

		} else {
			$count = 0;
			$rowArray = array ();
		}

		$criteria ['go'] = $this->getSanParam ( 'go' );

		$this->viewAssignEscaped ( 'results', $rowArray );
		if ($rowArray) {
			$first = reset ( $rowArray );
			if (isset ( $first ['phone_work'] )) {
				foreach ( $rowArray as $key => $val ) {
					$phones = array ();
					if ($val ['phone_work'])
					$phones [] = str_replace ( ' ', '&nbsp;', trim ( $val ['phone_work'] ) ) . '&nbsp;(w)';
					if ($val ['phone_home'])
					$phones [] = str_replace ( ' ', '&nbsp;', trim ( $val ['phone_home'] ) ) . '&nbsp;(h)';
					if ($val ['phone_mobile'])
					$phones [] = str_replace ( ' ', '&nbsp;', trim ( $val ['phone_mobile'] ) ) . '&nbsp;(m)';
					$rowArray [$key] ['phone'] = implode ( ', ', $phones );
				}
				$this->view->assign ( 'results', $rowArray );
			}
		}

		$this->view->assign ( 'count', $count );
		$this->view->assign ( 'criteria', $criteria );

		//province
		/*
		$provinceArray = OptionList::suggestionList ( 'location_province', 'province_name', false, false, false );
		$this->viewAssignEscaped ( 'provinces', $provinceArray );
		//district
		$districtArray = OptionList::suggestionList ( 'location_district', array ('district_name', 'parent_province_id' ), false, false, false );
		$this->viewAssignEscaped ( 'districts', $districtArray );
		*/
		$locations = Location::getAll();
		$this->viewAssignEscaped('locations',$locations);

		//course
		$courseArray = TrainingTitleOption::suggestionList ( false, 10000 );
		$this->viewAssignEscaped ( 'courses', $courseArray );
		//topics
		$topicsArray = OptionList::suggestionList ( 'training_topic_option', 'training_topic_phrase', false, false, false );
		$this->viewAssignEscaped ( 'topics', $topicsArray );
		//qualifications (primary)
		$qualsArray = OptionList::suggestionList ( 'person_qualification_option', 'qualification_phrase', false, false, false, 'parent_id IS NULL' );
		$this->viewAssignEscaped ( 'qualifications_primary', $qualsArray );
		//qualifications (secondary)
		$qualsArray = OptionList::suggestionList ( 'person_qualification_option', 'qualification_phrase', false, false, false, 'parent_id IS NOT NULL' );
		$this->viewAssignEscaped ( 'qualifications_secondary', $qualsArray );
		//pepfar
		$organizersArray = OptionList::suggestionList ( 'training_pepfar_categories_option', 'pepfar_category_phrase', false, false, false );
		$this->viewAssignEscaped ( 'pepfars', $organizersArray );
		//upcoming classes
		if (! $criteria ['doCount']) {
			$upcomingArray = TrainingRecommend::getUpcomingTrainingTitles ();
			$this->viewAssignEscaped ( 'upcoming', $upcomingArray );
		}

		//recommended
		require_once 'models/table/TrainingRecommend.php';
		$topicsRecommend = TrainingRecommend::getTopics ();
		$this->viewAssignEscaped ( 'topicsRecommend', $topicsRecommend->ToArray () );
		//facilities list
		$rowArray = OptionList::suggestionList ( 'facility', array ('facility_name', 'id' ), false, 9999 );
		$facilitiesArray = array ();
		foreach ( $rowArray as $key => $val ) {
			if ($val ['id'] != 0)
			$facilitiesArray [] = $val;
		}
		$this->viewAssignEscaped ( 'facilities', $facilitiesArray );

	}

	public function needsByPersonCountAction() {
		$this->view->assign ( 'mode', 'count' );
		return $this->needsReport ();
	}

	public function needsByPersonNameAction() {
		return $this->needsReport ();
	}
	/*
	function needsReportOLD() {
	//find the first date in the database
	$db = Zend_Db_Table_Abstract::getDefaultAdapter();

	$criteria['province_id'] = $this->getSanParam('province_id');
	$criteria['district_id'] = $this->getSanParam('district_id');
	if ( strstr($criteria['district_id'], '_') !== false ) {
	$parts = explode('_',$criteria['district_id']);
	$criteria['district_id'] = $parts[1];
	}

	$criteria['facility_name'] = $this->getSanParam('facility_name');
	$criteria['qualification_id'] = $this->getSanParam('qualification_id');
	$criteria['qualification_secondary_id'] = $this->getSanParam('qualification_secondary_id');

	$criteria['concatNames'] = $this->getSanParam('concatNames');


	$criteria['doCount'] = ($this->view->mode == 'count');
	$criteria['showProvince'] = ($this->getSanParam('showProvince') OR ($criteria['doCount'] and ($criteria['province_id'] or $criteria['province_id'] === '0') ));
	$criteria['showDistrict'] = ($this->getSanParam('showDistrict') OR ($criteria['doCount'] and ($criteria['district_id'] or $criteria['district_id'] === '0') ));
	//$criteria['showQualification'] = ($this->getSanParam('showQualification') OR ($criteria['doCount']  and ($criteria['qualification_id'] or $criteria['qualification_id'] === '0') ));

	$criteria['showQualPrim'] = ($this->getSanParam('showQualPrim') OR ($criteria['doCount']  and ($criteria['qualification_id'] or $criteria['qualification_id'] === '0') ));
	$criteria['showQualSecond'] = ($this->getSanParam('showQualSecond') OR ($criteria['doCount']  and ($criteria['qualification_secondary_id'] or $criteria['qualification_secondary_id'] === '0') ));

	$criteria['showFacility'] = ($this->getSanParam('showFacility') OR ($criteria['doCount'] and $criteria['facility_name']));
	$criteria['showEmail'] = ($this->getSanParam('showEmail'));
	$criteria['showPhone'] = ($this->getSanParam('showPhone'));

	$criteria['go'] = $this->getSanParam('go');
	if ($criteria['go'] ) {

	$sql = 'SELECT ';

	if ( $criteria['doCount'] ) {
	$distinct = ($criteria['distinctCount']) ? 'DISTINCT ' : '';
	$sql .= ' COUNT('.$distinct.'person_id) as "cnt" ';
	} else {
	if ($criteria['concatNames'] )
	$sql .= ' DISTINCT person_id as "id", CONCAT(first_name, '."' '".',last_name) as "name", pt.training_start_date  ';
	else
	$sql .= ' DISTINCT person_id as "id", last_name, first_name, middle_name, pt.training_start_date  ';
	}
	if ( $criteria['showPhone'] ) {
	$sql .= ", CASE WHEN (pt.phone_work IS NULL OR pt.phone_work = '') THEN NULL ELSE pt.phone_work END as \"phone_work\", CASE WHEN (pt.phone_home IS NULL OR pt.phone_home = '') THEN NULL ELSE pt.phone_home END as \"phone_home\", CASE WHEN (pt.phone_mobile IS NULL OR pt.phone_mobile = '') THEN NULL ELSE pt.phone_mobile END as \"phone_mobile\" ";
	}
	if ( $criteria['showEmail'] ) {
	$sql .= ', pt.email ';
	}
	if ( $criteria['showDistrict'] ) {
	$sql .= ', tld.district_name ';
	}
	if ( $criteria['showProvince'] ) {
	$sql .= ', tlp.province_name ';
	}
	if ( $criteria['showFacility'] ) {
	$sql .= ', pt.facility_name ';
	}
	if ( $criteria['showQualPrim'] ) {
	$sql .= ', pq.qualification_phrase ';
	}
	if ( $criteria['showQualSecond'] ) {
	$sql .= ', pqs.qualification_phrase AS qualification_secondary_phrase';
	}
	$sql .=	' FROM (SELECT training.*, person.facility_id as "facility_id", person.id as "person_id", person.last_name, person.first_name, person.middle_name, person.phone_work, person.phone_home, person.phone_mobile, person.email, CASE WHEN person.active = \'deceased\' THEN \'inactive\' ELSE person.active END as "active", CASE WHEN person.gender IS NULL THEN \'na\' WHEN person.gender = \'\' THEN \'na\' ELSE person.gender END as "gender", primary_qualification_option_id, tto.training_title_phrase AS training_title, facility.province_id as "province_id", facility.district_id as "district_id",facility.facility_name FROM training,course,person_to_training,facility,person,training_title_option tto  WHERE training_title_option_id = tto.id AND training.id = person_to_training.training_id AND person.id = person_to_training.person_id AND training.training_title_option_id = course.id AND person.facility_id = facility.id) as pt ';


	if ( $criteria['showDistrict'] ) {
	$sql .= '	LEFT JOIN location_district as tld ON pt.district_id = tld.id ';
	}
	if ( $criteria['showProvince'] ) {
	$sql .= '	JOIN location_province as tlp ON pt.province_id = tlp.id ';
	}
	if ( $criteria['showQualPrim'] || $criteria['showQualSecond']) {
	//$sql .= '	JOIN person_qualification_option as pq ON pq.id = pt.primary_qualification_option_id ';
	// primary qualifications
	$sql .= '
	LEFT JOIN person_qualification_option as pq ON (
	(pt.primary_qualification_option_id = pq.id AND pq.parent_id IS NULL)
	OR
	pq.id = (SELECT parent_id FROM person_qualification_option WHERE id = pt.primary_qualification_option_id LIMIT 1)
	)';

	// secondary qualifications
	$sql .= '
	LEFT JOIN person_qualification_option as pqs ON (
	pt.primary_qualification_option_id = pqs.id AND pqs.parent_id IS NOT NULL
	)';
	}


	$where = '';


	if ( $criteria['facility_name'] ) {
	if ( strlen($where) ) $where .= ' AND ';
	$where .= ' pt.facility_name = \''.$criteria['facility_name'].'\'' ;
	}

	if ( $criteria['district_id'] or $criteria['district_id'] === '0' ) {
	if ( strlen($where) ) $where .= ' AND ';
	$where .= ' pt.district_id = '.$criteria['district_id'] ;
	} else if ( $criteria['province_id'] or $criteria['province_id'] === '0'  ) {
	if ( strlen($where) ) $where .= ' AND ';
	$where .= ' pt.province_id = '.$criteria['province_id'] ;
	}

	if ( $criteria['qualification_id'] ) {
	if ( strlen($where) ) $where .= ' AND ';
	$where .= ' (pq.id = '.$criteria['qualification_id'] . ' OR pqs.parent_id = ' . $criteria['qualification_id'] . ') ';
	}
	if ( $criteria['qualification_secondary_id'] ) {
	if ( strlen($where) ) $where .= ' AND ';
	$where .= ' pqs.id = '.$criteria['qualification_secondary_id'] ;
	}


	if ( $where )
	$sql .= ' WHERE '.$where;

	if ( $criteria['doCount'] ) {

	$groupBy =  '';
	if ( $criteria['showProvince']) {
	if ( strlen($groupBy) ) $groupBy .= ' , ';
	$groupBy .=	' pt.province_id';
	}
	if ( $criteria['showDistrict']) {
	if ( strlen($groupBy) ) $groupBy .= ' , ';
	$groupBy .=	'  pt.district_id';
	}
	if ( $criteria['showFacility']) {
	if ( strlen($groupBy) ) $groupBy .= ' , ';
	$groupBy .=	'  pt.facility_id';
	}
	if ( $criteria['showQualPrim']) {
	if ( strlen($groupBy) ) $groupBy .= ' , ';
	$groupBy .=	'  pq.id ';
	}
	if ( $criteria['showQualSecond']) {
	if ( strlen($groupBy) ) $groupBy .= ' , ';
	$groupBy .=	'  pqs.id ';
	}

	if ( $groupBy != '' )
	$groupBy = ' GROUP BY '.$groupBy;
	$sql .= $groupBy;
	}
	$rowArray = $db->fetchAll($sql);

	if ( $criteria['doCount'] ) {
	$count = 0;
	foreach( $rowArray as $row ) {
	$count += $row['cnt'];
	}
	} else {
	$count = count($rowArray);
	}
	if ( $this->_getParam('outputType')  ) $this->sendData($this->reportHeaders(false, $rowArray));

	} else {
	$count = 0;
	$rowArray  = array();
	}

	$criteria['go'] = $this->getSanParam('go');


	$this->viewAssignEscaped('results',$rowArray);
	if ( $rowArray ) {
	$first = reset($rowArray);
	if ( isset($first['phone_work'] ) ) {
	foreach($rowArray as $key => $val) {
	$phones = array();
	if ( $val['phone_work'] )
	$phones []= str_replace(' ','&nbsp;',trim($val['phone_work'])).'&nbsp;(w)';
	if ( $val['phone_home'] )
	$phones []= str_replace(' ','&nbsp;',trim($val['phone_home'])).'&nbsp;(h)';
	if ( $val['phone_mobile'] )
	$phones []= str_replace(' ','&nbsp;',trim($val['phone_mobile'])).'&nbsp;(m)';
	$rowArray[$key]['phone'] = implode(', ',$phones);
	}
	$this->view->assign('results',$rowArray);
	}
	}

	$this->view->assign('count',$count);
	$this->view->assign('criteria',$criteria);

	//province
	$provinceArray = OptionList::suggestionList('location_province','province_name',false,false,false);
	$this->viewAssignEscaped('provinces',$provinceArray);
	//district
	$districtArray = OptionList::suggestionList('location_district',array('district_name','parent_province_id'),false,false,false);
	$this->viewAssignEscaped('districts',$districtArray);
	//course
	$courseArray = Course::suggestionList(false,10000);;
	$this->viewAssignEscaped('courses',$courseArray);
	//topics
	$topicsArray = OptionList::suggestionList('training_topic_option','training_topic_phrase',false,false,false);
	$this->viewAssignEscaped('topics',$topicsArray);
	//qualifications (primary)
	$qualsArray = OptionList::suggestionList('person_qualification_option','qualification_phrase',false,false,false,'parent_id IS NULL');
	$this->viewAssignEscaped('qualifications_primary',$qualsArray);
	//qualifications (secondary)
	$qualsArray = OptionList::suggestionList('person_qualification_option','qualification_phrase',false,false,false,'parent_id IS NOT NULL');
	$this->viewAssignEscaped('qualifications_secondary',$qualsArray);
	//pepfar
	$organizersArray = OptionList::suggestionList('training_pepfar_categories_option','pepfar_category_phrase',false,false,false);
	$this->viewAssignEscaped('pepfars',$organizersArray);
	//organizers
	//$organizersArray = OptionList::suggestionList('training_organizer_option','training_organizer_phrase',false,false,false);
	//$this->viewAssignEscaped('organizers',$organizersArray);
	//funding
	$fundingArray = OptionList::suggestionList('training_funding_option','funding_phrase',false,false,false);
	$this->viewAssignEscaped('funding',$fundingArray);

	//organizers
	$this->view->assign('organizers_checkboxes', Checkboxes::generateHtml('training_organizer_option', 'training_organizer_phrase', $this->view));

	}

	*/

	public function rosterAction() {

		$criteria ['training_organizer_id'] = $this->getSanParam ( 'training_organizer_id' );
		$criteria ['is_extended'] = $is_extended = $this->getSanParam ( 'is_extended' );
		$criteria ['add_additional'] = $add_additional = $this->getSanParam ( 'add_additional' );

		//find the first date in the database
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT MIN(training_start_date) as \"start\" FROM training WHERE is_deleted = 0";
		$rowArray = $db->fetchAll ( $sql );
		$start_default = $rowArray [0] ['start'];
		$parts = explode ( '-', $start_default );
		$criteria ['start-year'] = $parts [0];
		$criteria ['start-month'] = $parts [1];
		$criteria ['start-day'] = $parts [2];

		if ($this->getSanParam ( 'start-year' ))
		$criteria ['start-year'] = $this->getSanParam ( 'start-year' );
		if ($this->getSanParam ( 'start-month' ))
		$criteria ['start-month'] = $this->getSanParam ( 'start-month' );
		if ($this->getSanParam ( 'start-day' ))
		$criteria ['start-day'] = $this->getSanParam ( 'start-day' );
		if ($this->view->mode == 'search') {
			$sql = "SELECT MAX(training_start_date) as \"start\" FROM training ";
			$rowArray = $db->fetchAll ( $sql );
			$end_default = $rowArray [0] ['start'];
			$parts = explode ( '-', $end_default );
			$criteria ['end-year'] = $parts [0];
			$criteria ['end-month'] = $parts [1];
			$criteria ['end-day'] = $parts [2];
		} else {
			$criteria ['end-year'] = date ( 'Y' );
			$criteria ['end-month'] = date ( 'm' );
			$criteria ['end-day'] = date ( 'd' );
		}

		if ($this->getSanParam ( 'end-year' ))
		$criteria ['end-year'] = $this->getSanParam ( 'end-year' );
		if ($this->getSanParam ( 'end-month' ))
		$criteria ['end-month'] = $this->getSanParam ( 'end-month' );
		if ($this->getSanParam ( 'end-day' ))
		$criteria ['end-day'] = $this->getSanParam ( 'end-day' );

		if (is_numeric ( $criteria ['training_organizer_id'] )) {

			$sql = "SELECT id FROM training ";
			$where = "WHERE is_deleted=0 AND training_organizer_option_id={$criteria['training_organizer_id']}";

			if (intval ( $criteria ['start-year'] )) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$startDate = $criteria ['start-year'] . '-' . $criteria ['start-month'] . '-' . $criteria ['start-day'];
				$where .= ' training_start_date >= \'' . $startDate . '\' ';
			}

			if (intval ( $criteria ['end-year'] )) {
				if (strlen ( $where ))
				$where .= ' AND ';
				$endDate = $criteria ['end-year'] . '-' . $criteria ['end-month'] . '-' . $criteria ['end-day'];
				$where .= ' training_start_date <= \'' . $endDate . '\'  ';
			}

			$sql .= $where;
			//$sql .= " ORDER BY training_title ";


			$rowArray = $db->fetchAll ( $sql );

			require_once ('models/table/Training.php');
			require_once ('models/table/TrainingToTrainer.php');
			require_once ('models/table/PersonToTraining.php');

			$tableObj = new Training ( );

			$output = '<html><body>';
			if (! $rowArray)
			$output .= 'No trainings found.';

			foreach ( $rowArray as $row ) {
				$rowRay = $tableObj->getTrainingInfo ( $row ['id'] );

				// calculate end date
				switch ($rowRay ['training_length_interval']) {
					case 'week' :
					$days = $rowRay ['training_length_value'] * 7;
					break;
					case 'day' :
					$days = $rowRay ['training_length_value'] - 1; // start day counts as a day?
					break;
					default :
					$days = false;
					break;
				}

				if ($days) {
					$rowRay ['training_end_date'] = strtotime ( "+$days day", strtotime ( $rowRay ['training_start_date'] ) );
					$rowRay ['training_end_date'] = date ( 'Y-m-d', $rowRay ['training_end_date'] );
				} else {
					$rowRay ['training_end_date'] = $rowRay ['training_start_date'];
				}

				$rowRay ['duration'] = $rowRay ['training_length_value'] . ' ' . $rowRay ['training_length_interval'] . (($rowRay ['training_length_value'] == 1) ? "" : "s");

				//$output .= '<h2>' . $rowRay['training_title'] . '<h2>';
				$output .= "
				<p>
				<strong>" . $this->tr ( 'Training Name' ) . ":</strong> {$rowRay['training_title']}<br />
				<strong>" . t ( 'Training Center' ) . ":</strong> {$rowRay['training_location_name']}<br />
				<strong>" . t ( 'Dates' ) . ":</strong> {$rowRay['training_start_date']}" . ($rowRay ['training_start_date'] != $rowRay ['training_end_date'] ? ' - ' . $rowRay ['training_end_date'] : '') . "<br />
				<strong>" . t ( 'Course Length' ) . ":</strong> {$rowRay['duration']}<br />
				<strong>" . $this->tr ( 'Training Topic' ) . ":</strong> {$rowRay['training_topic_phrase']}<br />
				<strong>" . $this->tr ( 'Training Level' ) . ":</strong> {$rowRay['training_level_phrase']}<br />
				" . ($rowRay ['training_got_curriculum_phrase'] ? "<strong>" . $this->tr ( 'GOT Curriculum' ) . "</strong>: {$rowRay['training_got_curriculum_phrase']}<br />" : '') . "
				" . ($rowRay ['got_comments'] ? "<strong>" . $this->tr ( 'GOT Comment' ) . "</strong>: {$rowRay['got_comments']}<br />" : '') . "
				" . ($rowRay ['comments'] ? "<strong>" . $this->tr ( 'Comments' ) . "</strong>: {$rowRay['comments']}<br />" : "") . "
				" . ($rowRay ['pepfar'] ? "<strong>" . $this->tr ( 'PEPFAR Category' ) . ":</strong> {$rowRay['pepfar']}<br />" : "") . "
				" . ($rowRay ['objectives'] ? "<strong>" . $this->tr ( 'Course Objectives' ) . ":</strong> " . nl2br ( $rowRay ['objectives'] ) : '') . "
				</p>
				";

				/* Trainers */
				$trainers = TrainingToTrainer::getTrainers ( $row ['id'] )->toArray ();

				$output .= '
				<table border="1" style="border-collapse:collapse;" cellpadding="3">
				<caption style="text-align:left;"><em>' . t ( 'Course Trainers' ) . '</em></caption>
				<tr>
				<th>' . $this->tr ( 'Last Name' ) . '</th>
				<th>' . $this->tr ( 'First Name' ) . '</th>
				<th>' . t ( 'Days' ) . '</th>
				</tr>
				';
				foreach ( $trainers as $tRow ) {
					$output .= "
					<tr>
					<td>{$tRow['last_name']}</td>
					<td>{$tRow['first_name']}</td>
					<td>{$tRow['duration_days']}</td>
					</tr>
					";
				}

				$output .= '
				</table>
				<br>
				';

				/* Participants */
				$persons = PersonToTraining::getParticipants ( $row ['id'] )->toArray ();

				$output .= '
				<table border="1" style="border-collapse:collapse;" cellpadding="3">
				<caption style="text-align:left;"><em>' . t ( 'Course Participants' ) . '</em></caption>
				<tr>';
				$headers = array();
				$headers []= $this->tr ( 'Last Name' );
				if ( $this->setting ( 'display_middle_name') ) $headers []= $this->tr ( 'Middle Name' );
				$headers []= $this->tr ( 'First Name' );
				$headers []= t ( 'Date of Birth' );
				$headers []= $this->tr ( 'Facility' );
				if ( $add_additional ) {
					if  ($this->setting ( 'display_region_b' )) $headers []= $this->tr ( 'Region B (Health District)' );
					if  ($this->setting ( 'display_region_c' )) $headers []= $this->tr ( 'Region C (Local Region)' );
					$headers []= $this->tr ( 'Region A (Province)' );
					$headers []= t ( 'Primary Qualification' );
					$headers []= t ( 'Secondary Qualification' );
				}
				if ( $is_extended ) {
					$headers []= t ( 'Pre-Test' );
					$headers []= t ( 'Post-Test' );
					$headers []= t ( 'Change in Score' );
				}
				$output .= '<th>'.implode('</th><th>', $headers);
				$output .='</th></tr>';

				$locations = Location::getAll();

				foreach ( $persons as $r ) {
					if (is_numeric ( $r ['score_percent_change'] )) { // add percent
						if ($r ['score_percent_change'] > 0) {
							$r ['score_percent_change'] = "+" . $r ['score_percent_change'];
						}
						$r ['score_percent_change'] = "{$r['score_percent_change']}%";
					}
					$r ['score_change'] = '';

					if ($r ['score_post']) {
						$r ['score_change'] = $r ['score_post'] - $r ['score_pre'];
					}

					$output .= "<tr><td>";
					$body_fields = array();
					$body_fields[] = $r['last_name'];
					if ( $this->setting ( 'display_middle_name') ) $body_fields[] = $r['middle_name'];
					$body_fields[] = $r['first_name'];
					$body_fields[] = $r['birthdate'];
					$body_fields[] = $r['facility_name'];
					if ( $add_additional ) {
						list($city_name, $prov_id, $dist_id, $regc_id) = Location::getCityInfo($r['location_id'], $this->setting('num_location_tiers'));
						if ( $this->setting ( 'display_region_b' ) ) $body_fields[] = $locations[$dist_id]['name'];
						if ( $this->setting ( 'display_region_c' ) ) $body_fields[] = $locations[$regc_id]['name'];
						$body_fields[] = $locations[$prov_id]['name'];

						if ( (!$r['primary_qualification']) OR ($r['primary_qualification'] == 'unknown')) {
							$body_fields[] = $r['qualification'];
							$body_fields[] = '';
						} else {
							$body_fields[] = $r['primary_qualification'];
							$body_fields[] = $r['qualification'];
						}
						//        $body_fields[] = $r['primary_responsibility'];
						//        $body_fields[] = $r['secondary_responsibility'];
					}
					if ( $is_extended ) {
						$body_fields[] = $r['score_pre'];
						$body_fields[] = $r['score_post'];
						$body_fields[] = $r['score_change'];
					}

					$output .= implode('</td><td>', $body_fields);

					$output .="</td></tr>";

					"</tr>";
				}

				$output .= '
				</table>
				';

				$output .= '<br><hr size="1">';
			}

			$output .= '</html></body>';

			echo $output;
			exit ();
		}

		//organizers
		$organizersArray = OptionList::suggestionList ( 'training_organizer_option', 'training_organizer_phrase', false, false, false );
		$this->viewAssignEscaped ( 'organizers', $organizersArray );
		$this->view->assign ( 'criteria', $criteria );

	}

	public function evaluationsAction() {

		$criteria ['evaluation_id'] = $this->getSanParam ( 'evaluation_id' );
		$training_id = $this->getSanParam ( 'training_id' );

		//find the first date in the database
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$sql = "SELECT MIN(timestamp_created) as \"start\" FROM evaluation_response WHERE is_deleted = 0";
		$rowArray = $db->fetchAll ( $sql );
		$start_default = $rowArray [0] ['start'];
		$parts = explode ( ' ', $start_default );
		$parts = explode ( '-', $parts [0] );
		$criteria ['start-year'] = $parts [0];
		$criteria ['start-month'] = $parts [1];
		$criteria ['start-day'] = $parts [2];

		if ($this->getSanParam ( 'start-year' ))
		$criteria ['start-year'] = $this->getSanParam ( 'start-year' );
		if ($this->getSanParam ( 'start-month' ))
		$criteria ['start-month'] = $this->getSanParam ( 'start-month' );
		if ($this->getSanParam ( 'start-day' ))
		$criteria ['start-day'] = $this->getSanParam ( 'start-day' );
		if ($this->view->mode == 'search') {
			$sql = "SELECT MAX(timestamp_created) as \"start\" FROM evaluation_response ";
			$rowArray = $db->fetchAll ( $sql );
			$end_default = $rowArray [0] ['start'];
			$parts = explode ( ' ', $start_default );
			$parts = explode ( '-', $parts [0] );
			$criteria ['end-year'] = $parts [0];
			$criteria ['end-month'] = $parts [1];
			$criteria ['end-day'] = $parts [2];
		} else {
			$criteria ['end-year'] = date ( 'Y' );
			$criteria ['end-month'] = date ( 'm' );
			$criteria ['end-day'] = date ( 'd' );
		}

		if (is_numeric ( $criteria ['evaluation_id'] ) || $training_id) {

			$sql = "SELECT eqr.* FROM evaluation_response as er JOIN evaluation_question_response as eqr ON er.id = eqr.evaluation_response_id ";

			if ($training_id)
			$where = "JOIN evaluation_to_training as ett ON er.evaluation_to_training_id = ett.id WHERE ett.training_id=$training_id";
			else
			$where = "WHERE er.is_deleted=0 AND evaluation_id={$criteria['evaluation_id']}";
			/*
			if (intval ( $criteria ['start-year'] )) {
			if (strlen ( $where ))
			$where .= ' AND ';
			$startDate = $criteria ['start-year'] . '-' . $criteria ['start-month'] . '-' . $criteria ['start-day'];
			$where .= ' er.timestamp_created >= \'' . $startDate . '\' ';
			}

			if (intval ( $criteria ['end-year'] )) {
			if (strlen ( $where ))
			$where .= ' AND ';
			$endDate = $criteria ['end-year'] . '-' . $criteria ['end-month'] . '-' . $criteria ['end-day'];
			$where .= ' er.evaluation_id <= \'' . $endDate . '\'  ';
			}
			*/
			if ($training_id)
			$sql .= $where . ' ORDER BY ett.evaluation_id, eqr.evaluation_question_id';
			else
			$sql .= $where . ' ORDER BY er.evaluation_id, eqr.evaluation_question_id';

			$rowArray = $db->fetchAll ( $sql );
			$this->view->assign ( 'results', $rowArray );

			//fetch questions
			$sql = "SELECT eq.*,e.`title` FROM evaluation_question as eq, evaluation as e  ";
			if ($training_id) {
				$sql .= "JOIN evaluation_to_training as ett ON ett.evaluation_id = e.id ";
				$sql .= "WHERE eq.evaluation_id = e.id AND ett.training_id = $training_id ";
			} else {
				$sql .= "WHERE eq.evaluation_id = e.id AND eq.evaluation_id={$criteria['evaluation_id']} ";
			}
			$sql .= " ORDER BY weight ASC ";
			$rowArray = $db->fetchAll ( $sql );
			$this->view->assign ( 'questions', $rowArray );

		}
		/*
		if ($this->getSanParam ( 'end-year' ))
		$criteria ['end-year'] = $this->getSanParam ( 'end-year' );
		if ($this->getSanParam ( 'end-month' ))
		$criteria ['end-month'] = $this->getSanParam ( 'end-month' );
		if ($this->getSanParam ( 'end-day' ))
		$criteria ['end-day'] = $this->getSanParam ( 'end-day' );
		*/
		//evalutions
		$evaluationsArray = OptionList::suggestionList ( 'evaluation', 'title', false, false, false );
		$this->viewAssignEscaped ( 'evaluations', $evaluationsArray );
		$this->view->assign ( 'criteria', $criteria );

	}

	public function psTrainingsByNameAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psTrainingByParticipantsAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psMissingStudentsTrainedAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psMissingStudentsByNameAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psMissingGraduatedStudentsAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psMissingCourseByStudentCountAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psMissingCourseByNameAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psMissingCohortByParticipantCountAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psMissingInstitutionInformationAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psMissingTutorByNameAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psMissingFacilityReportAction() {
		$this->view->assign ( 'mode', 'id' );
		#		return $this->trainingReport ();
	}

	public function psStudentsTrainedAction() {
		$helper = new Helper();
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'institutions', $helper->getInstitutions());
		$this->view->assign ( 'cadres', $helper->getCadres());
		$this->view->assign ( 'cohorts', $helper->getCohorts());
		$this->view->assign ( 'nationalities', $helper->getNationalities());
		$this->view->assign ( 'funding', $helper->getFunding());
		$this->view->assign ( 'tutors', $helper->getTutors());
		$this->view->assign ( 'facilities', $helper->getFacilities());
		$this->view->assign ( 'coursetypes', $helper->AdminCourseTypes());
		$this->view->assign ( 'degrees', $helper->getDegrees());
		if ($this->getSanParam ( 'process' )){

			$maintable = "person p";
			$select = array();
			$select[] = "p.id as personid";
			$select[] = "p.first_name";
			$select[] = "p.last_name";

			$join = array();
			$join[] = array(
			"table" => "student",
			"abbreviation" => "s",
			"compare" => "s.personid = p.id",
			"type" => "inner"
			);

			$where = array();
			$where[] = "p.is_deleted = 0";

			$sort = array();

			if ($this->getSanParam ( 'showinstitution' )){
				$select[] = "i.institutionname";

				$join[] = array(
				"table" => "link_student_institution",
				"abbreviation" => "lsi",
				"compare" => "lsi.id_student = s.id",
				"type" => "inner"
				);

				$join[] = array(
				"table" => "institution",
				"abbreviation" => "i",
				"compare" => "i.id = lsi.id_institution",
				"type" => "inner"
				);
				if ($this->getSanParam('institution')){
					$where[] = "i.id = " . $this->getSanParam('institution');
				}
			}

			if ($this->getSanParam ( 'showcohort' )){
				$select[] = "c.cohortname";

				$join[] = array(
				"table" => "link_student_cohort",
				"abbreviation" => "lsc",
				"compare" => "lsc.id_student = s.id",
				"type" => "inner"
				);

				$join[] = array(
				"table" => "cohort",
				"abbreviation" => "c",
				"compare" => "c.id = lsc.id_cohort",
				"type" => "inner"
				);

				if ($this->getSanParam('cohort')){
					$where[] = "c.id = " . $this->getSanParam('cohort');
				}
			}

			#			if ($this->getSanParam ( 'showcoursename' )){
			#				echo "show showcoursename<br>";
			#			} else {
			#				echo "do not show showcoursename<br>";
			#			}
			#			if ($this->getSanParam ( 'coursename' ));

			#			if ($this->getSanParam ( 'showgrades' )){
			#				echo "show showgrades<br>";
			#			} else {
			#				echo "do not show showgrades<br>";
			#			}
			#			if ($this->getSanParam ( 'grades' ));

			#			if ($this->getSanParam ( 'showexams' )){
			#				echo "show showexams<br>";
			#			} else {
			#				echo "do not show showexams<br>";
			#			}
			#			if ($this->getSanParam ( 'exams' ));

			if ($this->getSanParam ( 'showcadre' )){
				$select[] = "ca.cadrename";

				$join[] = array(
				"table" => "cadres",
				"abbreviation" => "ca",
				"compare" => "ca.id = s.cadre",
				"type" => "inner"
				);

				if ($this->getSanParam('cadre')){
					$where[] = "ca.id = " . $this->getSanParam('cadre');
				}
			}

			if ($this->getSanParam ( 'showyearinschool' )){
				# REQUIRES COHORT LINK
				$found = false;
				foreach ($join as $j){
					if ($j['table'] == "cohort"){
						$found = true;
					}
				}

				if (!$found){
					$join[] = array(
					"table" => "link_student_cohort",
					"abbreviation" => "lsc",
					"compare" => "lsc.id_student = s.id",
					"type" => "inner"
					);

					$join[] = array(
					"table" => "cohort",
					"abbreviation" => "c",
					"compare" => "c.id = lsc.id_cohort",
					"type" => "inner"
					);
				}
				$select[] = "c.startdate";
				if ($this->getSanParam('yearinschool')){
					$where[] = "c.startdate LIKE '" . mysql_real_escape_string(substr($this->getSanParam('yearinschool'), 0, 4)) . "%'";
				}
			}

			#			if ($this->getSanParam ( 'showcoursetype' )){
			#				echo "show showcoursetype<br>";
			#			} else {
			#				echo "do not show showcoursetype<br>";
			#			}
			#			if ($this->getSanParam ( 'coursetype' ));

			#			if ($this->getSanParam ( 'showtopic' )){
			#				echo "show showtopic<br>";
			#			} else {
			#				echo "do not show showtopic<br>";
			#			}
			#			if ($this->getSanParam ( 'topic' ));

			#			if ($this->getSanParam ( 'showstudents' )){
			#				echo "show showstudents<br>";
			#			} else {
			#				echo "do not show showstudents<br>";
			#			}
			#			if ($this->getSanParam ( 'students' ));

			$query = "SELECT " . implode(", ", $select) . "\n";
			$query .= " FROM " . $maintable . "\n";
			if (count ($join) > 0){
				foreach ($join as $j){
					$query .= strtoupper($j['type']) . " JOIN " . $j['table'] . " " . $j['abbreviation'] . " ON " . $j['compare'] . "\n";
				}
			}
			if (count ($where) > 0){
				$query .= "WHERE " . implode(" AND ", $where) . "\n";
			}

			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$rowArray = $db->fetchAll ($query);
			$this->view->assign('output',$rowArray);
			$this->view->assign('query',$query);

			#			echo $query;
			#			exit;
		}
		#		return $this->trainingReport ();
	}

	public function psStudentsByNameAction() {
		$helper = new Helper();
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'institutions', $helper->getInstitutions());
		$this->view->assign ( 'cadres', $helper->getCadres());
		$this->view->assign ( 'cohorts', $helper->getCohorts());
		$this->view->assign ( 'nationalities', $helper->getNationalities());
		$this->view->assign ( 'funding', $helper->getFunding());
		$this->view->assign ( 'tutors', $helper->getTutors());
		$this->view->assign ( 'facilities', $helper->getFacilities());
		$this->view->assign ( 'coursetypes', $helper->AdminCourseTypes());
		$this->view->assign ( 'degrees', $helper->getDegrees());
		#		return $this->trainingReport ();

		if ($this->getSanParam ( 'process' )){
			/*
			$maintable = "person p";
			$select = array();
			$select[] = "p.id as personid";
			$select[] = "p.first_name";
			$select[] = "p.last_name";

			$join = array();
			$join[] = array(
			"table" => "student",
			"abbreviation" => "s",
			"compare" => "s.personid = p.id",
			"type" => "inner"
			);

			$where = array();
			$where[] = "p.is_deleted = 0";

			$sort = array();

			if ($this->getSanParam ( 'showinstitution' )){
			$select[] = "i.institutionname";

			$join[] = array(
			"table" => "link_student_institution",
			"abbreviation" => "lsi",
			"compare" => "lsi.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "institution",
			"abbreviation" => "i",
			"compare" => "i.id = lsi.id_institution",
			"type" => "inner"
			);
			if ($this->getSanParam('institution')){
			$where[] = "i.id = " . $this->getSanParam('institution');
			}
			}

			if ($this->getSanParam ( 'showcohort' )){
			$select[] = "c.cohortname";

			$join[] = array(
			"table" => "link_student_cohort",
			"abbreviation" => "lsc",
			"compare" => "lsc.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "cohort",
			"abbreviation" => "c",
			"compare" => "c.id = lsc.id_cohort",
			"type" => "inner"
			);

			if ($this->getSanParam('cohort')){
			$where[] = "c.id = " . $this->getSanParam('cohort');
			}
			}

			#			if ($this->getSanParam ( 'showcoursename' )){
			#				echo "show showcoursename<br>";
			#			} else {
			#				echo "do not show showcoursename<br>";
			#			}
			#			if ($this->getSanParam ( 'coursename' ));

			#			if ($this->getSanParam ( 'showgrades' )){
			#				echo "show showgrades<br>";
			#			} else {
			#				echo "do not show showgrades<br>";
			#			}
			#			if ($this->getSanParam ( 'grades' ));

			#			if ($this->getSanParam ( 'showexams' )){
			#				echo "show showexams<br>";
			#			} else {
			#				echo "do not show showexams<br>";
			#			}
			#			if ($this->getSanParam ( 'exams' ));

			if ($this->getSanParam ( 'showcadre' )){
			$select[] = "ca.cadrename";

			$join[] = array(
			"table" => "cadres",
			"abbreviation" => "ca",
			"compare" => "ca.id = s.cadre",
			"type" => "inner"
			);

			if ($this->getSanParam('cadre')){
			$where[] = "ca.id = " . $this->getSanParam('cadre');
			}
			}

			if ($this->getSanParam ( 'showyearinschool' )){
			# REQUIRES COHORT LINK
			$found = false;
			foreach ($join as $j){
			if ($j['table'] == "cohort"){
			$found = true;
			}
			}

			if (!$found){
			$join[] = array(
			"table" => "link_student_cohort",
			"abbreviation" => "lsc",
			"compare" => "lsc.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "cohort",
			"abbreviation" => "c",
			"compare" => "c.id = lsc.id_cohort",
			"type" => "inner"
			);
			}
			$select[] = "c.startdate";
			if ($this->getSanParam('yearinschool')){
			$where[] = "c.startdate LIKE '" . mysql_real_escape_string(substr($this->getSanParam('yearinschool'), 0, 4)) . "%'";
			}
			}

			#			if ($this->getSanParam ( 'showcoursetype' )){
			#				echo "show showcoursetype<br>";
			#			} else {
			#				echo "do not show showcoursetype<br>";
			#			}
			#			if ($this->getSanParam ( 'coursetype' ));

			#			if ($this->getSanParam ( 'showtopic' )){
			#				echo "show showtopic<br>";
			#			} else {
			#				echo "do not show showtopic<br>";
			#			}
			#			if ($this->getSanParam ( 'topic' ));

			#			if ($this->getSanParam ( 'showstudents' )){
			#				echo "show showstudents<br>";
			#			} else {
			#				echo "do not show showstudents<br>";
			#			}
			#			if ($this->getSanParam ( 'students' ));

			$query = "SELECT " . implode(", ", $select) . "\n";
			$query .= " FROM " . $maintable . "\n";
			if (count ($join) > 0){
			foreach ($join as $j){
			$query .= strtoupper($j['type']) . " JOIN " . $j['table'] . " " . $j['abbreviation'] . " ON " . $j['compare'] . "\n";
			}
			}
			if (count ($where) > 0){
			$query .= "WHERE " . implode(" AND ", $where) . "\n";
			}

			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$rowArray = $db->fetchAll ($query);
			$this->view->assign('output',$rowArray);
			$this->view->assign('query',$query);

			#			echo $query;
			#			exit;
			*/
			$this->view->assign('output',array());
			$this->view->assign('query',"");
		}

	}

	public function psGraduatedStudentsAction() {
		$helper = new Helper();
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'institutions', $helper->getInstitutions());
		$this->view->assign ( 'cadres', $helper->getCadres());
		$this->view->assign ( 'cohorts', $helper->getCohorts());
		$this->view->assign ( 'nationalities', $helper->getNationalities());
		$this->view->assign ( 'funding', $helper->getFunding());
		$this->view->assign ( 'tutors', $helper->getTutors());
		$this->view->assign ( 'facilities', $helper->getFacilities());
		$this->view->assign ( 'coursetypes', $helper->AdminCourseTypes());
		$this->view->assign ( 'degrees', $helper->getDegrees());
		#		return $this->trainingReport ();
		if ($this->getSanParam ( 'process' )){
			/*
			$maintable = "person p";
			$select = array();
			$select[] = "p.id as personid";
			$select[] = "p.first_name";
			$select[] = "p.last_name";

			$join = array();
			$join[] = array(
			"table" => "student",
			"abbreviation" => "s",
			"compare" => "s.personid = p.id",
			"type" => "inner"
			);

			$where = array();
			$where[] = "p.is_deleted = 0";

			$sort = array();

			if ($this->getSanParam ( 'showinstitution' )){
			$select[] = "i.institutionname";

			$join[] = array(
			"table" => "link_student_institution",
			"abbreviation" => "lsi",
			"compare" => "lsi.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "institution",
			"abbreviation" => "i",
			"compare" => "i.id = lsi.id_institution",
			"type" => "inner"
			);
			if ($this->getSanParam('institution')){
			$where[] = "i.id = " . $this->getSanParam('institution');
			}
			}

			if ($this->getSanParam ( 'showcohort' )){
			$select[] = "c.cohortname";

			$join[] = array(
			"table" => "link_student_cohort",
			"abbreviation" => "lsc",
			"compare" => "lsc.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "cohort",
			"abbreviation" => "c",
			"compare" => "c.id = lsc.id_cohort",
			"type" => "inner"
			);

			if ($this->getSanParam('cohort')){
			$where[] = "c.id = " . $this->getSanParam('cohort');
			}
			}

			#			if ($this->getSanParam ( 'showcoursename' )){
			#				echo "show showcoursename<br>";
			#			} else {
			#				echo "do not show showcoursename<br>";
			#			}
			#			if ($this->getSanParam ( 'coursename' ));

			#			if ($this->getSanParam ( 'showgrades' )){
			#				echo "show showgrades<br>";
			#			} else {
			#				echo "do not show showgrades<br>";
			#			}
			#			if ($this->getSanParam ( 'grades' ));

			#			if ($this->getSanParam ( 'showexams' )){
			#				echo "show showexams<br>";
			#			} else {
			#				echo "do not show showexams<br>";
			#			}
			#			if ($this->getSanParam ( 'exams' ));

			if ($this->getSanParam ( 'showcadre' )){
			$select[] = "ca.cadrename";

			$join[] = array(
			"table" => "cadres",
			"abbreviation" => "ca",
			"compare" => "ca.id = s.cadre",
			"type" => "inner"
			);

			if ($this->getSanParam('cadre')){
			$where[] = "ca.id = " . $this->getSanParam('cadre');
			}
			}

			if ($this->getSanParam ( 'showyearinschool' )){
			# REQUIRES COHORT LINK
			$found = false;
			foreach ($join as $j){
			if ($j['table'] == "cohort"){
			$found = true;
			}
			}

			if (!$found){
			$join[] = array(
			"table" => "link_student_cohort",
			"abbreviation" => "lsc",
			"compare" => "lsc.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "cohort",
			"abbreviation" => "c",
			"compare" => "c.id = lsc.id_cohort",
			"type" => "inner"
			);
			}
			$select[] = "c.startdate";
			if ($this->getSanParam('yearinschool')){
			$where[] = "c.startdate LIKE '" . mysql_real_escape_string(substr($this->getSanParam('yearinschool'), 0, 4)) . "%'";
			}
			}

			#			if ($this->getSanParam ( 'showcoursetype' )){
			#				echo "show showcoursetype<br>";
			#			} else {
			#				echo "do not show showcoursetype<br>";
			#			}
			#			if ($this->getSanParam ( 'coursetype' ));

			#			if ($this->getSanParam ( 'showtopic' )){
			#				echo "show showtopic<br>";
			#			} else {
			#				echo "do not show showtopic<br>";
			#			}
			#			if ($this->getSanParam ( 'topic' ));

			#			if ($this->getSanParam ( 'showstudents' )){
			#				echo "show showstudents<br>";
			#			} else {
			#				echo "do not show showstudents<br>";
			#			}
			#			if ($this->getSanParam ( 'students' ));

			$query = "SELECT " . implode(", ", $select) . "\n";
			$query .= " FROM " . $maintable . "\n";
			if (count ($join) > 0){
			foreach ($join as $j){
			$query .= strtoupper($j['type']) . " JOIN " . $j['table'] . " " . $j['abbreviation'] . " ON " . $j['compare'] . "\n";
			}
			}
			if (count ($where) > 0){
			$query .= "WHERE " . implode(" AND ", $where) . "\n";
			}

			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$rowArray = $db->fetchAll ($query);
			$this->view->assign('output',$rowArray);
			$this->view->assign('query',$query);

			#			echo $query;
			#			exit;
			*/
			$this->view->assign('output',array());
			$this->view->assign('query',"");
		}
	}

	public function psCourseByStudentCountAction() {
		$helper = new Helper();
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'institutions', $helper->getInstitutions());
		$this->view->assign ( 'cadres', $helper->getCadres());
		$this->view->assign ( 'cohorts', $helper->getCohorts());
		$this->view->assign ( 'nationalities', $helper->getNationalities());
		$this->view->assign ( 'funding', $helper->getFunding());
		$this->view->assign ( 'tutors', $helper->getTutors());
		$this->view->assign ( 'facilities', $helper->getFacilities());
		$this->view->assign ( 'coursetypes', $helper->AdminCourseTypes());
		$this->view->assign ( 'degrees', $helper->getDegrees());
		#		return $this->trainingReport ();
		if ($this->getSanParam ( 'process' )){
			/*
			$maintable = "person p";
			$select = array();
			$select[] = "p.id as personid";
			$select[] = "p.first_name";
			$select[] = "p.last_name";

			$join = array();
			$join[] = array(
			"table" => "student",
			"abbreviation" => "s",
			"compare" => "s.personid = p.id",
			"type" => "inner"
			);

			$where = array();
			$where[] = "p.is_deleted = 0";

			$sort = array();

			if ($this->getSanParam ( 'showinstitution' )){
			$select[] = "i.institutionname";

			$join[] = array(
			"table" => "link_student_institution",
			"abbreviation" => "lsi",
			"compare" => "lsi.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "institution",
			"abbreviation" => "i",
			"compare" => "i.id = lsi.id_institution",
			"type" => "inner"
			);
			if ($this->getSanParam('institution')){
			$where[] = "i.id = " . $this->getSanParam('institution');
			}
			}

			if ($this->getSanParam ( 'showcohort' )){
			$select[] = "c.cohortname";

			$join[] = array(
			"table" => "link_student_cohort",
			"abbreviation" => "lsc",
			"compare" => "lsc.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "cohort",
			"abbreviation" => "c",
			"compare" => "c.id = lsc.id_cohort",
			"type" => "inner"
			);

			if ($this->getSanParam('cohort')){
			$where[] = "c.id = " . $this->getSanParam('cohort');
			}
			}

			#			if ($this->getSanParam ( 'showcoursename' )){
			#				echo "show showcoursename<br>";
			#			} else {
			#				echo "do not show showcoursename<br>";
			#			}
			#			if ($this->getSanParam ( 'coursename' ));

			#			if ($this->getSanParam ( 'showgrades' )){
			#				echo "show showgrades<br>";
			#			} else {
			#				echo "do not show showgrades<br>";
			#			}
			#			if ($this->getSanParam ( 'grades' ));

			#			if ($this->getSanParam ( 'showexams' )){
			#				echo "show showexams<br>";
			#			} else {
			#				echo "do not show showexams<br>";
			#			}
			#			if ($this->getSanParam ( 'exams' ));

			if ($this->getSanParam ( 'showcadre' )){
			$select[] = "ca.cadrename";

			$join[] = array(
			"table" => "cadres",
			"abbreviation" => "ca",
			"compare" => "ca.id = s.cadre",
			"type" => "inner"
			);

			if ($this->getSanParam('cadre')){
			$where[] = "ca.id = " . $this->getSanParam('cadre');
			}
			}

			if ($this->getSanParam ( 'showyearinschool' )){
			# REQUIRES COHORT LINK
			$found = false;
			foreach ($join as $j){
			if ($j['table'] == "cohort"){
			$found = true;
			}
			}

			if (!$found){
			$join[] = array(
			"table" => "link_student_cohort",
			"abbreviation" => "lsc",
			"compare" => "lsc.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "cohort",
			"abbreviation" => "c",
			"compare" => "c.id = lsc.id_cohort",
			"type" => "inner"
			);
			}
			$select[] = "c.startdate";
			if ($this->getSanParam('yearinschool')){
			$where[] = "c.startdate LIKE '" . mysql_real_escape_string(substr($this->getSanParam('yearinschool'), 0, 4)) . "%'";
			}
			}

			#			if ($this->getSanParam ( 'showcoursetype' )){
			#				echo "show showcoursetype<br>";
			#			} else {
			#				echo "do not show showcoursetype<br>";
			#			}
			#			if ($this->getSanParam ( 'coursetype' ));

			#			if ($this->getSanParam ( 'showtopic' )){
			#				echo "show showtopic<br>";
			#			} else {
			#				echo "do not show showtopic<br>";
			#			}
			#			if ($this->getSanParam ( 'topic' ));

			#			if ($this->getSanParam ( 'showstudents' )){
			#				echo "show showstudents<br>";
			#			} else {
			#				echo "do not show showstudents<br>";
			#			}
			#			if ($this->getSanParam ( 'students' ));

			$query = "SELECT " . implode(", ", $select) . "\n";
			$query .= " FROM " . $maintable . "\n";
			if (count ($join) > 0){
			foreach ($join as $j){
			$query .= strtoupper($j['type']) . " JOIN " . $j['table'] . " " . $j['abbreviation'] . " ON " . $j['compare'] . "\n";
			}
			}
			if (count ($where) > 0){
			$query .= "WHERE " . implode(" AND ", $where) . "\n";
			}

			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$rowArray = $db->fetchAll ($query);
			$this->view->assign('output',$rowArray);
			$this->view->assign('query',$query);

			#			echo $query;
			#			exit;
			*/
			$this->view->assign('output',array());
			$this->view->assign('query',"");
		}
	}

	public function psCourseByNameAction() {
		$helper = new Helper();
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'institutions', $helper->getInstitutions());
		$this->view->assign ( 'cadres', $helper->getCadres());
		$this->view->assign ( 'cohorts', $helper->getCohorts());
		$this->view->assign ( 'nationalities', $helper->getNationalities());
		$this->view->assign ( 'funding', $helper->getFunding());
		$this->view->assign ( 'tutors', $helper->getTutors());
		$this->view->assign ( 'facilities', $helper->getFacilities());
		$this->view->assign ( 'coursetypes', $helper->AdminCourseTypes());
		$this->view->assign ( 'degrees', $helper->getDegrees());

		#		return $this->trainingReport ();
		if ($this->getSanParam ( 'process' )){
			/*
			$maintable = "person p";
			$select = array();
			$select[] = "p.id as personid";
			$select[] = "p.first_name";
			$select[] = "p.last_name";

			$join = array();
			$join[] = array(
			"table" => "student",
			"abbreviation" => "s",
			"compare" => "s.personid = p.id",
			"type" => "inner"
			);

			$where = array();
			$where[] = "p.is_deleted = 0";

			$sort = array();

			if ($this->getSanParam ( 'showinstitution' )){
			$select[] = "i.institutionname";

			$join[] = array(
			"table" => "link_student_institution",
			"abbreviation" => "lsi",
			"compare" => "lsi.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "institution",
			"abbreviation" => "i",
			"compare" => "i.id = lsi.id_institution",
			"type" => "inner"
			);
			if ($this->getSanParam('institution')){
			$where[] = "i.id = " . $this->getSanParam('institution');
			}
			}

			if ($this->getSanParam ( 'showcohort' )){
			$select[] = "c.cohortname";

			$join[] = array(
			"table" => "link_student_cohort",
			"abbreviation" => "lsc",
			"compare" => "lsc.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "cohort",
			"abbreviation" => "c",
			"compare" => "c.id = lsc.id_cohort",
			"type" => "inner"
			);

			if ($this->getSanParam('cohort')){
			$where[] = "c.id = " . $this->getSanParam('cohort');
			}
			}

			#			if ($this->getSanParam ( 'showcoursename' )){
			#				echo "show showcoursename<br>";
			#			} else {
			#				echo "do not show showcoursename<br>";
			#			}
			#			if ($this->getSanParam ( 'coursename' ));

			#			if ($this->getSanParam ( 'showgrades' )){
			#				echo "show showgrades<br>";
			#			} else {
			#				echo "do not show showgrades<br>";
			#			}
			#			if ($this->getSanParam ( 'grades' ));

			#			if ($this->getSanParam ( 'showexams' )){
			#				echo "show showexams<br>";
			#			} else {
			#				echo "do not show showexams<br>";
			#			}
			#			if ($this->getSanParam ( 'exams' ));

			if ($this->getSanParam ( 'showcadre' )){
			$select[] = "ca.cadrename";

			$join[] = array(
			"table" => "cadres",
			"abbreviation" => "ca",
			"compare" => "ca.id = s.cadre",
			"type" => "inner"
			);

			if ($this->getSanParam('cadre')){
			$where[] = "ca.id = " . $this->getSanParam('cadre');
			}
			}

			if ($this->getSanParam ( 'showyearinschool' )){
			# REQUIRES COHORT LINK
			$found = false;
			foreach ($join as $j){
			if ($j['table'] == "cohort"){
			$found = true;
			}
			}

			if (!$found){
			$join[] = array(
			"table" => "link_student_cohort",
			"abbreviation" => "lsc",
			"compare" => "lsc.id_student = s.id",
			"type" => "inner"
			);

			$join[] = array(
			"table" => "cohort",
			"abbreviation" => "c",
			"compare" => "c.id = lsc.id_cohort",
			"type" => "inner"
			);
			}
			$select[] = "c.startdate";
			if ($this->getSanParam('yearinschool')){
			$where[] = "c.startdate LIKE '" . mysql_real_escape_string(substr($this->getSanParam('yearinschool'), 0, 4)) . "%'";
			}
			}

			#			if ($this->getSanParam ( 'showcoursetype' )){
			#				echo "show showcoursetype<br>";
			#			} else {
			#				echo "do not show showcoursetype<br>";
			#			}
			#			if ($this->getSanParam ( 'coursetype' ));

			#			if ($this->getSanParam ( 'showtopic' )){
			#				echo "show showtopic<br>";
			#			} else {
			#				echo "do not show showtopic<br>";
			#			}
			#			if ($this->getSanParam ( 'topic' ));

			#			if ($this->getSanParam ( 'showstudents' )){
			#				echo "show showstudents<br>";
			#			} else {
			#				echo "do not show showstudents<br>";
			#			}
			#			if ($this->getSanParam ( 'students' ));

			$query = "SELECT " . implode(", ", $select) . "\n";
			$query .= " FROM " . $maintable . "\n";
			if (count ($join) > 0){
			foreach ($join as $j){
			$query .= strtoupper($j['type']) . " JOIN " . $j['table'] . " " . $j['abbreviation'] . " ON " . $j['compare'] . "\n";
			}
			}
			if (count ($where) > 0){
			$query .= "WHERE " . implode(" AND ", $where) . "\n";
			}

			$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$rowArray = $db->fetchAll ($query);
			$this->view->assign('output',$rowArray);
			$this->view->assign('query',$query);

			#			echo $query;
			#			exit;
			*/
			$this->view->assign('output',array());
			$this->view->assign('query',"");
		}
	}

	public function psCohortByParticipantCountAction() {
		$helper = new Helper();
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'institutions', $helper->getInstitutions());
		$this->view->assign ( 'cadres', $helper->getCadres());
		$this->view->assign ( 'cohorts', $helper->getCohorts());
		$this->view->assign ( 'nationalities', $helper->getNationalities());
		$this->view->assign ( 'funding', $helper->getFunding());
		$this->view->assign ( 'tutors', $helper->getTutors());
		$this->view->assign ( 'facilities', $helper->getFacilities());
		$this->view->assign ( 'facilitytypes', $helper->getFacilityTypes());
		$this->view->assign ( 'sponsors', $helper->getSponsors());
		$this->view->assign ( 'coursetypes', $helper->AdminCourseTypes());
		$this->view->assign ( 'degrees', $helper->getDegrees());
	}

	public function psInstitutionInformationAction() {
		$helper = new Helper();
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'institutions', $helper->getInstitutions());
		$this->view->assign ( 'cadres', $helper->getCadres());
		$this->view->assign ( 'cohorts', $helper->getCohorts());
		$this->view->assign ( 'nationalities', $helper->getNationalities());
		$this->view->assign ( 'funding', $helper->getFunding());
		$this->view->assign ( 'tutors', $helper->getTutors());
		$this->view->assign ( 'facilities', $helper->getFacilities());
		$this->view->assign ( 'facilitytypes', $helper->getFacilityTypes());
		$this->view->assign ( 'sponsors', $helper->getSponsors());
		$this->view->assign ( 'coursetypes', $helper->AdminCourseTypes());
		$this->view->assign ( 'degrees', $helper->getDegrees());
	}

	public function psTutorByNameAction() {
		$helper = new Helper();
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'institutions', $helper->getInstitutions());
		$this->view->assign ( 'cadres', $helper->getCadres());
		$this->view->assign ( 'cohorts', $helper->getCohorts());
		$this->view->assign ( 'nationalities', $helper->getNationalities());
		$this->view->assign ( 'funding', $helper->getFunding());
		$this->view->assign ( 'tutors', $helper->getTutors());
		$this->view->assign ( 'facilities', $helper->getFacilities());
		$this->view->assign ( 'facilitytypes', $helper->getFacilityTypes());
		$this->view->assign ( 'sponsors', $helper->getSponsors());
		$this->view->assign ( 'coursetypes', $helper->AdminCourseTypes());
		$this->view->assign ( 'degrees', $helper->getDegrees());
	}

	public function psFacilityReportAction() {
		$helper = new Helper();
		$this->view->assign ( 'mode', 'id' );
		$this->view->assign ( 'institutions', $helper->getInstitutions());
		$this->view->assign ( 'cadres', $helper->getCadres());
		$this->view->assign ( 'cohorts', $helper->getCohorts());
		$this->view->assign ( 'nationalities', $helper->getNationalities());
		$this->view->assign ( 'funding', $helper->getFunding());
		$this->view->assign ( 'tutors', $helper->getTutors());
		$this->view->assign ( 'facilities', $helper->getFacilities());
		$this->view->assign ( 'facilitytypes', $helper->getFacilityTypes());
		$this->view->assign ( 'sponsors', $helper->getSponsors());
		$this->view->assign ( 'coursetypes', $helper->AdminCourseTypes());
		$this->view->assign ( 'degrees', $helper->getDegrees());
	}
}