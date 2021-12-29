<?php
/*This file have all functions to handle options.
	1) Set Option
	2) Get Option
	3) Install Admin
	4) Authentication
	*/
function script_version()
{
	return get_option('version');
}

function send_email($message, $mailto, $subject)
{
	//getting set email addresses from database.
	$from_email = get_option('email_from');
	$reply_to = get_option('email_to');

	$mailheaders = "From:" . $from_email;
	$mailheaders .= "Reply-To:" . $reply_to;
	$from = $from_email;

	$headers = "FROM: " . $from;
	$semi_rand = md5(time());
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

	$headers .= "\nMIME-Version: 1.0\n" .
		"Content-Type: multipart/mixed;\n" .
		" boundary=\"{$mime_boundary}\"";

	$message .= "This is a multi-part message in MIME format.\n\n" .
		"--{$mime_boundary}\n" .
		"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
		"Content-Transfer-Encoding: 7bit\n\n" .
		$message . "\n\n";
	$message .= "--{$mime_boundary}\n" .
		"Content-Type: {$fileatt_type};\n" .
		" name=\"{$filename}\"\n" .
		"Content-Transfer-Encoding: base64\n\n" .
		mail($mailto, $subject, $message, $headers);
}

function project_log($store_id, $store_log)
{
	global $db;
	$query = "INSERT into store_logs VALUES(NULL, '" . date("Y-m-d H:i:s") . "', '" . $store_log . "', '" . $store_id . "', '" . $_SESSION['user_id'] . "')";
	$result = $db->query($query) or die($db->error);
}

function set_option($option_name, $option_value)
{
	global $db;
	$query = "SELECT * from options WHERE option_name='" . $option_name . "'";
	$result = $db->query($query) or die($db->error);
	$num_rows = $result->num_rows;

	if ($num_rows > 0) {
		$query = "DELETE from options WHERE option_name='" . $option_name . "'";
		$result = $db->query($query) or die($db->error);
	} //This will delete record
	$query = "INSERT into options VALUES(NULL, '" . $option_name . "', '" . $option_value . "')";
	$result = $db->query($query) or die($db->error);
	//this function do not return anything!
} //set option function ends here.

function get_option($option_name)
{
	global $db;
	$query = "SELECT * from options WHERE option_name='" . $option_name . "'";
	$result = $db->query($query) or die($db->error);
	if ($row = $result->fetch_array()) {
		$option_value = stripslashes($row['option_value']); //this will remove database slashes from values
	} else {
		$option_value = '';
	}
	return $option_value; //This function returns option value.
} //get option value function ends here.

function install_admin($first_name, $last_name, $email, $password)
{
	global $db;
	$password = md5($password);
	//check if admin already exist.
	$query = "SELECT * from users WHERE user_type='admin'";
	$result = $db->query($query) or die($db->error);
	$num_rows = $result->num_rows;

	if ($num_rows > 0) {
		echo 'Admin already exist cannot be addedd!';
	} else {
		//adding admin
		$query = "INSERT into users (user_id, first_name, last_name, email, password, status, date_register, user_type)
					VALUES(NULL, '" . $first_name . "', '" . $last_name . "', '" . $email . "', '" . $password . "', 'activate', '" . date('Y-m-d') . "', 'admin')";
		$result = $db->query($query) or die($db->error);
	}

	//adding deafult user level subscriber.
	$query = "SELECT * from user_level WHERE level_name='subscriber'";
	$result = $db->query($query) or die($db->error);
	$num_rows = $result->num_rows;

	if ($num_rows > 0) {
		//do nothing already subscriber level
	} else {
		$query = "INSERT into user_level VALUES(NULL, 'subscriber', 'Default user level given access to profile.php', 'profile.php')";
		$result = $db->query($query) or die($db->error);
	}
} //Function checkes if admin does not exist this will create admin.

function insert_warehouse($name_warh, $address, $country, $area, $volume, $freezone)
{
	global $db;
	//adding warehouse
	$query = "INSERT into warehouses (warehouse_id, name, address, country, surface, volume, freezone)
					VALUES(NULL, '" . $name_warh . "', '" . $address . "', '" . $country . "', '" . $area . "', '" . $volume . "', '" . $freezone . "')";
	$result = $db->query($query) or die($db->error);
}



function redirect_user($user_type)
{
	global $db;
	$impotant = new ImportantFunctions();
	if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin') {
		HEADER('LOCATION: dashboard.php');
	} else if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == $user_type) {
		$_SESSION['order_source_id'] = $impotant->getOrderSourceIdForCurrentOwner();
		HEADER('LOCATION:store_owner_dashboard.php');
	} else {
		HEADER('LOCATION:warehouse.php');
	}
}
function authenticate_user($access_level)
{
	global $db;
	if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != '') {
		//check user level
		if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin') {
			//admin can access all pages.
		} else if ($access_level == 'all') {
			//all user types can access here but only when signed in.
		} else {
			if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == $access_level) {
				//You can access this page now.
			} else {
				$query = "SELECT * from user_level WHERE level_name='" . $_SESSION['user_type'] . "'";
				$result = $db->query($query) or die($db->error);
				$num_rows = $result->num_rows;

				if ($num_rows > 0) {
					$row = $result->fetch_array();
					$page = $row['level_page'];
					HEADER('LOCATION:' . $page);
				} else {
					//If you are not admin and not given access user. You will be redirected to index.php
					HEADER('LOCATION: index.php');
				}
			} //if user level is accessable.
		}
	} else {
		HEADER('LOCATION: index.php');
	} //this is loged in user.
} //authenticate user ends here.

function partial_access($access_type)
{
	if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != '') {
		if ($access_type == 'admin' && $_SESSION['user_type'] == 'admin') {
			return TRUE;
		} else if ($access_type == 'all') {
			return TRUE;
		} else if ($access_type == $_SESSION['user_type']) {
			return TRUE;
		} else {
			return FALSE;
		}
	} else {
		return FALSE;
	}
} //partial access function ends here.
function currency_format($number, $fractional = false)
{
	if ($fractional) {
		$number = sprintf('%.2f', $number);
	}
	while (true) {
		$replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
		if ($replaced != $number) {
			$number = $replaced;
		} else {
			break;
		}
	}
	return $number;
}

function randomPassword()
{
	$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	$pass = array();
	$alphaLength = strlen($alphabet) - 1;
	for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass);
}

function get_client_ip()
{
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP']))
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_X_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if (isset($_SERVER['REMOTE_ADDR']))
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}

function time_elapsed_string($datetime, $full = false)
{
	$now = new DateTime;
	$ago = new DateTime($datetime);
	$diff = $now->diff($ago);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = array(
		'y' => 'year',
		'm' => 'month',
		'w' => 'week',
		'd' => 'day',
		'h' => 'hour',
		'i' => 'minute',
		's' => 'second',
	);
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}
