<?php

	header('Content-Type: application/json');
//messages processings
	require_once('../system_load.php');
	//loading system.
	authenticate_user('all');
	
	$product = new Product;
	$warehouse = new Warehouse;	
	/*retriving Data From Database.*/
    $data_points = array();
    //$items = $product->num_products($_SESSION['warehouse_id']);
	$volume_warh = $warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'volume');
	$occuped_volume = $warehouse->occuped_volume($_SESSION['warehouse_id']);
	$security_zone = $warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'freezone');
	$free_volume = floatval($warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'volume')) - ($occuped_volume + $security_zone);
	
    //$result = mysqli_query($db, "SELECT * FROM sales");
    
         
        $point = array("label" => $_SESSION['warehouse_id'] , "y" => $volume_warh);
        
        array_push($data_points, $point);        
   
    
    echo json_encode($data_points, JSON_NUMERIC_CHECK);



?>