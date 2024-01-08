<?php
$app_title = app_set_title(TEXT_FORM_TAB);
switch($app_module_action)
{
    case 'save':
      $Lingua->saveFormsTabs($_POST['forms_tabs']);
      redirect_to(ROOX_PLUGIN ."/{$module_name}/forms_tabs", 'entities_id=' . $_POST['entities_id']);
}