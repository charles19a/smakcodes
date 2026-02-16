<?php
if( !class_exists( "SmackZohoApi" ) )
{
	class SmackZohoApi{
		/******************************************************************************************
		 * Copyright (C) Smackcoders 2016 - All Rights Reserved
		 * Unauthorized copying of this file, via any medium is strictly prohibited
		 * Proprietary and confidential
		 * You can contact Smackcoders at email address info@smackcoders.com.
		 *******************************************************************************************/

		 public $zohocrmurl;
		 public $access_token;
		 public $refresh_token;
		 public $client_id;
		 public $client_secret;
		 public $callback;
		 public $domain;
		 public $zohoapidomain;
		 public function __construct()
		{
			$activated_plugin = get_option("WpLeadBuilderProActivatedPlugin");
			$zohoconfig=get_option("wp_{$activated_plugin}_settings");
			$accesstok = isset($zohoconfig['access_token']) ? $zohoconfig['access_token'] : '';
			$refreshtok = isset($zohoconfig['refresh_token']) ? $zohoconfig['refresh_token']: '';
			$zohoapidomain = isset($zohoconfig['api_domain']) ? $zohoconfig['api_domain']: '';

			$this->access_token=$accesstok;
			$this->refresh_token=$refreshtok;
			$this->client_id=$zohoconfig['key'];
			$this->callback=$zohoconfig['callback'];
			$this->client_secret=$zohoconfig['secret'];
			$this->domain=$zohoconfig['domain'];
			$this->zohoapidomain = $zohoapidomain;

		}
		public function APIMethod($module, $methodname, $authkey)
		{
			//$url = "https://www.zohoapis".$this->domain."/crm/v5/settings/fields?module=$module";
			$url = $this->zohoapidomain."/crm/v7/settings/fields?module=$module";

			$args = array(
					'headers' => array(
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
				     );
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );
			$body = json_decode($response, true);
			return $body;
		}
		public function Zoho_CreateRecord( $module = "Lead", $data_array = array(), $extraParams = "" ) {
			try{
				
				$apiUrl = $this->zohoapidomain."/crm/v7/$module";
				
				$fields = json_encode($data_array);
				$headers = array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen($fields),
						sprintf('Authorization: Zoho-oauthtoken %s', $this->access_token),
						);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $apiUrl);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
				curl_setopt($ch, CURLOPT_TIMEOUT, 60);
				$result = curl_exec($ch);
				curl_close($ch); 
				$result_array = json_decode($result,true);
				if($extraParams != "")
				{
					foreach($extraParams as $field => $path){			
						$this->insertattachment($result_array,$path,$module);
					}
				}
			}catch(\Exception $exception){
				// TODO - handle the error in log
			}
			return $result_array;
		}
		public function insertattachment($result_array,$path,$module)
		{
			$crm_id = $result_array['data'][0]['details']['id'];
			if($this->domain == 'cloud.ca'){
				$apiUrl = "https://www.zohoapis.ca/crm/v5/$module/$crm_id/Attachments";
			}
			else{
				$apiUrl = $this->zohoapidomain."/crm/v7/$module/$crm_id/Attachments";
			}
			$headers = array(
					'Content-Type: multipart/form-data',
					sprintf('Authorization: Zoho-oauthtoken %s', $this->access_token),
					);
			if (function_exists('curl_file_create')) { // php 5.6+
				$cFile = curl_file_create($path);
			} else { //
				$cFile = '@' . realpath($path);
			}
			$post = array('file'=> $cFile);                        
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiUrl);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			$result = curl_exec($ch);
			curl_close($ch);
			$result_array = json_decode($result,true);
		}
		public function ZohoGet_Getaccess( $config , $code ) {
			if($this->domain == '.ca'){
				$token_url = "https://accounts.zohocloud".$this->domain."/oauth/v2/token?";	
			}
			else{
				$token_url = "https://accounts.zoho".$this->domain."/oauth/v2/token?";
			}
			
			$params = "code=" .$code
				. "&redirect_uri=" . $this->callback 
				. "&client_id=" . $this->client_id
				. "&client_secret=" . $this->client_secret
				. "&grant_type=authorization_code";
			$curl = curl_init($token_url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			$json_response = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if ( $status != 200 ) {
				die("Error: call to token URL $token_url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
			}
			curl_close($curl);
			$response = json_decode($json_response, true);
			return $response;
		}
		public function refresh_token() {
			if($this->domain == '.ca'){
				$token_url = "https://accounts.zohocloud".$this->domain."/oauth/v2/token?";
			}
			else{
				$token_url = "https://accounts.zoho".$this->domain."/oauth/v2/token?";
			}
			$params = "&refresh_token=" . $this->refresh_token
				. "&client_id=" . $this->client_id
				. "&client_secret=" . $this->client_secret
				. "&grant_type=refresh_token";
			$curl = curl_init($token_url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			$json_response = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if ( $status != 200 ) {
				die("Error: call to token URL $token_url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
			}
			curl_close($curl);
			$response = json_decode($json_response, true);
			return $response;
		}
		public function Zoho_UpdateRecord($module,$module_fields,$ids_present){
			if($this->domain == 'cloud.ca'){
				$apiUrl = "https://www.zohoapis.ca/crm/v5/$module/" . $ids_present;
			}
			else{
				$apiUrl = $this->zohoapidomain."/crm/v7/$module/" . $ids_present;
			}
			$fields = json_encode(array("data" => array($module_fields)));
			$headers = array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($fields),
					sprintf('Authorization: Zoho-oauthtoken %s', $this->access_token),
					);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiUrl);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			$result = curl_exec($ch);
			curl_close($ch); 
			$result_array = json_decode($result,TRUE);
			return $result_array;
		}

		public function insertRecord( $modulename, $methodname, $authkey , $xmlData="" , $extraParams = "" )
		{
			$uri = $this->zohocrmurl . $modulename . "/".$methodname."";
			/* Append your parameters here */
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$authkey}";//Give your authtoken
			if($extraParams != "" && !is_array($extraParams) )
			{
				$postContent .= $extraParams;
			}
			$postContent .= "&xmlData={$xmlData}";
			$postContent .= "&wfTrigger=true";
			$ch = curl_init($uri);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			curl_close($ch);
			//Attachment
			if($extraParams && is_array($extraParams)){
				foreach($extraParams as $field => $path){
					$this->insertattachment($result_array,$authkey,$path,$modulename);//Feb03 fix
				}
			}
			//Attachment
			return $result_array;
		}


		public function getRecords( $modulename, $email, $authkey , $selectColumns ="" , $xmlData="" , $extraParams = "" )
		{
			if($this->domain == 'cloud.ca'){
				$url = "https://www.zohoapis.ca/crm/v5/$modulename/search?email=$email";
			}
			else{
				$url = $this->zohoapidomain."/crm/v7/$modulename/search?email=$email";
			}
			$args = array(
					'headers' => array(
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
				     );
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );
			$body = json_decode($response, true);
			return $body;
		}
		public function Zoho_Getuser()
		{
			if($this->domain == 'cloud.ca'){
				$url = "https://www.zohoapis.ca/crm/v5/users?type=AllUsers";
			}
			else{
				$url = $this->zohoapidomain."/crm/v7/users?type=AllUsers";
			}
			$args = array(
					'headers' => array(
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
				     );
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );
			$body = json_decode($response, true);
			return $body;
		}

		public function convertLeads($modulename , $crm_id , $order_id , $lead_no , $authkey , $sales_order )
		{
			if($this->domain == 'cloud.ca'){
				$url = "https://www.zohoapis.ca/crm/v5/$modulename/$crm_id";
			}
			else{
				$url = $this->zohoapidomain."/crm/v7/$modulename/$crm_id";
			}
			$args = array(
					'headers' => array(
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
				     );
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );
			$body = json_decode($response, true);
			$attachments='';
			$record = $this->Zoho_CreateRecord( 'Contacts',$body,$attachments);
			$records=$this->Zoho_DeleteRecord($crm_id,$modulename);
			return $record;
		}	
		public function Zoho_DeleteRecord($crm_id,$modulename) {
			if($this->domain == 'cloud.ca'){
				$apiUrl = "https://www.zohoapis.ca/crm/v5/$modulename/$crm_id";
			}
			else{
				$apiUrl = $this->zohoapidomain."/crm/v7/$modulename/$crm_id";
			}
			$headers = array(
					'Content-Type: application/json',
					sprintf('Authorization: Zoho-oauthtoken %s', $this->access_token),
					);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiUrl);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			$result = curl_exec($ch);
			curl_close($ch); 
			$result_array = json_decode($result,TRUE);
			return $result_array;
		}
		public function getAccountId($authkey)
		{
			if($this->domain == '.ca'){
				$Account_uri = "https://crm.zohocloud".$this->domain."/crm/private/xml/Accounts/getRecords";
			}
			else{
				$Account_uri = "https://crm.zoho".$this->domain."/crm/private/xml/Accounts/getRecords";
			}
			$Account_postContent = "scope=crmapi";
			$Account_postContent .= "&authtoken={$authkey}";//Give your authtoken
			$Account_postContent .= "&selectColumns=Accounts(ACCOUNTID)";

			$ch = curl_init($Account_uri);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $Account_postContent);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);

			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			curl_close($ch);
			$ACCOUNT_ID = $result_array['result']['Accounts']['row'][0]['FL'];
			return $ACCOUNT_ID;
		}

		public function getModules($TFA_authtoken)
		{
			$uri = "https://crm.zoho".$this->domain."/crm/private/xml/Info/getModules?"; // Check Auth token present in Zoho //ONLY FOR TFA CHECK
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$TFA_authtoken}";
			$ch = curl_init($uri );
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			curl_close($ch);
			return $result_array;

		}

		public function getConvertLeadOwner($modulename , $authkey , $record_id )
		{
			$zohourl = "https://crm.zoho".$this->domain."/crm/private/xml/";
			$methodname = 'getRecords';
			$module_slug = rtrim( $modulename , 's' );
			$uri = $zohourl . $modulename . "/".$methodname."";
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$authkey}";//Give your authtoken
			$postContent .= "&id={$record_id}&selectColumns={$modulename}({$module_slug} Owner)";

			$ch = curl_init($uri);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);

			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			curl_close($ch);
			$Lead_owner = $result_array['result'][$modulename]['row']['FL'][1];
			return $Lead_owner;
		}

		public function getAuthenticationToken( $username , $password  )
		{
			$username = urlencode( $username );
			$password = urlencode( $password );
			$param = "SCOPE=ZohoCRM/crmapi&EMAIL_ID=".$username."&PASSWORD=".$password;
			if($this->domain == '.ca'){
				$ch = curl_init("https://accounts.zohocloud".$this->domain."/apiauthtoken/nb/create");
			}
			else{
				$ch = curl_init("https://accounts.zoho".$this->domain."/apiauthtoken/nb/create");
			}
			
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			$anArray = explode("\n",$result);
			$authToken = explode("=",$anArray['2']);
			$cmp = strcmp($authToken['0'],"AUTHTOKEN");
			if ($cmp == 0)
			{
				$return_array['authToken'] = $authToken['1'];
			}
			$return_result = explode("=" , $anArray['3'] );
			$cmp1 = strcmp($return_result['0'],"RESULT");
			if($cmp1 == 0)
			{
				$return_array['result'] = $return_result['1'];
			}
			if($return_result[1] == 'FALSE'){
				$return_cause = explode("=",$anArray[2]);
				$cmp2 = strcmp($return_cause[0],'CAUSE');
				if($cmp2 == 0)
					$return_array['cause'] = $return_cause[1];
			}
			curl_close($ch);
			return $return_array;
		}
	}
}
?>
