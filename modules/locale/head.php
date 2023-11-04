<?php 
global $app_session_token; 
$thisPlugin = ROOX_PLUGIN;
echo <<<JS
    <script>
        const sess_token = '{$app_session_token}',
            plugin_name = '{$thisPlugin}';
    </script>
    <script src="plugins/{$thisPlugin}/js/locale.js"></script>
JS;
?>
