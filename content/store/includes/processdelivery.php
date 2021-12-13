<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	
	$delivery = new Delivery;
	$warehouse_obj = new Warehouse;
	$prod = new Product;
	$note = new Notes;
	
	extract($_POST);
	$warehouse_id=$_POST['source'];
	$store_id=$_POST['bureau'];
	//form validation first important part.
	
	
	//form processing starts here.
	//$date1 = strtotime($date);
	$date1= date('Y-m-d');
	$date2 = strtotime($deliverydate);
	$date2= date('Y-m-d', $date2);
	$delivery_id = $delivery->add_delivery($date1, $date2, $_SESSION['warehouse_id'], $bureau, $clientorder);
	
	foreach($qty as $index => $qt) {
		$product_id_in = $product_id[$index];
		$quantity = $qt;
		//$cost_in = $cost[$index];
		//$warehouse_id_in = $warehouse_id[$index];
		
		$long= $prod->get_product_dimensions($product_id_in,'long_pr');
		$larg= $prod->get_product_dimensions($product_id_in,'larg');
		$haut= $prod->get_product_dimensions($product_id_in,'haut');
		$poids= $prod->get_product_dimensions($product_id_in,'poids');
		$v=$quantity*$long/100*$larg/100*$haut/100;
		$p=$poids*$quantity;
		
		
		$delivery_detail_id = $delivery->add_delivery_detail($delivery_id, $bureau, $product_id_in, $quantity, $v, $p);
	} //processing details
	
		$note_title = 'New Delivery Created';
		$note_details = 'New Delivery: '.$delivery_id.' created recently.';
		$note->add_note($note_title, $note_details );
	HEADER('LOCATION: ../newdelivery.php?message=Delivery created successfully !!');
	