<?php
//Notes Class

class Warehouse
{
	public $warehouse_id;
	public $name;
	public $address;
	public $city;
	public $state;
	public $country;
	public $manager;
	public $contact;
	public $surface;
	public $volume;
	public $freezone;

	function set_warehouse($warehouse_id)
	{
		global $db;
		$query = 'SELECT * from warehouses WHERE warehouse_id="' . $warehouse_id . '" ';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		extract($row);
		$this->name = $name;
		$this->address = $address;
		$this->city = $city;
		$this->state = $state;
		$this->country = $country;
		$this->manager = $manager;
		$this->contact = $contact;
		$this->surface = $surface;
		$this->volume = $volume;
		$this->freezone = $freezone;
	} //Set Warehouse ends here..

	function update_warehouse($warehouse_id, $name, $address, $city, $state, $country, $manager, $contact)
	{
		global $db;
		$query = 'UPDATE warehouses SET
				  name = "' . $name . '",
				  address = "' . $address . '",
				  city = "' . $city . '",
				  state = "' . $state . '",
				  country = "' . $country . '",
				  manager = "' . $manager . '",
				  contact = "' . $contact . '"
				   WHERE warehouse_id="' . $warehouse_id . '" ';
		$result = $db->query($query) or die($db->error);
		return 'Warehouse updated Successfuly!';
	} //update user level ends here.	

	function add_warehouse($name, $address, $city, $state, $country, $manager, $contact, $area, $volume, $freezone)
	{
		global $db;
		$query = "SELECT * from warehouses WHERE name='" . $name . "' ";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;

		if ($num_rows > 0) {
			return 'A warehouse with same name already exists.';
		} else {
			$query = "INSERT into warehouses(warehouse_id, name, address, city, state, country, manager, contact, surface, volume, freezone)
				VALUES(NULL, '" . $name . "', '" . $address . "', '" . $city . "', '" . $state . "', '" . $country . "', '" . $manager . "', '" . $contact . "', '" . $area . "', '" . $volume . "', '" . $freezone . "')
			";
			$result = $db->query($query) or die($db->error);
			return 'Warehouse added successfuly.';
		}
	} //add warehouse ends here.

	function list_warehouses()
	{
		global $db;
		if (partial_access('admin')) {
			$query = 'SELECT * from warehouses';
		} else {
			$query = "SELECT * from warehouses as w1, warehouse_access as w2 WHERE w1.warehouse_id=w2.warehouse_id AND w2.user_id='" . $_SESSION["user_id"] . "'";
		}
		$result = $db->query($query) or die($db->error);
		$content = '';
		$count = 0;
		while ($row = $result->fetch_array()) {
			extract($row);

			$count++;
			if ($count % 2 == 0) {
				$class = 'even';
			} else {
				$class = 'odd';
			}
			$content .= '<tr class="' . $class . '" >';
			$content .= '<tr>';
			$content .= '<td>';
			$content .= $warehouse_id;
			$content .= '</td><td>';
			$content .= $name;
			$content .= '</td><td>';
			$content .= $address;
			$content .= '</td><td>';
			$content .= $city;
			$content .= '</td><td>';
			$content .= $state;
			$content .= '</td><td>';
			$content .= $country;
			$content .= '</td><td>';
			$content .= $manager;
			$content .= '</td><td>';
			$content .= $contact;
			$content .= '</td><td>';
			$content .= '<form method="post" name="view_warehouses" action="warehouse.php">';
			$content .= '<input type="hidden" name="warehouse_id" value="' . $warehouse_id . '">';
			$content .= '<input type="submit" class="btn btn-default btn-sm" value="Select Warehouse">';
			$content .= '</form>';
			$content .= '</td>';
			if (partial_access('admin')) {
				$content .= '<td><form method="post" name="edit" action="editwarehouse.php?w=' . $warehouse_id . '">';
				$content .= '<input type="hidden" name="edit_warehouse" value="' . $warehouse_id . '">';
				$content .= '<button type="submit" class="btn btn-success" value="Edit" ><i class="fa fa-cog" aria-hidden="true"></i></button> ';
				$content .= '</form>';
				$content .= '</td><td>';
				/*$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
			$content .= '<input type="hidden" name="delete_warehouse" value="'.$warehouse_id.'">';
			$content .= '<button type="submit" class="btn btn-danger" value="Delete" ><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
			$content .= '</form>';
			$content .= '</td>';*/
			}
			$content .= '</tr>';

			unset($class);
		} //loop ends here.	

		echo $content;
	} //list_notes ends here.

	function delete_warehouse($warehouse_id)
	{
	} //warehouse ends here.

	function disable_warehouse($warehouse_id)
	{
		global $db;

		$product = new Product;
		$qty = $product->total_qty($warehouse_id);
		if ($qty == 0) {
			$message = '<div class="alert alert-danger">The Warehouse you want to delete contains products, please transfer the products to another warehouse before deleting !! See the list of products <a href="products.php"> HERE </a></div> ';
		} else {
			$query = "Update warehouses SET disabled='1' WHERE warehouse_id='" . $warehouse_id . "'";
			$result = $db->query($query) or die($db->error);
			$message = $qty . "-" . "<div class='alert alert-info'>Warehosue successfully deleted  !!</div>";
		}
		return $message;
	} //warehouse ends here.



	function warehouse_options()
	{
		global $db;
		$query = 'SELECT * from warehouses ORDER by warehouse_id ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';

		while ($row = $result->fetch_array()) {

			$options .= '<option  value="' . $row['warehouse_id'] . '">' . $row['warehouse_id'] . ' | ' . $row['name'] . ' (' . $row['city'] . ')</option>';
		}

		echo $options;
	}

	function warehouse_options_by_inv($product_id)
	{
		global $db;

		$wareHouse = '';
		$queryWarehouse = 'SELECT * from inventory WHERE product_id="' . $product_id . '" AND store_id="' . $_SESSION['store_id'] . '"';
		$resultWarehouse = $db->query($queryWarehouse) or die($db->error);

		while ($rowWarehouse = $resultWarehouse->fetch_array()) {
			$wh_id = $rowWarehouse['warehouse_id'];

			$available = 0;
			$queryAvailable = 'SELECT * from inventory WHERE product_id="' . $product_id . '" AND warehouse_id="' . $wh_id . '"';
			$resultAvailable = $db->query($queryAvailable) or die($db->error);

			while ($rowAvailable = $resultAvailable->fetch_array()) {
				$available +=  $rowAvailable['inn'];
				$available -= $rowAvailable['out_inv'];
			}

			$wh_array[] = '';
			if ($available != 0 || $available != '') {
				if (in_array($wh_id, $wh_array)) {
				} else {
					$wh_nameQuery = 'SELECT * from warehouses WHERE warehouse_id="' . $wh_id . '"';
					$wh_nameResult = $db->query($wh_nameQuery) or die($db->error);
					$wh_nameRow = $wh_nameResult->fetch_array();

					$wh_name = $wh_nameRow['name'];
					$wareHouse .= "<option value='" . $wh_id . "'>";
					$wareHouse .= $wh_name . '  (' . $available . ')';
					$wareHouse .= "</option>";
					$wh_array[] = $wh_id;
				}
			}
		}
		return $wareHouse;
	} //warehouse_options_by_inv

	function get_warehouse_info($warehouse_id, $term)
	{
		global $db;
		$query = "SELECT * from warehouses WHERE warehouse_id='" . $warehouse_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	} //get user email ends here.


	function get_warehouse_name()
	{
		global $db;
		$cont = '<li><a href="addstock.php';
		$cont .= '?sess=' . session_id() . '';
		$cont .= '">';
		$cont .= ' <span class="text-success"> <i class="fa fa-caret-right"></i> Ajout Stock </span> ';
		$cont .= '</a></li>';

		$query = "SELECT * from warehouses as w1, warehouse_access as w2 WHERE w1.warehouse_id=w2.warehouse_id AND w2.user_id='" . $_SESSION["user_id"] . "'";
		$result = $db->query($query) or die($db->error);

		while ($row = $result->fetch_array()) {

			$cont .= '<li><a href="products_warh.php?wid=';
			$cont .= $row["warehouse_id"];
			$cont .= '&sess=' . session_id() . '';
			$cont .= '">';
			$cont .= '<span class="text-success"> <i class="fa fa-caret-right"></i> ' . $row["name"] . '</span>';
			$cont .= '</a></li>';
		}
		echo $cont;

		//return $row["name"];
	}

	function list_warehouse_logs($argument)
	{
		global $db;
		if ($argument == 'all') {
			$log_query = "SELECT * from warehouse_log WHERE store_id='" . $_SESSION['store_id'] . "' ORDER by date ASC";
		} else {
			$log_query = "SELECT * from warehouse_log WHERE store_id='" . $_SESSION['store_id'] . "' AND current_wh_id='" . $argument . "' OR new_wh_id='" . $argument . "' ORDER by date ASC";
		}
		$log_result = $db->query($log_query) or die($db->error);

		$table = '';
		while ($log_row = $log_result->fetch_array()) {
			extract($log_row);

			$product_query = "SELECT * from products WHERE product_id='" . $product_id . "'";
			$product_result = $db->query($product_query) or die($db->error);
			$product_row = $product_result->fetch_array();

			$users = new Users;
			$agent = $users->get_user_info($agent_id, 'first_name') . ' ' . $users->get_user_info($agent_id, 'last_name');

			$products = new Product;
			$product_title = $products->get_product_info($product_id, 'product_name');

			$table .= '<tr><td>';
			$table .= $wh_log_id;
			$table .= '</td><td>';
			$datetime = strtotime($date);
			$table .= date('d-m-Y', $datetime);
			$table .= '</td><td>';
			$table .= $product_title;
			$table .= '</td><td>';
			$table .= $units;
			$table .= '</td><td>';
			$table .= $this->get_warehouse_info($current_wh_id, 'name');
			$table .= '</td><td>';
			$table .= $this->get_warehouse_info($new_wh_id, 'name');
			$table .= '</td><td>';
			$table .= $agent;
			$table .= '</td></tr>';
		} //ends here.
		echo $table;
	} //warehouse Logs

	function ware_house_details()
	{
		global $db;

		$wh_query = "SELECT * from warehouses WHERE store_id='" . $_SESSION['store_id'] . "' ORDER by name ASC";
		$wh_result = $db->query($wh_query) or die($db->error);
		$table = '';

		$total_inventory = 0;
		$inventory_query = "SELECT * from inventory WHERE warehouse_id='0'";
		$inventory_result = $db->query($inventory_query) or die($db->error);

		while ($inventory_row = $inventory_result->fetch_array()) {
			$total_inventory += $inventory_row['inn'];
			$total_inventory -= $inventory_row['out_inv'];
		} //inventory calculation loop ends here.

		$table .= '<tr><td>';
		$table .= '0';
		$table .= '</td><td>';
		$table .= 'Unallocated Inventory';
		$table .= '</td><td>';
		$table .= '&nbsp;';
		$table .= '</td><td>';
		$table .= $total_inventory;
		$table .= '</td><td>';
		$table .= '<a href="wh_inv_detail.php?wh_id=0">Inventory Detail</a>';
		$table .= '</td></tr>';

		while ($wh_row = $wh_result->fetch_array()) {
			$total_inventory = 0;
			$inventory_query = "SELECT * from inventory WHERE warehouse_id='" . $wh_row['warehouse_id'] . "'";
			$inventory_result = $db->query($inventory_query) or die($db->error);

			while ($inventory_row = $inventory_result->fetch_array()) {
				$total_inventory += $inventory_row['inn'];
				$total_inventory -= $inventory_row['out_inv'];
			} //inventory calculation loop ends here.

			$table .= '<tr><td>';
			$table .= $wh_row['warehouse_id'];
			$table .= '</td><td>';
			$table .= $wh_row['name'];
			$table .= '</td><td>';
			$table .= $wh_row['manager'];
			$table .= '</td><td>';
			$table .= $total_inventory;
			$table .= '</td><td>';
			$table .= '<a href="wh_inv_detail.php?wh_id=' . $wh_row['warehouse_id'] . '">Inventory Detail</a>';
			$table .= '</td></tr>';
		} //while loop for warehouse details
		echo $table;
	} //ware_house_details Ends here

	function products_list($wh_id)
	{
		global $db;
		$inventory_query = "SELECT * from inventory WHERE warehouse_id='" . $wh_id . "' ORDER by product_Id ASC";
		$inventory_result = $db->query($inventory_query) or die($db->error);

		$table = '';
		while ($inventory_row = $inventory_result->fetch_array()) {
			extract($inventory_row);
			$product_id_arr[] = $product_id;
		}
		if (isset($product_id_arr)) {
			$product_id_arr = array_unique($product_id_arr);
			foreach ($product_id_arr as $val) {
				$products = new Product;
				$product_name = $products->get_product_info($val, 'product_name');

				$units_query = "SELECT * from inventory WHERE warehouse_id='" . $wh_id . "' AND product_id='" . $val . "'";
				$units_result = $db->query($units_query) or die($db->error);

				$units = 0;

				while ($units_row = $units_result->fetch_array()) {
					$units += $units_row['inn'] - $units_row['out_inv'];
				} //units result

				$table .= '<tr><td>';
				$table .= $val;
				$table .= '</td><td>';
				$table .= $product_name;
				$table .= '</td><td>';
				$table .= $units;
				$table .= '</td><td>';
				$table .= '<a href="wh_inv_detail.php?wh_id=' . $wh_id . '&product_id=' . $val . '&avail_units=' . $units . '&product_name=' . $product_name . '">Transfer</a>';
				$table .= '</td></tr>';
			} //foreach ends here.
		} else {
			$table .= 'No inventory available.';
		}

		echo $table;
	} //End of products list function.

	function warehouse_transfer($units, $product_id, $curr_wh_id, $new_wh_id)
	{
		global $db;

		$minus_query = "INSERT into inventory VALUES('', '0', '" . $units . "', '" . $_SESSION['store_id'] . "', '" . $product_id . "', '" . $curr_wh_id . "')";
		$minus_result = $db->query($minus_query) or die($db->error);

		$add_query = "INSERT into inventory VALUES('', '" . $units . "', '0', '" . $_SESSION['store_id'] . "', '" . $product_id . "', '" . $new_wh_id . "')";
		$add_result = $db->query($add_query) or die($db->error);

		$add_log_query = "INSERT into warehouse_log VALUES('', '" . date('Y-m-d') . "', '" . $product_id . "', '" . $units . "', '" . $curr_wh_id . "', '" . $new_wh_id . "', '" . $_SESSION['store_id'] . "', '" . $_SESSION['user_id'] . "')";
		$add_log_result = $db->query($add_log_query) or die($db->error);
		return 'Stock moved successfully.';
	} //warehouse transfer.
	function add_inventory($inn, $out_inv, $product_id, $warehouse_id, $lot)
	{
		global $db;
		$datetime = date("Y-m-d");
		//$datetime = date('d-m-Y', $datetime);
		$query = "INSERT into inventory(inventory_id, dateinventory, inn, out_inv, warehouse_id, product_id, lot) VALUES(NULL, '" . $datetime . "', '" . $inn . "', '" . $out_inv . "', '" . $warehouse_id . "', '" . $product_id . "', '" . $lot . "')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;
	} //add inventory function ends here.

	function occuped_volume($warehouse_id)
	{
		global $db;
		$volume_total = 0;
		$query = "SELECT * from products,dimensions WHERE products.warehouse_id='" . $warehouse_id . "' AND dimensions.product_id = products.product_id";
		$result = $db->query($query) or die($db->error);
		while ($row = $result->fetch_array()) {
			$pr_id = $row['product_id'];
			$long_pr = $row['long_pr'];
			$larg_pr = $row['larg'];
			$haut_pr = $row['haut'];
			$qtyin = 0;
			$qtyout = 0;
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $pr_id . "' AND warehouse_id='" . $warehouse_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];

			$volume = $inventory * $long_pr * $larg_pr * $haut_pr / 1000000;
			$volume_total += $volume;
		}


		return $volume_total;
	} //get user email ends here.
	function get_inventory_inn($warehouse_id, $date1, $date2)
	{
		global $db;
		$items = 0;
		$date1 = strtotime($date1);
		$date1 = date('Y-m-d', $date1);
		$date2 = strtotime($date2);
		$date2 = date('Y-m-d', $date2);
		$query = "SELECT SUM(inn) as qty_in from inventory WHERE warehouse_id='" . $warehouse_id . "' AND dateinventory BETWEEN '" . $date1 . "' AND '" . $date2 . "' ";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		$items = $row['qty_in'];
		if ($items == NULL) {
			$items = 0;
		}
		return $items;
	} //get user email ends here.
	function get_inventory_out($warehouse_id, $date1, $date2)
	{
		global $db;
		$qty = 0;
		$date1 = strtotime($date1);
		$date1 = date('Y-m-d', $date1);
		$date2 = strtotime($date2);
		$date2 = date('Y-m-d', $date2);
		$query = "SELECT SUM(out_inv) as qty_out from inventory WHERE warehouse_id='" . $warehouse_id . "' AND dateinventory BETWEEN '" . $date1 . "' AND '" . $date2 . "' ";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		$items = $row['qty_out'];
		if ($items == NULL) {
			$items = 0;
		}
		return $items;
	} //get user email ends here.
	function warehouse_cost($warehouse_id)
	{
		global $db;
		$cost_pr = 0;
		$cost_total = 0;
		$query = "SELECT * from products,price WHERE products.warehouse_id='" . $warehouse_id . "' AND price.product_id = products.product_id";
		$result = $db->query($query) or die($db->error);
		while ($row = $result->fetch_array()) {
			$pr_id = $row['product_id'];
			$cost = $row['cost'];

			$qtyin = 0;
			$qtyout = 0;
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $pr_id . "' AND warehouse_id='" . $warehouse_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];

			$cost_pr = $inventory * $cost;
			$cost_total += $cost_pr;
		}


		return $cost_total;
	} //get user email ends here.

}//class ends here.