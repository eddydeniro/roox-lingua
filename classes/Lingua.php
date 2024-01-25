<?php
namespace Roox;

class Lingua
{
    const SWITCHER_KEY = 'CFG_LANGUAGE_SWITCHER';
    const CFG_SCOPE = [
        'menu_title'=>'',
        'window_heading'=>TEXT_INFO,
        'listing_heading'=>'',
        'insert_button'=>TEXT_ADD, 
        'comments_insert_button'=>TEXT_ADD_COMMENT, 
        'comments_window_heading'=>TEXT_COMMENT, 
        'comments_listing_heading'=>TEXT_COMMENT
    ];
    public $language_id;
    private $language_name,
        $lingua_table,
        $lingua_fields_table,
        $lingua_entities_table,
        $lingua_entities_menu_table,
        $lingua_entities_configuration_table,
        $lingua_forms_tabs_table,
        $lingua_dictionary_table,
        $partner_tables;

    function __construct($language_name)
    {
        $this->language_name = $language_name;
        $tmp = explode("\\", get_class());
        $currentClass = strtolower($tmp[count($tmp)-1]);
        global ${ROOX_PLUGIN . "_" . $currentClass . "_tables"};
        //define tables
        foreach (${ROOX_PLUGIN . "_" . $currentClass . "_tables"} as $table) 
        {
            $this->{$table . "_table"} = ROOX_PLUGIN . "_" . $table;
        }

        $this->partner_tables = [
            $this->lingua_entities_table=>['app_entities', 'entities_id'],
            $this->lingua_entities_configuration_table=>['app_entities_configuration', 'entities_configuration_id'],
            $this->lingua_entities_menu_table=>['app_entities_menu', 'entities_menu_id'],
            $this->lingua_fields_table=>['app_fields', 'field_id'],
            $this->lingua_forms_tabs_table=>['app_forms_tabs', 'forms_tabs_id'],            
        ];

        $this->language_id = $this->getLanguageId($language_name);
    }
    
    function getEntitiesCfgScope()
    {
        return self::CFG_SCOPE;
    }   

    /**
     * languageSwitcher
     *
     * @param  string $updateData
     * @return int
     */
    function languageSwitcher($updateData = "")
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

        foreach ($this->partner_tables as $lingua_table=>$partner) 
        {
            list($app_table, $foreign_key) = $partner;
            $q = db_query("SELECT COUNT(*) as num FROM {$lingua_table} le LEFT JOIN {$app_table} e ON (le.{$foreign_key}=e.id) WHERE e.id IS NULL"); 
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
        foreach ($this->partner_tables as $lingua_table=>$partner) 
        {
            list($app_table, $foreign_key) = $partner;
            db_query("DELETE le FROM {$lingua_table} le LEFT JOIN {$app_table} e ON (le.{$foreign_key}=e.id) WHERE e.id IS NULL"); 
        }        
        //TBD for comments tables because if using default title, it will translated by its language file
    }  


    function saveEntitiesMenu($data)
    {
        $sql_data = [];
        foreach ($data as $menu_id => $values) 
        {
            if(!$values['name'])
            {
                continue;
            }
            $name = db_input($values['name']);
            $sql_data[] = "({$this->language_id}, $menu_id, '{$name}')";
        }
        $query = "INSERT INTO {$this->lingua_entities_menu_table} (`language_id`, `entities_menu_id`, `name`) VALUES " . implode(",",$sql_data) . " ON DUPLICATE KEY UPDATE `name`=VALUES(`name`)";
        db_query($query);
        $this->saveUpdateTime();
    }

    function getEntitiesMenu()
    {
        $table = $this->lingua_entities_menu_table;
        $partner_table = $this->partner_tables[$table][0];
        $q = db_query("SELECT le.* FROM {$table} le JOIN {$partner_table} e ON (le.entities_menu_id=e.id) WHERE language_id={$this->language_id}");
        $return = [];
        if(db_num_rows($q))
        {
            while($d = db_fetch_array($q))
            {
                $return[$d['entities_menu_id']] = ['name'=>$d['name']];
            }
        }
        return $return;        
    }


    /**
     * saveEntityData
     *
     * @param  array $entities_data
     * @return void
     */
    function saveEntityData($entities_data)
    {
        $lingua_entities_values = [];
        $lingua_entities_cfg_values = [];
        foreach ($entities_data as $entity_id => $values) 
        {
            if($values['name'])
            {
                $name = db_input($values['name']);
                $lingua_entities_values[] = "('{$this->language_id}', '$entity_id', '{$name}')";
            }
            foreach ($values['cfg'] as $cfg_id => $cfg_value) 
            {
                if($cfg_value)
                {
                    if(!is_numeric($cfg_id))
                    {
                        $new_cfg_value = CFG_APP_LANGUAGE == $this->language_name ? $cfg_value : '';
                        $cfg_id = $this->saveEntityCfg($entity_id, $cfg_id, $new_cfg_value);
                    }    
                    $cfg_value = db_input($cfg_value);
                    $lingua_entities_cfg_values[] = "('{$this->language_id}', '$cfg_id', '$cfg_value')"; 
                }
            }
        }
        $update = false;
        
        if(count($lingua_entities_values))
        {
            $entities_query = "INSERT INTO {$this->lingua_entities_table} (`language_id`, `entities_id`, `name`) VALUES " . implode(",",$lingua_entities_values) . " ON DUPLICATE KEY UPDATE `name`=VALUES(`name`)";
            db_query($entities_query);
            $update = true;
        }
        if(count($lingua_entities_cfg_values))
        {
            $entities_cfg_query = "INSERT INTO {$this->lingua_entities_configuration_table} (`language_id`, `entities_configuration_id`, `configuration_value`) VALUES " . implode(",",$lingua_entities_cfg_values) . " ON DUPLICATE KEY UPDATE `configuration_value`=VALUES(`configuration_value`)";
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
        $cfg_value = db_input($cfg_value);
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
            $name = db_input($values['name']);
            $shortname = db_input($values['shortname']);
            $sql_data[] = "({$this->language_id}, $field_id, '{$name}', '{$shortname}')";
        }
        $fields_query = "INSERT INTO {$this->lingua_fields_table} (`language_id`, `field_id`, `name`, `short_name`) VALUES " . implode(",",$sql_data) . " ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `short_name`=VALUES(`short_name`)";
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
            $name = db_input($values['name']);
            $description = db_input($values['description']);
            $sql_data[] = "({$this->language_id}, $forms_tabs_id, '{$name}', '{$description}')";
        }
        $fields_query = "INSERT INTO {$this->lingua_forms_tabs_table} (`language_id`, `forms_tabs_id`, `name`, `description`) VALUES " . implode(",",$sql_data) . " ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`)";
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
        $q = db_query("SELECT lf.*, f.entities_id FROM {$this->lingua_forms_tabs_table} lf JOIN app_forms_tabs f ON (lf.forms_tabs_id=f.id) WHERE language_id={$this->language_id} $filter");
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
     * saveUpdateTime
     *
     * @return void
     */
    private function saveUpdateTime()
    {
        $updateTime = time();
        db_query("UPDATE {$this->lingua_table} SET `date_updated`=$updateTime WHERE id={$this->language_id}");
    }   

    /**
     * getUpdateTime
     *
     * @return int
     */
    private function getUpdateTime()
    {
        $q = db_query("SELECT date_updated FROM {$this->lingua_table} WHERE id={$this->language_id}");
        $d = db_fetch_array($q);
        return (int)$d['date_updated'];
    }   

    /**
     * getLanguageId
     *
     * @param  string $language_name
     * @return int
     */
    private function getLanguageId($language_name)
    {
        $info = db_find($this->lingua_table, $language_name, "`language`");
        $id = $info['id'];
        if(!$id)
        {
            $language_name = db_input($language_name);
            db_query("INSERT INTO {$this->lingua_table} (`language`) VALUES ('{$language_name}')");
            $id = db_insert_id();
        }
        return (int)$id;
    }    

    /**
     * setLanguageCache
     *
     * @param  string $token
     * @return array
     */
    function setLanguageCache($token, $override = false)
    {
        $session_name = ROOX_PLUGIN.'_language_cache';
        global ${$session_name};
        if(!app_session_is_registered('app_logged_users_id'))
        {
            app_session_unregister($session_name);
        }

        $shouldUpdate = false;
        if($override)
        {
            $shouldUpdate = true;
        }
        else
        {
            $isCached = app_session_is_registered($session_name);
            if($isCached)
            {
                $isSamelanguage = isset(${$session_name}['language']) && ${$session_name}['language']==$this->language_name;
                $isSameToken = isset(${$session_name}['token']) && ${$session_name}['token']==$token;
                $isSameUpdate = isset(${$session_name}['date_updated']) && ${$session_name}['date_updated']==$this->getUpdateTime();
    
                if(!$isSamelanguage || !$isSameToken || !$isSameUpdate)
                {
                    $shouldUpdate = true;
                }
            }        
            else
            {
                app_session_register($session_name);
                $shouldUpdate = true;
            }    
        }

        if($shouldUpdate)
        {
            ${$session_name} = $this->getLanguageCache($token);
        }
    }    

    /**
     * getLanguageCache
     *
     * @param  string $token
     * @return array
     */
    private function getLanguageCache($token)
    {
        return [
            'language' => $this->language_name,
            'token' => $token,
            'date_updated' => $this->getUpdateTime(),
            'entities' => $this->getEntitiesData(),
            'entities_menu' => $this->getEntitiesMenu(),
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
            c.configuration_name, c.configuration_value, le.name AS language_name, 
            lc.configuration_value AS language_cfg_value FROM app_entities e 
            LEFT JOIN app_entities_configuration c ON (e.id=c.entities_id) 
            LEFT JOIN (SELECT * FROM {$this->lingua_entities_table} WHERE language_id={$this->language_id}) le ON (e.id=le.entities_id) 
            LEFT JOIN (SELECT * FROM {$this->lingua_entities_configuration_table} WHERE language_id={$this->language_id}) lc ON (c.id=lc.entities_configuration_id);"); 
            
        $default_cfg = [];
        while($d = db_fetch_array($q))
        {
            $entity_name = $d['language_name'] ? $d['language_name'] : $d['name'];
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
                $lingua_value = $d['language_cfg_value'];
                $current_value = $result[$d['id']][$d['configuration_name']]['value'];
                $result[$d['id']][$d['configuration_name']] = ['id'=>$d['cfg_id'], 'original'=>$d['configuration_value'], 'value'=>$lingua_value ? $lingua_value : ($current_value ? $current_value : $entity_name)];
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
        $q = db_query("SELECT lf.*, f.name AS original_name, f.entities_id, f.tooltip_display_as, f.tooltip_in_item_page, f.is_required FROM {$this->lingua_fields_table} lf JOIN app_fields f ON (lf.field_id=f.id) WHERE language_id={$this->language_id} $filter");
        $return = [];
        if(db_num_rows($q))
        {
            while($d = db_fetch_array($q))
            {
                if(!isset($return[$d['entities_id']]))
                {
                    $return[$d['entities_id']] = [];
                }
                $return[$d['entities_id']][$d['field_id']] = $d;
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
        $q = db_query("SELECT d.*, dv.dict_value AS language_value FROM ".ROOX_PLUGIN."_dictionary d LEFT JOIN (SELECT * FROM {$this->lingua_dictionary_table} WHERE language_id={$this->language_id}) dv ON (d.id=dv.dict_id)");
        $return = [];
        while($d = db_fetch_array($q))
        {
            $return[$d['id']] = ['dict_key'=>$d['dict_key'], 'dict_value'=>$d['language_value'] ? $d['language_value'] : ($d['dict_value'] ? $d['dict_value'] : $d['dict_key'])];
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
            $dict_value = db_input($dict_value);
            $sql_data[] = "({$this->language_id}, $dict_id, '{$dict_value}')";
        }

        $fields_query = "INSERT INTO {$this->lingua_dictionary_table} (`language_id`, `dict_id`, `dict_value`) VALUES " . implode(",", $sql_data) . " ON DUPLICATE KEY UPDATE `dict_value`=VALUES(`dict_value`)";
        db_query($fields_query);
    }

    static private function getReports()
    {
        $result = [];
        $q = db_query("SELECT id, entities_id FROM app_reports WHERE reports_type = 'entity_menu'");
        while($d = db_fetch_array($q))
        {
            $result[$d['id']] = $d['entities_id'];
        }
        return $result;
    }
    static function getEntityCfg($entity_id, $cfg_key = 'listing_heading')
    {
        global ${ROOX_PLUGIN . '_language_cache'};
        $lingua_menu_data = ${ROOX_PLUGIN . '_language_cache'}['entities'][$entity_id];
        return (strlen($lingua_menu_data[$cfg_key]['value']) > 0 ? $lingua_menu_data[$cfg_key]['value'] : $lingua_menu_data['name']);                
    }

    static function buildEntitiesMenu($menu)
    {
        global $app_user, ${ROOX_PLUGIN . '_language_cache'};
        $locale_entities = ${ROOX_PLUGIN . '_language_cache'}['entities'];

        $custom_entities_menu = array();
        $menu_query = db_fetch_all('app_entities_menu', 'length(entities_list)>0', 'sort_order, name');
        while($v = db_fetch_array($menu_query))
        {
            $custom_entities_menu = array_merge($custom_entities_menu, explode(',', $v['entities_list']));
        }
    
        $where_sql = '';
    
        if(count($custom_entities_menu) > 0)
        {
            $where_sql = " and e.id not in (" . implode(',', $custom_entities_menu) . ")";
        }
    
        if($app_user['group_id'] == 0)
        {
            $entities_query = db_query("select * from app_entities e where (e.parent_id = 0 or e.display_in_menu=1) {$where_sql} order by e.sort_order, e.name");
        }
        else
        {
            $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and (e.parent_id = 0 or display_in_menu=1) {$where_sql} order by e.sort_order, e.name");
        }
    
        while($entities = db_fetch_array($entities_query))
        {
            if($entities['parent_id'] == 0)
            {
                $s = array();
    
                $entity_cfg = new \entities_cfg($entities['id']);
                $menu_title = $locale_entities[$entities['id']]['menu_title']['value'] ?? (strlen($entity_cfg->get('menu_title')) > 0 ? $entity_cfg->get('menu_title') : $entities['name']);
                $menu_icon = (strlen($entity_cfg->get('menu_icon')) > 0 ? $entity_cfg->get('menu_icon') : ($entities['id'] == 1 ? 'fa-user' : 'fa-reorder'));
    
                $menu[] = array(
                    'title' => $menu_title, 
                    'url' => url_for('items/items', 
                    'path=' . $entities['id']), 
                    'class' => $menu_icon,
                    'icon_color' => $entity_cfg->get('menu_icon_color'),
                    'bg_color' => $entity_cfg->get('menu_bg_color'),
                    );
            }
            else
            {
                $reports_info = \reports::create_default_entity_report($entities['id'], 'entity_menu');
    
                //check if parent reports was not set
                if($reports_info['parent_id'] == 0)
                {
                    \reports::auto_create_parent_reports($reports_info['id']);
                }
    
                $entity_cfg = new \entities_cfg($entities['id']);
                $menu_title = $locale_entities[$entities['id']]['menu_title']['value'] ?? (strlen($entity_cfg->get('menu_title')) > 0 ? $entity_cfg->get('menu_title') : $entities['name']);
                $menu_icon = (strlen($entity_cfg->get('menu_icon')) > 0 ? $entity_cfg->get('menu_icon') : 'fa-reorder');
    
                $menu[] = array(
                    'title' => $menu_title, 
                    'url' => url_for('reports/view', 'reports_id=' . $reports_info['id']), 
                    'class' => $menu_icon,
                    'icon_color' => $entity_cfg->get('menu_icon_color'),
                    'bg_color' => $entity_cfg->get('menu_bg_color'),
                    );
            }
        }
    
        return $menu;
    }

    static function buildCustomEntitiesMenu($menu, $parent_id = 0, $level = 0)
    {
        global $app_user, ${ROOX_PLUGIN . '_language_cache'};

        $locale_entities_menu = ${ROOX_PLUGIN . '_language_cache'}['entities_menu'];
        $locale_entities = ${ROOX_PLUGIN . '_language_cache'}['entities'];
        if($level > 3)
            return [];
    
        $custom_entities_menu = array();
        $entities_menu_query = db_fetch_all('app_entities_menu', 'parent_id=' . $parent_id, 'sort_order, name');
        while($entities_menu = db_fetch_array($entities_menu_query))
        {
            $sub_menu = array();
    
            //add entities
            if(strlen($entities_menu['entities_list']??'') and $entities_menu['type']=='entity')
            {
                $where_sql = " e.id in (" . $entities_menu['entities_list'] . ")";
    
                if($app_user['group_id'] == 0)
                {
                    $entities_query = db_query("select * from app_entities e where e.id in (" . $entities_menu['entities_list'] . ") order by field(e.id," . $entities_menu['entities_list'] . ")");
                }
                else
                {
                    $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and e.id in (" . $entities_menu['entities_list'] . ") order by field(e.id," . $entities_menu['entities_list'] . ")");
                }
    
                while($entities = db_fetch_array($entities_query))
                {
                    if($entities['parent_id'] == 0)
                    {
                        $s = array();
    
                        $entity_cfg = new \entities_cfg($entities['id']);
                        $menu_title = $locale_entities[$entities['id']]['menu_title']['value'] ?? (strlen($entity_cfg->get('menu_title')) > 0 ? $entity_cfg->get('menu_title') : $entities['name']);
                        $menu_icon = (strlen($entity_cfg->get('menu_icon')) > 0 ? $entity_cfg->get('menu_icon') : ($entities['id'] == 1 ? 'fa-user' : 'fa-reorder'));
    
                        $sub_menu[] = array(
                            'title' => $menu_title, 
                            'url' => url_for('items/items', 'path=' . $entities['id']), 
                            'class' => $menu_icon,
                            'icon_color' => $entity_cfg->get('menu_icon_color'),
                            'bg_color' => $entity_cfg->get('menu_bg_color'),
                            );
                    }
                    else
                    {
                        $reports_info = \reports::create_default_entity_report($entities['id'], 'entity_menu');
    
                        //check if parent reports was not set
                        if($reports_info['parent_id'] == 0)
                        {
                            \reports::auto_create_parent_reports($reports_info['id']);
                        }
    
                        $entity_cfg = new \entities_cfg($entities['id']);
                        $menu_title = $locale_entities[$entities['id']]['menu_title']['value'] ?? (strlen($entity_cfg->get('menu_title')) > 0 ? $entity_cfg->get('menu_title') : $entities['name']);
                        $menu_icon = (strlen($entity_cfg->get('menu_icon')) > 0 ? $entity_cfg->get('menu_icon') : ($entities['id'] == 1 ? 'fa-user' : 'fa-reorder'));
    
                        $sub_menu[] = array(
                            'title' => $menu_title, 
                            'url' => url_for('reports/view', 'reports_id=' . $reports_info['id']), 
                            'class' => $menu_icon,
                            'icon_color' => $entity_cfg->get('menu_icon_color'),
                            'bg_color' => $entity_cfg->get('menu_bg_color'),
                            );
                    }
                }
            }
    
            //add reports
            if($entities_menu['type']=='entity')
            {
                $sub_menu = \entities_menu::build_menu($entities_menu['reports_list'], $sub_menu);
                $sub_menu = \entities_menu::build_pages_menu($entities_menu['pages_list'], $sub_menu);
            }
            
            //add urls
            if($entities_menu['type']=='url' and strlen($entities_menu['url']))
            {            
                if((strlen($entities_menu['users_groups']) and in_array($app_user['group_id'],explode(',',$entities_menu['users_groups']))) or strlen($entities_menu['assigned_to']) and in_array($app_user['id'],explode(',',$entities_menu['assigned_to'])))
                {
                    $menu_icon = (strlen($entities_menu['icon']) > 0 ? $entities_menu['icon'] : 'fa-reorder');
                    $sub_menu[] = array(
                        'title' => $locale_entities_menu[$entities_menu['id']]['name'] ?? $entities_menu['name'], 
                        'url' => $entities_menu['url'], 
                        'class' => $menu_icon,'target'=>'_blank',
                        'icon_color' => $entities_menu['icon_color'],
                        'bg_color' => $entities_menu['bg_color'],
                        );                
                }
                
            }       
    
            $sub_menu = self::buildCustomEntitiesMenu($sub_menu, $entities_menu['id'], $level + 1);
    
            $nested_query = db_query("select id from app_entities_menu where parent_id='" . $entities_menu['id'] . "' limit 1");
            $has_nested = db_fetch_array($nested_query);
    
            if(count($sub_menu) == 1 and !$has_nested)
            {
                $menu_icon = (strlen($entities_menu['icon']) > 0 ? $entities_menu['icon'] : 'fa-reorder');
                $menu[] = array(
                    'title' => $locale_entities_menu[$entities_menu['id']]['name'] ?? $entities_menu['name'], 
                    'url' => $sub_menu[0]['url'], 
                    'class' => $menu_icon,
                    'icon_color' => $entities_menu['icon_color'],
                    'bg_color' => $entities_menu['bg_color'],
                    'target'=>$sub_menu[0]['target']??false);
            }
            elseif(count($sub_menu) > 0)
            {
                $menu_icon = (strlen($entities_menu['icon']??'') > 0 ? $entities_menu['icon'] : 'fa-reorder');
                $menu[] = array(
                    'title' => $locale_entities_menu[$entities_menu['id']]['name'] ?? $entities_menu['name'], 
                    'url' => $sub_menu[0]['url'], 
                    'class' => $menu_icon, 
                    'icon_color' => $entities_menu['icon_color'],
                    'bg_color' => $entities_menu['bg_color'],
                    'submenu' => $sub_menu);
            }
        }        
    
        return $menu;
    }
    
    static function buildMainMenu()
    {
        global $app_user;
    
        $menu = array();
    
        if(is_ext_installed())
        {
            $menu = \mail_accounts::render_menu_item($menu);
        }
    
        $menu[] = array('title' => TEXT_MENU_DASHBOARD, 'url' => url_for('dashboard/dashboard'), 'class' => 'fa-home');
    
        $menu = build_reports_groups_menu($menu);

        // $menu = build_entities_menu($menu);

        $menu = self::buildEntitiesMenu($menu); //EDO

        // $menu = build_custom_entities_menu($menu);
        
        $menu = self::buildCustomEntitiesMenu($menu); //EDO
    
        $menu = build_call_history_menu($menu);
    
        $menu = build_reports_menu($menu);
    
        $menu = build_search_menu($menu);
    
        if(count($plugin_menu = \plugins::include_menu('menu')) > 0)
        {
            $menu = array_merge($menu, $plugin_menu);
        }
    
        //only administrators have access to configurations
        if($app_user['group_id'] == 0)
        {
            //menu Configuration
    
    
            $s = array();
            $s[] = array('title' => TEXT_MENU_APPLICATION, 'url' => url_for('configuration/application'));
            
            $ss = [];
            $ss[] = array('title' => TEXT_USERS_CONFIGURATION, 'url' => url_for('configuration/users_settings'));
            $ss[] = array('title' => TEXT_MENU_USER_REGISTRATION_EMAIL, 'url' => url_for('configuration/users_registration'));
            $ss[] = array('title' => TEXT_PUBLIC_REGISTRATION, 'url' => url_for('configuration/public_users_registration'));
            $s[] = array('title' => TEXT_USERS, 'url' => url_for('configuration/users_settings'), 'submenu' => $ss);
            
            $ss = [];
            $ss[] = array('title' => TEXT_MENU_LOGIN_PAGE, 'url' => url_for('configuration/login_page'));
            $ss[] = array('title' => TEXT_2STEP_VERIFICATION, 'url' => url_for('configuration/2step_verification'));
            $ss[] = array('title' => TEXT_SOCIAL_LOGIN, 'url' => url_for('configuration/social_login'));
            $ss[] = array('title' => TEXT_GUEST_LOGIN, 'url' => url_for('configuration/guest_login'));
            $s[] = array('title' => TEXT_BUTTON_LOGIN, 'url' => url_for('configuration/login_page'), 'submenu' => $ss);
            
            $ss = [];
            $ss[] = array('title' => TEXT_MENU_EMAIL_OPTIONS, 'url' => url_for('configuration/emails'));
            $ss[] = array('title' => TEXT_EMAIL_SMTP_CONFIGURATION, 'url' => url_for('configuration/emails_smtp'));
            $ss[] = array('title' => TEXT_EMAILS_LAYOUT, 'url' => url_for('configuration/emails_layout'));
            $ss[] = array('title' => TEXT_BUTTON_SEND_TEST_EMAIL, 'url' => url_for('configuration/emails_send_test'));
            $s[] = array('title' => TEXT_MENU_EMAIL_OPTIONS, 'url' => url_for('configuration/emails'), 'submenu' => $ss);
            
            $s[] = array('title' => TEXT_MENU_ATTACHMENTS, 'url' => url_for('configuration/attachments'));
            $s[] = array('title' => TEXT_MENU_SECURITY, 'url' => url_for('configuration/security'));
            $s[] = array('title' => TEXT_SERVER_LOAD, 'url' => url_for('configuration/server_load'));
            $s[] = array('title' => TEXT_MENU_LDAP, 'url' => url_for('configuration/ldap'));
            $s[] = array('title' => 'PDF', 'url' => url_for('configuration/pdf'));
            $s[] = array('title' => TEXT_HOLIDAYS, 'url' => url_for('holidays/holidays'));
    
    
            $s[] = array('title' => TEXT_MENU_MAINTENANCE_MODE, 'url' => url_for('configuration/maintenance_mode'));
            $s[] = array('title' => TEXT_CUSTOM_CSS, 'url' => url_for('configuration/custom_css'));
            $s[] = array('title' => TEXT_CUSTOM_HTML, 'url' => url_for('configuration/custom_html'));
            $s[] = array('title' => TEXT_CUSTOM_PHP, 'url' => url_for('custom_php/code'));
    
    
            $menu[] = array('title' => TEXT_MENU_CONFIGURATION, 'url' => url_for('configuration/application'), 'submenu' => $s, 'class' => 'fa-gear');
    
            $s = array();
            $s[] = array('title' => TEXT_MENU_ENTITIES_LIST, 'url' => url_for('entities/entities'));
            $s[] = array('title' => TEXT_MENU_USERS_ACCESS_GROUPS, 'url' => url_for('users_groups/users_groups'));
            $s[] = array('title' => TEXT_MENU_GLOBAL_LISTS, 'url' => url_for('global_lists/lists'));
            $s[] = array('title' => TEXT_GLOBAL_VARS, 'url' => url_for('global_vars/vars'));
            $s[] = array('title' => TEXT_MENU_CONFIGURATION_MENU, 'url' => url_for('entities/menu'));
            $s[] = array('title' => TEXT_DASHBOARD_CONFIGURATION, 'url' => url_for('dashboard_configure/index'));
            $menu[] = array('title' => TEXT_MENU_APPLICATION_STRUCTURE, 'url' => url_for('entities/'), 'class' => 'fa-sitemap', 'submenu' => $s);
    
            $s = \plugins::include_menu('extension');
    
            if(count($s) > 0)
            {
                $menu[] = array('title' => TEXT_MENU_EXTENSION, 'url' => url_for('ext/ext/'), 'submenu' => $s, 'class' => 'fa-puzzle-piece');
            }
            else
            {
                $menu[] = array('title' => TEXT_MENU_EXTENSION, 'url' => url_for('tools/extension'), 'class' => 'fa-puzzle-piece');
            }
    
    
            //Menu Tools
            $s = array();
            $s[] = array('title' => TEXT_USERS_ALERTS, 'url' => url_for('users_alerts/users_alerts'));
            $s[] = array('title' => TEXT_USERS_LOGIN_LOG, 'url' => url_for('tools/users_login_log'));
            
            $ss = [];
            $ss[] = array('title' => TEXT_MENU_BACKUP, 'url' => url_for('tools/db_backup'));
            $ss[] = array('title' => TEXT_AUTO_BACKUP, 'url' => url_for('tools/db_backup_auto'));
            $s[] = array('title' => TEXT_MENU_BACKUP, 'url' => url_for('tools/db_backup'), 'submenu' => $ss);
            $s[] = array('title' => TEXT_MENU_CHECK_VERSION, 'url' => url_for('tools/check_version'));
            $s[] = array('title' => TEXT_MENU_SERVER_INFO, 'url' => url_for('tools/server_info'));
            
            $ss = [];
            $ss[] = array('title' => TEXT_SETTINGS, 'url' => url_for('logs/settings'));
            $ss[] = array('title' => 'HTTP', 'url' => url_for('logs/view','type=http'));
            $ss[] = array('title' => 'MySQL', 'url' => url_for('logs/view','type=mysql'));
            $ss[] = array('title' => 'PHP', 'url' => url_for('logs/view','type=php'));
            $ss[] = array('title' => 'Email', 'url' => url_for('logs/view','type=email'));
            $s[] = array('title' => TEXT_LOGS, 'url' => url_for('logs/settings'), 'submenu' => $ss);
            
            $menu[] = array('title' => TEXT_MENU_TOOLS, 'url' => url_for('tools/db_backup'), 'submenu' => $s, 'class' => 'fa-wrench');
    
    
            $store_language = (APP_LANGUAGE_SHORT_CODE == 'ru' ? '.ru' : '');
    
            $s = array();
            $s[] = array('title' => TEXT_DOCUMENTATION, 'url' => 'https://docs.rukovoditel.net' . $store_language, 'target' => '_balnk');
            $s[] = array('title' => TEXT_MENU_REPORT_FORUM, 'url' => 'https://forum.rukovoditel.net' . $store_language, 'target' => '_balnk');
            $s[] = array('title' => TEXT_NEWS, 'url' => (APP_LANGUAGE_SHORT_CODE == 'ru' ? 'https://vk.com/rukovoditel_project' : 'https://www.facebook.com/RukovoditelProject/timeline'), 'target' => '_balnk');
            $s[] = array('title' => TEXT_MENU_DONATE, 'url' => 'https://www.rukovoditel.net' . $store_language . '/donate.php', 'target' => '_balnk');
            $s[] = array('title' => TEXT_MENU_CONTACT_US, 'url' => 'https://www.rukovoditel.net' . $store_language . '/contact_us.php', 'target' => '_balnk');
            $menu[] = array('title' => TEXT_DOCUMENTATION, 'url' => 'https://docs.rukovoditel.net' . $store_language, 'submenu' => $s, 'class' => 'fa-book');
        }
    
        return $menu;
    }
}
?>
