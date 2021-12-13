<?php
//Purchase Class

class Purchase { 

	function get_purchase_info($purchase_id, $term) { 
		global $db;
		$query = "SELECT * from purchases WHERE purchase_id='".$purchase_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	//add purchase functions starts here.
	function add_purchase($datetime, $supp_inv_no, $memo, $vendor_id, $payment_method) { 
		global $db;
		
		$query = "INSERT into purchases(purchase_id, datetime, supp_inv_no, memo, vendor_id, payment_status, store_id, agent_id) VALUES(NULL, '".$datetime."', '".$supp_inv_no."', '".$memo."', '".$vendor_id."', '".$payment_method."', '".$_SESSION['store_id']."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add_purchase ends here. returns purchase id.
	//add purchase functions ends here.
	
	function add_debt($payable, $paid, $vendor_id) { 
		global $db;
		
		$query = "INSERT into debts(debt_id, payable, paid, vendor_id, store_id) VALUES(NULL, '".$payable."', '".$paid."', '".$vendor_id."', '".$_SESSION['store_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add_debt ends here.
	
	function add_inventory($inn, $out_inv, $product_id, $warehouse_id) {
		global $db;
		$query = "INSERT into inventory(inventory_id, inn, out_inv, store_id, product_id, warehouse_id) VALUES(NULL, '".$inn."', '".$out_inv."', '".$_SESSION['store_id']."', '".$product_id."', '".$warehouse_id."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;	
	}//add inventory function ends here.
	
	function add_price($cost, $selling_price, $product_id) {
		global $db;	
		$query = "INSERT into price(price_id, cost, selling_price, store_id, product_id) VALUES(NULL, '".$cost."', '".$selling_price."', '".$_SESSION['store_id']."', '".$product_id."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add price table ends here.
	
	function add_purchase_detail($purchase_id, $price_id, $inventory_id, $debt_id) {
		global $db;	
		$query = "INSERT into purchase_detail(purchase_detail_id, purchase_id, store_id, price_id, inventory_id, debt_id) VALUES(NULL, '".$purchase_id."', '".$_SESSION['store_id']."', '".$price_id."', '".$inventory_id."', '".$debt_id."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add purchase detail function ends here.	
	
	function view_purchase_invoice($purchase_id) {
		global $db;
		
		/*Products Detail.*/
		$pu_detail_query = "SELECT * from purchase_detail WHERE purchase_id='".$purchase_id."' AND store_id='".$_SESSION['store_id']."'";
		$pu_detail_result = $db->query($pu_detail_query) or die($db->error);
	
		$grandTotal = 0;
		$paid = 0;
		$rows = '';
	
		while($pu_detail_row = $pu_detail_result->fetch_array()) { 
			$price_id = $pu_detail_row['price_id'];
			$inventory_id = $pu_detail_row['inventory_id'];
			$debt_id = $pu_detail_row['debt_id'];
		
			$inventoryQuery = "SELECT * from inventory WHERE inventory_id='".$inventory_id."'";
			$inventoryResult = $db->query($inventoryQuery) or die($db->error);
			$inventoryRow = $inventoryResult->fetch_array();
			$qty = $inventoryRow['inn'];
			$product_id = $inventoryRow['product_id'];
			
			$pductQuery = "SELECT * from products WHERE product_id='".$product_id."'";
			$productResult = $db->query($pductQuery) or die($db->error);
			$productRow = $productResult->fetch_array();
			
			$pId = $productRow['product_manual_id'];
			$pName = $productRow['product_name'];
		
			$priceQuery = "SELECT * from price WHERE price_id='".$price_id."'";
			$priceResult = $db->query($priceQuery) or die($db->error);
			$priceRow = $priceResult->fetch_array();
		
			$debtQuery = "SELECT * from debts WHERE debt_id='".$debt_id."'";
			$debtResult = $db->query($debtQuery) or die($db->error);
			$debtRow = $debtResult->fetch_array();
		
			$cost = $priceRow['cost'];
			$grandTotal += $cost*$qty;
			$paid += $debtRow['paid'];
				
			$rows .= "<tr><td>";
			$rows .= $pId;
			$rows .= "</td><td>";
			$rows .= $pName;
			$rows .= "</td><td>";
			$rows .= $cost;
			$rows .= "</td><td>";
			$rows .= $qty;
			$rows .= "</td><td>";
			$rows .= number_format($qty*$cost);
			$rows .= "</td></tr>";
		}
		$return_message = array(
			"rows" => $rows,
			"grand_total" => $grandTotal,
			"paid_amount" => $paid
		);
		return $return_message;
	}//view purchase invoice ends here.
	
	function list_all_purchases() { 
		global $db;
		
		$query = "SELECT * from purchases WHERE store_id='".$_SESSION['store_id']."' ORDER by purchase_id DESC";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$vendors = new Vendor;
			$vendor_name = $vendors->get_vendor_info($vendor_id, 'full_name');
			
			$purchase_detail = "SELECT * from purchase_detail WHERE purchase_id='".$purchase_id."'";
			$purchase_detail_result = $db->query($purchase_detail) or die($db->error);
			
			$payable = 0;
			$paid = 0;
			$items = 0;
			
			while($purchase_detail_row = $purchase_detail_result->fetch_array()) {
				$inventory_id = $purchase_detail_row['inventory_id'];
				$debt_id = $purchase_detail_row['debt_id'];
				
				//Inventory q?uery.
				$inventory_query = "SELECT * from inventory WHERE inventory_id='".$inventory_id."'";
				$inventory_result = $db->query($inventory_query) or die($db->error);
				$inventory_row = $inventory_result->fetch_array();
				
				$items += $inventory_row['inn'];
				
				//Inventory q?uery.
				$debt_query = "SELECT * from debts WHERE debt_id='".$debt_id."'";
				$debt_result = $db->query($debt_query) or die($db->error);
				$debt_row = $debt_result->fetch_array();
				
				$payable += $debt_row['payable'];
				$paid += $debt_row['paid'];
					
			}//purchase detail loop.
			
			$content .= '<tr><td>';
			$content .= $purchase_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $agent_name;
			$content .= '</td><td>';
			$content .= $vendor_name;
			$content .= '</td><td>';
			$content .= $supp_inv_no;
			$content .= '</td><td>';
			$content .= $memo;
			$content .= '</td><td>';
			$content .= $payment_status;
			$content .= '</td><td>';
			$content .= $items;
			$content .= '</td><td>';
			$content .= number_format($payable);
			$content .= '</td><td>';
			$content .= number_format($paid);
			$content .= '</td><td>';
			$content .= '<a href="reports/view_purchase_invoice.php?purchase_id='.$purchase_id.'" target="_blank">View</a>';
			$content .= '</td>';
				if(partial_access('admin')) { $content .= '<td>';
				$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_purchase" value="'.$purchase_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Delete">';
				$content .= '</form>';
				$content .= '</td>'; }
				$content .= '</tr>';	
		}//main_while loop
		echo $content;
	}//list_all purchases function ends here.
	
	function delete_purchase($purchase_id) {
		global $db;
		
		$query = "DELETE FROM purchases WHERE purchase_id='".$purchase_id."'";
		$result = $db->query($query) or die($db->error);
		
		$query = "SELECT * from purchase_detail WHERE purchase_id='".$purchase_id."'";
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
		$delete = "DELETE FROM purchase_detail WHERE purchase_id='".$purchase_id."'";
		$result = $db->query($delete) or die($db->error);
		
		$delete = "DELETE FROM vendor_log WHERE transaction_type='Purchase Invoice' AND type_table_id='".$purchase_id."'";
		$result = $db->query($delete) or die($db->error);
		
		$delete = "DELETE FROM vendor_log WHERE transaction_type='Cash Purchase' AND type_table_id='".$purchase_id."'";
		$result = $db->query($delete) or die($db->error);
		
		return "Purchase was deleted successfuly.";
	}//delete_sale ends here.
	
	function purchase_graph_data() { 
		global $db;
		$query = "SELECT * FROM purchases WHERE datetime > DATE_SUB(NOW(), INTERVAL 1 MONTH) AND store_id='".$_SESSION['store_id']."'";
		$result = $db->query($query) or die($db->error);

		$date_set = '';
		$daily_sale = 0;
		$today = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$sale_detail = "SELECT * from purchase_detail WHERE purchase_id='".$purchase_id."'";
			$sale_detail_result = $db->query($sale_detail) or die($db->error);
			$receiveable = 0;
			while($sale_detail_row = $sale_detail_result->fetch_array()) {
				$creditor_id = $sale_detail_row['debt_id'];
				//Inventory q?uery.
				$credit_query = "SELECT * from debts WHERE debt_id='".$creditor_id."'";
				$credit_result = $db->query($credit_query) or die($db->error);
				$credit_row = $credit_result->fetch_array();
				
				$receiveable += $credit_row['payable'];
			}//purchase detail loop.
			
			$datetime = strtotime($datetime);
			$date_pr = date('Y-m-d', $datetime);
			$daily_sale = $receiveable; 
			$today = $date_pr;
			$content[] = Array(
				"date" => $today,
				"total" => $daily_sale
			);
		}//main_while loop
		
		$new_arr = array();
		array_walk($content,function ($v,$k) use(&$new_arr) {
    		array_key_exists($v['date'],$new_arr) ? $new_arr[$v['date']] = $new_arr[$v['date']]+$v['total'] : $new_arr[$v['date']]=$v['total'];
});
	$js_arr = '';
	foreach($new_arr as $key => $value) { 
		if($js_arr != '') { 
			$js_arr .= ', ';
		}
		$js_arr .= '["'.$key.'", '.$value.']';
	}
	echo $js_arr;
}//list_all purchases function ends here.
		
}//Purchase Class Ends here.