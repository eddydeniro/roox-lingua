<?php
switch($app_module_action)
{
    case 'set_locale':
        $locale_setting = isset($_POST['locale']) ? $_POST['locale'] : $_GET['locale'];
        db_perform('app_entity_1', ['field_13'=>$locale_setting], 'update', "id={$app_user['id']}");
        redirect_to_ref($_GET['ref']);
    case 'cfg_switcher':
        $cfg_switcher = (int)$_POST['switcher'];
        $Locale->localeSwitcher($cfg_switcher);
        echo $cfg_switcher ? "reload" : "";
    case 'clean_orphans':
        $Locale->validateData();
        echo $Locale->countOrphans();
        exit();
}
?>