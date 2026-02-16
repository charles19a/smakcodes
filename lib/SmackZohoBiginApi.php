<?php
if( !class_exists( "SmackZohoBiginApi" ) )
{
	class SmackZohoBiginApi{
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
			$url = $this->zohoapidomain."/bigin/v1/settings/fields?module=$module";
			$args = array(
                    'sslverify' => false,
					'headers' => array(
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
				     );
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );
			$body = json_decode($response, true);
			return $body;
		}
		public function Zoho_CreateRecord($module, $data_array, $extraParams = "") {
			try{
				
				$apiUrl = $this->zohoapidomain."/bigin/v1/$module";
				
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
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Disable SSL check for local dev
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
			
            $apiUrl = $this->zohoapidomain."/bigin/v1/$module/$crm_id/Attachments";
			
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
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
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
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
			
            $apiUrl = $this->zohoapidomain."/bigin/v1/$module/" . $ids_present;
			
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			curl_close($ch); 
			$result_array = json_decode($result,TRUE);
			return $result_array;
		}

		public function getRecords( $modulename, $email, $authkey , $selectColumns ="" , $xmlData="" , $extraParams = "" )
		{
			
            $url = $this->zohoapidomain."/bigin/v1/$modulename/search?email=$email";
			
			$args = array(
                    'sslverify' => false,
					'headers' => array(
						'Authorization' => 'Zoho-oauthtoken '.$this->access_token
						)
				     );
			$response = wp_remote_retrieve_body( wp_remote_get($url, $args ) );
			$body = json_decode($response, true);
			return $body;
		}

		public function Zoho_GetRecords( $module )
		{
			$url = $this->zohoapidomain."/bigin/v1/$module";
			$args = array(
				'sslverify' => false,
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
			
            $url = $this->zohoapidomain."/bigin/v1/users?type=AllUsers";
			
			$args = array(
                    'sslverify' => false,
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
            // Placeholder: Bigin conversion might differ.
			return null;
		}	

		public function Zoho_DeleteRecord($crm_id,$modulename) {
			
            $apiUrl = $this->zohoapidomain."/bigin/v1/$modulename/$crm_id";
			
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			curl_close($ch); 
			$result_array = json_decode($result,TRUE);
			return $result_array;
		}
	}
}
?>
