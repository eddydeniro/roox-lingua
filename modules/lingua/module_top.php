<?php
  $app_title = app_set_title(ucwords(ROOX_PLUGIN) . " &raquo; " . MOD_LINGUA);
  $plugin_name = ROOX_PLUGIN;
  $module_name = 'lingua';
  $module_title = MOD_LINGUA;  
  $module_info = TEXT_LINGUA_INFO;
  $module_version = '1.0';
  $module_url = 'https://github.com/eddydeniro/roox-lingua';

  $Lingua->setLanguageCache($app_session_token, true);
?>