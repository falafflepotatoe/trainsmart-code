<?php 
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