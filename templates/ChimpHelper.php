<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

require_once('MailChimp.php');
class ChimpHelper	
{
	/**
	 * mailchimp api key
	 */
	public $apiKey;

	/**
	 * chimp object
	 */
	public $chimp;

	public function __construct($apiKey)	{
		$this->chimp = new SmackcodersMailChimp($apiKey);
		$this->apiKey = $apiKey;
	}

	/**
	 * Get links to all other resources available in the API
	 **/
	public function getApiInfo(){
		$api = $this->chimp->get('/');
		return $api;
	}

	/**
	 * get chimp list	 
	 * @param integer $start
	 * @param integer $limit
	 * @param datetime $lastSyncedTime
	 * @param integer $listid
	 * @return array $lists
	 */
	public function getChimpLists($start, $limit, $lastSyncedTime, $listid = false)	{
		global $log;
		$log->debug('Entering ChimpHelper :: getChimpLists() method...');
		$lists = $this->chimp->get('lists',  array('fields'=> 'total_items,lists.id,lists.name,lists.date_created','count'=>$limit,'offset'=>$start , 'since_date_created' => $lastSyncedTime),$timeout=null);
		$log->debug('Exiting ChimpHelper :: getChimpLists method ...');
		return $lists;
	}
	/**
	 * get chimp particular list
	 **/
	public function getChimpPerLists($start, $limit, $lastSyncedTime, $listid = false){
		global $log;
		$log->debug('Entering ChimpHelper :: getChimpPerLists() method...');
		$lists = $this->chimp->get('lists/'.$listid,array('since_last_changed' => $lastSyncedTime),$timeout=null);
		$log->debug('Exiting ChimpHelper :: getChimpPerLists method ...');
		return $lists;
	}


	public function createLists($method, $batch){
		global $log;
		$log->debug('Entering ChimpHelper ::createLists() method...');
		$lists = $this->chimp->post('lists/', $batch);
		$log->debug('Exiting ChimpHelper :: createLists method ...');
		return $lists;
	}
	public function updateLists($listId, $batch){
		global $log;
		$log->debug('Entering ChimpHelper :: updateLists() method...');
		$lists = $this->chimp->patch('lists/'.$listId, $batch);
		$log->debug('Exiting ChimpHelper :: updateLists method ...');
		return $lists;
	}
	/** DELETE CHIMP LISTS **/

	public function deleteMemebers($listid,$memeberid,$batch)	{
		global $log;
		$log->debug('Entering ChimpHelper :: deleteMemebers() method...');
		$response = $this->chimp->delete('DELETE','lists/'.$listid.'/members/'.$memeberid,$batch);
		$log->debug('Exiting ChimpHelper :: deleteMemebers method ...');
		return $response;
	}

	public function getMemebers($listid,$memeberid)      {
		global $log;
		$log->debug('Entering ChimpHelper :: getMemebers() method...');
		$response = $this->chimp->get('lists/'.$listid.'/members/'.$memeberid);
		$log->debug('Exiting ChimpHelper :: getMemebers method ...');
		return $response;
	}

	public function getMemebers_batch($listid,$memeberid)      {
		global $log;
		$log->debug('Entering ChimpHelper :: getMemebers_batch() method...');
		$response = $this->chimp->getbatch('GET','','lists/'.$listid.'/members/'.$memeberid);
		$log->debug('Exiting ChimpHelper :: getMemebers_batch method ...');
		return $response;
	}

	public function getMemeber($listid,$email)      {
		global $log;
		$log->debug('Entering ChimpHelper :: getMemeber() method...');
		$subscriber_hash = $this->chimp->subscriberHash($email);
		$response = $this->chimp->get('lists/'.$listid.'/members/'.$subscriber_hash);
		$log->debug('Exiting ChimpHelper :: getMemeber method ...');
		return $response;
	}

	/**
	 * get list memeber activity
	 * @param string $listId
	 * @param array $memberIds
	 * @return array $memberActivity
	 */
	public function getListMemberActivities($listId,$memberIds)	{
		global $log;
		$log->debug('Entering ChimpHelper :: getListMemberActivities() method...');
		$memberActivity = $this->chimp->get('lists/'.$listId.'/members/'.$memberIds.'/activity');
		$log->debug('Exiting ChimpHelper :: getListMemberActivities method ...');
		return $memberActivity;
	}


	/**
	 * return lists for given member id
	 * @param string $memberId
	 * @return array $lists
	 */
	public function getListsForMemberID($memberId)	{
		global $log;
		$log->debug('Entering ChimpHelper :: getListsForMemberID() method...');
		$lists = $this->chimp->get('helper/lists-for-email', array('email' => array('euid' => $memberId)));
		$log->debug('Exiting ChimpHelper :: getListsForMemberID method ...');
		return $lists;
	}



	public function campgainFeedback($campaignId){
		global $log;
		$log->debug('Entering ChimpHelper :: campgainFeedback() method...');
		$feedback = $this->chimp->get('reports/'.$campaignId.'/advice');
		$log->debug('Exiting ChimpHelper :: campgainFeedback method ...');
		return $feedback;
	}

	public function listGrowth($listId){
		global $log;
		$log->debug('Entering ChimpHelper :: listGrowth() method...');
		$response = $this->chimp->get('lists/'.$listId.'/growth-history');
		$log->debug('Exiting ChimpHelper :: listGrowth method ...');
		return $response;
	}

	/** TODO charr data for MailCHimp reports **/
	/** Reports Abuse Collection **/
	public function reportsAbuseCollection($campaignid,$method)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsAbuseCollection() method...');
		$reports  = $this->chimp->get('reports/'.$campaignid.'/abuse-reports');
		$log->debug('Exiting ChimpHelper :: reportsAbuseCollection method ...');
		return $reports;
	}

	/** Reports Abuse Instance **/
	public function reportsAbuseInstance($campaingid,$id) {
		global $log;
		$log->debug('Entering ChimpHelper :: reportsAbuseInstance() method...');
		$reports = $this->chimp->get('reports/'.$campaingid.'/abuse-reports/'.$id);
		$log->debug('Exiting ChimpHelper :: reportsAbuseInstance method ...');
		return $reports;
	}

	/** Reports Click Details Collection **/

	public function reportsClickDetailsCollection($campaignid)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsClickDetailsCollection() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/click-details');
		$log->debug('Exiting ChimpHelper :: reportsClickDetailsCollection method ...');
		return $reports;
	}


	/** Reports Click Details Instance **/

	public function reportsClickDetailsInstance($campaignid,$id)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsClickDetailsInstance() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/click-details/'.$id);
		$log->debug('Exiting ChimpHelper :: reportsClickDetailsInstance method ...');
		return $reports;
	}

	/** Reports Collection **/

	public function reportsCollection($id = false)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsCollection() method...');
		$reports = $this->chimp->get('reports');
		$log->debug('Exiting ChimpHelper :: reportsCollection method ...');
		return $reports;
	}

	/** Reports Domain Performance Collection **/

	public function reportsDomainPerformanceCollection($campaignid)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsDomainPerformanceCollection() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/domain-performance');
		$log->debug('Exiting ChimpHelper :: reportsDomainPerformanceCollection method ...');
		return $reports;
	}

	/** Reports Email Activity Collection **/

	public function reportsEmailActivityCollection($campaignid)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsEmailActivityCollection() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/email-activity');
		$log->debug('Exiting ChimpHelper :: reportsEmailActivityCollection method ...');
		return $reports;
	}

	/** Reports Email Activity Instance **/

	public function reportsEmailActivityInstance($campaignid,$email)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsEmailActivityInstance() method...');
		$subscriber_hash = $this->chimp->subscriberHash($email);
		$reports = $this->chimp->get('reports/'.$campaignid.'/email-activity/'.$subscriber_hash);
		$log->debug('Exiting ChimpHelper :: reportsEmailActivityInstance method ...');
		return $reports;
	}

	/** Reports Instance **/

	public function reportsInstance($campaignid) {
		global $log;
		$log->debug('Entering ChimpHelper :: reportsInstance() method...');
		$reports = $this->chimp->get('reports/'.$campaignid);
		$log->debug('Exiting ChimpHelper :: reportsInstance method ...');
		return $reports;
	}

	/** Reports Sent To Collection **/

	public function reportsSentToCollection($campaignid)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsSentToCollection() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/sent-to');
		$log->debug('Exiting ChimpHelper :: reportsSentToCollection method ...');
		return $reports;
	}

	/* Reports Send To Instance */

	public function reportsSentToInstance($campaignid,$email)    {
		global $log;
		$log->debug('Entering ChimpHelper :: reportsSentToInstance() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/send-to/'.$email);
		$log->debug('Exiting ChimpHelper :: reportsSentToInstance method ...');
		return $reports;
	}

	/** Reports Unsubscribes Collection **/
	public function reportsUnsubcribedCollection($campaignid)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsUnsubcribedCollection() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/unsubscribed');
		$log->debug('Exiting ChimpHelper :: reportsUnsubcribedCollection method ...');
		return $reports;
	}

	/** Reports Unsubscribes Instance **/
	public function reportsUnsubcribeInstance($campaignid,$email)	{
		global $log;
		$log->debug('Entering ChimpHelper :: reportsUnsubcribeInstance() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/unsubscribed/'.$email);
		$log->debug('Exiting ChimpHelper :: reportsUnsubcribeInstance method ...');
		return $reports;
	}

	/**Reports Sub-Reports**/	
	public function reportSubReport($campaignid){
		global $log;
		$log->debug('Entering ChimpHelper :: reportSubReport() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/sub-reports/');
		$log->debug('Exiting ChimpHelper :: reportSubReport method ...');
		return $reports;

	}
	/**Reports locations**/
	public function reportLocation($campaignid){
		global $log;
		$log->debug('Entering ChimpHelper :: reportLocation() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/locations/');
		$log->debug('Exiting ChimpHelper :: reportLocation method ...');
		return $reports;

	}

	/**Reports EepURL**/
	public function reportEepurl($campaignid){
		global $log;
		$log->debug('Entering ChimpHelper :: reportEepurl() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/eepurl/');
		$log->debug('Exiting ChimpHelper :: reportEepurl method ...');
		return $reports;

	}
	/**Reports advice**/
	public function reportAdvice($campaignid){
		global $log;
		$log->debug('Entering ChimpHelper :: reportAdvice() method...');
		$reports = $this->chimp->get('reports/'.$campaignid.'/advice/');
		$log->debug('Exiting ChimpHelper :: reportAdvice method ...');
		return $reports;

	}

	/**
	 * get campaign summary
	 * @param string $campaignId
	 * @return array $campaignSummary
	 */
	public function getChimpCampaignSummary($campaignId) {
		global $log;
		$log->debug('Entering ChimpHelper :: getChimpCampaignSummary() method...');
		$campaignSummary = $this->chimp->get('reports/'. $campaignId);
		$log->debug('Exiting ChimpHelper :: getChimpCampaignSummary method ...');
		return $campaignSummary;
	}

	/*Create campaign*/
	public function createCampaign($method, $batch){
		global $log;
		$log->debug('Entering ChimpHelper :: createCampaign() method...');
		$response = $this->chimp->post('/campaigns', $batch);
		$log->debug('Exiting ChimpHelper :: createCampaign method ...');
		return $response;
	}
	public function updateCampaign($campaignId, $batch){
		global $log;
		$log->debug('Entering ChimpHelper :: updateCampaign() method...');
		$response = $this->chimp->patch('/campaigns/'.$campaignId, $batch);
		$log->debug('Exiting  ChimpHelper :: updateCampaign method ...');
		return $response;
	}

	/**
	 * get chimp campaigns 
	 * @param integer $start
	 * @param integer $limit
	 * @param datetime $lastSyncedTime
	 * @param integer $listid
	 * @return array $lists
	 */
	public function getChimpCampaigns($start, $limit, $lastSyncedTime, $campaignId = false)	{
		$campaigns = $this->chimp->get('campaigns', array('fields' => 'campaigns.id,campaigns.settings.title,campaigns.create_time,campaigns.type,campaigns.status,campaigns.content_type,campaigns.recipients,total_items','count' => $limit,'offset' => $start, 'since_create_time' => $lastSyncedTime));
		return $campaigns;
	}

	public function CheckListDeleted($listid,$service)	{
		global $log;
		$log->debug('Entering ChimpHelper :: CheckListDeleted() method...');
		$deleted = $this->chimp->get($service.'/'.$listid);
		$log->debug('Exiting ChimpHelper :: CheckListDeleted method ...');
		return $deleted;
	}

	//get individual campaign list	
	public function getChimpCampaignsInd($campaignid,$lastSyncedTime = false)	{
		global $log;
		$log->debug('Entering ChimpHelper :: getChimpCampaignsInd() method...');
		$campaignInfo = $this->chimp->get('campaigns/'.$campaignid, array('since_create_time' => $lastSyncedTime));
		$log->debug('Exiting ChimpHelper :: getChimpCampaignsInd method ...');
		return $campaignInfo;
	}
	/**
	 * get chimp list merge vars
	 * @param array $listIds
	 * @return array $lists
	 */

	public function getListMergeVars($listIds,$total=false) {
		global $log;
		$log->debug('Entering ChimpHelper :: getListMergeVars() method...');
		$listMergeVars = $this->chimp->get('lists/'.$listIds.'/merge-fields',array('count'=>$total),$timeout=null );
		$log->debug('Exiting ChimpHelper ::getListMergeVars method ...');
		return $listMergeVars;
	}	

	/** get individual list groups list

	**/
	public function getIndividualListgroupings($listId, $start, $limit){
		global $log;
		$log->debug('Entering ChimpHelper :: getIndividualListgroupings() method...');
		$groupinginfo = $this->chimp->get('lists/'.$listId.'/interest-categories',array('fields'=>'categories.list_id,categories.id,categories.title,categories.type,categories.display_order,total_items', 'offset' => $start, 'count' => $limit),$timeout=null);
		$log->debug('Exiting  ChimpHelper :: getIndividualListgroupings method ...');
		return $groupinginfo;
	}

	/** get individual sub categorie
	 **/
	public function getIndividualcategorylist($groupindId,$listId, $start, $limit){
		global $log;
		$log->debug('Entering ChimpHelper :: getIndividualcategorylist() method...');
		$gropusinfo = $this->chimp->get('lists/'.$listId.'/interest-categories/'.$groupindId.'/interests',array('fields'=>'interests.category_id,interests.list_id,interests.id,interests.name,interests.display_order,total_items', 'offset' => $start, 'count' => $limit),$timeout=null);
		$log->debug('Exiting   ChimpHelper :: getIndividualcategorylist method ...');
		return $gropusinfo;
	}
	/**
	 * get chimp list members 
	 * @param integer $start
	 * @param integer $limit
	 * @param string $listId
	 * @param string $status
	 * @return array $listMembers
	 */
	public function getChimpListMembers($start, $limit, $listId, $status, $lastSyncedTime = false)	{
		global $log;
		$log->debug('Entering ChimpHelper :: getChimpListMembers() method...');
		$listMembers = $this->chimp->get('lists/'.$listId.'/members', array('fields'=>'total_items,members.id,members.email_address,members.unique_email_id,members.status,members.merge_fields,members.interests,members.stats,members.last_changed,members.list_id','count'=>$limit,'offset'=>$start,'since_last_changed'=>$lastSyncedTime,'sort_field'=>'last_changed','sort_dir'=>'ASC'),$timeout=null);
		$log->debug('Exiting  ChimpHelper :: getChimpListMembers method ...');
		return $listMembers;

	}

	public function fetchGroupsFromList($method, $list_id, $grouping_id)
	{
		global $log;
		$log->debug('Entering ChimpHelper :: fetchGroupsFromList() method...');
		$response = $this->chimp->get('lists/' . $list_id . '/interest-categories/' . $grouping_id . '/interests', $data);
		$log->debug('Exiting  ChimpHelper :: fetchGroupsFromList() method ...');
		return $response;
	}

	public function fetchListGroupings($list_id)
	{
		global $log;
		$log->debug('Entering ChimpHelper :: fetchListGroupings() method...');
		$response = $this->chimp->get('lists/' . $list_id . '/interest-categories', $data);
		$log->debug('Exiting  ChimpHelper :: fetchListGroupings() method ...');
		return $response;
	}

	/** 
	 * check whether chimp apikey is valid or not
	 * @return boolean
	 */
	public function checkChimpAPI()	{
		global $log;
		$log->debug('Entering ChimpHelper :: checkChimpAPI() method...');
		$isvalid = $this->chimp->get('');
		if(isset($isvalid['title']) && $isvalid['title'] == 'API Key Invalid' || !$isvalid)  
			return false;
		else
			return true;	
		$log->debug('Exiting  ChimpHelper :: checkChimpAPI method ...');
	}

	/**
	 * update campaign to mailchimp
	 * @param string $listId
	 * @param string $content
	 * @return array $response
	 */
	public function updateCampaignToMailChimp($data, $content)	{
		// type cannot be changed. so not need to pass type 
		// only one name can be passed at a time. should be options, content, segment_opts
		global $log;
		$log->debug('Entering ChimpHelper :: updateCampaignToMailChimp() method...');
		$response = $this->chimp->patch('campaigns/'.$data['cid'], $content);
		$log->debug('Exiting  ChimpHelper :: updateCampaignToMailChimp method ...');
		return $response;
	}


	/**
	 * send campaigns to mailchimp
	 * @param string $data
	 * @param string $content
	 * @return array $response
	 */
	public function sendCampaignToMailChimp($data, $content)	{
		global $log;
		$log->debug('Entering ChimpHelper :: sendCampaignToMailChimp() method...');
		$response = $this->chimp->post('campaigns/create', array('type' => $data['type'], 'options' => array('list_id' => $data['list_id'], 'subject' => $data['subject'], 'from_email' => $data['fromemail'], 'from_name' => $data['fromname'], 'to_name' => $data['toname'], 'generate_text' => false), 'content' => array($data['contenttype'] => $data['content'])));  
		$log->debug('Exiting  ChimpHelper :: sendCampaignToMailChimp method ...');
		return $response;
	}

	/** 
	 * add new groupings
	 * @param array $data
	 * @param array $response
	 */
	public function addGrouping($method,$data,$listId)	{
		global $log;
		$log->debug('Entering ChimpHelper :: addGrouping() method...');
		$response = $this->chimp->post('lists/'.$listId.'/interest-categories', $data);
		$log->debug('Exiting  ChimpHelper ::  addGrouping method ...');
		return $response;
	}

	/**
	 * Update grouping
	 * @param string method
	 * @param string data
	 * @param string listId
	 * @param string categoryid
	 */
	public function updateGrouping($method,$data,$listId,$categoryid)      {
		global $log;
		$log->debug('Entering ChimpHelper :: updateGrouping() method...');
		$response = $this->chimp->patch('lists/'.$listId.'/interest-categories/'.$categoryid, $data);
		$log->debug('Exiting ChimpHelper :: updateGrouping method ...');
		return $response;
	}

	/**
	 * Add Groups
	 * @param string method
	 * @param string data
	 * @param string listId
	 */
	public function addGroups($method,$data,$listId)	
	{
		global $log;
		$log->debug('Entering ChimpHelper :: addGroups() method...');
		$response = $this->chimp->post('lists/'.$listId.'/interest-categories/'.$data['category_id'].'/interests',$data);
		$log->debug('Exiting ChimpHelper :: addGroups method ...');
		return $response;
	}

	/**
	 * Update Group
	 * @param string method
	 * @param string data
	 * @param string listId
	 * @param string interestId
	 * @return array response
	 */
	public function updateGroups($method,$data,$listId,$interestId)        
	{
		global $log;
		$log->debug('Entering ChimpHelper :: updateGroups() method...');
		$response = $this->chimp->patch('lists/'.$listId.'/interest-categories/'.$data['category_id'].'/interests/'.$interestId,$data);
		$log->debug('Exiting ChimpHelper :: updateGroups method ...');
		return $response;
	}

	/**
	 * Delete a groupings from list 
	 * @param string method
	 * @param string listid
	 * @param string grouping_id
	 * @return array response
	 */
	public function deleteGrouping($method, $listid, $grouping_id)	
	{
		global $log;
		$log->debug('Entering ChimpHelper :: deleteGrouping() method ...');
		$response = $this->chimp->delete($method,'lists/'.$listid.'/interest-categories/' . $grouping_id, null);
		$log->debug('Exiting ChimpHelper :: deleteGrouping() method ...');
		return $response;
	}		

	public function deleteGroups($method,$listid,$childid,$parentid)	
	{
		global $log;
		$log->debug('Entering ChimpHelper :: deleteGroups() method...');
		$response = $this->chimp->delete($method,'lists/'.$listid.'/interest-categories/'.$parentid.'/interests/'.$childid , null);
		$log->debug('Exiting ChimpHelper :: deleteGroups method ...');
		return $response;
	}		

	public function addMembers($method,$listId,$batch)      {
		global $log;
		$log->debug('Entering ChimpHelper :: addMembers() method...');
		$response = $this->chimp->post('lists/'.$listId.'/members',$batch);
		$log->debug('Exiting ChimpHelper :: addMembers method ...');
		return $response;
	}
	public function updateMembers($method,$listId,$memberid,$batch) {
		global $log;
		$log->debug('Entering ChimpHelper :: updateMembers() method...');
		$response = $this->chimp->patch('lists/'.$listId.'/members/'.$memberid,$batch);
		$log->debug('Exiting ChimpHelper :: updateMembers method ...');
		return $response;
	}
	public function batchOperations(){
		global $log;
		$log->debug('Entering ChimpHelper :: batchOperations() method...');
		$response = $this->chimp->get('/batches');
		$log->debug('Exiting ChimpHelper :: batchOperations method...');
		return $response;
	}
	public function batchOpertionsInstance($id,$batchid){
		global $log;
		$log->debug('Entering ChimpHelper :: batchOperationsInstance() method...');
		$response = $this->chimp->getbatch('GET',$batchid, '/batches'.$batchid, '');
		$log->debug('Exiting ChimpHelper :: batchOperationsInstance method ...');
		return $response;
	}
	/**
	 * send list changes to mailchimp
	 * @param string $listId
	 * @param array $batch
	 * @return array $response
	 */
	// For adding the membere adn update the membere
	public function addMembers_batch($method,$operation_id, $listId,$batch)	{
		global $log;
		$log->debug('Entering ChimpHelper :: addMembers_batch() method...');
		$response = $this->chimp->getbatch($method,$operation_id,'lists/'.$listId.'/members',$batch);
		$log->debug('Exiting ChimpHelper :: addMembers_batch method ...');
		return $response;
	}

	public function updateMembers_batch($method,$operation_id,$listId,$memberid,$batch)	{
		global $log;
		$log->debug('Entering ChimpHelper :: updateMembers_batch() method...');
		$response = $this->chimp->getbatch('PUT',$operation_id,'lists/'.$listId.'/members/'.$memberid,$batch);
		$log->debug('Exiting ChimpHelper :: updateMembers_batch method ...');
		return $response;
	}

	public function sendListChangesToMailChimp($listId, $batch, $optIn)	{
		global $log;
		$log->debug('Entering ChimpHelper :: sendListChangesToMailChimp() method...');
		$response = $this->chimp->post('lists/batch-subscribe', array('id' => $listId, 'update_existing' => true, 'double_optin' => $optIn, 'batch' => $batch));
		$log->debug('Exiting ChimpHelper :: sendListChangesToMailChimp method ...');
		return $response;
	}

	/**
	 * unsubscribe user from mailchimp
	 * @param string $listId
	 * @param array $data
	 * @return array $response
	 */
	public function unsubscribeUserFromMailChimp($listId, $data)	{
		global $log;
		$log->debug('Entering ChimpHelper :: unsubscribeUserFromMailChimp() method...');
		$response = $this->chimp->patch('lists/unsubscribe', array('id' => $listId, 'email' => $data));
		$log->debug('Exiting ChimpHelper :: unsubscribeUserFromMailChimp method ...');
		return $response;
	}

	/*Authorized Apps*/
	public function authorizedApps($batch){
		global $log;
		$log->debug('Entering ChimpHelper :: authorizeApps() method...');
		$response = $this->chimp->post('/authorized-apps',$batch);
		$log->debug('Exiting ChimpHelper :: authorizeApps method ...');
		return $response;
	}

	public function getAuthorizedAppList(){
		global $log;
		$log->debug('Entering ChimpHelper :: getAuthorizedAppList() method...');
		$response = $this->chimp->get( '/authorized-apps');
		$log->debug('Exiting ChimpHelper :: getAuthorizedAppList method ...');
		return $response;
	}
	public function getAuthorizedApp($app_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getAuthorizedApp() method...');
		$response = $this->chimp->get( '/authorized-apps'.$app_id);
		$log->debug('Exiting ChimpHelper :: getAuthorizeApp method ...');
		return $response;
	}
	/**Automations**/
	public function getAutomations(){
		global $log;
		$log->debug('Entering ChimpHelper :: getAutomations() method...');
		$response = $this->chimp->get( '/automations');
		$log->debug('Exiting ChimpHelper :: getAutomations method ...');
		return $response;
	}

	public function getAutomationWorklow($workflow_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getAutomationWorklow() method...');
		$response = $this->chimp->get( '/automations/'.$workflow_id);
		$log->debug('Exiting ChimpHelper :: getAutomationWorklow method ...');
		return $response;
	}
	public function pauseAutomation($workflow_id){
		global $log;
		$log->debug('Entering ChimpHelper :: pauseAutomation() method...');
		$response = $this->chimp->post( '/automations/'.$workflow_id.'/actions/pause-all-emails');
		$log->debug('Exiting ChimpHelper :: pauseAutomation method ...');
		return $response;
	}
	public function startAutomation($workflow_id){
		global $log;
		$log->debug('Entering ChimpHelper :: startAutomation() method...');
		$response = $this->chimp->post( '/automations/'.$workflow_id.'/actions/start-all-emails');
		$log->debug('Exiting ChimpHelper :: startAutomation method ...');
		return $response;
	}

	/**Automation Emails**/
	public function getAutomatedEmailList($workflow_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getAutomatedEmailList() method...');
		$response = $this->chimp->get( '/automations/'.$workflow_id.'/emails');
		$log->debug('Exiting ChimpHelper :: getAutomatedEmailList method ...');
		return $response;
	}
	public function getAutomatedEmail($workflow_id, $workflow_email_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getAutomatedEmail() method...');
		$response = $this->chimp->post( '/automations/'.$workflow_id.'/emails'.$workflow_email_id);
		$log->debug('Exiting ChimpHelper :: getAutomatedEmail method ...');
		return $response;
	}
	public function pauseAutomatedEmail($workflow_id,$workflow_email_id){
		global $log;
		$log->debug('Entering ChimpHelper :: pauseAutomatedEmail() method...');
		$response = $this->chimp->post( '/automations/'.$workflow_id.'/emails'.$workflow_email_id.'actions/pause');
		$log->debug('Exiting ChimpHelper :: pauseAutomatedEmail method ...');
		return $response;
	}
	public function startAutomatedEmail($workflow_id,$workflow_email_id){
		global $log;
		$log->debug('Entering ChimpHelper :: startAutomatedEmail() method...');
		$response = $this->chimp->post( '/automations/'.$workflow_id.'/emails/'.$workflow_email_id.'/actions/start');
		$log->debug('Exiting ChimpHelper :: startAutomatedEmail method ...');
		return $response;
	}

	/**Campaigns folder**/
	public function createCampaignFolder($batch){
		global $log;
		$log->debug('Entering ChimpHelper :: createCampaignFolder() method...');
		$response = $this->chimp->post( '/campaign-folders/',$batch);
		$log->debug('Exiting ChimpHelper :: createCampaignFolder method ...');
		return $response;
	}
	public function getCampaignFolder(){
		global $log;
		$log->debug('Entering ChimpHelper :: getCampaignFolder() method...');
		$response = $this->chimp->get( '/campaign-folders/');
		$log->debug('Exiting ChimpHelper :: getCampaignFolder method ...');
		return $response;
	}
	public function getSpecificCampaignFolder($folder_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getSpecificCampaignFolder() method...');
		$response = $this->chimp->get( '/campaign-folders/'.$folder_id);
		$log->debug('Exiting ChimpHelper :: getSpecificCampaignFolder method ...');
		return $response;
	}
	public function updateCampaignFolder($folder_id){
		global $log;
		$log->debug('Entering ChimpHelper :: updateCampaignFolder() method...');
		$response = $this->chimp->patch( '/campaign-folders/'.$folder_id);
		$log->debug('Exiting ChimpHelper :: updateCampaignFolder method ...');
		return $response;
	}
	public function deleteCampaignFolder(){
		global $log;
		$log->debug('Entering ChimpHelper :: deleteCampaignFolder() method...');
		$response = $this->chimp->delete( '/campaign-folders/'.$folder_id);
		$log->debug('Exiting ChimpHelper :: deleteCampaignFolder method ...');
		return $response;
	}
	/**Converations**/
	public function getConversationList(){
		global $log;
		$log->debug('Entering ChimpHelper :: getConversationList() method...');
		$response = $this->chimp->get( '/conversations');
		$log->debug('Exiting ChimpHelper :: getConversationList method ...');
		return $response;
	} 
	public function getConversation($conversation_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getConversation() method...');
		$response = $this->chimp->get( '/conversations/'.$conversation_id);
		$log->debug('Exiting ChimpHelper :: getConversation method ...');
		return $response;
	}
	public function PostConversationMessage($conversation_id,$batch){
		global $log;
		$log->debug('Entering ChimpHelper :: PostConversationMessage() method...');
		$response = $this->chimp->post( '/conversations/'.$conversation_id.'/messages',$batch);
		$log->debug('Exiting ChimpHelper :: PostConversationMessage method ...');
		return $response;
	}
	public function getConversationMessage($conversation_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getConversationMessage() method...');
		$response = $this->chimp->get( '/conversations/'.$conversation_id.'/messages');
		$log->debug('Exiting ChimpHelper :: getConversationMessage method ...');
		return $response;
	}

	public function getSpecificConversationMessage($conversation_id, $message_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getSpecificConversationMessage() method...');
		$response = $this->chimp->get( '/conversations/'.$conversation_id.'/messages/'.$message_id);
		$log->debug('Exiting ChimpHelper :: getSpecificConversationMessage method ...');
		return $response;
	}
	/**E-commerce Stores**/
	public function CreateEcommerceStore($batch){
		global $log;
		$log->debug('Entering ChimpHelper :: CreateEcommerceStore() method...');
		$response = $this->chimp->post( '/ecommerce/stores/',$batch);
		$log->debug('EXiting ChimpHelper :: CreateEcommerceStore method ...');
		return $response;
	}
	public function GetEcommerceStores(){
		global $log;
		$log->debug('Entering ChimpHelper :: GetEcommerceStores() method...');
		$response = $this->chimp->get( '/ecommerce/stores');
		$log->debug('Exiting ChimpHelper :: GetEcommerceStores method ...');
		return $response;
	}
	public function GetSpecificEcommerceStores($store_id){
		global $log;
		$log->debug('Entering ChimpHelper :: GetSpecificEcommerceStores() method...');
		$response = $this->chimp->get( '/ecommerce/stores'.$store_id);
		$log->debug('Exiting ChimpHelper :: GetSpecificEcommerceStores method ...');
		return $response;
	}
	public function UpdateEcommerceStores($store_id){
		global $log;
		$log->debug('Entering ChimpHelper :: UpdateEcommerceStores() method...');
		$response = $this->chimp->patch( '/ecommerce/stores'.$store_id);
		$log->debug('Exiting ChimpHelper :: UpdateEcommerceStores method ...');
		return $response;
	}
	public function deleteEcommerceStores($store_id,$batch){
		global $log;
		$log->debug('Entering ChimpHelper :: deleteEcommerceStores() method...');
		$response = $this->chimp->post( '/ecommerce/stores'.$store_id,$batch);
		$log->debug('Exiting ChimpHelper :: deleteEcommerceStores method ...');
		return $response;
	}
	/**Templates**/
	public function createTemplate($batch){
		global $log;
		$log->debug('Entering ChimpHelper :: createTemplate() method...');
		$response = $this->chimp->post( '/templates',$batch);
		$log->debug('Exiting ChimpHelper :: createTemplate method ...');
		return $response;
	}
	public function getTemplate(){
		global $log;
		$log->debug('Entering ChimpHelper :: getTemplate() method...');
		$response = $this->chimp->get( '/templates');
		$log->debug('Exiting ChimpHelper :: getTemplate method ...');
		return $response;
	}
	public function getSpecificTemplate($template_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getSpecificTemplate() method...');
		$response = $this->chimp->get( '/templates/'.$template_id);
		$log->debug('Exiting ChimpHelper :: getSpecificTemplate method ...');
		return $response;
	}
	public function UpdateTemplate($template_id,$batch){
		global $log;
		$log->debug('Entering ChimpHelper :: UpdateTemplate() method...');
		$response = $this->chimp->patch( '/templates/'.$template_id,$batch);
		$log->debug('Exiting ChimpHelper :: UpdateTemplate method ...');
		return $response;
	}
	public function deleteTemplate($template_id,$batch){
		global $log;
		$log->debug('Entering ChimpHelper :: deleteTemplate() method...');
		$response = $this->chimp->delete( '/templates/'.$template_id,$batch);
		$log->debug('Exiting ChimpHelper :: deleteTemplate method ...');
		return $response;
	}
	public function getTemplateContent($template_id,$batch){
		global $log;
		$log->debug('Entering ChimpHelper :: getTemplateContent() method...');
		$response = $this->chimp->patch( '/templates/'.$template_id.'/default-content',$batch);
		$log->debug('Exiting ChimpHelper :: getTemplateContent method ...');
		return $response;
	}
	/**Template Folder**/
	public function createTemplateFolders($batch){
		global $log;
		$log->debug('Entering ChimpHelper :: createTemplateFolders() method...');
		$response = $this->chimp->post( '/templates-folders',$batch);
		$log->debug('Exiting ChimpHelper :: createTemplateFolders method ...');
		return $response;
	}
	public function getTemplateFolders(){
		global $log;
		$log->debug('Entering ChimpHelper :: getTemplateFolders() method...');
		$response = $this->chimp->get( '/templates-folders');
		$log->debug('Exiting ChimpHelper :: getTemplateFolders method ...');
		return $response;
	}
	public function getSpecificTemplateFolder($folder_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getSpecificTemplateFolder() method...');
		$response = $this->chimp->get( '/templates-folders/'.$folder_id);
		$log->debug('Exiting ChimpHelper :: getSpecificTemplateFolder method ...');
		return $response;
	}
	public function UpdateTemplateFolder($template_id,$batch){
		global $log;
		$log->debug('Entering ChimpHelper :: UpdateTemplateFolder() method...');
		$response = $this->chimp->patch( '/templates-folders/'.$folder_id,$batch);
		$log->debug('Exiting ChimpHelper :: UpdateTemplateFolder method ...');
		return $response;
	}
	public function deleteTemplateFolder($folder_id,$batch){
		global $log;
		$log->debug('Entering ChimpHelper :: deleteTemplateFolder() method...');
		$response = $this->chimp->delete( '/templates-folders/'.$folder_id,$batch);
		$log->debug('Exiting ChimpHelper :: deleteTemplateFolder method ...');
		return $response;
	}
	public function getTemplateDefaultContent($template_id,$batch){
		global $log;
		$log->debug('Entering ChimpHelper :: getTemplateDefaultContent() method...');
		$response = $this->chimp->patch( '/templates/'.$template_id.'/default-content',$batch);
		$log->debug('Exiting ChimpHelper :: getTemplateDefaultContent method ...');
		return $response;
	}

	/**File Manager**/
	public function UploadFile($files){
		global $log;
		$log->debug('Entering ChimpHelper :: UploadFile() method...');
		$response = $this->chimp->post( '/file-manager/'.$files);
		$log->debug('Exiting ChimpHelper :: UploadFile method ...');
		return $response;
	}

	public function getStoredFiles(){
		global $log;
		$log->debug('Entering ChimpHelper :: getStoredFiles() method...');
		$response = $this->chimp->get( '/file-manager/files');
		$log->debug('Exiting ChimpHelper :: getStoredFiles method ...');
		return $response;
	}
	public function getSpecificFile($file_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getSpecificFile() method...');
		$response = $this->chimp->post( '/file-manager/files'.$file_id);
		$log->debug('Exiting ChimpHelper :: getSpecificFile method ...');
		return $response;
	}
	public function updateSpecificFile($file_id){
		global $log;
		$log->debug('Entering ChimpHelper :: updateSpecificFile() method...');
		$response = $this->chimp->patch( '/file-manager/files'.$file_id);
		$log->debug('Exiting ChimpHelper :: updateSpecificFile method ...');
		return $response;
	}
	public function deleteSpecificFile($file_id){
		global $log;
		$log->debug('Entering ChimpHelper :: deleteSpecificFile() method...');
		$response = $this->chimp->delete( '/file-manager/files'.$file_id);
		$log->debug('Exiting ChimpHelper :: deleteSpecificFile method ...');
		return $response;
	}
	/**File Manager Folders**/
	public function createFolder($files){
		global $log;
		$log->debug('Entering ChimpHelper :: createFolder() method...');
		$response = $this->chimp->post( '/file-manager/folders');
		$log->debug('Exiting ChimpHelper :: createFolder method ...');
		return $response;
	}

	public function getFolders(){
		global $log;
		$log->debug('Entering ChimpHelper :: getFolders() method...');
		$response = $this->chimp->get( '/file-manager/folders');
		$log->debug('Exiting ChimpHelper :: getFolders method ...');
		return $response;
	}
	public function getSpecificFolder($folder_id){
		global $log;
		$log->debug('Entering ChimpHelper :: getSpecificFolder() method...');
		$response = $this->chimp->post( '/file-manager/folders'.$folder_id);
		$log->debug('Exiting ChimpHelper :: getSpecificFolder method ...');
		return $response;
	}
	public function updateSpecificFolder($folder_id){
		global $log;
		$log->debug('Entering ChimpHelper :: updateSpecificFolder() method...');
		$response = $this->chimp->patch( '/file-manager/folders'.$folder_id);
		$log->debug('Exiting ChimpHelper :: updateSpecificFolder method ...');
		return $response;
	}
	public function deleteSpecificFolder($folder_id){
		global $log;
		$log->debug('Entering ChimpHelper :: deleteSpecificFolder() method...');
		$response = $this->chimp->delete( '/file-manager/folders'.$folder_id);
		$log->debug('Exiting ChimpHelper :: deleteSpecificFolder method ...');
		return $response;
	}

}
