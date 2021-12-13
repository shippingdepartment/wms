<?php
//store Class

class Store {
	public $store_manual_id;
	public $store_name;
	public $business_type;
	public $address1;
	public $address2;
	public $city;
	public $state;
	public $country;
	public $zip_code;
	public $phone;
	public $email;
	public $currency;
	public $store_logo;
	public $description;
	
	function get_store_info($store_id, $term) { 
		global $db;
		$query = "SELECT * from stores WHERE store_id='".$store_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.	
	
	function delete_store($store_id) {
		global $db; 
		
		$delete[] = "DELETE from clients WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from creditors WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from customer_log WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from debts WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from inventory WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from payments WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from price WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from products WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from product_categories WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from product_rates WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from product_taxes WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from purchases WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from purchase_detail WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from purchase_returns WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from purchase_return_detail WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from purchase_return_receiving WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from receivings WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from return_reasons WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from sales WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from sale_detail WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from sale_returns WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from sale_return_detail WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from sale_return_payment WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from stores WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from store_access WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from store_logs WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from vendors WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from vendor_log WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from warehouses WHERE store_id='".$store_id."'";
		$delete[] = "DELETE from warehouse_log WHERE store_id='".$store_id."'";
		
		foreach($delete as $query) { 
			$result = $db->query($query) or die($db->error);
		}
		return 'Store deleted successfuly!.';
	}//delete Account function ends here.
	
	function store_name($store_id) { 
		global $db;
		$query = 'SELECT * from stores WHERE store_id="'.$store_id.'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row['store_name'];
	}//store_info ends here.
		
	function set_store($store_id) { 
		global $db;
		if($_SESSION['user_type'] != 'admin') {
			$query_access = "SELECT * from store_access WHERE user_id='".$_SESSION['user_id']."' AND store_id='".$store_id."'";
			$result_access = $db->query($query_access) or die($db->error);
			$row_num = $result_access->num_rows;
			if($row_num < 0) { 
			echo 'You have no access to this store.';
			exit();
			}
		}
		$query = "SELECT * from stores WHERE store_id='".$store_id."'"; 
		$result = $db->query($query) or die($db->error);
		if($result->num_rows > 0) {
			$row = $result->fetch_array();
			extract($row);	
			$this->store_manual_id = $store_manual_id;
			$this->store_name = $store_name;
			$this->business_type = $business_type;
			$this->address1 = $address1;
			$this->address2 = $address2;
			$this->city = $city;
			$this->state = $state;
			$this->country = $country;
			$this->zip_code = $zip_code;
			$this->phone = $phone;
			$this->email = $email;
			$this->currency = $currency;
			$this->store_logo = $store_logo;
			$this->description = $description;
		} else { 
			echo 'This store does not exist or You cant access this store.';
		}
		
	}//level set ends here.
	
	function update_store($store_id, $store_manual_id, $store_name, $business_type, $address1, $address2, $city, $state, $country, $zip_code, $phone, $email, $currency, $store_logo, $description) {
		global $db;
		if($_SESSION['user_type'] != 'admin') {
			exit();
		}//checks admin user.
		$query = 'UPDATE stores SET
			store_manual_id="'.$store_manual_id.'",
			store_name="'.$store_name.'",
			business_type="'.$business_type.'",
			address1="'.$address1.'",
			address2="'.$address2.'",
			city="'.$city.'",
			state="'.$state.'",
			country="'.$country.'",
			zip_code="'.$zip_code.'",
			phone="'.$phone.'",
			email="'.$email.'",
			currency="'.$currency.'",
			store_logo="'.$store_logo.'",
			description="'.$description.'"
			WHERE store_id='.$store_id.'
			';	
		$result = $db->query($query) or die($db->error);
		return 'store was updated successfuly!';
		}//update_store function ends here.
	
	function add_store($store_manual_id, $store_name, $business_type, $address1, $address2, $city, $state, $country, $zip_code, $phone, $email, $currency, $store_logo, $description) { 
		global $db;
		//check manual id if already exist.
		$query = "SELECT * from stores WHERE store_manual_id='".$store_manual_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) { 
			return 'Please chose different manual unique id. The id '.$store_manual_id.' already exists.';
			exit();
		} else { 
			$query = 'INSERT into stores
			(store_id, store_manual_id, store_name, business_type, address1, address2, city, state, country, zip_code, phone, email, currency, store_logo, description, user_id) 
			VALUES(NULL, "'.$store_manual_id.'", "'.$store_name.'", "'.$business_type.'", "'.$address1.'", "'.$address2.'", "'.$city.'", "'.$state.'", "'.$country.'", "'.$zip_code.'", "'.$phone.'", "'.$email.'", "'.$currency.'", "'.$store_logo.'", "'.$description.'", "'.$_SESSION['user_id'].'")';
			$result = $db->query($query) or die($db->error);
			return 'store added successfuly.';
		}
	}//add_store ends here.
	
	function list_stores() {
			global $db;
			if($_SESSION['user_type'] == 'admin') { 
				$query = 'SELECT * from stores ORDER by store_name ASC';
				$result = $db->query($query) or die($db->error);
			$content = '';
			$count = 0;
			while($row = $result->fetch_array()) { 
				extract($row);
				$count++;
				if($count%2 == 0) { 
					$class = 'even';
				} else { 
					$class = 'odd';
				}
				if($store_logo != '') { 
					$store_logo = '<img src="'.$store_logo.'" height="30" width="30" />';
				}
				$content .= '<tr class="'.$class.'">';
				$content .= '<td>';
				$content .= $store_manual_id;
				$content .= '</td><td>';
				$content .= $store_name;
				$content .= '</td><td>';
				$content .= $business_type;
				$content .= '</td><td>';
				$content .= $city;
				$content .= '</td><td>';
				$content .= $phone;
				$content .= '</td><td>';
				$content .= $email;
				$content .= '</td><td>';
				$content .= $currency;
				$content .= '</td><td>';
				$content .= $store_logo;
				$content .= '</td><td>';
				$content .= '<form method="post" name="view_stores" action="store.php">';
				$content .= '<input type="hidden" name="store_id" value="'.$store_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Select Store">';
				$content .= '</form>';
				$content .= '</td>';
				if(partial_access('admin')) { $content .= '<td><form method="post" name="edit" action="manage_store.php">';
				$content .= '<input type="hidden" name="edit_store" value="'.$store_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Edit">';
				$content .= '</form>';
				$content .= '</td><td>';
				$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_store" value="'.$store_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Delete">';
				$content .= '</form>';
				$content .= '</td>'; }
				$content .= '</tr>'; 
				unset($class);
			}//loop ends here.
			} else { 
				$content = '';
				$query = 'SELECT * from stores ORDER by store_name ASC';
				$result = $db->query($query) or die($db->error);
				$count = 0;
			while($row = $result->fetch_array()) { 
				$query = "SELECT * from store_access WHERE user_id='".$_SESSION['user_id']."' AND store_id='".$row['store_id']."'"; 
				$result_ca = $db->query($query) or die($db->error);
				$num_rows = $result_ca->num_rows;
				if($num_rows > 0) { 
					$store_acce = '1';
				} else { 
					$store_acce = '0';
				}
				
				if($store_acce == '1') {
				extract($row);
				$count++;
				if($count%2 == 0) { 
					$class = 'even';
				} else { 
					$class = 'odd';
				}
				if($store_logo != '') { 
					$store_logo = '<img src="'.$store_logo.'" height="30" width="30" />';
				}
				$content .= '<tr class="'.$class.'">';
				$content .= '<td>';
				$content .= $store_manual_id;
				$content .= '</td><td>';
				$content .= $store_name;
				$content .= '</td><td>';
				$content .= $business_type;
				$content .= '</td><td>';
				$content .= $city;
				$content .= '</td><td>';
				$content .= $phone;
				$content .= '</td><td>';
				$content .= $email;
				$content .= '</td><td>';
				$content .= $currency;
				$content .= '</td><td>';
				$content .= $store_logo;
				$content .= '</td><td>';
				$content .= '<form method="post" name="view_stores" action="store.php">';
				$content .= '<input type="hidden" name="store_id" value="'.$store_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Select Store">';
				$content .= '</form>';
				$content .= '</td>';
				$content .= '</tr>'; 
				unset($class);
				}//if have store access.
			}//loop ends here.
				
			} //if else ends here.
			
		echo $content;
	}//list_levels ends here.
	
	function store_options() {
		global $db; 
		$query = 'SELECT * from stores ORDER by store_name ASC';
		$result = $db->query($query) or die($db->error);
		
			while($row = $result->fetch_array()) { 
				$options .= '<option value="'.$row['store_id'].'">'.$row['store_manual_id'].' | '.ucfirst($row['store_name']).'</option>';
			}
		echo $options;
	}//store options
}//store class ends here.