<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

/*

Cases : 
1) CreateNewFieldShortcode		Will create new field shortcode
2) FetchCrmFields			Will Fetch crm fields from the the crm
3) FieldSwitch				Enable/Disable single field
4) DuplicateSwitch			Change Duplicate handling settings 
5) MoveFields				Change the order of the fields
6) MandatorySwitch			Make Mandatory or Remove Mandatory
7) SaveDisplayLabel			Save Display Label
8) SwitchMultipleFields			Enable/Disable multiple fields
9) SwitchWidget				Enable/Disable widget  form
10) SaveAssignedTo			Save Assignee of the form leads 
11) CaptureAllWpUsers			Capture All wp users
 */

class OverallFunctionsPRO {

	public function CheckFetchedDetails()
	{
		$HelperObj = new WPCapture_includes_helper_PRO();
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$shortcodeObj = new CaptureData();
		$leadsynced = $shortcodeObj->selectFieldManager( $activatedplugin , 'Leads' );
		$contactsynced = $shortcodeObj->selectFieldManager( $activatedplugin , 'Contacts' );
		$content = "";
		$flag = true;
		
		$return_array = array( 'content' => "$content" , 'status' => $flag , 'leadsynced' => $leadsynced , 'contactsynced' => $contactsynced);

		return $return_array;
	}

	function getRoundRobinOwner( $assignedto_old )
	{
		$crm_users_list = get_option( 'crm_users' );
		$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$RR_users_list = $crm_users_list[$activated_crm];
		$RR_users_id = $RR_users_list['id'];

		foreach( $RR_users_id as $RR_key => $RR_val )
		{
			$i = $RR_key;
			if( $assignedto_old == $RR_val )
			{
				if( isset( $RR_users_id[$i+1] ))
				{
					$assignedto_new = $RR_users_id[$i+1];
				}
				else
				{
					$assignedto_new = $RR_users_id[0];
				}
			}

			$i++;
		}
		return $assignedto_new;

	}


	public function CreateNewFieldShortcode( $crmtype , $module ){

		$moduleslug = rtrim( strtolower($module) , "s");
		$tmp_option = "smack_{$crmtype}_{$moduleslug}_fields-tmp";
		if(!function_exists("generateRandomStringActivate"))
		{
			function generateRandomString($length = 10) {
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$randomString = '';
				for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, strlen($characters) - 1)];
				}
				return $randomString;
			}
		}
		$list_of_shorcodes = Array();
		$shortcode_present_flag = "No";

		$options = "smack_fields_shortcodes";
		$config_contact_shortcodes = get_option($options);
		if(is_array($config_contact_shortcodes))
		{
			foreach($config_contact_shortcodes as $shortcode => $values)
			{
				$list_of_shorcodes[] = $shortcode;
			}
		}
		$config_wpform_shortcodes = get_option($options);
		if(is_array($config_wpform_shortcodes))
		{
			foreach($config_wpform_shortcodes as $shortcode => $values)
			{
				$list_of_shorcodes[] = $shortcode;
			}
		}

		for($notpresent = "no" ; $notpresent == "no"; )
		{
			$random_string = generateRandomString(5);
			if(in_array($random_string, $list_of_shorcodes))
			{
				$shortcode_present_flag = 'Yes';
			}
			if($shortcode_present_flag != 'yes')
			{
				$notpresent = 'yes';
			}
		}
		$options = $tmp_option;
		return $random_string;
	}

	public static function doFieldAjaxAction()
	{
		$error=[];
		$crmtype = isset($_REQUEST['crmtype']) ? sanitize_text_field($_REQUEST['crmtype']) : "";
		$module = isset($_REQUEST['module']) ? sanitize_text_field($_REQUEST['module']) : "";
		$options = sanitize_text_field($_REQUEST['option']);
		$onAction = sanitize_text_field($_REQUEST['onAction']);
		$HelperObj = new WPCapture_includes_helper_PRO();
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		// $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$content = '';
		$FunctionsObj = new PROFunctions();
		// $tmp_option = "smack_{$activatedplugin}_{$moduleslug}_fields-tmp";
		if($onAction == 'onEditShortCode');
		{
			$original_options = "smack_{$activatedplugin}_fields_shortcodes";
			$original_config_fields = get_option($original_options);
		}
		$SettingsConfig = get_option("wp_{$activatedplugin}_settings");
		if($onAction == 'onCreate')
		{
			$config_fields = get_option($options);
		}
		else
		{
			$config_fields = get_option($options);
		}
		if(isset($config_fields['fields']))
		{
			$FieldCount =count($config_fields['fields']);
		}

		if(isset($config_fields)){
			$error[0] = 'no fields';
		}
		switch($_REQUEST['doaction'])
		{
		case "GetAssignedToUser":
			$Functions = new PROFunctions();
			echo $Functions->getUsersListHtml();
			break;
		case "CheckformExits":
			include(SM_LB_PRO_DIR.'includes/class_lb_manage_shortcodes.php');
			$fields = new ManageShortcodesActions();
			$all_fields = $fields->ManageFields($_REQUEST['shortcode'],$_REQUEST['crmtype'],$_REQUEST['module'],$_REQUEST['bulkaction'],$_REQUEST['chkarray'],$_REQUEST['labelarray'],$_REQUEST['orderarray']);
			$moduleslug = rtrim( strtolower($module) , "s");
			$config_fields = get_option( "smack_{$crmtype}_{$moduleslug}_fields-tmp" );
			if( !isset($config_fields['fields'][0]) )
				die( "Not synced" );
			else
				die( "Synced" );
			break;
		case "GetTemporaryFields":
			$moduleslug = rtrim( strtolower($module) , "s");
			$config_fields = get_option( "smack_{$crmtype}_{$moduleslug}_fields-tmp" );
			if($options != 'getSelectedModuleFields')
			{
				include(SM_LB_PRO_DIR.'templates/crm-fields-form.php');
			}
			break;

		case "FetchCrmFields":
			$moduleslug = rtrim( strtolower($module) , "s");
			$config_fields = $FunctionsObj->getCrmFields( $module );
			$seq = 1;
			$field_details = $current_fields = $existing_fields = array();
			if(isset($config_fields['fields'])){
				foreach($config_fields['fields'] as $fkey => $fval) {
					$field_details['name'] = $fval['name'];
					$field_details['label'] = $fval['label'];
					$field_details['type'] = isset($fval['type']['name']) ? $fval['type']['name'] : "";
					$field_details['field_values'] = null;
					if(! empty( $fval['type']['picklistValues'] ) ) {
						$field_details['field_values'] = serialize($fval['type']['picklistValues']);
					}
					$field_details['module'] = $module;
					if( isset($fval['mandatory']) && $fval['mandatory'] == 2 )
						$field_details['mandatory'] = 1;
					else
						$field_details['mandatory'] = 0;
					$field_details['crmtype'] = $crmtype;
					$field_details['sequence'] = $seq;
					$field_details['base_model'] = null;
					if(isset($fval['base_model']))
						$field_details['base_model'] = $fval['base_model'];
					$seq++;

					if($field_details['label']=='Date of Birth')
					{
						$field_details['type']='date';
					}
					$DataObj = new CaptureData();
					$DataObj->fieldManager( $field_details , $module );
					$DataObj->updateShortcodeFields( $field_details , $module );
					$current_fields[] = $field_details['name'];
				}
			}


			if($options != 'getSelectedModuleFields')
			{
				include(SM_LB_PRO_DIR.'templates/display-log.php');
			}

			global $wpdb;
			$get_existing_fields = $wpdb->get_results( $wpdb->prepare("select field_name from wp_smackleadbulider_field_manager where module_type =%s and crm_type =%s" , $module , $crmtype) );
			foreach($get_existing_fields as $ex_key => $ex_val){
				$existing_fields[] = $ex_val->field_name;
			}

			if(!empty($existing_fields))
			{
				$check_deleted_fields = array();
				$check_deleted_fields = array_diff($existing_fields , $current_fields);
				if(!empty($check_deleted_fields))
				{
					//Delete fields from table
					$DataObj = new CaptureData();
					$DataObj->DeleteFields( $crmtype , $module , $check_deleted_fields );	
				}
			}

			//Update Current Fields
			$options = "smack_{$crmtype}_{$moduleslug}_fields-tmp";
			update_option($options, $config_fields);
			$options = "smack_fields_shortcodes";
			$edit_config_fields = get_option($options);
			$edit_config_fields[sanitize_text_field($_REQUEST['shortcode'])] = $config_fields;
			update_option($options, $edit_config_fields);
			break;
		case "FetchAssignedUsers":
			$HelperObj = new WPCapture_includes_helper_PRO();
			$module = $HelperObj->Module;
			$moduleslug = $HelperObj->ModuleSlug;
			$activatedplugin = $HelperObj->ActivatedPlugin;
			// $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
			$FunctionsObj = new PROFunctions();
			$crmusers = get_option( 'crm_users' );
			$users = $FunctionsObj->getUsersList();
			$crmusers[$activatedplugin] = $users;
			update_option('crm_users', $crmusers);
			$content .='<h5>Assigned Users:</h5>';
			$firstname = '';
			if(is_array($users)){
				foreach($users['first_name'] as $assignusers)
				{
					$firstname .= $assignusers."<br>";
				}

			}
			echo $content;
			echo $firstname;die;
			break;
		default:
			break;
		}
	}

	public function update_formtitle( $shortcode , $tp_title , $tp_formtype ) 
	{
		global $wpdb;
		switch( $tp_formtype )	
		{
		case 'ninjaform':
			$get_checkid = $wpdb->get_results("select thirdpartyid from wp_smackformrelation where  shortcode='{$shortcode}' and thirdparty='ninjaform'");
			if(isset($get_checkid[0])) {
				$checkid = $get_checkid[0]->thirdpartyid;
			} else {
				$checkid = '';
			}
			if(!empty( $checkid ))
			{
				$wpdb->update( $wpdb->prefix ."nf_objectmeta" , array( 'meta_value' => $tp_title ) , array( 'meta_key' => 'form_title' , 'object_id' => $checkid ) );
				$ninja_option_name = "_transient_nf_form_".$checkid;
				$ninja_array = get_option( $ninja_option_name );
				$get_ninja_settings = $ninja_array->settings ;
				$get_ninja_settings['form_title'] = $tp_title ;
				$ninja_array->settings =  $get_ninja_settings ;
				update_option( $ninja_option_name , $ninja_array  );

			}			
			break;

		case 'contactform':
			$get_checkid = $wpdb->get_results("select thirdpartyid from wp_smackformrelation where  shortcode='{$shortcode}' and thirdparty='contactform'");
			if(isset($get_checkid[0])) {
				$checkid = $get_checkid[0]->thirdpartyid;
			} else {
				$checkid = "";
			}
			if( !empty( $checkid ))
			{	
				$wpdb->update( $wpdb->posts , array('post_title' => $tp_title ) , array( 'ID' => $checkid ) );	
			}

			break;

		case 'wpform':
			$get_checkid = $wpdb->get_results("select thirdpartyid from wp_smackformrelation where  shortcode='{$shortcode}' and thirdparty='wpform'");
			if(isset($get_checkid[0])) {
				$checkid = $get_checkid[0]->thirdpartyid;
			} else {
				$checkid = "";
			}
			if( !empty( $checkid ))
			{	
				$wpdb->update( $wpdb->posts , array('post_title' => $tp_title ) , array( 'ID' => $checkid ) );	
			}

			break;

		case 'wpformpro':
			$get_checkid = $wpdb->get_results("select thirdpartyid from wp_smackformrelation where  shortcode='{$shortcode}' and thirdparty='wpformpro'");
			if(isset($get_checkid[0])) {
				$checkid = $get_checkid[0]->thirdpartyid;
			} else {
				$checkid = "";
			}
			if( !empty( $checkid ))
			{	
				$wpdb->update( $wpdb->posts , array('post_title' => $tp_title ) , array( 'ID' => $checkid ) );	
			}

			break;

		case 'gravityform':
			$get_checkid = $wpdb->get_results("select thirdpartyid from wp_smackformrelation where  shortcode='{$shortcode}' and thirdparty='gravityform'");
			$checkid = $get_checkid[0]->thirdpartyid;
			if(!empty( $checkid ))                  
			{
				$wpdb->update( $wpdb->prefix ."gf_form" , array( 'title' => $tp_title ) , array( 'id' => $checkid ) );
				$get_grav_arr = $wpdb->get_results( $wpdb->prepare( "select display_meta from {$wpdb->prefix}gf_form_meta where form_id=%d" , $checkid ),ARRAY_A );	
				$grav_disp_meta = (array) json_decode($get_grav_arr[0]['display_meta']);
				$grav_disp_meta['title'] = $tp_title;
				$grav_disp_meta = json_encode( $grav_disp_meta );
				$wpdb->update( $wpdb->prefix."gf_form_meta" , array( 'display_meta' => $grav_disp_meta ) , array('form_id' => $checkid ) );	
			}


			break;
		}
		return;
	}
	public function doNoFieldAjaxAction()
	{
		global $wpdb;
		$shortcodedata=[];
		$user_sync_array=[];
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = $HelperObj->Module;
		// $moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		// $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$SettingsConfig = get_option("wp_{$activatedplugin}_settings");
		$shortcodeObj = new CaptureData();
		switch($_REQUEST['doaction'])
		{
		case "SaveFormSettings":
			$shortcode_name = sanitize_text_field($_REQUEST['shortcode']);
			$thirdparty_title = sanitize_text_field( $_REQUEST['thirdparty_title'] );
			$thirdparty_form_type = sanitize_text_field( $_REQUEST['thirdparty_form_type'] );
			if($thirdparty_form_type != 'none'){
				update_option( $shortcode_name , $thirdparty_title);
				update_option( 'Thirdparty_'.$shortcode_name , $thirdparty_form_type);
			}
			if( $thirdparty_title != "" )
			{
				$this->update_formtitle($shortcode_name , $thirdparty_title , $thirdparty_form_type );
			}
			$shortcodedata['module'] =  $module;
			$shortcodedata['crm_type'] =  $activatedplugin;
			$shortcodedata['name'] = $shortcode_name;
			$shortcodedata['type'] = sanitize_text_field($_REQUEST['formtype']);
			$shortcodedata['assignto'] = sanitize_text_field($_REQUEST['assignedto']);
			$shortcodedata['errormesg'] = sanitize_text_field($_REQUEST['errormessage']);
			$shortcodedata['successmesg'] = sanitize_text_field($_REQUEST['successmessage']);
			$shortcodedata['duplicate_handling'] = sanitize_text_field($_REQUEST['duplicate_handling']);
			if( sanitize_text_field($_REQUEST['enableurlredirection']) == "true" )
			{
				$shortcodedata['isredirection'] = 1;
			}
			else
			{
				$shortcodedata['isredirection'] = 0;
			}
			$shortcodedata['urlredirection'] = sanitize_text_field($_REQUEST['redirecturl']);
			if( sanitize_text_field($_REQUEST['enablecaptcha']) == "true" )
			{
				$shortcodedata['captcha'] = 1;
			}
			else
			{
				$shortcodedata['captcha'] = 0;
			}

			$shortcodeObj->formShorcodeManager( $shortcodedata , "edit" );
			break;

		case "CaptureAllWpUsers" :
			$config_user_capture = get_option("smack_{$activatedplugin}_user_capture_settings");
			$module = $config_user_capture['user_sync_module'];
			if( $module == "Leads" || $module == "Contacts" )
			{
				$rr_module = 'leads';
			}
			$wp_start = $_POST['wp_start'];
			$wp_offset = $_POST['wp_offset'];
			$users_synced_count = $_POST['synced_count'];
			$fetch_last_id = $wpdb->get_results( "select ID from {$wpdb->prefix}users order by id desc limit 1" );
			$last_user_id = $fetch_last_id[0]->ID;
			$wp_users_count = count(get_users( ));
			$duplicate_cancelled = 0;
			$duplicate_inserted = 0;
			$duplicate_updated = 0;
			$successful = 0;
			$failed = 0;
			// $url = isset($SettingsConfig['url']) ? $SettingsConfig['url'] : "";
			// $username = $SettingsConfig['username'];
			// $accesskey = isset($SettingsConfig['accesskey']) ? $SettingsConfig['accesskey'] : $SettingsConfig['password'];
			$wp_active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
			$FunctionsObj = new PROFunctions();
			global $wpdb;
			$blogusers = $wpdb->get_results( "select ID from ".$wpdb->prefix."users limit $wp_start, $wp_offset" );
			$user = array();
			foreach($blogusers as $users)
			{
				$user[] = $users->ID;
			}
			$users_within_limit = count( $user );
			if( !empty( $user )) {
				foreach($user as $user_id)
				{
					$wp_active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
					$wp_assigneduser_config = get_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" );
					$wp_usersync_assignedto = $wp_assigneduser_config['usersync_assign_leads'];
					//Code For RR
					$assignedto_old = $wp_assigneduser_config['usersync_rr_value'];
					if( empty( $assignedto_old))
					{
						$get_first_usersync_owner = new PROFunctions();
						$get_first_user = $get_first_usersync_owner->getUsersList();
						$assignedto_old = $get_first_user['id'][0];
						$wp_assigneduser_config['usersync_rr_value'] = $assignedto_old;	
						update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config );	
					}
					$email_present = "no";
					$posts = new CapturingProcessClassPRO();
					$post  = $posts-> mapUserCaptureFields( $module , $user_id , $assignedto_old );
					// $list=$post['list'];
					$user_email = "";
					$CheckEmailResult = array();
					$duplicate_option_check = $config_user_capture['smack_capture_duplicates'];
					if( isset( $post[$FunctionsObj->duplicateCheckEmailField()] ) )
					{
						if( $duplicate_option_check == 'skip_both' ){

							$CheckEmailResult_Leads = $FunctionsObj->checkEmailPresent('Leads' , $post[$FunctionsObj->duplicateCheckEmailField()]);
							$CheckEmailResult_Contacts = $FunctionsObj->checkEmailPresent('Contacts' , $post[$FunctionsObj->duplicateCheckEmailField()]);

							if( $CheckEmailResult_Leads == 1 || $CheckEmailResult_Contacts == 1 )
							{
								$CheckEmailResult = 1;
							}
						}else{		
							$CheckEmailResult = $FunctionsObj->checkEmailPresent($module , $post[$FunctionsObj->duplicateCheckEmailField()] );
						}
						$user_email = $post[$FunctionsObj->duplicateCheckEmailField()];
					}

					if(($CheckEmailResult == 1 ) && ($duplicate_option_check == 'skip' || $duplicate_option_check == 'skip_both'))
					{
						$duplicate_cancelled++;
					}
					else
					{
						$result_id = $FunctionsObj->result_ids;
						$result_emails = $FunctionsObj->result_emails;
						if($config_user_capture['smack_capture_duplicates'] == 'update')
						{
							foreach( $result_emails as $key => $email )
							{
								if($email == $user_email)
								{
									$ids_present = $result_id[$key];
									$email_present = "yes";
								}
							}

							//      Update Code here
							if(isset($email_present) && ($email_present == "yes"))
							{ 
								$FunctionsObj->updateRecord( $module , $post , $ids_present );
								$duplicate_updated++;
							}
							else
							{
								$record = $FunctionsObj->createRecord( $module , $post);
								if($record['result'] == "success")
								{
									$duplicate_inserted++;
									if( $wp_usersync_assignedto == 'Round Robin' )
									{
										$new_assigned_val = $this->getRoundRobinOwner( $assignedto_old );
										$wp_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
										update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config);

									}

								}
							}
						}
						else
						{
							$record = $FunctionsObj->createRecord( $module , $post);
							if($record['result'] == "success")
							{
								$data = "/$module entry is added./";
								if( $wp_usersync_assignedto == 'Round Robin' )
								{
									$new_assigned_val = $this->getRoundRobinOwner( $assignedto_old );
									$wp_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
									update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config);

								}	

							}
						}
					}
					if(isset($data) && $data) {
						if(preg_match("/$module entry is added./",$data)) {
							if( !empty( $user_email )) {
								if((in_array($user_email, $FunctionsObj->result_emails)) && ($config_user_capture['smack_capture_duplicates']!= 'on' ))
								{
									$duplicate_inserted++;
								}
								$successful++;
							}  } else{
							$failed++;
								}	}
				} }
					$users_synced_count = $users_synced_count + $wp_offset;
				$wp_start = $wp_offset + $wp_start;
				$user_sync_array['start'] = $wp_start;
				$user_sync_array['offset'] = $wp_offset;
				$user_sync_array['total_count'] = $wp_users_count;
				$user_sync_array['last_user_id'] = $last_user_id;
				$user_sync_array['users_within_limit'] = $users_within_limit;
				$user_sync_array['synced_count'] = $users_synced_count;
				$user_sync_array['duplicate_option'] = $config_user_capture['smack_capture_duplicates'];
				$sync_array = json_encode( $user_sync_array );
				print_r( $sync_array );
				die;
				break;
		}
	}
}

class AjaxActionsClassPRO
{
	public static function adminAllActionsPRO()
	{
		$OverallFunctionObj = new OverallFunctionsPRO();
		if( isset($_REQUEST['operation']) && (sanitize_text_field($_REQUEST['operation']) == "NoFieldOperation") )
		{
			$OverallFunctionObj->doNoFieldAjaxAction( );
		}
		else
		{
			$OverallFunctionObj->doFieldAjaxAction();
		}
		die;
	}
}

add_action('wp_ajax_adminAllActionsPRO', array( "AjaxActionsClassPRO" , 'adminAllActionsPRO' ));

class CapturingProcessClassPRO
{
	// Updating user & One Time Manual Sync
	public function mapUserCaptureFields( $module , $user_id , $assignedto_old)
	{
		$usersync_active_crm = get_option( "WpLeadBuilderProActivatedPlugin" );
		$user_field_map = get_option("User{$usersync_active_crm}{$module}ModuleMapping");
		$user_data = get_userdata( $user_id );
		$user_meta = get_user_meta( $user_id );
		$user_fields = array( 'user_login' => __('Username', "wp-leads-builder-any-crm-pro") , 'role' => __('Role' , "wp-leads-builder-any-crm-pro" ) , 'user_nicename' => __('Nicename' , "wp-leads-builder-any-crm-pro" ) , 'user_email' => __('E-mail', "wp-leads-builder-any-crm-pro" ) , 'user_url' => __('Website', "wp-leads-builder-any-crm-pro" ) , 'display_name' => __('Display name publicly as', "wp-leads-builder-any-crm-pro" ) );
		$user_meta_field = array( 'nickname' => __('Nickname', "wp-leads-builder-any-crm-pro" ) , 'first_name' => __('First Name', "wp-leads-builder-any-crm-pro" ) , 'last_name' => __('Last Name', "wp-leads-builder-any-crm-pro" ) , 'description' => __('Biographical Info', "wp-leads-builder-any-crm-pro" )  , 'phone_number'=> __('Phone Number' , 'wp-leads-builder-any-crm-pro' ), 'mobile_number'=> __('Mobile Number' , 'wp-leads-builder-any-crm-pro' ));

		//wp-member custom fields
		$active_plugins = get_option( "active_plugins" );
		$custom_plugin = get_option( "custom_plugin" );
		$memberpress_plugin = "memberpress/memberpress.php";
		$custom_field_array = $acf_array = $acfpro_array = $post = $um_array = array();
		if( in_array("wp-members/wp-members.php" , $active_plugins) && $custom_plugin == 'wp-members' )
		{
			$wp_member_array = get_option("wpmembers_fields");
			$option = array();
			$i=0;
			foreach( $wp_member_array as $key=>$option_name )
			{       $i++;
			$option[$i]['label'] = $option_name['1'];
			$option[$i]['name'] = $option_name['2'];
			}

			foreach( $option as $opt_ke=>$opt_val  )
			{
				if( !array_key_exists( $opt_val['name'] , $user_fields) && !array_key_exists( $opt_val['name'] , $user_meta_field ) ){

					$custom_field_array[$opt_val['name']] =   $opt_val['label'] ;

				}
			}
			$user_fields = array_merge( $user_fields , $custom_field_array );
		}
		//End wp-members custom fields

		//Ultimate Members
		if( in_array("ultimate-member/ultimate-member.php" , $active_plugins)&& $custom_plugin == 'ultimate-member'  )
		{
			$um_array = get_option("um_fields");
			$option = $custom_field_array = array();
			$i=0;
			if( !empty( $um_array )) {
				foreach( $um_array as $key=>$option_name )
				{
					$i++;
					$option[$i]['label'] = $option_name['label'];
					$option[$i]['metakey'] = $option_name['metakey'];

				}
				foreach( $option as $opt_ke=>$opt_val  )
				{
					if( !array_key_exists( $opt_val['metakey'] , $user_fields) &&  (!array_key_exists( $opt_val['metakey'] , $user_meta_field )) ){
						$custom_field_array[$opt_val['metakey']] =   $opt_val['label'] ;
					}
				}
			}
		}

		//ACF custom-fields
		if( in_array("advanced-custom-fields/acf.php" , $active_plugins) && $custom_plugin == 'acf' )
		{
			global $wpdb;
			$acf_vals = array();
			$acf = $wpdb->get_results($wpdb->prepare( "select * from ".$wpdb->posts." where post_type=%s and post_status=%s" , 'acf' , 'publish'),ARRAY_A );
			$i = 0;
			foreach( $acf as $idkey=>$idval )
			{
				$id = $idval["ID"] ;
				$meta_fields = $wpdb->get_results( $wpdb->prepare("select meta_value from ".$wpdb->postmeta." where post_id=%s and meta_key like %s" , $id , 'field_%'),ARRAY_A );
				foreach( $meta_fields as $mkey=>$mvalue )
				{
					$meta_values = unserialize( $mvalue['meta_value'] ) ;
					$acf_vals[$i]['key']   = $meta_values['key'];
					$acf_vals[$i]['label'] = $meta_values['label'] ;
					$acf_vals[$i]['name']  = $meta_values['name'] ;
					$i++;
				}
			}

			foreach( $acf_vals as $acfkey => $acf_vl )
			{
				$meta_key = $acf_vl['name'];
				$check_db = $wpdb->get_results($wpdb->prepare( "select * from ". $wpdb->usermeta ." where meta_key=%s", $meta_key) );
				if( isset( $check_db ) && !empty($check_db) )
				{
					$acf_array[$acf_vl['name']] = $acf_vl['label'];
				}
			}
		}
		//End of ACF Custom-fields

		//ACF PRo
		if( in_array('advanced-custom-fields-pro/acf.php' , $active_plugins) && $custom_plugin == 'acfpro' )
		{
			global $wpdb;
			$acfpro_vals = array();
			$acfpro = $wpdb->get_results($wpdb->prepare( "select * from ".$wpdb->posts." where post_type=%s and post_status=%s" , 'acf-field' , 'publish'),ARRAY_A );
			$i = 0;
			if( !empty( $acfpro )) {
				foreach( $acfpro as $idkey=>$idval )
				{
					$acfpro_vals[$i]['key']   = $idval['post_excerpt'];
					$acfpro_vals[$i]['label'] = $idval['post_title'] ;
					$acfpro_vals[$i]['name']  = $idval['post_excerpt'] ;
					$i++;
				}
				foreach( $acfpro_vals as $acfkey => $acf_vl )
				{
					$acfpro_array[$acf_vl['name']] = $acf_vl['label'];
				}
				if( isset( $acfpro_array ) && !empty($acfpro_array))
				{
					$user_fields = array_merge( $user_fields , $acfpro_array );
				}
			}
		}

		//END ACF PRO 

		//MemberPress support
		if( in_array($memberpress_plugin , $active_plugins) && $custom_plugin == 'member-press' ) 
		{
			$mp_fields = array();
			$member_press = get_option('mepr_options');
			$field_list = $member_press['custom_fields'];
			$i = 0;
			$mp_custom_field_list = array();
			if( !empty( $field_list )) {
				foreach( $field_list as $custom_fields )
				{
					$mp_custom_field_list[$i]['field_key'] = $custom_fields['field_key'] ;
					$mp_custom_field_list[$i]['field_name'] = $custom_fields['field_name'];
					$mp_custom_field_list[$i]['options'] = $custom_fields['options'];
					$i++;
				}
				foreach( $mp_custom_field_list as $mp_label => $mp_option )
				{
					$mp_fields[$mp_option['field_key']] = $mp_option['field_name'];
				}	
				$user_fields = array_merge( $user_fields , $mp_fields );
			}
		}
		//End of Memberpress support
		foreach( $user_fields as $field_name => $Label )
		{
			if( isset($user_field_map[$field_name]) && $user_field_map[$field_name] != "" )
			{
				$post[$user_field_map[$field_name]] = isset($user_data->data->$field_name) ? $user_data->data->$field_name : "";
			}
		}
		foreach( $user_fields as $field_name => $Label )
		{
			if( $post[$user_field_map[$field_name]] == " " )
			{
				$post[$user_field_map[$field_name]] = isset($user_meta[$field_name][0]) ? $user_meta[$field_name][0] : "";
			}
		}
		foreach( $user_meta_field as $field_name => $Label )
		{
			if( $user_field_map[$field_name] != "" )
			{
				$post[$user_field_map[$field_name]] = isset($user_meta[$field_name][0]) ? $user_meta[$field_name][0] : "";
			}
		}
		//mapping wp-member custom-fields
		if( in_array("wp-members/wp-members.php" , $active_plugins) && $custom_plugin == 'wp-members' ) {
			foreach( $custom_field_array as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					if($user_field_map[$field_name] == $field_name)
					{
						$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
					}
				}
			}
		}//End of wp-member custom fields

		//Ultimate Member
		if( in_array("ultimate-member/ultimate-member.php" , $active_plugins) && $custom_plugin == 'ultimate-member' ) {
			global $wpdb;
			$fieldsarray=array();
			$get_types =$wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key ='_um_custom_fields'",ARRAY_A);
			foreach($get_types as $gets){
				$fieldsarray=array_merge($fieldsarray,unserialize($gets['meta_value']));
			}
			foreach( $custom_field_array as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					if(isset($fieldsarray[$field_name]['type'])&& $fieldsarray[$field_name]['type'] == 'multiselect'){
						$fieldvalue=unserialize($user_meta[$field_name][0]);
						$post[$user_field_map[$field_name]] = $fieldvalue;
					}
					elseif(isset($fieldsarray[$field_name]['type'])&& $fieldsarray[$field_name]['type'] == 'checkbox'){
						$fieldvalues=unserialize($user_meta[$field_name][0]);
						if(is_array($fieldvalues)){
							$fieldvalue = $fieldvalues[0];
						}
						else{
							$fieldvalue = 0;
						}
						if($fieldvalue == 1){
							$fieldvalue =true;
						}
						$post[$user_field_map[$field_name]] =$fieldvalue;
					}
					else{
						$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
					}
					
				}
			}
		}
		//End of ultimate-member custom fields      

		//Mapping ACF Custom Fields
		if( in_array("advanced-custom-fields/acf.php" , $active_plugins) && $custom_plugin == 'acf' )
		{
			foreach( $acf_array as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					if($user_field_map[$field_name] == $field_name) 
					{
						$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
					}
				}
			}
		}

		//ACF PRO 
		if( in_array("advanced-custom-fields-pro/acf.php" , $active_plugins) && $custom_plugin == 'acfpro' )
		{
			foreach( $acfpro_array as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
				}
			}
		}
		//ACF PRO

		if( in_array($memberpress_plugin , $active_plugins) && $custom_plugin == 'member-press' ) 
		{
			foreach( $mp_fields as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					if($user_field_map[$field_name] == $field_name)
					{
						$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
					}
				}
			}
		}
		$Assigned_user = CapturingProcessClassPRO::wp_get_usersync_assignedto($assignedto_old);		
		$Assigned_user_value = array_values($Assigned_user);
		if( $Assigned_user_value[0] != "--Select--" )
		{	
			$post = array_merge( $post , $Assigned_user );	
		}
		unset($post['']);
		if( $user_field_map['role'] != "" )
		{
			$post[$user_field_map['role']] = $user_data->roles[0];
		}
		return $post;
	}

	public static function wp_get_usersync_assignedto($assignedto_old)
	{
		$wp_active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$usersync_config = get_option( "smack_{$wp_active_crm}_user_capture_settings" );
		$module = $usersync_config['user_sync_module'];
		$wp_assigneduser_config = get_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" );		
		$assignedto_user = array();
		switch( $wp_active_crm )
		{
		case 'wpzohopro':
			if( $wp_assigneduser_config['usersync_assign_leads'] != "Round Robin" )
			{
				$assignedto_user['SMOWNERID'] = $wp_assigneduser_config['usersync_assign_leads'];	
			}
			else
			{
				$assignedto_user['SMOWNERID'] = $assignedto_old;
			}
			break;

		case 'wpzohopluspro':
			if( $wp_assigneduser_config['usersync_assign_leads'] != "Round Robin" )
			{
				$assignedto_user['SMOWNERID'] = $wp_assigneduser_config['usersync_assign_leads'];
			}
			else
			{
				$assignedto_user['SMOWNERID'] = $assignedto_old;
			}
			break;

		case 'wpsugarpro':
			if( $wp_assigneduser_config['usersync_assign_leads'] != "Round Robin" )
			{
				$assignedto_user['assigned_user_id'] = $wp_assigneduser_config['usersync_assign_leads'];	
			}
			else
			{
				$assignedto_user['assigned_user_id'] = $assignedto_old;
			}
			break;

		case 'wpsuitepro':
			if( $wp_assigneduser_config['usersync_assign_leads'] != "Round Robin" )
			{
				$assignedto_user['assigned_user_id'] = $wp_assigneduser_config['usersync_assign_leads'];
			}
			else
			{
				$assignedto_user['assigned_user_id'] = $assignedto_old;
			}
			break;


		case 'wptigerpro':
			if( $wp_assigneduser_config['usersync_assign_leads'] != "Round Robin" )
			{
				$assignedto_user['assigned_user_id'] = $wp_assigneduser_config['usersync_assign_leads'];	
			}
			else
			{
				$assignedto_user['assigned_user_id'] = $assignedto_old;
			}	
			break;

		case 'wpsalesforcepro':
			if( $wp_assigneduser_config['usersync_assign_leads'] != "Round Robin" )
			{
				$assignedto_user['OwnerId'] = $wp_assigneduser_config['usersync_assign_leads'];	
			}
			else
			{
				$assignedto_user['OwnerId'] = $assignedto_old;
			}
			break;

		case 'freshsales':
			if( $wp_assigneduser_config['usersync_assign_leads'] != "Round Robin" )
			{
				$assignedto_user['owner_id'] = $wp_assigneduser_config['usersync_assign_leads'];
			}
			else
			{
				$assignedto_user['owner_id'] = $assignedto_old;
			}
			break;

		}
		return $assignedto_user;
	}

	//Register new user

	public static function mapRegisterUser( $module , $user_id, $posted_fields , $assignedto_old )
	{
		$usersync_active_crm = get_option( "WpLeadBuilderProActivatedPlugin" );
		$user_field_map = get_option("User{$usersync_active_crm}{$module}ModuleMapping");
		$user_data = get_userdata( $user_id );
		$user_meta = get_user_meta( $user_id );
		$user_fields = array( 'user_login' => __('Username', "wp-leads-builder-any-crm-pro") , 'role' => __('Role' , "wp-leads-builder-any-crm-pro" ) , 'user_nicename' => __('Nicename' , "wp-leads-builder-any-crm-pro" ) , 'user_email' => __('E-mail', "wp-leads-builder-any-crm-pro" ) , 'user_url' => __('Website', "wp-leads-builder-any-crm-pro" ) , 'display_name' => __('Display name publicly as', "wp-leads-builder-any-crm-pro" ) );
		$user_meta_field = array( 'nickname' => __('Nickname', "wp-leads-builder-any-crm-pro" ) , 'first_name' => __('First Name', "wp-leads-builder-any-crm-pro" ) , 'last_name' => __('Last Name', "wp-leads-builder-any-crm-pro" ) , 'description' => __('Biographical Info', "wp-leads-builder-any-crm-pro" ) );
		//wp-member custom fields
		$active_plugins = get_option( "active_plugins" );
		$custom_plugin = get_option( "custom_plugin" );
		$memberpress_plugin = "memberpress/memberpress.php";
		$custom_field_array = $acf_array = $acfpro_array = $post = $um_array = array();
		if( in_array("wp-members/wp-members.php" , $active_plugins) && $custom_plugin == 'wp-members' )
		{
			$wp_member_array = get_option("wpmembers_fields");
			$option = array();
			$i=0;
			foreach( $wp_member_array as $key=>$option_name )
			{       $i++;
			$option[$i]['label'] = $option_name['1'];
			$option[$i]['name'] = $option_name['2'];
			}

			foreach( $option as $opt_ke=>$opt_val  )
			{
				if( !array_key_exists( $opt_val['name'] , $user_fields) && !array_key_exists( $opt_val['name'] , $user_meta_field ) ){

					$custom_field_array[$opt_val['name']] =   $opt_val['label'] ;

				}
			}
		}
		//End wp-members custom fields

		//Ultimate Members
		if( in_array("ultimate-member/ultimate-member.php" , $active_plugins)&& $custom_plugin == 'ultimate-member'  )
		{
			$um_array = get_option("um_fields");
			$option = $custom_field_array = array();
			$i=0;
			if( !empty( $um_array )) {
				foreach( $um_array as $key=>$option_name )
				{
					$i++;
					$option[$i]['label'] = $option_name['label'];
					$option[$i]['metakey'] = $option_name['metakey'];

				}
				foreach( $option as $opt_ke=>$opt_val  )
				{
					if( !array_key_exists( $opt_val['metakey'] , $user_fields ) &&  !array_key_exists( $opt_val['metakey'] , $user_meta_field ) ){
						$custom_field_array[$opt_val['metakey']] =   $opt_val['label'] ;
					}
				}
			}
		}

		//ACF custom-fields
		if( in_array("advanced-custom-fields/acf.php" , $active_plugins) && $custom_plugin == 'acf' )
		{
			global $wpdb;
			$acf_vals = array();
			$acf = $wpdb->get_results($wpdb->prepare( "select * from ".$wpdb->posts." where post_type=%s and post_status=%s" , 'acf' , 'publish'),ARRAY_A );
			$i = 0;
			foreach( $acf as $idkey=>$idval )
			{
				$id = $idval["ID"] ;
				$meta_fields = $wpdb->get_results( $wpdb->prepare("select meta_value from ".$wpdb->postmeta." where post_id=%s and meta_key like %s" , $id , 'field_%'),ARRAY_A );
				foreach( $meta_fields as $mkey=>$mvalue )
				{
					$meta_values = unserialize( $mvalue['meta_value'] ) ;
					$acf_vals[$i]['key']   = $meta_values['key'];
					$acf_vals[$i]['label'] = $meta_values['label'] ;
					$acf_vals[$i]['name']  = $meta_values['name'] ;
					$i++;
				}
			}

			foreach( $acf_vals as $acfkey => $acf_vl )
			{
				$meta_key = $acf_vl['name'];
				$check_db = $wpdb->get_results($wpdb->prepare( "select * from ". $wpdb->usermeta ." where meta_key=%s", $meta_key) );
				if( isset( $check_db ) && !empty($check_db) )
				{
					$acf_array[$acf_vl['name']] = $acf_vl['label'];
				}
			}
		}
		//End of ACF Custom-fields

		//ACF PRO
		if( in_array('advanced-custom-fields-pro/acf.php' , $active_plugins) && $custom_plugin == 'acfpro' )
		{
			global $wpdb;
			$acfpro_vals = array();
			$acfpro = $wpdb->get_results($wpdb->prepare( "select * from ".$wpdb->posts." where post_type=%s and post_status=%s" , 'acf-field' , 'publish'),ARRAY_A );
			$i = 0;
			if( !empty( $acfpro )) {
				foreach( $acfpro as $idkey=>$idval )
				{
					$acfpro_vals[$i]['key']   = $idval['post_excerpt'];
					$acfpro_vals[$i]['label'] = $idval['post_title'] ;
					$acfpro_vals[$i]['name']  = $idval['post_excerpt'] ;
					$i++;
				}
				foreach( $acfpro_vals as $acfkey => $acf_vl )
				{
					$acfpro_array[$acf_vl['name']] = $acf_vl['label'];
				}
				if( isset( $acfpro_array ) && !empty($acfpro_array))
				{
					$user_fields = array_merge( $user_fields , $acfpro_array );
				}
			}
		}
		//END ACF PRO

		//MemberPress support
		if( in_array($memberpress_plugin , $active_plugins) && $custom_plugin == 'member-press' ) 
		{
			$mp_fields = array();
			$member_press = get_option('mepr_options');
			$field_list = $member_press['custom_fields'];
			$i = 0;
			$mp_custom_field_list = array();
			if( !empty( $field_list )) {
				foreach( $field_list as $custom_fields )
				{
					$mp_custom_field_list[$i]['field_key'] = $custom_fields->field_key ;
					$mp_custom_field_list[$i]['field_name'] = $custom_fields->field_name;
					$mp_custom_field_list[$i]['options'] = $custom_fields->options;
					$i++;
				}
				foreach( $mp_custom_field_list as $mp_label => $mp_option )
				{
					$mp_fields[$mp_option['field_key']] = $mp_option['field_name'];
				}	
			}
		}
		//End of Memberpress support
		foreach( $user_fields as $field_name => $Label )
		{
			if( $user_field_map[$field_name] != "" )
			{
				$post[$user_field_map[$field_name]] = $user_data->data->$field_name;
			}
		}

		foreach( $user_meta_field as $field_name => $Label )
		{
			if( $user_field_map[$field_name] != "" )
			{
				$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
			}
		}

		//mapping wp-member custom-fields
		if( in_array("wp-members/wp-members.php" , $active_plugins) && $custom_plugin == 'wp-members' ) {
			foreach( $custom_field_array as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					$post[$user_field_map[$field_name]] = $posted_fields[$field_name];
				}
			}
		}//End of wp-member custom fields

		//Ultimate Member custom fields
		if( in_array("ultimate-member/ultimate-member.php" , $active_plugins) && $custom_plugin == 'ultimate-member' ) {
			foreach( $custom_field_array as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
				}
			}
		}
		//End of ultimate-member custom-fields

		//Mapping ACF Custom Fields
		if( in_array("advanced-custom-fields/acf.php" , $active_plugins) && $custom_plugin == 'acf' )
		{
			foreach( $acf_array as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
				}
			}
		}

		//ACF PRO
		if( in_array("advanced-custom-fields-pro/acf.php" , $active_plugins) && $custom_plugin == 'acfpro' )
		{
			foreach( $acfpro_array as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
				}
			}
		}
		//END ACF PRO

		if( in_array($memberpress_plugin , $active_plugins) && $custom_plugin == 'member-press' ) 
		{
			foreach( $mp_fields as $field_name => $Label )
			{
				if( $user_field_map[$field_name] != "" )
				{
					$post[$user_field_map[$field_name]] = $user_meta[$field_name][0];
				}
			}
		}
		//Assign user
		$Assigned_user = CapturingProcessClassPRO::wp_get_usersync_assignedto($assignedto_old);
		$Assigned_user_value = array_values($Assigned_user);
		if( $Assigned_user_value[0] != "--Select--" )
		{
			$post = array_merge( $post , $Assigned_user );
		}

		if( $user_field_map['role'] != "" )
		{
			$post[$user_field_map['role']] = $user_data->roles[0];
		}
		return $post;
	}


	function getRoundRobinOwner( $assignedto_old )	
	{
		$crm_users_list = get_option( 'crm_users' );
		$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$RR_users_list = $crm_users_list[$activated_crm];
		if(isset($RR_users_list['id'])){
			$RR_users_id = $RR_users_list['id'];
		}

		foreach( $RR_users_id as $RR_key => $RR_val )
		{
			$i = $RR_key;
			if( $assignedto_old == $RR_val )
			{
				if( isset( $RR_users_id[$i+1] ))
				{
					$assignedto_new = $RR_users_id[$i+1];
				}
				else
				{
					$assignedto_new = $RR_users_id[0];
				}
			}

			$i++;
		}
		return $assignedto_new;

	}

	function CaptureFormFields($globalvariables)
	{
		global $wpdb;
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = $HelperObj->Module;
		// $moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		// $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$duplicate_inserted = $duplicate_cancelled = $duplicate_updated = 0;
		if(isset($globalvariables['formattr']['module'])){
			$module = $globalvariables['formattr']['module'];
		}
		$post = $globalvariables['post'];

		// $list=$post['list'];
		$FunctionsObj = new PROFunctions();
		$emailfield = $FunctionsObj->duplicateCheckEmailField();
		$shortcode_name = $globalvariables['attrname'];
		$enable_round_robin = $wpdb->get_var($wpdb->prepare("select assigned_to from wp_smackleadbulider_shortcode_manager where shortcode_name =%s", $shortcode_name));
		if ($enable_round_robin == 'Round Robin') {
			$assignedto_old = $wpdb->get_var($wpdb->prepare("select Round_Robin from wp_smackleadbulider_shortcode_manager where shortcode_name =%s", $shortcode_name));
		}

		if (is_array($post)) {
			foreach ($post as $key => $value) {
				if (($key != 'moduleName') && ($key != 'submitcontactform') && ($key != 'submitcontactformwidget') && ($key != '') && ($key != 'submit')) {
					$module_fields[$key] = $value;
					if ($key == $emailfield) {
						$email_field_present = "yes";
						$user_email = $value;
					}
				}
			}
		}
		if (is_array($post)) {
			foreach ($post as $key => $value) {
				if (($key != 'moduleName') && ($key != 'submitwpform') && ($key != 'submitwpformwidget') && ($key != '') && ($key != 'submit')) {
					$module_fields[$key] = $value;
					if ($key == $emailfield) {
						$email_field_present = "yes";
						$user_email = $value;
					}
				}
			}
		}

		if ($enable_round_robin != 'Round Robin') {
			$module_fields[$FunctionsObj->assignedToFieldId()] = $globalvariables['assignedto'];
		} else {
			$module_fields[$FunctionsObj->assignedToFieldId()] = $assignedto_old;
		}
		unset($module_fields['formnumber']);
		unset($module_fields['IsUnreadByOwner']);

		if($activatedplugin == 'wpzohopro'){
			$module_fields['SMOWNERID'] = $module_fields['Lead_Owner'];
		}

		// Check both module and Skip
		if(isset($globalvariables['formattr']['duplicate_handling'])){
			$duplicate_option_check = $globalvariables['formattr']['duplicate_handling'];
		}
		if (isset($duplicate_option_check) && ($duplicate_option_check == 'skip_both')) {

			$CheckEmailResult_Leads = $FunctionsObj->checkEmailPresent('Leads', $post[$emailfield]);
			$CheckEmailResult_Contacts = $FunctionsObj->checkEmailPresent('Contacts', $post[$emailfield]);
			if ($CheckEmailResult_Leads == 1 || $CheckEmailResult_Contacts == 1) {
				$CheckEmailResult = 1;
			}
		} else {
			if (is_array($post)&& isset($post, $post[$emailfield])) {
				$CheckEmailResult = $FunctionsObj->checkEmailPresent($module, $post[$emailfield]);
			}
		}

		if ((isset($CheckEmailResult)&&($CheckEmailResult == 1) )&& ($duplicate_option_check == 'skip' || $duplicate_option_check == 'skip_both')) {
			$duplicate_cancelled++;
		} else {
			$result_id = $FunctionsObj->result_ids;
			$result_emails = $FunctionsObj->result_emails;
			if (isset($globalvariables['formattr']['duplicate_handling']) && $globalvariables['formattr']['duplicate_handling'] == 'update') {
				if(!empty($result_emails) && is_array($result_emails))
					foreach ($result_emails as $key => $email) {
						if (($email == $user_email) && ($user_email != "")) {
							$ids_present = $result_id[$key];
							$email_present = "yes";
						}
					}
				// Update Code here
				if (isset($email_present) && ($email_present == "yes")) {
					$record = $FunctionsObj->updateRecord($module, $module_fields, $ids_present);

					if ($record['result'] == "success") {
						$duplicate_updated++;
						$data = "/$module entry is added./";
					}
				} else {
					$record = $FunctionsObj->createRecord($module, $module_fields);
					if ($record['result'] == "success") {
						$duplicate_inserted++;
						$data = "/$module entry is added./";
						if ($enable_round_robin == 'Round Robin') {
							$new_assigned_val = self::getRoundRobinOwner($assignedto_old);
							$wpdb->update('wp_smackleadbulider_shortcode_manager', array('Round_Robin' => $new_assigned_val), array('shortcode_name' => $shortcode_name));
						}
					}
				}
			} else {
				if($activatedplugin == 'wpsalesforcepro'){
					unset($module_fields['submitcontactform']);
				}
				$record = $FunctionsObj->createRecord($module, $module_fields);
				$data = "failure";
				if ($record['result'] == "success")
					if(isset($record['crm_id'])){
						$crm_record_id = $record['crm_id'];
						$crm_id = (str_replace("12x", "", $crm_record_id));
					}
				//	$crm_record_id = $record['crm_id'];
				//	$crm_id = (str_replace("12x", "", $crm_record_id));
				$submit_data = $wpdb->get_results($wpdb->prepare("select success_count from wp_smackleadbulider_shortcode_manager where shortcode_name =%s", $shortcode_name), ARRAY_A);
				if(!empty($submit_data)){
					$submit_id = $submit_data[0]['success_count'] + 1;
					foreach ($module_fields as $meta_key => $meta_value) {
						$wpdb->insert('wp_smackleadbulider_formsubmission_record', array('form_id' => "$submit_id", 'shortcode_name' => "$shortcode_name", 'meta_key' => "$meta_key", 'meta_value' => "$meta_value", 'module' => "$module"));
					}
					{
						$duplicate_inserted++;
						$data = "/$module entry is added./";
						if ($enable_round_robin == 'Round Robin') {
							$new_assigned_val = self::getRoundRobinOwner($assignedto_old);
							$wpdb->update('wp_smackleadbulider_shortcode_manager', array('Round_Robin' => $new_assigned_val), array('shortcode_name' => $shortcode_name));
	
						}
					}
				}
				
			}
		}
		return $data;
	}

	//Ecom mapping configuration

	public static function ecom_mapped_submission($posted_array)
	{
		$ecom_module = $posted_array['ecom_module'];
		$ecom_active_crm = $posted_array['ecom_crm'];
		$ecom_shortcode = $posted_array['ecom_shortcode'];
		$duplicate_option = $posted_array['duplicate_option'];
		$ecom_items = $posted_array['products'];
		$ecom_order_id = $posted_array['order_id'];
		//RR
		$HelperObj = new WPCapture_includes_helper_PRO();
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$content = '';

		$get_first_ecom_owner = new PROFunctions();
		$get_first_user = $get_first_ecom_owner->getUsersList();
		$ecom_first_user = $get_first_user['id'][0];

		$ecom_existing_array = get_option( $ecom_shortcode );
		$ecom_RR_assignedto = $ecom_existing_array['ecom_assignedto'];
		$assignedto_old = $ecom_existing_array['ecom_roundrobin'];
		if( empty( $assignedto_old ))
		{
			$assignedto_old = $ecom_first_user;
			update_option( $ecom_existing_array['ecom_roundrobin'] , $assignedto_old );
		}
		if( isset($ecom_module)  )
		{
			$module = $ecom_module;
			$duplicate_inserted = 0;
			$duplicate_updated = 0;
			$FunctionsObj = new PROFunctions();
			$post = $posted_array['posted'];
			$Assigned_user = CapturingProcessClassPRO::wp_get_ecom_assignedto($ecom_shortcode , $assignedto_old);
			$Assigned_user_value = array_values($Assigned_user);
			if( $Assigned_user_value[0] != "--Select--" )
			{
				$post = array_merge( $post , $Assigned_user );
			}
			if( isset( $post[$FunctionsObj->duplicateCheckEmailField()] ) )
			{
				$CheckEmailResult = $FunctionsObj->checkEmailPresent($module , $post[$FunctionsObj->duplicateCheckEmailField()] );
				$user_email = $post[$FunctionsObj->duplicateCheckEmailField()];
			}

			if( $duplicate_option == 'create' )
			{
				$record = $FunctionsObj->createEcomRecord( $module , $post , $ecom_order_id);
				if($record['result'] == "success")
				{
					if( $ecom_RR_assignedto == 'Round Robin' )
					{
						$new_assigned_val = new OverallFunctionsPRO();
						$new_assigned_val = $new_assigned_val->getRoundRobinOwner( $assignedto_old );
						$ecom_existing_array['ecom_roundrobin'] = $new_assigned_val;
						update_option( $ecom_shortcode , $ecom_existing_array);					
					}
				}
				if( $ecom_active_crm != 'wpsugarpro' && $ecom_active_crm != 'freshsales' && $ecom_active_crm != 'wpsuitepro' || $ecom_active_crm != 'joforce') {
					foreach( $ecom_items as $item_key => $product_array )
					{	
						$product_present = $FunctionsObj->checkProductPresent( 'Products' , $item_key );
						$product_name = $item_key;	
						$result_id = $FunctionsObj->result_ids;
						$result_products = $FunctionsObj->result_products;
						foreach( $result_products as $key => $product )
						{
							if($product == $product_name)
							{
								$ids_present = $result_id[$key];
								$product_present = "yes";
							}
						}
						//      Update Code here
						if(isset($product_present) && ($product_present == "yes"))
						{
							$record = $FunctionsObj->updateEcomRecord( 'Products' , $product_array , $ids_present , $ecom_order_id);
							$duplicate_updated++;
						}
						else
						{
							$record = $FunctionsObj->createEcomRecord( 'Products' , $product_array , $ecom_order_id);
							if($record)
							{
								$duplicate_inserted++;
							}
						}

						//END
					}
				} //End Check CRM
				if($record)
				{
					$data = "/$module entry is added./";
				}
			}
		}
	}

	public static function ecom_convert_lead($lead_id , $order_id , $lead_no , $sales_order )
	{
		$module = 'Leads';
		$FunctionsObj = new PROFunctions();	
		$FunctionsObj->convertLead( $module , $lead_id , $order_id , $lead_no , $sales_order);
	}

	public static function ecom_create_sales_order( $order_id , $contact_id , $sales_order )
	{
		$module = 'Contacts';
		$FunctionsObj = new PROFunctions();
		$FunctionsObj->create_sales_order( $module , $order_id , $contact_id , $sales_order);
	}

	//Create data with thirdparty mapped configuration
	public static function thirdparty_mapped_submission($posted_array)
	{

		$list=$posted_array['posted'];
		if (isset($list['list'])) {
			$tp_list=$list['list'];
		}
		$tp_module = $posted_array['third_module'];
		// $tp_active_crm = $posted_array['thirdparty_crm'];
		// $tp_plugin_name = $posted_array['third_plugin'];
		// $tp_form_title = $posted_array['form_title'];
		$tp_shortcode = $posted_array['shortcode'];
		$duplicate_option = $posted_array['duplicate_option'];

		//Code For RR
		$get_existing_option = get_option( $tp_shortcode );

		$tp_assignedto = $get_existing_option['thirdparty_assignedto'];
		$assignedto_old = isset($get_existing_option['tp_roundrobin']) ? $get_existing_option['tp_roundrobin'] : '';
		$wp_active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		if( empty( $assignedto_old))
		{
			$get_first_RR_owner = new PROFunctions();
			$get_first_user = $get_first_RR_owner->getUsersList();
			$firstuser = isset($get_first_user) ? $get_first_user : '';
			$firstuser1 = isset($firstuser['id']) ? $firstuser['id'][0] : '';
			$assignedto_old = $firstuser1;
			$get_existing_option['tp_roundrobin'] = $assignedto_old;
			update_option( $tp_shortcode , $get_existing_option );
		}

		//END RR


		if( isset($tp_module)  )
		{
			$module = $tp_module;
			$duplicate_cancelled = 0;
			$duplicate_inserted = 0;
			$FunctionsObj = new PROFunctions();
			$post = $posted_array['posted'];
			$Assigned_user = CapturingProcessClassPRO::wp_get_mapping_assignedto($tp_shortcode , $assignedto_old);
			$Assigned_user_value = array_values($Assigned_user);
			if(isset($Assigned_user_value[0])){
				if( $Assigned_user_value[0] != "--Select--" )
				{
					$post = array_merge( $post , $Assigned_user );
				}else{
					$assign_user_key = array_keys($Assigned_user);
					$get_crm_users = get_option('crm_users');
					$crmuserid = $get_crm_users['wpzohopro']['id'][0];
					$assign_user = array();
					$assign_user[$assign_user_key[0]] = $crmuserid;
					$post = array_merge( $post , $assign_user);
				}

			}
			else{
				$post = array_merge( $post , $Assigned_user );
			}
			$user_email = "";
			$CheckEmailResult = array();
			if( isset( $post[$FunctionsObj->duplicateCheckEmailField()] ) )
			{
				if( $duplicate_option == 'skip_both' ){

					$CheckEmailResult_Leads = $FunctionsObj->checkEmailPresent('Leads' , $post[$FunctionsObj->duplicateCheckEmailField()]);
					$CheckEmailResult_Contacts = $FunctionsObj->checkEmailPresent('Contacts' , $post[$FunctionsObj->duplicateCheckEmailField()]);
					if( $CheckEmailResult_Leads == 1 || $CheckEmailResult_Contacts == 1 )
					{
						$CheckEmailResult = 1;
					}

				}else{
					$CheckEmailResult = $FunctionsObj->checkEmailPresent($module , $post[$FunctionsObj->duplicateCheckEmailField()] );
				}
				$user_email = $post[$FunctionsObj->duplicateCheckEmailField()];
			}


			if(($CheckEmailResult == 1) && ($duplicate_option =='skip' || $duplicate_option == 'skip_both'))
			{
				$duplicate_cancelled++;
			}
			else
			{
				$result_id = $FunctionsObj->result_ids;
				$result_emails = $FunctionsObj->result_emails;
				if($duplicate_option == 'update')
				{
					foreach( $result_emails as $key => $email )
					{
						if($email == $user_email)
						{
							$ids_present = $result_id[$key];
							$email_present = "yes";
						}
					}
					//      Update Code here
					if(isset($email_present) && ($email_present == "yes"))
					{
						$record = $FunctionsObj->updateRecord( $module , $post , $ids_present );

						if($record['result'] == "success")
						{
							//success			
						}
					}
					else
					{
						$record = $FunctionsObj->createRecord( $module , $post);
						if($record['result'] == "success")
						{
							$duplicate_inserted++;
							if( $tp_assignedto == 'Round Robin' )
							{            
								$new_assigned_val = new OverallFunctionsPRO();
								$new_assigned_val = $new_assigned_val->getRoundRobinOwner( $assignedto_old );
							$get_existing_option['tp_roundrobin'] = $new_assigned_val;
							update_option( $tp_shortcode , $get_existing_option);
							}

						}
					}
				}
				else
				{
					$record = $FunctionsObj->createRecord( $module , $post);
					if($record['result'] == "success")
					{
						if( $tp_assignedto == 'Round Robin' )
						{
							$new_assigned_val = new OverallFunctionsPRO();
							$new_assigned_val = $new_assigned_val->getRoundRobinOwner( $assignedto_old );
							$get_existing_option['tp_roundrobin'] = $new_assigned_val;
							update_option( $tp_shortcode , $get_existing_option);
						}
					}
				}
			}
		}
	}


	//ECOM ASSIGNEDTO
	public static function wp_get_ecom_assignedto($shortcode , $assignedto_old)
	{
		$wp_active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$wp_assigneduser_config = get_option( $shortcode );
		$module = $wp_assigneduser_config['ecom_module'];	
		$ecom_RR_assignedto = $wp_assigneduser_config['ecom_assignedto'];	
		$assignedto_user = array();
		switch( $wp_active_crm )
		{
		case 'wpzohopro':
			if( $ecom_RR_assignedto != 'Round Robin' )	
			{
				$assignedto_user['SMOWNERID'] = $wp_assigneduser_config['ecom_assignedto'];
			}
			else
			{
				$assignedto_user['SMOWNERID'] = $assignedto_old;
			}	
			break;

		case 'wpzohopluspro':
			if( $ecom_RR_assignedto != 'Round Robin' )
			{
				$assignedto_user['SMOWNERID'] = $wp_assigneduser_config['ecom_assignedto'];
			}
			else
			{
				$assignedto_user['SMOWNERID'] = $assignedto_old;
			}
			break;

		case 'wpsugarpro':
			if( $ecom_RR_assignedto != 'Round Robin' )      
			{
				$assignedto_user['assigned_user_id'] = $wp_assigneduser_config['ecom_assignedto'];	
			}
			else
			{
				$assignedto_user['assigned_user_id'] = $assignedto_old;
			}
			break;

		case 'wpsuitepro':
			if( $ecom_RR_assignedto != 'Round Robin' )
			{
				$assignedto_user['assigned_user_id'] = $wp_assigneduser_config['ecom_assignedto'];
			}
			else
			{
				$assignedto_user['assigned_user_id'] = $assignedto_old;
			}
			break;


		case 'wptigerpro':
			if( $ecom_RR_assignedto != 'Round Robin' )      
			{
				$assignedto_user['assigned_user_id'] = $wp_assigneduser_config['ecom_assignedto'];	
			}
			else
			{
				$assignedto_user['assigned_user_id'] = $assignedto_old;
			}
			break;

		case 'wpsalesforcepro':
			if( $ecom_RR_assignedto != 'Round Robin' )      
			{
				$assignedto_user['OwnerId'] = $wp_assigneduser_config['ecom_assignedto'];	
			}
			else
			{
				$assignedto_user['OwnerId'] = $assignedto_old;
			}
			break;

		case 'freshsales':
			if( $ecom_RR_assignedto != 'Round Robin' )
			{
				$assignedto_user['owner_id'] = $wp_assigneduser_config['ecom_assignedto'];
			}
			else
			{
				$assignedto_user['owner_id'] = $assignedto_old;
			}
			break;

		}
		return $assignedto_user;
	}


	public static function wp_get_mapping_assignedto($shortcode , $assignedto_old)
	{
		$wp_active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$wp_assigneduser_config = get_option( $shortcode );
		$module = $wp_assigneduser_config['third_module'];
		$tp_assignedto = $wp_assigneduser_config['thirdparty_assignedto'];		
		$assignedto_user = array();
		switch( $wp_active_crm )
		{
		case 'wpzohopro':
			if( $tp_assignedto != 'Round Robin' )
			{
				$assignedto_user['SMOWNERID'] = $wp_assigneduser_config['thirdparty_assignedto'];	
			}
			else
			{
				$assignedto_user['SMOWNERID'] = $assignedto_old;
			}
			break;

		case 'wpzohopluspro':
			if( $tp_assignedto != 'Round Robin' )
			{
				$assignedto_user['SMOWNERID'] = $wp_assigneduser_config['thirdparty_assignedto'];
			}
			else
			{
				$assignedto_user['SMOWNERID'] = $assignedto_old;
			}
			break;

		case 'wpsugarpro':
			if( $tp_assignedto != 'Round Robin' )
			{
				$assignedto_user['assigned_user_id'] = $wp_assigneduser_config['thirdparty_assignedto'];	
			}
			else
			{
				$assignedto_user['assigned_user_id'] = $assignedto_old;
			}
			break;

		case 'wpsuitepro':
			if( $tp_assignedto != 'Round Robin' )
			{
				$assignedto_user['assigned_user_id'] = $wp_assigneduser_config['thirdparty_assignedto'];
			}
			else
			{
				$assignedto_user['assigned_user_id'] = $assignedto_old;
			}
			break;


		case 'wptigerpro':
			if( $tp_assignedto != 'Round Robin' )
			{
				$assignedto_user['assigned_user_id'] = $wp_assigneduser_config['thirdparty_assignedto'];	
			}
			else
			{
				$assignedto_user['assigned_user_id'] = $assignedto_old;
			}


			break;

		case 'wpsalesforcepro':
			if( $tp_assignedto != 'Round Robin' )
			{
				$assignedto_user['OwnerId'] = $wp_assigneduser_config['thirdparty_assignedto'];	
			}
			else
			{
				$assignedto_user['OwnerId'] = $assignedto_old;
			}
			break;

		case 'freshsales':
			if( $tp_assignedto != 'Round Robin' )
			{
				$assignedto_user['owner_id'] = $wp_assigneduser_config['thirdparty_assignedto'];
			}
			else
			{
				$assignedto_user['owner_id'] = $assignedto_old;
			}
			break;

		}
		return $assignedto_user;
	}



	/*
	   Capture wordpress user on registration or creating a user from Wordpress Users
	 */
	//Register new user
	public static function capture_registering_users($user_id)
	{
		$posted_custom_fields = $_POST;
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = $HelperObj->Module;
		// $moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		// $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$config_user_capture = get_option("smack_{$activatedplugin}_user_capture_settings");

		//Code For RR
		$wp_active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$wp_assigneduser_config = get_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" );
		$wp_usersync_assignedto = $wp_assigneduser_config['usersync_assign_leads'];

		$assignedto_old = $wp_assigneduser_config['usersync_rr_value'];
		if( empty( $assignedto_old))
		{
			$get_first_usersync_owner = new PROFunctions();
			$get_first_user = $get_first_usersync_owner->getUsersList();
			$assignedto_old = $get_first_user['id'][0];
			$wp_assigneduser_config['usersync_rr_value'] = $assignedto_old;
			update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config );       
		}

		//END RR


		if( isset($config_user_capture['user_sync_module'])  )
		{
			$module = $config_user_capture['user_sync_module'];
			$duplicate_cancelled = 0;
			$duplicate_inserted = 0;
			$duplicate_updated = 0;
			$FunctionsObj = new PROFunctions();
			$posts = new CapturingProcessClassPRO();
			$post = $posts->mapUserCaptureFields( $module , $user_id , $assignedto_old );
			// $list=$post['list'];
			$user_email = "";
			$CheckEmailResult = array();
			$duplicate_option_check = $config_user_capture['smack_capture_duplicates'];
			if( isset( $post[$FunctionsObj->duplicateCheckEmailField()] ) )
			{
				if( $duplicate_option_check == 'skip_both' ){
					$CheckEmailResult_Leads = $FunctionsObj->checkEmailPresent('Leads' , $post[$FunctionsObj->duplicateCheckEmailField()]);
					$CheckEmailResult_Contacts = $FunctionsObj->checkEmailPresent('Contacts' , $post[$FunctionsObj->duplicateCheckEmailField()]);
					if( $CheckEmailResult_Leads == 1 || $CheckEmailResult_Contacts == 1 )
					{
						$CheckEmailResult = 1;
					}

				}else{
					$CheckEmailResult = $FunctionsObj->checkEmailPresent($module , $post[$FunctionsObj->duplicateCheckEmailField()] );
				}
				$user_email = $post[$FunctionsObj->duplicateCheckEmailField()];
			}


			if( ($CheckEmailResult == 1) && ( $config_user_capture['smack_capture_duplicates'] =='skip' || $config_user_capture['smack_capture_duplicates'] == 'skip_both' ))
			{
				$duplicate_cancelled++;
			}
			else
			{
				$result_id = $FunctionsObj->result_ids;
				$result_emails = $FunctionsObj->result_emails;
				if($config_user_capture['smack_capture_duplicates'] == 'update')
				{
					foreach( $result_emails as $key => $email )
					{
						if($email == $user_email)
						{
							$ids_present = $result_id[$key];
							$email_present = "yes";
						}
					}

					//      Update Code here
					if(isset($email_present) && ($email_present == "yes"))
					{
						$FunctionsObj->updateRecord( $module , $post , $ids_present );
						$duplicate_updated++;
					}
					else
					{
						$record = $FunctionsObj->createRecord( $module , $post);
						if($record['result'] == "success")
						{
							$duplicate_inserted++;
							if( $wp_usersync_assignedto == 'Round Robin' )
							{
								$new_assigned_val = new OverallFunctionsPRO();
								$new_assigned_val = $new_assigned_val->getRoundRobinOwner( $assignedto_old );
								$wp_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
								update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config);

							}							
						}
					}
				}
				else
				{
					$record = $FunctionsObj->createRecord( $module , $post);
					if($record['result'] == "success")
					{
						$data = "/$module entry is added./";
						if( $wp_usersync_assignedto == 'Round Robin' )
						{
							$new_assigned_val = new OverallFunctionsPRO();
							$new_assigned_val = $new_assigned_val->getRoundRobinOwner( $assignedto_old );
							$wp_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
							update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config);

						}     
					}
				}
			}
		}
	}

	// Updating old user in user profile
	public static function capture_updating_users($user_id)
	{
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = $HelperObj->Module;
		// $moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		// $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$config_user_capture = get_option("smack_{$activatedplugin}_user_capture_settings");
		$custom_plugin = get_option( 'custom_plugin' );

		//Code For RR
		$wp_active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$wp_assigneduser_config = get_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" );
		$wp_usersync_assignedto = $wp_assigneduser_config['usersync_assign_leads'];

		$assignedto_old = $wp_assigneduser_config['usersync_rr_value'];
		if( empty( $assignedto_old))
		{
			$get_first_usersync_owner = new PROFunctions();
			$get_first_user = $get_first_usersync_owner->getUsersList();
			$assignedto_old = $get_first_user['id'][0];
			$wp_assigneduser_config['usersync_rr_value'] = $assignedto_old;
			update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config );
		}

		//END RR



		if( isset($config_user_capture['user_sync_module'])  )
		{
			$module = $config_user_capture['user_sync_module'];
			$duplicate_cancelled = 0;
			$duplicate_inserted = 0;
			$duplicate_updated = 0;
			$FunctionsObj = new PROFunctions();
			$posts = new CapturingProcessClassPRO();
			$post = $posts->mapUserCaptureFields( $module , $user_id , $assignedto_old );
			// $list=$post['list'];
			$user_email = "";
			$CheckEmailResult = array();
			$duplicate_option_check = $config_user_capture['smack_capture_duplicates'] ;
			if( isset( $post[$FunctionsObj->duplicateCheckEmailField()] ) )
			{
				if( $duplicate_option_check == 'skip_both' ){
					$CheckEmailResult_Leads = $FunctionsObj->checkEmailPresent('Leads' , $post[$FunctionsObj->duplicateCheckEmailField()]);
					$CheckEmailResult_Contacts = $FunctionsObj->checkEmailPresent('Contacts' , $post[$FunctionsObj->duplicateCheckEmailField()]);

					if( $CheckEmailResult_Leads == 1 || $CheckEmailResult_Contacts == 1)
					{
						$CheckEmailResult = 1;
					}

				}else{

					$CheckEmailResult = $FunctionsObj->checkEmailPresent($module , $post[$FunctionsObj->duplicateCheckEmailField()] );

				}
				$user_email = $post[$FunctionsObj->duplicateCheckEmailField()];
			}

			if( ( $CheckEmailResult == 1) && ($duplicate_option_check =='skip' || $duplicate_option_check == 'skip_both' ))
			{
				$duplicate_cancelled++;
			}
			else
			{
				$result_id = $FunctionsObj->result_ids;
				$result_emails = $FunctionsObj->result_emails;
				if($config_user_capture['smack_capture_duplicates'] == 'update')
				{
					foreach( $result_emails as $key => $email )
					{
						if($email == $user_email)
						{
							$ids_present = $result_id[$key];
							$email_present = "yes";
						}
					}
					//      Update Code here
					if(isset($email_present) && ($email_present == "yes"))
					{
						$FunctionsObj->updateRecord( $module , $post , $ids_present );
						$duplicate_updated++;
					}
					else
					{
						$record = $FunctionsObj->createRecord( $module , $post);
						if($record['result'] == "success")
						{
							$duplicate_inserted++;
							if( $wp_usersync_assignedto == 'Round Robin' )
							{
								$new_assigned_val = new OverallFunctionsPRO();
								$new_assigned_val = $new_assigned_val->getRoundRobinOwner( $assignedto_old );
								$wp_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
								update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config);                                                                                         
							}
						}
					}
				}
				else
				{
					if( $custom_plugin == 'ultimate-member' ){
						foreach( $result_emails as $key => $email )
						{
							if($email == $user_email)
							{
								$ids_present = $result_id[$key];
								$email_present = "yes";
							}
						}
						//      Update Code here
						if(isset($email_present) && ($email_present == "yes"))
						{
							$FunctionsObj->updateRecord( $module , $post , $ids_present );
							$duplicate_updated++;
						}
						else
						{
							$record = $FunctionsObj->createRecord( $module , $post);
							if($record['result'] == "success")
							{
								$duplicate_inserted++;
								if( $wp_usersync_assignedto == 'Round Robin' )
								{
									$new_assigned_val = new OverallFunctionsPRO();
									$new_assigned_val = $new_assigned_val->getRoundRobinOwner( $assignedto_old );
									$wp_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
									update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config);
								}
							}
						}

					}else{
						$record = $FunctionsObj->createRecord( $module , $post);
						if($record['result'] == "success")
						{
							$data = "/$module entry is added./";
							if( $wp_usersync_assignedto == 'Round Robin' )
							{
								$new_assigned_val = new OverallFunctionsPRO();
								$new_assigned_val = $new_assigned_val->getRoundRobinOwner( $assignedto_old );
								$wp_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
								update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config);
							}

						}
					}
				}
			}
		}
	}

}
