<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	authenticate_user('all');
	
	$product = new Product;
	$warehouse = new Warehouse;	
	/*retriving Data From Database.*/
	
	$product_id = $_POST['product_id'];
	$warehouse_id = $_POST['warehouse_id'];
	
	$product_name = $product->get_product_info($product_id, 'product_name');
	$product_manual_id = $product->get_product_info($product_id, 'product_manual_id');
	
	$warehouse_name = $warehouse->get_warehouse_info($warehouse_id, 'name');
		
		$purchaseData = array(
					"product_name" => $product_name,
					"product_manual_id" => $product_manual_id,
					"warehouse_name" => $warehouse_name
					);
	echo json_encode($purchaseData);