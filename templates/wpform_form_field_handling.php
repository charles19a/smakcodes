<?php
/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly


add_action( 'wpforms_process_complete', 'wpf_dev_process_complete', 10, 4 );

function wpf_dev_process_complete($fields, $entry) 
{
	global $wpdb,$HelperObj;
	$post_id = $entry['id'];
	$ArraytoApi = $code = [];
	$thirdparty = 'wpform';
	$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
	$get_wpform_option = $activated_crm.'_wp_wpform_lite'.$post_id;
	$check_map_exist = get_option ( $get_wpform_option );

	if( !empty( $check_map_exist ))
	{

		$submit_array = [];
		$crm_array = $check_map_exist['fields'];
		$third_party_array = [];

		foreach($fields as $field_key => $field_value){
			$third_party_array[$field_value['name']] = $field_value['value'];
		}

		foreach($crm_array as $crm_key => $crm_values){
			if(array_key_exists($crm_key , $third_party_array)){
				$submit_array[$crm_values] = $third_party_array[$crm_key];
			}
		}

		$ArraytoApi['posted'] = $submit_array;
		$ArraytoApi['third_module'] = $check_map_exist['third_module'];
		$ArraytoApi['thirdparty_crm'] = $check_map_exist['thirdparty_crm'];
		$ArraytoApi['third_plugin'] = $check_map_exist['third_plugin'];
		$ArraytoApi['form_title'] = $check_map_exist['form_title'];
		$ArraytoApi['shortcode'] = $get_wpform_option;
		$ArraytoApi['duplicate_option'] = $check_map_exist['thirdparty_duplicate'];
		$capture_obj = new CapturingProcessClassPRO();
		$capture_obj->thirdparty_mapped_submission($ArraytoApi);
	}
	else
	{
		$smack_shortcode = $wpdb->get_var($wpdb->prepare("select shortcode from wp_smackformrelation where thirdpartyid =%d and thirdparty=%s " , $post_id , $thirdparty ) );
		$code['name'] = $smack_shortcode;
		$newform = new CaptureData();
		$activatedPlugin = $HelperObj->ActivatedPlugin;
		$newshortcode = $newform->formfields_settings( $code['name'] );
		$FormSettings = $newform->getFormSettings( $code['name'] , $activatedPlugin);
		if (is_object($FormSettings)) {
			$module = $FormSettings->module;
		}
		$all_fields = $_POST;
		foreach($all_fields as $key=>$value)	{
			if(preg_match('/^_wp/',$key))
				unset($all_fields[$key]);
		}
		global $_POST;
		$_POST = array();
		$_POST = $ArraytoApi;
		callCurlPRO('post');
		return true;
	}
}
