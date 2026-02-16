<?php
require_once('../../../wp-load.php');
$zohoapi = new SmackZohoBiginApi();
$activated_plugin = get_option("WpLeadBuilderProActivatedPlugin");
$zohoconfig = get_option("wp_{$activated_plugin}_settings");
$token = isset($zohoconfig['access_token']) ? $zohoconfig['access_token'] : '';

// Get a contact ID
$contacts = $zohoapi->Zoho_GetRecords('Contacts');
$contact_id = $contacts['data'][0]['id'];

$module = 'Deals';
$payload = [
    "data" => [
        [
            "Deal_Name" => "Test Deal " . time(),
            "Pipeline" => "Sales Pipeline Standard",
            "Stage" => "Qualification",
            "Amount" => 1000,
            "Closing_Date" => date('Y-m-d', strtotime('+7 days')),
            "Contact_Name" => (int)$contact_id,
            "Layout" => "1176663000000000173"
        ]
    ]
];

$res = $zohoapi->Zoho_CreateRecord($module, $payload);
header('Content-Type: application/json');
echo json_encode($res, JSON_PRETTY_PRINT);
