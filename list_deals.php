<?php
require_once('../../../wp-load.php');
$zohoapi = new SmackZohoBiginApi();
$activated_plugin = get_option("WpLeadBuilderProActivatedPlugin");
$zohoconfig = get_option("wp_{$activated_plugin}_settings");
$token = isset($zohoconfig['access_token']) ? $zohoconfig['access_token'] : '';
$url = $zohoapi->zohoapidomain . "/bigin/v1/Deals";

$args = array(
    'sslverify' => false,
    'headers' => array(
        'Authorization' => 'Zoho-oauthtoken ' . $token
    )
);
$response = wp_remote_get($url, $args);
header('Content-Type: application/json');
echo wp_remote_retrieve_body($response);
