<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

    $siteurl = site_url();
    $siteurl = esc_url( $siteurl );
    $chkUpgrade = get_option('smack_wp_sugar_lead_fields');
    $active_plugin = get_option('WpLeadBuilderProActivatedPlugin');
    $config = get_option("wp_{$active_plugin}_settings");
    $app_url = isset($config['url']) ? esc_url($config['url']) : '';
    $user = isset($config['username']) ? sanitize_text_field($config['username']) : '';
    $pass = isset($config['password']) ? sanitize_text_field($config['password']) : '';
    if($active_plugin == 'wpsugarpro' ){
        $LB_setting_name = "Sugar CRM Settings";
    }elseif($active_plugin == 'wpsuitepro'){
        $LB_setting_name = "Suite CRM Settings";
    }
    else{
        $LB_setting_name = "Joforce CRM Settings";
    }

    if( $config == "" )
    {
        $config_data = 'no';
    }
    else
    {
        $config_data = 'yes';
    }
    ?>
        
<div class="clearfix"></div>
<div class="">
    <div class="panel"id="panel" style="width:98%;">
        <div class="panel-body">
            <div class="col-md-12">
				<div class="col-md-6">
                    <?php if( $active_plugin == 'wpsugarpro'){?>
                    <img src="<?php echo SM_LB_DIR?>assets/images/sugarcrm-logo.png" width=168 height=42>
                    <?php }else { ?>
                    <img src="<?php echo SM_LB_DIR?>assets/images/suite-logo.png" width=168 height=25>
                    <?php } ?>
                    <input type="hidden" id="get_config" value="<?php echo $config_data ?>">
                    <input type="hidden" id="revert_old_crm_pro" value="wpsugarpro">
                    <span id="save_config" style="font:14px;width:200px;">
                    </span>
                </div>
                <div class="col-md-6">
                    <div class="col-md-6" id="crm_select_dropdown">
                        <label id="inneroptions" class="leads-builder-crm">
                            <?php echo esc_html__('Select your CRM', "wp-leads-builder-any-crm-pro" ); ?></label>
                    </div>
                    <div class="col-md-5" style="margin-left: 10px;">
                        <?php $ContactFormPluginsObj = new ContactFormPROPlugins();echo $ContactFormPluginsObj->getPluginActivationHtml();
                        ?>
                    </div>
                </div>
            </d iv>
            <form id="smack-sugar-settings-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
                <input type="hidden" name="smack-sugar-settings-form" value="smack-sugar-settings-form" />
                <input type="hidden" id="plug_URL" value="<?php echo esc_attr(SM_LB_URL);?>" />
                <!-- <div class="form-group">
                <div class="col-md-3 col-md-offset-3">
                    <label id="inneroptions" class="leads-builder-heading"><?php echo esc_html__('Select your CRM', "wp-leads-builder-any-crm-pro" ); ?></label>
                    </div>
                    <div class="col-md-3">
                <?php $ContactFormPluginsObj = new ContactFormPROPlugins();echo $ContactFormPluginsObj->getPluginActivationHtml();
                ?>
                    </div>
                </div> -->
                <div class="clearfix"></div>
                <hr>
                <div class="mt30">
                    <div class="form-group col-md-12 ml15">
                        <label id="inneroptions" class="leads-builder-heading"
                            style="margin-left:0"><?php echo $LB_setting_name; ?>
                        </label>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="mt20">
                    <div class="form-group col-md-12">
                        <div class="col-md-2 label-space">
                            <label id="innertext" class="leads-builder-label">
                                <?php echo esc_html__('CRM URL' , "wp-leads-builder-any-crm-pro" ); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <input type='text' style="border-radius: 7px" class='smack-vtiger-settings form-control' name='url'
                                id='smack_sugar_host_address' value="<?php echo $app_url ?>" />
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="col-md-2 label-space">
                            <label id="innertext" class="leads-builder-label">
                                <?php echo esc_html__('Username' , "wp-leads-builder-any-crm-pro" ); ?> </label>
                        </div>
                        <div class="col-md-3">
                            <input type='text' style="border-radius: 7px" class='smack-vtiger-settings form-control' name='username'
                                id='smack_host_username' value="<?php echo $user ?>" />
                        </div>
                        <div class="col-md-2 label-space">
                            <label id="innertext" class="leads-builder-label">
                                <?php echo esc_html__('Password' , "wp-leads-builder-any-crm-pro" ); ?> </label>
                        </div>
                        <div class="col-md-3">
                            <input type='password' style="border-radius: 7px" class='smack-vtiger-settings form-control' name='password'
                                id='smack_host_access_key' value="<?php echo $pass ?>" />
                        </div>
                    </div>
                </div>
                <input type="hidden" name="posted" value="<?php echo 'posted';?>">
                <input type="hidden" id="site_url" name="site_url" value="<?php echo esc_attr($siteurl) ;?>">
                <input type="hidden" id="active_plugin" name="active_plugin"
                    value="<?php echo esc_attr($active_plugin); ?>">
                <input type="hidden" id="leads_fields_tmp" name="leads_fields_tmp"
                    value="smack_wpsugarpro_leads_fields-tmp">
                <input type="hidden" id="contact_fields_tmp" name="contact_fields_tmp"
                    value="smack_wpsugarpro_contacts_fields-tmp">
                <div class="col-md-offset-8">
                    <span id="SaveCRMConfig">
                        <input type="button"
                            value="<?php echo esc_attr__('Save CRM Configuration' , "wp-leads-builder-any-crm-pro" );?>"
                            id="save" class="save_config_button"
                            onclick="saveCRMConfiguration(this.id);" />
                    </span>
                </div>
                <!-- </div> wp-common-crm-content -->
            </form>
            <div id="loading-sync"
                style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm-pro/assets/images/ajax-loaders.gif) no-repeat center">
                <?php echo esc_html__('' , 'wp-leads-builder-any-crm-pro' ); ?></div>
            <div id="loading-image"
                style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm-pro/assets/images/ajax-loaders.gif) no-repeat center">
                <?php echo esc_html__('' , "wp-leads-builder-any-crm-pro" );?></div>
        </div>
    </div>
</div>