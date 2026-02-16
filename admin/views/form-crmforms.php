<?php
/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
?>

<div class="mt30">
	<div class="panel" style="width:auto; margin-right: 20px;">
		<div class="panel-body">
<?php

require_once( SM_LB_PRO_DIR."includes/Functions.php" );
$OverallFunctionsPROObj = new OverallFunctionsPRO();
$page = $_REQUEST['page'];
$result = $OverallFunctionsPROObj->CheckFetchedDetails();
require_once( SM_LB_PRO_DIR."templates/thirdparty_mapping.php" );
if( !$result['status'] )
{
	$display_content = "<br>". $result['content']." to create Forms <br><br>";
	echo "<div style='font-weight:bold;  color:red; font-size:16px;text-align:center'> $display_content </div>";
}
else
{
	global $crmdetailsPRO;
	global $attrname;
	global $migrationmap;
	global $wpdb;
	global $lb_admin;
	require_once( SM_LB_PRO_DIR."includes/class_lb_manage_shortcodes.php" );
	$HelperObj = new WPCapture_includes_helper_PRO();
	$module = $HelperObj->Module;
	$moduleslug = $HelperObj->ModuleSlug;
	$activatedplugin = $HelperObj->ActivatedPlugin;
	$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
	$lb_admin->setActivatedPluginLabel($activatedpluginlabel);
	$plugin_url= SM_LB_PRO_DIR;
	$lb_admin->setPluginsUrl($plugin_url);
	$onAction= 'onCreate';
	$active_plugins = get_option( "active_plugins" );
	$siteurl= site_url();
	$crm_users = get_option("crm_users");
	$users_detail = array();
	$crmuserid = isset($crm_users[$activatedplugin]['id']) ? $crm_users[$activatedplugin]['id'] : '';

	if(is_array($crmuserid)){
		foreach( $crm_users[$activatedplugin]['id'] as $key => $value )
		{
			$users_detail[$value] = array( 'user_name' => $crm_users[$activatedplugin]['user_name'][$key] , 'first_name' => $crm_users[$activatedplugin]['first_name'][$key] , 'last_name' => $crm_users[$activatedplugin]['last_name'][$key]  );
		}

	}
	$crmdetailsPRO[$activatedplugin]['Label'] = str_replace('pro', '', $crmdetailsPRO[$activatedplugin]['Label']);

	$content1 = "";
	$content1 .= "<div class='leads-builder-heading col-md-12 mb20' style='display:flex;justify-content: center;align-items: center;'><h4>".__('Form Shortcodes Table' , "wp-leads-builder-any-crm-pro" )."</h4> ( {$crmdetailsPRO[$activatedplugin]['Label']} )  </div>
				 <div>
				 <table style='width:100%; max-width: 100%; margin-bottom:20px; border: 1px solid #dddddd;'>
				 <tr style='border-top: 1px solid #dddddd;'>
				 </tr>
				 <tr class='smack-crm-pro-highlight smack-crm-pro-alt' style='border-top: 1px solid #dddddd;'>
				 <th class='smack-crm-free-list-view-th' style='width: 300px;'>".__('Shortcode / Title' , 'wp-leads-builder-any-crm-pro' )."</th>
				 <th class='smack-crm-free-list-view-th' style='width: 200px;'>".__('Assignee' , 'wp-leads-builder-any-crm-pro' )."</th>
				 <th class='smack-crm-free-list-view-th' style='width: 200px;'>".__('Module' , 'wp-leads-builder-any-crm-pro' )."</th>
				 <th class='smack-crm-free-list-view-th' style='width: 200px;'>".__('Thirdparty' , 'wp-leads-builder-any-crm-pro' )."</th>			
				 <th class='smack-crm-free-list-view-th' style='width: 200px;'>".__('Actions' , 'wp-leads-builder-any-crm-pro' )."</th>
				 </tr>";

	//$shortcodemanager = $wpdb->get_results("select *from wp_smackleadbulider_shortcode_manager where crm_type = '{$activatedplugin}'");
	$shortcodemanager = $wpdb->get_results( 
		$wpdb->prepare("SELECT * FROM wp_smackleadbulider_shortcode_manager WHERE crm_type = %s", $activatedplugin) 
	);
	foreach($shortcodemanager as $shortcode_fields)
	{
		$content1 .= "<tr style='background-color:white'>";
		$shortcode_name = "[" . $shortcode_fields->crm_type . "-web-form name='" . $shortcode_fields->shortcode_name . "']";

		if( $shortcode_fields->assigned_to == "Round Robin" )
		{
			$assigned_to = "Round Robin";
		}
		else
		{
			$assigned_to = $users_detail[$shortcode_fields->assigned_to]['first_name']." ".$users_detail[$shortcode_fields->assigned_to]['last_name'];
		}
		$oldshortcodename = "";
		$oldshortcode_reveal_html = "";
		$oldshortcode_html = "";
		if( $shortcode_fields->old_shortcode_name != NULL )
		{
			$oldshortcodename = $shortcode_fields->old_shortcode_name;
			$oldshortcode_reveal_html = "<p><a style='cursor:pointer;' id='oldshortcodename_reveal{$shortcode_fields->shortcode_id}' onclick='jQuery(\"#oldshortcodename\"+{$shortcode_fields->shortcode_id}).show(); jQuery(\"#oldshortcodename_reveal\"+{$shortcode_fields->shortcode_id}).hide(); '> Click here to reveal old shortcode </a></p>";
			$oldshortcode_html = "<p style='display:none;' id='oldshortcodename{$shortcode_fields->shortcode_id}'> $oldshortcodename </p>";
		}

		$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>" . $shortcode_name . "$oldshortcode_reveal_html $oldshortcode_html</td>";
		$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>" . $assigned_to . "</td>";
		$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>" . $shortcode_fields->module . "</td>";
		$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> None </td>";
		$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align:center;'>";
		$content1 .= "<a href='#' onclick='create_form(\"Editshortcode\" , \"$shortcode_fields->module\" , \"$shortcode_fields->shortcode_name\",\"$activatedplugin\")'> <i class='icon-pencil2'></i> </a>";
		$content1 .= "<a href='#' style='margin-left:2px;' onclick='create_form(\"Deleteshortcode\" , \"$shortcode_fields->module\" , \"$shortcode_fields->shortcode_name\",\"$activatedplugin\")'>  <i class='icon-trash2'></i> </a>";
		$content1 .= "<a href='#' style='margin-left:2px;' onclick='formrecord_history(\"formrecord\" , \"$shortcode_fields->module\" , \"$shortcode_fields->shortcode_name\",\"$activatedplugin\")'>  <i class='icon-history'></a>";
		$content1 .= "</td>";
		$content1 .= "</tr>";
	}

	//Codes for getting Thirdparty existing forms
	$existing_content = '';
	if(in_array( "gravityforms/gravityforms.php" , $active_plugins)) {
		$save_gravity_form_id = array();
		$gravity_option_name = $activatedplugin."_wp_gravity";
		$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$gravity_option_name%" ) );
		if( !empty( $list_of_shortcodes ))
		{
			foreach( $list_of_shortcodes as $list_key => $list_val )
			{
				$shortcode_name = $list_val->option_name;
				$form_id = explode( $gravity_option_name , $shortcode_name );
				$save_gravity_form_id[] = $form_id[1];

			}
		}
		foreach( $save_gravity_form_id as $grav_val )
		{
			$post_value = $wpdb->get_results( $wpdb->prepare( "select * from {$wpdb->prefix}gf_form where is_trash='0' and id like %d" , "$grav_val%" ) );
			$get_config = get_option($gravity_option_name."".$grav_val);
			$exist_module = $get_config['third_module'];
			$exist_assignee = $get_config['thirdparty_assignedto_name'];
			$get_form_title = $wpdb->get_results( $wpdb->prepare( "select title from {$wpdb->prefix}gf_form where id=%d" , $grav_val ) );
			$gravity_form_title = $get_form_title[0]->title;
			$third_plugin = $get_config['third_plugin'];
			if(isset($get_config['tp_roundrobin'])) {
				$third_roundrobin = $get_config['tp_roundrobin'];
			} else {
				$third_roundrobin = "";
			}
			$gravityform_shortcode='[gravityform id="'.$grav_val.'"'.'  title="true"'.' description="false"]';

			if(!empty($post_value)){
				$existing_content .= "<tr>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $gravityform_shortcode</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>				
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> Gravity Form</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
				$existing_content .= "<a href='#' onclick='return show_map_config(\"$exist_module\" , \"$gravity_form_title\" , \"$grav_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i> </a>";
				$existing_content .= "<a href='#' onclick='return delete_map_config(\"$third_plugin\" , \"$grav_val\" );' style='margin-left:2px;'> <i class='icon-trash2'></i></a>";
				$existing_content .= "<a href='#' onclick='return DownloadJSON(\"$grav_val\");'style='margin-left:8px;'><i class='icon-cloud-download'></i></a></td>";
				$existing_content .="</tr>";
			}
			else{
				$option_name=$gravity_option_name."".$grav_val;
				delete_option($option_name);
			}
		}
	}

	//Mapped Elementor Forms List View
	if(in_array("elementor-pro/elementor-pro.php", $active_plugins)){
		$save_elementor_form_id = array();
		$elementor_option_name = $activatedplugin."_wp_elementor";
		$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$elementor_option_name%" ) );
		if( !empty( $list_of_shortcodes ))
		{
			foreach( $list_of_shortcodes as $list_key => $list_val )
			{
				$shortcode_name = $list_val->option_name;
				$form_id = explode( $elementor_option_name , $shortcode_name );
				$save_elementor_form_id[] = $form_id[1];
			}
		}


		foreach( $save_elementor_form_id as $ele_val )
		{
			$get_config = get_option($elementor_option_name."".$ele_val);
			$exist_module = $get_config['third_module'];
			$exist_assignee = $get_config['thirdparty_assignedto_name'];
			$get_form_title = $wpdb->get_results( $wpdb->prepare( "select post_title from {$wpdb->prefix}posts where id=%d" , $ele_val ) );
			$ninja_form_title = 'Anotherformee';
			$third_plugin = $get_config['third_plugin'];
			if(isset($get_config['tp_roundrobin'])) {
				$third_roundrobin = $get_config['tp_roundrobin'];
			} else {
				$third_roundrobin = "";
			}
			$elementor_form_shortcode='[elementor_form id='.$ele_val.']';
			$existing_content .= "<tr><td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $elementor_form_shortcode</td>
								 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>				
								 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>
								 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> Elementor Forms</td>
								 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
			$existing_content .= "<a href='#' onclick='return show_map_config(\"$exist_module\" , \"$ninja_form_title\" , \"$ele_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i></a>";
			$existing_content .= "<a href='#' onclick='return delete_map_config(\"$third_plugin\" , \"$ele_val\");' style='margin-left:2px;'> <i class='icon-trash2'></i> </a>";
			$existing_content .= "<a href='#' onclick='return DownloadJSON(\"$ele_val\");'style='margin-left:8px;'><i class='icon-cloud-download'></i></a></td>";
			$existing_content .= "</tr>";
		}
	}

	//NINJA MAPPED FIELDS
	if(in_array( "ninja-forms/ninja-forms.php" , $active_plugins)) {

		$save_ninja_form_id = array();
		$ninja_option_name = $activatedplugin."_wp_ninja";
		$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$ninja_option_name%" ) );
		if( !empty( $list_of_shortcodes ))
		{
			foreach( $list_of_shortcodes as $list_key => $list_val )
			{
				$shortcode_name = $list_val->option_name;
				$form_id = explode( $ninja_option_name , $shortcode_name );
				$save_ninja_form_id[] = $form_id[1];

			}
		}

		foreach( $save_ninja_form_id as $ninja_val )
		{
			$post_value = $wpdb->get_results( $wpdb->prepare( "select * from {$wpdb->prefix}nf3_forms where id like %d" , "$ninja_val%" ) );
			$get_config = get_option($ninja_option_name."".$ninja_val);
			$exist_module = $get_config['third_module'];
			$exist_assignee = $get_config['thirdparty_assignedto_name'];
			$get_form_title = $wpdb->get_results( $wpdb->prepare( "select title from {$wpdb->prefix}nf3_forms where id=%d" , $ninja_val ) );
			$ninja_form_title = $get_form_title[0]->title;
			$third_plugin = $get_config['third_plugin'];
			if(isset($get_config['tp_roundrobin'])) {
				$third_roundrobin = $get_config['tp_roundrobin'];
			} else {
				$third_roundrobin = "";
			}
			$ninja_form_shortcode='[ninja_form id='.$ninja_val.']';
			if(!empty($post_value)){
				$existing_content .= "<tr><td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $ninja_form_shortcode</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>				
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> Ninja Forms</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
				$existing_content .= "<a href='#' onclick='return show_map_config(\"$exist_module\" , \"$ninja_form_title\" , \"$ninja_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i></a>";
				$existing_content .= "<a href='#' onclick='return delete_map_config(\"$third_plugin\" , \"$ninja_val\");' style='margin-left:2px;'> <i class='icon-trash2'></i> </a>";
				$existing_content .= "<a href='#' onclick='return DownloadJSON(\"$ninja_val\");'style='margin-left:8px;'><i class='icon-cloud-download'></i></a></td>";
				$existing_content .= "</tr>";
			}
			else{
				$option_name=$ninja_option_name."".$ninja_val;
				delete_option($option_name);
			}
		}
	}

	//CONTACT FORM MAPPING
	if(in_array( "contact-form-7/wp-contact-form-7.php" , $active_plugins)) {
		$save_contact_form_id = array();
		$contact_option_name = $activatedplugin."_wp_contact";
		$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$contact_option_name%" ) );
		if( !empty( $list_of_shortcodes ))
		{
			foreach( $list_of_shortcodes as $list_key => $list_val )
			{
				$shortcode_name = $list_val->option_name;
				$form_id = explode( $contact_option_name , $shortcode_name );
				$save_contact_form_id[] = $form_id[1];

			}
		}

		foreach( $save_contact_form_id as $contact_val )
		{
			$post_value = $wpdb->get_results( $wpdb->prepare( "select * from {$wpdb->prefix}posts where id like %d" , "$contact_val%" ) );
			$get_config = get_option($contact_option_name."".$contact_val);
			$exist_module = $get_config['third_module'];
			$exist_assignee = $get_config['thirdparty_assignedto_name'];
			$get_form_title = $wpdb->get_results( $wpdb->prepare( "select post_title from $wpdb->posts where post_type=%s and ID=%d" , 'wpcf7_contact_form' , $contact_val ) );

			$contact_form_title = $get_form_title[0]->post_title;
			$third_plugin = $get_config['third_plugin'];
			if(isset($get_config['tp_roundrobin'])) {
				$third_roundrobin = $get_config['tp_roundrobin'];
			} else {
				$third_roundrobin = "";
			}
			$contact_form_shortcode='[contact-form-7 id="'.$contact_val.'"'.'  title="'.$contact_form_title.'"]';
			if(!empty($post_value)){
				$existing_content .= "<tr>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $contact_form_shortcode</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>			
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>	
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> Contact Form7</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
				$existing_content .= "<a href='#' onclick='show_map_config(\"$exist_module\" , \"$contact_form_title\" , \"$contact_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i> </a>";
				$existing_content .= "<a href='#' onclick='return delete_map_config(\"$third_plugin\" , \"$contact_val\" );' style='margin-left:2px;'> <i class='icon-trash2'></i> </a>";
				$existing_content .= "<a href='#' onclick='return DownloadJSON(\"$contact_val\");'style='margin-left:8px;'><i class='icon-cloud-download'></i></a></td>";
				$existing_content .= "</tr>";
			}
			else{
				$option_name=$contact_option_name."".$contact_val;
				delete_option( $option_name );
			}
		}
	}

	//WPFORM MAPPING
	if(in_array( "wpforms-lite/wpforms.php" , $active_plugins)) {
		$save_wpform_form_id = array();
		$wpform_option_name = $activatedplugin."_wp_wpform_lite";
		$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$wpform_option_name%" ) );
		if( !empty( $list_of_shortcodes ))
		{
			foreach( $list_of_shortcodes as $list_key => $list_val )
			{
				$shortcode_name = $list_val->option_name;
				$form_id = explode( $wpform_option_name , $shortcode_name );
				$save_wpform_form_id[] = $form_id[1];

			}
		}

		foreach( $save_wpform_form_id as $wpform_val )
		{
			$post_value = $wpdb->get_results( $wpdb->prepare( "select * from {$wpdb->prefix}posts where id like %d" , "$wpform_val%" ) );
			$get_config = get_option($wpform_option_name."".$wpform_val);
			$exist_module = $get_config['third_module'];
			$exist_assignee = $get_config['thirdparty_assignedto_name'];
			$get_form_title = $wpdb->get_results( $wpdb->prepare( "select post_title from $wpdb->posts where post_type=%s and ID=%d" , 'wpforms' , $wpform_val ) );
			$wpform_form_title = $get_form_title[0]->post_title;
			$third_plugin = $get_config['third_plugin'];
			if(isset($get_config['tp_roundrobin'])) {
				$third_roundrobin = $get_config['tp_roundrobin'];
			} else {
				$third_roundrobin = "";
			}
			$wpforms_shortcode='[wpforms id="'.$wpform_val.'"]';
			if(!empty($post_value)){
				$existing_content .= "<tr>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $wpforms_shortcode</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>			
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>	
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> WP Forms</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
				$existing_content .= "<a href='#' onclick='return show_map_config(\"$exist_module\" , \"$wpform_form_title\" , \"$wpform_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i> </a>";
				$existing_content .= "<a href='#' onclick='return delete_map_config(\"$third_plugin\" , \"$wpform_val\" );' style='margin-left:2px;'> <i class='icon-trash2'></i> </a>";
				$existing_content .= "<a href='#' onclick='return DownloadJSON(\"$wpform_val\");'style='margin-left:8px;'><i class='icon-cloud-download'></i></a></td>";
				$existing_content .= "</tr>";
			}
			else{
				$option_name=$wpform_option_name."".$wpform_val;
				delete_option( $option_name );
			}
		}
	}
	//WPFORMPRO MAPPING
	if(in_array( "wpforms/wpforms.php" , $active_plugins)) {
		$save_wpformpro_form_id = array();
		$wpformpro_option_name = $activatedplugin."_wp_wpform_pro";
		$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$wpformpro_option_name%" ) );
		if( !empty( $list_of_shortcodes ))
		{
			foreach( $list_of_shortcodes as $list_key => $list_val )
			{
				$shortcode_name = $list_val->option_name;
				$form_id = explode( $wpformpro_option_name , $shortcode_name );
				$save_wpformpro_form_id[] = $form_id[1];

			}
		}

		foreach( $save_wpformpro_form_id as $wpformpro_val )
		{
			$post_value = $wpdb->get_results( $wpdb->prepare( "select * from {$wpdb->prefix}posts where id like %d" , "$wpformpro_val%" ) );
			$get_config = get_option($wpformpro_option_name."".$wpformpro_val);
			$exist_module = $get_config['third_module'];
			$exist_assignee = $get_config['thirdparty_assignedto_name'];
			$get_form_title = $wpdb->get_results( $wpdb->prepare( "select post_title from $wpdb->posts where post_type=%s and ID=%d" , 'wpforms' , $wpformpro_val ) );
			$wpformpro_form_title = $get_form_title[0]->post_title;
			$third_plugin = $get_config['third_plugin'];
			if(isset($get_config['tp_roundrobin'])) {
				$third_roundrobin = $get_config['tp_roundrobin'];
			} else {
				$third_roundrobin = "";
			}
			$wpformpro_shortcode='[wpforms id="'.$wpformpro_val.'"]';
			if(!empty($post_value)){
				$existing_content .= "<tr>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $wpformpro_shortcode</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>			
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>	
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> WP Forms Pro</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
				$existing_content .= "<a href='#' onclick='return show_map_config(\"$exist_module\" , \"$wpformpro_form_title\" , \"$wpformpro_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i> </a>";
				$existing_content .= "<a href='#' onclick='return delete_map_config(\"$third_plugin\" , \"$wpformpro_val\" );' style='margin-left:2px;'> <i class='icon-trash2'></i> </a>";
				$existing_content .= "<a href='#' onclick='return DownloadJSON(\"$wpformpro_val\");'style='margin-left:8px;'><i class='icon-cloud-download'></i></a></td>";
				$existing_content .= "</tr>";
			}
			else{
				$option_name=$wpformpro_option_name."".$wpformpro_val;
				delete_option( $option_name );
			}
		}
	}
	//CALDERA MAPPED FIELDS
	if(in_array( "caldera-forms/caldera-core.php" , $active_plugins)) {
		$save_caldera_form_id = array();
		$caldera_option_name = $activatedplugin."_wp_caldera";
		$list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$caldera_option_name%" ) );
		if( !empty( $list_of_shortcodes ))
		{
			foreach( $list_of_shortcodes as $list_key => $list_val )
			{
				$shortcode_name = $list_val->option_name;
				$form_id = explode( $caldera_option_name , $shortcode_name );
				$save_caldera_form_id[] = $form_id[1];
			}
		}

		foreach( $save_caldera_form_id as $caldera_val )
		{  
			$cal_value=$save_caldera_form_id[0];
			$post_value = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cf_forms where form_id ='$cal_value'",ARRAY_A) ;
			$get_config = get_option($caldera_option_name."".$caldera_val);
			$exist_module = $get_config['third_module'];
			$exist_assignee = $get_config['thirdparty_assignedto_name'];
			$get_form_config = $wpdb->get_results( 
				$wpdb->prepare(
					"SELECT config FROM {$wpdb->prefix}cf_forms WHERE form_id = %s AND type = %s", 
					$caldera_val, 
					'primary'
				), 
				ARRAY_A
			);
			$caldera_forms_config = $get_form_config[0]['config'];
			$caldera_form_config = unserialize($caldera_forms_config);
			$caldera_form_title = $caldera_form_config['name'];
			$third_plugin = $get_config['third_plugin'];
			if(isset($get_config['tp_roundrobin'])) {
				$third_roundrobin = $get_config['tp_roundrobin'];
			} else {
				$third_roundrobin = "";
			}
			$caldera_form_shortcode='[caldera_form id="'.$cal_value.'"]';
			if(!empty($post_value)){
				$existing_content .= "<tr><td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $caldera_form_shortcode</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>				
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> Caldera Forms</td>
									 <td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
				$existing_content .= "<a href='#' onclick='return show_map_config(\"$exist_module\" , \"$caldera_form_title\" , \"$caldera_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i></a>";
				$existing_content .= "<a href='#' onclick='return delete_map_config(\"$third_plugin\" , \"$caldera_val\");' style='margin-left:2px;'> <i class='icon-trash2'></i> </a>";
				$existing_content .= "<a href='#' onclick='return DownloadJSON(\"$cal_value\");'style='margin-left:8px;'><i class='icon-cloud-download'></i></a></td>";
				$existing_content .= "</tr>";
			}
			else{
				$option_name=$caldera_option_name."".$caldera_val;
				delete_option( $option_name );
			}
		}
	}
	$content1 .= $existing_content;	
	$content1 .= "</table></div>";
	echo $content1;
	?>
			<div class="col-md-12 text-center" style="display:flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
				<div class="">
					<?php
					$lead_label = 'Create Lead Form';
					$lead_module = 'Leads';
					if($activatedplugin == 'wpzohobiginpro') {
						$lead_label = 'Create Deal Form';
						$lead_module = 'Leads';
					}
					if($result['leadsynced']){
					?>
					<input class="crm_form_button" type="submit"
						value="<?php echo esc_attr__($lead_label , "wp-leads-builder-any-crm-pro" ); ?>"
						onclick="create_form('createshortcode','<?php echo $lead_module; ?>','','')" id="generateleadsshortcode" />
					<?php
					}else{
					?>
					<input class="crm_form_button" type="submit"
						value="<?php echo esc_attr__($lead_label , "wp-leads-builder-any-crm-pro" ); ?>"
						onclick="create_form('createshortcode','<?php echo $lead_module; ?>','','')" id="generateleadsshortcode" disabled />
					<?php	
					}
					?>
				</div>
				<div class="">
					<?php
					$contact_label = 'Create Contact Form';
					$contact_module = 'Contacts';
					if($activatedplugin == 'wpzohobiginpro') {
						$contact_label = 'Create Contact Form';
						$contact_module = 'Contacts';
					}
					if($result['contactsynced']){
					?>
					<input class="crm_form_button" type="submit"
						value="<?php echo esc_attr__($contact_label , "wp-leads-builder-any-crm-pro" ); ?>"
						onclick="create_form('createshortcode','<?php echo $contact_module; ?>','','')" id="generatecontactsshortcode" />
					<?php
					}else{
					?>
					<input class="crm_form_button" type="submit"
						value="<?php echo esc_attr__($contact_label , "wp-leads-builder-any-crm-pro" ); ?>"
						onclick="create_form('createshortcode','<?php echo $contact_module; ?>','','')" id="generatecontactsshortcode"
						disabled />
					<?php	
					}
					?>
				</div>

				<div class="">
					<input class="crm_form_button" type="button" id="thirdparty_map"
						value="<?php echo esc_attr__('Use Existing Form' , "wp-leads-builder-any-crm-pro" ); ?>" />
				</div>
				<div class="">
					<input class="crm_form_button" type="button" id="import_file"
						value="<?php echo esc_attr__('Import For Json File' , "wp-leads-builder-any-crm-pro" ); ?>" />
				</div>
			</div>

			<head>
				<meta charset="utf-8">
			</head>

			<body>
				<div class="container" style="width:100%;">
					<!-- Trigger the modal with a button -->
					<!-- Modal -->
					<div class="modal fade" id="mapping-modalbox" role="dialog">
						<div class="modal-dialog">
							<!-- Modal content-->
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title" style="margin-left:28%;color:#1caf9a;">Configure an existing Form</h4>
									<button type="button" class="close" id="close_map_modal" data-dismiss="modal"
										onclick="remove_map_contents();">&times;</button>
								</div>
								<div class="modal-body">
									<div id="clear_contents" class="col-md-offset-1">
										<p id='show_form_list'>
											<?php
				$mapping_ui_fields = new thirdparty_mapping();
				echo $mapping_ui_fields->get_mapping_config();
				?>
										</p>
									</div>
									<p id="display_form_lists" class='col-md-offset-1'>
									</p>
									<p id="mapping_options" class='col-md-offset-1'>
									</p>
									<form name='mapping_fields' id='mapping_fields' onsubmit="return false;">
										<div id="CRM_field_mapping" class='col-md-offset-1'>
										</div>
										<div class="modal-footer col-md-12 form-group" style='margin-top:100px;'>
											<button type="button" id="close"
												class=" pull-left"
												style='margin-right:50%' data-dismiss="modal"
												onclick="remove_map_contents();">Close</button>
											<input type="button"
												class="pull-right"
												name="map_crm_fields" value="Configure" id="map_fields"
												onclick="map_thirdparty_crm_fields();">
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="loading-image"
					style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm-pro/assets/images/ajax-loaders.gif) no-repeat center">
					<?php echo esc_html__('' , "wp-leads-builder-any-crm-pro"  ); ?> </div>
				<div class="container" style="width:50%;">
					<!-- Trigger the modal with a button -->
					<!-- Modal -->
					<div class="modal fade" id="import-modalbox" role="dialog">
						<div class="modal-dialog">
							<!-- Modal content-->
							<div class="modal-content" style='height:280px;width:330px;margin-left:30%;'>
								<div class="modal-header">
									<h4 class="modal-title" style="text-align:center;color:green;">Import For Json File
									</h4>
									<button type="button" class="close" id="close_map_modal" data-dismiss="modal"
										onclick="remove_map_contents();">&times;</button>
								</div>
								<div class="modal-body" style='padding:0;'>
									<div id="clear_contents" class="col-md-offset-1">
										<p id='show_form_list'>
											<div class="panel"
												style="width: 80%;margin-top:0;margin-bottom:0;background-color:white"
												id="all_addons_view">
												<div class="panel-body">
													<!-- <input type="file" id="myfile" name="myfile"  ><br><br>							 -->
													<input type="file" id="actual-btn" style='display:none;' />
													<!-- our custom upload button -->
													<label id="actual_btn" style='width:54%;margin-left:30%'
														for="actual-btn">Choose File</label>
													<!-- name of file chosen -->
													<span id="file-chosen" style='margin-left:30%'>No file chosen</span>
												</div>
											</div>
										</p>
									</div>
									<p id="display_form_lists" class='col-md-offset-1'>
									</p>
									<p id="mapping_options" class='col-md-offset-1'>
									</p>
									<form name='mapping_fields' id='mapping_fields' onsubmit="return false;">
										<?php wp_nonce_field('sm-leads-builder'); ?>
										<div class="modal-footer col-md-12 form-group" style='margin-top:5px;'>
											<button type="button" id="close"
												class="smack-btn btn-default btn-radius pull-left"
												style='margin-right:30%' data-dismiss="modal"
												onclick="remove_map_contents();">Close</button>
											<input type="button" id="myfile-button"
												value="<?php echo esc_attr__('Import' , 'wp-leads-builder-any-crm-pro' );?>"
												onclick="upload_function();"
												class="smack-btn smack-btn-primary btn-radius" />
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<script>
					jQuery(document).ready(function () {
						jQuery(".mapping-modalbox").hide();
						jQuery("#thirdparty_map").click(function () {
							//	alert("arshu");
							jQuery("#mapping-modalbox").show();
							jQuery("#clear_contents").show();
						});
					});
					jQuery(document).ready(function () {
						jQuery(".import-modalbox").hide();
						document.getElementById("myfile-button").disabled = true;
						jQuery("#import_file").click(function () {
							jQuery("#import-modalbox").show();
							jQuery("#clear_contents").show();
						});
					});

					const actualBtn = document.getElementById('actual-btn');
					const fileChosen = document.getElementById('file-chosen');
					actualBtn.addEventListener('change', function () {
						fileChosen.textContent = this.files[0].name
					})
					$(function () {
						var tmppath = "";
						var value;
						$('#actual-btn').change(function (event) {

							var tmp = event.target.files[0];
							var filename = event.target.files[0]['name'];
							var exten = filename.split('.');
							var extension = exten[1];
							if (extension == 'json') {
								document.getElementById("myfile-button").disabled = false;
								if (tmp) {
									// create reader
									var reader = new FileReader();
									reader.readAsText(tmp);
									reader.onload = function (e) {
										// browser completed reading file - display it
										value = e.target.result;
										jQuery.ajax({
											type: 'POST',
											url: ajaxurl,
											data: {
												action: 'import_file',
												'value': value,
												'filename': filename,
											}
										});
									};
								}
							} else {
								swal('Error!', 'Unsupported File', 'success');
								document.getElementById("myfile-button").disabled = true;
							}
						});
					});
				</script>
				<script type="text/javascript">
					jQuery.browser = {};
					(function () {
						jQuery.browser.msie = false;
						jQuery.browser.version = 0;
						if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
							jQuery.browser.msie = true;
							jQuery.browser.version = RegExp.$1;
						}
					})();
				</script>
			</body>
			<?php
}
?>
		</div>
	</div>
</div>
<style>
	#actual_btn {
		background-color: #1caf9a;
		;
		color: white;
		padding: 0.7rem;
		font-family: sans-serif;
		border-radius: 50px;
		width: 600px;
		cursor: pointer;
		margin-top: 1rem;
		margin-left: 50%;
	}

	#file-chosen {
		padding: 0.7rem;
		font-family: sans-serif;
		border-radius: 50px;
		display: block ruby;
		cursor: pointer;
		margin-top: 1rem;
		margin-left: 50%;
	}
</style>