<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	authenticate_user('subscriber');
	
	$transfer_obj = new Transfer;
	$warehouse_obj = new Warehouse;
	
	extract($_POST);
	$source=$_SESSION['warehouse_id'];
	$destination_id=$_POST['bureau'];
	$datetransfer=$_POST['datetransfer'];
	$nb = $_POST['nb'];
	echo $nb;
	//form validation first important part.
	
	
	
	//form processing starts here.
	
	$transfer_id = $transfer_obj->add_transfer($datetransfer, $source, $bureau);
	
	
	//foreach($qty as $index => $qt) {
		for ($k = 0; $k < $nb; $k++) {
			extract($_POST);
		$product_id_in = $pid[$k];
		$quantity = $qty[$k];
		
		//$cost_in = $cost[$index];
		$warehouse_id_in = $wid[$k];
		
		echo "<script>alert($pid[$k])</script>"; 
		//echo $pid[$i];
		echo "<script>alert($qty[$k])</script>"; 
		//echo ' - '.$qty[$i];
		echo "<script>alert($wid[$k])</script>"; 
		//echo ' - '.$wid[$i];
		
		
		$transfer_detail_id = $transfer_obj->add_transfer_detail($transfer_id, $warehouse_id_in, $product_id_in, $quantity);
			$note_title = 'New Transfert Added';
			$note_details = 'New Transfer added and requires to be approved !!';
			$note->add_note($note_title, $note_details );
	} //processing details
	
	if($save == 'Save') { 
		HEADER('LOCATION: ../newtransfer.php?message=Le Transfer est enregistré aves succes.$nb='.$nb.'');
	} else if($print == 'Print'){ 
		HEADER('LOCATION: ../newtransfer.php?message=Le Transfer est enregistré aves succes.&transfer_id='.$transfer_id);	
	}