<?php
	
	require_once('../system_load.php');
	//PRocessing of Vendor.
	authenticate_user('all');
	
	
	
	//adding product 
	
	<option  value=''>Choose Supplier</option>
	<?=$supplier->supplier_options($supplier->supplier_id); ?>	
	}//isset add level
	
	
	/*require_once('../system_load.php');
	//PRocessing of Vendor.
	authenticate_user('all');
	
	if(isset($_POST['add_supplier'])) {
		$add_vendor = $_POST['add_supplier'];
		if($add_vendor == '1') { 
			extract($_POST);
			$message = $supplier->add_supplier($supplier_code, $supplier_name, $tax_supplier, $mobile, $phone, $address, $city, $state, $zipcode, $country, $email);
			
			$supplier_options = "<option value=''>Select Vendor</option>";
			$supplier_options .= $supplier->supplier_options($_SESSION['sn_id']);
			 
			 $supplierData = array(
				"message" => $message,
				"supplier_options" => $supplier_options,
				"vendor_id" => $_SESSION['vn_id']
			);
		
			echo json_encode($vendorData);
			exit();
		}
	}//isset add level
	
	
	//adding client 
	
	if(isset($_POST['add_client'])) {
		$add_client = $_POST['add_client'];
		if($add_client == '1') { 
			extract($_POST);
			$message = $client->add_client($full_name, $business_title, $mobile, $phone, $address, $city, $state, $zipcode, $country, $email, $price_level, $notes);
			
			$supplier_options = "<option value=''>Select Client</option>";
			$supplier_options .= $client->client_options($_SESSION['cn_id']);
			 
			 $clientData = array(
				"message" => $message,
				"client_options" => $client_options,
				"client_id" => $_SESSION['cn_id']
			);
		
			echo json_encode($clientData);
			exit();
		
		}
	}//isset add level*/