<?php
    if(CFG_LANGUAGE_SWITCH)
    {
        global $locale_name, $locale_setting;
        $data = file_get_contents("plugins/".ROOX_PLUGIN."/modules/{$module}/components/flags/".str_replace('.php', '', $locale_setting).".png");
        $$locale_setting = 'data:image/png;base64,' . base64_encode($data);
        $referrer = base64_encode($_SERVER['QUERY_STRING']);
?>
    <li class="dropdown" id="language_switcher">
        <a href="#" title="<?php echo $locale_name; ?>" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <img src="<?php echo $$locale_setting; ?>" alt="<?php echo strtoupper(APP_LANGUAGE_SHORT_CODE); ?>" height="15" style="margin-top:-5px;">
        </a>
        <ul class="dropdown-menu extended">
            <li style="cursor:pointer">
                <p><?php echo TEXT_LANGUAGE; ?></p>
            </li>
            <li>
                <ul class="dropdown-menu-list scroller" style="height: 150px; overflow: hidden; width: auto;">
                <?php 
                    foreach(app_get_languages_choices() as $k=>$language)
                    {
                        if($k==$locale_setting)
                        {
                            continue;
                        }
                        $$k = isset($$k) ? $$k : 'data:image/png;base64,' . base64_encode(file_get_contents("plugins/".ROOX_PLUGIN."/modules/{$module}/components/flags/".str_replace('.php', '', $k).".png"));                        
                        echo "
                        <li>
                            <a href='".url_for(ROOX_PLUGIN."/{$module}/", "action=set_language&language={$k}&ref={$referrer}")."'>
                                <img src='{$$k}' alt='$language' height='15'>  $language
                            </a>
                        </li>";
                    } 
                ?>
                </ul>
            </li>          
        </ul>            
    </li>
<?php
}    
?>