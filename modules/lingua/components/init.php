<?php
    $language_choices = app_get_languages_choices();
    $language_setting = $app_user['language'] ? $app_user['language'] : 'english.php' ;
    $language_name = $language_choices[$language_setting];  

    $Locale = new Roox\Lingua($language_setting, ROOX_PLUGIN);
    $locale_id = $Locale->lingua_id;
    $Locale->setLocaleCache($app_session_token);
    $roox_dictionary = $Locale->getDefinitions();
    define('CFG_LANGUAGE_SWITCH', $Locale->languageSwitcher());
    foreach ($roox_dictionary as $values) 
    {
        if(!defined($values['dict_key']))
        {
            define($values['dict_key'], $values['dict_value']);
        }
    }
    $app_layout = component_path(ROOX_PLUGIN . "/lingua/layout");
?>