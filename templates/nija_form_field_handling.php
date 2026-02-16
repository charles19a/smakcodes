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
add_action( 'init', 'ninja_forms_register_example' );
function ninja_forms_register_example(){
	add_action('ninja_forms_save_sub' , 'ninja_forms_example');
}

function ninja_forms_example($arr){
	global $wpdb,$HelperObj;
	$new_form_array=[];
	$smackfieldname = $OriginalMap = $code = [];
	$posted_array = json_decode( stripslashes( $_POST['formData'] ), TRUE  );
	$form_id = $posted_array['id'];
	$posted_fields = $posted_array['fields'];

	$ninja_posted_array = array();	
	$active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
	//form the array of posted_values
	foreach($posted_fields as $ninja_key => $ninja_value){
		if(is_array($ninja_value['value']))
		{
			$get_multi_vals = '';
			if($active_crm =='wpzohopro'){
				$ninja_posted_array[$ninja_value['id']] = $ninja_value['value'];

			}
			else{
				foreach($ninja_value['value'] as $multi_vals)
				{
					$get_multi_vals .= $multi_vals." |##| ";
				}
				$get_multi_vals = substr($get_multi_vals,0,-6);
				$ninja_posted_array[$ninja_value['id']] = $get_multi_vals;
			}
			
		}else{
			$ninja_posted_array[$ninja_value['id']] = $ninja_value['value'];
		}
	}
	//Get all field labels from table
	$get_fields = $wpdb->get_results($wpdb->prepare("select id,label from {$wpdb->prefix}nf3_fields where parent_id=%d and type!=%s" , $form_id , 'submit'));

	//Form the array with label , value pair
	foreach($get_fields as $field_key => $field_val )
	{	
		$new_form_array[$field_val->label] = $ninja_posted_array[$field_val->id];

	}
	$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
	$thirdparty = 'ninjaform';
	$get_ninja_option = $activated_crm.'_wp_ninja'.$form_id;
	$check_map_exist = get_option( $get_ninja_option );
	if( !empty( $check_map_exist ))
	{
		$module = $check_map_exist['third_module'];
		$all_fields = $new_form_array;
		$i =0;
		foreach( $all_fields as $post_key => $post_val )
		{
			$ninja_posted_array[$i] = $post_val;
			$i++;
		}

		//Mapped config keys
		$mapped_array = $check_map_exist['fields'];
		$mapped_array_key_labels = array_keys( $mapped_array );

		$get_json_array = $wpdb->get_results( $wpdb->prepare( "select parent_id,label from {$wpdb->prefix}nf3_fields where parent_id=%d and type !=%s" , $form_id , 'submit' ) );
		$i = 0;
		$ninja_form_labels = array();
		foreach( $get_json_array as $ninja_key => $ninja_data )
		{
			$ninja_form_labels[$i] = $ninja_data->label;
			$i++;
		}

		//get mapped label keys from gravity array
		foreach( $ninja_form_labels as $nf_key => $nf_val)
		{
			foreach( $mapped_array_key_labels as $labels )
			{
				if( $labels == $nf_val && $labels!='gclid_value' )
				{
					$index = $nf_key;
					$field_name = $mapped_array[$labels];
					$user_value = $ninja_posted_array[$index];
					$data_array[$field_name] = $user_value;
				}
				if( $labels == 'gclid_value' )
				{
					$index = $nf_key;
					$field_name = $mapped_array[$labels];
					$user_value = $_COOKIE['gclid'];
					$data_array[$field_name] = $user_value;
				}
			}
		}//Get Mapped Array
		$activatedPlugin = $check_map_exist['thirdparty_crm'];
		foreach($data_array as $key=>$value)
		{                         
			if($value=='checked' || $value == 1)
			{
				switch($activatedPlugin)
				{
				case 'wptigerpro':
					$data_array[$key] ='1';
					break;

				case 'wpsugarpro':
				case 'wpsuitepro':
					$data_array[$key] ='on';
					break;

				case 'wpzohopro':
				case 'wpzohopluspro':
					$data_array[$key] =true;
					break;

				case 'wpsalesforcepro':
					$data_array[$key] ='on';
					break;

				case 'freshsales':
					$data_array[$key] ='1';
					break;
				}
			}
		}
		if($activatedPlugin == "wpsalesforcepro" && $module == "Contacts")
		{
			$data_array['Birthdate'] = date("Y-m-d", strtotime($data_array['Birthdate']));
		}
		if($activatedPlugin == "wptigerpro" && $module == "Contacts")
		{
			$data_array['birthday'] = date("Y-m-d", strtotime($data_array['birthday']));
			$data_array['support_start_date'] = date("Y-m-d", strtotime($data_array['support_start_date']));
			$data_array['support_end_date'] = date("Y-m-d", strtotime($data_array['support_end_date']));
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
		$ArraytoApi['shortcode'] = $get_ninja_option;
		$ArraytoApi['duplicate_option'] = $check_map_exist['thirdparty_duplicate'];
		$capture_obj = new CapturingProcessClassPRO();
		$capture_obj->thirdparty_mapped_submission($ArraytoApi);
		return true;
	}
	else
	{	
		//check with the smack form relation
		$smack_shortcode = $wpdb->get_var($wpdb->prepare("select shortcode from wp_smackformrelation where thirdpartyid=%d and thirdparty=%s", $form_id , $thirdparty ));
		if( empty( $smack_shortcode ))
		{
			//Do nothing- Normal Ninja Form submission.
		}
		else
		{
			//Get all the user submitted values
			$all_fields = $new_form_array;
			//Mapping for Ninja forms and smack forms
			$mapping = $wpdb->get_results($wpdb->prepare("select smackfieldid,thirdpartyfieldids from wp_smackthirdpartyformfieldrelation where thirdpartyformid=%d", $form_id),ARRAY_A);

			foreach($mapping as $key=>$value)	{
				$smackfiledid[$key]= $value['smackfieldid'];
				$thirdpartyfiledid[$key] = $value['thirdpartyfieldids'];
			}

			//get name from smack from tables
			$smackfieldName = $wpdb->get_results($wpdb->prepare(" select a.field_name , a.field_values , a.field_type from wp_smackleadbulider_field_manager as a join wp_smackleadbulider_form_field_manager as b join wp_smackthirdpartyformfieldrelation as c where b.field_id=a.field_id and c.smackfieldid=b.rel_id and thirdpartyformid=%d" , $form_id), ARRAY_A);

			foreach($smackfieldName as $key=>$value)	
			{
				$smackfieldname[$key] = $value['field_name'];
			}
			$thirdpartyfiledid = array_flip($thirdpartyfiledid);

			foreach($thirdpartyfiledid as $key=>$value)	
			{
				$OriginalMap[$key] = $smackfieldname[$value];
			}
			if( is_array( $all_fields ) ){ //Make sure $all_fields is an array.
				//Loop through each of our submitted values.
				foreach( $all_fields as $field_id => $user_value ){
					//Do something with those values
					$ArraytoApi[$OriginalMap[$field_id]] = $user_value;
				}
				$code['name'] = $smack_shortcode;
				$newform = new CaptureData();
				$newshortcode = $newform->formfields_settings( $code['name'] );
				$FormSettings = $newform->getFormSettings( $code['name'] );
				$module = $FormSettings->module; 
				$ArraytoApi['moduleName'] = $module;
				$ArraytoApi['formnumber'] = $form_id;
				$ArraytoApi['submit'] = 'Submit'; 
				$activatedPlugin = $HelperObj->ActivatedPlugin;
				foreach($ArraytoApi as $key=>$value)
				{
					if($key=='')
					{
						$noe = $key;
					}
					if($value=='checked')
					{
						switch($activatedPlugin)
						{
						case 'wptigerpro':
							$ArraytoApi[$key] ='1';
							break;

						case 'wpsugarpro':
						case 'wpsuitepro':
							$ArraytoApi[$key] ='on';
							break;

						case 'wpzohopro':
						case 'wpzohopluspro':
							$ArraytoApi[$key] ='true';
							break;

						case 'wpsalesforcepro':
							$ArraytoApi[$key] ='on';
							break;

						case 'freshsales':
							$ArraytoApi[$key] ='1';
							break;				
						}
					}
				}
				unset($ArraytoApi[$noe]);
				if($activatedPlugin == "wpsalesforcepro" && $module == "Contacts")
				{
					$ArraytoApi['Birthdate'] = date("Y-m-d", strtotime($ArraytoApi['Birthdate']));
				}
				if($activatedPlugin == "wptigerpro" && $module == "Contacts")
				{
					$ArraytoApi['birthday'] = date("Y-m-d", strtotime($ArraytoApi['birthday']));
					$ArraytoApi['support_start_date'] = date("Y-m-d", strtotime($ArraytoApi['support_start_date']));
					$ArraytoApi['support_end_date'] = date("Y-m-d", strtotime($ArraytoApi['support_end_date']));
				}

				if( $activatedPlugin == 'freshsales' )
				{
					foreach( $smackfieldName as $sm_key => $sm_value )
					{
						foreach( $ArraytoApi as $API_key => $API_val )
						{
							if( $sm_value['field_name'] == $API_key && $sm_value['field_values'] != '' )
							{
								$get_choices = unserialize( $sm_value['field_values'] );
								foreach( $get_choices as $choice_key => $choice_val )
								{
									if( $choice_val['label'] == $API_val )
									{
										$ArraytoApi[$API_key] = $choice_val['id'];
									}
								}
							}
							if( $sm_value['field_name'] == $API_key && $sm_value['field_type'] == 'boolean' && $API_val == "" )
							{
								$ArraytoApi[$API_key] = '0';
							}
						}
					}
				}

				global $_POST;
				$_POST = array();
				$_POST = $ArraytoApi;
				smackContactFormGeneratorPRO($code , 'thirdparty');
				callCurlPRO('post');
				return true;	
			}
		}//End 
	}
}
?>
