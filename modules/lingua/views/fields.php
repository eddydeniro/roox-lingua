
<?php 
  require component_path(ROOX_PLUGIN . "/{$module_name}/header"); 
?>

<!-- <h4><?php echo TEXT_FIELDS_CONFIGURATION ?></h4> -->

<?php
  $locked = CFG_APP_LANGUAGE == $app_user['language'] ? true : false;
  echo ($locked ? TEXT_CANNOT_TRANSLATE : form_tag('language_fields_form', url_for("{$plugin_name}/{$module_name}/fields", 'action=save')).submit_tag(TEXT_BUTTON_SAVE) . input_hidden_tag('entities_id', $_GET['entities_id']));
?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ID ?></th>
    <th><?php echo TEXT_FORM_TAB ?></th>
    <th><?php echo TEXT_NAME ?></th>
    <th><?php echo TEXT_SHORT_NAME ?></th>
    <th><?php echo TEXT_TOOLTIP ?></th>
    <th><?php echo TEXT_TOOLTIP_ON_ITEM_PAGE ?></th>
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
$fields_query = db_query("SELECT f.*, fr.sort_order AS form_rows_sort_order, RIGHT(f.forms_rows_position, 1) AS forms_rows_pos, t.name AS tab_name, IF(f.type IN (" . $reserverd_fields_types_list . "),-1,t.sort_order) AS tab_sort_order FROM app_fields f LEFT JOIN app_forms_rows fr ON fr.id=LEFT(f.forms_rows_position,LENGTH(f.forms_rows_position)-2), app_forms_tabs t WHERE f.type NOT IN ('fieldtype_action', 'fieldtype_id') AND f.entities_id='" . $_GET['entities_id'] . "' AND f.forms_tabs_id=t.id {$fields_sql_query} ORDER BY tab_sort_order, t.name, form_rows_sort_order, forms_rows_pos, f.sort_order, f.name");

if(db_num_rows($fields_query)==0) echo '<tr><td colspan="6">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; 
$current_fields_data = ${ROOX_PLUGIN . '_language_cache'}['fields'][$_GET['entities_id']] ?? [];
$checked_fields = ['name', 'short_name', 'tooltip', 'tooltip_item_page'];
while($v = db_fetch_array($fields_query)):
    foreach ($checked_fields as $field) 
    {
      ${$field} = empty($current_fields_data[$v['id']][$field]) ? $v[$field] : $current_fields_data[$v['id']][$field];  
    }
?>
<tr>

<?php if(in_array($v['type'], $reserverd_fields_types)){ ?>
	
	<td><?php echo (in_array($v['type'],fields_types::get_reserved_types()) ? "" : $v['id']) ?></td>
	<td></td>
	<td><?php echo input_tag("fields[{$v['id']}][name]", ($name ? $name : fields_types::get_title($v['type']??'')), array('class' => 'form-control input-medium transparent')) ?></td>
	
<?php } else { ?>
  
  <td><div style='padding:8px 0;'><?php echo $v['id'] ?></div></td>
  <td><div style='padding:8px 0;'><?php echo $v['tab_name'] ?></div></td>    
  <td><?php echo input_tag("fields[{$v['id']}][name]", $name, array('class' => 'form-control input-medium transparent')) ?></td>

<?php }?>
  <td><?php echo input_tag("fields[{$v['id']}][short_name]", $short_name, array('class' => 'form-control input-medium transparent')) ?></td>
  <td><?php echo input_tag("fields[{$v['id']}][tooltip]", $tooltip, array('class' => 'form-control input-medium transparent')) ?></td>
  <td><?php echo input_tag("fields[{$v['id']}][tooltip_item_page]", $tooltip_item_page, array('class' => 'form-control input-medium transparent')) ?></td>
  
</tr>  
<?php endwhile ?>
</tbody>
</table>
</div>
<?php 
  echo $locked ? "" : "</form>";
?>