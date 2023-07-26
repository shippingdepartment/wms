<?php
define ("DB_HOST", "php");
define ("DB_USER", "root");
define ("DB_PASS", "admin123");
define ("DB_NAME", "localhost");
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($db->connect_errno > 0){
die("Unable to connect to database [".$db->connect_error."]");
}
?>
