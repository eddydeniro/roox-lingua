<?php

$app_title = app_set_title(TEXT_ENTITIES_HEADING);

switch($app_module_action)
{
    case 'set_lang':
        $locale_setting = $_POST['locale_setting'];
        db_perform('app_entity_1', ['field_13'=>$locale_setting], 'update', "id={$app_user['id']}");
        list($page, $entity_id) = explode('_', $_POST['page']);
        redirect_to("$plugin_name/$module_name/$page" . ((int)$entity_id ? "&entities_id=$entity_id" : ""));        
        break;
    case 'save':

        $entities_data = $_POST['entities'];
        $local_entities_values = [];
        $local_entities_cfg_values = [];
        foreach ($entities_data as $entity_id => $values) 
        {
            if($values['name'])
            {
                $local_entities_values[] = "($locale_id, $entity_id, '{$values['name']}')";
            }
            foreach ($values['cfg'] as $cfg_id => $cfg_value) 
            {
                if($cfg_value)
                {
                    $local_entities_cfg_values[] = "($locale_id, $cfg_id, '$cfg_value')"; 
                }
            }
        }
        $entities_query = "INSERT INTO $locale_entities_table (`locale_id`, `entities_id`, `name`) VALUES " . implode(",",$local_entities_values) . " ON DUPLICATE KEY UPDATE `locale_id`=VALUES(`locale_id`), `entities_id`=VALUES(`entities_id`), `name`=VALUES(`name`)";
        $entities_cfg_query = "INSERT INTO $locale_entities_configuration_table (`locale_id`, `entities_configuration_id`, `configuration_value`) VALUES " . implode(",",$local_entities_cfg_values) . " ON DUPLICATE KEY UPDATE `locale_id`=VALUES(`locale_id`), `entities_configuration_id`=VALUES(`entities_configuration_id`), `configuration_value`=VALUES(`configuration_value`)";
        db_query($entities_query);
        db_query($entities_cfg_query);
        redirect_to("$plugin_name/$module_name/entities");
        break;
}

$entities_list = entities::get_tree(0, [],  0, [], [], false, $entities_filter);
$q = db_query("SELECT * FROM $locale_entities_table WHERE locale_id={$locale_id}");
$locale_entities = [];
if(db_num_rows($q))
{
    while($d = db_fetch_array($q))
    {
        $locale_entities[$d['entities_id']] = $d['name'];
    }
}

//entities configuration
$q = db_query("SELECT * FROM app_entities_configuration WHERE configuration_name IN ('menu_title','listing_heading','window_heading','insert_button')");
$app_entities_configs = [];
while($d = db_fetch_array($q))
{
    if(!isset($app_entities_configs[$d['entities_id']]))
    {
      $app_entities_configs[$d['entities_id']] = [];
    }
    $app_entities_configs[$d['entities_id']][$d['configuration_name']] = [
        'id'=>$d['id'],
        'value'=>$d['configuration_value'],
    ];
}
$q = db_query("SELECT lc.*, c.configuration_name, c.entities_id FROM $locale_entities_configuration_table lc JOIN app_entities_configuration c ON (lc.entities_configuration_id=c.id) WHERE locale_id = $locale_id");
while($d = db_fetch_array($q))
{
    $app_entities_configs[$d['entities_id']][$d['configuration_name']]['value'] = $d['configuration_value'];
}