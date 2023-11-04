<?php
  $app_title = app_set_title(TEXT_LOCALE_CONFIG);
  $plugin_name = ROOX_PLUGIN;
  $module_name = 'locale';
 
/*
IS THIS TABLE NECESSARY TO TRANSLATE? FOR NOW, I DONT THINK SO :)

CREATE TABLE IF NOT EXISTS `app_entities_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `entities_groups_id` int NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_groups_id` (`entities_groups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

*/

  $locale_table = "app_{$plugin_name}_locale";
  $locale_fields_table = "app_{$plugin_name}_locale_fields";
  $locale_entities_table = "app_{$plugin_name}_locale_entities";
  $locale_entities_menu = "app_{$plugin_name}_locale_entities_menu";
  $locale_entities_configuration_table = "app_{$plugin_name}_locale_entities_configuration";
  $locale_forms_tabs_table = "app_{$plugin_name}_locale_forms_tabs";
  // $locale_comments_forms_tabs_table = "app_{$plugin_name}_locale_comments_forms_tabs";

  if (!isset($_SESSION[$locale_table]) || !$_SESSION[$locale_table]) 
  {
      $Locale->createTables();
      $Locale->registerAllLanguages($locale_choices);

      $_SESSION[$locale_table] = true;
  }  

  if(!isset($_SESSION['locale_data']))
  {

  }
  //app_write_cache()
  //$app_entities_cache
  /*
  [1] => Array
    (
        [id] => 1
        [parent_id] => 0
        [group_id] => 0
        [name] => Users
        [notes] => 
        [display_in_menu] => 0
        [sort_order] => 10
    )
  */
  //$app_fields_cache
  /*
  [1] => Array
    (
        [1] => Array
            (
                [id] => 1
                [type] => fieldtype_action
                [name] => action
                [entities_id] => 1
                [configuration] => 
                [is_heading] => 
            )
  */

  $q = db_query("SELECT * FROM $locale_table WHERE `language`='$locale_setting'");

  if(db_num_rows($q))
  {
    $d = db_fetch_array($q);
    $locale_id = $d['id'];
  }
  else
  {
    db_query("INSERT INTO $locale_table (`language`) VALUES ('{$locale_setting}')");
    $locale_id = db_insert_id();  
  }

  // $info_query = db_fetch_all('app_entities_configuration',"entities_id='21'");
  // $entity_cfg = [];
  // while($info = db_fetch_array($info_query))
  // {      
  //     $entity_cfg[] = $info;
  // }
  // print_rr($entity_cfg);
?>