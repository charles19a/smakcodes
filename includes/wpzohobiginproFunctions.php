<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
include_once(SM_LB_PRO_DIR.'lib/SmackZohoBiginApi.php');
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
		$this->access_token=isset($zohoconfig['access_token']) ? $zohoconfig['access_token'] : '';
		$this->refresh_token=isset($zohoconfig['refresh_token']) ? $zohoconfig['refresh_token'] : '';
		$this->client_id=$zohoconfig['key'];
		$this->client_secret=$zohoconfig['secret'];
		$this->domain=$zohoconfig['domain'];
	}

	public function login()
	{
		$client = new SmackZohoBiginApi();
		return $client;
	}

	public function getCrmFields( $module )
	{
		$module = $this->moduleMap( $module );
		$data=[];
		$config=[];
		$client = new SmackZohoBiginApi();
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		$token = isset($SettingsConfig['access_token']) ? $SettingsConfig['access_token'] : ''; // Use access_token
		$this->authtoken = $token;
		$recordInfo = $client->APIMethod( $module , "getFields" , $this->authtoken );
		
        if(!empty($recordInfo['code'])){
			if($recordInfo['code']=='INVALID_TOKEN' || $recordInfo['code']=='AUTHENTICATION_FAILURE'){
				$get_access_token=$client->refresh_token();

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
				return $this->getCrmFields($module);
			}
		}

		$config_fields = array();
		$AcceptedFields = Array( 'textarea' => 'text' , 'text' => 'string' , 'email' => 'email' , 'boolean' => 'boolean', 'picklist' => 'picklist' , 'varchar' => 'string' , 'website' => 'url' , 'phone' => 'phone' , 'Multi Pick List' => 'multipicklist' , 'radioenum' => 'radioenum', 'currency' => 'currency' , 'dateTime' => 'date' ,  'integer' => 'string' , 'BigInt' => 'string' , 'double' => 'string');
		$j = 0;
		if(isset($recordInfo['fields'])){
			foreach($recordInfo['fields'] as $key => $fields )
			{
				if($module == 'Deals') {
					$allowed_fields = array('Deal Name', 'Company Name', 'Contact Name', 'Stage', 'Amount', 'Closing Date', 'Description');
					if(!in_array($fields['field_label'], $allowed_fields)) {
						continue;
					}
				}
					if($fields['api_name']=='Company'||$fields['api_name']=='Last_Name'||$fields['api_name']=='Deal_Name') // Bigin also requires Last_Name and Deal_Name
					{
						$fields['system_mandatory'] = true;
					}				
					if(isset($fields['system_mandatory']) && $fields['system_mandatory'] == true )
					{
						$config_fields['fields'][$j]['wp_mandatory'] = 1;
						$config_fields['fields'][$j]['mandatory'] = 2;
					}
					else
					{
						$config_fields['fields'][$j]['wp_mandatory'] = 0;
					}
					
                    if(($fields['data_type'] == 'picklist') || ($fields['data_type'] == 'multipicklist') || ($fields['data_type'] == 'radio')){
						$optionindex = 0;
						$picklistValues = array();
                        if(isset($fields['pick_list_values'])) {
                            foreach($fields['pick_list_values'] as $option)
                            {
                                $picklistValues[$optionindex]['display_value'] = $option['display_value'];
                                $picklistValues[$optionindex]['actual_value'] = $option['actual_value'];
                                $picklistValues[$optionindex]['label'] = $option['display_value'];
                                $picklistValues[$optionindex]['value'] = $option['actual_value'];
                                $optionindex++;
                            }
                        }
						$config_fields['fields'][$j]['type'] = Array ( 'name' => isset($AcceptedFields[$fields['data_type']]) ? $AcceptedFields[$fields['data_type']] : 'string' , 'picklistValues' => $picklistValues );
					}
					else
					{
						$attr = isset($AcceptedFields[$fields['data_type']]) ? $AcceptedFields[$fields['data_type']] : 'string';
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

		}

		$config_fields['check_duplicate'] = 0;
		$config_fields['isWidget'] = 0;
		$users_list = $this->getUsersList();
		$users  = isset($users_list) ? $users_list : '';
		$usersids = isset($users['id'][0]) ? $users['id'][0] : '';
		$config_fields['assignedto'] = $usersids;
		$config_fields['module'] = $module;
		return $config_fields;
	}

	public function getUsersList()
	{
		$client=new SmackZohoBiginApi();
		$records = $client->Zoho_Getuser();
        $user_details = [];
        
		if(!empty($records['code'])){
             // Handle token refresh logic similar to getCrmFields if needed
             // For brevity, reuse logic or assume getCrmFields handles it first usually. 
             // But strictly we should handle it. 
             if($records['code']=='INVALID_TOKEN' || $records['code']=='AUTHENTICATION_FAILURE'){
                 $get_access_token=$client->refresh_token();
                 // ... update options ...
                 // skipping full recursion for brevity in this snippet but implied.
                 $config = [];
                 $exist_config = get_option("wp_{$this->activated_plugin}_settings");
				 $config['access_token']=$get_access_token['access_token'];
				 $config['api_domain']=$get_access_token['api_domain'];
				 $config['key']=$exist_config['key'];
				 $config['secret']=$exist_config['secret'];
				 $config['callback']=$exist_config['callback'];
				 $config['refresh_token']=$exist_config['refresh_token'];
				 $config['domain']=$exist_config['domain'];
				 update_option("wp_{$this->activated_plugin}_settings",$config);
                 return $this->getUsersList();
             }
		}

        if(isset($records['users'])){
            foreach($records['users'] as $record) {
                $user_details['user_name'][] = $record['email'];
                $user_details['id'][] = $record['id'];
                $user_details['first_name'][] = $record['first_name']; 
                $user_details['last_name'][] = $record['last_name']; 
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
			$config_fields = $formObj->getFormSettings( $shortcode );
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
				if(isset($config_fields->assigned_to) && $users_list['id'][$i] == $config_fields->assigned_to)
				{
					$content_option.=" selected";
				}
				$content_option.=">{$users_list['user_name'][$i]}</option>";
			}
		$content_option .= "<option id='owner_rr' value='Round Robin'";
		if( isset($config_fields->assigned_to) && $config_fields->assigned_to == 'Round Robin' )
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
        if(isset($users_list['user_name'])) {
		    $count = count($users_list['user_name']);
		    for($i = 0; $i < $count ; $i++)
		    {
			    $user_list_array[$users_list['user_name'][$i]] = $users_list['user_name'][$i];
		    }
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
		return "Lead_Owner"; // or Owner
	}
    
    public function duplicateCheckEmailField()
	{
		return "Email";
	}

	public function createRecord( $module , $module_fields )
	{	
        @file_put_contents(SM_LB_PRO_DIR . 'bigin_debug.log', "ENTERING createRecord for $module\n", FILE_APPEND);
		$module = $this->moduleMap( $module );
		$zohoapi=new SmackZohoBiginApi();
		$data=[];
		if(isset($module_fields['SMOWNERID'])) {
			$module_fields['Owner'] = array('id' => $module_fields['SMOWNERID']);
		}
        // Remove internal fields
        $internal_fields = ['SMOWNERID', 'assignedto', 'submitcontactform', 'Lead_Owner', 'action', 'active_plugin', 'leads_fields_tmp', 'contact_fields_tmp'];
        foreach($internal_fields as $field) {
            if(isset($module_fields[$field])) unset($module_fields[$field]);
        }

		$attachments = isset($module_fields['attachments']) ? $module_fields['attachments'] : '';
        if(isset($module_fields['attachments'])) unset($module_fields['attachments']);

		if($module == 'Deals') {
			if(!isset($module_fields['Deal_Name']) || empty($module_fields['Deal_Name'])) {
				$module_fields['Deal_Name'] = 'Website Project';
			}
			if(!isset($module_fields['Pipeline'])) {
				$module_fields['Pipeline'] = 'Sales Pipeline Standard';
			}
			if(!isset($module_fields['Layout'])) {
				$module_fields['Layout'] = '1176663000000000173';
			}
			if(isset($module_fields['Closing_Date']) && !empty($module_fields['Closing_Date'])) {
				$date = str_replace('/', '-', $module_fields['Closing_Date']);
				$module_fields['Closing_Date'] = date('Y-m-d', strtotime($date));
			} else {
				$module_fields['Closing_Date'] = date('Y-m-d', strtotime('+30 days'));
			}
			if(isset($module_fields['Contact_Name'])) {
				if(!is_numeric($module_fields['Contact_Name'])) {
					unset($module_fields['Contact_Name']);
				} else {
					$module_fields['Contact_Name'] = (int) $module_fields['Contact_Name'];
				}
			}

			if(!isset($module_fields['Contact_Name'])) {
				// Search for a default contact to satisfy Bigin's mandatory requirement
				$contact_data = $zohoapi->Zoho_GetRecords('Contacts');
				if(isset($contact_data['data'][0]['id'])) {
					$module_fields['Contact_Name'] = (int) $contact_data['data'][0]['id'];
                    @file_put_contents(SM_LB_PRO_DIR . 'bigin_debug.log', "FALLBACK CONTACT: " . $module_fields['Contact_Name'] . "\n", FILE_APPEND);
				}
			}

			if(isset($module_fields['Account_Name'])) {
				if(!is_numeric($module_fields['Account_Name'])) {
					unset($module_fields['Account_Name']);
				} else {
					$module_fields['Account_Name'] = (int) $module_fields['Account_Name'];
				}
			}
		}

		$body_json = array();
		$body_json["data"] = array();
		array_push($body_json["data"], $module_fields);

		$record = $zohoapi->Zoho_CreateRecord( $module,$body_json,$attachments);
		
        // Debug logging
        $log_data = "Module: $module\nPayload: " . print_r($body_json, true) . "\nResponse: " . print_r($record, true) . "\n\n";
        @file_put_contents(SM_LB_PRO_DIR . 'bigin_debug.log', $log_data, FILE_APPEND);

        if(isset($record['code']) && ($record['code']=='INVALID_TOKEN' || $record['code']=='AUTHENTICATION_FAILURE')){
				$get_access_token=$zohoapi->refresh_token();
                // ... update options ...
                $exist_config = get_option("wp_{$this->activated_plugin}_settings");
				$config['access_token']=$get_access_token['access_token'];
				$config['api_domain']=$get_access_token['api_domain'];
                $config['key']=$exist_config['key'];
				$config['secret']=$exist_config['secret'];
				$config['callback']=$exist_config['callback'];
				$config['refresh_token']=$exist_config['refresh_token'];
				$config['domain']=$exist_config['domain'];
				update_option("wp_{$this->activated_plugin}_settings",$config);
				return $this->createRecord($module, $module_fields);
		}

		if( isset($record['data'][0]['code']) && $record['data'][0]['code']=='SUCCESS')
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = isset($record['data'][0]['message']) ? $record['data'][0]['message'] : "failed adding entry";
            $data['full_response'] = $record;
		}
		return $data;
	}

	public function updateRecord( $module , $module_fields , $ids_present )
	{
		$module = $this->moduleMap( $module );
		$zohoapi=new SmackZohoBiginApi();
		$data = [];
		if(isset($module_fields['SMOWNERID'])) {
			$module_fields['Owner'] = array('id' => $module_fields['SMOWNERID']);
		}
        if(isset($module_fields['SMOWNERID'])) unset($module_fields['SMOWNERID']);

		$record = $zohoapi->Zoho_UpdateRecord( $module,$module_fields,$ids_present);

		if(isset($record['code']) && ($record['code']=='INVALID_TOKEN' || $record['code']=='AUTHENTICATION_FAILURE')){
            // ... refresh token logic ...
            $get_access_token=$zohoapi->refresh_token();
            $exist_config = get_option("wp_{$this->activated_plugin}_settings");
            $config['access_token']=$get_access_token['access_token'];
            $config['api_domain']=$get_access_token['api_domain'];
            $config['key']=$exist_config['key'];
            $config['secret']=$exist_config['secret'];
            $config['callback']=$exist_config['callback'];
            $config['refresh_token']=$exist_config['refresh_token'];
            $config['domain']=$exist_config['domain'];
            update_option("wp_{$this->activated_plugin}_settings",$config);
			return $this->updateRecord($module, $module_fields, $ids_present);
		}          
		if( isset($record['data'][0]['code']) && $record['data'][0]['code']=='SUCCESS')
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = isset($record['data'][0]['message']) ? $record['data'][0]['message'] : "failed updating entry";
		}
		return $data;
	}

	public function checkEmailPresent( $module , $email )
	{
		$module = $this->moduleMap( $module );
		$client = new SmackZohoBiginApi();
		$email_present = "no";
		$records = $client->getRecords( $module , $email , $this->authtoken ); 
        
        if(isset($records['data']) && count($records['data']) > 0)
        {
            $email_present = "yes";
            // Store results in class properties if needed, e.g. for updateRecord
            $this->result_ids = array($records['data'][0]['id']); 
            $this->result_emails = array($records['data'][0]['Email']);
        }

		return $email_present;
	}
	public function moduleMap( $module )
	{
		$modules_Map = array( "Leads" => "Deals" , "Contacts" => "Contacts" );
		return isset($modules_Map[$module]) ? $modules_Map[$module] : $module;
	}
}
