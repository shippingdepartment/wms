<?php
//user levels Class

class ProductCategory {
	public $category_name;
	public $category_description;
	
	function set_category($category_id) { 
		global $db;
		$query = 'SELECT * from product_categories WHERE warehouse_id="'.$_SESSION['warehouse_id'].'" AND category_id="'.$category_id.'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		$this->category_name = $row['category_name'];
		$this->category_description = $row['category_description'];
	}//Category ends here. setting
	
	function update_category($category_id, $category_name, $category_description) { 
		global $db;
		$query = 'UPDATE product_categories SET
				  category_name = "'.$category_name.'",
				  category_description = "'.$category_description.'"
				   WHERE category_id="'.$category_id.'" AND warehouse_id="'.$_SESSION['warehouse_id'].'"';
		$result = $db->query($query) or die($db->error);
		return 'Product Category updated Successfuly!';
	}//update user level ends here.
	
	function add_category($category_name, $category_description) { 
		global $db;
		//checking if level already exist.
		$query = "SELECT * from product_categories WHERE category_name='".$category_name."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) { 
			$message = 'You cannot add duplicate categories.';
		} else { 
			$query = "INSERT into product_categories VALUES(NULL, '".$category_name."', '".$category_description."', '".$_SESSION['warehouse_id']."')";
			$result = $db->query($query) or die($db->error);
			$message = 'Product Category added successfuly!';
		}
		return $message;
	}//add_user_level ends here.
	
	function list_categories() {
		global $db;
			$query = 'SELECT * from product_categories ORDER by category_name ASC';
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
				$content .= $category_id;
				$content .= '</td><td>';
				$content .= $category_name;
				$content .= '</td><td>';
				$content .= $category_description;
				$content .= '</td>';
				if(partial_access('admin')) {
				$content .= '<td>';
				$content .= '<form method="post" name="edit" action="newcategory.php">';
				$content .= '<input type="hidden" name="edit_category" value="'.$category_id.'">';
				$content .= '<button type="submit" class="btn btn-success" value="Edit"><i class="fa fa-cog" aria-hidden="true"></i></button>';
				$content .= '</form>';
				$content .= '</td><td>';
				$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_category" value="'.$category_id.'">';
				$content .= '<button type="submit" class="btn btn-danger" value="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
				$content .= '</form>';
				$content .= '</td>';
				}
				$content .= '</tr>';
				unset($class);
			}//loop ends here.
		echo $content;
	}//list_levels ends here.
	
	function delete_category($category_id) {
		global $db;
		$query = "DELETE FROM product_categories WHERE category_id='".$category_id."'";
		$result = $db->query($query) or die($db->error);
		return "Category was deleted successfuly!";
	}//delete category ends here.
	
	function category_options($selected_category) {
		global $db;
		$query = 'SELECT * from product_categories WHERE warehouse_id="'.$_SESSION['warehouse_id'].'" ORDER by category_name ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';
		if($selected_category != '') { 
			while($row = $result->fetch_array()) { 
				if($selected_category == $row['category_id']) {
				$options .= '<option selected="selected" value="'.$row['category_id'].'">'.ucfirst($row['category_name']).'</option>';
				} else {
				$options .= '<option value="'.$row['category_id'].'">'.ucfirst($row['category_name']).'</option>';
				}
			}
		} else { 
			while($row = $result->fetch_array()) { 
				$options .= '<option value="'.$row['category_id'].'">'.ucfirst($row['category_name']).'</option>';
			}
		}
		echo $options;	
	}//return user level options for select
	
	function category_options_retreived($selected_category) {
		global $db;
		$query = 'SELECT * from product_categories WHERE warehouse_id="'.$_SESSION['warehouse_id'].'" ORDER by category_name ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';
		if($selected_category != '') { 
			while($row = $result->fetch_array()) { 
				if($selected_category == $row['category_id']) {
				$options .= '<option selected="selected" value="'.$row['category_id'].'">'.ucfirst($row['category_name']).'</option>';
				} else {
				$options .= '<option value="'.$row['category_id'].'">'.ucfirst($row['category_name']).'</option>';
				}
			}
		} else { 
			while($row = $result->fetch_array()) { 
				$options .= '<option value="">Choose Category</option>';
				$options .= '<option value="'.$row['category_id'].'">'.ucfirst($row['category_name']).'</option>';
			}
		}
		echo $options;	
	}//return user level options for select
	
	function get_category_info($category_id, $term) { 
		global $db;
		$query = "SELECT * from product_categories WHERE category_id='".$category_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
}//class ends here.