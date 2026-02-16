<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

/******************************
 * Filename	: includes/WPCapture_includes_helper.php
 * Description	: Check
 * Author 	: check
 * Owner  	: smackcoders.com
 * Date   	: Mar11,2014
 */

class WPCapture_includes_helper_PRO {
	public $capturedId=0;
	public $ActivatedPlugin;
	public $ActivatedPluginLabel;
	public $Action;
	public $Module;
	public $ModuleSlug;
	public $instanceurl;
	public $accesstoken;
	public function __construct()
	{
		global $IncludedPluginsPRO;
		$ContactFormPluginsObj = new ContactFormPROPlugins();
		$this->ActivatedPlugin = $ContactFormPluginsObj->getActivePlugin();
		$activeplgin = isset($IncludedPluginsPRO[$this->ActivatedPlugin]) ? $IncludedPluginsPRO[$this->ActivatedPlugin] : '';
		$this->ActivatedPluginLabel = $activeplgin;
		if(isset( $_REQUEST['action'] ))
		{
			$this->Action = sanitize_text_field($_REQUEST['action']);
		}
		else
		{
			$this->Action = "";
		}
		if(isset($_REQUEST['module']))
		{
			$this->Module = sanitize_text_field($_REQUEST['module']);
		}
		else
		{
			$this->Module = "";
		}
		$this->ModuleSlug = rtrim( strtolower($this->Module) , "s");
	}

	public static function activate(){
		$WPCapture_includes_helper = new WPCapture_includes_helper_PRO();
		$WPCapture_includes_helper->initializeMigration();
		self::create_ecom_Table();

		$sync_array = array( 'user_sync_module' => 'Leads' , 'smack_capture_duplicates' => 'skip' );
		//Wpuser_assigned_to//new
		$wp_user_assignto = array( 'usersync_assign_leads' => '--Select--');
		update_option( 'smack_wptigerpro_usersync_assignedto_settings' , $wp_user_assignto );
		update_option( 'smack_wpsugarpro_usersync_assignedto_settings' , $wp_user_assignto );
		update_option( 'smack_wpzohopro_usersync_assignedto_settings' , $wp_user_assignto );
		update_option( 'smack_wpsalesforcepro_usersync_assignedto_settings' , $wp_user_assignto );
		update_option( 'smack_freshsales_usersync_assignedto_settings' , $wp_user_assignto );
		update_option( 'smack_wpsuitepro_usersync_assignedto_settings' , $wp_user_assignto );
		update_option( 'smack_wpzohopluspro_usersync_assignedto_settings' , $wp_user_assignto );

		//END wpuser_assigned_to
		update_option("smack_wptigerpro_user_capture_settings", $sync_array);
		update_option("smack_wpsugarpro_user_capture_settings", $sync_array);
		update_option("smack_wpzohopro_user_capture_settings", $sync_array);
		update_option("smack_wpsalesforcepro_user_capture_settings", $sync_array);
		update_option("smack_freshsales_user_capture_settings", $sync_array);
		update_option("smack_wpsuitepro_user_capture_settings", $sync_array);
		update_option("smack_wpzohopluspro_user_capture_settings", $sync_array);
		update_option("smack_joforce_user_capture_settings", $sync_array);

		update_option( "WpLeadThirdPartyPLugin" , "none" );
		update_option( "WpMappingModule" , "Leads" );
		update_option( "custom_plugin" , "none" );
		update_option( "Sync_value_on_off" , "On" );
		update_option( "ecom_wc_module" , "Not Enabled" );
		global $IncludedPluginsPRO , $DefaultActivePluginPRO ;
		$index = 0;
		$i = 0;
		foreach($IncludedPluginsPRO as $key => $value)
		{
			if($DefaultActivePluginPRO == $key)
			{
				update_option('WpLeadBuilderProActivatedPlugin' , $DefaultActivePluginPRO);
				$index = 1;
			}
			if( $i == 0 )
			{
				$firstplugin = $key;
			}
			$i++;
		}
		update_option("WpLeadBuilderProFirstTimeWarning" , "true");
		if($index == 0)
		{
			update_option( 'WpLeadBuilderProActivatedPlugin' , $firstplugin );
		}
		self::createPluginTables();
		self::createPluginTablesNew();
		self::checkVersion();
	}

	public static function deactivate(){

		//VTiger deactivation code
		global $IncludedPluginsPRO;
		global $wpdb;
		$droptablesettings = get_option('wp_droptablepro_settings');
		$droptable = isset($droptablesettings['droptable']) ? $droptablesettings['droptable'] : '';
		$usersync_assignedto_option_name = array();
		$wp_gravity_option_name = array();
		$wp_ninja_option_name = array();
		$wp_wpform_lite_option_name = array();
		$wp_wpform_pro_option_name = array();
		$wp_caldera_option_name = array();
		$wp_contact_option_name = array();

		$wp_contact_name_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%wp_contact%'"));
		foreach ($wp_contact_name_list as $value) {

			$wp_contact_option_name[]=$value->option_name;
		}

		$wp_gravity_name_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%wp_gravity%'"));
		foreach ($wp_gravity_name_list as $value) {
			$wp_gravity_option_name[]=$value->option_name;
		}

		$wp_ninja_name_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%wp_ninja%'"));
		foreach ($wp_ninja_name_list as $value) {
			$wp_ninja_option_name[]=$value->option_name;
		}

		$wp_wpform_lite_name_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%wp_wpform_lite%'"));
		foreach ($wp_wpform_lite_name_list as $value) {
			$wp_wpform_lite_option_name[]=$value->option_name;
		}
		$wp_wpform_pro_name_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%wp_wpform_pro%'"));
		foreach ($wp_wpform_pro_name_list as $value) {
			$wp_wpform_pro_option_name[]=$value->option_name;
		}

		$wp_caldera_name_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%wp_caldera%'"));
		foreach ($wp_caldera_name_list as $value) {
			$wp_caldera_option_name[]=$value->option_name;
		}
		$usersync_assignedto_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%usersync_assignedto%'"));
		foreach ($usersync_assignedto_list as $value) {

			$usersync_assignedto_option_name[]=$value->option_name;
		}
		$user_capture_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%user_capture%'"));
		foreach ($user_capture_list as $value) {

			$user_capture_option_name[]=$value->option_name;
		}
		$usersync_assignedto_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%usersync_assignedto%'"));
		foreach ($usersync_assignedto_list as $value) {

			$usersync_assignedto_option_name[]=$value->option_name;
		}
		$user_capture_list=$wpdb->get_results($wpdb->prepare("SELECT option_name from wp_options where option_name like '%user_capture%'"));
		foreach ($user_capture_list as $value) {

			$user_capture_option_name[]=$value->option_name;
		}
	
		$table_array = ['wp_smackleadbulider_shortcode_manager','wp_smack_ecom_info','wp_smackleadbulider_field_manager','wp_smackleadbulider_form_field_manager','wp_smackthirdpartyformfieldrelation','wp_smackformrelation','wp_smackleadbulider_formsubmission_record'];
		if(!empty($droptable) && $droptable == 'on'){
			

			foreach($wp_contact_option_name as $wp_contact_name) {
				delete_option($wp_contact_name);
			}
			foreach($wp_gravity_option_name as $wp_gravity_name) {
				delete_option($wp_gravity_name);
			}

			foreach($wp_ninja_option_name as $wp_ninja_name) {
				delete_option($wp_ninja_name);
			}
			foreach($wp_wpform_lite_option_name as $wp_wpform_lite_name) {
				delete_option($wp_wpform_lite_name);
			}
			foreach($wp_wpform_pro_option_name as $wp_wpform_pro_name) {
				delete_option($wp_wpform_pro_name);
			}

			foreach($wp_caldera_option_name as $wp_caldera_name) {
				delete_option($wp_caldera_name);
			}
			foreach($usersync_assignedto_option_name as $usersync_assignedto_name){
				delete_option($usersync_assignedto_name);
			}

			foreach($user_capture_option_name as $user_capture_name){
				delete_option($user_capture_name);
			}
			foreach($table_array as $table){
				$wpdb->query('DROP table ' . $table);
			}

			foreach( $IncludedPluginsPRO as $key => $value )
			{
				delete_option( "smack_{$key}_lead_post_field_settings" );
				delete_option( "smack_{$key}_lead_widget_field_settings" );
				delete_option( "smack_{$key}_lead_fields-tmp" );
				delete_option( "smack_{$key}_contact_fields-tmp" );
				delete_option( "smack_{$key}_list_fields-tmp" );
				delete_option( "wp_{$key}_settings" );
				delete_option( "smack_fields_shortcodes" );
			}

			delete_option("smack_oldversion_shortcodes");
			delete_option("WpLeadBuilderProFirstTimeWarning");
			delete_option('WpLeadThirdPartyPLugin');
			delete_option('WpMappingModule');
			delete_option('wp-leads-builder-for-any-crm-pro-version');
			delete_option('WpLeadBuilderProActivatedPlugin');
		}
	}

	public function initializeMigration(){
		$migratable_plugin = array( "wp-tiger-pro" => "wp-tiger-pro/wp-tiger-pro.php", "wp-sugar-pro" => "wp-sugar-pro/wp-sugar-pro.php" , "wp-tiger-free" => "wp-tiger/wp-tiger.php" , "wp-sugar-free" => "wp-sugar-free/wp-sugar-free.php" , "wp-zoho-free" => "wp-zoho-crm/wp-zoho-crm.php" ,  "wp-leads-builder-crm" => "wp-leads-builder-any-crm/index.php" );
		$active_plugins = get_option("active_plugins");
		foreach( $migratable_plugin as $key => $plugins_path )
		{
			if(in_array( $plugins_path , $active_plugins ))
			{
				$this->processMigration( $key , $plugins_path );
			}
		}
	}

	public static function checkVersion()
	{
		$wp_lead_builder_version = get_option( "wp-leads-builder-for-any-crm-pro-version" );
		update_option( 'wp-leads-builder-for-any-crm-pro-version' , SM_LB_VERSION );
		if( $wp_lead_builder_version == NULL || $wp_lead_builder_version == "" || !$wp_lead_builder_version )
		{
			self::createPluginTables();
			self::migrateFromOlderToNewerVersion();
		}
		if( $wp_lead_builder_version == NULL || $wp_lead_builder_version == "" || !$wp_lead_builder_version || $wp_lead_builder_version <= 1.1)
		{
			self::createPluginTablesNew();
		}
		if( $wp_lead_builder_version == NULL || $wp_lead_builder_version == "" || !$wp_lead_builder_version || $wp_lead_builder_version < 2.0)
		{
			self::altershortcodetable();  // Add column for Round Robin method Assigned to
			self::migrateForFreshsalesSupport(); // Add column for base_model
			self::generateWCItables(); //Generate Customer Insight Tables
			self::generateCampaignTables();//Generate table for Campaign
		}
	}

	public static function createPluginTables()
	{
		global $wpdb;
		$wpdb->query("CREATE TABLE IF NOT EXISTS `wp_smackleadbulider_shortcode_manager` (
					`shortcode_id` int(11) NOT NULL AUTO_INCREMENT,
					`shortcode_name` varchar(10) NOT NULL,
					`old_shortcode_name` varchar(255) DEFAULT NULL,
					`form_type` varchar(10) NOT NULL,
					`assigned_to` varchar(200) NOT NULL,
					`error_message` text NOT NULL,
					`success_message` text NOT NULL,
					`submit_count` int(10) NOT NULL DEFAULT '0',
					`success_count` int(10) NOT NULL DEFAULT '0',
					`failure_count` int(10) NOT NULL DEFAULT '0',
					`is_redirection` tinyint(1) NOT NULL,
					`url_redirection` varchar(255) NOT NULL,
					`duplicate_handling` varchar(10) NOT NULL DEFAULT 'none',
					`google_captcha` tinyint(1) NOT NULL,
					`module` varchar(25) NOT NULL,
					`Round_Robin` varchar(50) NOT NULL,
					`crm_type` varchar(25) NOT NULL,
					PRIMARY KEY (`shortcode_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
	    $wpdb->query("CREATE TABLE IF NOT EXISTS `wp_smackleadbulider_formsubmission_record` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`form_id` int(11) NOT NULL,
					`shortcode_name` varchar(10) NOT NULL,
					`meta_key` varchar(255) NOT NULL,
					`meta_value` longtext NOT NULL,
					`crm_id` int(11) NOT NULL,
					`module` varchar(20) NOT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		$wpdb->query("CREATE TABLE IF NOT EXISTS `wp_smackleadbulider_field_manager` (
					`field_id` int(11) NOT NULL AUTO_INCREMENT,
					`field_name` varchar(50) NOT NULL,
					`field_label` varchar(50) NOT NULL,
					`field_type` varchar(20) NOT NULL,
					`field_values` longtext NOT NULL,
					`field_default` text NOT NULL,
					`module_type` varchar(20) NOT NULL,
					`field_mandatory` varchar(10) NOT NULL,
					`crm_type` varchar(25) NOT NULL,
					`field_sequence` int(10) NOT NULL,
					`base_model` varchar(20) NOT NULL,
					PRIMARY KEY (`field_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");

		$wpdb->query("CREATE TABLE IF NOT EXISTS `wp_smackleadbulider_form_field_manager` (
					`rel_id` int(11) NOT NULL AUTO_INCREMENT,
					`shortcode_id` int(11) NOT NULL,
					`field_id` int(11) NOT NULL,
					`wp_field_mandatory` varchar(10) NOT NULL,
					`state` varchar(10) NOT NULL,
					`custom_field_type` varchar(20) NOT NULL,
					`custom_field_values` longtext NOT NULL,
					`custom_field_default` text NOT NULL,
					`form_field_sequence` int(3) NOT NULL,
					`display_label` varchar(50) NOT NULL,
					PRIMARY KEY (`rel_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
	}

	public static function createPluginTablesNew()
	{
		global $wpdb;
		//new table for form relation with third party plugins
		$wpdb->query("CREATE TABLE IF NOT EXISTS `wp_smackformrelation` (
					`id` int(6) unsigned NOT NULL AUTO_INCREMENT,
					`shortcode` varchar(30) NOT NULL,
					`thirdparty` varchar(30) NOT NULL,
					`thirdpartyid` int(50) DEFAULT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8
		");

		//create table for form field relations
		$wpdb->query("CREATE TABLE IF NOT EXISTS `wp_smackthirdpartyformfieldrelation` (
					`id` int(6) unsigned NOT NULL AUTO_INCREMENT,
					`smackshortcodename` varchar(30) NOT NULL,
					`smackfieldid` int(20) DEFAULT NULL,
					`smackfieldslable` varchar(30) NOT NULL,
					`thirdpartypluginname` varchar(30) NOT NULL,
					`thirdpartyformid` int(50) DEFAULT NULL,
					`thirdpartyfieldids` varchar(50) DEFAULT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8
		");
	}

	public static function create_ecom_Table()
	{
		global $wpdb;
		$wpdb->query("CREATE TABLE IF NOT EXISTS `wp_smack_ecom_info` (
			  id int(6) unsigned NOT NULL AUTO_INCREMENT,
			  crmid varchar(100) DEFAULT NULL,
			  crm_name varchar(100) NOT NULL,
			  wp_user_id varchar(100) NOT NULL,
			  is_user int(30) NOT NULL,
			  lead_no varchar(100) DEFAULT NULL,
			  product_id varchar(100) DEFAULT NULL,
			  contact_no varchar(100) DEFAULT NULL,
			  order_id varchar(100) DEFAULT NULL,
			  sales_orderid varchar(100) DEFAULT NULL,		
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8
		");
	}

	//Customer Insight tables
	public static function generateWCItables()
	{
		global $wpdb;
		$woo_customer_insight_info ="CREATE TABLE IF NOT EXISTS wci_activity (
									id int(20) NOT NULL AUTO_INCREMENT,
									session_id int(20) NOT NULL,
									session_key varchar(200) NOT NULL,
									user_id  varchar(200) DEFAULT NULL,
									user_email varchar(200) DEFAULT NULL,
									is_user int(10) DEFAULT NULL,
									user_ip varchar(30) DEFAULT NULL,
									country varchar(200) DEFAULT NULL,
									date date DEFAULT NULL,
									information LONGBLOB DEFAULT NULL,
									visited_url LONGBLOB DEFAULT NULL,
									page_id int(20) DEFAULT NULL,
									page_title varchar(200) DEFAULT NULL,
									spent_time int(20) DEFAULT NULL,
									clicked_button varchar(100) DEFAULT NULL,
									date_time datetime DEFAULT NULL,
									PRIMARY KEY (id)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query($woo_customer_insight_info);

		$create_btn_click_table =  "CREATE TABLE IF NOT EXISTS wci_events (
									id int(20) NOT NULL AUTO_INCREMENT,
									session_id int(20) NOT NULL,
									session_key varchar(200) NOT NULL,
									user_id varchar(200) DEFAULT NULL,
									user_email varchar(200) DEFAULT NULL,
									user_ip varchar(40) DEFAULT NULL,
									country varchar(100) DEFAULT NULL,
									prod_id int(20) DEFAULT NULL,
									product LONGBLOB DEFAULT NULL,
									button_name varchar(100) DEFAULT NULL,
									page_url LONGBLOB DEFAULT NULL,
									date datetime DEFAULT '0000-00-00 00-00-00',
									count int(30) DEFAULT NULL,
									date_without_time date DEFAULT NULL,
									PRIMARY KEY (id)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query($create_btn_click_table);

		$create_usr_profile =  "CREATE TABLE IF NOT EXISTS wci_history (
								id int(20) NOT NULL AUTO_INCREMENT,
								user_id int(30) DEFAULT NULL,
								user_name varchar(200) DEFAULT NULL,
								email varchar(200) DEFAULT NULL,
								date datetime DEFAULT '0000-00-00 00-00-00',
								role varchar(100) DEFAULT NULL,
								login_time datetime DEFAULT '0000-00-00 00-00-00',
								logout_time datetime DEFAULT '0000-00-00 00-00-00',
								status varchar(100) DEFAULT NULL,
								PRIMARY KEY (id)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query($create_usr_profile);

		$wci_user_purchased_history =  "CREATE TABLE IF NOT EXISTS wci_user_purchased_history (
										id int(20) NOT NULL AUTO_INCREMENT,
										user_ip varchar(30) DEFAULT NULL,
										user_id varchar(20) DEFAULT NULL,
										user_email varchar(100) DEFAULT NULL,
										product_id int(50) DEFAULT NULL,
										product_name varchar(200) DEFAULT NULL,
										PRIMARY KEY (id)
										) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query($wci_user_purchased_history);

		$wci_create_usr_profile_updated =  "CREATE TABLE IF NOT EXISTS wci_user_profile_updated (
											id int(20) NOT NULL AUTO_INCREMENT,
											user_id int(20) UNIQUE NOT NULL,
											user_name varchar(200) DEFAULT NULL,
											email varchar(200) DEFAULT NULL,
											date datetime DEFAULT '0000-00-00 00-00-00',
											role varchar(30) DEFAULT NULL,
											login_time datetime DEFAULT '0000-00-00 00-00-00',
											logout_time datetime DEFAULT '0000-00-00 00-00-00',
											PRIMARY KEY (id)
												) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query( $wci_create_usr_profile_updated );

		$wci_abandon_cart = "CREATE TABLE IF NOT EXISTS wci_abandon_cart (
							id int(20) NOT NULL AUTO_INCREMENT,
							user_email varchar(100) NOT NULL,
							order_id int(30) UNIQUE NOT NULL,
							date datetime DEFAULT NULL,
							time_difference int(30) DEFAULT NULL,
							PRIMARY KEY (id)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query($wci_abandon_cart);

		$wci_successful_purchases = "CREATE TABLE IF NOT EXISTS wci_successful_purchases (
									id int(20) NOT NULL AUTO_INCREMENT,
									user_name varchar(200) NOT NULL,
									user_email varchar(200) NOT NULL,
									order_id int(30) UNIQUE NOT NULL,
									order_status varchar(30) NOT NULL,
									date datetime DEFAULT NULL,
									products LONGBLOB DEFAULT NULL,
									coupon_code varchar(100) DEFAULT NULL,
									coupon_amount varchar(100) DEFAULT NULL,
									total_price int(30) DEFAULT NULL,
									discount_type varchar(100) DEFAULT NULL,
									PRIMARY KEY (id)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query($wci_successful_purchases);

		$wci_user_session = "CREATE TABLE IF NOT EXISTS wci_user_session (
							id int(40) NOT NULL AUTO_INCREMENT,
							session_id int(20) NOT NULL,
							user_id varchar(200) NOT NULL,
							user_name varchar(200) NOT NULL,
							country varchar(100) NOT NULL,
							is_cart int(10) NOT NULL,
							product_key varchar(200) NOT NULL,
							product_data LONGBLOB DEFAULT NULL,
							is_checkout int(10) NOT NULL,
							is_payment int(10) NOT NULL,
							order_id int(30) NOT NULL,
							payment_success int(20) NOT NULL,
							payment_failure int(20) NOT NULL,
							session_value LONGBLOB DEFAULT NULL,
							date datetime DEFAULT '0000-00-00 00-00-00',
							PRIMARY KEY (id)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query($wci_user_session);

		$wci_maintain_session ="CREATE TABLE IF NOT EXISTS wci_maintain_session (
								session_id bigint(20) NOT NULL AUTO_INCREMENT,
								session_key char(32) NOT NULL,
								user_id char(32) NOT NULL,
								session_value longtext NOT NULL,
								session_expiry bigint(20) NOT NULL,
								UNIQUE KEY session_id (session_id),
								PRIMARY KEY  (session_key)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query( $wci_maintain_session );		
	}

	public static function generateCampaignTables()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE smack_campaign_info (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				campaign_id varchar(100) DEFAULT NULL,
				campaign_name varchar(100) DEFAULT NULL,
				campaign_details LONGBLOB DEFAULT NULL,
				mailing_source varchar(50) DEFAULT NULL,
				date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";
		$wpdb->query($sql);
	}

	public static function altershortcodetable()
	{
		global $wpdb;
		$result = $wpdb->query("SHOW COLUMNS FROM `{$wpdb->prefix}smackleadbulider_shortcode_manager` LIKE 'Round_Robin'");
		if($result == 0){
			$wpdb->query( "ALTER table wp_smackleadbulider_shortcode_manager ADD Round_Robin varchar(50)");
		}
	}

	public static function migrateForFreshsalesSupport() {
		global $wpdb;
		$result = $wpdb->query("SHOW COLUMNS FROM `{$wpdb->prefix}smackleadbulider_field_manager` LIKE 'base_model'");
		if($result == 0){
			$wpdb->query( "ALTER table wp_smackleadbulider_field_manager ADD base_model varchar(20)");
		}
	}

	public static function migrateFromOlderToNewerVersion()
	{
		global $wpdb;
		$ProPluginMap = Array(
			"wptigerpro",
			"wpsugarpro",
			"wpzohopro",
			"wpsalesforcepro",
		);

		foreach( $ProPluginMap as $pluginslug )
		{
			$fields=[];
			$field_array_core=[];
			$leadmodule_tmp = get_option("smack_{$pluginslug}_lead_fields-tmp");
			$module_type = "Leads";
			$crm_type = $pluginslug;
			if(!empty($leadmodule_tmp['fields'])){
				foreach( $leadmodule_tmp['fields'] as $field_array )
				{
					$field_name = $field_array['name'];
					$field_type = $field_array['type']['name'];
					$field_label = $field_array['display_label'];
					$field_values = serialize($field_array['type']['picklistValues']);
					if( $field_array['mandatory'] == 2 )
					{
						$field_mandatory = 1;
					}
					else
					{
						$field_mandatory = 0;
					}
					$field_sequence = $field_array['order'];
					$fields[$pluginslug]['Leads'] = $wpdb->insert( 'wp_smackleadbulider_field_manager' , array( 'field_name' => $field_name, 'field_label' => $field_label , 'field_type' => $field_type , 'field_values' => $field_values, 'module_type' => $module_type, 'field_mandatory' => $field_mandatory, 'crm_type' => $crm_type , 'field_sequence' => $field_sequence ) );
					$field_id = $wpdb->insert_id;
					$field_array['field_id'] = $field_id;
					$field_array_core[$pluginslug]['Leads'][$field_name] = $field_array;
				}
			}

			$module_type = "Contacts";
			$crm_type = $pluginslug;
			if(!empty($leadmodule_tmp['fields'])){
				foreach( $leadmodule_tmp['fields'] as $field_array )
				{
					$field_name = $field_array['name'];
					$field_type = $field_array['type']['name'];
					$field_label = $field_array['display_label'];
					$field_values = serialize($field_array['type']['picklistValues']);
					if( $field_array['mandatory'] == 2 )
					{
						$field_mandatory = 1;
					}
					else
					{
						$field_mandatory = 0;
					}
					$field_sequence = $field_array['order'] + 1;
					$fields[$pluginslug]['Contacts'] = $wpdb->insert('wp_smackleadbulider_field_manager' , array( 'field_name' => $field_name, 'field_label' => $field_label, 'field_type' => $field_type, 'field_values' => $field_values , 'module_type' => $module_type, 'field_mandatory' => $field_mandatory, 'crm_type' => $crm_type, 'field_sequence' => $field_sequence ) );
					$field_id = $wpdb->insert_id;
					$field_array['field_id'] = $field_id;
					$field_array_core[$pluginslug]['Contacts'][$field_name] = $field_array ;
				}
			}

			$get_user_sync = get_option("smack_{$pluginslug}_user_capture_settings");
			// $get_user_sync['user_sync_module'] = "none";
			update_option( "smack_{$pluginslug}_user_capture_settings" , $get_user_sync );
		}
		$Shortcode_manager_config = get_option( "smack_fields_shortcodes" );
		if(!empty($Shortcode_manager_config)){
			foreach ( $Shortcode_manager_config as $shortcode_name => $shortcode_option )
			{
				$form_type = $shortcode_option['isWidget'];
				$assigned_to = $shortcode_option['assignedto'];
				$error_message = $shortcode_option['errormessage'];
				$success_message = $shortcode_option['successmessage'];
				if( isset( $shortcode_option['enableurlredirection'] ) && ($shortcode_option['enableurlredirection'] == "on") )
				{
					$is_redirection = 1;
					$url_redirection = $shortcode_option['redirecturl'];
				}
				if( isset( $shortcode_option['enablecaptcha'] ) && ($shortcode_option['enablecaptcha'] == "on") )
				{
					$google_captcha = 1;
				}
				$module = $shortcode_option['module'];
				$crm_type = $shortcode_option['crm'];
				$wpdb->insert( 'wp_smackleadbulider_shortcode_manager' , array( 'shortcode_name' => $shortcode_name , 'form_type' => $form_type , 'assigned_to' => $assigned_to, 'error_message' => $error_message, 'success_message' => $success_message, 'is_redirection' => $is_redirection, 'url_redirection' => $url_redirection, 'google_captcha' => $google_captcha , 'module' => $module, 'crm_type' => $crm_type ) );
				$shortcode_id = $wpdb->insert_id;
				foreach( $shortcode_option['fields'] as $key => $field_array )
				{
					$field_id = $field_array_core[$crm_type][$module][$field_array['name']]['field_id'];
					$wp_field_mandatory = $field_array['wp_mandatory'];
					$state = $field_array['publish'];
					$custom_field_type = $field_array['type']['name'];
					$custom_field_values = serialize($field_array['type']['picklistValues']);
					$form_field_sequence = $field_array['order'];
					$display_label = $field_array['display_label'];
					$wpdb->insert( 'wp_smackleadbulider_form_field_manager' , array( 'shortcode_id' => $shortcode_id, 'field_id' => $field_id, 'wp_field_mandatory' => $wp_field_mandatory, 'state' => $state, 'custom_field_type' => $custom_field_type , 'custom_field_values' => $custom_field_values , 'form_field_sequence' => $form_field_sequence , 'display_label' => $display_label ) );
				}
			}

		}

		$user_details = array();
		$wptigerpro_settings = get_option("wp_wptigerpro_settings");
		if( is_array( $wptigerpro_settings ) )
		{
			if(!class_exists("Vtiger_WSClient"))
			{
				include_once(SM_LB_PRO_DIR."./lib/vtwsclib/Vtiger/WSClient.php");
			}
			$url = $wptigerpro_settings['url'];
			$username = $wptigerpro_settings['username'];
			$accesskey = $wptigerpro_settings['accesskey'];
			$client = new Vtiger_WSClient($url);
			$login = $client->doLogin($username, $accesskey);
			if (!$login) {

			} else {
				$query = "select user_name, id, first_name, last_name  from Users";
				$records = $client->doQuery($query);
				if($records) {
					$columns = $client->getResultColumns($records);
					foreach($records as $record) {
						$user_details['user_name'][] = $record['user_name'];
						$user_details['id'][] = $record['id'];
						$user_details['first_name'][] = $record['first_name'];
						$user_details['last_name'][] = $record['last_name'];
					}
				}
			}
			$crmusers = get_option( "crm_users" );
			$crmusers['wptigerpro'] = $user_details;
			update_option("crm_users" , $crmusers );
		}
		$user_details = array();
		$wpsugarpro_settings = get_option("wp_wpsugarpro_settings");
		if( is_array( $wpsugarpro_settings ) )
		{
			if(!defined('sugarEntry') || !sugarEntry)
			{
				define('sugarEntry', TRUE);
				include_once(SM_LB_PRO_DIR.'lib/nusoap/nusoap.php');
			}
			$client = new nusoapclient($wpsugarpro_settings['url'].'/soap.php?wsdl',true);
			$user_auth = array(
				'user_auth' => array(
					'user_name' => $wpsugarpro_settings['username'],
					'password' => md5($wpsugarpro_settings['password']),
					'version' => '0.1'
				),
				'application_name' => 'wp-sugar-pro'
			);
			$login = $client->call('login',$user_auth);
			$session_id = $login['id'];
			$client_array = array( 'login' => $login , 'session_id' => $session_id , "clientObj" => $client );
			$client = $client_array['clientObj'];
			$recordInfo = $client->call('user_list', array('user_name' => $wpsugarpro_settings['username'] , 'password' => md5($wpsugarpro_settings['password'])));
			$userindex = 0;
			if(is_array($recordInfo))
				foreach($recordInfo as $record)
				{
					$user_details['user_name'][$userindex] = $record['user_name'];
					$user_details['id'][$userindex] = $record['id'];
					$user_details['first_name'][$userindex] = $record['first_name'];
					$user_details['last_name'][$userindex] = $record['last_name'];
					$userindex++;
				}
			$crmusers = get_option( "crm_users" );
			$crmusers['wpsugarpro'] = $user_details;
			update_option("crm_users" , $crmusers );
		}
		$user_details = array();
		$wpzohopro_settings = get_option("wp_wpzohopro_settings");
		if( is_array( $wpzohopro_settings ) )
		{
			if( !class_exists( "SmackZohoApi" ) )
			{
				include_once(SM_LB_PRO_DIR.'lib/SmackZohoApi.php');
			}
			$client = new SmackZohoApi();
			$authtoken = $wpzohopro_settings['authtoken'];
			$extraparams = "&type=ActiveUsers";
			$records = $client->getRecords( "Users" , "getUsers" , $authtoken , "" , "" , $extraparams );
			if( isset( $records['user']['@attributes'] ) ) {
			{
				$user_details['user_name'][] = $records['user']['@attributes']['email'];
				$user_details['id'][] = $records['user']['@attributes']['id'];
				$user_details['first_name'][] = $records['user']['@attributes']['email'];
				$user_details['last_name'][] = "";
			}
			}
			else
			{
				foreach($records['user'] as $record) {
					$user_details['user_name'][] = $record['@attributes']['email'];
					$user_details['id'][] = $record['@attributes']['id'];
					$user_details['first_name'][] = $record['@attributes']['email']; 
					$user_details['last_name'][] = ""; 
				}
			}
			$crmusers = get_option( "crm_users" );
			$crmusers['wpzohopro'] = $user_details;
			update_option("crm_users" , $crmusers );
		}
		$user_details = array();
		$wpsalesforcepro_settings = get_option( "wp_wpsalesforcepro_settings" );
		if( is_array( $wpsalesforcepro_settings ) )
		{
			if(!function_exists("Getaccess_token"))
			{
				include_once(SM_LB_PRO_DIR.'lib/SmackSalesForceApi.php');
			}
			$records = Getuser( $this->instanceurl, $this->accesstoken );
			foreach($records['recentItems'] as $record) {
				$user_details['user_name'][] = $record['Name'] ;
				$Name = explode(" ",$record['Name']);
				$user_details['first_name'][]= $Name[0];
				$user_details['last_name'][] = $Name[1];
				$user_details['id'][] = $record['Id'];
			}
			$crmusers = get_option( "crm_users" );
			$crmusers['wpsalesforcepro'] = $user_details;
			update_option("crm_users" , $crmusers );
		}
	}

	public function processMigration( $key , $plugins_path )
	{
		switch( $key )
		{
		case "wp-tiger-pro":
			$this->migrateWpTigerPro();
			break;
		case "wp-sugar-pro":
			$this->migrateWpSugarPro();
			break;
		case "wp-tiger-free":
			$this->migrateWpTigerFree();
			break;
		case "wp-sugar-free":
			$this->migrateWpSugarFree();
			break;
		case "wp-zoho-free":
			$this->migrateWpZohoFree();
			break;
		case "wp-leads-builder-crm":
			$this->migrateWpLeadsBuilderCrm();
			break;
		}
	}

	public function migrateWpTigerPro()
	{
		$recatcha_settings=[];
		$settings=[];
		$user_capture_settings=[];
		$config_contact_shortcodes = get_option("smack_fields_shortcodes");
		if(!is_array($config_contact_shortcodes))
		{
			$config_contact_shortcodes = array();
		}
		$smack_wp_tiger_settings = get_option( 'smack_wp_tiger_settings' );
		$smack_wp_tiger_fields_shortcodes = get_option( 'smack_wp_vtiger_fields_shortcodes' );
		$smack_wp_tiger_user_capture_settings = get_option( 'smack_wp_tiger_user_capture_settings' );
		$smack_wp_tiger_contact_fields_tmp = get_option( 'smack_wp_vtiger_contact_fields-tmp' );
		$smack_wp_tiger_lead_fields_tmp = get_option( 'smack_wp_vtiger_lead_fields-tmp' );
		$migrationmap = array();
		foreach( $smack_wp_tiger_fields_shortcodes as $key => $field_settings )
		{
			if($field_settings['isWidget'] == 1)
			{
				$field_settings['formtype'] = "widget";
			}
			else
			{
				$field_settings['formtype'] = "post";
			}
			$field_settings['crm'] = "wptigerpro";
			unset($field_settings['isWidget']);
			$oldrandam = $key;
			if( !array_key_exists($key , $config_contact_shortcodes) )
			{
				$config_contact_shortcodes[$key] = $field_settings;
				$migrationmap['wp-tiger-pro-form'][] = array(
					"name" => "wptigerpro",
					"newrandomname" => $key,
					"oldrandomname" => "$oldrandam",
					"crm" => "wp-tiger-pro");
			}
			else
			{
				$Randomstring = $this->CreateNewFieldShortcode("wptigerpro" , "Leads");
				$config_contact_shortcodes[$Randomstring] = $field_settings;
				$migrationmap['wp-tiger-pro-form'][] = array(
					"name" => "wptigerpro",
					"newrandomname" => $key,
					"oldrandomname" => "$oldrandam",
					"crm" => "wp-tiger-pro");
			}
			update_option("smack_oldversion_shortcodes" , $migrationmap);
			update_option("smack_fields_shortcodes" , $config_contact_shortcodes);
		}
		if( is_array($smack_wp_tiger_contact_fields_tmp) )
		{
			if($smack_wp_tiger_contact_fields_tmp['isWidget'] == 1)
			{
				$smack_wp_tiger_contact_fields_tmp['formtype'] = "widget";
			}
			else
			{
				$smack_wp_tiger_contact_fields_tmp['formtype'] = "post";
			}
			$smack_wp_tiger_contact_fields_tmp['crm'] = "wptigerpro";
			unset($smack_wp_tiger_contact_fields_tmp['isWidget']);
			update_option("smack_wptigerpro_contact_fields-tmp" , $smack_wp_tiger_contact_fields_tmp );
		}
		if( is_array($smack_wp_tiger_lead_fields_tmp) )
		{
			if($smack_wp_tiger_lead_fields_tmp['isWidget'] == 1)
			{
				$smack_wp_tiger_lead_fields_tmp['formtype'] = "widget";
			}
			else
			{
				$smack_wp_tiger_lead_fields_tmp['formtype'] = "post";
			}
			$smack_wp_tiger_lead_fields_tmp['crm'] = "wptigerpro";
			unset($smack_wp_tiger_lead_fields_tmp['isWidget']);
			update_option("smack_wptigerpro_lead_fields-tmp" , $smack_wp_tiger_lead_fields_tmp );
		}
		if(is_array($smack_wp_tiger_settings))
		{
			$fieldNames = array(
				'url' => 'smack_host_address',
				'username' => 'smack_host_username',
				'accesskey' => 'smack_host_access_key',
			);
			$recaptcha_field_values = array(
				'smack_recaptcha' => "smack_recaptcha",
				'recaptcha_public_key' => "smack_public_key",
				'recaptcha_private_key' => "smack_private_key",
			);
			foreach( $recaptcha_field_values as $key => $value )
			{
				if(isset($smack_wp_tiger_settings[$value]))
				{
					$recatcha_settings[$key] = $smack_wp_tiger_settings[$value];
				}
			}
			update_option("wp_wptigerpro_captcha_settings", $recatcha_settings);
			foreach($fieldNames as $key => $value)
			{
				if(isset($smack_wp_tiger_settings[$value]))
				{
					$settings[$key] = $smack_wp_tiger_settings[$value];
				}
			}
			update_option("wp_wptigerpro_settings", $settings );
		}
		if(is_array($smack_wp_tiger_user_capture_settings))
		{
			if(isset($smack_wp_tiger_user_capture_settings['smack_user_capture']) && ( $smack_wp_tiger_user_capture_settings['smack_user_capture'] == "on" ))
			{
				$user_capture_settings['smack_user_capture'] = "on";
			}
			$user_capture_settings['smack_capture_duplicates'] = $smack_wp_tiger_user_capture_settings['smack_capture_duplicates'];
			update_option( "smack_wptigerpro_user_capture_settings" , $user_capture_settings );
		}
	}

	public function migrateWpSugarPro()
	{
		$recatcha_settings=[];
		$user_capture_settings=[];
		$settings=[];
		$config_contact_shortcodes = get_option("smack_fields_shortcodes");
		$smack_wp_sugar_settings = get_option( 'smack_wp_sugar_settings' );
		$smack_wp_sugar_fields_shortcodes = get_option( 'smack_wp_sugar_fields_shortcodes' );
		$smack_wp_sugar_user_capture_settings = get_option( 'smack_wp_sugar_user_capture_settings' );
		$smack_wp_sugar_contact_fields_tmp = get_option( 'smack_wp_sugar_contact_fields-tmp' );
		$smack_wp_sugar_lead_fields_tmp = get_option( 'smack_wp_sugar_lead_fields-tmp' );
		$migrationmap = get_option("smack_oldversion_shortcodes");
		if(!is_array($migrationmap))
		{
			$migrationmap = array();
		}
		foreach( $smack_wp_sugar_fields_shortcodes as $key => $field_settings )
		{
			if($field_settings['isWidget'] == 1)
			{
				$field_settings['formtype'] = "widget";
			}
			else
			{
				$field_settings['formtype'] = "post";
			}
			unset($field_settings['isWidget']);
			$field_settings['crm'] = "wpsugarpro";
			$oldrandam = $key;
			if( !array_key_exists($key , $config_contact_shortcodes) )
			{
				$config_contact_shortcodes[$key] = $field_settings;
				$migrationmap['sugar-web-form'][] = array(
					"name" => "wpsugarpro",
					"newrandomname" => $key,
					"oldrandomname" => "$oldrandam",
					"crm" => "wp-sugar-pro");
			}
			else
			{
				$Randomstring = $this->CreateNewFieldShortcode(  "wpsugarpro" , "Leads"  );
				$config_contact_shortcodes[$Randomstring] = $field_settings;
				$migrationmap["sugar-web-form"][] = array(
					"name" => "wpsugarpro",
					"newrandomname" => "$Randomstring",
					"oldrandomname" => "$oldrandam",
					"crm" => "wp-sugar-pro");
			}
			update_option("smack_oldversion_shortcodes" , $migrationmap);
			update_option("smack_fields_shortcodes" , $config_contact_shortcodes);
		}
		if( is_array($smack_wp_sugar_contact_fields_tmp) )
		{
			if($smack_wp_sugar_contact_fields_tmp['isWidget'] == 1)
			{
				$smack_wp_sugar_contact_fields_tmp['formtype'] = "widget";
			}
			else
			{
				$smack_wp_sugar_contact_fields_tmp['formtype'] = "post";
			}
			$smack_wp_sugar_contact_fields_tmp['crm'] = "wpsugarpro";
			if($smack_wp_sugar_contact_fields_tmp['check_duplicate'] == 0)
			{
				$smack_wp_sugar_contact_fields_tmp['check_duplicate'] = "none";
			}
			else
			{
				$smack_wp_sugar_contact_fields_tmp['check_duplicate'] = "skip";
			}
			unset($smack_wp_sugar_contact_fields_tmp['isWidget']);
			update_option("smack_wpsugarpro_contact_fields-tmp" , $smack_wp_sugar_contact_fields_tmp );
		}
		if( is_array($smack_wp_sugar_lead_fields_tmp) )
		{
			if($smack_wp_sugar_lead_fields_tmp['isWidget'] == 1)
			{
				$smack_wp_sugar_lead_fields_tmp['formtype'] = "widget";
			}
			else
			{
				$smack_wp_sugar_lead_fields_tmp['formtype'] = "post";
			}
			$smack_wp_sugar_lead_fields_tmp['crm'] = "wpsugarpro";
			unset($smack_wp_sugar_lead_fields_tmp['isWidget']);
			if($smack_wp_sugar_lead_fields_tmp['check_duplicate'] == 0)
			{
				$smack_wp_sugar_lead_fields_tmp['check_duplicate'] = "none";
			}
			else
			{
				$smack_wp_sugar_lead_fields_tmp['check_duplicate'] = "skip";
			}
			update_option("smack_wpsugarpro_lead_fields-tmp" , $smack_wp_sugar_lead_fields_tmp );
		}
		if(is_array($smack_wp_sugar_settings))
		{
			$fieldNames = array(
				'url' => 'smack_host_address',
				'username' => 'smack_host_username',
				'password' => 'smack_host_access_key',
			);
			$recaptcha_field_values = array(
				'smack_recaptcha' => "smack_recaptcha",
				'recaptcha_public_key' => "smack_public_key",
				'recaptcha_private_key' => "smack_private_key",
			);
			foreach( $recaptcha_field_values as $key => $value )
			{
				if(isset($smack_wp_sugar_settings[$value]))
				{
					$recatcha_settings[$key] = $smack_wp_sugar_settings[$value];
				}
			}
			update_option("wp_wpsugarpro_captcha_settings", $recatcha_settings);
			foreach($fieldNames as $key => $value)
			{
				if(isset( $smack_wp_sugar_settings[$value] ))
				{
					$settings[$key] = $smack_wp_sugar_settings[$value];
				}
			}
			update_option("wp_wpsugarpro_settings", $settings );
		}
		if(is_array($smack_wp_sugar_user_capture_settings))
		{
			if(isset($smack_wp_sugar_user_capture_settings['smack_user_capture']) && ( $smack_wp_sugar_user_capture_settings['smack_user_capture'] == "on" ))
			{
				$user_capture_settings['smack_user_capture'] = "on";
			}
			if(isset($smack_wp_sugar_user_capture_settings['smack_capture_duplicates']) && ( $smack_wp_sugar_user_capture_settings['smack_capture_duplicates'] == "on" ))
			{
				$user_capture_settings['smack_capture_duplicates'] = "skip";
			}
			else
			{
				$user_capture_settings['smack_capture_duplicates'] = "none";
			}
			update_option( "smack_wpsugarpro_user_capture_settings" , $user_capture_settings );
		}
	}

	public function migrateWpTigerFree()
	{
		$Fields_by_FieldName=[];
		$db_fields=[];
		$config_fields=[];
		$fieldNames_values=[];
		$config_contact_shortcodes = get_option("smack_fields_shortcodes");
		$smack_vtlc_settings = get_option('smack_vtlc_settings');
		$smack_vtlc_field_settings = get_option('smack_vtlc_field_settings');
		$smack_vtlc_widget_field_settings = get_option('smack_vtlc_widget_field_settings');
		$migrationmap = get_option("smack_oldversion_shortcodes");
		if(!is_array($migrationmap))
		{
			$migrationmap = array();
		}
		if(isset($smack_vtlc_settings))
		{
			$old_url = getcwd();
			global $plugin_dir_wp_tiger;
			chdir($plugin_dir_wp_tiger);
			if(!class_exists("Vtiger_WSClient"))
			{
				include_once($plugin_dir_wp_tiger . "vtwsclib/Vtiger/WSClient.php");
			}
			$url = $smack_vtlc_settings['url'];
			$username = $smack_vtlc_settings['smack_host_username'];
			$accesskey = $smack_vtlc_settings['smack_host_access_key'];
			$client = new Vtiger_WSClient($url);
			$login = $client->doLogin($username, $accesskey);
			if (!$login) {

			} else {
				$record = $recordInfo = $client->doDescribe("Leads");
				if ($record) {
					$fields = $record['fields'];
					foreach( $fields as $fieldattribute )
					{
						$Fields_by_FieldName[$fieldattribute['name']] = $fieldattribute;
					}
				}
			}
			if (!empty ($smack_vtlc_settings ['hostname']) && !empty ($smack_vtlc_settings ['dbuser'])) {
				$vtdb = new wpdb ($smack_vtlc_settings ['dbuser'], $smack_vtlc_settings ['dbpass'], $smack_vtlc_settings ['dbname'], $smack_vtlc_settings ['hostname']);
				$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata FROM vtiger_field WHERE tabid = 7 AND tablename != 'vtiger_crmentity' AND uitype != 4 ORDER BY block, sequence");
			}
			foreach( $allowedFields as $stdobj )
			{
				$db_fields[$stdobj->fieldname] = $stdobj->fieldid;
			}
			$smack_vtlc_field_settings_array = array(
				0 => array( "varname" => "smack_vtlc_field_settings" , "arrayname" => "fieldlist" , "formtype" => "post" , "stats" => "wp-tiger-contact-form-attempts"  , "shortcode" => "display_contact_page" ),
				1 => array( "varname" => "smack_vtlc_widget_field_settings" , "arrayname" => "widgetfieldlist" , "formtype" => "widget" , "stats" => "wp-tiger-contact-widget-form-attempts" ,  "shortcode" => "display_widget_area" )
			);
			foreach( $smack_vtlc_field_settings_array as $smack_vtlc_field_settings_array_key => $smack_vtlc_field_settings_array_value )
			{
				$varname = $smack_vtlc_field_settings_array_value['varname'];
				$arrayname = $smack_vtlc_field_settings_array_value['arrayname'];
				$formtype = $smack_vtlc_field_settings_array_value['formtype'];
				$shortcode = $smack_vtlc_field_settings_array_value['shortcode'];
				$stats = get_option($smack_vtlc_field_settings_array_value['stats']);
				if($varname == "smack_vtlc_field_settings")
				{
					$fieldlist_array = $smack_vtlc_field_settings[$arrayname];
				}
				else
				{
					$fieldlist_array = $smack_vtlc_widget_field_settings[$arrayname];
				}
				$j=0;
				$count=count($recordInfo['fields']);
				for($i=0;$i<$count;$i++)
				{
					if($recordInfo['fields'][$i]['nullable']=="" && $recordInfo['fields'][$i]['editable']=="" ){
					}
					elseif($recordInfo['fields'][$i]['type']['name'] == 'reference'){
					}
					elseif($recordInfo['fields'][$i]['name'] == 'modifiedby' || $recordInfo['fields'][$i]['name'] == 'assigned_user_id' ){
					}
					else{
						$config_fields['fields'][$j] = $recordInfo['fields'][$i];
						$config_fields['fields'][$j]['order'] = $j;
						if( in_array( $db_fields[$recordInfo['fields'][$i]['name']] , $fieldlist_array ) )
						{
							$config_fields['fields'][$j]['publish'] = 1;
						}
						else
						{
							$config_fields['fields'][$j]['publish'] = 0;
						}
						$config_fields['fields'][$j]['display_label'] = $recordInfo['fields'][$i]['label'];
						if($recordInfo['fields'][$i]['mandatory']==1)
						{
							$config_fields['fields'][$j]['wp_mandatory'] = 1;
							$config_fields['fields'][$j]['mandatory'] = 2;
						}
						else
						{
							$config_fields['fields'][$j]['wp_mandatory'] = 0;
						}
						$j++;
					}
				}
				$config_fields['crm'] = "wptigerpro";
				$config_fields['module'] = "Leads";
				$config_fields['formtype'] = $formtype;
				$config_fields['assignedto'] = 1;
				$config_fields['check_duplicate'] = "none";
				$config_fields['total'] = $stats['success'];
				$config_fields['success'] = $stats['success'];
				$random_string = $this->CreateNewFieldShortcode( "wptigerpro" , "Leads" );
				$migrationmap[$shortcode][] = array(
					"name" => "wptigerpro",
					"newrandomname" => "$random_string");
				update_option("smack_oldversion_shortcodes" , $migrationmap);
				update_option("smack_wptigerpro_lead_fields-tmp" , $config_fields);
				$config_contact_shortcodes[$random_string] = $config_fields;
			}
			update_option("smack_fields_shortcodes" , $config_contact_shortcodes);
		}
		$fieldNames = array(
			'url' => "url",
			'username' => "smack_host_username",
			'accesskey' => "smack_host_access_key",
			'user_capture' => "wp_tiger_smack_user_capture",
		);
		foreach( $fieldNames as $key => $value )
		{
			$fieldNames_values[$key] = $smack_vtlc_settings[$value];
		}
		update_option("wp_wptigerpro_settings" , $fieldNames_values);
		chdir($old_url);
	}

	public function migrateWpSugarFree()
	{
		$fieldNames_values=[];
		$config_leads_fields=[];
		$user_details=[];
		$smack_wp_sugar_free_settings = get_option( 'smack_wp_sugar_free_settings' );
		$smack_wp_sugar_free_field_settings = get_option( 'smack_wp_sugar_free_field_settings' );
		$smack_wp_sugar_widget_free_field_settings = get_option( 'smack_wp_sugar_widget_free_field_settings' );
		$smack_wp_sugar_free_field_settings['widgetfieldlist'] = $smack_wp_sugar_widget_free_field_settings['widgetfieldlist'];
		$migrationmap = get_option("smack_oldversion_shortcodes");
		if(!is_array($migrationmap))
		{
			$migrationmap = array();
		}
		$config_contact_shortcodes = get_option("smack_fields_shortcodes");
		if( is_array($smack_wp_sugar_free_settings))
		{
			$fieldNames = array(
				'url' => 'url',
				'username' => 'username',
				'password' => 'password',
				'user_capture' => "wp_sugar_free_smack_user_capture",
			);
			foreach( $fieldNames as $key => $value )
			{
				$fieldNames_values[$key] = $smack_wp_sugar_free_settings[$value];
			}
			update_option("wp_wpsugarpro_settings" , $fieldNames_values);
		}

		if( is_array($smack_wp_sugar_free_field_settings) )
		{
			global $plugin_dir_wp_sugar;
			$plugin_dir_wp_sugar;
			if(!defined('sugarEntry') || !sugarEntry)
			{
				define('sugarEntry', TRUE);
				include_once($plugin_dir_wp_sugar.'nusoap/nusoap.php');
			}
			$url = trim($smack_wp_sugar_free_settings['url'], '/');
			$username = $smack_wp_sugar_free_settings['username'];
			$password = $smack_wp_sugar_free_settings['password'];
			$client = new nusoapclient($url.'/soap.php?wsdl',true);
			$user_auth = array(
				'user_auth' => array(
					'user_name' => $username,
					'password' => md5($password),
					'version' => '0.1'
				),
				'application_name' => 'wp-sugar-free');
			$login = $client->call('login',$user_auth);
			$session_id = $login['id'];

			$recordInfo = $client->call('get_module_fields', array('session' => $session_id, 'module_name' => "Leads"));

			if(isset($recordInfo['error']['number']) && is_array($recordInfo['error']) )
			{
			}
			if(isset($recordInfo))
			{
				$j=0;
				$module = $recordInfo['module_name'];
				$AcceptedFields = Array( 'text' => 'text' , 'bool' => 'boolean', 'enum' => 'picklist' , 'varchar' => 'string' , 'url' => 'url' , 'phone' => 'phone' , 'multienum' => 'multipicklist' , 'radioenum' => 'radioenum', 'currency' => 'currency' ,'date' => 'date' , 'datetime' => 'date' );
				$count=count($recordInfo['module_fields']);
				for($i=0;$i<$count;$i++)
				{
					if(array_key_exists($recordInfo['module_fields'][$i]['type'], $AcceptedFields)){
						if(($recordInfo['module_fields'][$i]['type'] == 'enum') || ($recordInfo['module_fields'][$i]['type'] == 'multienum') || ($recordInfo['module_fields'][$i]['type'] == 'radioenum')){
							$optionindex = 0;
							$picklistValues = array();
							foreach($recordInfo['module_fields'][$i]['options'] as $option)
							{
								$picklistValues[$optionindex]['label'] = $option['name'] ;
								$picklistValues[$optionindex]['value'] = $option['value'];
								$optionindex++;
							}
							$recordInfo['module_fields'][$i]['type'] = Array ( 'name' => $AcceptedFields[$recordInfo['module_fields'][$i]['type']] , 'picklistValues' => $picklistValues );
						}
						else
						{
							$recordInfo['module_fields'][$i]['type'] = Array( 'name' => $AcceptedFields[$recordInfo['module_fields'][$i]['type']]);
						}
						$config_leads_fields['fields'][$j] = $recordInfo['module_fields'][$i];
						$config_leads_fields['fields'][$j]['order'] = $j;
						$config_leads_fields['fields'][$j]['publish'] = 0;
						$config_leads_fields['fields'][$j]['display_label'] = trim($recordInfo['module_fields'][$i]['label'], ':');
						if($recordInfo['module_fields'][$i]['required']==1)
						{
							$config_leads_fields['fields'][$j]['wp_mandatory'] = 1;
							$config_leads_fields['fields'][$j]['mandatory'] = 2;
						}
						else
						{
							$config_leads_fields['fields'][$j]['wp_mandatory'] = 0;
						}
						$j++;
					}
				}
				$formtypes_array = array('post' => array( 'optionname' => "smack_wp_sugar_free_field_settings" , "fieldlistname" => "fieldlist" , "shortcode" => "sugarcrm_webtolead" ) , "widget" => array( 'optionname' => 'smack_wp_sugar_widget_free_field_settings' , "fieldlistname" => "widgetfieldlist" , "shortcode" => "sugarcrm_webtolead_WG" ) );
				foreach( $formtypes_array as $formtype => $formtype_array )
				{
					$config_post_fields = $config_leads_fields;
					$shortcode = $formtype_array['shortcode'];
					foreach($config_leads_fields['fields'] as $key => $values )
					{
						if(in_array($values['name'] , $smack_wp_sugar_free_field_settings[$formtype_array['fieldlistname']]))
						{
							$config_post_fields['fields'][$key]['publish'] = 1;
						}
					}
					$config_post_fields['check_duplicate'] = "none";
					$config_post_fields['formtype'] = $formtype;
					$config_post_fields['crm'] = "wpsugarpro";
					$recordInfo_user = $client->call('user_list', array('user_name' => $username, 'password' => md5($password)));
					$userindex = 0;
					if(is_array($recordInfo_user))
						foreach($recordInfo_user as $record)
						{
							$user_details['user_name'][$userindex] = $record['user_name'];
							$user_details['id'][$userindex] = $record['id'];
							$user_details['first_name'][$userindex] = $record['first_name'];
							$user_details['last_name'][$userindex] = $record['last_name'];
							$userindex++;
						}
					$config_post_fields['assignedto'] = $user_details['id'][0];
					$config_post_fields['module'] = "Leads";
					$randomstring = $this->CreateNewFieldShortcode("wpsugarpro" , "Leads");
					$migrationmap[$shortcode][] = array(
						"name" => "wpsugarpro",
						"newrandomname" => $randomstring);
					update_option("smack_oldversion_shortcodes" , $migrationmap);
					$config_contact_shortcodes[$randomstring] = $config_post_fields;
					update_option("smack_wpsugarpro_lead_fields-tmp" , $config_post_fields);
					update_option("smack_fields_shortcodes" , $config_contact_shortcodes);
				}
			}
		}
	}

	public function migrateWpZohoFree()
	{
		$fieldNames_values = [];
		$smack_zoho_crm_settings = get_option( 'smack_zoho_crm_settings' );
		$smack_zoho_crm_field_settings = get_option( 'smack_zoho_crm_field_settings' );
		$smack_zoho_crm_widget_field_settings = get_option( 'smack_zoho_crm_widget_field_settings' );
		$migrationmap = get_option("smack_oldversion_shortcodes");
		if(!is_array($migrationmap))
		{
			$migrationmap = array();
		}
		$smack_zoho_crm_field_settings['widgetfieldlist'] = $smack_zoho_crm_widget_field_settings['widgetfieldlist'];
		$config_contact_shortcodes = get_option("smack_fields_shortcodes");
		if( is_array($smack_zoho_crm_settings))
		{
			$fieldNames = array(
				'username' => 'username',
				'password' => 'password',
				'authtoken'  => 'authkey',
				'user_capture' => "wp_zoho_crm_smack_user_capture",
			);
			foreach( $fieldNames as $key => $value )
			{
				$fieldNames_values[$key] = $smack_zoho_crm_settings[$value];
			}
			update_option("wp_wpzohopro_settings" , $fieldNames_values);
		}

		if( is_array($smack_zoho_crm_field_settings) )
		{
			global $plugin_dir_wp_zoho_crm;
			$plugin_dir_wp_zoho_crm;
			if(!class_exists('SmackZohoApi'))
			{
				include_once($plugin_dir_wp_zoho_crm.'/SmackZohoApi.php');
			}
			$client = new SmackZohoApi();
			$recordInfo = $client->APIMethod( "Leads" , "getFields" , $smack_zoho_crm_settings['authkey'] );
			$config_fields = array();
			$AcceptedFields = Array( 'TextArea' => 'text' , 'Text' => 'string' , 'Email' => 'email' , 'Boolean' => 'boolean', 'Pick List' => 'picklist' , 'varchar' => 'string' , 'Website' => 'url' , 'Phone' => 'phone' , 'Multi Pick List' => 'multipicklist' , 'radioenum' => 'radioenum', 'Currency' => 'currency' , 'DateTime' => 'date' , 'datetime' => 'date' , 'Integer' => 'string' );
			$j = 0;
			foreach($recordInfo['section'] as $section ){
				if(!empty($section['FL']))
					foreach($section['FL'] as $key => $fields )
					{
						if( ($key === '@attributes') )
						{
							if( $fields['req'] == 'true' )
							{
								$config_fields['fields'][$j]['wp_mandatory'] = 1;
								$config_fields['fields'][$j]['mandatory'] = 2;
							}
							else
							{
								$config_fields['fields'][$j]['wp_mandatory'] = 0;
							}
							if(($fields['type'] == 'Pick List') || ($fields['type'] == 'Multi Pick List') || ($fields['type'] == 'Radio')){
								$optionindex = 0;
								$picklistValues = array();
								foreach($fields['val'] as $option)
								{
									$picklistValues[$optionindex]['label'] = $option ;
									$picklistValues[$optionindex]['value'] = $option;
									$optionindex++;
								}
								$config_fields['fields'][$j]['type'] = Array ( 'name' => $AcceptedFields[$fields['type']] , 'picklistValues' => $picklistValues );
							}
							else
							{
								$config_fields['fields'][$j]['type'] = array("name" => $AcceptedFields[$fields['type']]);
							}
							$config_fields['fields'][$j]['name'] = str_replace(" " , "_", $fields['dv']);
							$config_fields['fields'][$j]['fieldname'] = $fields['dv'];
							$config_fields['fields'][$j]['label'] = $fields['label'];
							$config_fields['fields'][$j]['display_label'] = $fields['label'];
							$config_fields['fields'][$j]['publish'] = 1;
							$config_fields['fields'][$j]['order'] = $j;
							$j++;
						}
						elseif( $fields['@attributes']['isreadonly'] == 'false' && ( $fields['@attributes']['type'] != 'Lookup' ) && ( $fields['@attributes']['type'] != 'OwnerLookup' ) && ( $fields['@attributes']['type'] != 'Lookup' ) )
						{
							if( $fields['@attributes']['req'] == 'true' )
							{
								$config_fields['fields'][$j]['mandatory'] = 2;
								$config_fields['fields'][$j]['wp_mandatory'] = 1;
							}
							else
							{
								$config_fields['fields'][$j]['wp_mandatory'] = 0;
							}

							if(($fields['@attributes']['type'] == 'Pick List') || ($fields['@attributes']['type'] == 'Multi Pick List') || ($fields['@attributes']['type'] == 'Radio')){
								$optionindex = 0;
								$picklistValues = array();
								foreach($fields['val'] as $option)
								{
									$picklistValues[$optionindex]['label'] = $option;
									$picklistValues[$optionindex]['value'] = $option;
									$optionindex++;
								}
								$config_fields['fields'][$j]['type'] = Array ( 'name' => $AcceptedFields[$fields['@attributes']['type']] , 'picklistValues' => $picklistValues );
							}
							else
							{
								$config_fields['fields'][$j]['type'] = array( 'name' => $AcceptedFields[$fields['@attributes']['type']] );
							}
							$config_fields['fields'][$j]['name'] = str_replace(" " , "_", $fields['@attributes']['dv']);
							$config_fields['fields'][$j]['fieldname'] = $fields['@attributes']['dv'];
							$config_fields['fields'][$j]['label'] = $fields['@attributes']['label'];
							$config_fields['fields'][$j]['display_label'] = $fields['@attributes']['label'];
							$config_fields['fields'][$j]['publish'] = 0;
							$config_fields['fields'][$j]['order'] = $j;
							$j++;
						}
					}
			}
			$formtypes_array = array('post' => array( 'optionname' => "smack_zoho_crm_field_settings" , "fieldlistname" => "fieldlist" , "shortcode" => "zoho_crm_lead_page" ) , "widget" => array( 'optionname' => 'smack_zoho_crm_widget_field_settings' , "fieldlistname" => "widgetfieldlist" , "shortcode" => "zoho_crm_lead_widget_area" ) );
			foreach( $formtypes_array as $formtype => $formtype_array )
			{
				$shortcode = $formtype_array['shortcode'];

				$config_post_fields = $config_fields;
				foreach($config_fields['fields'] as $key => $values )
				{
					if(in_array($values['fieldname'] , $smack_zoho_crm_field_settings[$formtype_array['fieldlistname']]))
					{
						$config_post_fields['fields'][$key]['publish'] = 1;
					}
				}
				$config_post_fields['check_duplicate'] = "none";
				$config_post_fields['formtype'] = $formtype;
				$config_post_fields['crm'] = "wpzohopro";
				$config_post_fields['assignedto'] = "";
				$config_post_fields['module'] = "Leads";
				$randomstring = $this->CreateNewFieldShortcode("wpzohopro" , "Leads");
				$migrationmap[$shortcode][] = array(
					"name" => "wpzohopro",
					"newrandomname" => $randomstring );
				update_option("smack_oldversion_shortcodes" , $migrationmap);
				$config_contact_shortcodes[$randomstring] = $config_post_fields;
				update_option("smack_wpzohopro_lead_fields-tmp" , $config_post_fields);
				update_option("smack_fields_shortcodes" , $config_contact_shortcodes);
			}
		}
	}

	public function CreateNewFieldShortcode( $crmtype , $module ){

		$moduleslug = rtrim( strtolower($module) , "s");
		$tmp_option = "smack_{$crmtype}_{$moduleslug}_fields-tmp";
		if(!function_exists("generateRandomStringActivate"))
		{
			function generateRandomStringActivate($length = 10) {
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
		for($notpresent = "no" ; $notpresent == "no"; )
		{
			$random_string = generateRandomStringActivate(5);
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

	public function migrateWpLeadsBuilderCrm()
	{
		global $crmdetails;
		$config_contact_shortcodes = get_option("smack_fields_shortcodes");
		$IncludedFreePlugins = Array(
			'wptigerfree' => "WP Tiger free",
			'wpsugarfree' => "WP Sugar free",
			'wpzohofree' => "WP Zoho free",
			'wpsalesforcefree' => "WP Salesforce free",
		);
		$FreeProPluginMap = Array(
			'wptigerfree' => "wptigerpro",
			'wpsugarfree' => "wpsugarpro",
			'wpzohofree' => "wpzohopro",
			'wpsalesforcefree' => "wpsalesforcepro",
		);
		foreach( $IncludedFreePlugins as $key => $value )
		{
			$smack_key_lead_post_field_settings[$key] = get_option( "smack_{$key}_lead_post_field_settings" );
			$smack_key_lead_widget_field_settings[$key] = get_option( "smack_{$key}_lead_widget_field_settings" );
			//	$smack_key_lead_fields_tmp[$key] = get_option( "smack_{$key}_lead_fields-tmp" );
			$wp_key_settings[$key] = get_option( "wp_{$key}_settings" );
		}
		if(is_array($smack_key_lead_post_field_settings))
			foreach( $smack_key_lead_post_field_settings as $key => $field_settings )
			{
				$shortcode = $this->CreateNewFieldShortcode( $FreeProPluginMap[$key] , $crmdetails[$FreeProPluginMap[$key]]['modulename']["Leads"] );
				$field_settings['formtype'] = "post";
				$field_settings['crm'] = $FreeProPluginMap[$key];
				$successfulAttemptsOption = get_option( "wp-{$key}-contact-post-form-attempts" );
				$field_settings['total'] = $successfulAttemptsOption['total'];
				$field_settings['success'] = $successfulAttemptsOption['success'];
				$config_contact_shortcodes[$shortcode] = $field_settings;
				update_option( "smack_fields_shortcodes" , $config_contact_shortcodes );
			}
		if(is_array($smack_key_lead_widget_field_settings))
			foreach( $smack_key_lead_widget_field_settings as $key => $field_settings )
			{
				$shortcode = $this->CreateNewFieldShortcode( $FreeProPluginMap[$key] , $crmdetails[$FreeProPluginMap[$key]]['modulename']["Leads"] );
				$field_settings['formtype'] = "widget";
				$field_settings['crm'] = $FreeProPluginMap[$key];
				$successfulAttemptsOption = get_option( "wp-{$key}-contact-widget-form-attempts" );
				$field_settings['total'] = $successfulAttemptsOption['total'];
				$field_settings['success'] = $successfulAttemptsOption['success'];
				$config_contact_shortcodes[$shortcode] = $field_settings;
				update_option( "smack_fields_shortcodes" , $config_contact_shortcodes );
			}
		if(is_array($wp_key_settings))
			foreach( $wp_key_settings as $key => $field_settings )
			{
				$field_settings['formtype'] = "post";
				$field_settings['crm'] = $FreeProPluginMap[$key];
				update_option( "smack_{$FreeProPluginMap[$key]}_lead_fields-tmp" , $field_settings );
			}
		if(is_array($wp_key_settings))
			foreach( $wp_key_settings as $key => $settings )
			{
				update_option("wp_{$FreeProPluginMap[$key]}_settings", $settings );
			}
	}

	public function renderMenu()
	{
		include(plugin_dir_path(__FILE__) . '../templates/menu.php');
	}

	public function renderContent()
	{
		if($this->Action == "Settings" || $this->Action=="")
		{
			if($this->Action=="")
			{
				$this->Action = "Settings";
			}
			$action = $this->ActivatedPlugin.$this->Action;
			$module = $this->Module;
		}
		else
		{
			$action = $this->Action;
			$module = $this->Module;
		}
		include(plugin_dir_path(__FILE__) . '../modules/'.$action.'/actions/actions.php');
		include(plugin_dir_path(__FILE__) . '../modules/'.$action.'/templates/view.php');
	}
}

class CallWPCaptureObjPRO extends WPCapture_includes_helper_PRO
{
	private static $_instance = null;
	public static function getInstance()
	{
		if( !is_object(self::$_instance) )
			self::$_instance = new WPCapture_includes_helper_PRO();
		return self::$_instance;
	}
}// CallWPCaptureObj Class Ends
