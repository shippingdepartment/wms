<?php
//Purchase Class

class Delivery { 

	function get_delivery_info($delivery_id, $term) { 
		global $db;
		$query = "SELECT * from deliveries WHERE delivery_id='".$delivery_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	function get_delivery_details_info($delivery_id, $term) { 
		global $db;
		$query = "SELECT * from delivery_detail WHERE delivery_id='".$delivery_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	//add purchase functions starts here.
	function add_delivery($datetime, $deliverydate, $warehouse_id, $client_id, $client_order_ref) { 
		global $db;
		
		$query = "INSERT into deliveries(delivery_id, datetime, dateissued, warehouse_id, client_id, client_order_ref, user_id, invoiced, delivered) VALUES(NULL, '".$datetime."', '".$deliverydate."', '".$warehouse_id."', '".$client_id."', '".$client_order_ref."', '".$_SESSION['user_id']."','0','0')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	
	function add_inventory($dateinventory, $inn, $out_inv, $product_id, $delivery_id) {
		global $db;
		$query = "INSERT into inventory(inventory_id, dateinventory, inn, out_inv, product_id, warehouse_id, delivery_id) VALUES(NULL, '".$dateinventory."', '".$inn."', '".$out_inv."', '".$product_id."', '".$_SESSION['warehouse_id']."', '".$delivery_id."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;	
	}
	
	function add_delivery_detail($delivery_id, $client_id, $product_id, $qty, $volume, $poids) {
		global $db;	
		$query = "INSERT into delivery_detail(delivery_detail_id, delivery_id, product_id, qty, volume, poids, warehouse_id) VALUES(NULL, '".$delivery_id."', '".$product_id."', '".$qty."', '".$volume."', '".$poids."', '".$_SESSION['warehouse_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add purchase detail function ends here.	
	
	function delivery_exist($delivery_id) {
		global $db;	
	$exist = False;
	$query = "SELECT * from deliveries WHERE delivery_id='".$delivery_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if($num_rows > 0) { 
			$exist = True;
		} else {
			$exist = False;
		}
		
		return ($exist);
	}
	
	function view_delivery_invoice($delivery_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from delivery_detail WHERE delivery_id='".$delivery_id."' ";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		//$grandTotal = 0;
		//$paid = 0;
		$rows = '';
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			$qty = $tr_detail_row['qty'];
			$product_id = $tr_detail_row['product_id'];
			$weight = $tr_detail_row['poids'];
			
			$pductQuery = "SELECT * from products WHERE product_id='".$product_id."'";
			$productResult = $db->query($pductQuery) or die($db->error);
			$productRow = $productResult->fetch_array();
			//$totweight = $qty*$
			$pId = $productRow['product_manual_id'];
			$pName = $productRow['product_name'];
				
			$rows .= "<tr><td>";
			$rows .= $pId;
			$rows .= "</td><td>";
			$rows .= $pName;
			$rows .= "</td><td>";
			$rows .= $qty;
			$rows .= "</td><td>";
			$rows .= number_format($weight,2).' Kg';
			$rows .= "</td></tr>";
		}
		$return_message = array(
			"rows" => $rows
		);
		return $return_message;
	}//view purchase invoice ends here.
	
	function view_packing_invoice($delivery_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from loading_approve WHERE delivery_id='".$delivery_id."' ";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		//$grandTotal = 0;
		//$paid = 0;
		$rows = '';
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			$qty = $tr_detail_row['qty_appr'];
			$product_id = $tr_detail_row['product_id'];
			
			
			$pductQuery = "SELECT * from products, dimensions WHERE (products.product_id = dimensions.product_id) AND (products.product_id='".$product_id."')";
			$productResult = $db->query($pductQuery) or die($db->error);
			$productRow = $productResult->fetch_array();
			//$totweight = $qty*$
			$pId = $productRow['product_manual_id'];
			$pName = $productRow['product_name'];
			$long_pr = $productRow['long_pr'];
			$larg = $productRow['larg'];
			$haut = $productRow['haut'];
			$weight = $productRow['poids'];
			$volume = ($long_pr/100) * ($larg/100) * ($haut/100);
			$totalvolume = $volume*$qty;
				
			$rows .= "<tr><td>";
			$rows .= $pId;
			$rows .= "</td><td>";
			$rows .= $pName;
			$rows .= "</td><td align='right'>";
			$rows .= $qty;
			$rows .= "</td><td align='right'>";
			$rows .= number_format(($weight*$qty),2).' Kg';
			$rows .= "</td><td align='right'>";
			$rows .= number_format($totalvolume,2).' m<sup>3</sup>';
			$rows .= "</td></tr>";
		}
		$return_message = array(
			"rows" => $rows
		);
		return $return_message;
	}//view purchase invoice ends here.
	
	
	
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
			$content .= '</td>';
			$content .= '</tr>';	
		}//main_while loop
		
		$output = array( 
			"content" 			=> $content,
			"items_qty" 		=> $items_received
		);
		
		return $output;
	}//list_all purchases function ends here.
	
	function list_all_deliveries($warehouse_id) { 
		global $db;
		$user=new Users;
		$user_id = $_SESSION['user_id'];
		$function_id = $user->get_user_info($user_id,"user_function");
		
		$store_name='';
		$approved = 0;
		
		$query = "SELECT * from deliveries WHERE warehouse_id='".$warehouse_id."' ORDER by delivery_id DESC";
		
		$result = $db->query($query) or die($db->error);
	
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$users = new Users;
			$agent_name = $users->get_user_info($user_id, 'first_name').' '.$users->get_user_info($user_id, 'last_name');
			
			$client = new Client;
			$client_name = $client->get_client_info($client_id, 'full_name');
			//$from_name = $warehouses->get_warehouse_info($wid, 'name');
			
			$delivery_detail = "SELECT * from delivery_detail WHERE delivery_id='".$delivery_id."'";
			$delivery_detail_result = $db->query($delivery_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			
			while($delivery_detail_row = $delivery_detail_result->fetch_array()) {
				
				$items += $delivery_detail_row['qty'];
				$volume += $delivery_detail_row['volume'];
				$weight += $delivery_detail_row['poids'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $delivery_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$dateissued = strtotime($dateissued);
			$content .= date('d-m-Y',$dateissued);
			$content .= '</td><td>';
			$content .= $client_name;
			$content .= '</td><td align="right">';
			$content .= number_format($volume,2).' m<sup>3';
			$content .= '</td><td align="right">';
			$content .= number_format($weight,2).' Kg';
			$content .= '</td><td>';
			$content .= '<a href="reports/deliverydetails.php?did='.$delivery_id.'" target="_blank" style="color:#0000FF;">Details</a>';
			$content .= '</td>';
			if($delivered == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || (partial_access('deliveries') && $function_id !='storea')) { 
					$content .= '<form method="post" name="formloading"  action="approveloading.php">';
					$content .= '<input type="hidden" name="approve_loading" value="'.$delivery_id.'">';
					$content .= '<Button type="submit" class="btn btn-success" value=""><i class="fa fa-check-circle-o"></i> Approve Loading</button>';
					$content .= '</form>';
					
					}
					$content .= '</td>';
					$content .= '<td><i class="fa fa-remove" style="font-size:16px;color:red"></i> Not Loaded Yet</td>';
					
					$content .= '</tr>';	
			}
			elseif($delivered == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Loaded <br></td>';
					$content .= '<td>';
					$content .= '<a href="reports/packinglist.php?did='.$delivery_id.'" target="_blank" style="color:#0000FF;"><i class="fa fa-print" style="font-size:16px"></i> Packing List</a><br>';
					$content .= '</td>'; 
					$content .= '<td>';
					$content .= '</td>';
			}
					$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all transfers function ends here.
	echo $content;
	}
	
	function print_all_deliveries($warehouse_id) { 
		global $db;
		$user=new Users;
		$user_id = $_SESSION['user_id'];
		$function_id = $user->get_user_info($user_id,"user_function");
		
		$store_name='';
		$approved = 0;
		
		$query = "SELECT * from deliveries WHERE warehouse_id='".$warehouse_id."' ORDER by delivery_id DESC";
		
		$result = $db->query($query) or die($db->error);
	
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$users = new Users;
			$agent_name = $users->get_user_info($user_id, 'first_name').' '.$users->get_user_info($user_id, 'last_name');
			
			$client = new Client;
			$client_name = $client->get_client_info($client_id, 'full_name');
			//$from_name = $warehouses->get_warehouse_info($wid, 'name');
			
			$delivery_detail = "SELECT * from delivery_detail WHERE delivery_id='".$delivery_id."'";
			$delivery_detail_result = $db->query($delivery_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			
			while($delivery_detail_row = $delivery_detail_result->fetch_array()) {
				
				$items += $delivery_detail_row['qty'];
				$volume += $delivery_detail_row['volume'];
				$weight += $delivery_detail_row['poids'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $delivery_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$dateissued = strtotime($dateissued);
			$content .= date('d-m-Y',$dateissued);
			$content .= '</td><td>';
			$content .= $client_name;
			$content .= '</td><td align="right">';
			$content .= number_format($volume,2).' m<sup>3';
			$content .= '</td><td align="right">';
			$content .= number_format($weight,2).' Kg';
			$content .= '</td>';
			if($delivered == 0){
					$content .= '<td>';
					$content .= '<i class="fa fa-times-circle-o" style="font-size:16px;color:red"></i> Not loaded yet';
					$content .= '</td>';
					$content .= '</tr>';	
			}
			elseif($delivered == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Loaded <br></td>';
					
			}
					$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all transfers function ends here.
	echo $content;
	}
	
	function list_all_deliveriesbycust($warehouse_id, $client_id) { 
		global $db;
		$store_name='';
		$user=new Users;
		$user_id = $_SESSION['user_id'];
		$function_id = $user->get_user_info($user_id,"user_function");
		
		$query = "SELECT * from deliveries WHERE warehouse_id='".$warehouse_id."' AND client_id='".$client_id."' ORDER by delivery_id DESC";
		
		$result = $db->query($query) or die($db->error);
	
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			
			$users = new Users;
			$agent_name = $users->get_user_info($user_id, 'first_name').' '.$users->get_user_info($user_id, 'last_name');
			
			$client = new Client;
			$client_name = $client->get_client_info($client_id, 'full_name');
			//$from_name = $warehouses->get_warehouse_info($wid, 'name');
			
			$delivery_detail = "SELECT * from delivery_detail WHERE delivery_id='".$delivery_id."'";
			$delivery_detail_result = $db->query($delivery_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			
			while($delivery_detail_row = $delivery_detail_result->fetch_array()) {
				
				$items += $delivery_detail_row['qty'];
				$volume += $delivery_detail_row['volume'];
				$weight += $delivery_detail_row['poids'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $delivery_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$dateissued = strtotime($dateissued);
			$content .= date('d-m-Y',$dateissued);
			$content .= '</td><td>';
			$content .= $client_name;
			$content .= '</td><td align="right">';
			$content .= number_format($volume,2).' m<sup>3';
			$content .= '</td><td align="right">';
			$content .= number_format($weight,2).' Kg';
			$content .= '</td><td>';
			$content .= '<a href="reports/deliverydetails.php?did='.$delivery_id.'" target="_blank" style="color:#0000FF;">Details</a>';
			$content .= '</td>';
			if($delivered == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || (partial_access('deliveries') && $function_id !='storea')) { 
					$content .= '<form method="post" name="formloading"  action="approveloading.php">';
					$content .= '<input type="hidden" name="approve_loading" value="'.$delivery_id.'">';
					$content .= '<Button type="submit" class="btn btn-success" value=""><i class="fa fa-check-circle-o"></i> Approve Loading</button>';
					$content .= '</form>';
					
					}
					$content .= '</td>';
					$content .= '<td><i class="fa fa-remove" style="font-size:16px;color:red"></i> Not Loaded Yet</td>';
					
					$content .= '</tr>';	
			}
			elseif($delivered == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Loaded <br></td>';
					$content .= '<td>';
					$content .= '<a href="reports/packinglist.php?did='.$delivery_id.'" target="_blank" style="color:#0000FF;"><i class="fa fa-print" style="font-size:16px"></i> Packing List</a><br>';
					$content .= '</td>'; 
					$content .= '<td>';
					$content .= '</td>';
			}
					$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all transfers function ends here.
	echo $content;
	}
	
	function list_delivery_report($warehouse_id, $client_id, $filter) { 
		global $db;
		$store_name='';
		
		
		$query = "SELECT * from deliveries WHERE warehouse_id='".$warehouse_id."' AND client_id='".$client_id."' ORDER by delivery_id DESC LIMIT ".$filter."";
		
		$result = $db->query($query) or die($db->error);
		$count = 0;
		$content = '';
		$txt='';
		while($row = $result->fetch_array()) {
			extract($row);
			$count +=1;
			$users = new Users;
			$agent_name = $users->get_user_info($user_id, 'first_name').' '.$users->get_user_info($user_id, 'last_name');
			
			$client = new Client;
			$client_name = $client->get_client_info($client_id, 'full_name');
			//$from_name = $warehouses->get_warehouse_info($wid, 'name');
			
			$delivery_detail = "SELECT * from delivery_detail WHERE delivery_id='".$delivery_id."'";
			$delivery_detail_result = $db->query($delivery_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			
			
			while($delivery_detail_row = $delivery_detail_result->fetch_array()) {
				
				$items += $delivery_detail_row['qty'];
				$volume += $delivery_detail_row['volume'];
				$weight += $delivery_detail_row['poids'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $count;
			$content .= '</td><td>';
			$content .= $delivery_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$dateissued = strtotime($dateissued);
			$content .= date('d-m-Y',$dateissued);
			$content .= '</td><td>';
			$content .= $client_name;
			$content .= '</td><td>';
			$content .= $volume.' m<sup>3';
			$content .= '</td><td>';
			$content .= $weight.' Kg';
			$content .= '</td><td>';
			$content .= '<a href="reports/deliverydetails.php?did='.$delivery_id.'" target="_blank" style="color:#0000FF;">Details</a>';
			$content .= '</td>';
			if($delivered == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || (partial_access('delivery'))) { 
					$content .= '<a href="approveloading.php?did='.$delivery_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Click to Approve Loading</a><br>';
					}
					$content .= '</td>';
					$content .= '<td><i class="fa fa-remove" style="font-size:16px;color:red"></i> Not Loaded</td>';
					
					$content .= '</tr>';	
			}
			elseif($delivered == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Loaded <br></td>';
					$content .= '<td>';
					$content .= '<a href="reports/loadingdetails.php?tid='.$delivery_id.'" target="_blank"><i class="fa fa-print" style="font-size:16px"></i> Packing List</a><br>';
					$content .= '</td>'; 
					$content .= '<td>';
					$content .= '</td>';
			}
					$content .= '</tr>';
			}
			if ($count ==1) { $txt='delivery'; } else {$txt='deliveries';}
			$content .= '<tr><td colspan="10" style="color:#CC0000;font-size:14px"><i> Result :'.$count.' '.$txt.' found</i></tr>';
		
		
		//main_while loop
		
	//list_all transfers function ends here.
	echo $content;
	}
	
	function add_inventory_return($dateReturn, $inn, $out_inv, $product_id, $warehouse_id, $return_id) {
		global $db;
		$query = "INSERT into inventory(inventory_id, dateinventory, inn, out_inv, warehouse_id, product_id, return_id) VALUES(NULL, '".$dateReturn."', '".$inn."', '".$out_inv."', '".$warehouse_id."', '".$product_id."', '".$return_id."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;	
	}//add inventory function ends here.
	
	
	
	
	
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
	
	function add_transfer_reception($transfer_id, $datetime, $product_id, $qty1, $qty2) { 
		global $db;
		
		$query = "INSERT into transfer_received(transfer_received_id, transfer_id, date_reception, bureau_id, product_id, qty, qty_appr, agent_id) VALUES(NULL, '".$transfer_id."', '".$datetime."', '".$_SESSION['warehouse_id']."', '".$product_id."', '".$qty1."', '".$qty2."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	
	function return_delivery_details($delivery_id) {
		global $db;
		/* Discount -- */
		$tr_order_query = "SELECT * from deliveries WHERE delivery_id='".$delivery_id."'";
		$tr_order_result = $db->query($tr_order_query) or die($db->error);
		$row = $tr_order_result->fetch_array();
		//$discount = $row['discount'];
		//$payment = $row['payment'];
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from delivery_detail WHERE delivery_id='".$delivery_id."'";
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
			$rows .= '<input type="hidden" name="qty[]" id="qty'.$nb.'" size="8"  value="'.$qty.'" >'.$qty;
			$rows .= '</td><td>';
			$rows .= '<input type="number" name="qte_appr[]" size="8" id="qteapp'.$nb.'" value="" onfocusout="checkReturnedQty('.$nb.')" required >';
			$rows .= '</td><td>';
			$rows .= '<input type="text" name="cause[]" size="35" id="cause" placeholder=" Write the Return reason ..." value=""   >';
			$rows .= '</td><td>';
			$rows .= '<input type="number" name="qte_dmg[]" size="8" id="qtedmg'.$nb.'" value="" onfocusout="checkDamagedQty('.$nb.')"  >';
			/*$rows .= '</td><td>';
			$rows .= '<input type="text" name="qte_ok[]" size="8" id="qteok" value=""  >';*/
			$rows .= '</td></tr>';
			$nb = $nb+1;

		}
		
		
		echo $rows;
		echo '<input type="text" style="display: none" name="nb" id="nb" value="'.$nb.'"  >';
		
	}//view purchase invoice ends here.
	
	function return_transfer_details($transfer_id) {
		global $db;
		/* Discount -- */
		$tr_order_query = "SELECT * from transfers WHERE transfer_id='".$transfer_id."'";
		$tr_order_result = $db->query($tr_order_query) or die($db->error);
		$row = $tr_order_result->fetch_array();
		
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
			$rows .= '<input type="hidden" name="qty[]" id="qty'.$nb.'" size="8"  value="'.$qty.'" >'.$qty;
			$rows .= '</td><td>';
			$rows .= '<input type="text" name="qte_appr[]" size="8" id="qteapp'.$nb.'" value="" onfocusout="checkReturnedQty('.$nb.')" required >';
			$rows .= '</td><td>';
			$rows .= '<input type="text" name="cause[]" size="35" id="cause" placeholder=" Write the Return reason ..." value="" required >';
			$rows .= '</td><td>';
			$rows .= '<input type="text" name="qte_dmg[]" size="8" id="qtedmg'.$nb.'" value="" onfocusout="checkDamagedQty('.$nb.')" required >';
			/*$rows .= '</td><td>';
			$rows .= '<input type="text" name="qte_ok[]" size="8" id="qteok" value="" required >';*/
			$rows .= '</td></tr>';
			$nb = $nb+1;

		}
		
		
		echo $rows;
		echo '<input type="text" style="display: none" name="nb" id="nb" value="'.$nb.'"  >';
		
	}//view purchase invoice ends here.
	
	
	
	
	function add_return_details($return_id, $pid, $qty_ret, $ret_reason, $qty_dmg) { 
		global $db;
		
		$query = "INSERT into return_detail(return_detail_id, return_id, product_id, qty_ret, ret_reason, qty_dmg) VALUES(NULL, '".$return_id."', '".$pid."', '".$qty_ret."', '".$ret_reason."', '".$qty_dmg."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	function add_return_delivery($delivery_id, $return_date) { 
		global $db;
		$reference ='';
		
		$query = "INSERT into returns(return_id, delivery_id, return_date, warehouse_id, user_id) VALUES(NULL, '".$delivery_id."', '".$return_date."', '".$_SESSION['warehouse_id']."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	function add_return_transfer($transfer_id, $return_date) { 
		global $db;
		$reference ='';
		
		$query = "INSERT into returns(return_id, transfer_id, return_date, warehouse_id, user_id) VALUES(NULL, '".$transfer_id."', '".$return_date."', '".$_SESSION['warehouse_id']."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	
	function list_all_returns($warehouse_id) { 
		global $db;
		
		$query = "SELECT * from returns WHERE warehouse_id='".$warehouse_id."' ORDER by return_id DESC";
		
		$result = $db->query($query) or die($db->error);
	
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$users = new Users;
			$agent_name = $users->get_user_info($user_id, 'first_name').' '.$users->get_user_info($user_id, 'last_name');
			
			$delivery = new Delivery;
			$client = new Client ;
			$transfer = new Transfer ;
			$warehouse = new Warehouse ;
			if ($delivery_id == 0 ) {
				$reference = "Transfer N# ".$transfer_id;
				$from = $transfer->get_transfer_info($transfer_id,'destination_id');
				$from = $warehouse->get_warehouse_info($from,'name');
			} else {
				$reference = "Delivery N# ".$delivery_id; 
				$from = $delivery->get_delivery_info($delivery_id,'client_id');
				$from = $client->get_client_info($from,'full_name');
			}
			
			$return_detail = "SELECT * from return_detail WHERE return_id='".$return_id."'";
			$return_detail_result = $db->query($return_detail) or die($db->error);
			
			$items1 = 0;
			$items2 = 0;
			
			while($return_detail_row = $return_detail_result->fetch_array()) {
				
				$items1 += $return_detail_row['qty_ret'];
				$items2 += $return_detail_row['qty_dmg'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $return_id;
			$content .= '</td><td>';
			$return_date = strtotime($return_date);
			$content .= date('d-m-Y', $return_date);
			$content .= '</td><td>';
			$content .= $reference;
			$content .= '</td><td>';
			$content .= $from;
			$content .= '</td><td>';
			$content .= $items1;
			$content .= '</td><td>';
			$content .= $items2;
			$content .= '</td><td>';
			$content .= '<a href="reports/returndetails.php?rid='.$return_id.'" target="_blank" style="color:#0000FF;">Details</a>';
			$content .= '</td>';
			$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all transfers function ends here.
	echo $content;
	}
	
	function print_all_returns($warehouse_id) { 
		global $db;
		
		$query = "SELECT * from returns WHERE warehouse_id='".$warehouse_id."' ORDER by return_id DESC";
		
		$result = $db->query($query) or die($db->error);
	
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$users = new Users;
			$agent_name = $users->get_user_info($user_id, 'first_name').' '.$users->get_user_info($user_id, 'last_name');
			
			$delivery = new Delivery;
			$client = new Client ;
			$transfer = new Transfer ;
			$warehouse = new Warehouse ;
			if ($delivery_id == 0 ) {
				$reference = "Transfer N# ".$transfer_id;
				$from = $transfer->get_transfer_info($transfer_id,'destination_id');
				$from = $warehouse->get_warehouse_info($from,'name');
			} else {
				$reference = "Delivery N# ".$delivery_id; 
				$from = $delivery->get_delivery_info($delivery_id,'client_id');
				$from = $client->get_client_info($from,'full_name');
			}
			
			$return_detail = "SELECT * from return_detail WHERE return_id='".$return_id."'";
			$return_detail_result = $db->query($return_detail) or die($db->error);
			
			$items1 = 0;
			$items2 = 0;
			
			while($return_detail_row = $return_detail_result->fetch_array()) {
				
				$items1 += $return_detail_row['qty_ret'];
				$items2 += $return_detail_row['qty_dmg'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $return_id;
			$content .= '</td><td>';
			$return_date = strtotime($return_date);
			$content .= date('d-m-Y', $return_date);
			$content .= '</td><td>';
			$content .= $reference;
			$content .= '</td><td>';
			$content .= $from;
			$content .= '</td><td>';
			$content .= $items1;
			$content .= '</td><td>';
			$content .= $items2;
			$content .= '</td>';
					
			$content .= '</tr>';
			}
		
		
		//main_while loop
		
	
	echo $content;
	}
	
	function list_returns_report($warehouse_id, $client_id, $filter) { 
		global $db;
		
		$query = "SELECT * from returns, deliveries WHERE returns.delivery_id=deliveries.delivery_id AND deliveries.client_id='".$client_id."' AND returns.warehouse_id='".$warehouse_id."' ORDER by return_id DESC LIMIT ".$filter."";
		
		$result = $db->query($query) or die($db->error);
		$count = 0;
		$content = '';
		$txt='';
		while($row = $result->fetch_array()) {
			extract($row);
			$count +=1;
			$users = new Users;
			$agent_name = $users->get_user_info($user_id, 'first_name').' '.$users->get_user_info($user_id, 'last_name');
			
			$delivery = new Delivery;
			$client = new Client ;
			
			if ($delivery_id == '0' ) {
				$reference = "Transfer N# ".$transfer_id;
				$from = $transfer->get_transfer_info($transfer_id,'destination_id');
				$from = $warehouse->get_warehouse_info($from,'name');
			} else {
				$reference = "Delivery N# ".$delivery_id; 
				$from = $delivery->get_delivery_info($delivery_id,'client_id');
				$from = $client->get_client_info($from,'full_name');
			}
			
			$return_detail = "SELECT * from return_detail WHERE return_id='".$return_id."'";
			$return_detail_result = $db->query($return_detail) or die($db->error);
			
			$items1 = 0;
			$items2 = 0;
			
			while($return_detail_row = $return_detail_result->fetch_array()) {
				
				$items1 += $return_detail_row['qty_ret'];
				$items2 += $return_detail_row['qty_dmg'];
				
								
			}//Transfer detail loop.
			
			$content .= '<tr><td>';
			$content .= $count;
			$content .= '</td><td>';
			$content .= $return_id;
			$content .= '</td><td>';
			$return_date = strtotime($return_date);
			$content .= date('d-m-Y', $return_date);
			$content .= '</td><td>';
			$content .= $reference;
			$content .= '</td><td>';
			$content .= $from;
			$content .= '</td><td>';
			$content .= $items1;
			$content .= '</td><td>';
			$content .= $items2;
			$content .= '</td><td>';
			$content .= '<a href="reports/deliverydetails.php?did='.$delivery_id.'" target="_blank" style="color:#0000FF;">Details</a>';
			$content .= '</td>';
			$content .= '<td></td>';
			$content .= '<td></td>';			
			$content .= '</tr>';
			}
			if ($count ==1) { $txt='return'; } else {$txt='returns';}
			$content .= '<tr><td colspan="10" style="color:#CC0000;font-size:14px"><i> Result :'.$count.' '.$txt.' found</i></tr>';
		
		
		//main_while loop
		
	//list_all transfers function ends here.
	echo $content;
	}
	
	function top10_delivered_products($warehouse_id) {
		global $db;
		
		$query = "SELECT qty from delivery_detail WHERE warehouse_id ='".$warehouse_id."'";
		$result = $db->query($query) or die($db->error);
		$total = 0;
		$percentage = 0;
		while($row = $result->fetch_array()) {
			$total += $row['qty'];
		}
		
		$tr_detail_query = "SELECT * FROM (SELECT product_id, SUM(qty) as qte from delivery_detail GROUP BY product_id ORDER BY qte DESC ) AS D Limit 10";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
		$rows = '';
		$count = 0;
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			extract($tr_detail_row);
			$product = new Product;
			$product_name = $product->get_product_info($product_id,'product_name');
			$code = $product->get_product_info($product_id,'product_manual_id');
			$count +=1;
			$rows .= '<tr>';
			$rows .= '<th scope="row">'.$count.'</th>';
			$rows .= '<td align="right">'.$code.'</td>';
			$rows .= '<td>'.$product_name.'</td>';
			$rows .= '<td align="right">'.$tr_detail_row['qte'].'</td>';
			if ($total != 0) {
				$percentage = $tr_detail_row['qte']*100/$total;
			} else { $percentage = 0;	}		
			$rows .= '<td align="right" style="font-size:12px;color:#0cb262">'.number_format($percentage,2).' %</td>';
			$rows .= '</tr>';
		
		}
		echo $rows;
	}
	
	function top5_returned_products($warehouse_id) {
		global $db;
		
		$query = "SELECT qty_ret from returns, return_detail WHERE returns.return_id=return_detail.return_id AND returns.warehouse_id ='".$warehouse_id."' AND returns.delivery_id!=0";
		$result = $db->query($query) or die($db->error);
		$total = 0;
		$percentage = 0;
		while($row = $result->fetch_array()) {
			$total += $row['qty_ret'];
		}
		
		$tr_detail_query = "SELECT * FROM (SELECT product_id, SUM(qty_ret) as qte from returns, return_detail WHERE returns.return_id=return_detail.return_id AND returns.warehouse_id='".$warehouse_id."' AND returns.delivery_id!=0  GROUP BY product_id ORDER BY qte DESC ) AS D Limit 5";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
		$rows = '';
		$count = 0;
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			extract($tr_detail_row);
			$product = new Product;
			$product_name = $product->get_product_info($product_id,'product_name');
			$code = $product->get_product_info($product_id,'product_manual_id');
			$count +=1;
			$rows .= '<tr>';
			$rows .= '<th scope="row">'.$count.'</th>';
			$rows .= '<td align="right">'.$code.'</td>';
			$rows .= '<td>'.$product_name.'</td>';
			$rows .= '<td align="right">'.$tr_detail_row['qte'].'</td>';
			if ($total != 0) {
				$percentage = $tr_detail_row['qte']*100/$total;
			} else { $percentage = 0;	}		
			$rows .= '<td align="right" style="font-size:12px;color:#CC0000">'.number_format($percentage,2).' %</td>';
			$rows .= '</tr>';
		
		}
		echo $rows;
	}
	
	function top10_delivered_products_chart($warehouse_id) {
		global $db;
		
		$tr_detail_query = "SELECT * FROM (SELECT product_id, SUM(qty) as qte from delivery_detail WHERE warehouse_id='".$warehouse_id."' GROUP BY product_id ORDER BY qte DESC ) AS D Limit 10";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
		$rows = '';
		
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			extract($tr_detail_row);
			$product = new Product;
			$product_name = $product->get_product_info($product_id,'product_name');
			$rows .= "['".$product_name."', ".$tr_detail_row['qte']."],";
		}
		
		echo $rows;
	}
	
	function top5_returned_products_chart($warehouse_id) {
		global $db;
		
		$tr_detail_query = "SELECT * FROM (SELECT product_id, SUM(qty_ret) as qte from returns, return_detail WHERE returns.return_id=return_detail.return_id AND returns.warehouse_id='".$warehouse_id."' AND returns.delivery_id!=0 GROUP BY product_id ORDER BY qte DESC ) AS D Limit 5";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
		$rows = '';
		
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			extract($tr_detail_row);
			$product = new Product;
			$product_name = $product->get_product_info($product_id,'product_name');
			$rows .= "['".$product_name."', ".$tr_detail_row['qte']."],";
		}
		
		echo $rows;
	}
	
	function delivery_count($warehouse_id) {
		global $db;
		
		$query = "SELECT * FROM deliveries where warehouse_id = '".$warehouse_id."'";
		$result = $db->query($query) or die($db->error);
		$count = 0;
		
		while($row = $result->fetch_array()) { 
			$count += 1;
		}
		return $count;
	}
	
	function retour_count($warehouse_id) {
		global $db;
		
		$query = "SELECT * FROM returns where warehouse_id = '".$warehouse_id."'";
		$result = $db->query($query) or die($db->error);
		$count = 0;
		
		while($row = $result->fetch_array()) { 
			$count += 1;
		}
		return $count;
	}
	
	function delivery_returns_qty($warehouse_id) {
		global $db;
		
		$query = "SELECT * FROM returns, return_detail WHERE returns.return_id = return_detail.return_id  AND returns.warehouse_id = '".$warehouse_id."' AND returns.delivery_id !=0";
		$result = $db->query($query) or die($db->error);
		$count = 0;
		
		while($row = $result->fetch_array()) { 
			$count += $row['qty_ret'];
		}
		return $count;
	}
	
	function count_products_delivered($warehouse_id) {
		global $db;
		
		$query = "SELECT * FROM delivery_detail where warehouse_id = '".$warehouse_id."'";
		$result = $db->query($query) or die($db->error);
		$count = 0;
		
		while($row = $result->fetch_array()) { 
			$count += $row['qty'];
		}
		return $count;
	}
	
	function count_products_returned($warehouse_id) {
		global $db;
		
		$query = "SELECT * FROM returns, return_detail where returns.return_id = return_detail.return_id AND returns.warehouse_id = '".$warehouse_id."'";
		$result = $db->query($query) or die($db->error);
		$count = 0;
		
		while($row = $result->fetch_array()) { 
			$count += $row['qty_ret'];
		}
		return $count;
	}
	
	function add_loading_approve($delivery_id, $datetime, $product_id, $qty1, $qty2) { 
		global $db;
		
		$query = "INSERT into loading_approve(loading_approve_id, delivery_id, date_approve, warehouse_id, product_id, qty, qty_appr, agent_id) VALUES(NULL, '".$delivery_id."', '".$datetime."', '".$_SESSION['warehouse_id']."', '".$product_id."', '".$qty1."', '".$qty2."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	//Update Approved = OK
	function update_loading_delivery($delivery_id) { 
		global $db;
		
		$query = "UPDATE deliveries SET delivered = '1' WHERE delivery_id='".$delivery_id."'";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add_purchase ends here. returns purchase id.
	
	
	function approve_loading_details($delivery_id) {
		global $db;
		/* Discount -- */
		$tr_order_query = "SELECT * from deliveries WHERE delivery_id='".$delivery_id."'";
		$tr_order_result = $db->query($tr_order_query) or die($db->error);
		$row = $tr_order_result->fetch_array();
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from delivery_detail WHERE delivery_id='".$delivery_id."'";
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
			//$rows .= '<input type="text" name="discount[]"  value="'.$discount.'" > %';
			//$rows .= '</td><td>';
			$rows .= '<input type="hidden" name="qty[]"  value="'.$qty.'" >'.$qty;
			$rows .= '</td><td>';
			$rows .= '<input type="text" name="qte_appr[]" id="qteapp" value="'.$qty.'" required >';
			
			$rows .= '</td></tr>';
			$nb = $nb+1;

		}
		
		echo $rows;
		echo '<input type="text" style="display: none" name="nb" id="nb" value="'.$nb.'"  >';
		
	}//view purchase invoice ends here.
	
	function get_return_info($return_id, $term) { 
		global $db;
		$query = "SELECT * from returns WHERE return_id='".$return_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	function get_return_details_info($return_id, $term) { 
		global $db;
		$query = "SELECT * from return_detail WHERE return_id='".$return_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	function view_return_invoice($return_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from return_detail WHERE return_id='".$return_id."' ";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		//$grandTotal = 0;
		//$paid = 0;
		$rows = '';
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			
			$qty = $tr_detail_row['qty_ret'];
			$qty_dmg = $tr_detail_row['qty_dmg'];
			$reason = $tr_detail_row['ret_reason'];
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
			$rows .= $qty_dmg;
			$rows .= "</td><td>";
			$rows .= $reason;
			$rows .= "</td></tr>";
		}
		$return_message = array(
			"rows" => $rows
		);
		return $return_message;
	}//view purchase invoice ends here.
	
	
}//Purchase Class Ends here.