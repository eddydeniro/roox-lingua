<div class="row">
    <div class="col-md-9">
        <h3 class="page-title"><?php echo MOD_LINGUA ?></h3>
    </div>
    <div class="col-md-3">
        <?php
        echo form_tag('lang_list_form', url_for("{$plugin_name}/{$module_name}/", "action=set_language&ref=" . base64_encode($_SERVER['QUERY_STRING']))) .
            select_tag('language', $language_choices, $language_setting, array('class' => 'form-control  ', 'onChange' => 'this.form.submit()')) .
            '</form>';
        ?>
    </div>
</div>
<p><?php echo TEXT_LINGUA_INFO; ?></p>
<hr>

<div class="navbar navbar-default navbar-items" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only"></span>
            <span class="fa fa-bar "></span>
            <span class="fa fa-bar fa-align-justify"></span>
            <span class="fa fa-bar"></span>
        </button>
        <a href="<?php echo url_for("{$plugin_name}/{$module_name}/entities") ?>" class="navbar-brand <?php echo $app_action=='entities'?'selected':''; ?>"><?php echo TEXT_HEADING_ENTITY_CONFIGURATION ?></a>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li class="navbar-nav-entities_menu <?php echo $app_action=='entities_menu'?'selected':''; ?>">
                <a href="<?php echo url_for("{$plugin_name}/{$module_name}/entities_menu") ?>"><?php echo TEXT_MENU_CONFIGURATION_MENU ?></a>
            </li>
            <?php if($app_action=='fields'): ?>
                <li class="navbar-nav-fields selected">
                    <a href="#"><?php echo TEXT_FIELDS . " | " . ${ROOX_PLUGIN . '_language_cache'}['entities'][$_GET['entities_id']]['name'] ?></a>
                </li>                
            <?php endif; ?>
            <?php if($app_action=='forms_tabs'): ?>
                <li class="navbar-nav-forms_tabs selected">
                    <a href="#"><?php echo TEXT_FORM_TAB . " | " . ${ROOX_PLUGIN . '_language_cache'}['entities'][$_GET['entities_id']]['name']?></a>
                </li>                
            <?php endif; ?>            
        </ul>
    </div>
</div>