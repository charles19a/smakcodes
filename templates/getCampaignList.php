<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

require_once('ChimpHelper.php');
require_once('MailChimp.php');

$mc_api_key = sanitize_text_field($_REQUEST['mc_apikey']);
update_option('mc_apikey' , $mc_api_key);

$camp_obj = new ChimpHelper($mc_api_key);
$campaign_list = $camp_obj->getChimpCampaigns($start=1, $limit=100, '', $campaignId = false);

foreach($campaign_list['campaigns'] as $camp_key => $camp_val )
{
	$get_campaign_list[$camp_val['id']] = $camp_val['settings']['title'];
}
update_option('mc_campaign_list' , $get_campaign_list);
die;

?>
