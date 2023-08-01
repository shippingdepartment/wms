<?php
$dbhost_name = "localhost";
$database = "php";
$username = "root";
$password = "123";
// $database = "virtual6_wms";
// $username = "virtual6_wms";
// $password = "virtual6_wms";
try {
$dbo = new PDO("mysql:host=".$dbhost_name.";dbname=".$database, $username, $password);
} catch (PDOException $e) {
print "Error!: " . $e->getMessage() . "<br/>";
die();
}
?>
