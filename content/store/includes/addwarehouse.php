<?php
	require_once('../system_load.php');
	
	authenticate_user('all');
	//adding Warehouse
	
	if(isset($_POST['add_warehouse'])) {
		$add_warehouse = $_POST['add_warehouse'];
		echo $addwarehouse;
		if($add_warehouse == '1') { 
			extract($_POST);
			$message = $warehouses->add_warehouse($name_warh, $address, $city, $state, $country, $manager_name, $manager_phone, $area, $volume, $freezone);
			 
			 $warehouseData = array(
				"message" => $message
			);
		
		}
		header("Location: ../warehouses.php?msg=nw"); /* Redirect browser */
exit();
	}//isset add level