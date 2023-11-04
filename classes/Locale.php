<?php
namespace Roox;
class Locale
{
    private $plugin_name, 
        $locale_id,
        $locale_table,
        $locale_fields_table,
        $locale_entities_table,
        $locale_entities_menu,
        $locale_entities_configuration_table,
        $locale_forms_tabs_table;

    function __construct($locale_name, $plugin = 'roox')
    {
        $this->plugin_name = $plugin;

        //define tables
        $this->locale_table = "app_{$plugin}_locale";
        $this->locale_fields_table = "app_{$plugin}_locale_fields";
        $this->locale_entities_table = "app_{$plugin}_locale_entities";
        $this->locale_entities_menu = "app_{$plugin}_locale_entities_menu";
        $this->locale_entities_configuration_table = "app_{$plugin}_locale_entities_configuration";
        $this->locale_forms_tabs_table = "app_{$plugin}_locale_forms_tabs";

        $this->locale_id = $this->getLocaleId($locale_name);

    }

    function createTables()
    {
        $table_query = "CREATE TABLE IF NOT EXISTS `{$this->locale_table}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT, 
            `language` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, 
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_language` (`language`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
          db_query($table_query);
    
          $table_query = "CREATE TABLE IF NOT EXISTS `{$this->locale_fields_table}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT, 
            `locale_id` int UNSIGNED NOT NULL, 
            `field_id` int UNSIGNED NOT NULL, 
            `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL, 
            `short_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, 
            `tooltip` text COLLATE utf8mb4_general_ci NOT NULL, 
            `tooltip_item_page` text COLLATE utf8mb4_general_ci NOT NULL, 
            `required_message` text COLLATE utf8mb4_general_ci NOT NULL, 
            `configuration` text COLLATE utf8mb4_general_ci NOT NULL, 
            PRIMARY KEY (`id`), 
            UNIQUE KEY `locale_field_index` (`locale_id`,`field_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
          db_query($table_query);
    
          $table_query = "CREATE TABLE IF NOT EXISTS `{$this->locale_entities_table}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `locale_id` int UNSIGNED NOT NULL,
            `entities_id` int UNSIGNED NOT NULL,
            `name` varchar(64) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_locale_entities_id` (`locale_id`,`entities_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
          db_query($table_query);
    
          $table_query = "CREATE TABLE IF NOT EXISTS `{$this->locale_entities_configuration_table}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `locale_id` int UNSIGNED NOT NULL,
            `entities_configuration_id` int UNSIGNED NOT NULL,
            `configuration_value` text NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_locale_entities_configuration_id` (`locale_id`,`entities_configuration_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
          db_query($table_query);
    
          $table_query = "CREATE TABLE IF NOT EXISTS `{$this->locale_entities_menu}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `locale_id` int UNSIGNED NOT NULL,
            `entities_menu_id` int UNSIGNED NOT NULL,
            `name` varchar(64) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_locale_entities_menu_id` (`locale_id`,`entities_menu_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
          db_query($table_query);
    
          $table_query ="CREATE TABLE IF NOT EXISTS `{$this->locale_forms_tabs_table}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `locale_id` int UNSIGNED NOT NULL,
            `forms_tabs_id` int UNSIGNED NOT NULL,
            `name` varchar(64) NOT NULL,
            `description` text NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_locale_forms_tabs_id` (`locale_id`,`forms_tabs_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
          db_query($table_query);        
    }
    function registerAllLanguages($languageList)
    {
        db_query("INSERT INTO {$this->locale_table} (`language`) VALUES ('".implode("'),('", array_keys($languageList))."')");
    }
    function registerLanguage($language)
    {
        db_query("INSERT INTO {$this->locale_table} (`language`) VALUES ('{$language}')");
        return db_insert_id();
    }
    private function getLocaleId($locale_name)
    {
        $info = db_find($this->locale_table, $locale_name, "`language`");
        return $info['id'];
    }
    function getEntiites()
    {
        $q = db_query("SELECT * FROM {$this->locale_entities_table} WHERE locale_id={$this->locale_id}");
        $locale_entities = [];
        if(db_num_rows($q))
        {
            while($d = db_fetch_array($q))
            {
                $locale_entities[$d['entities_id']] = $d['name'];
            }
        }
        return $locale_entities;        
    }
    function getEntiitesCfg()
    {
        $q = db_query("SELECT le.*, e.configuration_name FROM {$this->locale_entities_configuration_table} JOIN app_entities_configuration e ON (le.entities_configuration_id=e.id) WHERE le.locale_id={$this->locale_id}");
        $return = [];
        if(db_num_rows($q))
        {
            while($d = db_fetch_array($q))
            {
                if(!isset($return[$d['entities_id']]))
                {
                    $return[$d['entities_id']] = [];
                }
                $return[$d['entities_id']] = ['id'=>$d['entities_configuration_id'], 'name'=>$d['configuration_name'], 'value'=>$d['configuration_value']];
            }
        }
        return $return;        
    }
}
?>