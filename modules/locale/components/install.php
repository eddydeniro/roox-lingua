<?php
$plugin = ROOX_PLUGIN;
$locale_table = "{$plugin}_locale";
$locale_fields_table = "{$plugin}_locale_fields";
$locale_entities_table = "{$plugin}_locale_entities";
$locale_entities_menu_table = "{$plugin}_locale_entities_menu";
$locale_entities_configuration_table = "{$plugin}_locale_entities_configuration";
$locale_forms_tabs_table = "{$plugin}_locale_forms_tabs";
$locale_dictionary_table = "{$plugin}_locale_dictionary";

$table_query = "CREATE TABLE IF NOT EXISTS `{$locale_table}` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT, 
    `language` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, 
    `date_updated` bigint UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_language` (`language`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
db_query($table_query);

$table_query = "CREATE TABLE IF NOT EXISTS `{$locale_fields_table}` (
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

$table_query = "CREATE TABLE IF NOT EXISTS `{$locale_entities_table}` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `locale_id` int UNSIGNED NOT NULL,
    `entities_id` int UNSIGNED NOT NULL,
    `name` varchar(64) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_locale_entities_id` (`locale_id`,`entities_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
db_query($table_query);

$table_query = "CREATE TABLE IF NOT EXISTS `{$locale_entities_configuration_table}` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `locale_id` int UNSIGNED NOT NULL,
    `entities_configuration_id` int UNSIGNED NOT NULL,
    `configuration_value` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_locale_entities_configuration_id` (`locale_id`,`entities_configuration_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
db_query($table_query);

$table_query = "CREATE TABLE IF NOT EXISTS `{$locale_entities_menu_table}` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `locale_id` int UNSIGNED NOT NULL,
    `entities_menu_id` int UNSIGNED NOT NULL,
    `name` varchar(64) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_locale_entities_menu_id` (`locale_id`,`entities_menu_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
db_query($table_query);

$table_query = "CREATE TABLE IF NOT EXISTS `{$locale_forms_tabs_table}` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `locale_id` int UNSIGNED NOT NULL,
    `forms_tabs_id` int UNSIGNED NOT NULL,
    `name` varchar(64) NOT NULL,
    `description` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_locale_forms_tabs_id` (`locale_id`,`forms_tabs_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
db_query($table_query);   
  
$table_query = "CREATE TABLE IF NOT EXISTS `{$locale_dictionary_table}` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `locale_id` int UNSIGNED NOT NULL,
    `dict_id` int UNSIGNED NOT NULL,
    `dict_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_locale_id_dict_id` (`locale_id`,`dict_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
db_query($table_query);

db_query("
INSERT INTO `{$plugin}_dictionary` (`dict_key`, `dict_value`) VALUES
('CFG_LOCALE_SWITCHER', '1'),                
('TEXT_LOCALE_TRANSLATION', 'Locale Translation'),
('TEXT_LOCALE_TRANSLATION_INFO', 'Translate your entities and their components into several locale languages to make your application multilingual.'),
('TEXT_LOCALE_SWITCHER', 'Locale Switcher'),
('TEXT_LOCALE_SWITCHER_INFO', 'Locale switcher will be displayed on top menu.'),
('TEXT_CLEAR_ORPHANS', 'Clear Orphaned Data'),
('TEXT_CLEAR_ORPHANS_INFO', 'You can clear orphaned locale data left from entity or field deletion.'),
('TEXT_ORPHAN_DATA', 'Orphaned data'),
('TEXT_ORPHAN_DATA_CLEARED', 'Orphaned data is successfully cleared!'),
('TEXT_ORPHAN_DATA_NOT_CLEARED', 'Orphaned data is not cleared!')
;
");

$languageList = app_get_languages_choices();
db_query("INSERT IGNORE INTO {$locale_table} (`language`) VALUES ('".implode("'),('", array_keys($languageList))."')");

?>