<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

require_once(SM_LB_PRO_DIR.'lib/vtwsclib/Vtiger/WSClient.php');
class PROFunctions{
	public $username;
	public $accesskey;
	public $url;
	public $result_emails;
	public $result_ids;
	public $result_products;
	public function __construct()
	{
		global $lb_admin;
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$user_name = isset($SettingsConfig['username']) ? $SettingsConfig['username'] : '';
		$access_key = isset($SettingsConfig['accesskey']) ? $SettingsConfig['accesskey'] : '';
		$settingurl= isset($SettingsConfig['url']) ? $SettingsConfig['url'] : '';
		$this->username = $user_name;
		$this->accesskey = $access_key;
		$this->url = $settingurl;
		$lb_admin->setConfigurationDetails($SettingsConfig);
	}

	public function login($url,$accesskey,$username)
	{
		$client = new Vtiger_WSClient($url);
		$client->doLogin($username, $accesskey);
		return $client;
	}

	public function testLogin( $url , $username , $accesskey )
	{
		$client = new Vtiger_WSClient($url);
		$login = $client->doLogin($username, $accesskey);
		return $login;
	}

	public function getCrmFields( $module )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$configuser = isset($SettingsConfig['username']) ? $SettingsConfig['username'] : '';
		$configaccess = isset($SettingsConfig['accesskey']) ? $SettingsConfig['accesskey'] : '';
		$configurl = isset($SettingsConfig['url']) ? $SettingsConfig['url'] : '';
		$username = $configuser;
		$accesskey = $configaccess;
		$url = $configurl;
		$client = $this->login($url,$accesskey,$username);
		$recordInfo = $client->doDescribe($module);
		$config_fields = array();
		if($recordInfo)
		{
			$j=0;
			for($i=0;$i<count($recordInfo['fields']);$i++)
			{
				
				if($recordInfo['fields'][$i]['type']['name'] == 'reference'){
				}
				elseif($recordInfo['fields'][$i]['name'] == 'modifiedby' || $recordInfo['fields'][$i]['name'] == 'assigned_user_id' ){
				}
				else{
					$config_fields['fields'][$j] = $recordInfo['fields'][$i];
					$config_fields['fields'][$j]['order'] = $j;
					$config_fields['fields'][$j]['publish'] = 1;
					$config_fields['fields'][$j]['display_label'] = $recordInfo['fields'][$i]['label'];
					if($recordInfo['fields'][$i]['mandatory']==1)
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

	public function getUsersList()
	{
		$query = "select user_name, id, first_name, last_name  from Users";
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$accesskey = $SettingsConfig['accesskey'];
		$url = $SettingsConfig['url'];
		$client = $this->login($url,$accesskey,$username);
		$records = $client->doQuery($query);

		if($records) {

			foreach($records as $record) {
				$user_details['user_name'][] = $record['user_name'];
				$user_details['id'][] = $record['id'];
				$user_details['first_name'][] = $record['first_name'];
				$user_details['last_name'][] = $record['last_name'];
			}
		}
		else{
			$user_details = '';
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
		$html = '<select  name="assignedto" class="form-control" id="assignedto">';
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

	public function assignedToFieldId()
	{
		return "assigned_user_id";
	}

	public function mapUserCaptureFields( $user_firstname , $user_lastname , $user_email )
	{
		$post = array();
		$post['firstname'] = $user_firstname;
		$post['lastname'] = $user_lastname;
		$post[$this->duplicateCheckEmailField()] = $user_email;
		return $post;
	}

	public function createRecordOnUserCapture( $module , $module_fields )
	{
		return $this->createRecord( $module , $module_fields );
	}

	public function createRecord($module, $module_fields )
	{
		$data=[];
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$accesskey = $SettingsConfig['accesskey'];
		$url = $SettingsConfig['url'];

		$client = $this->login($url,$accesskey,$username);
		$client->setDebug(true);

		$record = $client->docreate( $module , $module_fields );

		if($record)
		{
			$data['result'] = "success";
			$data['crm_id'] = $record['id'];
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed adding entry";
    $this->sendFailureNotificationEmail($module, $module_fields, $data['reason']);
		}
		return $data;
	}

	
public function sendFailureNotificationEmail($module, $module_fields, $reason = '') {
    $CaptchaConfig = get_option("wp_captcha_settings", []);

    $emails = isset($CaptchaConfig['email']) ? explode(',', $CaptchaConfig['email']) : [];

    if (empty($emails)) {
        return;
    }

    $subject = "⚠️ CRM Record Creation Failed";
    $body  = "Module: $module\n";
    $body .= "Status: FAILURE\n";
    $body .= "Reason: " . (!empty($reason) ? $reason : 'Unknown error') . "\n\n";
    $body .= "Data Sent:\n" . print_r($module_fields, true);

    foreach ($emails as $email) {
        $email = trim($email);
        if (!empty($email)) {
            $result = wp_mail($email, $subject, $body);
        }
    }
}






	public function createEcomRecord($module, $module_fields , $order_id )
	{
		$data=[];
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$accesskey = $SettingsConfig['accesskey'];
		$url = $SettingsConfig['url'];

		$client = $this->login($url,$accesskey,$username);
	//	$client->debug = true;

		if( $module == 'Leads' || $module == 'Contacts' )
		{
			$record = $client->doCreate( $module , $module_fields );
		}

		if( $module == 'Products' )
		{
			$record = $client->doCreate( $module , $module_fields );
		}
		$email=$module_fields['email'];
		$query = "SELECT lastname, email,id FROM $module where email like '$email'";
		$records_test = $client->doQuery($query);

		global $wpdb;
		if($records_test)
		{
			$data['result'] = "success";
			$data['failure'] = 0;

			if( $module == "Leads" )
			{
				$crm_id = $records_test[0]['id'];
				$my_leadid = $crm_id;
				$crm_name = 'wptigerpro';
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
				$crm_id = $record['id'];
				$crm_name = 'wptigerpro';
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
			if( $module == 'Products' )
			{
				$crm_id = $record['id'];	
				$crm_name = 'wptigerpro';
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
			$data['reason'] = "failed adding entry";
		}
		return $data;
	}

	//Update Ecom Records

	public function updateEcomRecord( $module , $module_fields , $ids_present, $order_id )
	{
		global $wpdb;
		$data=[];
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$accesskey = $SettingsConfig['accesskey'];
		$url = $SettingsConfig['url'];

		$client = $this->login($url,$accesskey,$username);
		$selected_module = $client->doRetrieve( "$ids_present" );
		$update_client = $module_fields;
		foreach($update_client as $key => $value){
			$selected_module[$key] = $value;
		}
		$record = $client->doUpdate($selected_module);

		if( $module == 'Products' )
		{
			$crm_id = $record['id'];
			$crm_name = 'wptigerpro';
			$get_product = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=$order_id" ) );
			$prod_id = $get_product[0]->product_id;
			if( !empty( $prod_id ) )
			{
				$crm_id = $prod_id.",".$crm_id;
			}
			$wpdb->update( 'wp_smack_ecom_info' , array( 'product_id' => $crm_id ) , array( 'order_id' => $order_id ));
		}

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
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$data=[];
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$accesskey = $SettingsConfig['accesskey'];
		$url = $SettingsConfig['url'];

		$client = $this->login($url,$accesskey,$username);
		$selected_module = $client->doRetrieve( "$ids_present" );
		$update_client = $module_fields;
		foreach($update_client as $key => $value){
			$selected_module[$key] = $value;
		}

		$record = $client->doUpdate($selected_module);
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


	//Convert Lead

	public  function convertLead( $module , $crm_id , $order_id , $lead_no, $sales_order)
	{
		global $wpdb;
		$data=[];
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$accesskey = $SettingsConfig['accesskey'];
		$url = $SettingsConfig['url'];

		$client = $this->login($url,$accesskey,$username);
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
		$client = $this->login($url,$accesskey,$username);
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
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$accesskey = $SettingsConfig['accesskey'];
		$url = $SettingsConfig['url'];

		$client = $this->login($url,$accesskey,$username);
		$get_orgonizations = "SELECT id from Accounts";
		$org = $client->doQuery( $get_orgonizations );
		$sales_order['account_id'] = $org[0]['id'];
		$record_sales = $client->doCreate('SalesOrder' , $sales_order);
		$sales_orderid = $record_sales['id'];
		$cont_no = $contact_record['id'] ;
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

	//ECOM Check product already present
	public function checkProductPresent( $module , $product )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$result_products = array();
		$result_ids = array();
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$accesskey = $SettingsConfig['accesskey'];
		$url = $SettingsConfig['url'];

		$client = $this->login($url,$accesskey,$username);
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



	public function checkEmailPresent( $module , $email )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$result_emails = array();
		$result_ids = array();
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		if(isset($_REQUEST['crmtype']))
		{
			$SettingsConfig = get_option("wp_{$_REQUEST['crmtype']}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$username = $SettingsConfig['username'];
		$accesskey = $SettingsConfig['accesskey'];
		$url = $SettingsConfig['url'];

		$client = $this->login($url,$accesskey,$username);
		$email_present = "no";
		$query = "SELECT lastname, email FROM $module where email like '$email'";
		$records = $client->doQuery($query);
		if($records) {
			if(is_array($records))
			{
				foreach($records as $record) {
					$result_lastnames[] = $record['lastname'];
					$result_emails[] = $record['email'];
					$result_ids[] = $record['id'];

					if($email == $record['email'])
					{
						$code = $record['id'];
						$email_present = "yes";
					}
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

	function duplicateCheckEmailField()
	{
		return "email";
	}
}
