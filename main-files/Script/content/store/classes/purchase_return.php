<?php
//Purchase Class

class PurchaseReturn { 

	function get_purchase_return_info($purchase_rt_id, $term) { 
		global $db;
		$query = "SELECT * from purchase_returns WHERE purchase_rt_id='".$purchase_rt_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	//add purchase functions starts here.
	function add_purchase_return($datetime, $purchase_inv_no, $memo, $vendor_id, $payment_method) { 
		global $db;
		
		$query = "INSERT into purchase_returns(purchase_rt_id, datetime, invoice_no, memo, vendor_id, payment_status, store_id, agent_id) VALUES(NULL, '".$datetime."', '".$purchase_inv_no."', '".$memo."', '".$vendor_id."', '".$payment_method."', '".$_SESSION['store_id']."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add_purchase ends here. returns purchase id.


	function list_all_purchase_returns() { 
		global $db;
		
		$query = "SELECT * from purchase_returns WHERE store_id='".$_SESSION['store_id']."' ORDER by purchase_rt_id DESC";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$vendor = new Vendor;
			$vendor_name = $vendor->get_vendor_info($vendor_id, 'full_name');
			
			$purchase_detail = "SELECT * from purchase_return_detail WHERE purchase_rt_id='".$purchase_rt_id."'";
			$purchase_detail_result = $db->query($purchase_detail) or die($db->error);
			
			$receiveable = 0;
			$received = 0;
			$items = 0;
			
			while($purchase_detail_row = $purchase_detail_result->fetch_array()) {
				$inventory_id = $purchase_detail_row['inventory_id'];
				$debt_id = $purchase_detail_row['debt_id'];
				
				//Inventory q?uery.
				$inventory_query = "SELECT * from inventory WHERE inventory_id='".$inventory_id."'";
				$inventory_result = $db->query($inventory_query) or die($db->error);
				$inventory_row = $inventory_result->fetch_array();
				
				$items += $inventory_row['out_inv'];
				
				//Inventory q?uery.
				$debt_query = "SELECT * from debts WHERE debt_id='".$debt_id."'";
				$debt_result = $db->query($debt_query) or die($db->error);
				$debt_row = $debt_result->fetch_array();
				
				$receiveable += $debt_row['paid'];
				$received += $debt_row['payable'];
					
			}//purchase detail loop.
			
			$content .= '<tr><td>';
			$content .= $purchase_rt_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $agent_name;
			$content .= '</td><td>';
			$content .= $vendor_name;
			$content .= '</td><td>';
			$content .= $invoice_no;
			$content .= '</td><td>';
			$content .= $memo;
			$content .= '</td><td>';
			$content .= $payment_status;
			$content .= '</td><td>';
			$content .= $items;
			$content .= '</td><td>';
			$content .= number_format($receiveable);
			$content .= '</td><td>';
			$content .= number_format($received);
			$content .= '</td><td>';
			$content .= '<a href="reports/view_purchase_return_invoice.php?purchase_rt_id='.$purchase_rt_id.'" target="_blank">View</a>';
			$content .= '</td>';
				if(partial_access('admin')) { 
				$content .= '<td>';
				$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_purchase_return" value="'.$purchase_rt_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Delete">';
				$content .= '</form>';
				$content .= '</td>'; }
				$content .= '</tr>';	
		}//main_while loop
		echo $content;
	}//list_all purchases function ends here.
		
	function add_return_detail($purchase_rt_id, $price_id, $inventory_id, $debt_id, $reason_id) {
		global $db;	
		$query = "INSERT into purchase_return_detail(purchase_detail_id, purchase_rt_id, store_id, price_id, inventory_id, debt_id, reason_id) VALUES(NULL, '".$purchase_rt_id."', '".$_SESSION['store_id']."', '".$price_id."', '".$inventory_id."', '".$debt_id."', '".$reason_id."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add purchase detail function ends here.	
	
	function view_purchase_return_invoice($purchase_rt_id) {
		global $db;
		/*Products Detail.*/
		$sale_detail_query = "SELECT * from purchase_return_detail WHERE purchase_rt_id='".$purchase_rt_id."' AND store_id='".$_SESSION['store_id']."'";
		$sale_detail_result = $db->query($sale_detail_query) or die($db->error);
	
		$grandTotal = 0;
		$received = 0;
		$rows = '';
	
		while($sale_detail_row = $sale_detail_result->fetch_array()) {
			 
			$price_id = $sale_detail_row['price_id'];
			$inventory_id = $sale_detail_row['inventory_id'];
			$credit_id = $sale_detail_row['debt_id'];
			$reason_id = $sale_detail_row['reason_id'];
			
			$inventoryQuery = "SELECT * from inventory WHERE inventory_id='".$inventory_id."'";
			$inventoryResult = $db->query($inventoryQuery) or die($db->error);
			$inventoryRow = $inventoryResult->fetch_array();
			$qty = $inventoryRow['out_inv'];
			$product_id = $inventoryRow['product_id'];
			
			$pductQuery = "SELECT * from products WHERE product_id='".$product_id."'";
			$productResult = $db->query($pductQuery) or die($db->error);
			$productRow = $productResult->fetch_array();
			
			$pId = $productRow['product_manual_id'];
			$pName = $productRow['product_name'];
		
			$priceQuery = "SELECT * from price WHERE price_id='".$price_id."'";
			$priceResult = $db->query($priceQuery) or die($db->error);
			$priceRow = $priceResult->fetch_array();
		
			$creditQuery = "SELECT * from debts WHERE debt_id='".$credit_id."'";
			$creditResult = $db->query($creditQuery) or die($db->error);
			$creditRow = $creditResult->fetch_array();
		
			$price = $priceRow['cost'];
			$grandTotal += ($price*$qty);
			$received += $creditRow['payable'];
				
			$rows .= "<tr><td>";
			$rows .= $pId;
			$rows .= "</td><td>";
			$rows .= $pName;
			$rows .= "</td><td>";
			$rows .= $price;
			$rows .= "</td><td>";
			$rows .= $qty;
			$rows .= "</td><td>";
			$rows .= $qty*$price;
			$rows .= "</td></tr>";
		}
		$reason_obj = new Returnreason;
		$reason_title = $reason_obj->get_reason_info($reason_id, 'title');
		
		$rows .= "<tr><td colspan='6'><strong>Reason: </strong>".$reason_title."</td></tr>";
		$return_message = array(
			"rows" => $rows,
			"grand_total" => $grandTotal,
			"received_amount" => $received
		);
		return $return_message;
	}//view purchase invoice ends here.
	
	function delete_purchase_return($purchase_rt_id) {
		global $db;
		
		$query = "DELETE FROM purchase_returns WHERE purchase_rt_id='".$purchase_rt_id."'";
		$result = $db->query($query) or die($db->error);
		
		$query = "SELECT * from purchase_return_detail WHERE purchase_rt_id='".$purchase_rt_id."'";
		$result_detail = $db->query($query) or die($db->error);	
		
		while($row = $result_detail->fetch_array()) { 
			extract($row);
			
			$delete[] = "DELETE FROM price WHERE price_id='".$price_id."'";
			$delete[] = "DELETE FROM inventory WHERE inventory_id='".$inventory_id."'";
			$delete[] = "DELETE FROM debts WHERE debt_id='".$debt_id."'";
			
			foreach($delete as $query) { 
				$result = $db->query($query) or die($db->error);
			}
		}//main loop ends here.
		$delete = "DELETE FROM purchase_return_detail WHERE purchase_rt_id='".$purchase_rt_id."'";
		$result = $db->query($delete) or die($db->error);
		
		$delete = "DELETE FROM vendor_log WHERE transaction_type='Purchase Return' AND type_table_id='".$purchase_rt_id."'";
		$result = $db->query($delete) or die($db->error);
		
		$delete = "DELETE FROM vendor_log WHERE transaction_type='Invoice Return' AND type_table_id='".$purchase_rt_id."'";
		$result = $db->query($delete) or die($db->error);
		
		return "Purchase return was deleted successfuly.";
	}//delete_sale ends here.
		
}//Purchase Class Ends here.