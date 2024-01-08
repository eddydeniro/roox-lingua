<?php
$loaded_module = $_GET['module'];
$current_entity_id = 0;
global ${ROOX_PLUGIN . '_language_cache'}, $app_user;
$goReplace = $app_user['language'] == CFG_APP_LANGUAGE ? 0 : 1;
if(in_array($loaded_module, ['items/items', 'items/info']))
{
    $path_array = explode('/', $app_path);
    $last_path_item = explode('-', $path_array[count($path_array) - 1]);
    $current_entity_id = $last_path_item[0];
}
if($loaded_module=='reports/view')
{
    $current_entity_id = ${ROOX_PLUGIN . '_language_cache'}['reports'][$_GET['reports_id']];
}
$language_data = json_encode(array_filter(${ROOX_PLUGIN . '_language_cache'}, function($key){
    return !in_array($key, ['language', 'date_updated', 'token']);
}, ARRAY_FILTER_USE_KEY));

?>
<script>
$(document).ready(function(){
    const _entity_id = <?php echo $current_entity_id ?>,
        language_data = <?php echo $language_data ?>,
        go_replace = <?php echo  $goReplace ?>;

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

        if(params.module=='items/items' || params.module=='items/info' || params.module=='reports/view'){
            $('title').text($('title').text().replace(entities_data[_entity_id]['listing_heading']['original'], entities_data[_entity_id]['listing_heading']['value']));

            if(params.module=='reports/view'){
                $('h3.page-title').text(entities_data[_entity_id]['listing_heading']['value']);
            }
            $('.entitly-listing-buttons-left > button').text(current_entity_data['insert_button']['value']);
            const field_data = language_data['fields'][_entity_id];
            const tabs_data = language_data['forms_tabs'][_entity_id];   
            $('#ajax-modal').on('shown', function(){
                $('h4.modal-title').text(entities_data[_entity_id]['window_heading']['value']);
                for (const field_id in field_data){
                    if(field_data[field_id].name){
                        $('.form-group-' + field_id + '> label').text(field_data[field_id].name);
                    }        
                }
                for (const tab_id in tabs_data){
                    if(tabs_data[tab_id].name){
                        $('.form_tab_' + tab_id + '> a').text(tabs_data[tab_id].name);
                    }        
                }                
            })              
            if(params.module=='items/items' || params.module=='reports/view'){
                roox.waitForElement('.table-scrollable.table-wrapper').then(function(){
                    for (const field_id in field_data){
                        if(field_data[field_id].name){
                            $('.field-' + field_id + '-th').html('<div>'+(field_data[field_id].short_name ? field_data[field_id].short_name : field_data[field_id].name)+'</div>');
                        }        
                    }
                });    
            }
            if(params.module=='items/info'){
                $('div.navbar-header > a.navbar-brand').text(entities_data[_entity_id]['window_heading']['value']);
                for (const field_id in field_data){
                    if(field_data[field_id].name){
                        $('tr.form-group-' + field_id + '>th').text(field_data[field_id].name);
                    }        
                }
                for (const tab_id in tabs_data){
                    if(tabs_data[tab_id].name){
                        $('.check-form-tabs[cfg_tab_id=info_box_tab_'+tab_id+'] > div > h4').text(tabs_data[tab_id].name);
                    }        
                }
            }
        }

    }
})
</script>