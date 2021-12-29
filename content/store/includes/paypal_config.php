<?php
/* 
 * PayPal and database configuration 
 */

// PayPal configuration 
define('PAYPAL_ID', 'sb-0aqt32790362@business.example.com');
define('PAYPAL_SANDBOX', TRUE); //TRUE or FALSE 

define('PAYPAL_RETURN_URL', 'http://www.example.com/success.php');
define('PAYPAL_CANCEL_URL', 'http://www.example.com/cancel.php');
define('PAYPAL_NOTIFY_URL', 'http://www.example.com/ipn.php');
define('PAYPAL_CURRENCY', 'USD');


// Change not required 
define('PAYPAL_URL', (PAYPAL_SANDBOX == true) ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr");
