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
  add_action( 'gform_after_submission', 'gravity_forms_example' );

function gravity_forms_example($entry )
{
	global $wpdb, $HelperObj;
	$entries = $OriginalMap = $code =[];
	$smack_grav_values=[];
	$smackfieldslable=[];
	$smackfieldname=[];
	$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
	$form_id = intval($_POST['gform_submit']);
	//Mapp exixsting form
	
	$get_grav_option = $activated_crm.'_wp_gravity'.$form_id;
	$check_map_exist = get_option( $get_grav_option );
	if( !empty( $check_map_exist ))	
	{
		$all_fields = $_POST;
        	foreach($all_fields as $input=>$user_val)
        	{
        		if(preg_match('/^gform_|^state_|^is_submit/',$input))
        		unset($all_fields[$input]);
        	}
			foreach($all_fields as $ip => $user_val)
        	{
				$get_index = explode( "_" , $ip  );
				$newkey = $get_index[1];
				$all_fields[$newkey] = $all_fields[$ip];
				unset($all_fields[$ip]);
				unset( $all_fields['FILE'] );
        	}
		
//new code
		foreach( $entry as $entry_key => $entry_value )
		{
			if( is_numeric( $entry_key ) )
			{
				$entries[$entry_key] = $entry_value; 
			}
		}

		$grav_array_diff = array_diff($entries , $all_fields);
		$combined_array = array();
		foreach ($all_fields as $key => $val) {
			if($val) {
					$combined_array[$key] = $val;
			} else {
					$combined_array[$key] = $entries[$key];
			}
		}
		foreach($grav_array_diff as $k => $v) {
			if(!array_key_exists($k, $combined_array)) {
					$combined_array[$k] = $v;
			}
		}
		$all_fields = $combined_array;
	//END New Code for combining entries

		$grav = $wpdb->get_results($wpdb->prepare("select display_meta from ". $wpdb->prefix ."gf_form_meta where form_id=%d" , $form_id ));    
		$gravit = json_decode($grav[0]->display_meta);
		$grav_arr = $gravit->fields;
		$gravity = array();
		foreach($grav_arr as $keyy => $valuee){
			$id = $valuee->id;
			$gravity[$id] = $valuee->label;
		}
		$mapped_array = $check_map_exist['fields'];
		$mapped_array_key_labels = array_keys( $mapped_array );
		
		//get mapped label keys from gravity array
		foreach( $gravity as $gf_key => $gf_val)
		{
			foreach( $mapped_array_key_labels as $labels )
			{
				if( $labels == $gf_val && $labels != 'gclid_value' ) {
					$index = $gf_key;
					$field_name = $mapped_array[$labels];
					$user_value = $all_fields[$index];
					$data_array[$field_name] = $user_value; 		
				}
				if( $labels == 'gclid_value' ) {
					$index = $gf_key;
					$field_name = $mapped_array[$labels];
					$user_value = $_COOKIE['gclid'];
					$data_array[$field_name] = $user_value;
				}
			}
		}
		$activatedPlugin = $check_map_exist['thirdparty_crm'];
		foreach($data_array as $field => $usr_vals)
		{
			if($field == $usr_vals){
					switch($activatedPlugin)
					{
						case 'wptigerpro':
							$data_array[$field] ='1';
							break;
						case 'wpsugarpro':
						case 'wpsuitepro':
							$data_array[$field] ='on';
							break;
						case 'wpzohopro':
						case 'wpzohopluspro':
							$data_array[$field] ='true';
							break;
						case 'wpsalesforcepro':
							$data_array[$field] ='on';
							break;
						case 'freshsales':
							$data_array[$field] ='1';
							break;
						case 'mailchimp':
							$data_array[$field] ='1';
							break;

					}
			}
		}

	// Change drop down value to id for Fresh sales CRM
        if( $activatedPlugin == 'freshsales' )
        {
			$fs_module = strtolower( $check_map_exist['third_module'] );
			$fs_module = rtrim( $fs_module , 's' );
			$freshsales_option = get_option( "smack_{$activatedPlugin}_{$fs_module}_fields-tmp" );
			foreach( $freshsales_option['fields'] as $fs_key => $fs_option )
			{
				foreach( $data_array as $field_name => $posted_val ) {
					if( $fs_option['type']['name'] == 'picklist' && $fs_option['fieldname'] == $field_name )
					{
						foreach( $fs_option['type']['picklistValues'] as $pick_key => $pick_val )
						{
							if( $pick_val['label'] == $posted_val )
							{
								$data_array[$field_name] = $pick_val['id'];
							}
						}

					}
					if( $fs_option['type']['name'] == 'boolean' && $fs_option['fieldname'] == $field_name && $posted_val == "" )
					{
						$data_array[$field_name] = '0';
					}
				}
			}
        }
		$ArraytoApi['posted'] = $data_array;
		$ArraytoApi['third_module'] = $check_map_exist['third_module'];
		$ArraytoApi['thirdparty_crm'] = $check_map_exist['thirdparty_crm'];
		$ArraytoApi['third_plugin'] = $check_map_exist['third_plugin'];
		$ArraytoApi['form_title'] = $check_map_exist['form_title'];
		$ArraytoApi['shortcode'] = $get_grav_option;
		$ArraytoApi['duplicate_option'] = $check_map_exist['thirdparty_duplicate'];
		$capture_obj = new CapturingProcessClassPRO();
		$capture_obj->thirdparty_mapped_submission($ArraytoApi);
	}
	else
	{
		$thirdparty = 'gravityform';
		$smack_shortcode = $wpdb->get_var($wpdb->prepare("select shortcode from wp_smackformrelation where thirdpartyid=%d and thirdparty=%s" , $form_id , $thirdparty));
		$all_fields = $_POST;
		foreach($all_fields as $input=>$user_val)
		{
			if(preg_match('/^gform_|^state_|^is_submit/',$input))
			unset($all_fields[$input]);
		}
		$grav = $wpdb->get_results($wpdb->prepare("select display_meta from ". $wpdb->prefix ."gf_form_meta where form_id=%d" , $form_id ));	
		$gravit = json_decode($grav[0]->display_meta);
		$grav_arr = $gravit->fields;
		$gravity = array();

		//NEW CODE Smack Third Party form field relation

		$smack_relation_fields = $wpdb->get_results( $wpdb->prepare( "select smackfieldslable from wp_smackthirdpartyformfieldrelation where smackshortcodename=%s" , $smack_shortcode ),ARRAY_A);
		$rel_id = 1;
		foreach( $smack_relation_fields as $rel_key => $rel_value )
		{
			foreach( $rel_value as $sm_third_key => $sm_third_value )
				$smack_grav_values[$rel_id] = $sm_third_value; 
				$rel_id++;
		}

		foreach($grav_arr as $keyy => $valuee)
		{
			$id = $valuee->id;
			$gravity[$id] = $valuee->label;	
		}
		
		//Find array difference -extra fields to unset
		$grav_arr_key_diff = array_diff( $gravity , $smack_grav_values);
		foreach( $grav_arr_key_diff as $sm_difference_key => $sm_difference_val )
		{
			unset( $gravity[$sm_difference_key] );
		}
		$grav_fields_count = count( $gravity );
		foreach($all_fields as $ip => $user_val)
		{
			$get_index = explode( "_" , $ip  );
			$newkey = $get_index[1];			
			$all_fields[$newkey] = $all_fields[$ip];
			unset($all_fields[$ip]);
		}
		for( $i = 1 ; $i <= $grav_fields_count ; $i++ )
		{
			$grav_label = $gravity[$i];
			$grav_post_val = $all_fields[$i];
			$posted_fields[$grav_label] = $grav_post_val; 
		}

		$activatedPlugin = $HelperObj->ActivatedPlugin;
		foreach($posted_fields as $field => $usr_vals)
		{
			if($field == $usr_vals)
			{
				switch($activatedPlugin){
					case 'wptigerpro':
						$posted_fields[$field] ='1';
						break;
					case 'wpsugarpro':
					case 'wpsuitepro':
						$posted_fields[$field] ='on';
						break;
					case 'wpzohopro':
					case 'wpzohopluspro':
						$posted_fields[$field] ='true';
						break;
					case 'wpsalesforcepro':
						$posted_fields[$field] ='on';
						break;
					case 'freshsales':
						$posted_fields[$field] ='1';
						break;
					case 'mailcjimp':
						$posted_fields[$field] ='1';
						break;
				}
			}
		}
		$mapping = $wpdb->get_results($wpdb->prepare("select smackfieldslable,thirdpartyfieldids from wp_smackthirdpartyformfieldrelation where thirdpartyformid=%d" , $form_id ),ARRAY_A);
		foreach($mapping as $key=>$value){
			$smackfieldslable[$key] = $value['smackfieldslable'];
			$thirdpartyfieldids[$key] = $value['thirdpartyfieldids'];
		}
		$smackfieldName = $wpdb->get_results( $wpdb->prepare(" select a.field_name , a.field_values , a.field_type from wp_smackleadbulider_field_manager as a join wp_smackleadbulider_form_field_manager as b join wp_smackthirdpartyformfieldrelation as c where b.field_id=a.field_id and c.smackfieldid=b.rel_id and thirdpartyformid=%d" , $form_id ),ARRAY_A);

		foreach($smackfieldName as $key=>$value){
			$smackfieldname[$key] = $value['field_name'];
		}
		$thirdpartyfieldids = array_flip($thirdpartyfieldids);
		
		foreach($thirdpartyfieldids as $key=>$value){
			$OriginalMap[$key] = $smackfieldname[$value];
		}

		if( is_array( $posted_fields ) ){ //Make sure $all_fields is an array.
		//Loop through each of our submitted values.

		foreach( $posted_fields as $field_id => $user_value ){
		//Do something with those values
			$ArraytoApi[$OriginalMap[$field_id]] = $user_value;
		}

		if( $activatedPlugin == 'freshsales' )
		{
			foreach( $smackfieldName as $sm_key => $sm_value ){
				foreach( $ArraytoApi as $API_key => $API_val ){
					if( $sm_value['field_name'] == $API_key && $sm_value['field_type'] == 'boolean' && $API_val == "" )
					{
						$ArraytoApi[$API_key] = '0';
					}
				}
			}
		}

			$code['name'] = $smack_shortcode;
			$newform = new CaptureData();
			// $newshortcode = $newform->formfields_settings( $code['name'] );
			$FormSettings = $newform->getFormSettings( $code['name'] );
			$module = $FormSettings->module; //$shortcodes[$attr['name']]['module'];
			$ArraytoApi['moduleName'] = $module;
			$ArraytoApi['formnumber'] = $form_id;
			$ArraytoApi['submit'] = 'Submit';
		
			global $_POST;
			$_POST = array();
			$_POST = $ArraytoApi;
			smackContactFormGeneratorPRO($code , 'thirdparty');
			callCurlPRO('post');
			return true;
		}
	}
}
?>
