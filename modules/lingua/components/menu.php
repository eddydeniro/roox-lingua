<?php
$menu = [];
if($app_user['id']==1 || Roox\Core::hasAccess($module))
{
    $menu = ['title'=>MOD_LINGUA, 'url'=>url_for(ROOX_PLUGIN."/".$module."/entities"), 'class'=>'fa-language'];
}
?>