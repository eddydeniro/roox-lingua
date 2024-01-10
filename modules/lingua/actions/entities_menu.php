<?php
$app_title = app_set_title(TEXT_MENU_CONFIGURATION_MENU);

switch($app_module_action)
{
    case 'save':
      $Lingua->saveEntitiesMenu($_POST['entities_menu']);
      redirect_to(ROOX_PLUGIN ."/{$module_name}/entities_menu");
}