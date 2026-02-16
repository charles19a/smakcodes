<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
include_once(SM_LB_PRO_DIR.'lib/SmackZohoApi.php');
class PROFunctions{
	public $username;
	public $accesskey;
	public $authtoken;
	public $url;
	public $result_emails;
	public $result_ids;
	public $result_products;
	public $domain;
	public $activated_plugin;
	public $access_token;
	public $refresh_token;
	public $client_id;
	public $client_secret;
	public function __construct()
	{
		$this->activated_plugin = get_option("WpLeadBuilderProActivatedPlugin");
		$zohoconfig=get_option("wp_{$this->activated_plugin}_settings");
		$this->access_token=$zohoconfig['access_token'];
		$this->refresh_token=$zohoconfig['refresh_token'];
		$this->client_id=$zohoconfig['key'];
		$this->client_secret=$zohoconfig['secret'];
		$this->domain=$zohoconfig['domain'];
	}

	public function login()
	{
		$client = new SmackZohoApi();
		return $client;
	}

	public function getAuthenticationKey( $username , $password )
	{
		$client = $this->login();
		$return_array = $client->getAuthenticationToken( $username , $password  );
		return $return_array;
	}


	public function getCrmFields( $module )
	{
		$data=[];
		$config=[];
		$client = new SmackZohoApi();
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		$token = isset($SettingsConfig['authtoken']) ? $SettingsConfig['authtoken'] : '';
		$this->authtoken = $token;
		$recordInfo = $client->APIMethod( $module , "getFields" , $this->authtoken );
		if(!empty($recordInfo['code'])){
			if($recordInfo['code']=='INVALID_TOKEN' || $recordInfo['code']=='AUTHENTICATION_FAILURE'){
				$get_access_token=$client->refresh_token();

				// Mari added
				if(isset($get_access_token['error'])){
					if($get_access_token['error'] == 'access_denied'){
						$data['result'] = "failure";
						$data['failure'] = 1;
						$data['reason'] = "Access Denied to get the refresh token";
						return $data;
					}
				}

				$exist_config = get_option("wp_{$this->activated_plugin}_settings");
				$config['access_token']=$get_access_token['access_token'];
				$config['api_domain']=$get_access_token['api_domain'];
				$config['key']=$exist_config['key'];
				$config['secret']=$exist_config['secret'];
				$config['callback']=$exist_config['callback'];
				$config['refresh_token']=$exist_config['refresh_token'];
				$config['domain']=$exist_config['domain'];
				update_option("wp_{$this->activated_plugin}_settings",$config);
				$this->getCrmFields($module);
			}

		}

		$config_fields = array();
		$AcceptedFields = Array( 'textarea' => 'text' , 'text' => 'string' , 'email' => 'email' , 'boolean' => 'boolean', 'picklist' => 'picklist' , 'varchar' => 'string' , 'website' => 'url' , 'phone' => 'phone' , 'Multi Pick List' => 'multipicklist' , 'radioenum' => 'radioenum', 'currency' => 'currency' , 'dateTime' => 'date' ,  'integer' => 'string' , 'BigInt' => 'string' , 'double' => 'string');
		$j = 0;
		if(isset($recordInfo['fields'])){
			foreach($recordInfo['fields'] as $key => $fields )
			{
				if( ($key != '@attributes') )
				{
					if($fields['api_name']=='Company'||$fields['api_name']=='Last_Name')
					{
						$fields['req']='true';
					}				
					if(isset($fields['req']) && $fields['req'] == 'true' )
					{
						$config_fields['fields'][$j]['wp_mandatory'] = 1;
						$config_fields['fields'][$j]['mandatory'] = 2;
					}
					else
					{
						$config_fields['fields'][$j]['wp_mandatory'] = 0;
					}
					if(($fields['data_type'] == 'picklist') || ($fields['data_type'] == 'Multi Pick List') || ($fields['data_type'] == 'Radio')){
						$optionindex = 0;
						$picklistValues = array();
						foreach($fields['pick_list_values'] as $option)
						{
							$picklistValues[$optionindex]['display_value'] = $option ;
							$picklistValues[$optionindex]['actual_value'] = $option;
							$optionindex++;
						}
						$config_fields['fields'][$j]['type'] = Array ( 'name' => $AcceptedFields[$fields['data_type']] , 'picklistValues' => $picklistValues );
					}
					else
					{
						$attr = isset($AcceptedFields[$fields['data_type']]) ? $AcceptedFields[$fields['data_type']] : '';
						$config_fields['fields'][$j]['type'] = array("name" => $attr);
					}

					$config_fields['fields'][$j]['name'] = $fields['api_name'];
					$config_fields['fields'][$j]['fieldname'] = $fields['api_name'];
					$config_fields['fields'][$j]['label'] = $fields['field_label'];
					$config_fields['fields'][$j]['display_label'] = $fields['field_label'];
					$config_fields['fields'][$j]['publish'] = 1;
					$config_fields['fields'][$j]['order'] = $j;
					$j++;
				}
				elseif(isset($fields['@attributes']) && $fields['@attributes']['isreadonly'] == 'false' && ( $fields['@attributes']['type'] != 'Lookup' ) && ( $fields['@attributes']['type'] != 'OwnerLookup' ) && ( $fields['@attributes']['type'] != 'Lookup' ) )
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
					$config_fields['fields'][$j]['publish'] = 1;
					$config_fields['fields'][$j]['order'] = $j;
					$j++;
				}

			}

		}

		$config_fields['check_duplicate'] = 0;
		$config_fields['isWidget'] = 0;
		$users_list = $this->getUsersList();
		$users  = isset($users_list) ? $users_list : '';
		$usersid = isset($users['id']) ? $users['id'] : '';
		$usersids = isset($users[0]) ? $users[0] : '';
		$config_fields['assignedto'] = $usersids;
		$config_fields['module'] = $module;
		return $config_fields;
	}

	public function getUsersList()
	{
		$client=new SmackZohoApi();
		$records = $client->Zoho_Getuser();
		$config=[];
		$data=[];

		if(!empty($records['code'])){
			if($records['code']=='INVALID_TOKEN' || $records['code']=='AUTHENTICATION_FAILURE'){
				$get_access_token=$client->refresh_token();

				// Mari added
				if(isset($get_access_token['error'])){
					if($get_access_token['error'] == 'access_denied'){
						$data['result'] = "failure";
						$data['failure'] = 1;
						$data['reason'] = "Access Denied to get the refresh token";
						return $data;
					}
				}

				$exist_config = get_option("wp_{$this->activated_plugin}_settings");
				$config['access_token']=$get_access_token['access_token'];
				$config['api_domain']=$get_access_token['api_domain'];
				$config['key']=$exist_config['key'];
				$config['secret']=$exist_config['secret'];
				$config['callback']=$exist_config['callback'];
				$config['refresh_token']=$exist_config['refresh_token'];
				$config['domain']=$exist_config['domain'];
				update_option("wp_{$this->activated_plugin}_settings",$config);
				$this->getUsersList();
			}

		}

		elseif( isset( $records['users']['@attributes'] ) ) {
			$user_details['user_name'][] = $records['users']['@attributes']['email'];
			$user_details['id'][] = $records['user']['@attributes']['id'];
			$user_details['first_name'][] = $records['user']['@attributes']['email'];
			$user_details['last_name'][] = "";
		}
		else
		{   
			if(isset($records['users'])){
				foreach($records['users'] as $record) {
					$user_details['user_name'][] = $record['email'];
					$user_details['id'][] = $record['id'];
					$user_details['first_name'][] = $record['first_name']; 
					$user_details['last_name'][] = ""; 

				}
			}
			else{
				$user_details = '';
			}

		}
		return $user_details;
	}

	public function getUsersListHtml( $shortcode = "" )
	{
		$HelperObj = new WPCapture_includes_helper_PRO();

		$activatedplugin = $HelperObj->ActivatedPlugin;
		$formObj = new CaptureData();
		if(isset($shortcode) && ( $shortcode != "" ))
		{
			$config_fields = $formObj->getFormSettings( $shortcode );  // Get form settings 
		}
		$users_list = get_option('crm_users');
		$users_list = $users_list[$activatedplugin];
		$html = "";
		$html = '<select class="form-control" name="assignedto" id="assignedto">';
		$content_option = "";
		if(isset($users_list['user_name']))
		$count=count($users_list['user_name']);
			for($i = 0; $i < $count ; $i++)
			{
				$content_option.="<option id='{$users_list['user_name'][$i]}' value='{$users_list['id'][$i]}'";
				if($users_list['id'][$i] == $config_fields->assigned_to)
				{
					$content_option.=" selected";
				}
				$content_option.=">{$users_list['user_name'][$i]}</option>";
			}
		$content_option .= "<option id='owner_rr' value='Round Robin'";
		if( $config_fields->assigned_to == 'Round Robin' )
		{
			$content_option .= "selected";
		}
		$content_option .= "> Round Robin </option>";

		$html .= $content_option;
		$html .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		return $html;
	}

	public function getAssignedToList()
	{
		$user_list_array=[];
		$users_list = $this->getUsersList();
		$count = count($users_list['user_name']);
		for($i = 0; $i < $count ; $i++)
		{
			$user_list_array[$users_list['user_name'][$i]] = $users_list['user_name'][$i];
		}
		return $user_list_array;
	}

	public function mapUserCaptureFields( $user_firstname , $user_lastname , $user_email )
	{
		$post = array();
		$post['First_Name'] = $user_firstname;
		$post['Last_Name'] = $user_lastname;
		$post[$this->duplicateCheckEmailField()] = $user_email;
		return $post;
	}
	public function assignedToFieldId()
	{
		return "Lead_Owner";
	}

	public function createRecord( $module , $module_fields )
	{	
		$zohoapi=new SmackZohoApi();
		$data=[];
		$config=[];
		$owner_id = '';
		if ( isset( $module_fields['SMOWNERID'] ) ) {
			$owner_id = $module_fields['SMOWNERID'];
		} elseif ( isset( $module_fields['Lead_Owner'] ) ) {
			$owner_id = $module_fields['Lead_Owner'];
		}
		if ( $owner_id !== '' && $owner_id !== null ) {
			$module_fields['Owner'] = array( 'id' => (string) $owner_id );
		}
		$fields_to_skip = ['Digital_Interaction_s', 'Solution'];
		foreach($module_fields as $fieldname => $fieldvalue){
			if(!in_array($fieldname, $fields_to_skip)){
				continue;
			}

			$module_fields[$fieldname] = array();

			if(is_string($fieldvalue)){
				array_push($module_fields[$fieldname], $fieldvalue);
			}else if(is_array($fieldvalue)){
				array_push($module_fields[$fieldname], $fieldvalue);
			}
		}

		$attachments = isset($module_fields['attachments']) ? $module_fields['attachments'] : '';
		unset($module_fields['attachments']);
		unset($module_fields['SMOWNERID']);
		unset($module_fields['Lead_Owner']);
		$body_json = array();
		$body_json["data"] = array();

		array_push($body_json["data"], $module_fields);

		$record = $zohoapi->Zoho_CreateRecord( $module,$body_json,$attachments);

		$record_code = '';
		if ( is_array( $record ) ) {
			if ( isset( $record['code'] ) ) {
				$record_code = $record['code'];
			} elseif ( isset( $record['data'][0]['code'] ) ) {
				$record_code = $record['data'][0]['code'];
			}
		}

		if ( $record_code == 'INVALID_TOKEN' || $record_code == 'AUTHENTICATION_FAILURE' ) {
			$get_access_token = $zohoapi->refresh_token();

			if ( ! is_array( $get_access_token ) ) {
				$data['result'] = "failure";
				$data['failure'] = 1;
				$data['reason'] = "Failed to refresh access token";
				return $data;
			}

			if ( isset( $get_access_token['error'] ) ) {
				$data['result'] = "failure";
				$data['failure'] = 1;
				$data['reason'] = "Zoho token refresh failed: " . $get_access_token['error'];
				return $data;
			}

			if ( empty( $get_access_token['access_token'] ) ) {
				$data['result'] = "failure";
				$data['failure'] = 1;
				$data['reason'] = "Zoho token refresh failed: missing access token";
				return $data;
			}

			$exist_config = get_option( "wp_{$this->activated_plugin}_settings" );
			if ( ! is_array( $exist_config ) ) {
				$exist_config = array();
			}

			$config = $exist_config;
			$config['access_token'] = $get_access_token['access_token'];
			if ( ! empty( $get_access_token['api_domain'] ) ) {
				$config['api_domain'] = $get_access_token['api_domain'];
			}
			update_option( "wp_{$this->activated_plugin}_settings", $config );

			$zohoapi->access_token = $config['access_token'];
			if ( ! empty( $config['api_domain'] ) ) {
				$zohoapi->zohoapidomain = $config['api_domain'];
			}

			$record = $zohoapi->Zoho_CreateRecord( $module, $body_json, $attachments );
		}

		if ( is_array( $record ) && isset( $record['data'][0]['code'] ) && $record['data'][0]['code'] == 'SUCCESS' )
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			if ( is_array( $record ) && isset( $record['data'][0]['message'] ) ) {
				$data['reason'] = $record['data'][0]['message'];
			} elseif ( is_array( $record ) && isset( $record['message'] ) ) {
				$data['reason'] = $record['message'];
			} else {
				$data['reason'] = "failed adding entry";
			}
		}
		return $data;
	}

	public function replace_key_function($module_fields, $key1, $key2)
	{
		$keys = array_keys($module_fields);
		$index = array_search($key1, $keys);
		if ($index !== false) {
			$keys[$index] = $key2;
			$module_fields = array_combine($keys, $module_fields);
		}
		return $module_fields;
	}
	public function createRecordOnUserCapture( $module , $module_fields )
	{
		$zohoapi=new SmackZohoApi();
		$module_field=[];
		$data=[];
		$config=[];
		$module_field['data']=array($module_fields);
		$module_field['Owner']['id']=$module_fields['Lead_Owner'];
		$record = $zohoapi->Zoho_CreateRecord( $module,$module_field);
		if($record['code']=='INVALID_TOKEN' || $record['code']=='AUTHENTICATION_FAILURE'){
			$get_access_token=$zohoapi->refresh_token();

			// Mari added
			if(isset($get_access_token['error'])){
				if($get_access_token['error'] == 'access_denied'){
					$data['result'] = "failure";
					$data['failure'] = 1;
					$data['reason'] = "Access Denied to get the refresh token";
					return $data;
				}
			}

			$exist_config = get_option("wp_{$this->activated_plugin}_settings");
			$config['access_token']=$get_access_token['access_token'];
			$config['api_domain']=$get_access_token['api_domain'];
			$config['key']=$exist_config['key'];
			$config['secret']=$exist_config['secret'];
			$config['callback']=$exist_config['callback'];
			$config['refresh_token']=$exist_config['refresh_token'];
			$config['domain']=$exist_config['domain'];
			update_option("wp_{$this->activated_plugin}_settings",$config);
			$this->createRecord($module, $module_fields);
		}          
		elseif( $record['data'][0]['code']=='SUCCESS')
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed adding entry";
		}
		return $data;
	}


	public function createEcomRecord($module, $module_fields , $order_id )
	{
		$client = $this->login();
		$product_array_fields = $module_fields;
		global $wpdb;
		$module_field=[];
		$data=[];
		$attachment='';
		//LEADS / CONTACTS
		if( $module == 'Leads' || $module == 'Contacts' )
		{
			// If other language used --> change field name => label
			$zohoapi=new SmackZohoApi();
			$module_field['data']=array($module_fields);
			$module_field['Owner']['id']=$module_fields['Lead_Owner'];
			$record = $zohoapi->Zoho_CreateRecord( $module, $module_field ,$attachment);

		}
		//PRODUCT MODULE
		if( $module == 'Products' )
		{
			$postfields_product = "<{$module}>\n<row no=\"1\">\n";

			if(isset($product_array_fields))
			{
				foreach($product_array_fields as $key => $value)
				{
					$postfields_product .= "<FL val=\"".$key."\">".$value."</FL>\n";
				}
			}
			$postfields_product .= "</row>\n</$module>";
			$record = $client->insertRecord( $module , "insertRecords" , $this->authtoken ,  $postfields_product );
		}
		if( $record['data'][0]['code']=='SUCCESS')
		{
			$data['result'] = "success";
			$data['failure'] = 0;

			if( $module == "Leads" )
			{
				$crm_id = $record['data'][0]['details']['id'];
				$my_leadid = $crm_id;
				$crm_name = 'wpzohopro';
				if( is_user_logged_in() )
				{
					$user_id = get_current_user_id();
					$is_user = 1;
				}else
				{
					$user_id = 'guest';
					$is_user = 0;
				}
				$lead_no = $crm_id;
				$wpdb->insert( 'wp_smack_ecom_info' , array( 'crmid' => $crm_id , 'crm_name' => $crm_name , 'wp_user_id' => $user_id , 'is_user' => $is_user , 'lead_no' => $my_leadid , 'order_id' => $order_id ) );
			}
			if( $module == 'Contacts' )
			{
				$crm_id = $record['data']['id'];
				$crm_name = 'wpzohopro';
				$my_contactid = $crm_id;
				if( is_user_logged_in() )
				{
					$user_id = get_current_user_id();
					$is_user = 1;
				}else
				{
					$user_id = 'guest';
					$is_user = 0;
				}
				$contact_no = $crm_id;
				$wpdb->insert( 'wp_smack_ecom_info' , array( 'crmid' => $crm_id , 'crm_name' => $crm_name , 'wp_user_id' => $user_id , 'is_user' => $is_user , 'contact_no' => $my_contactid , 'order_id' => $order_id ) );

			}
			if( $module == 'Products' )
			{
				$crm_id = $record['result']['recorddetail']['FL'][0];
				$crm_name = 'wpzohopro';
				$get_product = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=$order_id" ) );
				$prod_id = $get_product[0]->product_id;
				if( !empty( $prod_id ) )
				{
					$crm_id = $prod_id.",".$crm_id;
				}
				$wpdb->insert( 'wp_smack_ecom_info' , array( 'product_id' => $crm_id ) , array( 'order_id' => $order_id ));             
			}
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed adding entry";
		}
		return $data;
	}

	public function updateEcomRecord( $module , $module_fields , $ids_present, $order_id )
	{
		$client = $this->login();
		$product_array_fields = $module_fields;
		global $wpdb;
		$data=[];

		//PRODUCT MODULE
		if( $module == 'Products' )
		{
			$postfields_product = "<{$module}>\n<row no=\"1\">\n";

			if(isset($product_array_fields))
			{
				foreach($product_array_fields as $key => $value)
				{
					$postfields_product .= "<FL val=\"".$key."\">".$value."</FL>\n";
				}
			}
			$postfields_product .= "</row>\n</$module>";
			$extraparams = "&id={$ids_present}";
			$record = $client->insertRecord( $module , "updateRecords" , $this->authtoken ,  $postfields_product , $extraparams);

			if( isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) updated successfully" ) )
			{
				$data['result'] = "success";
				$data['failure'] = 0;

				if( $module == 'Products' )
				{
					$crm_id = $record['result']['recorddetail']['FL'][0];
					$crm_name = 'wpzohopro';
					$get_product = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=$order_id" ) );
					$prod_id = $get_product[0]->product_id;
					if( !empty( $prod_id ) )
					{
						$crm_id = $prod_id.",".$crm_id;
					}
					$wpdb->update( 'wp_smack_ecom_info' , array( 'product_id' => $crm_id ) , array( 'order_id' => $order_id ));
				}

			}
			else
			{
				$data['result'] = "failure";
				$data['failure'] = 1;
				$data['reason'] = "failed updating entry";
			}

			return $data;
		}
	}

	//Convert Lead
	public  function convertLead( $module , $crm_id , $order_id , $lead_no , $sales_order)
	{
		$client = $this->login();	
		$data=[];
		$final_result = $client->convertLeads( $module , $crm_id , $order_id , $lead_no , $this->authtoken , $sales_order);
		$sales_order['SMOWNERID'] = $final_result['SMOWNERID'];
		$sales_order['CONTACTID'] = $final_result['CONTACT_ID'];
		$sales_order['ACCOUNTID'] = $final_result['ACCOUNT_ID'];

		$SO_fields = "<SalesOrders>\n<row no=\"1\">\n";
		foreach($sales_order as $key => $value)
		{
			if( $key != 'Product Details' )
			{
				$SO_fields .= "<FL val=\"".$key."\">".$value."</FL>\n";
			}
			else
			{
				$SO_fields .= "<FL val=\"".$key."\">";

				foreach( $value as $prod_key => $prod_val  )
				{
					$SO_fields .= "<product no=\"".$prod_key."\">\n";
					foreach( $prod_val as $item_key => $item_val )
					{       
						$SO_fields .= "<FL val=\"".$item_key."\">".$item_val."</FL>\n"; 
					}
					$SO_fields .= "</product>";
				}
				$SO_fields .= "</FL>\n";
			}
		}
		$SO_fields .= "</row>\n</SalesOrders>";
		$record = $client->insertRecord( 'SalesOrders' , "insertRecords" , $this->authtoken ,  $SO_fields );
		$sales_orderid = $record['result']['recorddetail']['FL'][0];
		global $wpdb;
		$wpdb->update( 'wp_smack_ecom_info' , array('contact_no' => $final_result['data'][0]['details']['id']) , array( 'order_id' => $order_id ) );
		$wpdb->update( 'wp_smack_ecom_info' , array('sales_orderid' => $sales_orderid) , array( 'order_id' => $order_id ) );
		if($record)
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed updating entry";
		}
		return $data;
	}	
	public function create_sales_order( $module , $order_id , $contact_id , $sales_order)
	{
		$client = $this->login();
		$data=[];
		$contact_owner  = $client->getConvertLeadOwner($module , $this->authtoken , $contact_id );
		$Account_id = $client->getAccountId( $this->authtoken );
		$sales_order['SMOWNERID'] = $contact_owner;
		$sales_order['CONTACTID'] = $contact_id;
		$sales_order['ACCOUNTID'] = $Account_id;

		$SO_fields = "<SalesOrders>\n<row no=\"1\">\n";
		foreach($sales_order as $key => $value)
		{
			if( $key != 'Product Details' )
			{
				$SO_fields .= "<FL val=\"".$key."\">".$value."</FL>\n";
			}
			else
			{
				$SO_fields .= "<FL val=\"".$key."\">";

				foreach( $value as $prod_key => $prod_val  )
				{
					$SO_fields .= "<product no=\"".$prod_key."\">\n";
					foreach( $prod_val as $item_key => $item_val )
					{       
						$SO_fields .= "<FL val=\"".$item_key."\">".$item_val."</FL>\n"; 
					}
					$SO_fields .= "</product>";
				}
				$SO_fields .= "</FL>\n";
			}
		}
		$SO_fields .= "</row>\n</SalesOrders>";
		$record = $client->insertRecord( 'SalesOrders' , "insertRecords" , $this->authtoken ,  $SO_fields );
		$sales_orderid = $record['result']['recorddetail']['FL'][0];
		global $wpdb;

		$wpdb->update( 'wp_smack_ecom_info' , array('sales_orderid' => $sales_orderid) , array( 'order_id' => $order_id ) );
		if($record)
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed updating entry";
		}
		return $data;
	}

	public function updateRecord( $module , $module_fields , $ids_present )
	{
		$data = [];
		$config = [];
		$fields_to_skip = ['Digital_Interaction_s', 'Solution'];
		foreach($module_fields as $fieldname => $fieldvalue){
			if(!in_array($fieldname, $fields_to_skip)){
				continue;
			}

			$module_fields[$fieldname] = array();

			if(is_string($fieldvalue)){
				array_push($module_fields[$fieldname], $fieldvalue);
			}else if(is_array($fieldvalue)){
				array_push($module_fields[$fieldname], $fieldvalue);
			}
		}


		$owner_id = '';
		if ( isset( $module_fields['SMOWNERID'] ) ) {
			$owner_id = $module_fields['SMOWNERID'];
		} elseif ( isset( $module_fields['Lead_Owner'] ) ) {
			$owner_id = $module_fields['Lead_Owner'];
		}
		if ( $owner_id !== '' && $owner_id !== null ) {
			$module_fields['Owner'] = array( 'id' => (string) $owner_id );
		}

		$attachments = isset($module_fields['attachments']) ? $module_fields['attachments'] : '';
		unset($module_fields['attachments']);
		unset($module_fields['SMOWNERID']);
		unset($module_fields['Lead_Owner']);

		$zohoapi = new SmackZohoApi();

		$record = $zohoapi->Zoho_UpdateRecord( $module, $module_fields, $ids_present );

		$record_code = '';
		if ( is_array( $record ) ) {
			if ( isset( $record['code'] ) ) {
				$record_code = $record['code'];
			} elseif ( isset( $record['data'][0]['code'] ) ) {
				$record_code = $record['data'][0]['code'];
			}
		}

		if ( $record_code == 'INVALID_TOKEN' || $record_code == 'AUTHENTICATION_FAILURE' ) {
			$get_access_token = $zohoapi->refresh_token();

			if ( ! is_array( $get_access_token ) ) {
				$data['result'] = "failure";
				$data['failure'] = 1;
				$data['reason'] = "Failed to refresh access token";
				return $data;
			}

			if ( isset( $get_access_token['error'] ) ) {
				$data['result'] = "failure";
				$data['failure'] = 1;
				$data['reason'] = "Zoho token refresh failed: " . $get_access_token['error'];
				return $data;
			}

			if ( empty( $get_access_token['access_token'] ) ) {
				$data['result'] = "failure";
				$data['failure'] = 1;
				$data['reason'] = "Zoho token refresh failed: missing access token";
				return $data;
			}

			$exist_config = get_option( "wp_{$this->activated_plugin}_settings" );
			if ( ! is_array( $exist_config ) ) {
				$exist_config = array();
			}

			$config = $exist_config;
			$config['access_token'] = $get_access_token['access_token'];
			if ( ! empty( $get_access_token['api_domain'] ) ) {
				$config['api_domain'] = $get_access_token['api_domain'];
			}
			update_option( "wp_{$this->activated_plugin}_settings", $config );

			$zohoapi->access_token = $config['access_token'];
			if ( ! empty( $config['api_domain'] ) ) {
				$zohoapi->zohoapidomain = $config['api_domain'];
			}

			$record = $zohoapi->Zoho_UpdateRecord( $module, $module_fields, $ids_present );
		}

		if ( is_array( $record ) && isset( $record['data'][0]['code'] ) && $record['data'][0]['code'] == 'SUCCESS' )
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			if ( is_array( $record ) && isset( $record['data'][0]['message'] ) ) {
				$data['reason'] = $record['data'][0]['message'];
			} elseif ( is_array( $record ) && isset( $record['message'] ) ) {
				$data['reason'] = $record['message'];
			} else {
				$data['reason'] = "failed adding entry";
			}
		}
		return $data;
	}
	public function checkEmailPresent( $module , $email )
	{
		$result_emails = $config = $result_lastnames = array();
		$result_ids = $data = array();
		$client = new SmackZohoApi();
		$email_present = "no";
		$extraparams = "&searchCondition=(Email|=|{$email})"; // Old API Method for search record
		$records = $client->getRecords( $module , $email , $this->authtoken , "Id , Email" , "" , $extraparams ); // Replaced getSearchRecords by searchRecords
		if(isset($records['code'])){
			if($records['code']=='INVALID_TOKEN' || $records['code']=='AUTHENTICATION_FAILURE'){
				$get_access_token=$client->refresh_token();

				// Mari added
				if(isset($get_access_token['error'])){
					if($get_access_token['error'] == 'access_denied'){
						$data['result'] = "failure";
						$data['failure'] = 1;
						$data['reason'] = "Access Denied to get the refresh token";
						return $data;
					}
				}

				$exist_config = get_option("wp_{$this->activated_plugin}_settings");
				$config['access_token']=$get_access_token['access_token'];
				$config['api_domain']=$get_access_token['api_domain'];
				$config['key']=$exist_config['key'];
				$config['secret']=$exist_config['secret'];
				$config['callback']=$exist_config['callback'];
				$config['refresh_token']=$exist_config['refresh_token'];
				$config['domain']=$exist_config['domain'];
				update_option("wp_{$this->activated_plugin}_settings",$config);
				$this->checkEmailPresent($module , $email);
			}

		}

		if(isset( $records['result'][$module]['row']['@attributes'] ))
		{
			$result_lastnames[] = "Last Name";
			$result_emails[] = $email; 
			$result_ids[] = $records['result'][$module]['row']['FL'];
			$email_present = "yes";
		}
		else
		{
			if(isset($records['data'])){
				foreach( $records['data'] as $key => $record )
				{
					$result_lastnames[] = "Last Name";
					$result_emails[] = $email; 
					$result_ids[] = $record['id'];
					$email_present = "yes";
				}
			}
		}
		$this->result_emails = $result_emails;
		$this->result_ids = $result_ids;
		if($email_present == 'yes')
			return true;
		else
			return false;
	}

	public function duplicateCheckEmailField()
	{
		return "Email";
	}

	public function checkProductPresent( $module , $product )
	{
		$result_ids = $result_products = array();
		$client = $this->login();
		$product_present = "no";
		$extraparams = "&criteria=(Product Name:$product)"; // New Method for search record
		$records = $client->getRecords( $module , "searchRecords" , $this->authtoken , "Product Name" , "" , $extraparams ); // // Replaced getSearchRecords by searchRecords
		if(isset( $records['result'][$module]['row']['@attributes'] ))
		{
			$result_products[] = $product; 
			$result_ids[] = $records['result'][$module]['row']['FL'];
			$product_present = "yes";
		}
		else
		{
			if(is_array($records['result'][$module]['row']))
			{
				foreach( $records['result'][$module]['row'] as $key => $record )
				{
					$result_products[] = $product; 
					$result_ids[] = $record['FL'];
					$product_present = "yes";
				}
			}
		}
		$this->result_products = $result_products;
		$this->result_ids = $result_ids;
		if($product_present == 'yes')
			return true;
		else
			return false;
	}
}
