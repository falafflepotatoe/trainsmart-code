<?php

require_once ('app/controllers/ITechController.php');

class ReportFilterHelpers extends ITechController {
	
	public function __construct($request, $response, $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs = array () );
	
	}
	
	/**
	 * Get the location filter values
	 *
	 * @param unknown_type $criteria
	 * @param unknown_type $prefix
	 * 
	 * return $criteria, location tier, location id, city, and city_parent_id
	 */
  protected function getLocationCriteriaValues($criteria = array(), $prefix = '') {
  	if ( $prefix != '' ) $prefix .= '_';
    $provinces = $this->getSanParam ( $prefix.'province_id' );
    $districts = $this->getSanParam ( $prefix.'district_id' );
    $region_cs = $this->getSanParam ( $prefix.'region_c_id' );
    $criteria [$prefix.'city'] = $this->getSanParam ( $prefix.'city' );

    $criteria [$prefix.'province_id'] = $provinces;
    if (is_array ( $provinces ) ) {
    	 if ( $provinces [0] === "") { // "All"
        $criteria [$prefix.'province_id'] = array ();
    	 }
    }
    if (is_array ( $districts ) ) {
       if ( $districts [0] === "") { // "All"
        $criteria [$prefix.'district_id'] = array ();
       } else {
        foreach($districts as $did => $d) {
          if (strstr ( $d, '_' ) !== false) {
              $parts = explode ( '_', $d );
              $districts [$did] = $parts [1];
          }
        }
        $criteria [$prefix.'district_id'] = $districts;
       }
    } else {
         if (strstr ( $districts, '_' ) !== false) {
              $parts = explode ( '_', $districts );
              $districts = $parts [1];
          }
      $criteria [$prefix.'district_id'] = $districts;
    }
    
    if (is_array ( $region_cs ) ) {
       if ( $region_cs [0] === "") { // "All"
        $criteria [$prefix.'region_c_id'] = array ();
       } else {
        foreach($region_cs as $did => $d) {
          if (strstr ( $d, '_' ) !== false) {
              $parts = explode ( '_', $d );
              $region_cs [$did] = $parts [2];
          }
        }
        $criteria [$prefix.'region_c_id'] = $region_cs;
       }
    } else {
         if (strstr ( $region_cs, '_' ) !== false) {
              $parts = explode ( '_', $region_cs );
              $region_cs = $parts [2];
          }
      $criteria [$prefix.'region_c_id'] = $region_cs;
    }
    
    
    $city_parent_id = 0;
    if ( $this->setting ( 'display_region_c' ) ) {
      $city_parent_id = $region_cs;
    } else if ( $this->setting ( 'display_region_b' ) ) {
      $city_parent_id = $districts;
    } else {
      $city_parent_id = $provinces;
    }
    $criteria [$prefix.'city_parent_id'] = $city_parent_id;
    
    
    $location_tier = 1;
    $location_id = $criteria [$prefix.'province_id'];
    if ( $criteria [$prefix.'district_id'] ) {
      $location_id = $criteria [$prefix.'district_id'];
      $location_tier = 2;
    }
    if ( $criteria [$prefix.'region_c_id'] ) {
      $location_id = $criteria [$prefix.'region_c_id'];
      $location_tier = 3;
    } 
    
    return array($criteria, $location_tier, $location_id);
  }
	
}

?>