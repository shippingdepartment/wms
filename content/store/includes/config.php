<?php
$dbhost_name = "localhost";
$database = "php";
$username = "root";
$password = "admin123";
try {
$dbo = new PDO("mysql:host=".$dbhost_name.";dbname=".$database, $username, $password);
} catch (PDOException $e) {
print "Error!: " . $e->getMessage() . "<br/>";
die();
}
?>
