<?php require component_path(ROOX_PLUGIN . "/{$module_name}/header"); ?>

<!-- <h4><?php echo TEXT_FORM_TAB ?></h4> -->

<?php
    $locked = CFG_APP_LANGUAGE == $app_user['language'] ? true : false;
    echo ($locked ? TEXT_CANNOT_TRANSLATE : form_tag('language_form_tabs', url_for("{$plugin_name}/{$module_name}/forms_tabs", 'action=save')) . submit_tag(TEXT_BUTTON_SAVE) . input_hidden_tag('entities_id', $_GET['entities_id']));
?>
<div class="table-scrollable">
<table class="tree-table table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ID ?></th>
    <th style="width:35%;"><?php echo TEXT_NAME ?></th>
    <th style="width:60%;"><?php echo TEXT_DESCRIPTION ?></th>        
  </tr>
</thead>
<tbody>
<?php
$tabs = forms_tabs::get_tree(_GET('entities_id'));

if(count($tabs)==0) echo '<tr><td colspan="3">' . TEXT_NO_RECORDS_FOUND. '</td></tr>';
$language_tabs = ${ROOX_PLUGIN . '_language_cache'}['forms_tabs'][_GET('entities_id')] ?? []; 
$checked_fields = ['name', 'description'];

foreach($tabs as $v):
  foreach ($checked_fields as $field) 
  {
    ${$field} = empty($language_tabs[$v['id']][$field]) ? $v[$field] : $language_tabs[$v['id']][$field];  
  }
?>
<tr>
  <td><?php echo "<div style='padding:8px 0;'>{$v['id']}</div>" ?></td>
  <td> 
    <?php
        echo $locked ? "<div style='padding:8px 0;'>{$name}</div>" : input_tag("forms_tabs[{$v['id']}][name]", $name, array('class' => 'form-control input-medium transparent'));
    ?>
  </td>
  <td>
    <?php 
      echo ($locked ? "<div style='padding:8px 0;'>{$description}</div>" : input_tag("forms_tabs[{$v['id']}][description]", $description, array('class' => 'form-control input-medium transparent'))); 
    ?>
  </td>      
</tr>  
<?php endforeach ?>
</tbody>
</table>
</div>
<?php
    echo $locked ? "" : "</form>";
?>