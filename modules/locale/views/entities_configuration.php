<?php require('header.php') ?>

<?php $default_selector = array('1' => TEXT_YES, '0' => TEXT_NO); ?>

<?php echo form_tag('cfg', url_for('entities/entities_configuration', 'action=save&entities_id=' . $_GET['entities_id']), array('class' => 'form-horizontal')) ?>

<div class="tabbable tabbable-custom">

    <ul class="nav nav-tabs">
        <li class="active"><a href="#general_info"  data-toggle="tab"><?php echo TEXT_TITLES ?></a></li>
        <li><a href="#comments_configuration"  data-toggle="tab"><?php echo TEXT_COMMENTS_TITLE ?></a></li>   
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade active in" id="general_info">

            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_menu_title"><?php echo tooltip_icon(TEXT_MENU_TITLE_TOOLTIP) . TEXT_MENU_TITLE; ?></label>
                <div class="col-md-9">	
                    <?php echo input_tag('cfg[menu_title]', $cfg->get('menu_title'), array('class' => 'form-control input-large')); ?>       
                </div>			
            </div>

            <h3 class="form-section "><?php echo TEXT_WINDOW ?></h3>

            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_window_heading"><?php echo tooltip_icon(TEXT_WINDOW_HEADING_TOOLTIP) . TEXT_WINDOW_HEADING; ?></label>
                <div class="col-md-9">	
                    <?php echo input_tag('cfg[window_heading]', $cfg->get('window_heading'), array('class' => 'form-control input-large')); ?>       
                </div>			
            </div>
            
            <h3 class="form-section "><?php echo TEXT_NAV_LISTING_CONFIG ?></h3>
            
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_listing_heading"><?php echo tooltip_icon(TEXT_LISTING_HEADING_TOOLTIP) . TEXT_LISTING_HEADING; ?></label>
                <div class="col-md-9">	
                    <?php echo input_tag('cfg[listing_heading]', $cfg->get('listing_heading'), array('class' => 'form-control input-large')); ?>       
                </div>			
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_insert_button"><?php echo tooltip_icon(TEXT_INSERT_BUTTON_TITLE_TOOLTIP) . TEXT_INSERT_BUTTON_TITLE; ?></label>
                <div class="col-md-9">	
                    <?php echo input_tag('cfg[insert_button]', $cfg->get('insert_button'), array('class' => 'form-control input-large')); ?>       
                </div>			
            </div>

        </div>
        <div class="tab-pane fade" id="comments_configuration">
                
               <p class="form-section"><?= TEXT_HEADING ?></p>
                
                <div class="form-group" form_display_rules="cfg_comments_listing_type:table">
                    <label class="col-md-3 control-label" for="cfg_comments_listing_heading"><?php echo TEXT_LISTING_HEADING; ?></label>
                    <div class="col-md-9">	
                        <?php echo input_tag('cfg[comments_listing_heading]', $cfg->get('comments_listing_heading'), array('class' => 'form-control input-large','placeholder'=>TEXT_DEFAULT . ': ' . TEXT_COMMENTS)); ?>       
                    </div>			
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label" for="cfg_comments_insert_button"><?php echo  TEXT_INSERT_BUTTON_TITLE; ?></label>
                    <div class="col-md-9">	
                        <?php echo input_tag('cfg[comments_insert_button]', $cfg->get('comments_insert_button'), array('class' => 'form-control input-large','placeholder'=>TEXT_DEFAULT . ': ' . TEXT_BUTTON_ADD_COMMENT)); ?>       
                    </div>			
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label" for="cfg_comments_window_heading"><?php echo  TEXT_WINDOW_HEADING; ?></label>
                    <div class="col-md-9">	
                        <?php echo input_tag('cfg[comments_window_heading]', $cfg->get('comments_window_heading'), array('class' => 'form-control input-large','placeholder'=>TEXT_DEFAULT . ': ' . TEXT_COMMENT)); ?>       
                    </div>			
                </div>

            
            

        </div>

    </div>

</div>	  



<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>

</form>


<script>
    $(function ()
    {
        $('.tooltips').tooltip();
    });
</script>    



