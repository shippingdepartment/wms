<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	
	$note = new Notes;
	$warehouse = new Warehouse;
	
	if (isset($_GET['noteid']) AND $_GET['noteid']!='') {
		$note->noteread($_GET['noteid']);
		header("Location: {$_SERVER['HTTP_REFERER']}");
	}
	
	
	
	