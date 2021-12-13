<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	
	$transfer_obj = new Transfer;
	$warehouse_obj = new Warehouse;
	$prod = new Product;
	$note = new Notes;
	
	extract($_POST);
	$warehouse_ref=$_POST['source'];
	$store_id=$_POST['bureau'];
	
	//form processing starts here.
	
	$transfer_id = $transfer_obj->add_transfer($date, $_SESSION['warehouse_id'], $bureau);
	
	foreach($qty as $index => $qt) {
		$product_id_in = $product_id[$index];
		$quantity = $qt;
		//$cost_in = $cost[$index];
		$warehouse_id_in = $warehouse_id[$index];
		
		$long= $prod->get_product_dimensions($product_id_in,'long_pr');
		$larg= $prod->get_product_dimensions($product_id_in,'larg');
		$haut= $prod->get_product_dimensions($product_id_in,'haut');
		$poids= $prod->get_product_dimensions($product_id_in,'poids');
		$v=$quantity*$long/100*$larg/100*$haut/100;
		$p=$poids*$quantity;
		
		
		$transfer_detail_id = $transfer_obj->add_transfer_detail($transfer_id, $bureau, $product_id_in, $quantity, $v, $p);
	} //processing details
	
		$note_title = 'New Transfer Created';
		$note_details = 'New Transfert created ';
		$note->add_note($note_title, $note_details );
	
	if($save == 'Save') { 
		HEADER('LOCATION: ../newtransfer.php?message=Transfert saved successfully !!');
	} 