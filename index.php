<?php
/********************************************************************************************
 * Plugin Name: WP Leads Builder For Any CRM Pro
 * Description: Sync data from Webforms (contact 7 , Ninja & Gravity ) and WP User data to Salesforce, Zoho CRM, Zoho CRM Plus, Vtiger CRM, Sugar CRM & Freshsales CRM. Embed forms as Posts, Pages & Widgets.
 * Version: 3.0
 * Text Domain: wp-leads-builder-any-crm-pro
 * Domain Path: /languages
 * Author: Smackcoders
 * Plugin URI: https://goo.gl/kKWPui
 * Author URI: https://goo.gl/kKWPui
 *
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SM_WPLeadsBuilderForAnyCRMPro {

	public $version = '3.0';

	protected static $_instance = null;

	/**
	 * Main WPLeadsBuilderForAnyCRMPro Instance.
	 *
	 * Ensures only one instance of WPLeadsBuilderForAnyCRMPro is loaded or can be loaded.
	 *
	 * @since 4.5
	 * @static
	 * @return SM_WPLeadsBuilderForAnyCRMPro - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		$this->define_constants();
		$this->includes();

		add_action( 'init', array( $this, 'action_crm_init_pro') );
		add_action( 'init', array( $this, 'frontend_init_pro') );
		add_filter('http_request_args', array($this, 'curlArgs'));

		$this->init();
		$this->init_hooks();
		$active_plugins = get_option( "active_plugins" );
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if (is_plugin_active('wp-leads-builder-any-crm-pro/index.php')) {
			$active_plugins = get_option( "active_plugins" );
			if( in_array( "wp-leads-builder-any-crm/index.php", $active_plugins) ) {
				deactivate_plugins('wp-leads-builder-any-crm/index.php');
			}
			if( in_array( "wp-tiger/index.php", $active_plugins) ) {
				deactivate_plugins('wp-tiger/index.php');
			}
			if( in_array( "wp-sugar-free/index.php", $active_plugins) ) {
				deactivate_plugins('wp-sugar-free/index.php');
			}
			if( in_array( "wp-zoho-crm/index.php", $active_plugins) ) {
				deactivate_plugins('wp-zoho-crm/index.php');
			}
			if( in_array( "wp-freshsales/index.php", $active_plugins) ) {
				deactivate_plugins('wp-freshsales/index.php');
			}
			if( in_array( "wp-salesforce/index.php", $active_plugins) ) {
				deactivate_plugins('wp-salesforce/index.php');	
			}
		}


		if( in_array( "ninja-forms/ninja-forms.php", $active_plugins) ) {
			add_action( 'init', 'ninja_forms_register_example' );
			require_once('templates/nija_form_field_handling.php');
		}

		if( in_array( "contact-form-7/wp-contact-form-7.php", $active_plugins) ) {
			if(file_exists( SM_LB_PRO_DIR . 'templates/contact_form_field_handling.php')){
				require_once( SM_LB_PRO_DIR . 'templates/contact_form_field_handling.php');
			}
		}

		if( in_array( "wpforms/wp-wpforms.php", $active_plugins) || in_array( "wpforms-lite/wpforms.php" , $active_plugins)) {
			require_once( 'templates/wpform_form_field_handling.php');
		}

		if( in_array( "wpforms/wp-wpforms.php", $active_plugins) || in_array( "wpforms/wpforms.php" , $active_plugins)) {
			require_once( 'templates/wpformpro_form_field_handling.php');
		}

		if( in_array( "gravityforms/gravityforms.php", $active_plugins) || in_array( "Gravity forms/gravityforms.php", $active_plugins)) {
			require_once('templates/gravity_form_field_handling.php');
		}

		if(in_array("caldera-forms/caldera-core.php", $active_plugins)){
			require_once('templates/caldera_form_field_handling.php');
		}

		if(in_array("elementor-pro/elementor-pro.php", $active_plugins)){
			require_once('templates/elementorFormHandling.php');
		}

		if( in_array( "woocommerce/woocommerce.php", $active_plugins) ) {
			require_once( 'templates/ecom_wc_field_handling.php' );
		}
		require_once("includes/LBData.php");
		require_once("includes/LBContactFormPlugins.php");
		$ContactFormPlugins = new ContactFormPROPlugins();
		$ActivePlugin = $ContactFormPlugins->getActivePlugin();
		$get_debug_option = get_option("wp_{$ActivePlugin}_settings");
		if( $ActivePlugin != '' ){
			if( $ActivePlugin == "wpzohopluspro" ){
				$ActivePlugin = "wpzohopro";
			}
			if (file_exists(plugin_dir_path(__FILE__) . "includes/{$ActivePlugin}Functions.php")) {
				require_once("includes/{$ActivePlugin}Functions.php");
			}
		}

		require_once('includes/WPCapture_includes_helper.php');
		require_once("templates/SmackContactFormGenerator.php");
		require_once('includes/Functions.php');
		// Insight Files
		require_once('includes/WooCustomerInsightHelper.php');
		require_once('includes/CustomerInsight.php');
		require_once('includes/Countries.php');
		require_once('includes/Chart_Data.php');
		require_once('includes/WCI_AjaxActions.php');
	}

	private function init_hooks() {
		register_activation_hook(__FILE__, array('WPCapture_includes_helper_PRO', 'activate') );
		register_deactivation_hook(__FILE__, array('WPCapture_includes_helper_PRO', 'deactivate') );
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),  array($this, 'lb_plugin_row_meta'), 10, 2 );
		# Custom content after plugin row meta starts
		add_action('after_plugin_row_' . plugin_basename( __FILE__ ), array($this, 'after_lb_plugin_row_meta'), 10, 3);
		# Custom content after plugin row meta ends

		//User sync - on time creation
		$check_sync_value = get_option( 'Sync_value_on_off' );
		if( $check_sync_value == "On" ){
			add_action( 'profile_update', array( 'CapturingProcessClassPRO' , 'capture_updating_users' ) );
			add_action( 'user_register', array( 'CapturingProcessClassPRO' , 'capture_registering_users' ) );
		}

	}

	public function define_constants() {
		$this->define( 'SM_LB_PLUGIN_FILE', __FILE__ );
		$this->define( 'SM_LB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'SM_LB_PRO_DIR', plugin_dir_path(__FILE__));
		$this->define( 'SM_LB_SLUG', 'wp-leads-builder-any-crm-pro' );
		$this->define( 'SM_LB_DIR', WP_PLUGIN_URL . '/' .SM_LB_SLUG. '/');
		$this->define( 'SM_LB_SETTINGS', 'WP Leads Builder For Any CRM Pro' );
		$this->define( 'SM_LB_VERSION', '3.0');
		$this->define( 'SM_LB_NAME', 'Leads Builder For Any CRM Pro' );
		$this->define( 'SM_LB_URL',site_url().'/wp-admin/admin.php?page='.SM_LB_SLUG.'/index.php');
	}

	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	public function init() {
		if(is_admin()) :
			// Init action.
			do_action( 'uci_init' );
		if(is_admin()) {
			include_once('includes/LB_admin_ajax.php');
			SmackLBAdminAjax::smlb_ajax_events();
		}
		endif;
	}

	public function includes() {
		include_once ( 'admin/lb-admin.php' );
		$uciPages = array('lb-crmforms', 'lb-formsettings', 'lb-usersync', 'lb-ecominteg','lb-crmconfig','lb-droptable','lb-oppurtunities','lb-customerstats','lb-reports','lb-dashboard','lb-campaign','lb-new');
		require_once("includes/LBData.php");
		require_once("includes/LBContactFormPlugins.php");
		$ContactFormPlugins = new ContactFormPROPlugins();
		$ActivePlugin = $ContactFormPlugins->getActivePlugin();
		$get_debug_option = get_option("wp_{$ActivePlugin}_settings");
	}

	function action_crm_init_pro() {
		$lb_pages_list = array('lb-crmforms' , 'lb-formsettings' , 'lb-usersync' , 'lb-ecominteg' ,'lb-droptable','lb-crmconfig' , 'lb-oppurtunities' , 'lb-customerstats' , 'lb-reports' , 'lb-dashboard' , 'lb-campaign' , 'lb-create-leadform', 'lb-formhistory' , 'lb-create-contactform' , 'lb-usermodulemapping' , 'lb-mailsourcing' , 'wp-leads-builder-any-crm-pro' );
		if ( isset($_REQUEST['page']) && in_array($_REQUEST['page'] , $lb_pages_list) ) {
			wp_enqueue_style('main-style', plugins_url('assets/css/mainstyle.css', __FILE__));
			wp_enqueue_style('jquery-ui', plugins_url('assets/css/jquery-ui.css', __FILE__));

			wp_enqueue_style('common-crm-free-bootstrap-css', plugins_url('assets/css/bootstrap.css', __FILE__));
			wp_enqueue_style('common-crm-free-font-awesome-css', plugins_url('assets/css/font-awesome/css/font-awesome.css', __FILE__));
			wp_enqueue_style('jquery-confirm.min.css', plugins_url('assets/css/jquery-confirm.min.css', __FILE__));
			wp_enqueue_style('jquery-ui-12.1-css', plugins_url('assets/css/jquery-ui-12.1.css', __FILE__));
			wp_enqueue_style('sweet-alert-css', plugins_url('assets/css/sweetalert.css', __FILE__));
			wp_enqueue_script( 'jquery' );

			// Sweet Alert Js
			wp_register_script('sweet-alert-js', plugins_url('assets/js/sweetalert-dev.js', __FILE__));
			wp_enqueue_script('sweet-alert-js');

			wp_enqueue_script( 'notify-js', plugins_url( 'assets/js/notify.js', __FILE__ ) );

			wp_register_script('basic-action-js', plugins_url('assets/js/basicaction.js', __FILE__));
			wp_enqueue_script('basic-action-js');


			wp_register_script('common-crm-free-bootstrap-bootstrap-js', plugins_url('assets/js/bootstrap.min.js', __FILE__));
			wp_enqueue_script('common-crm-free-bootstrap-min-js');

			wp_register_script('jquery-js', plugins_url('assets/js/jquery.js', __FILE__));
			wp_enqueue_script('jquery-js');
			wp_register_script('jquery-min-js', plugins_url('assets/js/jquery.min.js', __FILE__));
			wp_enqueue_script('jquery-min-js');

			wp_register_script('jquery-ui-12.1-js', plugins_url('assets/js/jquery-ui-12.1.js', __FILE__));
			wp_enqueue_script('jquery-ui-12.1-js');


			wp_register_script('boot.min-js', plugins_url('assets/js/bootstrap-modal.min.js', __FILE__));
			wp_enqueue_script('boot.min-js');

			wp_register_script('jquery-confirm.min.js', plugins_url('assets/js/jquery-confirm.min.js', __FILE__));
			wp_enqueue_script('jquery-confirm.min.js');

			//Insight

			wp_register_script('d3.min.js',plugins_url('assets/js/d3.min.js',__FILE__));
			wp_register_script('crm-utils.js',plugins_url('assets/js/utils.js',__FILE__));
			wp_register_script('crm-chart.bundle.js',plugins_url('assets/js/Chart.bundle.js',__FILE__));

			wp_register_style('product_view.css',plugins_url('assets/css/wootracking_product_view.css',__FILE__));
			wp_enqueue_style('product_view.css');
			wp_enqueue_style('jquery-ui.css',plugins_url('assets/css/wootracking_jquery-ui.css',__FILE__));
			wp_enqueue_style('insight-bootstrap.css',plugins_url('assets/css/wootracking_bootstrap.min.css',__FILE__));

			wp_enqueue_script('chart.js',plugins_url('assets/js/wootracking-chart.js',__FILE__));
			wp_enqueue_script('crm-pie-chart.js',plugins_url('assets/js/wootracking-pie-chart.js',__FILE__));
			wp_enqueue_script('crm-utils.js');
			wp_enqueue_script('crm-chart.bundle.js');
			wp_enqueue_script('d3.min.js');
			//Added by usha
			wp_enqueue_style('leads-builder', plugins_url('assets/css/leads-builder.css', __FILE__));
			wp_enqueue_style('bootstrap-select', plugins_url('assets/css/bootstrap-select.css', __FILE__));
			wp_register_script('bootstrap-select-js', plugins_url('assets/js/bootstrap-select.js', __FILE__));
			wp_enqueue_script('bootstrap-select-js');
			wp_enqueue_style('icheck', plugins_url('assets/css/icheck/green.css', __FILE__));
			wp_enqueue_script( 'icheck-js', plugins_url( 'assets/js/icheck.min.js', __FILE__ ) );
			wp_enqueue_style( 'Icomoon Icons', plugins_url( 'assets/css/icomoon.css', __FILE__ ) );



		}
	}

	public static function lb_plugin_row_meta( $links, $file ) {
		if ( $file == SM_LB_PLUGIN_BASENAME ) {
			$row_meta = array(
				'settings' => '<a href="' . esc_url( apply_filters( 'sm_lb_settings_url', admin_url() . 'admin.php?page=wp-leads-builder-any-crm-pro' ) ) . '" title="' . esc_attr( __( 'Visit Plugin Settings', 'wp-leads-builder-any-crm-pro' ) ) . '" target="_blank">' . __( 'Settings', 'wp-leads-builder-any-crm-pro' ) . '</a>',
				'docs'     => '<a href="' . esc_url( apply_filters( 'sm_lb_docs_url', 'https://www.smackcoders.com/documentation/leads-builder-for-any-crm-from-wordpress-pro/community-version?utm_source=lead_builder_free&utm_campaign=plugin_menu&utm_medium=plugin' ) ) . '" title="' . esc_attr( __( 'View WP LeadBuilder Pro Documentation', 'wp-leads-builder-any-crm-pro' ) ) . '" target="_blank">' . __( 'Docs', 'wp-leads-builder-any-crm-pro' ) . '</a>',
				'videos'   => '<a href="' . esc_url( apply_filters( 'sm_lb_videos_url', 'https://youtu.be/4Oq9KxIjpvo' ) ) . '" title="' . esc_attr( __( 'View Videos for WP LeadBuilder Pro', 'wp-leads-builder-any-crm-pro' ) ) . '" target="_blank">' . __( 'Videos', 'wp-leads-builder-any-crm-pro' ) . '</a>',
				'support'  => '<a href="' . esc_url( apply_filters( 'sm_lb_support_url', 'https://www.smackcoders.com/support.html?utm_source=lead_builder_free&utm_campaign=plugin_menu&utm_medium=plugin' ) ) . '" title="' . esc_attr( __( 'Contact Support', 'wp-leads-builder-any-crm-pro' ) ) . '" target="_blank">' . __( 'Support', 'wp-leads-builder-any-crm-pro' ) . '</a>',
			);

			return array_merge( $row_meta, $links );
		}
	}

	public function curlArgs($response) {
		$response['sslverify'] = false;
		return $response;
	}

	public static function after_lb_plugin_row_meta() {
		$response = wp_safe_remote_get('https://www.smackcoders.com/wp-versions/wp-lead-builder.json');
		if ( is_wp_error( $response ) ) {
			return false;
		}
		else{
			if(isset($response['body'])){
				$response = json_decode($response['body']);
			}
		}
			$current_plugin_version = 3.0;
			if($current_plugin_version < $response->version[0]) {
				echo '<tr class="active"><td colspan="3">';
				echo '<div class="update-message notice inline notice-warning notice-alt"><p>There is a new version of WP Leads Builder For Any CRM Pro <b>[ version '. $response->version[0] .' ]</b> available. <a href="https://smackcoders.com/my-account.html" class="update-link" aria-label="Upgrade WP Leads Builder For Any CRM Pro now"> Upgrade now</a>.</p></div>';
				echo '</td></tr>';
			}
		
	}

	function frontend_init_pro()
	{
		if(!is_admin())
		{
			global $HelperObj;
			include_once ( 'includes/WPCapture_includes_helper.php' );
			$HelperObj = new WPCapture_includes_helper_PRO;
			$activatedplugin = $HelperObj->ActivatedPlugin;
			$config = get_option("wp_captcha_settings");
			if(!empty($config['smack_recaptcha'])){
				if($config['smack_recaptcha']=='yes')
				{
					wp_register_script( 'google-captcha-js' , "https://www.google.com/recaptcha/api.js" );
					wp_enqueue_script( 'google-captcha-js' );
				}

			}

			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_style('jquery-ui' , plugins_url('assets/css/jquery-ui.css', __FILE__) );
			wp_enqueue_style('front-end-styles' , plugins_url('assets/css/frontendstyles.css', __FILE__) );
			wp_enqueue_style('datepicker' , plugins_url('assets/css/datepicker.css', __FILE__) );
		}
	}
}

function SmackLB() {
	return SM_WPLeadsBuilderForAnyCRMPro::instance();
}
// Global for backwards compatibility.
$GLOBALS['wp_leads_builder_for_any_crm_pro'] = SmackLB();
