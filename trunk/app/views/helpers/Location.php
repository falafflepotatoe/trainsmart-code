<?php 
require_once('views/helpers/ITechTranslate.php');
/*<script type="text/javascript">
<!--//--><![CDATA[//><!--

YAHOO.util.Event.onDOMReady(function() {
	ITECH.LocationData = [
<?php 
$locationRows = array();
foreach($locationData as $location) {
  $locationRows []= '{name:"", id:'.$location['id'].' , tier:'.$location['tier'].', parent_id:'.$location['parent_id'].'}';
}
echo implode(',',$locationRows);
?>
  ];

});
//--><!]]> 
</script>
*/

//return parent1_parent2_id
function buildId($tier, $locations, $id) {
	$parents = '';
	$parent_id = (isset( $locations[$id]) ? $locations[$id]['parent_id']: 0);
	while($tier > 1 ) {
		if ($parent_id) {
		  $parents = $parent_id.'_'.$parents;
		  $parent_id = $locations[$parent_id]['parent_id'];
		} else {
      $parents = '0_'.$parents;
		}
		
	  $tier--;
	}
	
	return $parents.$id;
}

function renderFilter($locations, $tier, $widget_id, $default_val_id = false, $child_widget_id = false, $is_multiple = false) { 
  if ( $default_val_id === false) {
    foreach ( $locations as $val ) {
        if ( ($val['tier'] == $tier) && $val['is_default'])
          $default_val_id = $val['id'];
    }
  }
	
	?>
  <select id="<?php echo $widget_id;?>" name="<?php echo $widget_id;?><?php if ($is_multiple) echo '[]';?>" <?php if ( $is_multiple) echo 'multiple="multiple" size="10"';?> 
  <?php if ($child_widget_id ) { ?>onchange="setChildStatus_<?php echo $widget_id;?>();" <?php }?>> 
    <option value="">--<?php tp('choose');?>--</option>
    <?php
      foreach ( $locations as $val ) {
        if ( $val['tier'] == $tier) {
        	  $selected = '';
        	  if ( is_array($default_val_id) && (@in_array($val['id'], $default_val_id) ) ) {
        	   $selected = 'selected="selected"';
            } else if ( !is_array($default_val_id) && ($val['id'] === $default_val_id) ) {
        	   $selected = 'selected="selected"';
        	  }
            echo '<option value="'.buildId($tier, $locations, $val['id']).'" '.$selected.'>'.$val['name'].'</option>';      
        }
      }
    ?>
  </select>
  <?php
if ( $child_widget_id ) {?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--

function setChildStatus_<?php echo $widget_id;?>() {
	var widgetObj = YAHOO.util.Dom.get('<?php echo $widget_id;?>');
	setChildStatus(widgetObj.selectedIndex,'<?php echo $child_widget_id;?>','<?php echo $widget_id;?>');
}

YAHOO.util.Event.onDOMReady(function () {
	setChildStatus_<?php echo $widget_id;?>();
});
//--><!]]>
</script>
<?php }
}

function renderCityAutocomplete($prefix, $container, $data_url, $num_tiers) {
	$prefix = $prefix.'_';
?>
 <script type="text/javascript">
     <!--//--><![CDATA[//><!--

    var autoComp = makeAutocomplete('<?php echo $prefix;?>city', '<?php echo $container;?>', '<?php echo $data_url;?>' );
    appendExtraInfo(autoComp,<?php echo $num_tiers - 1;?>);
    // array can contain object references, element ids, or both
    function suggestRegions( oSelf , elItem , oData ) {
      var parent_id = elItem[2][<?php echo ($num_tiers)*2 - 1;?>];
      var selected = 0;
      selected = setSelected('<?php echo $prefix;?>province_id', parent_id, false);
      setChildStatus(selected,'<?php echo $prefix;?>district_id','<?php echo $prefix;?>province_id');
      <?php if ($num_tiers > 2 ) { ?>
      selected = setSelected('<?php echo $prefix;?>district_id', elItem[2][<?php echo ($num_tiers)*2 - 2;?>], true);
      <?php } ?>
      <?php if ($num_tiers > 3 ) { ?>
       setChildStatus(selected,'<?php echo $prefix;?>region_c_id','<?php echo $prefix;?>district_id');
       selected = setSelected('<?php echo $prefix;?>region_c_id', elItem[2][<?php echo ($num_tiers)*2 - 3;?>], true);
      <?php } ?>
      var is_new_chk = YAHOO.util.Dom.get('is_new_<?php echo $prefix;?>city');
      if ( is_new_chk ) is_new_chk.checked = false;
    }
     autoComp.itemSelectEvent.subscribe(suggestRegions);
    //--><!]]>
  </script>


<?php 	
}

function renderFacilityDropDown($facilities, $selected_index)
{
  $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
  $sql = 'SELECT DISTINCT
       f.id as id,
       f.location_id AS "zone1",
       l2.id as "zone2",
       l2.parent_id AS "zone3"
      FROM facility f
      LEFT JOIN location l1 ON f.location_id = l1.id
      LEFT JOIN location l2 ON l1.parent_id = l2.id
      ';
  $rowArray = $db->fetchAll($sql); // get 3 tiers of parent ids
  $location_classes = array();
  foreach($rowArray as $row){
    // store parent location ids in a hash. hash[id] = "zone1id zone2id zone3id"
    $location_classes[$row['id']] = trim($row['zone1'].' '.$row['zone2'].' '.$row['zone3']);
  }
  $dupe = '';
  // lets build a visible <select> and also a display:none <select> with all locations
  echo '<select id="facilityInput" name="facilityInput">';
  echo '<option class="defaultval" value="">--'.t('choose').'--</option>';
  foreach ( $facilities as $vals ) {
    echo '<option class="'.$location_classes[$vals['id']].'" value="'.$vals['id'].'" '.($selected_index == $vals['id']?'SELECTED':'').'>'.$vals['facility_name'].'</option>';
    $dupe .= '<option class="'.$location_classes[$vals['id']].'" value="'.$vals['id'].'" '.($selected_index == $vals['id']?'SELECTED':'').'>'.$vals['facility_name'].'</option>';
  }
  echo '</select>';
  echo '<div style="display:none;">';
  echo '<select id="facilityInputHidden" name="facilityInputHidden" style="display:none;visibility:hidden;">';
  echo '<option class="defaultval" value="">--'.t('choose').'--</option>';
  echo $dupe;
  echo '</select>';
  echo '</div>';
  
  // selects have a value attribute "region1_region2_region3", ie: 555_423_1
  // lets filter facility list by the last value when the user chooses something
  $js = '
      YAHOO.util.Event.onDOMReady(function () {
        var regionSelectElements = YAHOO.util.Dom.get(["province_id","district_id","region_c_id"]);
        for(index in regionSelectElements){
          YAHOO.util.Event.addListener(regionSelectElements[index], "change", function () {
            var compare_id = "";
            if (this.value != ""){
              classes = this.value.split("_");
              compare_id = classes[classes.length-1];
            } else {
              var district_id = YAHOO.util.Dom.get("district_id");
              if(district_id.value != ""){
                classes = district_id.value.split("_");
              } else {
                classes = YAHOO.util.Dom.get("province_id").value.split("_");
              }
              compare_id = classes[classes.length-1];
            }

            allFacilities = YAHOO.util.Dom.get("facilityInputHidden");
            cnt = allFacilities.length;
            facilityInput = YAHOO.util.Dom.get("facilityInput");
            facilityInput.innerHTML = "";

            for(i = 0; i < cnt; i++){
              row = allFacilities[i];

              if(compare_id == "" || YAHOO.util.Dom.hasClass(row, compare_id) || YAHOO.util.Dom.hasClass(row, "defaultval")){
                facilityInput.innerHTML += row.outerHTML;
              }

            }
          });
        }
      });
  ';
  echo '<script type="Text/JavaScript">'.$js.'</script>';


}