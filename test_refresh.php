<?php
require_once('C:/xampp/htdocs/wordpress/wp-load.php');
include_once(SM_LB_PRO_DIR.'lib/SmackZohoBiginApi.php');
$api = new SmackZohoBiginApi();
$res = $api->refresh_token();
print_r($res);
