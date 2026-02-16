<?php
require_once('../../../wp-load.php');
$Bigin = new PROFunctions();
$fields = $Bigin->getCrmFields('Deals');
header('Content-Type: application/json');
echo json_encode($fields, JSON_PRETTY_PRINT);
