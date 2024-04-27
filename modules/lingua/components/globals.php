<?php
    ${ROOX_PLUGIN . "_" . $module_name . "_tables"} = [
        $module_name, 
        $module_name . '_fields', 
        $module_name . '_entities', 
        $module_name . '_entities_menu', 
        $module_name . '_entities_configuration', 
        $module_name . '_forms_tabs', 
        $module_name . '_global_lists_choices',
        $module_name . '_fields_choices',
        $module_name . '_dictionary'
    ];

    foreach (${ROOX_PLUGIN . "_" . $module_name . "_tables"} as $value) 
    {
        ${ROOX_PLUGIN . "_" . $value . "_table"} = ROOX_PLUGIN . "_" . $value;
    }
?>