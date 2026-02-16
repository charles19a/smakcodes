<?php
/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

add_action( 'wpforms_process_complete', 'wpfp_dev_process_complete', 10, 4 );

function wpfp_dev_process_complete($fields, $entry) 
{

	global $wpdb,$HelperObj;
	$post_id = $entry['id'];
	$ArraytoApi = $code = [];
	$thirdparty = 'wpformpro';
	$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
	$get_wpformpro_option = $activated_crm.'_wp_wpform_pro'.$post_id;
	$check_map_exist = get_option ( $get_wpformpro_option );

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
				if((strpos($third_party_array[$crm_key], ';') !== false) && ($crm_values == 'annualrevenue' || $crm_values == 'Annual_Revenue')) {
					$explode_value = explode(';',$third_party_array[$crm_key]);
					$third_party_array[$crm_key] = $explode_value[1];
				}
				if((is_numeric($third_party_array[$crm_key])) && ($crm_values == 'rating' || $crm_values == 'Rating')){
					if($third_party_array[$crm_key] == 5) { $third_party_array[$crm_key] = 'Acquired'; }
					if($third_party_array[$crm_key] == 4) { $third_party_array[$crm_key] = 'Active'; }
					if($third_party_array[$crm_key] == 3) { $third_party_array[$crm_key] = 'Market Failed'; }
					if($third_party_array[$crm_key] == 2) { $third_party_array[$crm_key] = 'Project Cancelled'; }
					if($third_party_array[$crm_key] == 1) { $third_party_array[$crm_key] = 'Shutdown'; }
				}
				if((strpos($third_party_array[$crm_key], ';') !== false) && ($crm_values == 'leadstatus' || $crm_values == 'leadsource' || $crm_values == 'Industry' || $crm_values == 'rating' || $crm_values == 'Lead_Status' || $crm_values == 'Lead_Source' || $crm_values == 'industry ' || $crm_values == 'Rating')) {
					$explode_value = explode('-',$third_party_array[$crm_key]);
					$third_party_array[$crm_key] = $explode_value[0];
				}
				$submit_array[$crm_values] = $third_party_array[$crm_key];
			}
		}

		$ArraytoApi['posted'] = $submit_array;
		$ArraytoApi['third_module'] = $check_map_exist['third_module'];
		$ArraytoApi['thirdparty_crm'] = $check_map_exist['thirdparty_crm'];
		$ArraytoApi['third_plugin'] = $check_map_exist['third_plugin'];
		$ArraytoApi['form_title'] = $check_map_exist['form_title'];
		$ArraytoApi['shortcode'] = $get_wpformpro_option;
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
		$module = $FormSettings->module;
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
