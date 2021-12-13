<?Php
<?php
//Database Connection file. Update with your Database information once you create database from cpanel, or mysql.
	/*define ("DB_HOST", "localhost"); //Databse Host.
	define ("DB_USER", "root"); //Databse User.
	define ("DB_PASS", ""); //database password.
	define ("DB_NAME", "warhtest"); //database Name.*/
	if (isset($_POST['test']) {
		if ($_POST['test'] =='test' {
			$extract($_POST);

			$db = new mysqli($hostname, $username, $dbpass, $dbname);
			if($db->connect_errno > 0){
				fail();
			} else {
				success();
			}
		}
	}
	function success() {
    echo "Connection success.";
    exit;
}

	function fail() {
		echo "Connection fail.";
		exit;
	}
/*$sql="select product_id,product_name from products where supplier_id='".$sid."'";
//$result = $db->query($sql) or die($db->error);
$row=$dbo->prepare($sql);
$row->execute();
$result=$row->fetchAll(PDO::FETCH_ASSOC);

$main = array('data'=>$result);
echo json_encode($main);*/
?>