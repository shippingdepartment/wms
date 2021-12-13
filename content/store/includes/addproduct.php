<?php
	require_once('../system_load.php');
	//PRocessing of Vendor.
	authenticate_user('all');
	$note = new Notes;
	
	
	//adding product 
	
	if(isset($_POST['add_product'])) {
		$add_product = $_POST['add_product'];
		//echo $addwarehouse;
		if($add_product == '1') { 
			extract($_POST);
			$note_title = 'New Product Added';
			$note_details = 'New Product: '.$product_name.' added to this warehouse';			
			$product->add_product($code_product, $product_name, $supplier_id, $unit, $category_id, $tax_id, $cost, $price, $alert_units, $length, $width, $height, $weight);
			$note->add_note($note_title, $note_details );
			//$product->add_dimensions($product_id, $length, $width, $height, $weight );
			 //$productData = array(
			//	"message" => $message
			//);
		}
		header("Location: ../products.php?msg=np"); /* Redirect browser */
exit();
	}//isset add level