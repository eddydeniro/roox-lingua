<?php
$menu = [];
if(!$app_user['group_id'])
{
    $menu = ['title'=>TEXT_LOCALE_TRANSLATION, 'url'=>url_for(ROOX_PLUGIN."/".$module."/entities"), 'class'=>'fa-language'];
}
//This is where we can also get breadcrumb and modify it;
?>