<?php
//product Class

class Expenses {
	public $type_name;
	public $type_description;
	public $title;
	public $type_id;
	public $datetime;
	public $description;
	public $amount;
	public $agent_id;
	
	function delete_expense($expense_id) {
		global $db;
		
		$query = "DELETE FROM expenses WHERE expense_id='".$expense_id."' AND store_id='".$_SESSION['store_id']."'";
		$result = $db->query($query) or die($db->error);
		return "Expsense was deleted successfuly!";
	}//delete category ends here.
	
	function update_expense($edit_expense,$type_id, $datetime, $title, $description, $amount) { 
		global $db;
		
		$query = "UPDATE expenses SET 
					 type_id = '".$type_id."',
					 datetime = '".$datetime."',
					 title = '".$title."',
					 description = '".$description."',
					 amount = '".$amount."'
					 	WHERE expense_id='".$edit_expense."' AND store_id='".$_SESSION['store_id']."'";
		$result = $db->query($query) or die($db->error);				
		return "Expense updated successfuly.";
	}
	
	function set_expense($expense_id) { 
		global $db;
		$query = 'SELECT * from expenses WHERE store_id="'.$_SESSION['store_id'].'" AND expense_id="'.$expense_id.'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		
		$this->title = $row['title'];
		$this->type_name = $this->get_expense_type($row['type_id'], 'type_name');
		$this->type_id = $row['type_id'];
		$this->datetime = $row['datetime'];
		$this->description = $row['description'];
		$this->amount = $row['amount'];
		$this->agent_id = $row['agent_id'];
	}//Category ends here. setting
	
	function add_expense($type_id, $datetime, $title, $description, $amount) { 
		global $db;
		
		if(is_numeric($amount)) { 
			$query = "INSERT into expenses VALUES(NULL, '$type_id', '$datetime', '$title', '$description', '$amount', '".$_SESSION['user_id']."', '".$_SESSION['store_id']."')";			
			$result = $db->query($query) or die($db->error);
			$message = "Expense was added successfuly!";
		} else { 
			$message = "Amount needs to be a number.";
		}
		return $message;
	}
	
	function list_expenses() { 
		global $db;
		
		$query = "SELECT * FROM expenses WHERE store_id='".$_SESSION['store_id']."' ORDER by expense_id DESC";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		
		while($row = $result->fetch_array()) { 
			extract($row);
			global $new_user;
			$agent_name = $new_user->get_user_info($agent_id, 'first_name').' '.$new_user->get_user_info($agent_id, 'last_name');
			
			$content .= "<tr><td>";
			$content .= $expense_id."</td><td>";
			$datetime = strtotime($datetime);
			$content .= date('d M Y', $datetime)."</td><td>";
			$content .= $title."</td><td>";
			$content .= $this->get_expense_type($type_id, 'type_name')."</td><td>";
			$content .= currency_format($amount)."</td><td>";
			$content .= $agent_name."</td>";
				$content .= "<td>
							<a href='reports/expense_voucher.php?expense_id=".$expense_id."' target='_blank' title='View/Print'><span class='glyphicon glyphicon-eye-open'></span></a>";
				if(partial_access('admin')) {
				$content .= " <a href='manage_expenses.php?expense_id=".$expense_id."' title='Edit/Update'><span class='glyphicon glyphicon-edit'></span></a>
							 <a href='expenses.php?expense_id=".$expense_id."' class='delete' title='Delete'><span class='glyphicon glyphicon-trash'></span></a>";
				}
			$content .= "</td></tr>";
		}
		return $content;
	}
	
	/*Expense types related items below.*/
	function get_expense_type($type_id, $term) { 
		global $db;
		$query = "SELECT `".$term."` FROM 
						expense_types WHERE type_id='".$type_id."' AND store_id='".$_SESSION['store_id']."'";
		$result = $db->query($query) or die($db->error);				
		$row = $result->fetch_array();
		return $row[$term];
	}
	
	function expense_type_options($type_id) {
		global $db;
		$query = 'SELECT * from expense_types WHERE store_id="'.$_SESSION['store_id'].'" ORDER by type_name ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';
		if($type_id != '') { 
			while($row = $result->fetch_array()) { 
				if($type_id == $row['type_id']) {
				$options .= '<option selected="selected" value="'.$row['type_id'].'">'.ucfirst($row['type_name']).'</option>';
				} else { 
				$options .= '<option value="'.$row['type_id'].'">'.ucfirst($row['type_name']).'</option>';
				}
			}
		} else { 
			while($row = $result->fetch_array()) { 
				$options .= '<option value="'.$row['type_id'].'">'.ucfirst($row['type_name']).'</option>';
			}
		}
		echo $options;	
	}//return user level options for select

	
	function update_expense_type($expense_type_id, $type_name, $type_description) { 
		global $db;
		$query = 'UPDATE expense_types SET
				  type_name = "'.$type_name.'",
				  type_description = "'.$type_description.'"
				   WHERE type_id="'.$expense_type_id.'" AND store_id="'.$_SESSION['store_id'].'"';
		$result = $db->query($query) or die($db->error);
		return 'Expense type updated Successfuly!';
	}//update user level ends here.
	
	
	function delete_expense_type($type_id) {
		global $db;
		
		$expense_query = "SELECT * FROM expenses WHERE type_id='".$type_id."'";
		$result_expenses = $db->query($expense_query) or die($db->error);
		$num_rows = $result_expenses->num_rows; 
		
		if($num_rows > 0) { 
			return "Please delete expenses related to this type first before deleting it.";
		} else { 
			$query = "DELETE FROM expense_types WHERE type_id='".$type_id."' AND store_id='".$_SESSION['store_id']."'";
			$result = $db->query($query) or die($db->error);
			return "Expsense type was deleted successfuly!";
		}
	}//delete category ends here.
	
	function set_expense_type($expense_type_id) { 
		global $db;
		$query = 'SELECT * from expense_types WHERE store_id="'.$_SESSION['store_id'].'" AND type_id="'.$expense_type_id.'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		$this->type_name = $row['type_name'];
		$this->type_description = $row['type_description'];
	}//Category ends here. setting
	
	function add_expense_type($type_name, $type_description) { 
		global $db;
		//checking if level already exist.
		$query = "SELECT * from expense_types WHERE store_id='".$_SESSION['store_id']."' AND type_name='".$type_name."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) { 
			$message = 'You cannot add duplicate expense types.';
		} else { 
			$query = "INSERT into expense_types VALUES(NULL, '".$type_name."', '".$type_description."', '".$_SESSION['store_id']."')";
			$result = $db->query($query) or die($db->error);
			$message = 'Expense type added successfuly!';
		}
		return $message;
	}//add_user_level ends here.	
	
	function list_expense_types() {
		global $db;
			$query = 'SELECT * from expense_types WHERE  store_id="'.$_SESSION['store_id'].'" ORDER by type_name ASC';
			$result = $db->query($query) or die($db->error);
			$content = '';

			while($row = $result->fetch_array()) { 
				extract($row);
				$content .= '<tr>';
				$content .= '<td>';
				$content .= $type_id;
				$content .= '</td><td>';
				$content .= $type_name;
				$content .= '</td><td>';
				$content .= $type_description;
				$content .= '</td>';
				if(partial_access('admin')) {
				$content .= '<td>';
				$content .= '<form method="post" name="edit" action="manage_expense_types.php">';
				$content .= '<input type="hidden" name="edit_expense_type" value="'.$type_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Edit">';
				$content .= '</form>';
				$content .= '</td><td>';
				$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_expense_type" value="'.$type_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Delete">';
				$content .= '</form>';
				$content .= '</td>';
				}
				$content .= '</tr>';
				unset($class);
			}//loop ends here.
		echo $content;
	}//list_levels ends here.
	
	
}//class ends here.