<?php

	global $db; //creating database object.
	$count=0;
	if($db->query('SELECT 1 from clients') == FALSE) { 
		$query = 'CREATE TABLE IF NOT EXISTS `clients` (
		  `client_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `client_code` varchar(50) NOT NULL,
		  `full_name` varchar(200) DEFAULT NULL,
		  `business_title` varchar(200) DEFAULT NULL,
		  `tax` float NOT NULL,
		  `mobile` varchar(200) DEFAULT NULL,
		  `phone` varchar(200) DEFAULT NULL,
		  `address` varchar(200) DEFAULT NULL,
		  `city` varchar(200) DEFAULT NULL,
		  `state` varchar(200) DEFAULT NULL,
		  `zipcode` varchar(200) DEFAULT NULL,
		  `country` varchar(200) DEFAULT NULL,
		  `email` varchar(200) DEFAULT NULL,
		  `price_level` varchar(200) DEFAULT NULL,
		  `notes` varchar(400) DEFAULT NULL,
		  `store_id` bigint(20) DEFAULT NULL,
		  `status` int(11) NOT NULL,
		  PRIMARY KEY (`client_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from deliveries') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `deliveries` (
		  `delivery_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `datetime` datetime DEFAULT NULL,
		  `dateissued` date NOT NULL,
		  `client_id` bigint(20) DEFAULT NULL,
		  `client_order_ref` varchar(50) DEFAULT NULL,
		  `user_id` bigint(10) NOT NULL,
		  `warehouse_id` bigint(10) NOT NULL,
		  `invoiced` int(10) NOT NULL,
		  `delivered` int(11) NOT NULL,
		  PRIMARY KEY (`delivery_id`)
		) ';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from delivery_detail') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `delivery_detail` (
		  `delivery_detail_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `delivery_id` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) NOT NULL,
		  `qty` bigint(20) NOT NULL,
		  `volume` float NOT NULL,
		  `poids` float NOT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  `inventory_id` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`delivery_detail_id`)
		) ';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from dimensions') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `dimensions` (
		  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
		  `product_id` bigint(20) DEFAULT NULL,
		  `long_pr` float NOT NULL,
		  `larg` float DEFAULT NULL,
		  `haut` float DEFAULT NULL,
		  `poids` float NOT NULL,
		  PRIMARY KEY (`ID`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from functions') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `functions` (
		  `function_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `function_code` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		  `function_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		  PRIMARY KEY (`function_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from inventory') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `inventory` (
		  `inventory_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `dateinventory` date NOT NULL,
		  `inn` bigint(20) DEFAULT NULL,
		  `out_inv` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) DEFAULT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  `transfer_id` bigint(20) NOT NULL DEFAULT "0",
		  `delivery_id` bigint(20) NOT NULL,
		  `order_id` int(11) NOT NULL,
		  `lot` varchar(50) NOT NULL,
		  `return_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`inventory_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from loading_approve') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `loading_approve` (
		  `loading_approve_id` int(11) NOT NULL AUTO_INCREMENT,
		  `delivery_id` int(11) NOT NULL,
		  `date_approve` datetime NOT NULL,
		  `warehouse_id` int(11) NOT NULL,
		  `product_id` int(11) NOT NULL,
		  `qty` float NOT NULL,
		  `qty_appr` float NOT NULL,
		  `agent_id` int(11) NOT NULL,
		  PRIMARY KEY (`loading_approve_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from messages') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `messages` (
		  `message_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `message_datetime` datetime DEFAULT NULL,
		  `message_detail` varchar(1000) DEFAULT NULL,
		  PRIMARY KEY (`message_id`)
		) ';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from message_meta') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `message_meta` (
		  `msg_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `message_id` bigint(20) DEFAULT NULL,
		  `status` varchar(100) DEFAULT NULL,
		  `from_id` bigint(20) DEFAULT NULL,
		  `to_id` bigint(20) DEFAULT NULL,
		  `subject_id` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`msg_meta_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from notes') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `notes` (
		  `note_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `note_date` date DEFAULT NULL,
		  `note_title` varchar(200) DEFAULT NULL,
		  `note_detail` varchar(600) DEFAULT NULL,
		  `user_id` bigint(20) DEFAULT NULL,
		  `warehouse_id` int(11) NOT NULL,
		  `readstatus` int(11) NOT NULL,
		  PRIMARY KEY (`note_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from options') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `options` (
		  `option_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `option_name` varchar(500) NOT NULL,
		  `option_value` varchar(500) NOT NULL,
		  PRIMARY KEY (`option_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from orders') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `orders` (
		  `order_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `datetime` datetime DEFAULT NULL,
		  `deliverydate` datetime NOT NULL,
		  `supplier_id` bigint(20) DEFAULT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  `agent_id` bigint(20) DEFAULT NULL,
		  `approved` int(11) NOT NULL,
		  `received` int(11) NOT NULL,
		  PRIMARY KEY (`order_id`),
		  KEY `order_id` (`order_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from order_approved') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `order_approved` (
		  `order_approved_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `order_id` bigint(20) DEFAULT NULL,
		  `date_approve` datetime NOT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) NOT NULL,
		  `qty` bigint(20) NOT NULL,
		  `qty_appr` bigint(20) NOT NULL,
		  `agent_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`order_approved_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from order_detail') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `order_detail` (
		  `order_detail_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `order_id` bigint(20) DEFAULT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  `price_id` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) NOT NULL,
		  `qty` bigint(20) NOT NULL,
		  PRIMARY KEY (`order_detail_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from order_received') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `order_received` (
		  `order_received_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `order_id` bigint(20) DEFAULT NULL,
		  `date_reception` datetime NOT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) NOT NULL,
		  `qty` bigint(20) NOT NULL,
		  `qty_appr` bigint(20) NOT NULL,
		  `agent_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`order_received_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from price') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `price` (
		  `price_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `cost` float DEFAULT NULL,
		  `selling_price` float DEFAULT NULL,
		  `tax` double DEFAULT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) DEFAULT NULL,
		  `type` int(11) NOT NULL,
		  PRIMARY KEY (`price_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
	
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from products') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `products` (
		  `product_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `product_manual_id` varchar(200) DEFAULT NULL,
		  `product_name` varchar(200) DEFAULT NULL,
		  `supplier_id` bigint(20) DEFAULT NULL,
		  `product_unit` varchar(200) DEFAULT NULL,
		  `category_id` bigint(20) DEFAULT NULL,
		  `tax_id` bigint(20) DEFAULT NULL,
		  `alert_units` varchar(100) DEFAULT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`product_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from product_categories') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `product_categories` (
		  `category_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `category_name` varchar(200) DEFAULT NULL,
		  `category_description` varchar(600) DEFAULT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`category_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from product_rates') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `product_rates` (
		  `rate_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `default_rate` float DEFAULT NULL,
		  `level_1` float DEFAULT NULL,
		  `level_2` float DEFAULT NULL,
		  `level_3` float DEFAULT NULL,
		  `level_4` float DEFAULT NULL,
		  `level_5` float DEFAULT NULL,
		  `store_id` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`rate_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
	
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from product_taxes') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `product_taxes` (
		  `tax_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `tax_name` varchar(200) DEFAULT NULL,
		  `tax_rate` varchar(200) DEFAULT NULL,
		  `tax_type` varchar(200) DEFAULT NULL,
		  `tax_description` varchar(600) DEFAULT NULL,
		  `store_id` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`tax_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from returns') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `returns` (
		  `return_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `delivery_id` bigint(20) NOT NULL,
		  `transfer_id` bigint(20) NOT NULL,
		  `return_date` date NOT NULL,
		  `warehouse_id` bigint(20) NOT NULL,
		  `user_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`return_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from return_detail') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `return_detail` (
		  `return_detail_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `return_id` bigint(20) NOT NULL,
		  `product_id` bigint(20) NOT NULL,
		  `qty_ret` float NOT NULL,
		  `ret_reason` varchar(50) NOT NULL,
		  `qty_dmg` float NOT NULL,
		  PRIMARY KEY (`return_detail_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from stores') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `stores` (
		  `store_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `store_manual_id` varchar(100) NOT NULL,
		  `store_name` varchar(100) NOT NULL,
		  `business_type` varchar(100) NOT NULL,
		  `address1` varchar(200) NOT NULL,
		  `address2` varchar(200) NOT NULL,
		  `city` varchar(100) NOT NULL,
		  `state` varchar(100) NOT NULL,
		  `country` varchar(100) NOT NULL,
		  `zip_code` varchar(100) NOT NULL,
		  `phone` varchar(200) NOT NULL,
		  `email` varchar(200) NOT NULL,
		  `currency` varchar(50) NOT NULL,
		  `store_logo` varchar(500) NOT NULL,
		  `description` varchar(600) NOT NULL,
		  `user_id` varchar(100) NOT NULL,
		  PRIMARY KEY (`store_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from suppliers') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `suppliers` (
		  `supplier_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `supplier_code` varchar(50) NOT NULL,
		  `full_name` varchar(200) DEFAULT NULL,
		  `business_title` varchar(200) DEFAULT NULL,
		  `mobile` varchar(200) DEFAULT NULL,
		  `phone` varchar(200) DEFAULT NULL,
		  `address` varchar(200) DEFAULT NULL,
		  `city` varchar(200) DEFAULT NULL,
		  `state` varchar(200) DEFAULT NULL,
		  `zipcode` varchar(200) DEFAULT NULL,
		  `country` varchar(200) DEFAULT NULL,
		  `email` varchar(200) DEFAULT NULL,
		  `status` int(11) NOT NULL,
		  PRIMARY KEY (`supplier_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from transfers') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `transfers` (
		  `transfer_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `datetime` datetime DEFAULT NULL,
		  `warehouse_id` bigint(20) DEFAULT NULL,
		  `destination_id` bigint(20) DEFAULT NULL,
		  `agent_id` bigint(20) DEFAULT NULL,
		  `approved` int(11) NOT NULL,
		  `received` int(10) NOT NULL,
		  PRIMARY KEY (`transfer_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from transfer_approved') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `transfer_approved` (
		  `transfer_approved_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `transfer_id` bigint(20) DEFAULT NULL,
		  `date_approve` datetime NOT NULL,
		  `bureau_id` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) NOT NULL,
		  `qty` bigint(20) NOT NULL,
		  `qty_appr` bigint(20) NOT NULL,
		  `agent_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`transfer_approved_id`)
		 )';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from transfer_detail') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `transfer_detail` (
		  `transfer_detail_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `transfer_id` bigint(20) DEFAULT NULL,
		  `bureau_id` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) NOT NULL,
		  `qty` bigint(20) NOT NULL,
		  `volume` float NOT NULL,
		  `poids` float NOT NULL,
		  PRIMARY KEY (`transfer_detail_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
	
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from transfer_received') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `transfer_received` (
		  `transfer_received_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `transfer_id` bigint(20) DEFAULT NULL,
		  `date_reception` datetime NOT NULL,
		  `bureau_id` bigint(20) DEFAULT NULL,
		  `product_id` bigint(20) NOT NULL,
		  `qty` bigint(20) NOT NULL,
		  `qty_appr` bigint(20) NOT NULL,
		  `agent_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`transfer_received_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
	
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from users') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `users` (
		  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `first_name` varchar(100) NOT NULL,
		  `last_name` varchar(100) DEFAULT NULL,
		  `gender` varchar(50) DEFAULT NULL,
		  `date_of_birth` date DEFAULT NULL,
		  `address` varchar(200) DEFAULT NULL,
		  `mobile` varchar(200) DEFAULT NULL,
		  `phone` varchar(200) DEFAULT NULL,
		  `username` varchar(100) NOT NULL,
		  `email` varchar(200) NOT NULL,
		  `password` varchar(200) NOT NULL,
		  `status` varchar(100) NOT NULL,
		  `activation_key` varchar(100) DEFAULT NULL,
		  `date_register` date NOT NULL,
		  `user_type` varchar(100) NOT NULL,
		  `user_function` varchar(100) NOT NULL,
		  PRIMARY KEY (`user_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from user_level') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `user_level` (
		  `level_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `level_name` varchar(200) NOT NULL,
		  `level_description` varchar(600) NOT NULL,
		  `level_page` varchar(100) NOT NULL,
		  PRIMARY KEY (`level_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	} //creating user level table ends.
	
	//if database tables does not exist already create them.
	if($db->query('SELECT 1 from user_meta') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `user_meta` (
		  `user_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `user_id` bigint(20) NOT NULL,
		  `message_email` varchar(50) NOT NULL,
		  `last_login_time` datetime NOT NULL,
		  `last_login_ip` varchar(120) NOT NULL,
		  `login_attempt` bigint(20) NOT NULL,
		  `login_lock` varchar(50) NOT NULL,
		  PRIMARY KEY (`user_meta_id`)
		) ';
		$result = $db->query($query) or die($db->error);	
		$count +=1;
		
	} //creating Journal Voucher table ends.
	
	if($db->query('SELECT 1 from warehouses') == FALSE) { 
		$query = 'CREATE TABLE IF NOT EXISTS `warehouses` (
		  `warehouse_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `name` varchar(200) NOT NULL,
		  `address` varchar(200) NOT NULL,
		  `city` varchar(200) NOT NULL,
		  `state` varchar(200) NOT NULL,
		  `country` varchar(200) NOT NULL,
		  `manager` varchar(200) NOT NULL,
		  `contact` varchar(200) NOT NULL,
		  `surface` float NOT NULL,
		  `volume` float NOT NULL,
		  `freezone` float NOT NULL,
		  `disabled` int(11) NOT NULL DEFAULT "0",
		  PRIMARY KEY (`warehouse_id`)
		) ';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	}  //Creating Projects logs table ends here.
	
	if($db->query('SELECT 1 from warehouse_access') == FALSE) { 
		$query = 'CREATE TABLE IF NOT EXISTS `warehouse_access` (
			`access_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) DEFAULT NULL,
			`warehouse_id` bigint(20) DEFAULT NULL,
			`clients` bigint(20) DEFAULT NULL,
			`products` bigint(20) DEFAULT NULL,
			`reports` bigint(20) DEFAULT NULL,
			`transfers` int(20) DEFAULT NULL,
			`orders` bigint(20) NOT NULL,
			`deliveries` bigint(20) NOT NULL,
			`suppliers` int(11) NOT NULL,
			`stock` int(11) NOT NULL,
			`receptions` int(11) NOT NULL,
			`returns` int(11) NOT NULL,
			PRIMARY KEY (`access_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		$count +=1;
		
	}  //Creating users table ends here.
		/*echo 'Installation Finished...<br>';
		echo '=========================================================================<br>';
		echo 'Tables Created : '.($count+1).'/33<br>';
		echo '=========================================================================<br>';*/
	
	