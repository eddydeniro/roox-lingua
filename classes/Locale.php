<?php
/**
 * 
 */

namespace Roox;
class Locale
{
    const SWITCHER_KEY = 'CFG_LOCALE_SWITCHER';
    const CFG_SCOPE = ['menu_title'=>'','window_heading'=>TEXT_INFO,'listing_heading'=>'','insert_button'=>TEXT_ADD, 'comments_insert_button'=>TEXT_ADD_COMMENT, 'comments_window_heading'=>TEXT_COMMENT, 'comments_listing_heading'=>TEXT_COMMENT];
    public $locale_id;
    private $locale_name,
        $locale_table,
        $locale_fields_table,
        $locale_entities_table,
        $locale_entities_menu_table,
        $locale_entities_configuration_table,
        $locale_forms_tabs_table,
        $locale_dictionary_table,
        $partner_tables;

    function __construct($locale_name)
    {
        $plugin = ROOX_PLUGIN;
        $this->locale_name = $locale_name;
        //define tables
        $this->locale_table = "{$plugin}_locale";
        $this->locale_fields_table = "{$plugin}_locale_fields";
        $this->locale_entities_table = "{$plugin}_locale_entities";
        $this->locale_entities_menu_table = "{$plugin}_locale_entities_menu";
        $this->locale_entities_configuration_table = "{$plugin}_locale_entities_configuration";
        $this->locale_forms_tabs_table = "{$plugin}_locale_forms_tabs";
        $this->locale_dictionary_table = "{$plugin}_locale_dictionary";

        $this->partner_tables = [
            $this->locale_entities_table=>['app_entities', 'entities_id'],
            $this->locale_entities_configuration_table=>['app_entities_configuration', 'entities_configuration_id'],
            $this->locale_entities_menu_table=>['app_entities_menu', 'entities_menu_id'],
            $this->locale_fields_table=>['app_fields', 'field_id'],
            $this->locale_forms_tabs_table=>['app_forms_tabs', 'forms_tabs_id'],            
        ];
    
        // if(!is_table_exist($this->locale_table))
        // {
        //     $this->createTables();
        //     $this->registerAllLanguages();
        //     db_query("
        //         INSERT INTO `{$plugin}_dictionary` (`dict_key`, `dict_value`) VALUES
        //         ('".self::SWITCHER_KEY."', '1'),                
        //         ('TEXT_LOCALE_TRANSLATION', 'Locale Translation'),
        //         ('TEXT_LOCALE_TRANSLATION_INFO', 'Translate your entities and their components into several locale languages to make your application multilingual.'),
        //         ('TEXT_LOCALE_SWITCHER', 'Locale Switcher'),
        //         ('TEXT_LOCALE_SWITCHER_INFO', 'Locale switcher will be displayed on top menu.'),
        //         ('TEXT_CLEAR_ORPHANS', 'Clear Orphaned Data'),
        //         ('TEXT_CLEAR_ORPHANS_INFO', 'You can clear orphaned locale data left from entity or field deletion.'),
        //         ('TEXT_ORPHAN_DATA', 'Orphaned data'),
        //         ('TEXT_ORPHAN_DATA_CLEARED', 'Orphaned data is successfully cleared!'),
        //         ('TEXT_ORPHAN_DATA_NOT_CLEARED', 'Orphaned data is not cleared!')
        //         ;    
        //     ");
        // }

        $this->locale_id = $this->getLocaleId($locale_name);
    }
    
    /**
     * createTables
     *
     * @return void
     */
    /*
    private function createTables()
    {
        $table_query = "CREATE TABLE IF NOT EXISTS `{$this->locale_table}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT, 
            `language` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, 
            `date_updated` bigint UNSIGNED NOT NULL,
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
    
        $table_query = "CREATE TABLE IF NOT EXISTS `{$this->locale_entities_menu_table}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `locale_id` int UNSIGNED NOT NULL,
            `entities_menu_id` int UNSIGNED NOT NULL,
            `name` varchar(64) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_locale_entities_menu_id` (`locale_id`,`entities_menu_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        db_query($table_query);
    
        $table_query = "CREATE TABLE IF NOT EXISTS `{$this->locale_forms_tabs_table}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `locale_id` int UNSIGNED NOT NULL,
            `forms_tabs_id` int UNSIGNED NOT NULL,
            `name` varchar(64) NOT NULL,
            `description` text NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_locale_forms_tabs_id` (`locale_id`,`forms_tabs_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        db_query($table_query);   
          
        $table_query = "CREATE TABLE IF NOT EXISTS `{$this->locale_dictionary_table}` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `locale_id` int UNSIGNED NOT NULL,
            `dict_id` int UNSIGNED NOT NULL,
            `dict_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_locale_id_dict_id` (`locale_id`,`dict_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        db_query($table_query);
    }
    */
    function getEntitiesCfgScope()
    {
        return self::CFG_SCOPE;
    }   

    /**
     * localeSwitcher
     *
     * @param  string $updateData
     * @return int
     */
    function localeSwitcher($updateData = "")
    {
        if($updateData==="")
        {
            $q = db_query("SELECT dict_value FROM `". ROOX_PLUGIN ."_dictionary` WHERE dict_key = '". self::SWITCHER_KEY ."'");
            $d = db_fetch_array($q);
            return (int)$d['dict_value'];
        }
        return db_query("UPDATE ". ROOX_PLUGIN ."_dictionary SET dict_value={$updateData} WHERE dict_key='". self::SWITCHER_KEY ."'");
    }  

    /**
     * countOrphans
     *
     * @return int
     */
    function countOrphans()
    {
        $count = 0;

        foreach ($this->partner_tables as $locale_table=>$partner) 
        {
            list($app_table, $foreign_key) = $partner;
            $q = db_query("SELECT COUNT(*) as num FROM {$locale_table} le LEFT JOIN {$app_table} e ON (le.{$foreign_key}=e.id) WHERE e.id IS NULL"); 
            $d = db_fetch_array($q);
            $count += (int)$d['num'];
        }
        return $count;
    }  

    /**
     * validateData
     *
     * @return void
     */
    function validateData()
    {
        foreach ($this->partner_tables as $locale_table=>$partner) 
        {
            list($app_table, $foreign_key) = $partner;
            db_query("DELETE le FROM {$locale_table} le LEFT JOIN {$app_table} e ON (le.{$foreign_key}=e.id) WHERE e.id IS NULL"); 
        }        
        //TBD for comments tables because if using default title, it will translated by its language file
    }  

    /**
     * saveEntityData
     *
     * @param  array $entities_data
     * @return void
     */
    function saveEntityData($entities_data)
    {
        $locale_entities_values = [];
        $locale_entities_cfg_values = [];
        foreach ($entities_data as $entity_id => $values) 
        {
            if($values['name'])
            {
                $locale_entities_values[] = "('{$this->locale_id}', '$entity_id', '{$values['name']}')";
            }
            foreach ($values['cfg'] as $cfg_id => $cfg_value) 
            {
                if($cfg_value)
                {
                    if(!is_numeric($cfg_id))
                    {
                        $new_cfg_value = CFG_APP_LANGUAGE == $this->locale_name ? $cfg_value : '';
                        $cfg_id = $this->saveEntityCfg($entity_id, $cfg_id, $new_cfg_value);
                    }    
                    $locale_entities_cfg_values[] = "('{$this->locale_id}', '$cfg_id', '$cfg_value')"; 
                }
            }
        }
        $update = false;
        
        if(count($locale_entities_values))
        {
            $entities_query = "INSERT INTO {$this->locale_entities_table} (`locale_id`, `entities_id`, `name`) VALUES " . implode(",",$locale_entities_values) . " ON DUPLICATE KEY UPDATE `name`=VALUES(`name`)";
            db_query($entities_query);
            $update = true;
        }
        if(count($locale_entities_cfg_values))
        {
            $entities_cfg_query = "INSERT INTO {$this->locale_entities_configuration_table} (`locale_id`, `entities_configuration_id`, `configuration_value`) VALUES " . implode(",",$locale_entities_cfg_values) . " ON DUPLICATE KEY UPDATE `configuration_value`=VALUES(`configuration_value`)";
            db_query($entities_cfg_query); 
            $update = true;   
        }
        if($update)
        {
            $this->saveUpdateTime();   
        }
    }    

    private function saveEntityCfg($entities_id, $cfg_name, $cfg_value)
    {
        db_query("INSERT INTO app_entities_configuration (entities_id, configuration_name, configuration_value) VALUES ('$entities_id','$cfg_name', '$cfg_value');");
        return db_insert_id();
    }

    /**
     * saveFieldData
     *
     * @param  array $field_data
     * @return void
     */
    function saveFieldData($field_data = [])
    {
        $sql_data = [];
        foreach ($field_data as $field_id => $values) 
        {
            if(!$values['name'])
            {
                continue;
            }
            $sql_data[] = "({$this->locale_id}, $field_id, '{$values['name']}', '{$values['shortname']}')";
        }
        $fields_query = "INSERT INTO {$this->locale_fields_table} (`locale_id`, `field_id`, `name`, `short_name`) VALUES " . implode(",",$sql_data) . " ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `short_name`=VALUES(`short_name`)";
        db_query($fields_query);
        $this->saveUpdateTime();
    }   

    /**
     * saveFormsTabs
     *
     * @param  array $data
     * @return void
     */
    function saveFormsTabs($data = [])
    {
        $sql_data = [];
        foreach ($data as $forms_tabs_id => $values) 
        {
            if(!$values['name'])
            {
                continue;
            }
            $sql_data[] = "({$this->locale_id}, $forms_tabs_id, '{$values['name']}', '{$values['description']}')";
        }
        $fields_query = "INSERT INTO {$this->locale_forms_tabs_table} (`locale_id`, `forms_tabs_id`, `name`, `description`) VALUES " . implode(",",$sql_data) . " ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`)";
        db_query($fields_query);
        $this->saveUpdateTime();
    }    

    /**
     * getFormsTabs
     *
     * @param  array $entities_ids
     * @return array
     */
    function getFormsTabs($entities_ids = [])
    {
        $filter = "";
        if(count($entities_ids))
        {
            $filter = "AND f.entities_id IN (".implode(",", $entities_ids).")";
        }
        $q = db_query("SELECT lf.*, f.entities_id FROM {$this->locale_forms_tabs_table} lf JOIN app_forms_tabs f ON (lf.forms_tabs_id=f.id) WHERE locale_id={$this->locale_id} $filter");
        $return = [];
        if(db_num_rows($q))
        {
            while($d = db_fetch_array($q))
            {
                if(!isset($return[$d['entities_id']]))
                {
                    $return[$d['entities_id']] = [];
                }
                $return[$d['entities_id']][$d['forms_tabs_id']] = ['name'=>$d['name'], 'description'=>$d['description']];
            }
        }
        return $return;        
    }   

    /**
     * registerAllLanguages
     *
     * @return void
     */
    // private function registerAllLanguages()
    // {
    //     $languageList = app_get_languages_choices();
    //     db_query("INSERT IGNORE INTO {$this->locale_table} (`language`) VALUES ('".implode("'),('", array_keys($languageList))."')");
    // }  

    /**
     * saveUpdateTime
     *
     * @return void
     */
    private function saveUpdateTime()
    {
        $updateTime = time();
        db_query("UPDATE {$this->locale_table} SET `date_updated`=$updateTime WHERE id={$this->locale_id}");
    }   

    /**
     * getUpdateTime
     *
     * @return int
     */
    private function getUpdateTime()
    {
        $q = db_query("SELECT date_updated FROM {$this->locale_table} WHERE id={$this->locale_id}");
        $d = db_fetch_array($q);
        return (int)$d['date_updated'];
    }   

    /**
     * getLocaleId
     *
     * @param  string $locale_name
     * @return int
     */
    private function getLocaleId($locale_name)
    {
        $info = db_find($this->locale_table, $locale_name, "`language`");
        $id = $info['id'];
        if(!$id)
        {
            db_query("INSERT INTO {$this->locale_table} (`language`) VALUES ('{$locale_name}')");
            $id = db_insert_id();
        }
        return (int)$id;
    }    

    /**
     * setLocaleCache
     *
     * @param  string $token
     * @return array
     */
    function setLocaleCache($token)
    {
        if(!app_session_is_registered('app_logged_users_id'))
        {
            unset($_SESSION['locale_cache']);
            return [];
        }

        $shouldUpdate = false;
        $isCached = app_session_is_registered('locale_cache');
        if($isCached)
        {
            $isSameLocale = isset($_SESSION['locale_cache']['locale']) && $_SESSION['locale_cache']['locale']==$this->locale_name;
            $isSameToken = isset($_SESSION['locale_cache']['token']) && $_SESSION['locale_cache']['token']==$token;
            $isSameUpdate = isset($_SESSION['locale_cache']['date_updated']) && $_SESSION['locale_cache']['date_updated']==$this->getUpdateTime();

            if(!$isSameLocale || !$isSameToken || !$isSameUpdate)
            {
                $shouldUpdate = true;
            }
        }        
        else
        {
            $shouldUpdate = true;
        }
        if($shouldUpdate)
        {
            $_SESSION['locale_cache'] = $this->getLocaleCache($token);
        }
        return $_SESSION['locale_cache'];        
    }    

    /**
     * getLocaleCache
     *
     * @param  string $token
     * @return array
     */
    private function getLocaleCache($token)
    {
        return [
            'locale' => $this->locale_name,
            'token' => $token,
            'date_updated' => $this->getUpdateTime(),
            'entities' => $this->getEntitiesData(),
            // 'entities_menu' => $this->getMenu($entities_cfg),
            'fields' => $this->getFields(),
            'forms_tabs'=>$this->getFormsTabs(),
            'reports'=>self::getReports()
        ];
    }

    /**
     * getEntitiesData
     *
     * @return array
     */
    function getEntitiesData()
    {
        $result = [];
        $q = db_query("SELECT e.id, e.parent_id, e.name, c.id AS cfg_id, 
            c.configuration_name, c.configuration_value, le.name AS locale_name, 
            lc.configuration_value AS locale_cfg_value FROM app_entities e 
            LEFT JOIN app_entities_configuration c ON (e.id=c.entities_id) 
            LEFT JOIN (SELECT * FROM {$this->locale_entities_table} WHERE locale_id={$this->locale_id}) le ON (e.id=le.entities_id) 
            LEFT JOIN (SELECT * FROM {$this->locale_entities_configuration_table} WHERE locale_id={$this->locale_id}) lc ON (c.id=lc.entities_configuration_id);"); 
            
        $default_cfg = [];
        while($d = db_fetch_array($q))
        {
            $entity_name = $d['locale_name'] ? $d['locale_name'] : $d['name'];
            if(!isset($result[$d['id']]))
            {
                foreach (self::CFG_SCOPE as $key => $value) 
                {
                    $default_cfg[$key] = ['key'=>0, 'value'=>$value ? $value : $entity_name];
                }        
                $result[$d['id']] = array_merge(['name'=>'', 'original'=>''], $default_cfg);    
            }
            $result[$d['id']]['name'] = $entity_name;
            $result[$d['id']]['original'] = $d['name'];
            if(!$d['configuration_name'])
            {
                foreach($result[$d['id']] as $key=>$value)
                {
                    if(is_array($value) && !$value['value'])
                    {
                        $result[$d['id']][$key]['value'] = $entity_name;
                    }
                }
            }
            if(in_array($d['configuration_name'], array_keys(self::CFG_SCOPE)))
            {
                $locale_value = trim($d['locale_cfg_value']);
                $current_value = trim($result[$d['id']][$d['configuration_name']]['value']);
                $result[$d['id']][$d['configuration_name']] = ['id'=>$d['cfg_id'], 'original'=>$d['configuration_value'], 'value'=>$locale_value ? $locale_value : ($current_value ? $current_value : $entity_name)];
            }
        }
        return $result;
    } 

    /**
     * getFields
     *
     * @param  array $entities_ids
     * @return array
     */
    private function getFields($entities_ids = [])
    {   
        $filter = "";
        if(count($entities_ids))
        {
            $filter = "AND f.entities_id IN (".implode(",", $entities_ids).")";
        }
        $q = db_query("SELECT lf.*, f.entities_id FROM {$this->locale_fields_table} lf JOIN app_fields f ON (lf.field_id=f.id) WHERE locale_id={$this->locale_id} $filter");
        $return = [];
        if(db_num_rows($q))
        {
            while($d = db_fetch_array($q))
            {
                if(!isset($return[$d['entities_id']]))
                {
                    $return[$d['entities_id']] = [];
                }
                $return[$d['entities_id']][$d['field_id']] = ['name'=>$d['name'], 'short_name'=>$d['short_name']];
            }
        }
        return $return;        
    }  

    /**
     * localizeData
     *
     * @param  array $menu_data
     * @param  array $data_source
     * @param  string $cfg_key
     * @return void
     */
    static function localizeData(&$menu_data, $data_source, $cfg_key = 'menu_title')
    {
        foreach ($menu_data as $key => $menu_item) 
        {
            if(isset($menu_item['submenu']))
            {
                self::localizeData($menu_data[$key]['submenu'], $data_source);
                continue;
            }
            if(strpos($menu_item['url'], 'module=items/items')!==false)
            {
                list($x, $path) = explode('path=', $menu_item['url'], 2);
                $path_array = explode('/', $path);
                $entity_id = $path_array[count($path_array) - 1];
                $menu_data[$key]['title'] = $data_source['entities'][$entity_id][$cfg_key]['value'] ?? $menu_item['title'];                    
            }
            if(strpos($menu_item['url'], 'module=reports/view')!==false)
            {
                list($x, $reports_id) = explode('reports_id=', $menu_item['url'], 2);
                $entity_id = $data_source['reports'][$reports_id];
                $menu_data[$key]['title'] = $data_source['entities'][$entity_id][$cfg_key]['value'] ?? $menu_item['title'];                    
            }
        }
    } 

    /**
     * getDefinitions
     *
     * @return array
     */
    function getDefinitions()
    {
        $q = db_query("SELECT d.*, dv.dict_value AS locale_value FROM ".ROOX_PLUGIN."_dictionary d LEFT JOIN (SELECT * FROM {$this->locale_dictionary_table} WHERE locale_id={$this->locale_id}) dv ON (d.id=dv.dict_id) WHERE d.dict_key NOT LIKE 'CFG_%'");
        $return = [];
        while($d = db_fetch_array($q))
        {
            $return[$d['id']] = ['dict_key'=>$d['dict_key'], 'dict_value'=>$d['locale_value'] ? $d['locale_value'] : ($d['dict_value'] ? $d['dict_value'] : $d['dict_key'])];
        }
        return $return;
    }   

    /**
     * saveDefinitions
     *
     * @param  array $data
     * @return void
     */
    function saveDefinitions($data)
    {
        $sql_data = [];
        foreach ($data as $dict_id => $dict_value) 
        {
            if(!$dict_value)
            {
                continue;
            }
            $sql_data[] = "({$this->locale_id}, $dict_id, '{$dict_value}')";
        }

        $fields_query = "INSERT INTO {$this->locale_dictionary_table} (`locale_id`, `dict_id`, `dict_value`) VALUES " . implode(",", $sql_data) . " ON DUPLICATE KEY UPDATE `dict_value`=VALUES(`dict_value`)";
        db_query($fields_query);
    }

    static private function getReports()
    {
        $q = db_query("SELECT id, entities_id FROM app_reports WHERE reports_type = 'entity_menu'");
        while($d = db_fetch_array($q))
        {
            $result[$d['id']] = $d['entities_id'];
        }
        return $result;
    }
}
?>