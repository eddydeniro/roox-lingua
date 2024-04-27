<?php
$loaded_module = $_GET['module'];
$current_entity_id = 0;
global ${ROOX_PLUGIN . '_language_cache'}, $app_user, $app_fields_cache;
$goReplace = $app_user['language'] == CFG_APP_LANGUAGE ? 0 : 1;
if(in_array($loaded_module, ['items/items', 'items/', 'items/info']))
{
    $path_array = explode('/', $app_path);
    $last_path_item = explode('-', $path_array[count($path_array) - 1]);
    $current_entity_id = $last_path_item[0];
}
if($loaded_module=='reports/view')
{
    $current_entity_id = ${ROOX_PLUGIN . '_language_cache'}['reports'][$_GET['reports_id']];
}
$dropdown_fields = [];
if($current_entity_id)
{
    $types = [
        'fieldtype_dropdown', 
        'fieldtype_dropdown_multiple', 
        'fieldtype_dropdown_multilevel',
        'fieldtype_tags',
        'fieldtype_radioboxes',
        'fieldtype_checkboxes',
        'fieldtype_color',
        'fieldtype_stages',
        'fieldtype_grouped_users'
    ];
    $translated_global_list = Roox\Lingua::getTranslatedChoices(${ROOX_PLUGIN . '_language_cache'}['global_lists']);
    $translated_field_choices = Roox\Lingua::getTranslatedChoices(${ROOX_PLUGIN . '_language_cache'}['fields_choices']);
    foreach ($app_fields_cache[$current_entity_id] as $key => $value) 
    {
        if(in_array($value['type'], $types))
        {
            $cfg = new fields_types_cfg($value['configuration']);
            if($cfg->get('use_global_list') && in_array($cfg->get('use_global_list'), array_keys($translated_global_list)))
            {
                $dropdown_fields[$value['id']] = $translated_global_list[$cfg->get('use_global_list')];
            }
            if(!$cfg->get('use_global_list') && in_array($key, array_keys($translated_field_choices)))
            {
                $dropdown_fields[$value['id']] = $translated_field_choices[$key];
            }            
        }
    }
}
$language_data = json_encode(array_filter(${ROOX_PLUGIN . '_language_cache'}, function($key){
    return !in_array($key, ['language', 'date_updated', 'token']);
}, ARRAY_FILTER_USE_KEY));

?>
<script>
$(document).ready(function(){
    const _entity_id = <?php echo $current_entity_id ?>,
        language_data = <?php echo $language_data ?>,
        go_replace = <?php echo $goReplace ?>;

    const urlSearchParams = new URLSearchParams(window.location.search),
        params = Object.fromEntries(urlSearchParams.entries());

    if(_entity_id && go_replace)
    {
        const entities_data = language_data['entities'];
        const current_entity_data = entities_data[_entity_id];

        for (const entity_id in entities_data) {
            const item = entities_data[entity_id]['name'];
            $('a.navbar-nav-entity-' + _entity_id).text(item);
        }
        if(params.module=='items/items' || params.module=='items/' || params.module=='items/info' || params.module=='reports/view'){
            $('title').text($('title').text().replace(entities_data[_entity_id]['listing_heading']['original'], entities_data[_entity_id]['listing_heading']['value']));

            if(params.module=='reports/view'){
                $('h3.page-title').text(entities_data[_entity_id]['listing_heading']['value']);
            }
            $('.entitly-listing-buttons-left > button').text(current_entity_data['insert_button']['value']);
            const field_data = language_data['fields'][_entity_id];
            const tabs_data = language_data['forms_tabs'][_entity_id];   
            const dropdown_fields = <?php echo json_encode($dropdown_fields) ?>;
            const global_list_data = language_data['global_lists'];

            $('#ajax-modal').on('shown', function(){
                $('h4.modal-title').text(entities_data[_entity_id]['window_heading']['value']);
                for (const field_id in field_data){
                    if($('.form-group-' + field_id + '> label').length)
                    {
                        if(field_data[field_id].name && field_data[field_id].original_name){
                            $('.form-group-' + field_id + '> label').contents().filter(function(){
                                return this.nodeType===3;
                            })[0].nodeValue = ' ' + field_data[field_id].name;
                        }
                        if(field_data[field_id].tooltip){
                            $('.form-group-' + field_id + '> label').find('i').attr('data-original-title', field_data[field_id].tooltip);
                            $('.form-group-' + field_id).find('span.help-block').text(field_data[field_id].tooltip);
                        }
                    }
                }
                for (const field_id in dropdown_fields) {
                    if(typeof dropdown_fields[field_id] !=='undefined' && dropdown_fields[field_id]){
                        const input_choices = dropdown_fields[field_id];
                        for (const choices_id in input_choices) {
                            const selector1 = '#fields_'+field_id;
                            const option_text = $(selector1+' option[value='+choices_id+']').text().replace(input_choices[choices_id]['original'], input_choices[choices_id]['name']);
                            $(selector1+' option[value='+choices_id+']').text(option_text);
                            if($(selector1).hasClass('chosen-select'))
                            {
                                $(selector1).trigger('chosen:updated');
                            }
                            if($(selector1).hasClass('select2-hidden-accessible'))
                            {
                                //https://select2.org/troubleshooting/common-problems
                                /*
                                    Select2 does not function properly when I use it inside a Bootstrap modal.
                                    This issue occurs because Bootstrap modals tend to steal focus from other elements outside of the modal. Since by default, Select2 attaches the dropdown menu to the <body> element, it is considered "outside of the modal".                                    
                                    */
                                $(selector1).select2('destroy');
                                $(selector1).select2({
                                    dropdownParent: $('#ajax-modal')
                                });
                            }
                            const selector2 = '#fields_'+field_id+'_0';
                            if($(selector2).length){
                                const option_text2 = $(selector2+' option[value='+choices_id+']').text().replace(input_choices[choices_id]['original'], input_choices[choices_id]['name']);
                                $(selector2+' option[value='+choices_id+']').text(option_text2);
                                $(selector2).change(function(){
                                    const current_id = field_id;
                                    const current_choices = choices_id;
                                    const s = '#fields_'+current_id+'_1';
                                    const t = $(s+' option[value='+current_choices+']').text().replace(input_choices[current_choices]['original'], input_choices[current_choices]['name']);
                                    $(s+' option[value='+current_choices+']').text(t);
                                })
                            }
                            const selector3 = '#fields_'+field_id+'_1';
                            if($(selector3).length){
                                const option_text3 = $(selector3+' option[value='+choices_id+']').text().replace(input_choices[choices_id]['original'], input_choices[choices_id]['name']);
                                $(selector3+' option[value='+choices_id+']').text(option_text3);
                            }
                            const selector4 = '#uniform-fields_'+field_id+'_'+choices_id;
                            if($(selector4).length)
                            {
                                $(selector4).parent().contents().filter(function(){
                                    return this.nodeType===3;
                                })[0].nodeValue = ' ' + input_choices[choices_id]['name'];

                            }
                            
                        }
                    }
                }
                for (const tab_id in tabs_data){
                    if(tabs_data[tab_id].name){
                        $('.form_tab_' + tab_id + '> a').text(tabs_data[tab_id].name);
                        if(tabs_data[tab_id].description){
                            if($('#form_tab_'+ tab_id +' p:first-child').length)
                            {
                                $('#form_tab_'+ tab_id +' p:first-child').html(tabs_data[tab_id].description);
                            }
                            else
                            {
                                $('#form_tab_'+ tab_id).prepend('<p>' + tabs_data[tab_id].description + '</p>');
                            }
                        }
                    }        
                }                
            })              
            if(params.module=='items/items' || params.module=='items/'|| params.module=='reports/view'){
                roox.waitForElement('.table-scrollable.table-wrapper').then(function(){
                    for (const field_id in field_data){
                        if(field_data[field_id].name && field_data[field_id].original_name){
                            $('.field-' + field_id + '-th').html('<div>'+(field_data[field_id].short_name ? field_data[field_id].short_name : field_data[field_id].name)+'</div>');
                        }        
                    }
                });    
            }
            if(params.module=='items/info'){
                $('div.navbar-header > a.navbar-brand').text(entities_data[_entity_id]['window_heading']['value']);
                for (const field_id in field_data){
                    if($('tr.form-group-' + field_id + '>th').length)
                    {
                        if(field_data[field_id].name && field_data[field_id].original_name){
                            $('tr.form-group-' + field_id + '>th').contents().filter(function(){
                                return this.nodeType===3;
                            })[0].nodeValue = field_data[field_id].name + ' ';
                        }
                    }
                }
                for (const tab_id in tabs_data){
                    if(tabs_data[tab_id].name){
                        $('.check-form-tabs[cfg_tab_id=info_box_tab_'+tab_id+'] > div > h4').text(tabs_data[tab_id].name);
                    }        
                }

                var delay = 1000; //Should delay, I think Rukovoditel script is executed after this line.
                setTimeout(function() {
                    for (const field_id in field_data){
                        if($('tr.form-group-' + field_id + '>th').length)
                        {
                            const   check_tooltip_1 = field_data[field_id].tooltip_item_page,
                                    check_tooltip_2 = field_data[field_id].tooltip && field_data[field_id].tooltip_in_item_page,
                                    apply_tooltip =  check_tooltip_1 || check_tooltip_2,
                                    tooltip_used = check_tooltip_1 ? field_data[field_id].tooltip_item_page : field_data[field_id].tooltip;
                            if(apply_tooltip){
                                $('tr.form-group-' + field_id + '>th').find('i').attr('data-original-title', tooltip_used);
                            }
                        }
                    }

                }, delay);                
            }
        }

    }
})
</script>