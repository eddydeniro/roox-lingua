<?php
$app_title = app_set_title(TEXT_FIELDS_CONFIGURATION);

switch($app_module_action)
{
    case 'save':
        $Locale->saveFieldData($_POST['fields']);
        redirect_to(ROOX_PLUGIN ."/{$module_name}/fields", 'entities_id=' . $_POST['entities_id']);
}