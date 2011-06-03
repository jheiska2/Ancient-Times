<?php
// File: CCaptcha.php
// Description: Class CCaptcha
//
// A interface to hide specific implementations of CAPTCHA services. The pagecontroller can
// create a object of this class to use CAPTCHA-services. The actual implementation of the
// CAPTCHA-service is hidden behind this class. This makes it easier to change and extend the
// support of more CAPTHCHA services.
//
// Currently supporting:
// http://recaptcha.net/ through their recaptcha-php library.
class CCaptcha {

// Internal variables
public $iErrorMessage;

// Constructor
public function __construct() {
$this->iErrorMessage = "";
}

// Destructor
public function __destruct() { ; }

// Get HTML to display the CAPTCHA
public function GetHTMLToDisplay() {
$this->iErrorMessage = "";

require_once(TP_SOURCEPATH . '/recaptcha-php/recaptchalib.php');
$publickey = reCAPTCHA_PUBLIC; // you got this from the signup page
return recaptcha_get_html($publickey);
}

// Validate the answer
public function CheckAnswer() {
$this->iErrorMessage = "";

require_once(TP_SOURCEPATH . '/recaptcha-php/recaptchalib.php');
$privatekey = reCAPTCHA_PRIVATE;
$resp = recaptcha_check_answer ($privatekey,
$_SERVER["REMOTE_ADDR"],
$_POST["recaptcha_challenge_field"],
$_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
$this->iErrorMessage = "The reCAPTCHA wasn't entered correctly. Go back and try it again." .
"(reCAPTCHA said: " . $resp->error . ")";
return FALSE;
}
return TRUE;
}
} // End of Of Class
?>