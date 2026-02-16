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

add_action( 'elementor_pro/forms/validation', 'getElementorFormData', 10, 2 );

function getElementorFormData( $record= '', $ajax_handler= '' ){

	global $wpdb;
	$dataArray=[];
	$arraytoApi=[];
	$formId = $_POST['post_id'];
	$activatedCrm = get_option( 'WpLeadBuilderProActivatedPlugin' );
	$getElementorOption = $activatedCrm.'_wp_elementor'.$formId;
	$checkMapExist = get_option( $getElementorOption );
	$getShortcode = $activatedCrm.'_wp_elementor'.$formId;
	$crmFields = $checkMapExist['fields'];
	$formFields = $_POST['form_fields'];
	$combinedFields = [];
	foreach($crmFields as $key => $val)
	{
		if(array_key_exists($key, $formFields))
		{
			$combinedFields[$val] = $formFields[$key];
		}
	}

	if( (!empty( $checkMapExist ))){
		$mappedFields = $_POST['form_fields'];
		$submittedFields = [];
		//$eleFormEntries = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cf_form_entry_values WHERE entry_id = $entry_id ", ARRAY_A);
		$eleFormEntries = $wpdb->get_results(
			$wpdb->prepare("
				SELECT * FROM {$wpdb->prefix}cf_form_entry_values 
				WHERE entry_id = %d
			", $entry_id),
			ARRAY_A
		);	
		foreach($eleFormEntries as $eleFormVals){
			$submittedFields[$eleFormVals['field_id']] = $eleFormVals['value'];
		}	
	
		foreach($mappedFields as $mappedKey => $mappedValue){
			if(array_key_exists($mappedKey, $submittedFields)){
				$checkJson = json_decode($submittedFields[$mappedKey], true);	
				if(is_array($checkJson)){
					$finalVal = array_values($checkJson);
				}
				else{
					$finalVal = $submittedFields[$mappedKey];
				}	
				$dataArray[$mappedValue] = $finalVal;	
			}
		}

		$arraytoApi['posted'] = $combinedFields;
		$arraytoApi['third_module'] = $checkMapExist['third_module'];
		$arraytoApi['thirdparty_crm'] = $checkMapExist['thirdparty_crm'];
		$arraytoApi['third_plugin'] = $checkMapExist['third_plugin'];
		$arraytoApi['form_title'] = $checkMapExist['form_title'];
		$arraytoApi['shortcode'] = $getShortcode;
		$arraytoApi['duplicate_option'] = $checkMapExist['thirdparty_duplicate'];
		$captureObj = new CapturingProcessClassPRO();
		$captureObj->thirdparty_mapped_submission($arraytoApi);

	}
}
?>
