<div class="row">
    <div class="col-md-9">
        <h3 class="page-title"><?php echo MOD_LINGUA ?></h3>
    </div>
    <div class="col-md-3">
        <?php
            echo form_tag('lang_list_form', url_for("{$plugin_name}/{$module_name}/locale", "action=set_locale&ref=".base64_encode($_SERVER['QUERY_STRING']))) .
            select_tag('language', $locale_choices, $locale_setting, array('class' => 'form-control  ', 'onChange' => 'this.form.submit()')) .
            '</form>';
        ?>
    </div>
</div>
<p><?php echo TEXT_LINGUA_INFO; ?></p>
<hr>