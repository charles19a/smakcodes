<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly


class PROFunctions{
	public $username;
	public $accesskey;
	public $url;
	public $result_emails;
	public $result_ids;
	public function __construct()
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		if(isset($_REQUEST['crmtype']))
		{
			$crmtype = sanitize_text_field( $_REQUEST['crmtype'] );
			$SettingsConfig = get_option("wp_{$crmtype}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$user_name = isset($SettingsConfig['username']) ? $SettingsConfig['username'] : '';
		$pass = isset($SettingsConfig['password']) ? $SettingsConfig['password'] : '';
		$settingurl= isset($SettingsConfig['url']) ? $SettingsConfig['url'] : '';
		$this->username = $user_name;
		$this->accesskey = $pass;
		$this->url = $settingurl;

	}

	public function login($url,$username,$password)
	{
		$parse_url = parse_url($url, PHP_URL_HOST);
		$exp_url = explode(".", $parse_url);
		$domain = end($exp_url); 

		$client = new nusoapclient($url.'/soap.php?wsdl',true);
		if($domain == 'eu'){
			$user_auth = array(
				'user_auth' => array(
					'user_name' => $username,
					'password' => $password,
					'version' => '0.1'
				),
				'application_name' => 'wp-sugar-pro'
			);
		}
		else{
			$user_auth = array(
				'user_auth' => array(
					'user_name' => $username,
					'password' => md5($password),
					'version' => '0.1'
				),
				'application_name' => 'wp-sugar-pro'
			);
		}

		$login = $client->call('login',$user_auth);
		$session_id = $login['id'];
		$client_array = array( 'login' => $login , 'session_id' => $session_id , "clientObj" => $client );
		return $client_array;
	}

	public function testlogin( $url , $username , $password )
	{		
		$instance_url = $url . "/rest/v11";
		$resultant=[];
		//Login - POST /oauth2/token
		$auth_url = $instance_url . "/oauth2/token";

		$oauth2_token_arguments = array(
			"grant_type" => "password",
			//client id - default is sugar. 
			//It is recommended to create your own in Admin > OAuth Keys
			"client_id" => "sugar", 
			"client_secret" => "",
			"username" => $username,
			"password" => $password,
			//platform type - default is base.
			//It is recommend to change the platform to a custom name such as "custom_api" to avoid authentication conflicts.
			"platform" => "custom_api" 
		);

		$auth_request = curl_init($auth_url);
		curl_setopt($auth_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($auth_request, CURLOPT_HEADER, false);
		curl_setopt($auth_request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($auth_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($auth_request, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($auth_request, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json"
		));

		//convert arguments to json
		$json_arguments = json_encode($oauth2_token_arguments);
		curl_setopt($auth_request, CURLOPT_POSTFIELDS, $json_arguments);

		//execute request
		$oauth2_token_response = curl_exec($auth_request);

		//decode oauth2 response to get token
		$oauth2_token_response_obj = json_decode($oauth2_token_response);

		$oauth_token = $oauth2_token_response_obj->access_token;

		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;

		$resultant['url'] = $url;
		$resultant['username'] = $username;
		$resultant['password'] = $password;
		$resultant['oauth_token'] = $oauth_token;

		update_option("wp_{$activateplugin}_settings", $resultant);

		return $oauth_token;
	}

	public function getCrmFields( $module )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		if(isset($_REQUEST['crmtype']))
		{
			$crmtype = sanitize_text_field( $_REQUEST['crmtype'] );
			$SettingsConfig = get_option("wp_{$crmtype}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$password = $SettingsConfig['password'];
		$url = $SettingsConfig['url'];
		$oauth_token = $this->testlogin($url, $username, $password);

		$fetch_url = $url . "/rest/v11/metadata?type_filter=modules&module_filter=". $module;

		$curl_request = curl_init($fetch_url);
		curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($curl_request, CURLOPT_HEADER, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"oauth-token: {$oauth_token}"
		));

		//execute request
		$curl_response = curl_exec($curl_request);
		//decode json
		$recordInfo = json_decode($curl_response);

		//display the created record

		curl_close($curl_request);
		$config_fields = array();
		if(isset($recordInfo)){
			$AcceptedFields = Array( 'text' => 'text' , 'bool' => 'boolean', 'enum' => 'picklist' , 'varchar' => 'string' , 'url' => 'url' , 'phone' => 'phone' , 'multienum' => 'multipicklist' , 'radioenum' => 'radioenum', 'currency' => 'currency' ,'date' => 'date' , 'datetime' => 'date' , 'int' => 'text' , 'decimal' => 'text' , 'currency_id' => 'text' );
			$j = 0;
			$get_module_fields = $recordInfo->modules->$module->fields;
			$module_fields = (array)$get_module_fields;

			foreach($module_fields as $module_valuess){
				$module_values = (array)$module_valuess;

				if(($module_values['type'] == 'enum') || ($module_values['type'] == 'multienum') || ($module_values['type'] == 'radioenum')){
					$optionindex = 0;
					$module_values['type'] = Array ( 'name' => $AcceptedFields[$module_values['type']] , 'picklistValues' => $module_values['options'] );
				}
				else
				{
					$module_values['type'] = Array( 'name' => $AcceptedFields[$module_values['type']]);
				}
				$config_fields['fields'][$j] = $module_values;
				$config_fields['fields'][$j]['order'] = $j;
				$config_fields['fields'][$j]['publish'] = 1;
				$config_fields['fields'][$j]['display_label'] = trim($module_values['name'], ':');

				if(isset($module_values['required']) && $module_values['required'] == 1)
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

		$config_fields['check_duplicate'] = 0;
		$config_fields['isWidget'] = 0;

		$users_list = $this->getUsersList();
		if(isset($users_list['id'][0])) {
			$config_fields['assignedto'] = $users_list['id'][0];
		} else {
			$config_fields['assignedto'] = '';	
		}			
		$config_fields['module'] = $module;
		return $config_fields;
	}

	public function getUsersList()
	{
		$user_details = array();
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		if(isset($_REQUEST['crmtype']))
		{
			$crmtype = sanitize_text_field( $_REQUEST['crmtype'] );
			$SettingsConfig = get_option("wp_{$crmtype}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$password = $SettingsConfig['password'];
		$url = $SettingsConfig['url'];
		$oauth_token = $this->testlogin($url, $username, $password);

		$fetch_url = $url . "/rest/v11/Users";
		$curl_request = curl_init($fetch_url);
		curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($curl_request, CURLOPT_HEADER, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"oauth-token: {$oauth_token}"
		));

		//execute request
		$curl_response = curl_exec($curl_request);
		//decode json
		$recordInfo = json_decode($curl_response);

		$userindex = 0;
		if(!empty($recordInfo))
			foreach($recordInfo->records as $record)
			{
				$user_details['user_name'][$userindex] = $record->user_name;
				$user_details['id'][$userindex] = $record->id;
				$user_details['first_name'][$userindex] = $record->first_name;
				$user_details['last_name'][$userindex] = $record->last_name;
				$userindex++;
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
				$content_option.="<option id='{$users_list['id'][$i]}' value='{$users_list['id'][$i]}'";

				if($users_list['id'][$i] == $config_fields->assigned_to)
				{
					$content_option.=" selected";

				}
				$content_option.=">{$users_list['first_name'][$i]} {$users_list['last_name'][$i]}</option>";
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
		$count=count($users_list['user_name']);
		for($i = 0; $i < $count ; $i++)
		{
			$user_list_array[$users_list['id'][$i]] = $users_list['first_name'][$i] ." ". $users_list['last_name'][$i];
		}

		return $user_list_array;
	}

	public function mapUserCaptureFields( $user_firstname , $user_lastname , $user_email )
	{
		$post = array();
		$post['first_name'] = $user_firstname;
		$post['last_name'] = $user_lastname;
		$post[$this->duplicateCheckEmailField()] = $user_email;
		return $post;
	}

	public function assignedToFieldId()
	{
		return "assigned_user_id";
	}

	public function createRecordOnUserCapture( $module , $module_fields )
	{
		return $this->createRecord( $module , $module_fields );

	}

	public function createRecord( $module , $module_fields )
	{
		$data=[];
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		if(isset($_REQUEST['crmtype']))
		{
			$crmtype = sanitize_text_field( $_REQUEST['crmtype'] );
			$SettingsConfig = get_option("wp_{$crmtype}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$password = $SettingsConfig['password'];
		$instance_url = $SettingsConfig['url'];

		$oauth_token = $this->testlogin($instance_url, $username, $password);

		//Create Records - POST /<module>
		$url = $instance_url . "/rest/v11/" . $module;
		//Set up the Record details
		$record = array(
			'name' => 'Test Record',
			'email' => array(
				array(
					'email_address' => 'test@sugar.com',
					'primary_address' => true
				)
			),
		);

		$curl_request = curl_init($url);
		curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($curl_request, CURLOPT_HEADER, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"oauth-token: {$oauth_token}"
		));

		//convert arguments to json
		$json_arguments = json_encode($module_fields);

		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $json_arguments);
		//execute request
		$curl_response = curl_exec($curl_request);

		//decode json
		$createdRecord = json_decode($curl_response);

		//display the created record

		curl_close($curl_request);
		if(isset($createdRecord->id))
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
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$data=[];
		if(isset($_REQUEST['crmtype']))
		{
			$crmtype = sanitize_text_field( $_REQUEST['crmtype'] );
			$SettingsConfig = get_option("wp_{$crmtype}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$password = $SettingsConfig['password'];
		$url = $SettingsConfig['url'];
		$client_array = $this->login($url,$username,$password);
		$client = $client_array['clientObj'];
		$fieldvalues = array();
		foreach($module_fields as $key => $value)
		{
			$fieldvalues[] = array('name' => $key, 'value' => $value);
		}
		$set_entry_parameters = array(
			//session id
			"session" => $client_array['session_id'],
			//The name of the module from which to retrieve records.
			"module_name" =>  $module,
			//Record attributes
			"name_value_list" => $fieldvalues,
		);

		if( $module == 'Leads' || $module == 'Contacts' )
		{
			$response = $client->call('set_entry',  $set_entry_parameters , $this->url );
		}

		global $wpdb;
		if(isset( $response['id'] ))
		{
			$data['result'] = "success";
			$data['failure'] = 0;

			if( $module == "Leads" )
			{
				$crm_id = $response['id'];
				$my_leadid = $crm_id;
				$crm_name = 'wpsugarpro';
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
				$crm_id = $response['id'];
				$crm_name = 'wpsugarpro';
				$my_contactid = $crm_id;
				if( is_user_logged_in() )
				{
					$user_id = get_current_user_id();
					$is_user = 1;
				}else
				{
					$user_id = '';
					$is_user = 0;
				}
				$contact_no = $crm_id;
				$wpdb->insert( 'wp_smack_ecom_info' , array( 'crmid' => $crm_id , 'crm_name' => $crm_name , 'wp_user_id' => $user_id , 'is_user' => $is_user , 'contact_no' => $my_contactid , 'order_id' => $order_id ) );

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


	public function convertLead( $module , $lead_id , $order_id , $lead_no , $sales_order)
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		if(isset($_REQUEST['crmtype']))
		{
			$crmtype = sanitize_text_field( $_REQUEST['crmtype'] );
			$SettingsConfig = get_option("wp_{$crmtype}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$password = $SettingsConfig['password'];
		$url = $SettingsConfig['url'];
		$client_array = $this->login($url,$username,$password);
		$client = $client_array['clientObj'];
		$fieldvalues = array();
		$fetch_lead_details = array(
			"session" => $client_array['session_id'],
			"module_name" =>  'Leads',
			//Record attributes
			"ids" => array( $lead_no ),
		);

		$get_lead_details = $client->call('get_entries', $fetch_lead_details);
		$lead_fields = $get_lead_details['entry_list'][0]['name_value_list'];


		$unset_array = array('assigned_user_name' , 'modified_by_name' , 'created_by_name' , 'id' , 'date_entered' , 'date_modified' );
		$i = 0;
		foreach( $lead_fields as $field_key => $field_value )
		{
			if( in_array( $field_value['name'] , $unset_array ))
			{

				unset( $lead_fields[$field_key]);
			}
		}

		$create_contact_parameters = array(
			//session id
			"session" => $client_array['session_id'],
			//The name of the module from which to retrieve records.
			"module_name" =>  'Contacts',
			//Record attributes
			"name_value_list" => $lead_fields,
		);

		$create_contact = $client->call( 'set_entry' , $create_contact_parameters , $this->url );
		$contact_id = $create_contact['id'];
		$fieldvalues = 	array(
			array('name' => 'id' , 'value' => $lead_no ),	
			array( 'name' => 'deleted' , 'value' => '1' )
		);
		$delete_parameters = array(
			//session id
			"session" => $client_array['session_id'],
			//The name of the module from which to retrieve records.
			"module_name" =>  $module,
			//Record attributes
			"name_value_list" => $fieldvalues,
		);

		$client->call('set_entry',  $delete_parameters , $this->url );
		global $wpdb;
		$wpdb->update( 'wp_smack_ecom_info' , array('contact_no' => $contact_id) , array( 'order_id' => $order_id ) );	
	}

	public function updateRecord( $module , $module_fields , $ids_present )
	{
		$data=[];
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		if(isset($_REQUEST['crmtype']))
		{
			$crmtype = sanitize_text_field( $_REQUEST['crmtype'] );
			$SettingsConfig = get_option("wp_{$crmtype}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$password = $SettingsConfig['password'];
		$instance_url = $SettingsConfig['url'];
		$oauth_token = $this->testlogin($instance_url, $username, $password);
		$url = $instance_url . "/rest/v11/" . $module ."/" . $ids_present;

		$fieldvalues = array();
		$fieldvalues[] = array( 'name' => 'id', 'value' => $ids_present );
		foreach($module_fields as $key => $value)
		{
			$fieldvalues[] = array('name' => $key, 'value' => $value);
			if($key == 'email1')
			{
				$isemail_field_present = "yes";
			}
		}

		$curl_request = curl_init($url);
		curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($curl_request, CURLOPT_HEADER, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"oauth-token: {$oauth_token}"
		));

		//convert arguments to json
		$json_arguments = json_encode($record);
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $json_arguments);
		//execute request
		$curl_response = curl_exec($curl_request);
		//decode json
		$updatedRecord = json_decode($curl_response);

		//display the created record
		curl_close($curl_request);

		if(isset($updatedRecord['id']))
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

	public function checkEmailPresent( $module , $email )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$result_emails = array();
		$result_ids = array();
		$email_present = "no";
		// $module_table_name = strtolower($module);
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		if(isset($_REQUEST['crmtype']))
		{
			$crmtype = sanitize_text_field( $_REQUEST['crmtype'] );
			$SettingsConfig = get_option("wp_{$crmtype}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$password = $SettingsConfig['password'];
		$url = $SettingsConfig['url'];

		$oauth_token = $this->testlogin($url, $username, $password);

		$fetch_url = $instance_url . "/$module/duplicateCheck";
		//Set up the Record details
		$record = array(
			'email1' => $email,
		);

		$curl_request = curl_init($fetch_url);
		curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($curl_request, CURLOPT_HEADER, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"oauth-token: {$oauth_token}"
		));

		//convert arguments to json
		$json_arguments = json_encode($record);
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $json_arguments);
		//execute request
		$curl_response = curl_exec($curl_request);
		//decode json
		$createdRecord = json_decode($curl_response);
		curl_close($curl_request);

		$entry_list = $result['entry_list'];
		foreach($entry_list as $entry)
		{
			foreach($entry['name_value_list'] as $field)
			{
				if($field['name'] == 'last_name')
				{
					$result_lastnames[] = $field['value'];
				}
				if($field['name'] == 'email1')
				{
					if($email == $field['value'])
					{
						$email_present = 'yes';
					}
					$result_ids[] = $entry['id'];
					$result_emails[] = $field['value'];
					$result_emails1[] = $field['value'];
				}
				if($field['name'] == 'email2')
				{
					if($email == $field['value'])
					{
						$email_present = 'yes';
					}
					$result_ids[] = $entry['id'];
					$result_emails[] = $field['value'];
					$result_emails2[] = $field['value'];
				}
			}
		}

		$this->result_emails = $result_emails;
		$this->result_ids = $result_ids;
		if($email_present == 'yes')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function duplicateCheckEmailField()
	{
		return "email1";
	}

}
