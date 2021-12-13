<?Php
//include('includes/db_connect.php');
$sid=$_GET['sid'];
//$cat_id=2;
/// Preventing injection attack //// 
if(!is_numeric($sid)){
echo "Data Error";
exit;
 }
/// end of checking injection attack ////
require "includes/config.php";


$sql="select product_id,product_name from products where supplier_id='".$sid."'";
//$result = $db->query($sql) or die($db->error);
$row=$dbo->prepare($sql);
$row->execute();
$result=$row->fetchAll(PDO::FETCH_ASSOC);

$main = array('data'=>$result);
echo json_encode($main);
?>