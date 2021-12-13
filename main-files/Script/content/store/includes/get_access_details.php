<?php
	//messages processings
	require_once('../system_load.php');
	
	//authenticate_user('all');
	
	//$product = new Product;
	//$warehouse = new Warehouse;	
	
	
	$user_id = $_POST['user_id'];
	$store_id = $_POST['store_id'];
	
	$query = "SELECT * from warehouse_access WHERE user_id='".$user_id."' AND warehouse_id='".$store_id."'";
	$result = $db->query($query) or die($db->error);
	$row = $result->fetch_array();
	
	$product_access = $row['products'];
	$stock_access = $row['stock'];
	$order_access = $row['orders'];
	$return_access = $row['returns'];
	$delivery_access = $row['deliveries'];
	$client_access = $row['clients'];
	$supplier_access = $row['suppliers'];
	$transfer_access = $row['transfers'];
	$reception_access = $row['receptions'];
	$reports_access = $row['reports'];
	
	
		$purchaseData = array(
					"product_access" => $product_access,
					"stock_access" => $stock_access,
					"order_access" => $order_access,
					"return_access" => $return_access,
					"delivery_access" => $delivery_access,
					"client_access" => $client_access,
					"supplier_access" => $supplier_access,
					"transfer_access" => $transfer_access,
					"reception_access" => $reception_access,
					"reports_access" => $reports_access
					
					);
	echo json_encode($purchaseData);