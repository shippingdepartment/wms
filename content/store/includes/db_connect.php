<?php
define("DB_HOST", "localhost");
define("DB_USER", "virtual6_wms");
define("DB_PASS", "virtual6_wms");
define("DB_NAME", "virtual6_wms");
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_errno > 0) {
    die("Unable to connect to databasee [" . $db->connect_error . "]");
}
?>