<?php
require_once('../../../wp-load.php');
$zohoapi = new SmackZohoBiginApi();
$res = $zohoapi->Zoho_GetRecords('Contacts');
header('Content-Type: application/json');
echo json_encode($res);
