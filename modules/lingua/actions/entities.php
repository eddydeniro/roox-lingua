<?php
$app_title = app_set_title(TEXT_HEADING_ENTITY_CONFIGURATION);
$config_default = $Lingua->getEntitiesCfgScope();
$entities_config_scope = array_keys($config_default);
switch($app_module_action)
{
    case 'save':
        $Lingua->saveEntityData($_POST['entities']);
        $Lingua->setLanguageCache($app_session_token, true);
        redirect_to(ROOX_PLUGIN . "/$module_name/entities");
        break;
}
$entities_list = entities::get_tree(0, [],  0, [], [], false, 0);