<?php
    require component_path(ROOX_PLUGIN . "/{$module_name}/header"); 
    echo ($locked ? TEXT_CANNOT_TRANSLATE : form_tag('language_entities_form', url_for("{$plugin_name}/{$module_name}/entities", 'action=save')) . submit_tag(TEXT_BUTTON_SAVE));
?>
<div class="table-scrollable">
<div class="table-scrollable table-wrapper slimScroll" id="slimScroll">
    <table class="tree-table table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th><?php echo TEXT_ID ?></th>
                <th><?php echo TEXT_SETTINGS ?></th>
                <th><?php echo TEXT_NAME ?></th>
                <th><?php echo TEXT_MENU_TITLE ?></th>
                <th><?php echo TEXT_WINDOW_HEADING ?></th>
                <th><?php echo TEXT_LISTING_HEADING ?></th>
                <th><?php echo TEXT_INSERT_BUTTON_TITLE ?></th>                                
                <th><?php echo TEXT_COMMENT . ": " . TEXT_INSERT_BUTTON_TITLE ?></th>
                <th><?php echo TEXT_COMMENT . ": " . TEXT_WINDOW_HEADING ?></th>
                <th><?php echo TEXT_COMMENT . ": " . TEXT_LISTING_HEADING ?></th>                                

            </tr>
        </thead>
        <tbody>
        <?php if(count($entities_list) == 0) echo '<tr><td colspan="10">' . TEXT_NO_RECORDS_FOUND . '</td></tr>'; ?>
            <?php foreach($entities_list as $v): ?>
                <tr>
                    <td style="padding-top:10px;"><?php echo $v['id'] ?></td>
                    <td style="padding-top:10px;">
                        <?php
                            echo    button_icon(TEXT_FIELDS, 'fa fa-list-alt', url_for("{$plugin_name}/{$module_name}/fields", "entities_id={$v['id']}"), false) . ' ' . 
                                    button_icon(TEXT_FORM_TAB, 'fa fa-window-maximize', url_for("{$plugin_name}/{$module_name}/forms_tabs", "entities_id={$v['id']}"), false);                        ?>
                    </td>
                    <td>                        
                        <?php echo '<div class="tt" data-tt-id="entity_' . $v['id'] . '" ' . ($v['parent_id'] > 0 ? 'data-tt-parent="entity_' . $v['parent_id'] . '"' : '') . '></div>' ?>
                        <div class="input_adj">                        
                            <?php
                                $current_entity_data = ${ROOX_PLUGIN . '_language_cache'}['entities'][$v['id']];
                                $entity_name = $current_entity_data['name'];
                                echo $locked ? "<div style='padding:8px 0;'>{$entity_name}</div>" : input_tag("entities[{$v['id']}][name]", $entity_name, array('class' => 'form-control input-medium transparent'));
                            ?>
                        </div>
                    </td>
                    <?php
                    foreach($entities_config_scope as $cfg)
                    {
                        $text = strpos($cfg, 'comments')!==false ? TEXT_COMMENT : $entity_name;
                        $default = strpos($cfg, 'insert')!==false ? TEXT_ADD : $text;
                        list(${"{$cfg}_id"}, ${$cfg}) = [isset($current_entity_data[$cfg]['id']) ? $current_entity_data[$cfg]['id'] : $cfg, isset($current_entity_data[$cfg]['id']) ? $current_entity_data[$cfg]['value'] : ""];
                        echo "<td>". ($locked ? "<div style='padding:8px 0;'>{$$cfg}</div>" : input_tag("entities[{$v['id']}][cfg][".${"{$cfg}_id"}."]", $$cfg, array('class' => 'form-control input-medium transparent', 'placeholder'=>$default)))."</td>";
                    }
                    ?>
                </tr>  
            <?php endforeach ?>
        </tbody>
    </table>
</div>
</div>

<?php
    echo $locked ? "" : "</form>";
?>