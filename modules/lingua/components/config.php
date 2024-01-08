
<h5><u><b><?php echo TEXT_LANGUAGE_SWITCHER ?></b></u></h5>
<p>
<?php
    echo TEXT_LANGUAGE_SWITCHER_INFO;
?>
</p>
<?php
    echo form_tag('language_switch', url_for(ROOX_PLUGIN . '/lingua/')).input_hidden_tag('action','cfg_switcher');
?>
<div class="checkbox">
    <label>
        <input name="switcher" type="checkbox" value="1" id="cfg_switcher" <?php echo CFG_LANGUAGE_SWITCH ? "checked" : "" ?>> <?php echo TEXT_ACTIVE ?>
    </label>
</div>
</form>
<script>
    $('#cfg_switcher').on('change', function(){
        if(this.checked)
        {
            this.form.submit();
        } else {
            $.ajax({
                url:'<?php echo url_for("{$plugin_name}/{$module_name}/") ?>',
                method:'post',
                data:{switcher: 0, action: 'cfg_switcher'}
            });            
            $('#language_switcher').remove();
        }
        return;
        const isChecked = this.checked ? 1 : 0;
        $.ajax({
            url:'<?php echo url_for("{$plugin_name}/{$module_name}/") ?>',
            method:'post',
            data:{switcher: isChecked, action: 'cfg_switcher'}
        }).done(function(data){
            if(data=='reload'){
                location.reload();
            } else {
                $('#language_switcher').remove();
            }
        })
    })
</script>
<br>
<h5><u><b><?php echo ucwords(TEXT_ORPHAN_DATA) ?></b></u></h5>
<p>
<?php
    echo TEXT_CLEAR_ORPHANS_INFO;
    $orphanCount = $Locale->countOrphans();
?>
</p>
<p><?php echo TEXT_ORPHAN_DATA . ": <span id='orphan-count'>" . $orphanCount . "</span>"; ?></p>
<?php if($orphanCount):?>
<button type="button" id="clean_orphans" class="btn btn-primary"><?php echo TEXT_CLEAR_ORPHANS; ?></button>
<?php endif; ?>
<script>
    $("#clean_orphans").on('click', function(){
        $.ajax({
            url:'<?php echo url_for("{$plugin_name}/{$module_name}/") ?>',
            method:'post',
            data:{action: 'clean_orphans'}
        }).done(function(data){
            const orphanCount = parseInt(data);
            $('#orphan-count').text(orphanCount);
            if(!orphanCount)
            {
                roox.alert('<?php echo TEXT_ORPHAN_DATA_CLEARED;?>', 'success');
                $('#clean_orphans').remove();
            } else {
                roox.alert('<?php echo TEXT_ORPHAN_DATA_NOT_CLEARED;?>', 'danger');
            }
        });        
    })
</script>
<hr>
<?php    
    $update_url = url_for("{$plugin_name}/core/");
    $add_url = url_for("{$plugin_name}/core/form", "active={$module_name}");
    require component_path("{$plugin_name}/core/definitions_form");
?>