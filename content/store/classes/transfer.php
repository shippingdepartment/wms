<?php
//Purchase Class

class Transfer { 

	function get_transfer_info($transfer_id, $term) { 
		global $db;
		$query = "SELECT * from transfers WHERE transfer_id='".$transfer_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}
	function get_transfer_details_info($transfer_id, $term) { 
		global $db;
		$query = "SELECT * from transfer_approved WHERE transfer_id='".$transfer_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}
	
	function max_product_id() {
		global $db;
		$query = "SELECT MAX(product_id) + 1 as maxId FROM products";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row['maxId'];
	}
	
	function add_product_to_destination_warehouse($product_id, $destination_warehouse) { 
		global $db;
		$query = "INSERT INTO products(product_manual_id, product_name, supplier_id, product_unit, category_id, tax_id, alert_units, warehouse_id) SELECT  product_manual_id, product_name, supplier_id, product_unit, category_id, tax_id, alert_units,".$destination_warehouse." FROM products WHERE product_id=".$product_id;
		$result = $db->query($query) or die($db->error);
		$prId = $db->insert_id;
		//inserting values into price table.
		$query_price = "INSERT into price( cost, selling_price, warehouse_id, product_id) SELECT cost, selling_price, ".$destination_warehouse.", ".$prId." FROM price WHERE product_id = ".$product_id; 
		$result_price = $db->query($query_price) or die($db->error);
			
		//inserting product rates table.
		$query_rate = "INSERT into product_rates (default_rate, level_1, level_2, level_3, level_4, level_5, store_id, product_id) SELECT default_rate, level_1, level_2, level_3, level_4, level_5, ".$destination_warehouse.", ".$prId." FROM product_rates WHERE product_id = ".$product_id;
		$result_rate = $db->query($query_rate) or die($db->error);
			
		//inserting dimensions
		$query_dimensions = "INSERT into dimensions (product_id, long_pr, larg, haut, poids) SELECT ".$prId.", long_pr, larg, haut, poids FROM dimensions WHERE product_id = ".$product_id;
		$result_dimensions = $db->query($query_dimensions) or die($db->error);
		
		return $prId;
		
	}
	
	function get_reception_details_info($transfer_id, $term) { 
		global $db;
		$query = "SELECT * from transfer_received WHERE transfer_id='".$transfer_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	//add purchase functions starts here.
	function add_transfer($datetime, $warehouse_id, $bureau_id) { 
		global $db;
		
		$query = "INSERT into transfers(transfer_id, datetime, warehouse_id, destination_id, agent_id, approved, received) VALUES(NULL, '".$datetime."', '".$warehouse_id."', '".$bureau_id."', '".$_SESSION['user_id']."','0','0')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	
	function add_inventory($inn, $out_inv, $product_id, $transfer_id) {
		global $db;
		//$datetime = strtotime(date());
		$datetime = date('Y-m-d');
		$query = "INSERT into inventory(inventory_id, dateinventory, inn, out_inv, product_id, warehouse_id, transfer_id) VALUES(NULL, '".$datetime."', '".$inn."', '".$out_inv."', '".$product_id."', '".$_SESSION['warehouse_id']."', '".$transfer_id."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;	
	}
	
	function add_transfer_detail($transfer_id, $bureau_id, $product_id, $qty, $volume, $poids) {
		global $db;	
		$query = "INSERT into transfer_detail(transfer_detail_id, transfer_id, bureau_id, product_id, qty, volume, poids) VALUES(NULL, '".$transfer_id."', '".$bureau_id."', '".$product_id."', '".$qty."', '".$volume."', '".$poids."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add purchase detail function ends here.	
	
	function view_transfer_invoice($transfer_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from transfer_detail WHERE transfer_id='".$transfer_id."' ";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		//$grandTotal = 0;
		//$paid = 0;
		$rows = '';
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			
			$qty = $tr_detail_row['qty'];
			$product_id = $tr_detail_row['product_id'];
			
			$pductQuery = "SELECT * from products WHERE product_id='".$product_id."'";
			$productResult = $db->query($pductQuery) or die($db->error);
			$productRow = $productResult->fetch_array();
			
			$pId = $productRow['product_manual_id'];
			$pName = $productRow['product_name'];
		
			
				
			$rows .= "<tr><td>";
			$rows .= $pId;
			$rows .= "</td><td>";
			$rows .= $pName;
			$rows .= "</td><td>";
			$rows .= $qty;
			$rows .= "</td></tr>";
		}
		$return_message = array(
			"rows" => $rows
		);
		return $return_message;
	}//view purchase invoice ends here.
	
	function list_periodical_transfers($start_date, $end_date) { 
		global $db;
		
		$from = $start_date;
		$to = $end_date;
		
		$query = "SELECT * from transfers WHERE store_id='".$_SESSION['store_id']."' AND datetime between '".$from."' AND '".$to."' ORDER by transfer_id DESC";
		$result = $db->query($query) or die($db->error);
		
		
		$items_received	 	 = 0;
		//$purchase_amount 	 = 0;
		//$paid_amount 		 = 0;
				
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$warehouses = new Warehouse;
			$warehouse_name = $Warehouses->get_warehouse_info($warehouse_id, 'name');
			
			$transfer_detail = "SELECT * from transfer_detail WHERE transfer_id='".$transfer_id."'";
			$purchase_detail_result = $db->query($purchase_detail) or die($db->error);
			
			//$payable = 0;
			//$paid = 0;
			$items = 0;
			
			while($transfer_detail_row = $transfer_detail_result->fetch_array()) {
				$inventory_id = $transfer_detail_row['inventory_id'];
				//$debt_id = $purchase_detail_row['debt_id'];
				
				//Inventory q?uery.
				$inventory_query = "SELECT * from inventory WHERE inventory_id='".$inventory_id."'";
				$inventory_result = $db->query($inventory_query) or die($db->error);
				$inventory_row = $inventory_result->fetch_array();
				
				$items += $inventory_row['inn'];
				
				//Dept q?uery.
				/*$debt_query = "SELECT * from debts WHERE debt_id='".$debt_id."'";
				$debt_result = $db->query($debt_query) or die($db->error);
				$debt_row = $debt_result->fetch_array();
				
				$payable += $debt_row['payable'];
				$paid += $debt_row['paid'];*/
					
			}//purchase detail loop.
			
			$items_received		= $items_received+$items;
			//$purchase_amount	= $purchase_amount+$payable;
			//$paid_amount 		= $paid_amount+$paid;
			
			$content .= '<tr><td>';
			$content .= $transfer_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $warehouse_name;
			$content .= '</td><td>';
			$content .= $_SESSION['store_name'];
			$content .= '</td><td>';
			$content .= $items;
			/*$content .= '</td><td class="text-right">';
			$content .= number_format($payable, 2);
			$content .= '</td><td class="text-right">';
			$content .= number_format($paid, 2);*/
			$content .= '</td>';
			$content .= '</tr>';	
		}//main_while loop
		
		$output = array( 
			"content" 			=> $content,
			"items_qty" 		=> $items_received
		);
		
		return $output;
	}//list_all purchases function ends here.
	
	function list_all_transfers($warehouse_id) { 
		global $db;
		$store_name='';
		$approved = 0;
		
		$query = "SELECT * from transfers WHERE warehouse_id='".$warehouse_id."' ORDER by transfer_id DESC";
		
		$result = $db->query($query) or die($db->error);
	
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$wid = $row['warehouse_id'];
			$did = $row['destination_id'];
			$approved = $row['approved'];
			$received = $row['received'];
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$warehouses = new Warehouse;
			$destination_name = $warehouses->get_warehouse_info($did, 'name');
			$from_name = $warehouses->get_warehouse_info($wid, 'name');
			
			$transfer_detail = "SELECT * from transfer_detail WHERE transfer_id='".$transfer_id."'";
			$transfer_detail_result = $db->query($transfer_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			
			while($transfer_detail_row = $transfer_detail_result->fetch_array()) {
				
				$items += $transfer_detail_row['qty'];
				$volume += $transfer_detail_row['volume'];
				$weight += $transfer_detail_row['poids'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $transfer_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $from_name;
			$content .= '</td><td>';
			$content .= $destination_name;
			$content .= '</td><td>';
			$content .= $volume.' m<sup>3';
			$content .= '</td><td>';
			$content .= $weight.' Kg';
			$content .= '</td><td>';
			$content .= '<a href="reports/transferdetails.php?tid='.$transfer_id.'" target="_blank" style="color:#0000FF;">Details</a>';
			$content .= '</td>';
			if($approved == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || (partial_access('transfer'))) { 
					$content .= '<a href="approvetransfer.php?tid='.$transfer_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Click to Approve</a><br>';
					}
					$content .= '</td>';
					$content .= '<td><i class="fa fa-remove" style="font-size:16px;color:red"></i> Not Received</td><td>';
					if ((partial_access('admin')) || (partial_access('transfers'))) { 
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_transfer" value="'.$transfer_id.'">';
					$content .= '<button type="submit" class="btn btn-danger" value="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>';
					$content .= '</form>';
					}
					$content .= '</td>'; 
					
					$content .= '</tr>';	
			}
			elseif($approved == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approved<br></td>';
					$content .= '<td>';
					$content .= '<a href="reports/approvedtransfer.php?tid='.$transfer_id.'" target="_blank"><i class="fa fa-print" style="font-size:16px"></i> Print</a><br>';
					$content .= '</td>'; 
					$content .= '<td>';
					if($received == 0){
						if((partial_access('admin')) || (partial_access('transfers'))) { 
							$content .= '<i class="fa fa-remove" style="font-size:16px;color:red"></i> Not Received Yet';
							}
					}
					elseif($received == 1){ 
						$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Received <a href="reports/transfer_received.php?transfer_id='.$transfer_id.'"><i class="fa fa-print" style="font-size:16px"></i></a><br>';  
						}
						$content .= '</td>';}
						$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all transfers function ends here.
	echo $content;
	}
	
	function print_list_transfers($warehouse_id) { 
		global $db;
		$store_name='';
		$approved = 0;
		
		$query = "SELECT * from transfers WHERE warehouse_id='".$warehouse_id."' ORDER by transfer_id DESC";
		
		$result = $db->query($query) or die($db->error);
	
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$wid = $row['warehouse_id'];
			$did = $row['destination_id'];
			$approved = $row['approved'];
			$received = $row['received'];
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$warehouses = new Warehouse;
			$destination_name = $warehouses->get_warehouse_info($did, 'name');
			$from_name = $warehouses->get_warehouse_info($wid, 'name');
			
			$transfer_detail = "SELECT * from transfer_detail WHERE transfer_id='".$transfer_id."'";
			$transfer_detail_result = $db->query($transfer_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			
			while($transfer_detail_row = $transfer_detail_result->fetch_array()) {
				
				$items += $transfer_detail_row['qty'];
				$volume += $transfer_detail_row['volume'];
				$weight += $transfer_detail_row['poids'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $transfer_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $destination_name;
			$content .= '</td><td>';
			$content .= $volume.' m<sup>3';
			$content .= '</td><td>';
			$content .= $weight.' Kg';
			$content .= '</td>';
			if($approved == 0){
				$content .= '<td>Waiting approval from source warehouse.</td>';
			}
			elseif($approved == 1){
				if($received == 0){
					$content .= '<td>Waiting reception confirmation from destination warehouse.</td>';
				}
				elseif($received == 1){ 
					$content .= '<td>Tranfer received on destination warehouse.</td>';
				}
			}				
			$content .= '</tr>';
		}
		if($content =='') {
			$content ='<tr><td colspan="6"><i class="fa fa-exclamation-triangle"></i> No Sent Transfers found</td></tr>';
		}
		
	echo $content;
	}
	
	
	
	function list_all_transfers_received($warehouse_id) { 
		global $db;
		$store_name='';
		$approved = 0;
		
		$query = "SELECT * from transfers WHERE destination_id='".$warehouse_id."' AND approved='1' ORDER by transfer_id DESC";
		
		$result = $db->query($query) or die($db->error);
	
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$wid = $row['warehouse_id'];
			$did = $row['destination_id'];
			$approved = $row['approved'];
			$received = $row['received'];
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$warehouses = new Warehouse;
			$destination_name = $warehouses->get_warehouse_info($did, 'name');
			$from_name = $warehouses->get_warehouse_info($wid, 'name');
			
			$transfer_detail = "SELECT * from transfer_detail WHERE transfer_id='".$transfer_id."'";
			$transfer_detail_result = $db->query($transfer_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			
			while($transfer_detail_row = $transfer_detail_result->fetch_array()) {
				
				$items += $transfer_detail_row['qty'];
				$volume += $transfer_detail_row['volume'];
				$weight += $transfer_detail_row['poids'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $transfer_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $from_name;
			$content .= '</td><td>';
			$content .= $destination_name;
			$content .= '</td><td>';
			$content .= $volume.' m<sup>3';
			$content .= '</td><td>';
			$content .= $weight.' Kg';
			$content .= '</td><td>';
			$content .= '<a href="reports/transferdetails.php?tid='.$transfer_id.'" target="_blank" style="color:#0000FF;">Details</a>';
			$content .= '</td>';
			if($approved == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || (partial_access('transfer'))) { 
					$content .= '<a href="approvetransfer.php?tid='.$transfer_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Click to Approve</a><br>';
					}
					$content .= '</td>';
					$content .= '<td><i class="fa fa-remove" style="font-size:16px;color:red"></i> Not Received</td><td>';
					if ((partial_access('admin')) || (partial_access('transfers'))) { 
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_transfer" value="'.$transfer_id.'">';
					$content .= '<button type="submit" class="btn btn-danger" value="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>';
					$content .= '</form>';
					}
					$content .= '</td>'; 
					
					$content .= '</tr>';	
			}
			elseif($approved == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approved<br></td>';
					$content .= '<td>';
					$content .= '<a href="reports/transferdetails.php?tid='.$transfer_id.'" target="_blank"><i class="fa fa-print" style="font-size:16px"></i> Print</a><br>';
					$content .= '</td>'; 
					$content .= '<td>';
					if($received == 0){
						if((partial_access('admin')) || (partial_access('transfers'))) { 
							$content .= '<a href="receivetransfer.php?tid='.$transfer_id.'" target="_blank"><i class="fa fa-share-square-o" style="font-size:16px;color:orange"></i> Receive</a><br>';
							}
					}
					elseif($received == 1){ 
						$content .= '<i class="fa fa-share-square" style="font-size:16px;color:green"></i> Received <a href="reports/transfer_received.php?transfer_id='.$transfer_id.'" target="_blank"><i class="fa fa-print" style="font-size:16px"></i></a><br>';  
						}
						$content .= '</td>';}
						$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all transfers function ends here.
	echo $content;
	}
	
	function print_transfers_received($warehouse_id) { 
		global $db;
		$store_name='';
		$approved = 0;
		
		$query = "SELECT * from transfers WHERE destination_id='".$warehouse_id."' AND approved='1' ORDER by transfer_id DESC";
		
		$result = $db->query($query) or die($db->error);
	
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$wid = $row['warehouse_id'];
			$did = $row['destination_id'];
			$approved = $row['approved'];
			$received = $row['received'];
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$warehouses = new Warehouse;
			$destination_name = $warehouses->get_warehouse_info($did, 'name');
			$from_name = $warehouses->get_warehouse_info($wid, 'name');
			
			$transfer_detail = "SELECT * from transfer_detail WHERE transfer_id='".$transfer_id."'";
			$transfer_detail_result = $db->query($transfer_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			
			while($transfer_detail_row = $transfer_detail_result->fetch_array()) {
				
				$items += $transfer_detail_row['qty'];
				$volume += $transfer_detail_row['volume'];
				$weight += $transfer_detail_row['poids'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $transfer_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $from_name;
			$content .= '</td><td>';
			$content .= $volume.' m<sup>3';
			$content .= '</td><td>';
			$content .= $weight.' Kg';
			$content .= '</td>';
			if($approved == 0){
				$content .= '<td>Waiting approval from source warehouse.</td>';
			}
			elseif($approved == 1){
				if($received == 0){
					$content .= '<td>Waiting reception confirmation from destination warehouse.</td>';
				}
				elseif($received == 1){ 
					$content .= '<td>Tranfer received on destination warehouse.</td>';
				}
			}				
			$content .= '</tr>';
		}
		if($content =='') {
			$content ='<tr><td colspan="6"><i class="fa fa-exclamation-triangle"></i> No Received Transfers found</td></tr>';
		}
		
	echo $content;
	}
	
	function list_pof_transfers($store_id) { 
		global $db;
		$store_name='';
		$approved = 0;
		if ($store_id == "0") {
		$query = "SELECT * from transfers WHERE store_id='".$store_id."' ORDER by transfer_id DESC ";
		
		}
		else
		{
			$query = "SELECT * from transfers WHERE store_id='".$store_id."' ORDER by transfer_id DESC";
		}
		$result = $db->query($query) or die($db->error);
		
		
		
		/*$query1 = "SELECT * from stores WHERE store_id='".$store_id."' ";
		$result1 = $db->query($query1) or die($db->error);
		$row1 = $result1->fetch_array();
		$store_name = $row1['store_name'];*/
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$sid = $row['store_id'];
			$approved = $row['approved'];
			$received = $row['received'];
			$query1 = "SELECT * from stores WHERE store_id='".$sid."' ";
			$result1 = $db->query($query1) or die($db->error);
			$row1 = $result1->fetch_array();
			$store_name = $row1['store_name'];
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$warehouses = new Warehouse;
			$warehouse_name = $warehouses->get_warehouse_info($warehouse_id, 'name');
			
			$transfer_detail = "SELECT * from transfer_detail WHERE transfer_id='".$transfer_id."'";
			$transfer_detail_result = $db->query($transfer_detail) or die($db->error);
			
			$items = 0;
			
			while($transfer_detail_row = $transfer_detail_result->fetch_array()) {
				
				$items += $transfer_detail_row['qty'];
				
								
			}//purchase detail loop.
			
			$content .= '<tr><td>';
			$content .= $transfer_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $agent_name;
			$content .= '</td><td>';
			$content .= $warehouse_name;
			$content .= '</td><td>';
			$content .= $store_name;
			$content .= '</td><td>';
			$content .= $memo;
			$content .= '</td><td>';
			/*$content .= $payment_status;
			$content .= '</td><td>';*/
			$content .= $items;
			$content .= '</td><td>';
			/*$content .= number_format($payable);
			$content .= '</td><td>';
			$content .= number_format($paid);
			$content .= '</td><td>';*/
			$content .= '<a href="reports/view_transfer_invoice.php?transfer_id='.$transfer_id.'" target="_blank">Voir</a>';
			$content .= '</td>';
			if($approved == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || (partial_access('transfers'))) { 
					$content .= '<a href="approve_transfer.php?tid='.$transfer_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Approuver</a><br>';
					}
					$content .= '</td><td>';
					if ((partial_access('admin')) || (partial_access('transfers'))) { 
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_transfer" value="'.$transfer_id.'">';
					$content .= '<input type="submit" class="btn btn-default btn-sm" value="Supprimer">';
					$content .= '</form>';
					}
					$content .= '</td>'; 
					$content .= '<td>Non Reçu</td>';
					$content .= '</tr>';	
			}
			elseif($approved == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approuvé<br></td>';
					$content .= '<td>';
					$content .= '<a href="reports/transfer_invoice.php?transfer_id='.$transfer_id.'" target="_blank"><i class="fa fa-print" style="font-size:16px"></i> Imprimer</a><br>';
					$content .= '</td>'; 
					$content .= '<td>';
					if($received == 0){
						if((partial_access('admin')) || (partial_access('transfers'))) { 
							$content .= '<a href="receive_transfer.php?tid='.$transfer_id.'" target="_self"><i class="fa fa-share-square-o" style="font-size:16px;color:orange"></i> Recevoir</a><br>';
							}
					}
					elseif($received == 1){ 
						$content .= '<i class="fa fa-share-square" style="font-size:16px;color:green"></i> Reçu <a href="reports/transfer_received.php?transfer_id='.$transfer_id.'"><i class="fa fa-print" style="font-size:16px"></i></a><br>';  
						}
						$content .= '</td>';}
						$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all purchases function ends here.
	echo $content;
	}
	
	function list_unite_transfers($store_id) { 
		global $db;
		$store_name='';
		$approved = 0;
		if ($store_id == "0") {
		$query = "SELECT * from transfers WHERE warehouse_id='".$store_id."' ORDER by transfer_id ASC ";
		
		}
		else
		{
			$query = "SELECT * from transfers WHERE warehouse_id='".$store_id."' ORDER by transfer_id ASC";
		}
		$result = $db->query($query) or die($db->error);
		
		
		
		/*$query1 = "SELECT * from stores WHERE store_id='".$store_id."' ";
		$result1 = $db->query($query1) or die($db->error);
		$row1 = $result1->fetch_array();
		$store_name = $row1['store_name'];*/
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$sid = $row['store_id'];
			$approved = $row['approved'];
			$received = $row['received'];
			$query1 = "SELECT * from stores WHERE store_id='".$sid."' ";
			$result1 = $db->query($query1) or die($db->error);
			$row1 = $result1->fetch_array();
			$store_name = $row1['store_name'];
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$warehouses = new Warehouse;
			$warehouse_name = $warehouses->get_warehouse_info($warehouse_id, 'name');
			
			$transfer_detail = "SELECT * from transfer_detail WHERE transfer_id='".$transfer_id."' ";
			$transfer_detail_result = $db->query($transfer_detail) or die($db->error); 
			
			$items = 0;
			
			while($transfer_detail_row = $transfer_detail_result->fetch_array()) {
				
				$items += $transfer_detail_row['qty'];
				
								
			}//purchase detail loop.
			
			$content .= '<tr><td>';
			$content .= $transfer_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $agent_name;
			$content .= '</td><td>';
			$content .= $warehouse_name;
			$content .= '</td><td>';
			$content .= $store_name;
			$content .= '</td><td>';
			$content .= $memo;
			$content .= '</td><td>';
			/*$content .= $payment_status;
			$content .= '</td><td>';*/
			$content .= $items;
			$content .= '</td><td>';
			/*$content .= number_format($payable);
			$content .= '</td><td>';
			$content .= number_format($paid);
			$content .= '</td><td>';*/
			$content .= '<a href="reports/view_transfer_invoice.php?transfer_id='.$transfer_id.'" target="_blank">Voir</a>';
			$content .= '</td>';
			if($approved == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || (partial_access('transfers'))) { 
					$content .= '<a href="approve_transfer.php?tid='.$transfer_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Approuver</a><br>';
					}
					$content .= '</td><td>';
					if ((partial_access('admin')) || (partial_access('transfers'))) { 
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_transfer" value="'.$transfer_id.'">';
					$content .= '<input type="submit" class="btn btn-default btn-sm" value="Supprimer">';
					$content .= '</form>';
					}
					$content .= '</td>'; 
					$content .= '<td>Non Reçu</td>';
					$content .= '</tr>';	
			}
			elseif($approved == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approuvé<br></td>';
					$content .= '<td>';
					$content .= '<a href="reports/transfer_invoice.php?transfer_id='.$transfer_id.'" target="_blank"><i class="fa fa-print" style="font-size:16px"></i> Imprimer</a><br>';
					$content .= '</td>'; 
					$content .= '<td>';
					if($received == 0){
						if((partial_access('admin')) || (partial_access('transfer'))) { 
							$content .= '<a href="receive_transfer.php?tid='.$transfer_id.'" target="_self"><i class="fa fa-share-square-o" style="font-size:16px;color:orange"></i> Recevoir</a><br>';
							}
					}
					elseif($received == 1){ 
						$content .= '<i class="fa fa-share-square" style="font-size:16px;color:green"></i> Reçu <a href="reports/transfer_received.php?transfer_id='.$transfer_id.'"><i class="fa fa-print" style="font-size:16px"></i></a><br>';  
						}
						$content .= '</td>';}
						$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all purchases function ends here.
	echo $content;
	}
	
	
	function delete_transfer($transfer_id) {
		global $db;
		
		$query = "DELETE FROM transfers WHERE transfer_id='".$transfer_id."'";
		$result = $db->query($query) or die($db->error);
		
		$delete = "DELETE FROM transfer_detail WHERE transfer_id='".$transfer_id."'";
		$result = $db->query($delete) or die($db->error);
		
		return " <div class='alert alert-success'>Transfer N# ".$transfer_id." is deleted successfully !!.</div>";
	}//delete_sale ends here.
	
	function approve_transfer_details($transfer_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from transfer_detail WHERE transfer_id='".$transfer_id."'";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		$rows = '';
		$nb=0;
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			
			$qty = $tr_detail_row['qty'];
			$product_id = $tr_detail_row['product_id'];
			
			$pductQuery = "SELECT * from products WHERE product_id='".$product_id."'";
			$productResult = $db->query($pductQuery) or die($db->error);
			$productRow = $productResult->fetch_array();
			
			$pcode = $productRow['product_manual_id'];
			$pId = $product_id;
			$pName = $productRow['product_name'];
		
				
			$rows .= '<tr><td>';
			$rows .= '<input type="hidden" name="pid[]"  value="'.$pId.'" >'.$pcode;
			$rows .= '</td><td>';
			$rows .= '<input type="hidden" name="pname[]"  value="'.$pName.'" >'.$pName;
			$rows .= '</td><td>';
			$rows .= '<input type="hidden" name="qty[]"  value="'.$qty.'" >'.$qty;
			$rows .= '</td><td>';
			$rows .= '<input type="text" name="qte_appr[]" id="qteapp" value="" required >';
			$rows .= '</td></tr>';
			$nb = $nb+1;

		}
		//$rows .= '<tr><td>';
		//$rows .= '';
		//$rows .= '</td><td>';
		//$rows .= '</td><td>';
		//$rows .= '</td><td>';
		//$rows .= '</td></tr>';
		
		echo $rows;
		echo '<input type="text" style="display: none" name="nb" id="nb" value="'.$nb.'"  >';
		//'echo $nb;
		/*$return_message = array(
			"rows" => $rows
		);
		return $return_message;*/
	}//view purchase invoice ends here.
	function add_transfer_approve($transfer_id, $datetime, $product_id, $qty1, $qty2) { 
		global $db;
		
		$query = "INSERT into transfer_approved(transfer_approved_id, transfer_id, date_approve, bureau_id, product_id, qty, qty_appr, agent_id) VALUES(NULL, '".$transfer_id."', '".$datetime."', '".$_SESSION['warehouse_id']."', '".$product_id."', '".$qty1."', '".$qty2."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	
	//Update Approved = OK
	function update_transfer($transfer_id) { 
		global $db;
		
		$query = "UPDATE transfers SET approved = '1' WHERE transfer_id='".$transfer_id."'";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add_purchase ends here. returns purchase id.
	
	//Update Received = OK
	function receive_transfer($transfer_id) { 
		global $db;
		
		$query = "UPDATE transfers SET received = '1' WHERE transfer_id='".$transfer_id."'";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	
	function approved_transfer_invoice($transfer_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from transfer_approved WHERE transfer_id='".$transfer_id."' ";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		$rows = '';
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			
			$qty = $tr_detail_row['qty'];
			$qty1 = $tr_detail_row['qty_appr'];
			$product_id = $tr_detail_row['product_id'];
			
			$pductQuery = "SELECT * from products WHERE product_id='".$product_id."'";
			$productResult = $db->query($pductQuery) or die($db->error);
			$productRow = $productResult->fetch_array();
			
			$pId = $productRow['product_manual_id'];
			$pName = $productRow['product_name'];
						
			$rows .= "<tr><td>";
			$rows .= $pId;
			$rows .= "</td><td>";
			$rows .= $pName;
			$rows .= "</td><td>";
			$rows .= $qty;
			$rows .= "</td><td>";
			$rows .= $qty1;
			$rows .= "</td></tr>";
		}
		$return_message = array(
			"rows" => $rows
		);
		return $return_message;
	}//view purchase invoice ends
	
	function receive_transfer_details($transfer_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from transfer_approved WHERE transfer_id='".$transfer_id."'";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		$rows = '';
		$nb=0;
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			
			$qty = $tr_detail_row['qty'];
			$qty_appr = $tr_detail_row['qty_appr'];
			$product_id = $tr_detail_row['product_id'];
			
			$pductQuery = "SELECT * from products WHERE product_id='".$product_id."'";
			$productResult = $db->query($pductQuery) or die($db->error);
			$productRow = $productResult->fetch_array();
			
			$pcode = $productRow['product_manual_id'];
			$pId = $product_id;
			$pName = $productRow['product_name'];
		
				
			$rows .= '<tr><td>';
			$rows .= '<input type="hidden" name="pid[]"  value="'.$pId.'" >'.$pcode;
			$rows .= '</td><td>';
			$rows .= '<input type="hidden" name="pname[]"  value="'.$pName.'" >'.$pName;
			$rows .= '</td><td>';
			$rows .= '<input type="hidden" name="qty[]"  value="'.$qty.'" >'.$qty;
			$rows .= '</td><td>';
			$rows .= '<input type="text" name="qte_appr[]" id="qteapp" value="'.$qty_appr.'" required >';
			$rows .= '</td></tr>';
			$nb = $nb+1;

		}
		//$rows .= '<tr><td>';
		//$rows .= '';
		//$rows .= '</td><td>';
		//$rows .= '</td><td>';
		//$rows .= '</td><td>';
		//$rows .= '</td></tr>';
		
		echo $rows;
		echo '<input type="text" style="display: none" name="nb" id="nb" value="'.$nb.'"  >';
		//'echo $nb;
		/*$return_message = array(
			"rows" => $rows
		);
		return $return_message;*/
	}//view purchase invoice ends here.
	
	
	function received_transfer_invoice($transfer_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from transfer_received WHERE transfer_id='".$transfer_id."' ";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		$rows = '';
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			
			$qty = $tr_detail_row['qty_appr'];
			$product_id = $tr_detail_row['product_id'];
			
			$tr_detail_query1 = "SELECT * from transfer_approved WHERE transfer_id='".$transfer_id."' AND product_id='".$product_id."' ";
			$tr_detail_result1 = $db->query($tr_detail_query1) or die($db->error);
			$tr_detail_row1= $tr_detail_result1->fetch_array();
			$qty1 = $tr_detail_row1['qty_appr'];
			$units = $qty-$qty1;
			
			$pductQuery = "SELECT * from products WHERE product_id='".$product_id."'";
			$productResult = $db->query($pductQuery) or die($db->error);
			$productRow = $productResult->fetch_array();
			
			$pId = $productRow['product_manual_id'];
			$pName = $productRow['product_name'];
						
			$rows .= "<tr><td>";
			$rows .= $pId;
			$rows .= "</td><td>";
			$rows .= $pName;
			$rows .= "</td><td>";
			$rows .= $qty;
			$rows .= "</td><td>";
			$rows .= $qty1;
			$rows .= "</td><td>";
			if($units == 0) {
				$rows .= 'Qty Conform';
			} elseif($units > 0) {
				$rows .= 'Lack of '.abs($units).' units';
			} else {
				$rows .= 'Excess of '.abs($units).' units';
			}
			$rows .= "</td>";
			$rows .= "</tr>";
		}
		echo $rows;
		//$return_message = array(
		//	"rows" => $rows
		//);
		//return $return_message;
	}//view purchase invoice ends
	
	
	function add_transfer_reception($transfer_id, $datetime, $product_id, $qty1, $qty2) { 
		global $db;
		
		$query = "INSERT into transfer_received(transfer_received_id, transfer_id, date_reception, bureau_id, product_id, qty, qty_appr, agent_id) VALUES(NULL, '".$transfer_id."', '".$datetime."', '".$_SESSION['warehouse_id']."', '".$product_id."', '".$qty1."', '".$qty2."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	
	function transfer_exist($transfer_id) {
		global $db;	
		$exist = False;
		$query = "SELECT * from transfers WHERE transfer_id='".$transfer_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if($num_rows > 0) { 
			$exist = True;
		} else {
			$exist = False;
		}
		
		return ($exist);
	}
	
	
}//Purchase Class Ends here.