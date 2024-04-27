<?php
$app_title = app_set_title(TEXT_MENU_GLOBAL_LISTS);
$name_key = 'global_lists';
switch($app_module_action)
{
    case 'save':
      $Lingua->saveGlobalListChoices($_POST[$name_key]);
      redirect_to(ROOX_PLUGIN ."/{$module_name}/{$name_key}");
}