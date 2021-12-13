<?php
//Notes Class

class Supplier {
	public $supplier_code;
	public $full_name;
	public $business_title;
	public $mobile;
	public $phone;
	public $address;
	public $city;
	public $state;
	public $zipcode;
	public $country;
	public $email;
	public $status;
	
	
	function get_supplier_info($supplier_id, $term) { 
		global $db;
		$query = "SELECT * from suppliers WHERE supplier_id='".$supplier_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	function add_supplier($supplier_code, $supplier_name, $tax_supplier, $mobile, $phone, $address, $city, $state, $zipcode, $country, $email, $status) {
		global $db;
		$query = "SELECT * from suppliers WHERE full_name='".$supplier_name."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) { 
			return '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> A supplier with same name already exists.</div>';
		} else { 
			$query = "INSERT into suppliers(supplier_id, supplier_code, full_name, business_title, mobile, phone, address, city, state, zipcode, country, email, status)
				VALUES(NULL, '".$supplier_code."', '".$supplier_name."', '".$tax_supplier."', '".$mobile."', '".$phone."', '".$address."', '".$city."', '".$state."', '".$zipcode."', '".$country."', '".$email."', '".$status."')
			";
			$result = $db->query($query) or die($db->error);
			$_SESSION['sn_id'] = $db->insert_id;
			return '<div class="alert alert-success"><i class="fa fa-check-circle"></i> Supplier added successfuly.</div>';
		}
	}//add warehouse ends here.
	
	function set_supplier($supplier_id) { 
		global $db;
		$query = 'SELECT * from suppliers WHERE supplier_id="'.$supplier_id.'" ';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		extract($row);
		$this->supplier_code = $supplier_code;
		$this->full_name = $full_name;
		$this->business_title = $business_title;
		$this->mobile = $mobile;
		$this->phone = $phone;
		$this->address = $address;
		$this->city = $city;
		$this->state = $state;
		$this->zipcode = $zipcode;
		$this->country = $country;
		$this->email = $email;
		$this->status = $status;
		
	}//Set Warehouse ends here..
	
	function update_supplier($supplier_id, $supplier_code, $supplier_name, $tax_supplier, $mobile, $phone, $address, $city, $state, $zipcode, $country, $email, $status) {
		global $db;
		$query = 'UPDATE suppliers SET
				  supplier_code = "'.$supplier_code.'",
				  full_name = "'.$supplier_name.'",
				  business_title = "'.$tax_supplier.'",
				  mobile = "'.$mobile.'",
				  phone = "'.$phone.'",
				  address = "'.$address.'",
				  city = "'.$city.'",
				  state = "'.$state.'",
				  zipcode = "'.$zipcode.'",
				  country = "'.$country.'",
				  email = "'.$email.'",
				  status = "'.$status.'"
				   WHERE supplier_id = "'.$supplier_id.'" ';
		$result = $db->query($query) or die($db->error);
		return '<div class="alert alert-success"><i class="fa fa-check-circle"></i> Supplier updated Successfuly!</div>';
	}//update user level ends here.	
	
	function disable_supplier($supplier_id) {
		global $db;
		
		$query = 'UPDATE suppliers SET status = "0" WHERE supplier_id = "'.$supplier_id.'" ';
		$result = $db->query($query) or die($db->error);
		return 'Supplier Desactivated Successfuly!';
	}//update user level ends here.	
	function enable_supplier($supplier_id) {
		global $db;
		
		$query = 'UPDATE suppliers SET status = "1" WHERE supplier_id = "'.$supplier_id.'" ';
		$result = $db->query($query) or die($db->error);
		return 'Supplier Activated Successfuly!';
	}//update user level ends here.	
	
	function list_suppliers() {
		global $db;
		$query = 'SELECT * from suppliers ORDER by full_name ASC';
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
			$content .= '<tr class="'.$class.'">';
			$content .= '<td>';
			$content .= $supplier_code;
			$content .= '</td><td>';
			$content .= $full_name;
			$content .= '</td>';
			if(partial_access('admin')) { 
			$content .= '<td>';
			$content .= $business_title;
			$content .= '</td>';
			} 
			$content .= '<td>';
			$content .= $phone;
			$content .= '</td><td>';
			$content .= $address.' - '.$country;
			$content .= '</td><td>';
			$content .= $email;
			$content .= '</td><td>';
			if ($status=='0') {
			$content .= '<span class="text-danger">Inactive</span>';
			$content .= '</td>';
				if(partial_access('admin')) {
					$content .= '<td><form method="post" name="edit" action="newsupplier.php">';
					$content .= '<input type="hidden" name="edit_supplier" value="'.$supplier_id.'">';
					$content .= '<button type="submit" class="btn btn-success" value="Edit" title="Edit Supplier"><i class="fa fa-cog" aria-hidden="true"></i></button>';
					$content .= '</form>';
					$content .= '</td><td>';
					$content .= '<form method="post" name="enable" onsubmit="return confirm_delete();" action="suppliers.php">';
					$content .= '<input type="hidden" name="enable_supplier" value="'.$supplier_id.'">';
					$content .= '<button type="submit" class="btn btn-info" value="Enable" title="Activate Supplier"><i class="fa fa-unlock" aria-hidden="true"></i></button>';
					$content .= '</form>';
					$content .= '</td>';
					}
			} elseif ($status=='1') {
			$content .= '<span class="text-success">Active</span>';
			$content .= '</td>';
				if(partial_access('admin')) {
					$content .= '<td><form method="post" name="edit" action="newsupplier.php">';
					$content .= '<input type="hidden" name="edit_supplier" value="'.$supplier_id.'">';
					$content .= '<button type="submit" class="btn btn-success" value="Edit" title="Edit Supplier"><i class="fa fa-cog" aria-hidden="true"></i></button>';
					$content .= '</form>';
					$content .= '</td><td>';
					$content .= '<form method="post" name="disable" onsubmit="return confirm_delete();" action="suppliers.php">';
					$content .= '<input type="hidden" name="disable_supplier" value="'.$supplier_id.'" >';
					$content .= '<button type="submit" class="btn btn-danger" value="Disable" title="Deactivate Supplier"><i class="fa fa-lock" aria-hidden="true"></i></button>';
					$content .= '</form>';
					$content .= '</td>';
					}
			}
			
			$content .= '</tr>';
			unset($class);
		}//loop ends here.	
	echo $content;
	}
	
	function print_list_suppliers() {
		global $db;
		$query = 'SELECT * from suppliers ORDER by full_name ASC';
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
			$content .= '<tr class="'.$class.'">';
			$content .= '<td>';
			$content .= $supplier_code;
			$content .= '</td><td>';
			$content .= $full_name;
			$content .= '</td>';
			if(partial_access('admin')) { 
			$content .= '<td>';
			$content .= $business_title;
			$content .= '</td>';
			} 
			$content .= '<td>';
			$content .= $phone;
			$content .= '</td><td>';
			$content .= $address.' - '.$country;
			$content .= '</td><td>';
			$content .= $email;
			$content .= '</td><td>';
			if ($status=='0') {
			$content .= '<span class="text-danger">Inactive</span>';
			$content .= '</td>';
			} elseif ($status=='1') {
			$content .= '<span class="text-success">Active</span>';
			$content .= '</td>';
			}
			
			$content .= '</tr>';
			unset($class);
		}//loop ends here.	
	echo $content;
	}
	
	function delete_client($client_id) { 
		global $db;
		$query = "SELECT * FROM customer_log WHERE client_id='".$client_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) { 
			return 'Please delete sale invoices, receivings, return invoices, return payments for related client first.';
		} else { 
			$query = "DELETE FROM clients WHERE client_id='".$client_id."'";
			$result = $db->query($query) or die($db->error);
			return 'Client deleted successfuly!';
		}
	}//delete client ends here.
	
	function supplier_options($supplier_id) {
		global $db;
		$query = 'SELECT * from suppliers Where status="1" ORDER by full_name ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';
		if($supplier_id != '') { 
			while($row = $result->fetch_array()) { 
				if($supplier_id == $row['supplier_id']) {
				$options .= '<option selected="selected" value="'.$row['supplier_id'].'">'.$row['full_name'].' </option>';
				} else { 
				$options .= '<option value="'.$row['supplier_id'].'">'.$row['full_name'].' </option>';
				}
			}
		} else { 
			while($row = $result->fetch_array()) { 
				$options .= '<option value="'.$row['supplier_id'].'">'.$row['full_name'].' </option>';
			}
		}
		return $options;
		
		 
	}//vendor options ends here.
	
	function supplier_options_list($selected_supplier) {
		global $db;
		$query = 'SELECT * from suppliers Where status="1" ORDER by full_name ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';
		if($selected_supplier != '') { 
			while($row = $result->fetch_array()) { 
				if($selected_supplier == $row['supplier_id']) {
				$options .= '<option selected="selected" value="'.$row['supplier_id'].'">'.ucfirst($row['full_name']).'</option>';
				} else {
				$options .= '<option value="'.$row['supplier_id'].'">'.ucfirst($row['full_name']).'</option>';
				}
			}
		} else { 
			while($row = $result->fetch_array()) { 
				$options .= '<option value="">Choose Supplier</option>';
				$options .= '<option value="'.$row['supplier_id'].'">'.ucfirst($row['full_name']).'</option>';
			}
		}
		echo $options;	
	}//return user level options for select
	
	function add_log($datetime, $client_id, $transaction_type, $type_table_id) {
		global $db;
		$query = "INSERT into customer_log(customer_log_id, datetime, client_id, transaction_type, type_table_id, store_id) VALUES(NULL, '".$datetime."', '".$client_id."', '".$transaction_type."', '".$type_table_id."', '".$_SESSION['store_id']."')";	
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add log ends here.
	
	function add_receiving($date, $method, $ref_no, $memo, $amount, $client_id) { 
		global $db;
		$query = "INSERT into receivings(receiving_id, datetime, method, ref_no, memo, amount, client_id, agent_id, store_id) VALUES(NULL, '".$date."', '".$method."', '".$ref_no."', '".$memo."', '".$amount."', '".$client_id."', '".$_SESSION['user_id']."', '".$_SESSION['store_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add_payment ends here.
	
	function add_return_payment($date, $method, $ref_no, $memo, $amount, $client_id) { 
		global $db;
		$query = "INSERT into sale_return_payment(return_payment_id, datetime, method, ref_no, memo, amount, client_id, agent_id, store_id) VALUES(NULL, '".$date."', '".$method."', '".$ref_no."', '".$memo."', '".$amount."', '".$client_id."', '".$_SESSION['user_id']."', '".$_SESSION['store_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add_payment ends here.
	
	
	function get_client_balance($client_id) { 
		global $db;
		
		$creditQuery = "SELECt * from creditors WHERE client_id='".$client_id."' AND store_id='".$_SESSION['store_id']."'";
		$creditResult = $db->query($creditQuery) or die($db->error);
		$receiveable = 0;
	
		while($creditRow = $creditResult->fetch_array()) {
			$receiveable += $creditRow['receiveable'];
			if($creditRow['receiveable'] == 0) { 
				$receiveable -= $creditRow['received'];	
			}
		}

		$receivingQuery = "SELECt * from receivings WHERE client_id='".$client_id."' AND store_id='".$_SESSION['store_id']."'";
		$receivingResult = $db->query($receivingQuery) or die($db->error);
	
		while($recevingRow = $receivingResult->fetch_array()) {
			$receiveable -= $recevingRow['amount'];
		}
		
		$sale_return_payment = "SELECt * from sale_return_payment WHERE client_id='".$client_id."' AND store_id='".$_SESSION['store_id']."'";
		$sale_payment_result = $db->query($sale_return_payment) or die($db->error);
		
		while($sale_return_row = $sale_payment_result->fetch_array()) { 
			$receiveable -= $sale_return_row['amount'];
		}
		
		return $receiveable;
	}//get vendor balance ends here.

	function list_receivings() {
		global $db;
		$query = 'SELECT * from receivings WHERE store_id="'.$_SESSION['store_id'].'" ORDER by receiving_id DESC';
		$result = $db->query($query) or die($db->error);
		$content = '';

		while($row = $result->fetch_array()) { 
			extract($row);
			
			$datetime = strtotime($datetime);
			$date = date('d-M-Y', $datetime);
			
			$client = $this->get_client_info($client_id, 'full_name');
			
			$user = new Users;
			$agent = $user->get_user_info($agent_id, 'first_name').' '.$user->get_user_info($agent_id, 'last_name');
			
			$content .= '<tr><td>';
			$content .= $receiving_id;
			$content .= '</td><td>';
			$content .= $date;
			$content .= '</td><td>';
			$content .= $method;
			$content .= '</td><td>';
			$content .= $ref_no;
			$content .= '</td><td>';
			$content .= $agent;
			$content .= '</td><td>';
			$content .= $client;
			$content .= '</td><td>';
			$content .= $memo;
			$content .= '</td><td>';
			$content .= $amount;
			$content .= '</td>';
			if(partial_access('admin')) { 
				$content .= '<td><form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_receiving" value="'.$receiving_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Delete">';
				$content .= '</form>';
				$content .= '</td>'; }
				$content .= '</tr>';
			unset($class);
		}//loop ends here.	
	echo $content;
	}//list_notes ends here.
	
	function delete_receiving($receiving_id) {
		global $db;
		
		$query = "DELETE from receivings WHERE receiving_id='".$receiving_id."'";
		$result = $db->query($query) or die($db->error);
		
		$query = "DELETE from customer_log WHERE transaction_type='Sale Receiving' AND type_table_id='".$receiving_id."'";
		$result = $db->query($query) or die($db->error);
		
		$query = "DELETE from customer_log WHERE transaction_type='Receiving' AND type_table_id='".$receiving_id."'";
		$result = $db->query($query) or die($db->error);
		
		return 'Receiving deleted Successfuly.';	
	}//delete_purchase return receiving.
	
	function list_return_payments() {
		global $db;
		$query = 'SELECT * from sale_return_payment WHERE store_id="'.$_SESSION['store_id'].'" ORDER by return_payment_id DESC';
		$result = $db->query($query) or die($db->error);
		$content = '';

		while($row = $result->fetch_array()) { 
			extract($row);
			
			$datetime = strtotime($datetime);
			$date = date('d-M-Y', $datetime);
			
			$client = $this->get_client_info($client_id, 'full_name');
			
			$user = new Users;
			$agent = $user->get_user_info($agent_id, 'first_name').' '.$user->get_user_info($agent_id, 'last_name');
			
			$content .= '<tr><td>';
			$content .= $return_payment_id;
			$content .= '</td><td>';
			$content .= $date;
			$content .= '</td><td>';
			$content .= $method;
			$content .= '</td><td>';
			$content .= $ref_no;
			$content .= '</td><td>';
			$content .= $agent;
			$content .= '</td><td>';
			$content .= $client;
			$content .= '</td><td>';
			$content .= $memo;
			$content .= '</td><td>';
			$content .= $amount;
			$content .= '</td>';
			if(partial_access('admin')) { 
				$content .= '<td><form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_sale_return_payment" value="'.$return_payment_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Delete">';
				$content .= '</form>';
				$content .= '</td>'; }
				$content .= '</tr>';
		}//loop ends here.	
	echo $content;
	}//list_notes ends here.
	
	function delete_sale_return_payment($return_payment_id) {
		global $db;
		
		$query = "DELETE from sale_return_payment WHERE return_payment_id='".$return_payment_id."'";
		$result = $db->query($query) or die($db->error);
		
		$query = "DELETE from customer_log WHERE transaction_type='Sale Return Refund' AND type_table_id='".$return_payment_id."'";
		$result = $db->query($query) or die($db->error);
		return 'Return Payment deleted Successfuly.';	
	}//delete_purchase return receiving.
	
	function clear_creditors($amount, $client_id){
		global $db;
		
		$query = "SELECT * FROM creditors WHERE client_id='".$client_id."' ORDER by credit_id ASC";
		$result = $db->query($query) or die($db->error);
		
		while($row = $result->fetch_array()) {
			extract($row);
			if($receiveable == 0 || $receiveable == $received || $amount == 0) { 
				//do nothing.
			} else { 
				if($received == 0) {
					if($amount < $receiveable) { 
						$receive = $amount;
					} else { 
						$receive = $receiveable;
					}
					$query_up = "UPDATE creditors SET
						received = '".$receive."'
						WHERE credit_id='".$credit_id."'
						";
					$amount -= $receive;	
				} else if($received != 0) { 
					$difference = $receiveable-$received;
					if($amount < $difference) { 
						$receive = $amount+$received;
					} else { 
						$receive = $difference+$received;
					}
					$query_up = "UPDATE creditors SET
						received = '".$receive."'
						WHERE credit_id='".$credit_id."'
						";
					$amount -= $difference;	
				}
				$result_up = $db->query($query_up) or die($db->error);
			}//main if ends here.
		}//main loop ends.
	}//debts clear ends here.--

	function customers_balance_summary() { 
		global $db;
		
		$query = "SELECT * FROM clients WHERE store_id='".$_SESSION['store_id']."' ORDER by full_name ASC";
		$result = $db->query($query) or die($db->error);
		$content = '';
		$grand_total = 0;
		while($row = $result->fetch_array()) { 
			extract($row);
			//getting balance.
			$balance = $this->get_client_balance($client_id);
			$grand_total += $balance;
			
			$content .= '<tr><td>';
			$content .= $full_name;
			$content .= '</td><td>';
			$content .= $business_title;
			$content .= '</td><td align="right">';
			$content .= currency_format($grand_total);
			$content .= '</td></tr>';
		}	
			$new_store = new Store;
			$currency = $new_store->get_store_info($_SESSION['store_id'], 'currency');
			$content .= '<tr><th colspan="2" align="right">Grand Total</th><th align="right">'.$currency.' '.currency_format($grand_total).'</tH></tr>';
		echo $content;
	}//customers balance summary ends here.
	
	function customer_ledger_summary($client) {
		global $db;
		
		$query = "SELECT * from customer_log WHERE client_id='".$client."' ORDER by customer_log_id ASC";
		$result = $db->query($query) or die($db->error);
		$balance = 0;
		$content = '';
		$balance = 0;
		while($row = $result->fetch_array()) {
			extract($row);
			
			$datetime = strtotime($datetime);
			$date = date('d-M-Y', $datetime);
			
			$content .= '<tr><td>';
			$content .= $transaction_type;
			$content .= '</td><td>';
			$content .= $date;
			$content .= '</td><td>';
			$content .= $type_table_id;
			$content .= '</td><td>';
			
			if($transaction_type == 'Sale Invoice' || $transaction_type == 'Cash Sale') { 
				//Invoice Details.
				$sale_query = "SELECT * from sales WHERE sale_id='".$type_table_id."'";
				$sale_result = $db->query($sale_query) or die($db->error);
				
				while($sale_row = $sale_result->fetch_array()) {
					$content .= $sale_row['memo'];
					$content .= '</td><td>';
				}
				
				$sale_detail_query = "SELECT * from sale_detail WHERE sale_id='".$type_table_id."'";
				$sale_detail_result = $db->query($sale_detail_query) or die($db->error);
				$invoice_total = 0;
				while($sale_detail_row = $sale_detail_result->fetch_array()) { 
					$credit_query = "SELECT * from creditors WHERE credit_id='".$sale_detail_row['credit_id']."'";
					$credit_result = $db->query($credit_query) or die($db->error);
					
					while($credit_row = $credit_result->fetch_array()) { 
						$invoice_total += $credit_row['receiveable'];
					}
				}
				$balance = $invoice_total+$balance;
				
				$content .= currency_format($invoice_total);
				$content .= '</td><td>';
				$content .= currency_format($balance);
				$content .= '</td></tr>';
				
			} else if($transaction_type == 'Sale Receiving' || $transaction_type == 'Receiving') { 
				//Cash receivign.
				$receiving_query = "SELECT * from receivings WHERE receiving_id='".$type_table_id."'";
				$receiving_result = $db->query($receiving_query) or die($db->error);
				while($receiving_row = $receiving_result->fetch_array()) { 
					$content .= $receiving_row['memo'];
					$content .= '</td><td>';
					
					$balance = $balance-$receiving_row['amount'];
					$content .= '('.currency_format($receiving_row['amount']).')';
					$content .= '</td><td>';
					$content .= currency_format($balance);
					$content .= '</td></tr>';
				}
			} else if($transaction_type == 'Invoice Return' || $transaction_type == 'Sale Return') { 
				//sale return invoice.
				$sale_query = "SELECT * from sale_returns WHERE sale_rt_id='".$type_table_id."'";
				$sale_result = $db->query($sale_query) or die($db->error);
				
				while($sale_row = $sale_result->fetch_array()) {
					$content .= $sale_row['memo'];
					$content .= '</td><td>';
				}
				
				$sale_detail_query = "SELECT * from sale_return_detail WHERE sale_rt_id='".$type_table_id."'";
				$sale_detail_result = $db->query($sale_detail_query) or die($db->error);
				$invoice_total = 0;
				while($sale_detail_row = $sale_detail_result->fetch_array()) { 
					$credit_query = "SELECT * from creditors WHERE credit_id='".$sale_detail_row['credit_id']."'";
					$credit_result = $db->query($credit_query) or die($db->error);
					
					while($credit_row = $credit_result->fetch_array()) { 
						$invoice_total += $credit_row['received'];
					}
				}
				$balance = $balance-$invoice_total;
				
				$content .= '('.currency_format($invoice_total).')';
				$content .= '</td><td>';
				$content .= currency_format($balance);
				$content .= '</td></tr>';
				
				
			} else if($transaction_type == 'Sale Return Refund') { 
				//sale Return Payment.
				$receiving_query = "SELECT * from sale_return_payment WHERE return_payment_id='".$type_table_id."'";
				$receiving_result = $db->query($receiving_query) or die($db->error);
				while($receiving_row = $receiving_result->fetch_array()) { 
					$content .= $receiving_row['memo'];
					$content .= '</td><td>';
					
					$balance = $balance+$receiving_row['amount'];
					$content .= currency_format($receiving_row['amount']);
					$content .= '</td><td>';
					$content .= currency_format($balance);
					$content .= '</td></tr>';
				}
			}
			
		}//main loop ends here.
		echo $content;
	}//customer ledger summary ends here.
}//class ends here.