<?php
define ("DB_HOST", "localhost");
define ("DB_USER", "u195495796_cody");
define ("DB_PASS", "Adminn123*");
define ("DB_NAME", "u195495796_wmsdemo");
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($db->connect_errno > 0){
die("Unable to connect to database [".$db->connect_error."]");
}
?>
