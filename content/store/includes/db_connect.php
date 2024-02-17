<?php

$env = parse_ini_file("../../.env");

foreach ($env as $key => $value) {
    define($key, $value);
}

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_errno > 0) {
    die("Unable to connect to database [" . $db->connect_error . "]");
}
