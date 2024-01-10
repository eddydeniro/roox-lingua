<?php
$queries = [
    "CREATE TABLE IF NOT EXISTS `__TABLE__` (
        `id` int UNSIGNED NOT NULL AUTO_INCREMENT, 
        `language` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, 
        `date_updated` bigint UNSIGNED NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `idx_language` (`language`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `__TABLE__` (
        `id` int UNSIGNED NOT NULL AUTO_INCREMENT, 
        `language_id` int UNSIGNED NOT NULL, 
        `field_id` int UNSIGNED NOT NULL, 
        `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL, 
        `short_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, 
        `tooltip` text COLLATE utf8mb4_general_ci NOT NULL, 
        `tooltip_item_page` text COLLATE utf8mb4_general_ci NOT NULL, 
        `required_message` text COLLATE utf8mb4_general_ci NOT NULL, 
        `configuration` text COLLATE utf8mb4_general_ci NOT NULL, 
        PRIMARY KEY (`id`), 
        UNIQUE KEY `language_field_index` (`language_id`,`field_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;", 
    "CREATE TABLE IF NOT EXISTS `__TABLE__` (
        `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
        `language_id` int UNSIGNED NOT NULL,
        `entities_id` int UNSIGNED NOT NULL,
        `name` varchar(64) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `idx_language_entities_id` (`language_id`,`entities_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `__TABLE__` (
        `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
        `language_id` int UNSIGNED NOT NULL,
        `entities_menu_id` int UNSIGNED NOT NULL,
        `name` varchar(64) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `idx_language_entities_menu_id` (`language_id`,`entities_menu_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "CREATE TABLE IF NOT EXISTS `__TABLE__` (
        `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
        `language_id` int UNSIGNED NOT NULL,
        `entities_configuration_id` int UNSIGNED NOT NULL,
        `configuration_value` text NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `idx_language_entities_configuration_id` (`language_id`,`entities_configuration_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",           
    "CREATE TABLE IF NOT EXISTS `__TABLE__` (
        `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
        `language_id` int UNSIGNED NOT NULL,
        `forms_tabs_id` int UNSIGNED NOT NULL,
        `name` varchar(64) NOT NULL,
        `description` text NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `idx_language_forms_tabs_id` (`language_id`,`forms_tabs_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",  
    "CREATE TABLE IF NOT EXISTS `__TABLE__` (
        `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
        `language_id` int UNSIGNED NOT NULL,
        `dict_id` int UNSIGNED NOT NULL,
        `dict_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `idx_language_id_dict_id` (`language_id`,`dict_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"    
];
    
    
foreach (${ROOX_PLUGIN . "_" . $module_name . "_tables"} as $key => $table) 
{
    $query = str_replace("__TABLE__", ROOX_PLUGIN . "_" . $table, $queries[$key]);
    db_query($query);    
}

db_query("
    INSERT IGNORE INTO `{$dictionary_table}` (`dict_key`, `dict_value`) VALUES
    ('CFG_LANGUAGE_SWITCHER', '1'),                
    ('MOD_LINGUA', 'Lingua'),
    ('TEXT_LINGUA_INFO', 'Translate your entities and their components into several languages to make your application multilingual.'),
    ('TEXT_LANGUAGE_SWITCHER', 'Language Switcher'),
    ('TEXT_LANGUAGE_SWITCHER_INFO', 'Language switcher will be displayed on top menu.'),
    ('TEXT_CLEAR_ORPHANS', 'Clear Orphaned Data'),
    ('TEXT_CLEAR_ORPHANS_INFO', 'You can clear orphaned language data left from entity or field deletion.'),
    ('TEXT_ORPHAN_DATA', 'Orphaned data'),
    ('TEXT_ORPHAN_DATA_CLEARED', 'Orphaned data is successfully cleared!'),
    ('TEXT_ORPHAN_DATA_NOT_CLEARED', 'Orphaned data is not cleared!'),
    ('TEXT_CANNOT_TRANSLATE','You cannot translate into the same language as your application language setting.');
");

$languageList = app_get_languages_choices();
db_query("INSERT IGNORE INTO " . ROOX_PLUGIN . "_" . $module_name . " (`language`) VALUES ('".implode("'),('", array_keys($languageList))."')");

?>