<?php require component_path(ROOX_PLUGIN . "/{$module_name}/header"); ?>

<!-- <h4><?php echo TEXT_MENU_CONFIGURATION_MENU ?></h4> -->

<?php
    $locked = CFG_APP_LANGUAGE == $app_user['language'] ? true : false;
    echo ($locked ? "" : form_tag('language_entities_menu', url_for("{$plugin_name}/{$module_name}/entities_menu", 'action=save')) . submit_tag(TEXT_BUTTON_SAVE));
?>
<div class="table-scrollable">
<table class="tree-table table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ID ?></th>
    <th style="width:100%;"><?php echo TEXT_NAME ?></th>        
  </tr>
</thead>
<tbody>
<?php
$menu = entities_menu::get_tree();
if(count($menu)==0) echo '<tr><td colspan="2">' . TEXT_NO_RECORDS_FOUND. '</td></tr>';
$language_menu = ${ROOX_PLUGIN . '_language_cache'}['entities_menu'] ?? []; 
foreach($menu as $v):
?>
<tr>
  <td><?php echo "<div style='padding:8px 0;'>{$v['id']}</div>" ?></td>
  <td>

    <?php echo '<div class="tt" data-tt-id="menu_' . $v['id'] . '" ' . ($v['parent_id'] > 0 ? 'data-tt-parent="menu_' . $v['parent_id'] . '"' : '') . '></div>' ?>
    <div class="input_adj">                        
        <?php
          $name = empty($language_menu[$v['id']]['name']) ? $v['name'] : $language_menu[$v['id']]['name'];
          echo ($locked ? "<div style='padding:8px 0;'>{$name}</div>" : input_tag("entities_menu[{$v['id']}][name]", $name, array('class' => 'form-control input-medium transparent'))); 
        ?>
    </div>
  </td>      
</tr>  
<?php endforeach ?>
</tbody>
</table>
</div>
<?php
    echo $locked ? "" : "</form>";
?>
<?php echo '<a class="btn btn-default" href="' . url_for(ROOX_PLUGIN."/{$module_name}/entities") . '">' . TEXT_BUTTON_BACK. '</a>'; ?>