<?php
//Purchase Class

class Order { 

	//Order info
	function get_order_info($order_id, $term) { 
		global $db;
		$query = "SELECT * from orders WHERE order_id='".$order_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}
	// approved order Info
	function get_order_approve_info($order_id, $term) { 
		global $db;
		$query = "SELECT * from order_approved WHERE order_id='".$order_id."'";
		$result = $db->query($query) or die($db->error);
		if($row = $result->fetch_array()) {
			return $row[$term];
		} else {
			return '';
		}
	}
	// Received order Info
	function get_order_receive_info($order_id, $term) { 
		global $db;
		$query = "SELECT * from order_received WHERE order_id='".$order_id."'";
		$result = $db->query($query) or die($db->error);
		if($row = $result->fetch_array()) {
			return $row[$term];
		} else {
			return '';
		}
	}
	function get_order_details_info($order_id, $term) { 
		global $db;
		$query = "SELECT * from order_approved WHERE order_id='".$order_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	function get_bd_info($bd_id, $term) { 
		global $db;
		$query = "SELECT * from bd WHERE bd_id='".$bd_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	function get_bdnum_info($bd_id, $term) { 
		global $db;
		$query = "SELECT * from bdnum WHERE bd_num='".$bd_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	function get_driver_info($driver_id, $term) { 
		global $db;
		$query = "SELECT * from drivers WHERE cin='".$driver_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	function get_vehicule_info($vehicule_id, $term) { 
		global $db;
		$query = "SELECT * from vehicules WHERE vehicule_id='".$vehicule_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	//add purchase functions starts here.
	function add_order($datetime, $deliverydate, $supplier_id, $warehouse_id) { 
		global $db;
		
		$query = "INSERT into orders(order_id, datetime, deliverydate, supplier_id, warehouse_id, agent_id, approved) VALUES(NULL, '".$datetime."', '".$deliverydate."', '".$supplier_id."', '".$warehouse_id."', '".$_SESSION['user_id']."','0')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	
	
	function add_order_detail($order_id, $product_id, $qty) {
		global $db;	
		$query = "INSERT into order_detail(order_detail_id, order_id, warehouse_id, product_id, qty) VALUES(NULL, '".$order_id."', '".$_SESSION['warehouse_id']."', '".$product_id."', '".$qty."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add purchase detail function ends here.	
	
	function view_order_invoice($order_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from order_detail WHERE order_id='".$order_id."' ";
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
	
	function view_order_reception($order_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from order_received WHERE order_id='".$order_id."' ";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		//$grandTotal = 0;
		//$paid = 0;
		$rows = '';
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			
			$qty = $tr_detail_row['qty'];
			$qty_rec = $tr_detail_row['qty_appr'];
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
			$rows .= $qty_rec;
			$rows .= "</td></tr>";
		}
		$return_message = array(
			"rows" => $rows
		);
		return $return_message;
	}//view purchase invoice ends here.
	
	
	function list_periodical_orders($start_date, $end_date) { 
		global $db;
		
		$from = $start_date;
		$to = $end_date;
		
		$query = "SELECT * from orders WHERE store_id='".$_SESSION['store_id']."' AND datetime between '".$from."' AND '".$to."' ORDER by order_id DESC";
		$result = $db->query($query) or die($db->error);
		
		
		$items_received	 	 = 0;
		
				
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$warehouses = new Warehouse;
			$warehouse_name = $Warehouses->get_warehouse_info($warehouse_id, 'name');
			
			$order_detail = "SELECT * from order_detail WHERE order_id='".$order_id."'";
			$order_detail_result = $db->query($order_detail) or die($db->error);
			
			//$payable = 0;
			//$paid = 0;
			$items = 0;
			
			while($order_detail_row = $order_detail_result->fetch_array()) {
				$inventory_id = $transfer_detail_row['inventory_id'];
				//$debt_id = $purchase_detail_row['debt_id'];
				
				//Inventory q?uery.
				$inventory_query = "SELECT * from inventory WHERE inventory_id='".$inventory_id."'";
				$inventory_result = $db->query($inventory_query) or die($db->error);
				$inventory_row = $inventory_result->fetch_array();
				
				$items += $inventory_row['inn'];
				
					
			}//purchase detail loop.
			
			$items_received		= $items_received+$items;
			
			$content .= '<tr><td>';
			$content .= $order_id;
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
	
	function list_all_orders($warehouse_id, $supplier_id) { 
		global $db;
		$user=new Users;
		$user_id = $_SESSION['user_id'];
		$function_id = $user->get_user_info($user_id,"user_function");
		
		$store_name='';
		$approved = 0;
		$supp = 0;
		$count = 0;
		if ($supplier_id == "0") {
		$query = "SELECT * from orders WHERE warehouse_id='".$warehouse_id."' ORDER by order_id DESC";
		
		}
		else
		{
			$query = "SELECT * from orders WHERE warehouse_id='".$warehouse_id."' AND supplier_id='".$supplier_id."' ORDER by order_id DESC";
			$supp=1;
		}
		$result = $db->query($query) or die($db->error);
		
		
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$count += 1;
			$oid = $row['order_id'];
			$wid = $row['warehouse_id'];
			$approved = $row['approved'];
			$supplier_id = $row['supplier_id'];
			$query1 = "SELECT * from warehouses WHERE warehouse_id='".$wid."' ";
			$result1 = $db->query($query1) or die($db->error);
			$row1 = $result1->fetch_array();
			$warehouse_name = $row1['name'];
			
			$warehouse_access = new WarehouseAccess;
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$supplier = new Supplier;
			$supplier_name = $supplier->get_supplier_info($supplier_id, 'full_name');
			
			/*$warehouses = new Warehouse;
			$warehouse_name = $warehouses->get_warehouse_info($warehouse_id, 'name');*/
			
			$order_detail = "SELECT * from order_detail WHERE order_id='".$order_id."'";
			$order_detail_result = $db->query($order_detail) or die($db->error);
			
			$items = 0;
			
			while($order_detail_row = $order_detail_result->fetch_array()) {
				
				$items += $order_detail_row['qty'];
				
								
			}//purchase detail loop.
			
			$content .= '<tr><td>';
			$content .= $count;
			$content .= '</td><td>';
			$content .= 'Order N#: '.$order_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $warehouse_name;
			$content .= '</td><td>';
			$content .= $supplier_name;
			$content .= '</td><td>';
			$deliverydate = strtotime($deliverydate);
			$content .= date('d-m-Y', $deliverydate);
			$content .= '</td><td>';
			
			$content .= $items;
			$content .= '</td><td>';
			
			$content .= '<a href="reports/orderdetails.php?oid='.$order_id.'" target="_blank" style="color:#0000FF;">Details</a>';
			$content .= '</td>';
			if($approved == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || ( $warehouse_access->have_module_access('orders') && $function_id !='storea') ) { 
					$content .= '<a href="approveorder.php?oid='.$order_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Approve</a><br>';
					}
					$content .= '</td>';
					$content .= '<td><i class="fa fa-remove" style="font-size:16px;color:red"></i> Not Received</td><td>';
					if ((partial_access('admin')) || ( $warehouse_access->have_module_access('orders') && $function_id !='storea') ) { 
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_order" value="'.$order_id.'">';
					$content .= '<button type="submit" class="btn btn-danger"  value="Delete"><i class="fa fa-trash-o"  aria-hidden="true"></i> Remove  </button>';
					$content .= '</form>';
					}
					$content .= '</td>'; 
					$content .= '</tr>';
					
			}
			elseif($approved == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approved<br></td>';
					$content .= '<td>';
					$content .= '<a href="reports/approvedorder.php?oid='.$order_id.'" target="_blank"><i class="fa fa-print" style="font-size:16px"></i> Print</a><br>';
					$content .= '</td>'; 
					$content .= '<td>';
					if($received == 0){
						if ((partial_access('admin')) || ( $warehouse_access->have_module_access('orders') && $function_id !='storea') ) { 
							$content .= '<a href="receiveorder.php?oid='.$order_id.'" class="btn btn-success" target="_self"><i class="fa fa-share-square-o" aria-hidden="true" ></i> Receive</a><br>';
							}
					}
					elseif($received == 1){ 
					$content .= '<i class="fa fa-share-square" style="font-size:16px;color:green" ></i> Received <a href="reports/receivedorder.php?oid='.$order_id.'" target="_blank"><i class="fa fa-print" style="font-size:16px"></i></a><br>';  
					}
					$content .= '</td>';}
					$content .= '</tr>';
					
			}
			if ($supp==1) {
					$content .= '<tr><td colspan="10" style="color:#CC0000;font-size:14px"><i> Result :'.$count.' orders found</i></tr>';	
					}
		
		
		//main_while loop
		
	//list_all purchases function ends here.
	echo $content;
	}
	
	function print_all_orders($warehouse_id, $supplier_id) { 
		global $db;
		$user=new Users;
		$user_id = $_SESSION['user_id'];
		$function_id = $user->get_user_info($user_id,"user_function");
		
		$store_name='';
		$approved = 0;
		$supp = 0;
		$count = 0;
		if ($supplier_id == "0") {
		$query = "SELECT * from orders WHERE warehouse_id='".$warehouse_id."' ORDER by order_id DESC";
		
		}
		else
		{
			$query = "SELECT * from orders WHERE warehouse_id='".$warehouse_id."' AND supplier_id='".$supplier_id."' ORDER by order_id DESC";
			$supp=1;
		}
		$result = $db->query($query) or die($db->error);
		
		
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$count += 1;
			$oid = $row['order_id'];
			$wid = $row['warehouse_id'];
			$approved = $row['approved'];
			$supplier_id = $row['supplier_id'];
			$query1 = "SELECT * from warehouses WHERE warehouse_id='".$wid."' ";
			$result1 = $db->query($query1) or die($db->error);
			$row1 = $result1->fetch_array();
			$warehouse_name = $row1['name'];
			
			$warehouse_access = new WarehouseAccess;
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$supplier = new Supplier;
			$supplier_name = $supplier->get_supplier_info($supplier_id, 'full_name');
			
			$order_detail = "SELECT * from order_detail WHERE order_id='".$order_id."'";
			$order_detail_result = $db->query($order_detail) or die($db->error);
			
			$items = 0;
			
			while($order_detail_row = $order_detail_result->fetch_array()) {
				
				$items += $order_detail_row['qty'];
				
								
			}//purchase detail loop.
			
			$content .= '<tr><td>';
			$content .= 'Order N#: '.$order_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $warehouse_name;
			$content .= '</td><td>';
			$content .= $supplier_name;
			$content .= '</td><td>';
			$deliverydate = strtotime($deliverydate);
			$content .= date('d-m-Y', $deliverydate);
			$content .= '</td><td>';
			
			$content .= $items;
			$content .= '</td>';
			if($approved == 0){
					$content .= '<td>';
					$content .= '<i class="fa fa-times-circle-o" style="font-size:16px;color:red"></i> Not approved yet<br>';
					$content .= '</td>';
					$content .= '<td><i class="fa fa-times-circle-o" style="font-size:16px;color:red"></i> Not received yet</td>';
					$content .= '</tr>';
					
			}
			elseif($approved == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approved<br></td>';
					$content .= '<td>';
					if($received == 0){
						$content .= '<i class="fa fa-times-circle-o" style="font-size:16px;color:red"></i> Not received yet<br>';
					}
					elseif($received == 1){ 
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Received <br>';  
					}
					$content .= '</td>';}
					$content .= '</tr>';
					
			}
			/*if ($supp==1) {*/
					$content .= '<tr><td colspan="8" style="color:#CC0000;font-size:14px"><i><small> Result :'.$count.' orders found</i></small></td></tr>';	
			/*		}*/
		
		
		//main_while loop
		
	//list_all purchases function ends here.
	echo $content;
	}
	
	function list_all_orders_bysupp($warehouse_id, $supplier_id) { 
		global $db;
		$store_name='';
		$approved = 0;
		
		$user=new Users;
		$user_id = $_SESSION['user_id'];
		$function_id = $user->get_user_info($user_id,"user_function");
		
			$query = "SELECT * from orders WHERE warehouse_id='".$warehouse_id."' AND supplier_id='".$supplier_id."' ORDER by order_id DESC";
		//}
		$result = $db->query($query) or die($db->error);
		
		
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$oid = $row['order_id'];
			$wid = $row['warehouse_id'];
			$approved = $row['approved'];
			$supplier_id = $row['supplier_id'];
			$query1 = "SELECT * from warehouses WHERE warehouse_id='".$wid."' ";
			$result1 = $db->query($query1) or die($db->error);
			$row1 = $result1->fetch_array();
			$warehouse_name = $row1['name'];
			
			$warehouse_access = new WarehouseAccess;
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$supplier = new Supplier;
			$supplier_name = $supplier->get_supplier_info($supplier_id, 'full_name');
			
			/*$warehouses = new Warehouse;
			$warehouse_name = $warehouses->get_warehouse_info($warehouse_id, 'name');*/
			
			$order_detail = "SELECT * from order_detail WHERE order_id='".$order_id."'";
			$order_detail_result = $db->query($order_detail) or die($db->error);
			
			$items = 0;
			
			while($order_detail_row = $order_detail_result->fetch_array()) {
				
				$items += $order_detail_row['qty'];
				
								
			}//purchase detail loop.
			
			$content .= '<tr><td>';
			$content .= $order_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $warehouse_name;
			$content .= '</td><td>';
			$content .= $supplier_name;
			$content .= '</td><td>';
			$deliverydate = strtotime($deliverydate);
			$content .= date('d-m-Y', $deliverydate);
			$content .= '</td><td>';
			
			$content .= $items;
			$content .= '</td><td>';
			
			$content .= '<a href="reports/orderdetails.php?oid='.$order_id.'" target="_blank" style="color:#0000FF;">Details</a>';
			$content .= '</td>';
			if($approved == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || ( $warehouse_access->have_module_access('orders') && $function_id !='storea') ) { 
					$content .= '<a href="approveorder.php?oid='.$order_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Approve</a><br>';
					}
					$content .= '</td>';
					$content .= '<td><i class="fa fa-remove" style="font-size:16px;color:red"></i> Not Received</td><td>';
					if ((partial_access('admin')) || ( $warehouse_access->have_module_access('orders') && $function_id !='storea') ) { 
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_order" value="'.$order_id.'">';
					$content .= '<button type="submit" class="btn btn-danger btn-sm"  value="Delete"><i class="fa fa-trash-o"  aria-hidden="true"></i></button>';
					$content .= '</form>';
					}
					$content .= '</td>'; 
					$content .= '</tr>';	
			}
			elseif($approved == 1){
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approved<br></td>';
					$content .= '<td>';
					$content .= '<a href="reports/orderdetails.php?oid='.$order_id.'" target="_blank"><i class="fa fa-print" style="font-size:16px"></i> Print</a><br>';
					$content .= '</td>'; 
					$content .= '<td>';
					if($received == 0){
						if ((partial_access('admin')) || ( $warehouse_access->have_module_access('orders') && $function_id !='storea') ) { 
							$content .= '<a href="receiveorder.php?oid='.$order_id.'" target="_self"><i class="fa fa-share-square-o" style="font-size:16px;color:orange"></i> Receive</a><br>';
							}
					}
					elseif($received == 1){ 
						$content .= '<i class="fa fa-share-square" style="font-size:16px;color:green"></i> Received <a href="reports/orderreceived.php?oidid='.$order_id.'"><i class="fa fa-print" style="font-size:16px"></i></a><br>';  
						}
						$content .= '</td>';}
						$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all purchases function ends here.
	echo $content;
	}
	
	function list_all_orders_issued($store_id) { 
		global $db;
		$store_name='';
		$approved = 0;
		
			$query = "SELECT * from orders WHERE store_id='".$_SESSION['store_id']."' ORDER by order_id DESC";
		
		$result = $db->query($query) or die($db->error);
		
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$oid = $row['order_id'];
			$sid = $row['store_id'];
			$approved = $row['approved'];
			$client_id = $row['client_id'];
			$query1 = "SELECT * from stores WHERE store_id='".$sid."' ";
			$result1 = $db->query($query1) or die($db->error);
			$row1 = $result1->fetch_array();
			$store_name = $row1['store_name'];
			
			
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$clients = new Client;
			$client_name = $clients->get_client_info($client_id, 'full_name');
			
			$warehouses = new Warehouse;
			$warehouse_name = $warehouses->get_warehouse_info($warehouse_id, 'name');
			
			$order_detail = "SELECT * from order_detail WHERE order_id='".$order_id."'";
			$order_detail_result = $db->query($order_detail) or die($db->error);
			
			$items = 0;
			
			while($order_detail_row = $order_detail_result->fetch_array()) {
				
				$items += $order_detail_row['qty'];
				
								
			}//order detail loop.
			
			$content .= '<tr><td>';
			$content .= $order_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $client_name;
			$content .= '</td><td>';
			$content .= $warehouse_name;
			$content .= '</td><td>';
			$content .= $store_name;
			$content .= '</td><td>';
			$content .= $agent_name;
			$content .= '</td><td>';
			
			$content .= $items;
			$content .= '</td><td>';
			
			$content .= '<a href="reports/view_order_invoice.php?order_id='.$order_id.'" target="_blank">Voir</a>';
			$content .= '</td>';
			if($approved == 0){
					$content .= '<td>';
					if ((partial_access('admin')) || (partial_access('orders'))) { 
					$content .= '<a href="approve_order.php?oid='.$order_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Approuver</a><br>';
					}
					$content .= '</td><td>';
					if ((partial_access('admin')) || (partial_access('orders'))) { 
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_order" value="'.$order_id.'">';
					$content .= '<input type="submit" class="btn btn-default btn-sm" value="Supprimer">';
					$content .= '</form>';
					}
					$content .= '</td>'; 
					$content .= '</tr>';	
			}
			elseif($approved == 1){
					$query2 = "SELECT * from order_approved WHERE order_id='".$oid."' ";
					$result2 = $db->query($query2) or die($db->error);
					$row2 = $result2->fetch_array();
					$bd = $row2['bd'];
					
					$query3 = "SELECT * from bdnum WHERE order_id='".$oid."' ";
					$result3 = $db->query($query3) or die($db->error);
					$row3 = $result3->fetch_array();
					$bd1 = $row3['bd_num'];
					
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approuvé<br></td>';
					$content .= '<td>';
					if ($bd == 0)
					{
					$content .= '<a href="bd.php?oid='.$order_id.'" target="_blank"><i class="fa fa-navicon" style="font-size:16px"></i> Bon à délivrer </a><br>';
					}
					elseif($bd == 1)
					{
					$content .= '<a href="reports/view_bd_note.php?bdid='.$bd1.'&sid='.$store_id.'&wid='.$warehouse_id.'" target="_blank"><i class="fa fa-navicon" style="font-size:16px"></i> Afficher le BD </a><br>';
					}
					$content .= '</td>'; 
					}
					$content .= '</tr>';
			}
		
		
		//main_while loop
		
	//list_all purchases function ends here.
	echo $content;
	}
	
	function list_all_orders_received($store_id) { 
		global $db;
		$user = new Users;

		$id=$_SESSION["store_id"];
		$user_id = $_SESSION['user_id'];
		$function_id = $user->get_user_info($user_id,"user_function");
		
		$store_name='';
		$approved = 0;
		if (partial_access('admin') || $function_id == 'commerciala' || $function_id == 'commercialm' ) {
		$query = "SELECT * from orders  ORDER by order_id DESC";
		} else {
		$query = "SELECT * from orders WHERE warehouse_id='".$_SESSION['store_id']."' ORDER by order_id DESC";
		}
		$result = $db->query($query) or die($db->error);
		
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$oid = $row['order_id'];
			$sid = $row['store_id'];
			$approved = $row['approved'];
			$client_id = $row['client_id'];
			$query1 = "SELECT * from stores WHERE store_id='".$sid."' ";
			$result1 = $db->query($query1) or die($db->error);
			$row1 = $result1->fetch_array();
			$store_name = $row1['store_name'];
			
			
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$clients = new Client;
			$client_name = $clients->get_client_info($client_id, 'full_name');
			
			$warehouses = new Warehouse;
			$warehouse_name = $warehouses->get_warehouse_info($warehouse_id, 'name');
			
			$delivery = new Delivery;
			
			$order = new Order ;
			
			$query2 = "SELECT * from order_approved WHERE order_id='".$oid."' ";
					$result2 = $db->query($query2) or die($db->error);
					$row2 = $result2->fetch_array();
					$bd = $row2['bd'];
			$query3 = "SELECT * from bdnum WHERE order_id='".$oid."' ";
					$result3 = $db->query($query3) or die($db->error);
					$row3 = $result3->fetch_array();
					$bd_id = $row3['bd_num'];
					
					$bl = $order->get_bdnum_info($bd_id,"bl_id");
					$delivered = $delivery->get_delivery_info($bl,"delivered");
					
			
			$order_detail = "SELECT * from order_detail WHERE order_id='".$order_id."' ORDER BY order_id DESC";
			$order_detail_result = $db->query($order_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			$products = new Product;
			
			while($order_detail_row = $order_detail_result->fetch_array()) {
				
				$vol2=0;
				$poids1=0;
				$qte = $order_detail_row['qty'];
				$items += $qte;
				$pr = $order_detail_row['product_id'];
				$long_pr =0; $larg=0; $haut=0; $poids=0;
				$long_pr = $products->get_product_dimensions($pr,'long_pr')/100;
				$larg = $products->get_product_dimensions($pr,'larg')/100;
				$haut = $products->get_product_dimensions($pr,'haut')/100;
				$poids = $products->get_product_dimensions($pr,'poids');
				
				$vol1=$long_pr*$larg*$haut;
				$vol2=$vol1*$qte;
				$poids1=$poids*$qte;
				
				$volume += $vol2;
				$weight += $poids1;
				
								
			}//order detail loop.
			if ($delivered =='1') {
			$content .= '<tr><td><S>';
			$content .= $order_id;
			$content .= '</S></td><td><S>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</S></td><td><S>';
			$content .= $client_name;
			$content .= '</S></td><td><S>';
			$content .= $warehouse_name;
			$content .= '</S></td><td><S>';
			$content .= $store_name;
			$content .= '</S></td><td><S>';
			$content .= $agent_name;
			$content .= '</S><td align="right"><S>';
			$content .= number_format($volume,3). ' m&sup3;';
			$content .= '</S></td ><td align="right"><S>';
			$content .= number_format($weight,2). ' Kg';
			$content .= '</S></td><td>';
			$content .= '<a href="reports/view_order_invoice.php?order_id='.$order_id.'" target="_blank">Voir</a>';
			$content .= '</td>';
			
			$user = new Users;
			$user_id = $_SESSION['user_id'];
			$function_id = $user->get_user_info($user_id,"user_function");
			
			if($approved == 0){
					$content .= '<td><S>';
					if (partial_access('admin') || $function_id == 'unitm' || $function_id == 'commercialm' || $function_id == 'commerciala') { 
					$content .= '<a href="approve_order.php?oid='.$order_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Approuver</a><br>';
					}
					$content .= '</td><td><S>';
					if (partial_access('admin') ) { 
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_order" value="'.$order_id.'">';
					$content .= '<input type="submit" class="btn btn-default btn-sm" value="Supprimer">';
					$content .= '</form>';
					}
					$content .= '</td>'; 
					$content .= '</tr>';	
			}
			elseif($approved == 1){
					
					$content .= '<td><S>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approuvé';
					if($delivered == '1') {
					$content .= '</S><br> <font color="red" size="2">Délivré</font>';
					}
					$content .= '</td>';
					$content .= '<td>';
					if ($bd == 0)
					{
					$content .= '<a href="bd.php?oid='.$order_id.'" target="_blank"><i class="fa fa-navicon" style="font-size:16px"></i> Bon à délivrer </a><br>';
					}
					elseif($bd == 1)
					{
					$content .= '<a href="reports/view_bd.php?oid='.$order_id.'" target="_blank"><i class="fa fa-navicon" style="font-size:16px"></i> Afficher BD </a><br>';
					}
					$content .= '</td>'; 
					}
					$content .= '</tr>';	
			} else 
			{
			$content .= '<tr><td>';
			$content .= $order_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $client_name;
			$content .= '</td><td>';
			$content .= $warehouse_name;
			$content .= '</td><td>';
			$content .= $store_name;
			$content .= '</td><td>';
			$content .= $agent_name;
			$content .= '<td align="right">';
			$content .= number_format($volume,3). ' m&sup3;';
			$content .= '</td ><td align="right">';
			$content .= number_format($weight,2). ' Kg';
			$content .= '</td><td>';
			$content .= '<a href="reports/view_order_invoice.php?order_id='.$order_id.'" target="_blank">Voir</a>';
			$content .= '</td>';
			
			$user = new Users;
			$user_id = $_SESSION['user_id'];
			$function_id = $user->get_user_info($user_id,"user_function");
			
			if($approved == 0){
					$content .= '<td>';
					if (partial_access('admin') || $function_id == 'unitm' || $function_id == 'commercialm' || $function_id == 'commerciala') { 
					$content .= '<a href="approve_order.php?oid='.$order_id.'" target="_self"><i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Approuver</a><br>';
					}
					$content .= '</td><td>';
					if (partial_access('admin') ) { 
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_order" value="'.$order_id.'">';
					$content .= '<input type="submit" class="btn btn-default btn-sm" value="Supprimer">';
					$content .= '</form>';
					}
					$content .= '</td>'; 
					$content .= '</tr>';	
			}
			elseif($approved == 1){
					
					$content .= '<td>';
					$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approuvé';
					if($delivered == '1') {
					$content .= '<br> <font color="red" size="2">Délivré</font>';
					}
					$content .= '</td>';
					$content .= '<td>';
					if ($bd == 0)
					{
					$content .= '<a href="bd.php?oid='.$order_id.'" target="_blank"><i class="fa fa-navicon" style="font-size:16px"></i> Bon à délivrer </a><br>';
					}
					elseif($bd == 1)
					{
					$content .= '<a href="reports/view_bd.php?oid='.$order_id.'" target="_blank"><i class="fa fa-navicon" style="font-size:16px"></i> Afficher BD </a><br>';
					}
					$content .= '</td>'; 
					}
					$content .= '</tr>';
			}
		}
		
		
		//main_while loop
		
	//list_all purchases function ends here.
	echo $content;
	}
	
	function list_all_orders_volume($store_id) { 
		global $db;
		$user = new Users;

		$id=$_SESSION["store_id"];
		$user_id = $_SESSION['user_id'];
		$function_id = $user->get_user_info($user_id,"user_function");
		
		$store_name='';
		$approved = 0;
		if (partial_access('admin') || $function_id == 'commerciala' || $function_id == 'commercialm' ) {
		$query = "SELECT * from orders   ORDER by order_id DESC";
		} else {
		$query = "SELECT * from orders WHERE  warehouse_id='".$_SESSION['store_id']."' ORDER by order_id DESC";
		}
		$result = $db->query($query) or die($db->error);
		
		
		$content = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$oid = $row['order_id'];
			$sid = $row['store_id'];
			$approved = $row['approved'];
			if ($approved==1) 
				{
					
					$queryAPP = "SELECT * from bdnum WHERE order_id='".$oid."' ";
					$resultAPP = $db->query($queryAPP) or die($db->error);
					$rowAPP = $resultAPP->fetch_array();
					$bl_id = $rowAPP['bl_id'];
					$delivery = new Delivery;
					$delivered = $delivery->get_delivery_info($bl_id,"delivered");
					if ($delivered == '1')
					{
						continue;
					}
					
				}
			$client_id = $row['client_id'];
			$query1 = "SELECT * from stores WHERE store_id='".$sid."' ";
			$result1 = $db->query($query1) or die($db->error);
			$row1 = $result1->fetch_array();
			$store_name = $row1['store_name'];
			
			
			
			$users = new Users;
			$agent_name = $users->get_user_info($agent_id, 'first_name').' '.$users->get_user_info($agent_id, 'last_name');
			
			$clients = new Client;
			$client_name = $clients->get_client_info($client_id, 'full_name');
			
			$warehouses = new Warehouse;
			$warehouse_name = $warehouses->get_warehouse_info($warehouse_id, 'name');
			
			$products = new Product;
			
			$order_detail = "SELECT * from order_detail WHERE order_id='".$oid."'";
			$order_detail_result = $db->query($order_detail) or die($db->error);
			
			$items = 0;
			$volume = 0;
			$weight = 0;
			
			
			while($order_detail_row = $order_detail_result->fetch_array()) {
				$vol2=0;
				$poids1=0;
				$qte = $order_detail_row['qty'];
				$items += $qte;
				$pr = $order_detail_row['product_id'];
				$long_pr =0; $larg=0; $haut=0; $poids=0;
				$long_pr = $products->get_product_dimensions($pr,'long_pr')/100;
				$larg = $products->get_product_dimensions($pr,'larg')/100;
				$haut = $products->get_product_dimensions($pr,'haut')/100;
				$poids = $products->get_product_dimensions($pr,'poids');
				
				$vol1=$long_pr*$larg*$haut;
				$vol2=$vol1*$qte;
				$poids1=$poids*$qte;
				
				$volume += $vol2;
				$weight += $poids1;
				
				//echo '-volume='.$vol1.'-qty='.$qte;
								
			}//order detail loop.
			
			$content .= '<tr><td>';
			$content .= $order_id;
			$content .= '</td><td>';
			$datetime = strtotime($datetime);
			$content .= date('d-m-Y', $datetime);
			$content .= '</td><td>';
			$content .= $client_name;
			$content .= '</td><td>';
			$content .= $warehouse_name;
			$content .= '</td><td>';
			$content .= $store_name;
			$content .= '</td><td align="right">';
			
			$content .= $items;
			$content .= '</td><td>';
			
			$content .= '<a href="reports/view_order_invoice.php?order_id='.$order_id.'" target="_blank">Voir</a>';
			$content .= '</td>';
			
			$user = new Users;
			$user_id = $_SESSION['user_id'];
			$function_id = $user->get_user_info($user_id,"user_function");
			
			$content .= '<td align="right">';
			$content .= number_format($volume,3). ' m&sup3;';
			$content .= '</td ><td align="right">';
			$content .= number_format($weight,2). ' Kg';
			$content .= '</td><td>';
			if ( $approved==0) 
			{
			$content .= '<i class="fa fa-check-circle-o" style="font-size:16px;color:orange"></i> Non Encore Approuvé';
			}
			else
			{
			$content .= '<i class="fa fa-check-circle" style="font-size:16px;color:green"></i> Approuvé';
			}
			$content .= '</td>'; 
			$content .= '</tr>';	
			//$content .= '</tr>';
			
		}
		
		//main_while loop
		
	//list_all purchases function ends here.
	echo $content;
	}
	
	
	function list_partition_vehicules() { 
		global $db;
		$user = new Users;
		$approved = 0;
		$id=$_SESSION["store_id"];
		$user_id = $_SESSION['user_id'];
		$function_id = $user->get_user_info($user_id,"user_function");
		
		
		$queryB = "SELECT * from  stores WHERE pv='1' ";
		$resultB = $db->query($queryB) or die($db->error);
		$content = '';
		while($rowB = $resultB->fetch_array()) {
			
			
			
			$sid = $rowB['store_id'];
			$store_name = $rowB['store_name'];
			
			if (partial_access('admin') || $function_id == 'commerciala' || $function_id == 'commercialm' ) {
				$query = "SELECT * from orders   ORDER by order_id DESC";
			} else {
				$query = "SELECT * from orders WHERE  warehouse_id='".$_SESSION['store_id']."' ORDER by order_id DESC";
			}
			$result = $db->query($query) or die($db->error);
			
			
		
			$voltotal=0;
				$poidstotal=0;
			while($row = $result->fetch_array()) 
			{
				extract($row);
				$oid = $row['order_id'];
				if ($approved==1) 
				{
					
					$queryAPP = "SELECT * from bdnum WHERE order_id='".$oid."' ";
					$resultAPP = $db->query($queryAPP) or die($db->error);
					$rowAPP = $resultAPP->fetch_array();
					$bl_id = $rowAPP['bl_id'];
					$delivery = new Delivery;
					$delivered = $delivery->get_delivery_info($bl_id,"delivered");
					if ($delivered == '1')
					{
						continue;
					}
				}
				
				$products = new Product;
				$order = new Order;
				
				$order_detail = "SELECT * from order_detail WHERE order_id='".$oid."' and store_id='".$sid."'";
				$order_detail_result = $db->query($order_detail) or die($db->error);
				
				$items = 0;
				$volume = 0;
				$weight = 0;
				
				
				while($order_detail_row = $order_detail_result->fetch_array()) 
					{
						$vol2=0;
						$poids1=0;
						$qte = $order_detail_row['qty'];
						$items += $qte;
						$pr = $order_detail_row['product_id'];
						$long_pr =0; $larg=0; $haut=0; $poids=0;
						$long_pr = $products->get_product_dimensions($pr,'long_pr')/100;
						$larg = $products->get_product_dimensions($pr,'larg')/100;
						$haut = $products->get_product_dimensions($pr,'haut')/100;
						$poids = $products->get_product_dimensions($pr,'poids');
						
						$vol1=$long_pr*$larg*$haut;
						$vol2=$vol1*$qte;
						$poids1=$poids*$qte;
						
						$volume += $vol2;
						$weight += $poids1;
						
						//echo '-volume='.$vol1.'-qty='.$qte;
										
					}
					$voltotal += $volume;
					$poidstotal += $weight;
			}//order detail loop.
					
				
					
					$content .= '<tr><td>';
					$content .= $store_name;
					$content .= '</td><td align="right">';
					$content .= number_format($voltotal,3). ' m&sup3;';
					$content .= '</td><td align="right">';
					$content .= number_format($poidstotal,2). ' Kg';
					$content .= '</td><td >';
					$queryMT = "SELECT  * from vehicules WHERE (volume >= '".$voltotal."' AND poids >= '".$poidstotal."') ORDER BY (volume - ".$voltotal.") ";
					$resultMT = $db->query($queryMT) or die($db->error);
					$nb=0;
					while($rowMT = $resultMT->fetch_array()) 
						{
							$nb +=1;
							$marque = $rowMT['marque'];
							$mat1 = $rowMT['mat1'];
							$mat2 = $rowMT['mat2'];
							
							$content .= '<b><u><font color="blue"> Choix'.$nb.':</font></u></b> '.$marque.' '.$mat1.' TUN '.$mat2.'<br>';
						}
					if($nb==0){
						$content .= '<font color="Red"> Aucun Véhicule ne peut supporter ce Volume ou ce Poids !! <br> Veuillez réduire les quantités des Ordres </font>';
						$content .= '</td><td align="center"> -';
						$content .= '</td><td align="center"> -';
						$content .= '</td><td align="center"> -';
					}
					else 
					{
					$content .= '</td><td>';
					
					$queryMT1 = "SELECT  * from vehicules WHERE (volume >= '".$voltotal."' AND poids >= '".$poidstotal."')  ORDER BY (volume - ".$voltotal.")  ";
					$resultMT1 = $db->query($queryMT1) or die($db->error);
					while($rowMT1 = $resultMT1->fetch_array()) 
						{
							
							$chauffeur = $rowMT1['chauffeur'];
							$nom_chauffeur = $order->get_driver_info($chauffeur,"full_name");
							$content .= $nom_chauffeur.'<br>';
						}
					$content .= '</td><td align="right">';
					$queryMT2 = "SELECT  * from vehicules WHERE volume >= '".$voltotal."' AND poids >= '".$poidstotal."' ORDER BY (volume - ".$voltotal.")  ";
					$resultMT2 = $db->query($queryMT2) or die($db->error);
					while($rowMT2 = $resultMT2->fetch_array()) 
						{
						
							$volmt = $rowMT2['volume'];
							
							$content .= number_format($volmt,3).' m&sup3<br>';
						}
					$content .= '</td><td align="right">';
					$queryMT3 = "SELECT  * from vehicules WHERE volume >= '".$voltotal."' AND poids >= '".$poidstotal."' ORDER BY (volume - ".$voltotal.") ";
					$resultMT3 = $db->query($queryMT3) or die($db->error);
					while($rowMT3 = $resultMT3->fetch_array()) 
						{
						
							$poidsmt = $rowMT3['poids'];
							$content .= number_format($poidsmt,2).' Kg<br>';
						}
					}
				
					$content .= '</td>'; 
					$content .= '</tr>';	
				//$content .= '</tr>';
				
				
			
		}	
	//list_all purchases function ends here.
	echo $content;
	
}
	
	
	
	
	function delete_order($order_id) {
		global $db;
		
		$query = "DELETE FROM orders WHERE order_id='".$order_id."'";
		$result = $db->query($query) or die($db->error);
		
		$delete = "DELETE FROM order_detail WHERE order_id='".$order_id."'";
		$result = $db->query($delete) or die($db->error);
		
		return "Order Deleted Successfully !!.";
	}//delete_sale ends here.
	
	
	function approve_order_details($order_id) {
		global $db;
		/* Discount -- */
		$tr_order_query = "SELECT * from orders WHERE order_id='".$order_id."'";
		$tr_order_result = $db->query($tr_order_query) or die($db->error);
		$row = $tr_order_result->fetch_array();
		//$discount = $row['discount'];
		//$payment = $row['payment'];
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from order_detail WHERE order_id='".$order_id."'";
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
	
	
	
	function add_order_approve($order_id, $datetime, $product_id, $qty1, $qty2) { 
		global $db;
		
		$query = "INSERT into order_approved(order_approved_id, order_id, date_approve, warehouse_id, product_id, qty, qty_appr, agent_id) VALUES(NULL, '".$order_id."', '".$datetime."', '".$_SESSION['warehouse_id']."', '".$product_id."', '".$qty1."', '".$qty2."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	
	//Update Approved = OK
	function update_order($order_id) { 
		global $db;
		
		$query = "UPDATE orders SET approved = '1' WHERE order_id='".$order_id."'";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}//add_purchase ends here. returns purchase id.
	
	
	
	function approved_order_invoice($order_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from order_approved WHERE order_id='".$order_id."' ";
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
	
	
	
	function add_driver($cin, $name, $phone, $nbpermis, $catb, $catbe, $catc, $catce, $catd, $catde, $catd1, $cath, $validity) { 
		global $db;
		
		$query = "INSERT into drivers(cin, full_name, phone, nbpermis, catb, catbe, catc, catce, catd, catde, catd1, cath, validity) VALUES('".$cin."', '".$name."', '".$phone."', '".$nbpermis."', '".$catb."', '".$catbe."', '".$catc."', '".$catce."', '".$catd."', '".$catde."', '".$catd1."', '".$cath."', '".$validity."')";
		$result = $db->query($query) or die($db->error);
		return 'Le nouveau Chauffeur est ajouté avec succès.';
	}
	
	function add_vehicule($code1, $code2, $brand, $firstwork, $volume, $weight, $driver_id) { 
		global $db;
		
		$query = "INSERT into vehicules(ID, mat1, mat2, marque, datecirculation, volume, poids, chauffeur) VALUES(NULL,'".$code1."', '".$code2."', '".$brand."', '".$firstwork."', '".$volume."', '".$weight."', '".$driver_id."')";
		$result = $db->query($query) or die($db->error);
		return 'Le nouveau Moyen de Transport est ajouté avec succès.';
	}
	
	function driver_list() {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from drivers  ";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		//$grandTotal = 0;
		//$paid = 0;
		$rows = '';
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			
			$category='';
			$cin = $tr_detail_row['cin'];
			$name = $tr_detail_row['full_name'];
			$phone = $tr_detail_row['phone'];
			$nbpermis = $tr_detail_row['nbpermis'];
			$b = $tr_detail_row['catb']; if ($b==1) {$category .= ' B ';} 
			$be = $tr_detail_row['catbe']; if ($be==1) {$category .= '- BE ';} 
			$c = $tr_detail_row['catc']; if ($c==1) {$category .= '- C ';} 
			$ce = $tr_detail_row['catce']; if ($ce==1) {$category .= '- CE ';} 
			$d = $tr_detail_row['catd']; if ($d==1) {$category .= '- D ';} 
			$de = $tr_detail_row['catde']; if ($de==1) {$category .= '- DE ';} 
			$d1 = $tr_detail_row['catd1']; if ($d1==1) {$category .= '- D1 ';} 
			$h = $tr_detail_row['cath']; if ($h==1) {$category .= '- H ';} 
			$validity = $tr_detail_row['validity'];
			$validity = strtotime($validity);
	
			$rows .= "<tr><td>";
			$rows .= $cin;
			$rows .= "</td><td>";
			$rows .= $name;
			$rows .= "</td><td>";
			$rows .= $phone;
			$rows .= "</td><td>";
			$rows .= $nbpermis;
			$rows .= "</td><td>";
			$rows .= $category;
			$rows .= "</td><td>";
			$rows .= date('d-m-Y', $validity);
			$rows .= "</td></tr>";
		}
		/*$return_message = array(
			"rows" => $rows
		);
		return $return_message;*/
		echo $rows;
	}//view purchase invoice ends here.
	
	function vehicule_list() {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from vehicules  ";
		$tr_detail_result = $db->query($tr_detail_query) or die($db->error);
	
		//$grandTotal = 0;
		//$paid = 0;
		$rows = '';
	
		while($tr_detail_row = $tr_detail_result->fetch_array()) { 
			
			
			$code1 = $tr_detail_row['mat1'];
			$code2 = $tr_detail_row['mat2'];
			$marque = $tr_detail_row['marque'];
			//$marque = $strtoupper($marque);
			$datecirculation = $tr_detail_row['datecirculation'];
			$volume = $tr_detail_row['volume'];
			$poids = $tr_detail_row['poids'];
			$driver_id = $tr_detail_row['chauffeur'];
			$order= new Order;
			$chauffeur = $order->get_driver_info($driver_id,"full_name");
			$datecirculation = strtotime($datecirculation);
	
			$rows .= "<tr><td>";
			$rows .= $marque;
			$rows .= "</td><td>";
			$rows .= $code1." TUN ".$code2;
			$rows .= "</td><td>";
			$rows .= date('d-m-Y', $datecirculation);
			$rows .= "</td><td>";
			$rows .= $volume." m&sup3;";
			$rows .= "</td><td>";
			$rows .= $poids." Kg";
			$rows .= "</td><td>";
			$rows .= $chauffeur;
			$rows .= "</td></tr>";
		}
		/*$return_message = array(
			"rows" => $rows
		);
		return $return_message;*/
		echo $rows;
	}//view purchase invoice ends here.
	
	function driver_options() {
		global $db;
		$query = "SELECT * from drivers ORDER by full_name ASC";
		$result = $db->query($query) or die($db->error);
		
		
		$options = '';
		
		//if($client_id != '') { 
			while($row = $result->fetch_array()) {
				$cin = $row['cin'];
				$phone = $row['phone'];
				$options .= '<option  value="'.$cin.'">'.$row['full_name'].' (TEL:'.$phone.')</option>';
					
				}
			
		return $options;
		
		 
	}//vendor options ends here.
	function add_order_reception($order_id, $datetime, $product_id, $qty1, $qty2) { 
		global $db;
		
		$query = "INSERT into order_received(order_received_id, order_id, date_reception, warehouse_id, product_id, qty, qty_appr, agent_id) VALUES(NULL, '".$order_id."', '".$datetime."', '".$_SESSION['warehouse_id']."', '".$product_id."', '".$qty1."', '".$qty2."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	function add_inventory($inn, $out_inv, $product_id, $order_id) {
		global $db;
		$query = "INSERT into inventory(inventory_id, inn, out_inv, product_id, warehouse_id, order_id) VALUES(NULL, '".$inn."', '".$out_inv."', '".$product_id."', '".$_SESSION['warehouse_id']."', '".$order_id."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;	
	}
	function receive_order($order_id) { 
		global $db;
		
		$query = "UPDATE orders SET received = '1' WHERE order_id='".$order_id."'";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	}
	function receive_order_details($order_id) {
		global $db;
		
		/*Products Detail.*/
		$tr_detail_query = "SELECT * from order_approved WHERE order_id='".$order_id."'";
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
		
		
		echo $rows;
		echo '<input type="text" style="display: none" name="nb" id="nb" value="'.$nb.'"  >';
		
	}
	
	function order_count($warehouse_id) {
		global $db;
		
		$query = "SELECT * FROM orders where warehouse_id = '".$warehouse_id."'";
		$result = $db->query($query) or die($db->error);
		$count = 0;
		
		while($row = $result->fetch_array()) { 
			$count += 1;
		}
		return $count;
	}
	function reception_count($warehouse_id) {
		global $db;
		
		$query = "SELECT * FROM orders where warehouse_id = '".$warehouse_id."' AND received ='1'";
		$result = $db->query($query) or die($db->error);
		$count = 0;
		
		while($row = $result->fetch_array()) { 
			$count += 1;
		}
		return $count;
	}
	function count_products_ordered($warehouse_id) {
		global $db;
		
		$query = "SELECT * FROM order_detail where warehouse_id = '".$warehouse_id."'";
		$result = $db->query($query) or die($db->error);
		$count = 0;
		
		while($row = $result->fetch_array()) { 
			$count += $row['qty'];
		}
		return $count;
	}
	function count_products_received($warehouse_id) {
		global $db;
		
		$query = "SELECT * FROM order_detail, orders WHERE orders.order_id = order_detail.order_id AND orders.warehouse_id = '".$warehouse_id."' AND orders.received ='1'";
		$result = $db->query($query) or die($db->error);
		$count = 0;
		
		while($row = $result->fetch_array()) { 
			$count += $row['qty'];
		}
		return $count;
	}
	
	function top5_purshased_products($warehouse_id) {
		global $db;
		
		$query = "SELECT qty from order_detail WHERE warehouse_id ='".$warehouse_id."'";
		$result = $db->query($query) or die($db->error);
		$total = 0;
		$percentage = 0;
		while($row = $result->fetch_array()) {
			$total += $row['qty'];
		}
		
		$tr_detail_query = "SELECT * FROM (SELECT product_id, SUM(qty) as qte from order_detail GROUP BY product_id ORDER BY qte DESC ) AS D Limit 5";
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
	
	function top5_purchased_products_chart($warehouse_id) {
		global $db;
		
		$tr_detail_query = "SELECT * FROM (SELECT product_id, SUM(qty) as qte from order_detail WHERE warehouse_id='".$warehouse_id."' GROUP BY product_id ORDER BY qte DESC ) AS D Limit 5";
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
	
}//Purchase Class Ends here.