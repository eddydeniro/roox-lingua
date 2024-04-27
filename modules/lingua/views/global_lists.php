<?php
    require component_path(ROOX_PLUGIN . "/{$module_name}/header");
    echo ($locked ? TEXT_CANNOT_TRANSLATE : form_tag('language_'.$name_key, url_for("{$plugin_name}/{$module_name}/{$name_key}", 'action=save')) . submit_tag(TEXT_BUTTON_SAVE));
?>
<div class="table-scrollable">
<table class="table table-bordered">
<thead>
  <tr>
    <th><?php echo TEXT_ID ?></th>
    <th style="width:35%;"><?php echo TEXT_MENU_GLOBAL_LISTS ?></th>
    <th style="width:60%;"><?php echo TEXT_GLOBAL_LIST_CHOICES_CONFIG ?></th>        
  </tr>
</thead>
<tbody>
<?php
$lists = global_lists::get_lists_choices();

if(count($lists)==0) echo '<tr><td colspan="3">' . TEXT_NO_RECORDS_FOUND. '</td></tr>';
$global_list_choices = ${ROOX_PLUGIN . '_language_cache'}[$name_key] ?? [];
$html = "";
foreach($lists as $k=>$v):
    if(!$v)
    {
        continue;
    }
    if(empty($global_list_choices[$k]))
    {
        continue;
    }
    $choices_count = count($global_list_choices[$k]);
    $n = 0;
    foreach ($global_list_choices[$k] as $key => $value) 
    {
        $name = $value['name'] ?: $value['original'];
        $input = $locked ? "<div style='padding:8px 5px;'>{$name}</div>" : input_tag("{$name_key}[{$key}][name]", $name, array('class' => 'form-control input-medium transparent'));
        $global_lists_name = $n ? "" : "<td rowspan='{$choices_count}'><div style='padding:8px 5px;'>{$v}</div></td>";
        $html .= "<tr><td><div style='padding:8px 0;'>{$key}</div></td>{$global_lists_name}<td>$input</td></tr>";
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