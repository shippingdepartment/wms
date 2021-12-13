<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	
	$order_obj = new Order;
	$warehouse_obj = new Warehouse;
	$note = new Notes;
	
	extract($_POST);
	$warehouse_id=$_POST['source'];
	
	
	$order_id = $order_obj->add_order($date, $deliverydate, $supplier_id, $_SESSION['warehouse_id']);
	
	
	foreach($qty as $index => $qt) {
		$product_id_in = $product_id[$index];
		$quantity = $qt;
		//$cost_in = $cost[$index];
		$warehouse_id_in = $_SESSION['warehouse_id'];
		
		
		$order_detail_id = $order_obj->add_order_detail($order_id, $product_id_in, $quantity);
	} //processing details
		$note_title = 'New Order Created';
		$note_details = 'New Purshasing Order : '.$order_id.' created recently.';
		$note->add_note($note_title, $note_details );
	
	
		HEADER('LOCATION: ../neworder.php?message=Order Saved Successfully !!');	
	