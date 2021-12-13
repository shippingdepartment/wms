<?php

	global $db; //creating database object.
	
	
	if($db->query('SELECT 1 from options') == FALSE) {
		$query = 'CREATE TABLE IF NOT EXISTS `options` (
		  `option_id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `option_name` varchar(500) NOT NULL,
		  `option_value` varchar(500) NOT NULL,
		  PRIMARY KEY (`option_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		
	} //creating user level table ends.
	
	
	
	