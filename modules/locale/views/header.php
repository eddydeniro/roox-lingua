<style>
    .transparent{
        border: none;
        background: none;
    }
    .input_adj{
        display: inline-block;
        position: absolute;
    }
</style>
<div class="row">
    <div class="col-md-9">
        <h3 class="page-title"><?php echo TEXT_LOCALE_CONFIG ?></h3>
    </div>
    <div class="col-md-3">
        <?php
            echo form_tag('lang_list_form', url_for("{$plugin_name}/{$module_name}/entities", 'action=set_lang')) .
            input_hidden_tag('page', $current_page) .
            select_tag('locale_setting', $locale_choices, $locale_setting, array('class' => 'form-control  ', 'onChange' => 'this.form.submit()')) .
            '</form>';
        ?>
    </div>
</div>