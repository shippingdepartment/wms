<?php
	require_once('../system_load.php');
	
	authenticate_user('all');
	
	$note = new Notes;
	
	if(isset($_POST['add_supplier'])) {
		if($_POST['add_supplier'] == '1') { 
			extract($_POST);
			$message = $supplier->add_supplier($code_supplier, $supplier_name, $tax_supplier, $mobile, $phone, $address, $city, $state, $zipcode, $country, $email, $status);
			
			$note_title = 'New Supplier Added';
			$note_details = 'New Supplier: '.$supplier_name.' added to this warehouse';
			$note->add_note($note_title, $note_details );
			 
			 $warehouseData = array(
				"message" => $message
			);
		}
		header("Location: ../neworder.php"); /* Redirect browser */
		exit();
	}
	
	?>