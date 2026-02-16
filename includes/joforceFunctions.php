<?php
/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class PROFunctions
{
	/**
	 * Joforce Username
	 */
	public $username;

	/**
	 * Joforce Password
	 */
	public $password;

	/**
	 * Joforce app url
	 */
	public $url;

	/**
	 * Joforce API end point
	 */
	public $end_point = 'api/v1';

	/**
	 * Joforce auth token
	 */
	public $token;

	public $result_emails;

	public $result_ids;

	public $result_products;

	/**
	 * Joforce Constructor
	 */
	public function __construct()
	{
		global $lb_admin;
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))	{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		} 
		else	{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$user_name = isset($SettingsConfig['username']) ? $SettingsConfig['username'] : '';
		$pass = isset($SettingsConfig['password']) ? $SettingsConfig['password'] : '';
		$settingurl= isset($SettingsConfig['app_url']) ? $SettingsConfig['app_url'] : '';
		$settingtoken = isset($SettingsConfig['token']) ? $SettingsConfig['token'] : '';
		$this->username = $user_name;
		$this->password = $pass;
		$this->url = $settingurl;
		$this->token = $settingtoken;
		$lb_admin->setConfigurationDetails($SettingsConfig);
	}

	/**
	 * Login to Joforce
	 * 
	 * @return array $response
	 */
	public function login()
	{
		$params = array('username' => $this->username, 'password' => $this->password);
		$url = $this->url . '/' . $this->end_point . '/authorize';
		$response = $this->call($url, $params, 'POST');
		return $response;
	}

	/**
	 * Return Joforce module fields
	 * 
	 * @return array $config_fields
	 */
	public function getCrmFields($module)
	{
		$url = $this->url . '/' . $this->end_point . '/' . $module . '/' . 'fields';
		$recordInfo = $this->call($url, array(), 'GET');
		// If token expired, try to login again
		if(isset($recordInfo['success']) && $recordInfo['success'] != true && $recordInfo['code'] == 401)	{
			$this->login();
			$recordInfo = $this->call($url, array(), 'GET');
		}

		$config_fields = array();
		if($recordInfo)
		{
			$j = 0;
			$count=count($recordInfo['fields']);
			for($i = 0; $i<$count; $i++)
			{
				// If type is not set for field, skip that field.
				if(!isset($recordInfo['fields'][$i]['type']['name']))	{
					continue;
				}

				if($recordInfo['fields'][$i]['type']['name'] == 'reference')	{
					//
				}
				elseif($recordInfo['fields'][$i]['name'] == 'modifiedby' || $recordInfo['fields'][$i]['name'] == 'assigned_user_id' )	{
					//
				}
				else{
					$config_fields['fields'][$j] = $recordInfo['fields'][$i];
					$config_fields['fields'][$j]['order'] = $j;
					$config_fields['fields'][$j]['publish'] = 1;
					$config_fields['fields'][$j]['display_label'] = $recordInfo['fields'][$i]['label'];
					if($recordInfo['fields'][$i]['mandatory'] == 1)
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
			$config_fields['update_record'] = 0;
			$users_list = $this->getUsersList();
			$config_fields['assignedto'] = $users_list['id'][0];
			$config_fields['module'] = $module;
		}
		return $config_fields;
	}

	/**
	 * Return users from Joforce
	 * 
	 * @return array $user_details
	 */
	public function getUsersList()
	{
		$page = 1;
		$user_details=[];
		$users_list = array();
		do {
			$url = $this->url . '/' . $this->end_point . '/Users/list/' . $page;
			$users_list = $this->call($url, array(), 'GET');
			// If token expired, try to login again
			if (isset($recordInfo['success']) && $recordInfo['success'] != true && $recordInfo['code'] == 401) {
				$this->login();
				$recordInfo = $this->call($url, array(), 'GET');
			}
			if (isset($users_list)) {
				foreach ($users_list['records'] as $record) {
					$user_details['user_name'][] = $record['user_name'];
					$user_details['id'][] = $record['id'];
					$user_details['first_name'][] = $record['first_name'];
					$user_details['last_name'][] = $record['last_name'];
				}
			}
			$page = $page + 1;
		} while ($users_list['moreRecords'] === true);

		return $user_details;
	}

	/**
	 * Generate dropdown field using Users list
	 * 
	 * @return string $html
	 */
	public function getUsersListHtml($shortcode = "")
	{
		$HelperObj = new WPCapture_includes_helper_PRO();
		// $module = $HelperObj->Module;
		// $moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		// $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$formObj = new CaptureData();
		if (isset($shortcode) && ($shortcode != "")) {
			$config_fields = $formObj->getFormSettings($shortcode);  // Get form settings
		}

		$users_list = get_option('crm_users');
		$users_list = $users_list[$activatedplugin];
		$html = "";
		$html = '<select name="assignedto" id="assignedto" style="min-width:69px;border-radius: 7px !important;">';
		$content_option = "";
		if (isset($users_list['user_name']))
		$count= count($users_list['user_name']);
			for ($i = 0; $i < $count ; $i++) {
				$content_option .= "<option id='{$users_list['id'][$i]}' value='{$users_list['id'][$i]}'";
				if ($users_list['id'][$i] == $config_fields->assigned_to) {
					$content_option .= " selected";
				}
				$content_option .= ">{$users_list['first_name'][$i]} {$users_list['last_name'][$i]}</option>";
			}

		$content_option .= "<option id='owner_rr' value='Round Robin'";
		if ($config_fields->assigned_to == 'Round Robin') {
			$content_option .= "selected";
		}
		$content_option .= "> Round Robin </option>";
		$html .= $content_option;
		$html .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		return $html;
	}

	/**
	 * Return users list of Joforce
	 * 
	 * @return array $user_list_array
	 */
	public function getAssignedToList()
	{
		$user_list_array=[];
		$users_list = $this->getUsersList();
		$count = count($users_list['user_name']);
		for($i = 0; $i < $count ; $i++)
		{
			$user_list_array[$users_list['id'][$i]] = $users_list['first_name'][$i] ." ". $users_list['last_name'][$i];
		}
		return $user_list_array;
	}

	/**
	 * Assigned to field name of Joforce
	 */
	public function assignedToFieldId()
	{
		return "assigned_user_id";
	}

	/**
	 * Map user capture fields
	 * 
	 * @param string $user_firstname
	 * @param string $user_lastname
	 * @param string $user_email
	 * @return array $post
	 */
	public function mapUserCaptureFields( $user_firstname , $user_lastname , $user_email )
	{
		$post = array();
		$post['firstname'] = $user_firstname;
		$post['lastname'] = $user_lastname;
		$post[$this->duplicateCheckEmailField()] = $user_email;
		return $post;
	}

	/**
	 * Create record when user captured
	 */
	public function createRecordOnUserCapture( $module , $module_fields )
	{
		return $this->createRecord( $module , $module_fields );
	}

	/**
	 * Create a new record to Joforce
	 * 
	 * @param string $module
	 * @param array $module_fields
	 * @return array $data
	 */
	public function createRecord($module, $module_fields)
	{
		$data=[];
		$url = $this->url . '/' . $this->end_point . '/' . $module;
		$response = $this->call($url, $module_fields, 'POST');
		// If token expired, try to login again
		if (isset($response['success']) && $response['success'] != true && $response['code'] == 401) {
			$this->login();
			$response = $this->call($url, $module_fields, 'POST');
		}

		if(isset($response['code']) && $response['code'] != 200)	{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed adding entry";
		} 
		else {
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		return $data;
	}

	/**
	 * Update Joforce record 
	 * 
	 * @param string $module
	 * @param array $module_fields
	 * @param id $ids_present
	 * @return array $data
	 */
	public function updateRecord( $module , $module_fields , $ids_present )
	{
		$data=[];
		$url = $this->url . '/' . $this->end_point . '/' . $module . '/' . $ids_present;
		$response = $this->call($url, $module_fields, 'PUT');
		// If token expired, try to login again
		if (isset($response['success']) && $response['success'] != true && $response['code'] == 401) {
			$this->login();
			$response = $this->call($url, $module_fields, 'PUT');
		}

		if(isset($response['code']) && $response['code'] != 200)	{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed updating entry";
		} 
		else {
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		return $data;
	}

	public function createEcomRecord($module, $module_fields , $order_id )
	{
		$data=[];
		$url = $this->url . '/' . $this->end_point . '/' . $module;
		$response = $this->call($url, $module_fields, 'POST');
		// If token expired, try to login again
		if (isset($response['success']) && $response['success'] != true && $response['code'] == 401) {
			$this->login();
			$response = $this->call($url, $module_fields, 'POST');
		}

		global $wpdb;
		if(isset($response['code']) && $response['code'] != 200)	{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed adding entry";
		} 
		else {
			$data['result'] = "success";
			$data['failure'] = 0;

			$crm_id = $record['id'];	
			$crm_name = 'joforce';
			if($module == "Leads") {
				$my_leadid = $crm_id;
				if(is_user_logged_in()) {
					$user_id = get_current_user_id();
					$is_user = 1;
				}
				else {
					$user_id = 'guest';
					$is_user = 0;
				}
				$lead_no = $crm_id;
				$wpdb->insert( 'wp_smack_ecom_info' , array( 'crmid' => $crm_id , 'crm_name' => $crm_name , 'wp_user_id' => $user_id , 'is_user' => $is_user , 'lead_no' => $my_leadid , 'order_id' => $order_id ) );
			}

			if($module == 'Contacts') {
				$my_contactid = $crm_id;
				if(is_user_logged_in()) {
					$user_id = get_current_user_id();
					$is_user = 1;
				}
				else {
					$user_id = '';
					$is_user = 0;
				}
				$contact_no = $crm_id;
				$wpdb->insert( 'wp_smack_ecom_info' , array( 'crmid' => $crm_id , 'crm_name' => $crm_name , 'wp_user_id' => $user_id , 'is_user' => $is_user , 'contact_no' => $my_contactid , 'order_id' => $order_id ) );
			}

			if($module == 'Products') {
				$get_product = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=$order_id" ) );
				$prod_id = $get_product[0]->product_id;
				if(!empty($prod_id)) {
					$crm_id = $prod_id.",".$crm_id;
				}
				$wpdb->update( 'wp_smack_ecom_info' , array( 'product_id' => $crm_id ) , array( 'order_id' => $order_id ));		
			}
		}
		return $data;
	}

	// Update Ecom Records 
	public function updateEcomRecord( $module , $module_fields , $ids_present, $order_id )
	{
		global $wpdb;
		$data=[];
		$url = $this->url . '/' . $this->end_point . '/' . $module . '/' . $ids_present;
		$response = $this->call($url, $module_fields, 'PUT');
		// If token expired, try to login again
		if (isset($response['success']) && $response['success'] != true && $response['code'] == 401) {
			$this->login();
			$response = $this->call($url, $module_fields, 'PUT');
		}

		if($module == 'Products') {
			$crm_id = $response['id'];
			$crm_name = 'joforce';
			$get_product = $wpdb->get_results($wpdb->prepare("select product_id from wp_smack_ecom_info where order_id=$order_id"));
			$prod_id = $get_product[0]->product_id;
			if (!empty($prod_id)) {
				$crm_id = $prod_id . "," . $crm_id;
			}
			$wpdb->update('wp_smack_ecom_info', array('product_id' => $crm_id), array('order_id' => $order_id));
		}

		if(isset($response['code']) && $response['code'] != 200)	{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed updating entry";
		} 
		else {
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		return $data;
	}

	// Convert Lead
	// TODO Need to implement this functionality
	public  function convertLead( $module , $crm_id , $order_id , $lead_no, $sales_order)
	{
		global $wpdb;
		$data=[];
		$client = $this->login();
		$selected_module = $client->doRetrieve( "$crm_id" );
		$update_client = $selected_module;
		$field_replace_array = array('pobox' => 'mailingpobox' , 'city' => 'mailingcity' , 'state' => 'mailingstate' ,'country' => 'mailingcountry' , 'street' => 'mailingstreet' , 'designation' => 'title' );

		if( !empty( $update_client ))
		{
			foreach($update_client as $key => $value){
				foreach( $field_replace_array as $rep_key => $rep_val )
				{
					if( $rep_key == $key )
					{
						$selected_module[$rep_val] = $value;
					}
					else
					{
						$selected_module[$key] = $value;
					}	
				}
			}
		}
		$contact_record = $client->docreate('Contacts' , $selected_module);
		$client = $this->login();
		$client->doDelete("$lead_no");

		$cont_no = $contact_record['id'] ;
		//Get Orgonization ids
		$get_orgonizations = "SELECT id from Accounts";
		$org = $client->doQuery( $get_orgonizations );
		$sales_order['account_id'] = $org[0]['id'];
		$sales_order['contact_id'] = $cont_no;
		$record_sales = $client->doCreate('SalesOrder' , $sales_order);
		$sales_orderid = $record_sales['id'];
		$wpdb->update( 'wp_smack_ecom_info' , array('contact_no' => $cont_no) , array( 'order_id' => $order_id ) );
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
		global $wpdb;
		$data=[];
		$client = $this->login();
		$get_orgonizations = "SELECT id from Accounts";
		$org = $client->doQuery( $get_orgonizations );
		$sales_order['account_id'] = $org[0]['id'];
		$record_sales = $client->doCreate('SalesOrder' , $sales_order);
		$sales_orderid = $record_sales['id'];
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

	// ECOM Check product already present
	public function checkProductPresent( $module , $product )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$result_products = array();
		$result_ids = array();
		$client = $this->login();
		$product_present = "no";
		$count_query = "SELECT count(*) FROM $module";
		$records = $client->doQuery($count_query);
		$total = $records[0]['count'];

		if(  $total != 0 )
		{
			for($i=0;$i<=$total;$i=$i+100)
			{
				$query = "SELECT productname FROM $module LIMIT $i , 100";
				$records = $client->doQuery($query);
				if($records) {
					$columns = $client->getResultColumns($records);
					if(is_array($records))
					{
						foreach($records as $record) {
							$result_products[] = $record['productname'];
							$result_ids[] = $record['id'];

							if($product == $record['productname'])
							{
								$code = $record['id'];
								$product_present = "yes";
							}
						}
					}
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

	/**
	 * Check email present in the module
	 * 
	 * @param string $module
	 * @param string $email
	 * @return boolean
	 */
	public function checkEmailPresent( $module , $email )
	{
		$result_emails = array();
		$result_ids = array();
		$url = $this->url . '/' . $this->end_point . '/' . $module . '/search/email/' . $email;
		$response = $this->call($url, array(), 'GET');
		// If token expired, try to login again
		if (isset($response['success']) && $response['success'] != true && $response['code'] == 401) {
			$this->login();
			$response = $this->call($url, array(), 'GET');
		}

		if(isset($response['records']) && count($response['records']) > 0)	{
			foreach($response['records'] as $record)	{
				$result_emails[] = $record['email'];
				$result_ids[] = $record['id'];
			}
			$this->result_emails = $result_emails;
			$this->result_ids = $result_ids;
			return true;
		}
		return false;
	}

	/**
	 * Joforce email field
	 */
	public function duplicateCheckEmailField()
	{
		return 'email';
	}

	/**
	 * Call to CRM
	 * 
	 * @param string $url
	 * @param array $params
	 * @param string $method
	 */
	public function call($url, $params, $method)
	{
		$curl = curl_init($url);

		if($method == 'PUT')	{
			$post_params = null;
			foreach($params as $key => $value)	{ 
				$post_params .= $key.'='.$value.'&'; 
			}
			rtrim($post_params, '&');
		}
		else	{
			$post_params = $params;
		}

		$options_array = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $post_params,
		);

		if(!empty($this->token))	{
			$options_array[CURLOPT_HTTPHEADER] = array(
				"Authorization: Bearer " . $this->token,
				"Cache-Control: no-cache",	
			);
		}

		curl_setopt_array($curl, $options_array);

		$response = curl_exec($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);	
		$err = curl_error($curl);

		curl_close($curl);

		if ($err || $http_code != 200) {
			$response = array();
			$response['success'] = false;
			$response['message'] = $err;
			$response['code'] = $http_code;
			return $response;
		} 
		return json_decode($response, true);
	}
}
