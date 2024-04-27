<?php
    require component_path(ROOX_PLUGIN . "/{$module_name}/header");
    echo ($locked ? TEXT_CANNOT_TRANSLATE : form_tag('language_' . $name_key, url_for("{$plugin_name}/{$module_name}/{$name_key}", 'action=save')) . submit_tag(TEXT_BUTTON_SAVE));
?>
<div class="table-scrollable">
<table class="table table-bordered">
<thead>
  <tr>
    <th><?php echo TEXT_ID ?></th>
    <th style="width:25%;"><?php echo TEXT_ENTITY ?></th>
    <th style="width:25%;"><?php echo TEXT_FIELD ?></th>
    <th style="width:50%;"><?php echo TEXT_GLOBAL_LIST_CHOICES_CONFIG ?></th>        
  </tr>
</thead>
<tbody>
<?php
$lists = ${ROOX_PLUGIN . '_language_cache'}['fields_with_choices'] ?? []; 
if(count($lists)==0) echo '<tr><td colspan="3">' . TEXT_NO_RECORDS_FOUND. '</td></tr>';
$field_choices = ${ROOX_PLUGIN . '_language_cache'}[$name_key] ?? []; 
$html = "";
foreach($lists as $k=>$v):
    $choices_count = count($field_choices[$k]);
    $n = 0;
    $field = ${ROOX_PLUGIN . '_language_cache'}['fields'][$v['entities_id']][$k]['name'] ?? $v['field_name'];
    $entity = ${ROOX_PLUGIN . '_language_cache'}['entities'][$v['entities_id']]['name'];
    foreach ($field_choices[$k] as $key => $value) 
    {
        $name = $value['name'] ?: $value['original'];
        $input = $locked ? "<div style='padding:8px 5px;'>{$name}</div>" : input_tag("{$name_key}[{$key}][name]", $name, array('class' => 'form-control input-medium transparent'));
        $field_name = $n ? "" : "<td rowspan='{$choices_count}'><div style='padding:8px 5px;'>{$entity}</div></td><td rowspan='{$choices_count}'><div style='padding:8px 5px;'>{$field}</div></td>";
        $html .= "<tr><td><div style='padding:8px 0;'>{$key}</div></td>{$field_name}<td>$input</td></tr>";
        $n++;
    }
endforeach;   
echo $html; 
?>
</tbody>
</table>
</div>
<?php
    echo $locked ? "" : "</form>";
?>