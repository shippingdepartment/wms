<?php
//storeAccess Class

class StoreAccess {
	
	function add_store_access($user_id, $warehouse_id, $access_to) { 
		global $db;
		if($_SESSION['user_type'] == 'admin') {
			$query = "SELECT * from warehouse_access WHERE user_id='".$user_id."' AND warehouse_id='".$warehouse_id."'";
			$result = $db->query($query) or die($db->error);
			$rows = $result->num_rows;
			if($rows > 0) { 
				return '<div class="alert alert-danger">This User has already access to this Warehouse</div>';
			} else { 
				
				$products = 0;
				$transfers = 0;
				$orders = 0;
				$deliveries = 0;
				$clients = 0;
				$reports = 0;
				
				foreach($access_to as $access) { 
					if($access == 'products') { 
						$products = 1;
					} else if($access == 'transfers') { 
						$transfers = 1;
					} else if($access == 'deliveries') { 
						$deliveries = 1;
					} else if($access == 'clients') { 
						$clients = 1;
					} else if($access == 'reports') { 
						$reports = 1; 
					} else if($access == 'orders') { 
						$orders = 1;
					} 
				}
				
				$query = "INSERT into warehouse_access(user_id, warehouse_id, products, transfers, deliveries, clients, reports, orders) VALUES('".$user_id."', '".$warehouse_id."', '".$products."', '".$transfers."', '".$deliveries."', '".$clients."', '".$reports."', '".$orders."')";
				$result = $db->query($query) or die($db->error);
				return '<div class="alert alert-success">Access granted successfuly.</div>';
			}
		} else { 
			return '<div class="alert alert-danger">You have not the authority to grant Access !!</div>';
		}
	}//add store acces ends here,.
	
	function list_store_access() { 
		global $db;
		if($_SESSION['user_type'] != 'admin') {
			echo 'You cannot view this list.';	
		} else {
			$query = "SELECT * from store_access";
			$result = $db->query($query) or die($db->error);
			$options = '';
			while($row = $result->fetch_array()) {
				$query_user = "SELECT * from users WHERE user_id='".$row['user_id']."'";
				$result_user = $db->query($query_user) or die($db->error);
				$row_user = $result_user->fetch_array();
				//user info query ends here.
				$query_store = "SELECT * from stores WHERE store_id='".$row['store_id']."'";
				$result_store = $db->query($query_store) or die($db->error);
				$row_store = $result_store->fetch_array();	
				//store info ends here.
				
				$options .= '<tr>';
				$options .= '<td>'.$row['user_id'].'</td>';
				$options .= '<td>'.$row_user['first_name'].' '.$row_user['last_name'].'</td>';
				$options .= '<td>'.$row_user['email'].'</td>';
				$options .= '<td>'.$row_store['store_name'].'</td>';
				$options .= '<td>';
				if($row['sales'] == '1') { 
					$options .= 'Sales, ';
				} 
				if($row['purchase'] == '1'){ 
					$options .= 'Purchase, ';
				}
				if($row['vendors'] == '1'){ 
					$options .= 'Vendors, ';
				}
				if($row['clients'] == '1'){ 
					$options .= 'Clients, ';
				}
				if($row['products'] == '1'){ 
					$options .= 'Products, ';
				}
				if($row['warehouse'] == '1'){ 
					$options .= 'Warehouse, ';
				}
				if($row['returns'] == '1'){ 
					$options .= 'Returns, ';
				}
				if($row['price_level'] == '1'){ 
					$options .= 'Price Level, ';
				}
				if($row['reports'] == '1'){ 
					$options .= 'Reports, ';
				}
				if($row['expenses'] == '1'){ 
					$options .= 'Expenses';
				}
				$options .= '</td>';
				$options .= '<td><form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$options .= '<input type="hidden" name="delete_access" value="'.$row['access_id'].'">';
				$options .= '<input type="submit" class="btn btn-default btn-sm" value="Delete Access">';
				$options .= '</form></td>';
				$options .= '</tr>';
			}//while loop ends here.
			echo $options;	
		}
	}//list_store_access function ends here.
	
	function delete_access($access_id) {
			global $db; 
		if($_SESSION['user_type'] == 'admin' && $access_id != '') { 
			$query = "DELETE from store_access WHERE access_id='".$access_id."'";
			$result = $db->query($query) or die($db->error);
			return '<div class="alert alert-success">store access deleted successfuly!</diV>';
		}//if admin
	}//delete acces function ends here.
	
	function have_store_access() {
		global $db;
		$query = "SELECT * from store_access WHERE user_id='".$_SESSION['user_id']."' AND store_id='".$_SESSION['store_id']."'"; 
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if($num_rows > 0) { 
			return TRUE;
		} else { 
			return FALSE;
		}
	}//have_store_access.
	
	function have_module_access($module) { 
		global $db;
		$query = "SELECT * from store_access WHERE user_id='".$_SESSION['user_id']."' AND store_id='".$_SESSION['store_id']."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		
		if($row[$module] == '1') { 
			return TRUE;
		} else { 
			return FALSE;
		}
	}//end of have module access.
	
}//store access class ends here.