<?php
	require_once('../system_load.php');
	//PRocessing of Vendor.
	authenticate_user('all');
	
	if(isset($_POST['add_vendor'])) {
		$add_vendor = $_POST['add_vendor'];
		if($add_vendor == '1') { 
			extract($_POST);
			$message = $vendor->add_vendor($full_name, $contact_person, $mobile, $phone, $address, $city, $state, $zipcode, $country, $provider_of);
			
			$vendor_options = "<option value=''>Select Vendor</option>";
			$vendor_options .= $vendor->vendor_options($_SESSION['vn_id']);
			 
			 $vendorData = array(
				"message" => $message,
				"vendor_options" => $vendor_options,
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
			
			$client_options = "<option value=''>Select Client</option>";
			$client_options .= $client->client_options($_SESSION['cn_id']);
			 
			 $clientData = array(
				"message" => $message,
				"client_options" => $client_options,
				"client_id" => $_SESSION['cn_id']
			);
		
			echo json_encode($clientData);
			exit();
		
		}
	}//isset add level