<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

require_once('SmackContactFormGenerator.php');
add_filter('caldera_forms_get_entry_detail', 'caldera_forms_example', 10, 3);

function caldera_forms_example($entry, $entry_id, $form){
	global $wpdb;
	$ArraytoApi= $data_array = [];
	$form_id = $entry['form_id'];
	if($entry['status'] == 'pending'){
		update_option('wp_leads_caldera_form_entry', 'off');
	}

	$check_form_entry = get_option('wp_leads_caldera_form_entry');
	$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
	$get_caldera_option = $activated_crm.'_wp_caldera'.$form_id;
	$check_map_exist = get_option( $get_caldera_option );
	if( (!empty( $check_map_exist )) && ($entry['status'] == 'active') && ($check_form_entry == 'off')){
		$mapped_fields = $check_map_exist['fields'];

		$caldera_submitted_fields = [];
		$caldera_form_entries = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cf_form_entry_values WHERE entry_id = $entry_id ", ARRAY_A);
		foreach($caldera_form_entries as $caldera_form_values){
			$caldera_submitted_fields[$caldera_form_values['field_id']] = $caldera_form_values['value'];
		}	
		update_option('wp_leads_caldera_form_entry', 'on');
	
		foreach($mapped_fields as $mapped_key => $mapped_value){
			if(array_key_exists($mapped_key, $caldera_submitted_fields)){
				$check_for_json = json_decode($caldera_submitted_fields[$mapped_key], true);	
				if(is_array($check_for_json)){
					$final_value = array_values($check_for_json);
				}
				else{
					$final_value = $caldera_submitted_fields[$mapped_key];
				}	
				$data_array[$mapped_value] = $final_value;	
			}
		}
		
		$ArraytoApi['posted'] = $data_array;
        $ArraytoApi['third_module'] = $check_map_exist['third_module'];
        $ArraytoApi['thirdparty_crm'] = $check_map_exist['thirdparty_crm'];
        $ArraytoApi['third_plugin'] = $check_map_exist['third_plugin'];
        $ArraytoApi['form_title'] = $check_map_exist['form_title'];
        $ArraytoApi['shortcode'] = $get_caldera_option;
	    $ArraytoApi['duplicate_option'] = $check_map_exist['thirdparty_duplicate'];
        $capture_obj = new CapturingProcessClassPRO();
        $capture_obj->thirdparty_mapped_submission($ArraytoApi);
	}
	
}
?>