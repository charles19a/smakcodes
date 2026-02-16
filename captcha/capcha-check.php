<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

require_once('recaptchalib.php');
$publickey = "6Ldi69ESAAAAAIijH1t2um6ULYt0HTAFbN9nMA9T";
$privatekey = "6Ldi69ESAAAAAPZ6H0lWtwmnxdII9t6iDw4Vykve";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;
$resp = recaptcha_check_answer ($privatekey,
        "127.0.0.1",
        sanitize_text_field($_POST["recaptcha_challenge_field"]),
        sanitize_text_field($_POST["recaptcha_response_field"]));

if (!$resp->is_valid) {
  // What happens when the CAPTCHA was entered incorrectly
	echo "captcha failed";
} else {
  // Your code here to handle a successful verification
	echo "captha success";
}
?>
