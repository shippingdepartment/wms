<?php
//user levels Class

class ProductTax {
	public $tax_name;
	public $tax_rate;
	public $tax_type;
	public $tax_description;
	
	function set_tax($tax_id) { 
		global $db;
		$query = 'SELECT * from product_taxes WHERE store_id="'.$_SESSION['store_id'].'" AND tax_id="'.$tax_id.'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		$this->tax_name = $row['tax_name'];
		$this->tax_rate = $row['tax_rate'];
		$this->tax_type = $row['tax_type'];
		$this->tax_description = $row['tax_description'];
	}//Category ends here. setting
	
	function update_tax($tax_id, $tax_name, $tax_rate, $tax_type, $tax_description) { 
		global $db;
		$query = 'UPDATE product_taxes SET
				  tax_name = "'.$tax_name.'",
				  tax_rate = "'.$tax_rate.'",
				  tax_type = "'.$tax_type.'",
				  tax_description = "'.$tax_description.'"
				   WHERE tax_id="'.$tax_id.'" AND store_id="'.$_SESSION['store_id'].'"';
		$result = $db->query($query) or die($db->error);
		return 'Product Tax updated Successfuly!';
	}//update user level ends here.
	
	function add_tax($tax_name, $tax_rate, $tax_type, $tax_description) { 
		global $db;
		//checking if level already exist.
		$query = "SELECT * from product_taxes WHERE store_id='".$_SESSION['store_id']."' AND tax_name='".$tax_name."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) { 
			$message = 'You cannot add duplicate taxes.';
		} else { 
			$query = "INSERT into product_taxes VALUES(NULL, '".$tax_name."', '".$tax_rate."', '".$tax_type."', '".$tax_description."', '".$_SESSION['store_id']."')";
			$result = $db->query($query) or die($db->error);
			$message = 'Product Tax added successfuly!';
		}
		return $message;
	}//add_user_level ends here.
	
	function list_taxes() {
		global $db;
			$query = 'SELECT * from product_taxes WHERE store_id="'.$_SESSION['store_id'].'" ORDER by tax_name ASC';
			$result = $db->query($query) or die($db->error);
			$content = '';
			$count = 0;
			while($row = $result->fetch_array()) { 
				extract($row);
				$count++;
				if($count % 2 == 0) { 
					$class = 'even';
				} else { 
					$class = 'odd';
				}
				$content .= '<tr class="'.$class.'">';
				$content .= '<td>';
				$content .= $tax_id;
				$content .= '</td><td>';
				$content .= $tax_name;
				$content .= '</td><td>';
				$content .= $tax_rate;
				$content .= '</td><td>';
				if($tax_type == 'percentage') { 
					$tax_type = 'Percentage %';
				} else { 
					$new_store = new Store;
					$new_store->set_store($_SESSION['store_id']);
					
					$tax_type = 'Fixed '.$new_store->currency;
				}
				$content .= $tax_type;
				$content .= '</td><td>';
				$content .= $tax_description;
				$content .= '</td>';
				if(partial_access('admin')) {
				$content .= '<td>';
				$content .= '<form method="post" name="edit" action="manage_taxes.php">';
				$content .= '<input type="hidden" name="edit_tax" value="'.$tax_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Edit">';
				$content .= '</form>';
				$content .= '</td><td>';
				$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_tax" value="'.$tax_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Delete">';
				$content .= '</form>';
				$content .= '</td>';
				}
				$content .= '</tr>';
				unset($class);
			}//loop ends here.
		echo $content;
	}//list_levels ends here.
	
	function delete_tax($tax_id) {
		global $db;
		$query = "DELETE FROM product_taxes WHERE tax_id='".$tax_id."'";
		$result = $db->query($query) or die($db->error);
		return 'Tax was deleted successfuly!';
	}//delete category ends here.
	
	function tax_options($selected_tax) {
		global $db;
		$query = 'SELECT * from product_taxes WHERE store_id="'.$_SESSION['store_id'].'" ORDER by tax_name ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';
		if($selected_tax != '') { 
			while($row = $result->fetch_array()) { 
				if($selected_tax == $row['tax_id']) {
				$options .= '<option selected="selected" value="'.$row['tax_id'].'">'.ucfirst($row['tax_name']).'</option>';
				} else { 
				$options .= '<option value="'.$row['tax_id'].'">'.ucfirst($row['tax_name']).'</option>';
				}
			}
		} else { 
			while($row = $result->fetch_array()) { 
				$options .= '<option value="'.$row['tax_id'].'">'.ucfirst($row['tax_name']).'</option>';
			}
		}
		echo $options;	
	}//return user level options for select
	
	function get_tax_info($tax_id, $term) { 
		global $db;
		$query = "SELECT * from product_taxes WHERE tax_id='".$tax_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
}//class ends here.