<?php
switch($app_module_action)
{
    case 'set_language':
        $language_setting = isset($_POST['language']) ? $_POST['language'] : $_GET['language'];
        db_perform('app_entity_1', ['field_13'=>$language_setting], 'update', "id={$app_user['id']}");
        redirect_to_ref($_GET['ref']);
    case 'cfg_switcher':
        $cfg_switcher = (int)$_POST['switcher'];
        $Lingua->languageSwitcher($cfg_switcher);
        if($cfg_switcher)
        {
            ${ROOX_PLUGIN.'_active_tab'} = 'lingua';
            redirect_to(ROOX_PLUGIN."/core/");
        }
        exit();
    case 'clean_orphans':
        $Lingua->validateData();
        echo $Lingua->countOrphans();
        exit();
}
?>