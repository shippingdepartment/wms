<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	
	extract($_POST);
	

	
	
	if( isset($product)) { 
		$long = $products->get_product_dimensions($product, 'long_pr');
		$larg = $products->get_product_dimensions($product, 'larg');
		$haut = $products->get_product_dimensions($product, 'haut');
		$poids = $products->get_product_dimensions($product, 'poids');
		
		
		
		$dimensions_data = array(
						"long" => $long,
						"larg" => $larg,
						"haut" => $haut,
						"poids" => $poids
					);
		echo json_encode($dimensions_data);
		exit();
	}
	
	