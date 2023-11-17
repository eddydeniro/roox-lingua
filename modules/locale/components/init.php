<?php
    $locale_choices = app_get_languages_choices();
    $locale_setting = $app_user['language'] ? $app_user['language'] : 'english.php' ;
    $locale_name = $locale_choices[$locale_setting];  

    $Locale = new Roox\Locale($locale_setting, ROOX_PLUGIN);
    $locale_id = $Locale->locale_id;
    ${ROOX_PLUGIN . '_locale_cache'} = $Locale->setLocaleCache($app_session_token);
    $roox_dictionary = $Locale->getDefinitions();
    define('CFG_LOCALE_SWITCH', $Locale->localeSwitcher());
    foreach ($roox_dictionary as $values) 
    {
        if(!defined($values['dict_key']))
        {
            define($values['dict_key'], $values['dict_value']);
        }
    }
    $app_layout = component_path(ROOX_PLUGIN . "/locale/layout");
?>