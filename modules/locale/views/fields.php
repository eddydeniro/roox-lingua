
<?php 
  $current_page = 'fields_' . $_GET['entities_id'];
  require('header.php'); 
?>

<h4><?php echo TEXT_FIELDS_CONFIGURATION ?></h4>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th>#</th>
    <th><?php echo TEXT_FORM_TAB ?></th>
    <th width="100%"><?php echo TEXT_NAME ?></th>    
    <th><?php echo TEXT_SHORT_NAME ?></th>
  </tr>
</thead>
<tbody>
<?php

$fields_sql_query = '';

$entity_info = db_find('app_entities', $_GET['entities_id']);

//include fieldtype_parent_item_id only for sub entities
if($entity_info['parent_id']==0)
{
	$fields_sql_query .= " and f.type not in ('fieldtype_parent_item_id')";
}

$reserverd_fields_types = array_merge(fields_types::get_reserved_data_types(),fields_types::get_users_types());
$reserverd_fields_types_list = "'" . implode("','", $reserverd_fields_types). "'";
//'fieldtype_id','fieldtype_date_added','fieldtype_created_by','fieldtype_parent_item_id','fieldtype_date_updated'
$fields_query = db_query("SELECT f.*,fr.sort_order AS form_rows_sort_order,right(f.forms_rows_position,1) AS forms_rows_pos, t.name AS tab_name, IF(f.type IN (" . $reserverd_fields_types_list . "),-1,t.sort_order) AS tab_sort_order from app_fields f left join app_forms_rows fr on fr.id=LEFT(f.forms_rows_position,length(f.forms_rows_position)-2), app_forms_tabs t WHERE f.type NOT IN ('fieldtype_action', 'fieldtype_id') AND f.entities_id='" . $_GET['entities_id'] . "' AND f.forms_tabs_id=t.id {$fields_sql_query} ORDER BY tab_sort_order, t.name, form_rows_sort_order, forms_rows_pos, f.sort_order, f.name");

if(db_num_rows($fields_query)==0) echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; 

while($v = db_fetch_array($fields_query)):
    $cfg = new settings($v['configuration']);

    $heading_note = ($v['is_heading'] ? ' <span class="label label-info">' . TEXT_HEADING . '</span>': '');
?>
<tr>

<?php if(in_array($v['type'], $reserverd_fields_types)){ ?>
	
	<td><?php echo (in_array($v['type'],fields_types::get_reserved_types()) ? "" : $v['id']) ?></td>
	<td></td>
	<td><?php echo (strlen($v['name']??'') ? $v['name']:fields_types::get_title($v['type']??'')) . $heading_note ?></td>
	<td><?php echo $v['short_name']?></td>
	
<?php } else { ?>
  
  <td><?php echo $v['id'] ?></td>
  <td><?php echo $v['tab_name'] ?></td>    
  <td><?php echo $v['name'] ?></td>
  <td><?php echo $v['short_name']?></td>

<?php }?>
    
</tr>  
<?php endwhile ?>
</tbody>
</table>
</div>