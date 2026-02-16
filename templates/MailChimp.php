<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SmackcodersMailChimp
{
	private $apikey;
	private $endpoint = 'https://<dc>.api.mailchimp.com/3.0';

	public function __construct($apikey)
	{
		$this->apikey = $apikey; 
		list(, $datacentre) = explode('-', $this->apikey);
		$this->endpoint = str_replace('<dc>', $datacentre, $this->endpoint);
	}
	public function subscriberHash($email)
	{
		global $log;
		$log->debug('Entering MailChimp :: subscriberHash() method...');
		$log->debug('Exiting MailChimp ::subscriberHash method ...');
		return md5(strtolower($email));
	}
	public function get($method,$args=array(), $timeout=10)
	{
		global $log;
		return $this->makeRequest('GET', $method, $args, $timeout);
	}

	public function post($method, $args=array(), $timeout=10)
	{
		global $log;
		$log->debug('Entering MailChimp :: post() method...');
		$log->debug('Exiting MailChimp :: post method ...');
		return $this->makeRequest('POST', $method, $args, $timeout);

	}
	public function delete($call, $method, $args=array(), $timeout=10)
	{	
		global $log;
		$log->debug('Entering MailChimp :: delete() method...');
		$log->debug('Exiting MailChimp :: delete method ...');
		return $this->makeRequest($call, $method, $args, $timeout);

	}
	public function put($method, $args=array(), $timeout=10)
	{
		global $log;
		$log->debug('Entering MailChimp :: put() method...');
		$log->debug('Exiting MailChimp :: put method ...');
		return $this->makeRequest('PUT', $method, $args, $timeout);

	}
	public function patch($method, $args=array(), $timeout=10){
		global $log;
		$log->debug('Entering MailChimp :: patch() method...');
		$log->debug('Exiting MailChimp :: patch method ...');
		return $this->makeRequest('PATCH', $method, $args, $timeout);

	}

	public function getbatch($call, $id, $method,$args=array(), $timeout=10)
	{ 
		global $log;
		$log->debug('Entering MailChimp :: getbatch() method...');
		require_once('modules/VTMailChimpLists/libs/Batch.php');
		$batch = new SmackBatch($this, $batchid);
		if($call == 'GET'){
			return $batch->get($id, $method, $args);}
		if($call == 'POST'){
			return $batch->post($id, $method, $args); 
		}
		if($call == 'PUT'){
			return $batch->put($id, $method, $args);
		}
		$log->debug('Exiting MailChimp :: getbatch method ...');
	}

	public function makeRequest($http_verb, $method, $args=array() ,$timeout=10){
		global $log;
		$url = $this->endpoint.'/'.$method;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'SMACK/MC-API:3.0');
		curl_setopt($ch, CURLOPT_USERPWD, "user:".$this->apikey);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_verb);
		if($http_verb == 'GET'){
			$query = http_build_query($args);
			curl_setopt($ch, CURLOPT_URL, $url . '?' . $query);
			//	break;
		}
		if(!empty($args)){
			$jsonArgs = json_encode($args);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonArgs);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result ? json_decode($result, true) : false;
	}
}
?>
