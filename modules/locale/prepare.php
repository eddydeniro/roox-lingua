<?php
    $locale_choices = app_get_languages_choices();
    $locale_setting = $app_user['language'] ? $app_user['language'] : 'english.php' ;
    $locale_name = $locale_choices[$locale_setting];  

    $Locale = new Roox\Locale($locale_setting, ROOX_PLUGIN);
?>