<?php
    $current_page = 'entities_0';
    require('header.php'); 
?>

<h4><?php echo TEXT_ENTITIES_HEADING ?></h4>
<p></p>
<br>
<?php
    echo form_tag('local_entities_form', url_for("{$plugin_name}/{$module_name}/entities", 'action=save')) . (CFG_APP_LANGUAGE == $app_user['language'] ? "" : submit_tag(TEXT_BUTTON_SAVE));
?>
<div class="table-scrollable" >
    <table class="tree-table table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th><?php echo TEXT_ID ?></th>
                <th><?php echo TEXT_GROUP ?></th>
                <th width="100%"><?php echo TEXT_NAME ?></th>
                <th><?php echo TEXT_MENU_TITLE ?></th>
                <th><?php echo TEXT_WINDOW_HEADING ?></th>
                <th><?php echo TEXT_LISTING_HEADING ?></th>
                <th><?php echo TEXT_INSERT_BUTTON_TITLE ?></th>                                
            </tr>
        </thead>
        <tbody>
        <?php if(count($entities_list) == 0) echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>'; ?>
            <?php foreach($entities_list as $v): ?>
                <tr>
                    <td><?php echo $v['id'] ?></td>
                    <td><?php echo entities_groups::get_name_by_id($v['group_id']) ?></td>
                    <td style="white-space: nowrap">
                        
                        <?php echo '<div class="tt" data-tt-id="entity_' . $v['id'] . '" ' . ($v['parent_id'] > 0 ? 'data-tt-parent="entity_' . $v['parent_id'] . '"' : '') . '></div>' ?>

                        <div class="input_adj">                        
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle transparent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                    <li><?php echo link_to(TEXT_NAV_GENERAL_CONFIG, url_for("{$plugin_name}/{$module_name}/entities_configuration&entities_id={$v['id']}")) ?></li>
                                    <li><?php echo link_to(TEXT_NAV_FIELDS_CONFIG, url_for("{$plugin_name}/{$module_name}/fields&entities_id={$v['id']}")) ?></li>
                                    <li><?php echo link_to(TEXT_NAV_FORM_CONFIG, url_for("{$plugin_name}/{$module_name}/groups", "entities_id={$v['id']}")) ?></li>	
                                </ul>
                            </div>
                            <?php
                                $entity_name = $locale_entities[$v['id']] ?? $v['name'];
                                echo input_tag("entities[{$v['id']}][name]", $entity_name, array('class' => 'form-control transparent'));
                            ?>
                            <!-- <input type="text" class="form-control" style="border:none;background:none;" value="<?php echo $entity_name ?>"> -->
                        </div>
                        </div>

                    </td>
                    <td><?php echo input_tag("entities[{$v['id']}][cfg][{$app_entities_configs[$v['id']]['menu_title']['id']}]", $app_entities_configs[$v['id']]['menu_title']['value'], array('class' => 'form-control input-small transparent')); ?></td>
                    <td><?php echo input_tag("entities[{$v['id']}][cfg][{$app_entities_configs[$v['id']]['window_heading']['id']}]", $app_entities_configs[$v['id']]['window_heading']['value'], array('class' => 'form-control input-small transparent')); ?></td>
                    <td><?php echo input_tag("entities[{$v['id']}][cfg][{$app_entities_configs[$v['id']]['listing_heading']['id']}]", $app_entities_configs[$v['id']]['listing_heading']['value'], array('class' => 'form-control input-small transparent')); ?></td>
                    <td><?php echo input_tag("entities[{$v['id']}][cfg][{$app_entities_configs[$v['id']]['insert_button']['id']}]", $app_entities_configs[$v['id']]['insert_button']['value'], array('class' => 'form-control input-small transparent')); ?></td>                    
                </tr>  
<?php endforeach ?>
        </tbody>
    </table>
</div>
</form>

