<?php
//product Class

class Product
{
	public $product_id;
	public $product_manual_id;
	public $product_name;
	public $product_description;
	public $product_unit;
	public $category_id;
	public $tax_id;
	public $product_image;
	public $alert_units;
	public $supplier;
	public $product_cost;
	public $product_selling_price;
	public $long_pr;
	public $larg;
	public $haut;
	public $poids;
	public $pounds;
	public $ounces;
	public $photo;

	function moid_set_product_through_sku($sku, $store_id = '')
	{
		global $db;
		$important = new ImportantFunctions();
		$query = "SELECT * from products WHERE product_manual_id='" . $sku . "' LIMIT 1";
		$result = $db->query($query) or die($db->error);
		if ($result->num_rows == 0) {
			$response = $important->getOrderDataThroughOrderIDShipengin($store_id);
			foreach ($response->sales_orders as $key => $value) {
				$product = new Product();

				foreach ($value->sales_order_items as $key => $lineItems) {
					$lineItemsDetails = $lineItems->line_item_details;
					$resp = $product->moidAddProduct(
						$lineItemsDetails->sku,
						$lineItemsDetails->name,
						$lineItemsDetails->weight->unit,
						$lineItems->price_summary->estimated_tax->amount,
						$lineItems->price_summary->unit_price->amount,
						$lineItems->price_summary->unit_price->amount,
						100,
						$value->external_order_number,
						$store_id,
					);
				}
			}
		} else {
			$row = $result->fetch_array();
			extract($row);
			$this->product_manual_id = $product_manual_id;
			$this->product_name = $product_name;
			$this->photo = $row['photo'];
			//$this->product_description = $product_description;
			$this->product_unit = $product_unit;
			$this->category_id = $category_id;
			$this->tax_id = $tax_id;
			//$this->product_image = $product_image;
			$this->alert_units = $alert_units;
			//query supplier.
			// $query_supplier = "SELECT * from suppliers WHERE supplier_id='" . $supplier_id . "'";
			// $result_supplier = $db->query($query_supplier) or die($db->error);
			// $row_supplier = $result_supplier->fetch_array();

			// $this->supplier = $row_supplier['full_name'];

			//query cost and selling price.
			$query_cost = "SELECT * from price WHERE product_id='" . $row[0] . "' ORDER by price_id ASC LIMIT 1";
			$result_cost = $db->query($query_cost) or die($db->error);
			$row_cost = $result_cost->fetch_array();

			$this->product_cost = $row_cost['cost'];
			$this->product_selling_price = $row_cost['selling_price'];
			//query dimensions
			$query_dimensions = "SELECT * from dimensions WHERE product_id='" . $row[0] . "'";
			$result_dimensions = $db->query($query_dimensions) or die($db->error);
			$row_dimensions = $result_dimensions->fetch_array();

			$this->long_pr = $row_dimensions['long_pr'];
			$this->larg = $row_dimensions['larg'];
			$this->haut = $row_dimensions['haut'];
			$this->poids = $row_dimensions['poids'];
			$this->pounds = $row_dimensions['pounds'];
			$this->ounces = $row_dimensions['ounces'];
		}
	}


	function set_product($product_id)
	{
		global $db;
		$query = "SELECT * from products WHERE product_id='" . $product_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		extract($row);

		$this->product_manual_id = $product_manual_id;
		$this->product_name = $product_name;
		$this->photo = $row['photo'];
		//$this->product_description = $product_description;
		$this->product_unit = $product_unit;
		$this->category_id = $category_id;
		$this->tax_id = $tax_id;
		//$this->product_image = $product_image;
		$this->alert_units = $alert_units;
		//query supplier.
		$query_supplier = "SELECT * from suppliers WHERE supplier_id='" . $supplier_id . "'";
		$result_supplier = $db->query($query_supplier) or die($db->error);
		$row_supplier = $result_supplier->fetch_array();

		$this->supplier = $row_supplier['full_name'];

		//query cost and selling price.
		$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' ORDER by price_id ASC LIMIT 1";
		$result_cost = $db->query($query_cost) or die($db->error);
		$row_cost = $result_cost->fetch_array();

		$this->product_cost = $row_cost['cost'];
		$this->product_selling_price = $row_cost['selling_price'];
		//query dimensions
		$query_dimensions = "SELECT * from dimensions WHERE product_id='" . $product_id . "'";
		$result_dimensions = $db->query($query_dimensions) or die($db->error);
		$row_dimensions = $result_dimensions->fetch_array();

		$this->long_pr = $row_dimensions['long_pr'];
		$this->larg = $row_dimensions['larg'];
		$this->haut = $row_dimensions['haut'];
		$this->poids = $row_dimensions['poids'];
		$this->pounds = $row_dimensions['pounds'];
		$this->ounces = $row_dimensions['ounces'];
	}

	function add_product($product_manual_id, $product_name, $supplier_id, $product_unit, $category_id, $tax_id, $product_cost, $product_selling_price, $alert_units, $long_pr, $larg, $haut, $poids)
	{
		global $db;
		$query = "SELECT * from products WHERE product_manual_id='" . $product_manual_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			return 'A product already exist with this id.';
		} else {
			$query = "INSERT into products (product_id, product_manual_id, product_name, supplier_id, product_unit, category_id, tax_id, alert_units, warehouse_id,order_id) VALUES(NULL, '" . $product_manual_id . "', '" . $product_name . "', '" . $supplier_id . "', '" . $product_unit . "', '" . $category_id . "', '" . $tax_id . "', '" . $alert_units . "', '" . $_SESSION['warehouse_id'] . "',NULL)";
			$result = $db->query($query) or die($db->error);
			$product_id = $db->insert_id;

			//inserting values into price table.
			$query_price = "INSERT into price(price_id, cost, selling_price, warehouse_id, product_id) VALUES(NULL, '" . $product_cost . "', '" . $product_selling_price . "', '" . $_SESSION['warehouse_id'] . "', '" . $product_id . "')";
			$result_price = $db->query($query_price) or die($db->error);

			//inserting product rates table.
			$query_rate = "INSERT into product_rates (rate_id, default_rate, level_1, level_2, level_3, level_4, level_5, store_id, product_id) VALUES(NULL, '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $_SESSION['warehouse_id'] . "', '" . $product_id . "')";
			$result_rate = $db->query($query_rate) or die($db->error);

			//inserting dimensions
			$query_dimensions = "INSERT into dimensions (product_id, long_pr, larg, haut, poids) VALUES('" . $product_id . "', '" . $long_pr . "', '" . $larg . "', '" . $haut . "', '" . $poids . "')";
			$result_dimensions = $db->query($query_dimensions) or die($db->error);

			return 'Product was added successfuly!';
		}
	} //add product ends here.


	/***** MOID WORKS STARTS HERE */

	function moidAddProduct($product_manual_id, $product_name, $product_unit, $tax_id, $product_cost, $product_selling_price, $alert_units, $order_id, $storeId)
	{

		global $db;
		$query = "SELECT * from products WHERE order_id='" . $order_id . "'OR product_manual_id='" . $product_manual_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			return 'A product already exist with this id.';
		} else {
			$warehouse = new Warehouse;
			$query = "INSERT into products (product_id, product_manual_id, product_name, product_unit, tax_id, alert_units, warehouse_id,order_id,store_id) VALUES(NULL, '" . $product_manual_id . "', '" . $product_name . "',  '" . $product_unit . "',  '" . $tax_id . "', '" . $alert_units . "', '" . $_SESSION['warehouse_id'] . "', '" . $order_id . "' , '" . $storeId . "')";
			$result = $db->query($query) or die($db->error);
			$product_id = $db->insert_id;

			//inserting values into price table.
			$query_price = "INSERT into price(price_id, cost, selling_price, warehouse_id, product_id) VALUES(NULL, '" . $product_cost . "', '" . $product_selling_price . "', '" . $_SESSION['warehouse_id'] . "', '" . $product_id . "')";
			$result_price = $db->query($query_price) or die($db->error);

			//inserting product rates table.
			$query_rate = "INSERT into product_rates (rate_id, default_rate, level_1, level_2, level_3, level_4, level_5, store_id, product_id) VALUES(NULL, '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $product_selling_price . "', '" . $_SESSION['warehouse_id'] . "', '" . $product_id . "')";
			$result_rate = $db->query($query_rate) or die($db->error);

			// inserting dimensions
			$query_dimensions = "INSERT into dimensions (product_id, long_pr, larg, haut, poids, pounds, ounces) VALUES('" . $product_id . "', 1, 1, 1, 1,1,1)";
			$result_dimensions = $db->query($query_dimensions) or die($db->error);

			$message = $warehouse->add_inventory(500, '0', $product_id, $_SESSION['warehouse_id'], '234234');

			return $product_id;
		}
	}

	function moid_set_product($product_id)
	{
		global $db;
		$query = "SELECT * from products WHERE product_id='" . $product_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		extract($row);
		$this->photo = $photo;

		$this->product_manual_id = $product_manual_id;
		$this->product_name = $product_name;
		//$this->product_description = $product_description;
		$this->product_unit = $product_unit;
		// $this->category_id = $category_id;
		$this->tax_id = $tax_id;
		//$this->product_image = $product_image;
		$this->alert_units = $alert_units;
		//query supplier.
		$query_supplier = "SELECT * from suppliers WHERE supplier_id='" . $supplier_id . "'";
		$result_supplier = $db->query($query_supplier) or die($db->error);
		$row_supplier = $result_supplier->fetch_array();

		$this->supplier = $row_supplier['full_name'];

		//query cost and selling price.
		$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' ORDER by price_id ASC LIMIT 1";
		$result_cost = $db->query($query_cost) or die($db->error);
		$row_cost = $result_cost->fetch_array();

		$this->product_cost = $row_cost['cost'];
		$this->product_selling_price = $row_cost['selling_price'];
		//query dimensions
		$query_dimensions = "SELECT * from dimensions WHERE product_id='" . $product_id . "'";
		$result_dimensions = $db->query($query_dimensions) or die($db->error);
		$row_dimensions = $result_dimensions->fetch_array();

		$this->long_pr = $row_dimensions['long_pr'];
		$this->larg = $row_dimensions['larg'];
		$this->haut = $row_dimensions['haut'];
		$this->poids = $row_dimensions['poids'];
		$this->pounds = $row_dimensions['pounds'];
		$this->ounces = $row_dimensions['ounces'];
	}

	/***** MOID WORKS ENDS HERE */


	function add_dimensions($product_id, $long_pr, $larg, $haut, $poids, $pounds, $ounces)
	{
		global $db;
		$query_dimensions = "INSERT into dimensions (product_id, long_pr, larg, haut, poids,pounds,ounces) VALUES('" . $product_id . "', '" . $long_pr . "', '" . $larg . "', '" . $haut . "', '" . $poids . "'.'" . $pounds . "'.'" . $ounces . "')";
		$result_dimensions = $db->query($query_dimensions) or die($db->error);
		$ID = $db->insert_id;
		return 'Dimensions added successfully !!';
	}
	//add dimensions ends here.



	function update_product($edit_product, $product_name, $unit, $category, $cost, $price, $tax, $alert, $long_pr, $larg, $haut, $poids, $photo = null)
	{
		global $db;
		$query = "UPDATE products SET
			product_name='" . $product_name . "',
			product_unit='" . $unit . "',
			category_id='" . $category . "',
			tax_id='" . $tax . "',
			alert_units='" . $alert . "',
			photo='" . $photo . "'
			WHERE product_id='" . $edit_product . "'
		";
		$result = $db->query($query) or die($db->error);

		//update price.
		$update_price = "UPDATE price SET
		cost='" . $cost . "',
		selling_price='" . $price . "'
		WHERE product_id='" . $edit_product . " ORDER by price_id ASC LIMIT 1'
		";
		$result_price = $db->query($update_price) or die($db->error);
		//Updating price ends here.
		$update_rate = "UPDATE product_rates SET
		default_rate='" . $price . "'
		WHERE product_id='" . $edit_product . "'
		";
		$rate_query = $db->query($update_rate) or die($db->error);

		//update dimensions.
		$update_dimensions = "UPDATE dimensions SET
		long_pr='" . $long_pr . "',
		larg='" . $larg . "',
		haut='" . $haut . "',
		poids='" . $poids . "'
	
		WHERE product_id='" . $edit_product . "'
		";
		$result_dimensions = $db->query($update_dimensions) or die($db->error);
		//Updating dimensions ends here.

		return 'Product was updated successfuly.';
	} //update product ends here.

	function moid_update_product($edit_product, $product_name, $unit, $cost, $price, $tax, $alert, $long_pr, $larg, $haut, $poids, $photo, $pounds, $ounces)
	{

		global $db;
		$query = "UPDATE products SET
			product_name='" . $product_name . "',
			product_unit='" . $unit . "',
			tax_id='" . $tax . "',
			alert_units='" . $alert . "',
			photo='" . $photo . "'
			WHERE product_id='" . $edit_product . "'
		";
		$result = $db->query($query) or die($db->error);

		//update price.
		$update_price = "UPDATE price SET
		cost='" . $cost . "',
		selling_price='" . $price . "'
		WHERE product_id='" . $edit_product . " ORDER by price_id ASC LIMIT 1'
		";
		$result_price = $db->query($update_price) or die($db->error);
		//Updating price ends here.
		$update_rate = "UPDATE product_rates SET
		default_rate='" . $price . "'
		WHERE product_id='" . $edit_product . "'
		";
		$rate_query = $db->query($update_rate) or die($db->error);

		//update dimensions.
		$update_dimensions = "UPDATE dimensions SET
		long_pr='" . $long_pr . "',
		larg='" . $larg . "',
		haut='" . $haut . "',
		poids='" . $poids . "',
		pounds='" . $pounds . "',
		ounces='" . $ounces . "'
		WHERE product_id='" . $edit_product . "'
		";
		$result_dimensions = $db->query($update_dimensions) or die($db->error);
		//Updating dimensions ends here.

		return 'Product was updated successfuly.';
	} //update product ends here.

	function list_products()
	{
		global $db;
		$user = new Users;
		$user_function = $user->get_user_info($_SESSION['user_id'], 'user_function');
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
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
			$content .= '<tr >';
			$content .= '<td>';
			$content .= $product_manual_id;
			$content .= '</td><td>';
			$content .= $product_name;
			$content .= '</td><td>';
			$content .= $product_unit;
			// $content .= '</td><td>';
			//category and tax objects to get related information.
			// $product_category = new ProductCategory;
			//$product_tax = new ProductTax;

			// $content .= $product_category->get_category_info($category_id, 'category_name');
			$content .= '</td><td align="right">';
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' AND warehouse_id='" . $warehouse_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			$content .= number_format($alert_units, 2);
			$content .= '</td><td align="right">';
			$content .= number_format($inventory, 2);
			$content .= '</td>';
			//query cost and selling price.
			$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by price_id ASC LIMIT 1";
			$result_cost = $db->query($query_cost) or die($db->error);
			$row_cost = $result_cost->fetch_array();
			if (partial_access('admin') or ($user_function == 'manager')) {
				$content .= '<td align="right">';
				$content .= number_format($row_cost['cost'], 2) . ' $';
				$content .= '</td><td <td align="right">';

				$content .= number_format($row_cost['selling_price'], 2) . ' $';
				$content .= '</td>';
			}
			$content .= '<td <td <td align="center">';
			if ($inventory >  $alert_units) {
				$content .= '<span class="text-success">Available</span>';
			} else {
				$content .= '<span class="text-danger">Alert</span>';
			}
			$content .= '</td>';
			if ($row['photo'] != null) {
				$content .= '<td>';
				$content .= '<img width="100" height="100" src="upload/' . $row['photo'] . '"/>';
				$content .= '</td>';
			} else {
				$content .= '<td>';
				$content .= '</td>';
			}

			if (partial_access('admin') or ($user_function == 'manager')) {
				$content .= '<td class="no-print">';
				$content .= '<form method="post" name="edit" action="editproduct.php">';
				$content .= '<input type="hidden" name="edit_product" value="' . $product_id . '">';
				$content .= '<button type="submit" class="btn btn-success" value="Edit"><i class="fa fa-cog" aria-hidden="true"></i></button>';
				$content .= '</form>';
				$content .= '</td>';
			}
			if (partial_access('admin')) {
				$content .= '<td class="no-print">';
				$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_product" value="' . $product_id . '">';
				$content .= '<button type="submit" class="btn btn-danger" value="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
				$content .= '</form>';
				$content .= '</td>';
			}
			$content .= '</tr>';
			unset($class);
		} //loop ends here.
		echo $content;
	}
	function list_products_inventory()
	{
		global $db;
		$user = new Users;
		$user_function = $user->get_user_info($_SESSION['user_id'], 'user_function');
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
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
			$content .= '<tr >';
			$content .= '<td>';
			$content .= $product_manual_id;
			$content .= '</td><td>';
			$content .= $product_name;
			$content .= '</td><td>';
			$content .= $product_unit;
			$content .= '</td><td>';
			//category and tax objects to get related information.
			$product_category = new ProductCategory;
			//$product_tax = new ProductTax;

			$content .= $product_category->get_category_info($category_id, 'category_name');
			$content .= '</td><td align="center">';
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' AND warehouse_id='" . $warehouse_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			$content .= number_format($alert_units, 2);
			$content .= '</td><td align="center">';
			$content .= number_format($inventory, 2);
			$content .= '</td>';
			//query cost and selling price.
			$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by price_id ASC LIMIT 1";
			$result_cost = $db->query($query_cost) or die($db->error);
			$row_cost = $result_cost->fetch_array();
			if (partial_access('admin') or ($user_function == 'manager')) {
				$content .= '<td align="right">';
				$content .= number_format($row_cost['cost'], 2) . ' $';;
				$content .= '</td><td <td align="right">';

				$content .= number_format($row_cost['selling_price'], 2) . ' $';
				$content .= '</td>';
			}
			$content .= '<td <td <td align="center">';
			if ($inventory >  $alert_units) {
				$content .= '<span class="text-success">Available</span>';
			} else {
				$content .= '<span class="text-danger">Alert</span>';
			}
			$content .= '</td>';

			$content .= '</tr>';
			unset($class);
		} //loop ends here.
		echo $content;
	}

	function list_products_inventory_for_store()
	{
		global $db;
		$user = new Users;
		$user_function = $user->get_user_info($_SESSION['user_id'], 'user_function');
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
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
			$content .= '<tr >';
			$content .= '<td>';
			$content .= $product_manual_id;
			$content .= '</td><td>';
			$content .= $product_name;
			$content .= '</td><td>';
			$content .= $product_unit;
			$content .= '</td><td>';
			//category and tax objects to get related information.
			$product_category = new ProductCategory;
			//$product_tax = new ProductTax;

			$content .= $product_category->get_category_info($category_id, 'category_name');
			$content .= '</td><td align="center">';
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' AND warehouse_id='" . $warehouse_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			$content .= number_format($alert_units, 2);
			$content .= '</td><td align="center">';
			$content .= number_format($inventory, 2);
			$content .= '</td>';
			//query cost and selling price.
			$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by price_id ASC LIMIT 1";
			$result_cost = $db->query($query_cost) or die($db->error);
			$row_cost = $result_cost->fetch_array();
			if (partial_access('admin') or ($user_function == 'manager')) {
				$content .= '<td align="right">';
				$content .= number_format($row_cost['cost'], 2) . ' $';;
				$content .= '</td><td <td align="right">';

				$content .= number_format($row_cost['selling_price'], 2) . ' $';
				$content .= '</td>';
			}
			$content .= '<td <td <td align="center">';
			if ($inventory >  $alert_units) {
				$content .= '<span class="text-success">Available</span>';
			} else {
				$content .= '<span class="text-danger">Alert</span>';
			}
			$content .= '</td>';

			$content .= '</tr>';
			unset($class);
		} //loop ends here.
		echo $content;
	}


	function list_products_alert()
	{
		global $db;
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$content = '';
		$count = 0;
		while ($row = $result->fetch_array()) {
			extract($row);
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' AND warehouse_id='" . $warehouse_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			if ($inventory < $alert_units) {

				$count++;
				if ($count % 2 == 0) {
					$class = 'even';
				} else {
					$class = 'odd';
				}
				$content .= '<tr >';
				$content .= '<td>';
				$content .= $product_manual_id;
				$content .= '</td><td>';
				$content .= $product_name;
				$content .= '</td><td>';
				$content .= $product_unit;
				$content .= '</td><td>';
				$product_category = new ProductCategory;
				$content .= $product_category->get_category_info($category_id, 'category_name');
				$content .= '</td><td align="right">';
				$content .= number_format($alert_units, 2);
				$content .= '</td><td align="right">';
				$content .= number_format($inventory, 2);
				$content .= '</td>';
				//query cost and selling price.
				$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by price_id ASC LIMIT 1";
				$result_cost = $db->query($query_cost) or die($db->error);
				$row_cost = $result_cost->fetch_array();
				if (partial_access('admin')) {
					$content .= '<td align="right">';
					$content .= number_format($row_cost['cost'], 2) . ' $';;
					$content .= '</td><td <td align="right">';

					$content .= number_format($row_cost['selling_price'], 2) . ' $';
					$content .= '</td>';
				}
				$content .= '<td <td <td align="center">';
				if ($inventory >  $alert_units) {
					$content .= '<span class="text-success">Available</span>';
				} else {
					$content .= '<span class="text-danger">Alert</span>';
				}
				$content .= '</td>';
				if (partial_access('admin')) {
					$content .= '<td class="no-print">';
					$content .= '<form method="post" name="edit" action="editproduct.php">';
					$content .= '<input type="hidden" name="edit_product" value="' . $product_id . '">';
					$content .= '<button type="submit" class="btn btn-success" value="Edit"><i class="fa fa-cog" aria-hidden="true"></i></button>';
					$content .= '</form>';
					$content .= '</td><td class="no-print">';
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_product" value="' . $product_id . '">';
					$content .= '<button type="submit" class="btn btn-danger" value="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
					$content .= '</form>';
					$content .= '</td>';
				}
				$content .= '</tr>';
				unset($class);
			}
		} //loop ends here.
		echo $content;
	}

	public function list_product_alert_shipengine()
	{
		global $db;
		$query = "SELECT * from products WHERE store_id='" . $_SESSION['order_source_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$content = '';
		$count = 0;
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_array()) {
				extract($row);
				$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' AND warehouse_id='" . $warehouse_id . "'";
				$inventory_result = $db->query($inventory) or die($db->error);
				$inventory_row = $inventory_result->fetch_array();

				$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
				if ($inventory < $alert_units) {

					$count++;
					if ($count % 2 == 0) {
						$class = 'even';
					} else {
						$class = 'odd';
					}
					$content .= '<tr >';
					$content .= '<td>';
					$content .= $product_manual_id;
					$content .= '</td><td>';
					$content .= $product_name;
					$content .= '</td><td>';
					$content .= $product_unit;
					$content .= '</td><td>';
					$product_category = new ProductCategory;
					$content .= $product_category->get_category_info($category_id, 'category_name');
					$content .= '</td><td align="right">';
					$content .= number_format($alert_units, 2);
					$content .= '</td><td align="right">';
					$content .= number_format($inventory, 2);
					$content .= '</td>';
					//query cost and selling price.
					$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by price_id ASC LIMIT 1";
					$result_cost = $db->query($query_cost) or die($db->error);
					$row_cost = $result_cost->fetch_array();

					$content .= '<td <td <td align="center">';
					if ($inventory >  $alert_units) {
						$content .= '<span class="text-success">Available</span>';
					} else {
						$content .= '<span class="text-danger">Alert</span>';
					}
					$content .= '</td>';

					$content .= '</tr>';
					unset($class);
				}
			} //loop ends here.
			if (empty($content))
				echo '<p>No Stock Alert Found</p>';;
		} else {
		}
	}

	function list_alert_inventory()
	{
		global $db;
		$user = new Users;
		$user_function = $user->get_user_info($_SESSION['user_id'], 'user_function');
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$content = '';
		$count = 0;
		while ($row = $result->fetch_array()) {
			extract($row);
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' AND warehouse_id='" . $warehouse_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			if ($inventory < $alert_units) {

				$count++;
				if ($count % 2 == 0) {
					$class = 'even';
				} else {
					$class = 'odd';
				}
				$content .= '<tr >';
				$content .= '<td>';
				$content .= $product_manual_id;
				$content .= '</td><td>';
				$content .= $product_name;
				$content .= '</td><td>';
				$content .= $product_unit;
				$content .= '</td><td>';
				$product_category = new ProductCategory;
				$content .= $product_category->get_category_info($category_id, 'category_name');
				$content .= '</td><td align="right">';
				$content .= number_format($alert_units, 2);
				$content .= '</td><td align="right">';
				$content .= number_format($inventory, 2);
				$content .= '</td>';
				//query cost and selling price.
				$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by price_id ASC LIMIT 1";
				$result_cost = $db->query($query_cost) or die($db->error);
				$row_cost = $result_cost->fetch_array();
				if (partial_access('admin') or ($user_function == 'manager')) {
					$content .= '<td align="right">';
					$content .= number_format($row_cost['cost'], 2) . ' $';;
					$content .= '</td><td <td align="right">';

					$content .= number_format($row_cost['selling_price'], 2) . ' $';
					$content .= '</td>';
				}
				$content .= '<td <td <td align="center">';
				if ($inventory >  $alert_units) {
					$content .= '<span class="text-success">Available</span>';
				} else {
					$content .= '<span class="text-danger">Alert</span>';
				}
				$content .= '</td>';

				$content .= '</tr>';
				unset($class);
			}
		} //loop ends here.
		echo $content;
	}

	function list_products_out()
	{
		global $db;
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$content = '';
		$count = 0;
		while ($row = $result->fetch_array()) {
			extract($row);
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' AND warehouse_id='" . $warehouse_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			if ($inventory <= '0') {

				$count++;
				if ($count % 2 == 0) {
					$class = 'even';
				} else {
					$class = 'odd';
				}
				$content .= '<tr >';
				$content .= '<td>';
				$content .= $product_manual_id;
				$content .= '</td><td>';
				$content .= $product_name;
				$content .= '</td><td>';
				$content .= $product_unit;
				$content .= '</td><td>';
				$product_category = new ProductCategory;
				$content .= $product_category->get_category_info($category_id, 'category_name');
				$content .= '</td><td align="right">';
				$content .= number_format($alert_units, 2);
				$content .= '</td><td align="right">';
				$content .= number_format($inventory, 2);
				$content .= '</td>';
				//query cost and selling price.
				$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by price_id ASC LIMIT 1";
				$result_cost = $db->query($query_cost) or die($db->error);
				$row_cost = $result_cost->fetch_array();
				if (partial_access('admin')) {
					$content .= '<td align="right">';
					$content .= number_format($row_cost['cost'], 2) . ' $';;
					$content .= '</td><td <td align="right">';

					$content .= number_format($row_cost['selling_price'], 2) . ' $';
					$content .= '</td>';
				}
				$content .= '<td <td <td align="center">';
				if ($inventory >  $alert_units) {
					$content .= '<span class="text-success">Available</span>';
				} else {
					$content .= '<span class="text-danger">Alert</span>';
				}
				$content .= '</td>';
				if (partial_access('admin')) {
					$content .= '<td class="no-print">';
					$content .= '<form method="post" name="edit" action="editproduct.php">';
					$content .= '<input type="hidden" name="edit_product" value="' . $product_id . '">';
					$content .= '<button type="submit" class="btn btn-success" value="Edit"><i class="fa fa-cog" aria-hidden="true"></i></button>';
					$content .= '</form>';
					$content .= '</td><td class="no-print">';
					$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
					$content .= '<input type="hidden" name="delete_product" value="' . $product_id . '">';
					$content .= '<button type="submit" class="btn btn-danger" value="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
					$content .= '</form>';
					$content .= '</td>';
				}
				$content .= '</tr>';
				unset($class);
			}
		} //loop ends here.
		echo $content;
	}

	function list_inventory_out()
	{
		global $db;
		$user = new Users;
		$user_function = $user->get_user_info($_SESSION['user_id'], 'user_function');
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$content = '';
		$count = 0;
		while ($row = $result->fetch_array()) {
			extract($row);
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' AND warehouse_id='" . $warehouse_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			if ($inventory <= '0') {

				$count++;
				if ($count % 2 == 0) {
					$class = 'even';
				} else {
					$class = 'odd';
				}
				$content .= '<tr >';
				$content .= '<td>';
				$content .= $product_manual_id;
				$content .= '</td><td>';
				$content .= $product_name;
				$content .= '</td><td>';
				$content .= $product_unit;
				$content .= '</td><td>';
				$product_category = new ProductCategory;
				$content .= $product_category->get_category_info($category_id, 'category_name');
				$content .= '</td><td align="right">';
				$content .= number_format($alert_units, 2);
				$content .= '</td><td align="right">';
				$content .= number_format($inventory, 2);
				$content .= '</td>';
				//query cost and selling price.
				$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by price_id ASC LIMIT 1";
				$result_cost = $db->query($query_cost) or die($db->error);
				$row_cost = $result_cost->fetch_array();
				if (partial_access('admin') or ($user_function == 'manager')) {
					$content .= '<td align="right">';
					$content .= number_format($row_cost['cost'], 2) . ' $';;
					$content .= '</td><td <td align="right">';

					$content .= number_format($row_cost['selling_price'], 2) . ' $';
					$content .= '</td>';
				}
				$content .= '<td <td <td align="center">';
				if ($inventory >  $alert_units) {
					$content .= '<span class="text-success">Available</span>';
				} else {
					$content .= '<span class="text-danger">Alert</span>';
				}
				$content .= '</td>';

				$content .= '</tr>';
				unset($class);
			}
		} //loop ends here.
		echo $content;
	}



	function stock_detail()
	{
		global $db;
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_manual_id ASC";
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
			$content .= '<tr class="' . $class . '">';
			$content .= '<td>';
			$content .= $product_manual_id;
			$content .= '</td><td>';
			$content .= $product_name;
			$content .= '</td><td>';
			if ($product_image != '') {
				$product_image = '<img class="img-thumbnail" src="../' . $product_image . '" width="75" height="75" />';
			}
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];

			$content .= $product_image;
			$content .= '</td><td>';
			$content .= $inventory;
			$content .= '</td><td>';
			//query cost and selling price.
			$query_cost = "SELECT * from price WHERE product_id='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by price_id DESC LIMIT 1";
			$result_cost = $db->query($query_cost) or die($db->error);
			$row_cost = $result_cost->fetch_array();

			$content .= $row_cost['selling_price'];
			$content .= '</td>';
			$content .= '</tr>';
			unset($class);
		} //loop ends here.
		echo $content;
		echo 'Printed Rows: ' . $count;
	}

	function products_alert()
	{
		global $db;
		$query = "SELECT * from products WHERE store_id='" . $_SESSION['store_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$content = '';

		while ($row = $result->fetch_array()) {
			extract($row);

			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];

			if ($inventory <= $alert_units) {
				$content .= '<tr><td>';
				$content .= $product_manual_id;
				$content .= '</td><td>';
				$content .= $product_name;
				$content .= '</td><td>';
				$content .= substr($product_description, 0, 18);
				$content .= '</td><td>';
				$content .= $alert_units;
				$content .= '</td><td>';
				$content .= $inventory;
				$content .= '</td></tr>';
			}
		} //loop ends here.
		echo $content;
	}

	function total_qty($warehouse_id)
	{
		global $db;
		$query = "SELECT * from products WHERE warehouse_id='" . $warehouse_id . "'";
		$result = $db->query($query) or die($db->error);
		$count = 0;

		while ($row = $result->fetch_array()) {
			extract($row);

			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' AND warehouse_id='" . $warehouse_id . "' ";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			$count += $inventory;
		} //loop ends here.
		return $count;
	}

	function products_alert_count()
	{
		global $db;
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$count = 0;

		while ($row = $result->fetch_array()) {
			extract($row);

			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' ";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];

			if (($inventory <= $alert_units)) {
				$count++;
			}
		} //loop ends here.
		return $count;
	}

	function products_alert_stock()
	{
		global $db;
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$count = 0;

		while ($row = $result->fetch_array()) {
			extract($row);

			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' ";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];

			if (($inventory <= $alert_units)) {
				$count += $inventory;
			}
		} //loop ends here.
		return $count;
	}

	function products_out_stock()
	{
		global $db;
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$count = 0;

		while ($row = $result->fetch_array()) {
			extract($row);

			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];

			if ($inventory <= 0) {
				$count++;
			}
		} //loop ends here.
		return $count;
	}
	function products_soon_expired()
	{
		global $db;
		$query = "SELECT * from products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$count = 0;

		while ($row = $result->fetch_array()) {
			extract($row);

			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];

			if ($inventory <= 0) {
				$count++;
			}
		} //loop ends here.
		return $count;
	}

	function damaged_products()
	{
		global $db;
		$query = "SELECT * from returns WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "'";
		$result = $db->query($query) or die($db->error);
		$dmg = 0;

		while ($row = $result->fetch_array()) {
			extract($row);

			$dmg_request = "SELECT SUM(qty_dmg) FROM return_detail WHERE return_id='" . $return_id . "'";
			$dmg_result = $db->query($dmg_request) or die($db->error);
			$dmg_row = $dmg_result->fetch_array();

			$dmg += $dmg_row['SUM(qty_dmg)'];
		} //loop ends here.
		return $dmg;
	}


	function list_product_rates()
	{
		global $db;
		$query = "SELECT * from products WHERE store_id='" . $_SESSION['store_id'] . "' ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);

		$content = '';
		while ($row = $result->fetch_array()) {
			extract($row);

			$query_rate = "SELECT * from product_rates WHERE product_id='" . $product_id . "' AND store_id='" . $_SESSION['store_id'] . "'";
			$result_rate = $db->query($query_rate) or die($db->error);
			$row_rate = $result_rate->fetch_array();

			$content .= '<tr>';
			$content .= '<form method="post" action="">';
			$content .= '<td>';
			$content .= $product_manual_id;
			$content .= '</td><td>';
			$content .= $product_name;
			$content .= '</td><td>';
			$content .= '<input type="text" class="rate" name="default_rate" value="' . $row_rate['default_rate'] . '">';
			$content .= '</td><td>';
			$content .= '<input type="text" class="rate" name="level_1" value="' . $row_rate['level_1'] . '">';
			$content .= '</td><td>';
			$content .= '<input type="text" class="rate" name="level_2" value="' . $row_rate['level_2'] . '">';
			$content .= '</td><td>';
			$content .= '<input type="text" class="rate" name="level_3" value="' . $row_rate['level_3'] . '">';
			$content .= '</td><td>';
			$content .= '<input type="text" class="rate" name="level_4" value="' . $row_rate['level_4'] . '">';
			$content .= '</td><td>';
			$content .= '<input type="text" class="rate" name="level_5" value="' . $row_rate['level_5'] . '">';
			$content .= '</td><td>';
			$content .= '<input type="hidden" name="update_rate" value="' . $row_rate['rate_id'] . '">';
			$content .= '<input type="hidden" name="product_id" value="' . $product_id . '">';
			$content .= '<input type="submit" class="btn btn-default btn-sm" value="Update">';
			$content .= '</td></form></tr>';
		} //while loop products
		echo $content;
	} //list product rates to manage rates of different price levels.

	function delete_product($product_id)
	{
		global $db;
		$query = "SELECT * FROM inventory WHERE product_id='" . $product_id . "'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;

		if ($num_rows > 0) {
			return 'You cannot delete product please delete purchase invoices, sale invoices, sale returns, purchase returns related to this product first.';
		} else {
			$query = "DELETE FROM products WHERE product_id='" . $product_id . "'";
			$result = $db->query($query) or die($db->error);
			return 'Product was deleted successfuly.';
		}
	} //product delete

	function update_client_level($client_id, $price_level)
	{
		global $db;

		$query = 'UPDATE clients SET
			price_level="' . $price_level . '"
			WHERE client_id="' . $client_id . '" AND store_id="' . $_SESSION['store_id'] . '"
		';
		$result = $db->query($query) or die($db->error);
		return 'Price level was updated successfuly!';
	} //update_client level

	function update_product_rates($product_id, $rate_id, $default_rate, $level_1, $level_2, $level_3, $level_4, $level_5)
	{
		global $db;

		$update_rate = "UPDATE product_rates SET
		default_rate='" . $default_rate . "',
		level_1='" . $level_1 . "',
		level_2='" . $level_2 . "',
		level_3='" . $level_3 . "',
		level_4='" . $level_4 . "',
		level_5='" . $level_5 . "'
		WHERE rate_id='" . $rate_id . "'
		";
		$result_rate = $db->query($update_rate) or die($db->error);

		//update price.
		$update_price = "UPDATE price SET
		selling_price='" . $default_rate . "'
		WHERE product_id='" . $product_id . " ORDER by price_id ASC LIMIT 1'
		";
		$result_price = $db->query($update_price) or die($db->error);

		return 'Rate was updated successfuly!';
	}

	function list_client_levels()
	{
		global $db;
		$query = "SELECT * from clients WHERE store_id='" . $_SESSION['store_id'] . "' ORDER by full_name ASC";
		$result = $db->query($query) or die($db->error);

		$content = '';
		while ($row = $result->fetch_array()) {
			extract($row);
			$content .= '<tr>';
			$content .= '<form method="post" action="">';
			$content .= '<td>';
			$content .= $client_id;
			$content .= '</td><td>';
			$content .= $full_name;
			$content .= '</td><td>';
			$content .= $business_title;
			$content .= '</td><td>';
			$content .= $mobile;
			$content .= '</td><td>';
			$content .= $phone;
			$content .= '</td><td>';
			$content .= $email;
			$content .= '</td><td>';
			$content .= '<select name="price_level" class="form-control" style="height:28px;padding-top:3px;padding-bottom:3px;">';
			if ($price_level == 'default_level') :
				$content .= '<option selected="selected" value="default_level">Default</option>';
			else :
				$content .= '<option value="default_level">Default</option>';
			endif;
			if ($price_level == 'level_1') :
				$content .= '<option selected="selected" value="level_1">Level 1</option>';
			else :
				$content .= '<option value="level_1">Level 1</option>';
			endif;
			if ($price_level == 'level_2') :
				$content .= '<option selected="selected" value="level_2">Level 2</option>';
			else :
				$content .= '<option value="level_2">Level 2</option>';
			endif;
			if ($price_level == 'level_3') :
				$content .= '<option selected="selected" value="level_3">Level 3</option>';
			else :
				$content .= '<option value="level_3">Level 3</option>';
			endif;
			if ($price_level == 'level_4') :
				$content .= '<option selected="selected" value="level_4">Level 4</option>';
			endif;
			if ($price_level == 'level_5') :
				$content .= '<option selected="selected" value="level_5">Level 5</option>';
			else :
				$content .= '<option value="level_5">Level 5</option>';
			endif;
			$content .= '</select>';
			$content .= '</td><td>';
			$content .= '<input type="hidden" name="update_client" value="' . $client_id . '">';
			$content .= '<input type="submit" class="btn btn-default btn-sm" value="Update">';
			$content .= '</td></form></tr>';
		} //while loop products
		echo $content;
	} //list product rates to manage rates of different price levels.

	function product_options($product_id)
	{
		global $db;
		$query = 'SELECT * from products WHERE warehouse_id="' . $_SESSION['warehouse_id'] . '" ORDER by product_name ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';
		if ($product_id != '') {
			while ($row = $result->fetch_array()) {
				if ($product_id == $row['product_id']) {
					$options .= '<option selected="selected" value="' . $row['product_id'] . '">' . $row['product_name'] . ' (' . $row['product_manual_id'] . ')</option>';
				} else {
					$options .= '<option value="' . $row['product_id'] . '">' . $row['product_name'] . ' (' . $row['product_manual_id'] . ')</option>';
				}
			}
		} else {
			while ($row = $result->fetch_array()) {
				$options .= '<option value="' . $row['product_id'] . '">' . $row['product_name'] . ' (' . $row['product_manual_id'] . ')</option>';
			}
		}
		echo $options;
	} //product_options ends here.

	function product_names($product_id)
	{
		global $db;
		$query = 'SELECT * from products WHERE warehouse_id="' . $_SESSION['warehouse_id'] . '" ORDER by product_name ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';
		if ($product_id != '') {
			while ($row = $result->fetch_array()) {
				if ($product_id == $row['product_id']) {
					$options .= '<option selected="selected" value="' . $row['product_id'] . '">' . $row['product_name'] . '</option>';
				} else {
					$options .= '<option value="' . $row['product_id'] . '">' . $row['product_name'] . '</option>';
				}
			}
		} else {
			while ($row = $result->fetch_array()) {
				$options .= '<option value="' . $row['product_id'] . '">' . $row['product_name'] . ' </option>';
			}
		}
		echo $options;
	} //product_options ends here.

	function get_product_info($product_id, $term)
	{
		global $db;
		$query = "SELECT * from products WHERE product_id='" . $product_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	} //get user email ends here.

	function get_product_info_through_sku($product_id, $term)
	{
		global $db;
		$query = "SELECT * from products WHERE product_manual_id='" . $product_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	} //get user email ends here.

	function get_product_rate($product_id, $term)
	{
		global $db;
		$query = "SELECT * from product_rates WHERE product_id='" . $product_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	} //get user email ends here.
	function get_product_dimensions($product_id, $term)
	{
		global $db;
		$query = "SELECT * from dimensions WHERE product_id='" . $product_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	} //get user email ends here.
	function check_product_existance($product_id)
	{
		global $db;
		$exist = 0;
		$query = "SELECT * from dimensions WHERE product_id='" . $product_id . "'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			$exist = 1;
		} else {
			$exist = 0;
		}
		return $exist;
	} //get user email ends here.
	function check_price_existance($product_id)
	{
		global $db;
		$exist = 0;
		$query = "SELECT * from price WHERE product_id='" . $product_id . "' AND type='1'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			$exist = 1;
		} else {
			$exist = 0;
		}
		return $exist;
	} //get user email ends here.

	function update_dimensions($product_id, $long, $larg, $haut, $poids)
	{
		global $db;

		$query1 = "UPDATE dimensions SET long_pr='" . $long . "', larg='" . $larg . "', haut='" . $haut . "', poids='" . $poids . "' WHERE product_id='" . $product_id . "'";
		$result1 = $db->query($query1) or die($db->error);
		return 'Les Dimensions sont mises à jour avec succès!';
	} //add_purchase ends here. returns purchase id.


	function list_pos_products($category)
	{
		global $db;

		if (get_option($_SESSION['store_id'] . '_pos_items') == '' || !is_numeric(get_option($_SESSION['store_id'] . '_pos_items'))) {
			$items_to_show = 18;
		} else {
			$items_to_show = get_option($_SESSION['store_id'] . '_pos_items');
		}


		if (isset($_SESSION['category_id']) && $_SESSION['category_id'] == 'all') {
			$num_rows = "SELECT COUNT(*) FROM products WHERE store_id='" . $_SESSION['store_id'] . "'";
		} else {
			$num_rows = "SELECT COUNT(*) FROM products WHERE store_id='" . $_SESSION['store_id'] . "' AND category_id='" . $_SESSION['category_id'] . "'";
		}

		$num_rows_result = $db->query($num_rows) or die($db->error());
		$num_rows_rows = $num_rows_result->fetch_row();

		$last = ceil($num_rows_rows[0] / $items_to_show);

		if ($last < 1) {
			$last = 1;
		}

		$pagenum = 1;
		// Get pagenum from URL vars if it is present, else it is = 1
		if (isset($_SESSION['pn'])) {
			$pagenum = preg_replace('#[^0-9]#', '', $_SESSION['pn']);
		}
		// This makes sure the page number isn't below 1, or more than our $last page
		if ($pagenum < 1) {
			$pagenum = 1;
		} else if ($pagenum > $last) {
			$pagenum = $last;
		}
		// This sets the range of rows to query for the chosen $pagenum
		$limit = 'LIMIT ' . ($pagenum - 1) * $items_to_show . ',' . $items_to_show;

		if (isset($_SESSION['category_id']) && $_SESSION['category_id'] == 'all') {
			$query = "SELECT product_id, product_name, product_image FROM products WHERE store_id='" . $_SESSION['store_id'] . "' ORDER BY product_name ASC $limit";
		} else {
			$query = "SELECT product_id, product_name, product_image FROM products WHERE category_id='" . $_SESSION['category_id'] . "' AND store_id='" . $_SESSION['store_id'] . "' ORDER BY product_name ASC $limit";
		}

		// Establish the $paginationCtrls variable
		$paginationCtrls = '<ul class="pager">';
		// If there is more than 1 page worth of results
		if ($last != 1) {
			if ($pagenum > 1) {
				$previous = $pagenum - 1;
				$paginationCtrls .= '<li><a href="#" id="prevpage" data-page=' . $previous . '">Previous</a></li>';
			}
			if ($pagenum != $last) {
				$next = $pagenum + 1;
				$paginationCtrls .= '<li><a href="#" id="nextpage" data-page=' . $next . '>Next</a></li>';
			}
		}
		$paginationCtrls .= '</ul>';

		$result = $db->query($query) or die($db->error);
		$products = '';
		while ($row = $result->fetch_array()) {
			$products .= '<div class="product">';
			if ($row['product_image'] == '') {
				$product_image = 'images/product_icon.png';
			} else {
				$product_image = $row['product_image'];
			}
			$products .= '<input type="image" src="' . $product_image . '" class="pos_product_id" value="' . $row['product_id'] . '"  />';
			$products .= '<label title="Click on image to add product">' . $row['product_name'] . '</label>';
			$products .= '</div>';
		}
		return $paginationCtrls . $products;
	} //list POs products

	function num_products($warehouse_id)
	{
		global $db;
		$query = "SELECT * FROM products WHERE warehouse_id='" . $_SESSION['warehouse_id'] . "'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		return $num_rows;
	}
	function qty_products($warehouse_id)
	{
		global $db;
		$query = "SELECT * FROM products WHERE warehouse_id='" . $_SESSION['product_id'] . "'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		return $num_rows;
	}

	function product_mvt($product_id)
	{
		global $db;
		$product = new Product;
		$product_name = $product->get_product_info($product_id, 'product_name');
		$query = "SELECT * from inventory WHERE product_id ='" . $product_id . "' AND warehouse_id='" . $_SESSION['warehouse_id'] . "' ORDER by inventory_id DESC";
		$result = $db->query($query) or die($db->error);
		$content = '';
		$count = 0;
		$operation = '';
		$reference = '';
		while ($row = $result->fetch_array()) {
			extract($row);
			$count += 1;
			$content .= '<tr><td>';
			$content .= $count;
			$content .= '</td><td>';
			$content .= $dateinventory;
			$content .= '</td><td>';
			$content .= $product_name;
			$content .= '</td><td>';
			if ($transfer_id != 0) {
				$operation = " Goods Transferred ";
			} elseif ($delivery_id != 0) {
				$operation = " Goods Sold ";
			} elseif ($return_id != 0) {
				$operation = " Goods Returned ";
			} elseif ($order_id != 0) {
				$operation = " Goods Ordered ";
			} else {
				$operation = " Stock Add ";
			}
			$content .= $operation;
			$content .= '</td >';
			if (($transfer_id != 0) or ($delivery_id != 0) or ($order_id != 0)) {
				$content .= "<td style='color:#CC0000' align='right'>";
				$content .= '-' . $out_inv;
				$content .= '</td>';
			} else {
				$content .= "<td style='color:#0d47a1' align='right'>";
				$content .= $inn;
				$content .= '</td>';
			}
			$content .= '<td>';
			if ($transfer_id != 0) {
				$reference = " Transfer N#: " . $transfer_id;
			} elseif ($delivery_id != 0) {
				$reference = " Delivery N#: " . $delivery_id;
			} elseif ($return_id != 0) {
				$reference = " Goods Returned Return N#: " . $return_id;
			} elseif ($order_id != 0) {
				$reference = " Order N#: " . $order_id;
			} else {
				$reference = " Stock Add Lot# " . $lot;
			}
			$content .= $reference;
			$content .= '</td></tr>';
		} //loop ends here.
		$content .= '<tr><td colspan="6" style="color:#CC0000;font-size:14px"><i> History of ' . $product_name . ' :' . $count . ' movements found</i></tr>';
		echo $content;
	}


	function getStockAlertCount()
	{
		global $db;
		$query = "SELECT * from products  ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$content = '';
		$count = 0;
		while ($row = $result->fetch_array()) {
			extract($row);
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "' ";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			if ($inventory < $alert_units) {
				$count++;
			}
		} //loop ends here.
		echo $count;
	}

	function getOutOfStockCount()
	{
		global $db;
		$user = new Users;
		$query = "SELECT * from products ORDER by product_name ASC";
		$result = $db->query($query) or die($db->error);
		$content = '';
		$count = 0;
		while ($row = $result->fetch_array()) {
			extract($row);
			$inventory = "SELECT SUM(inn), SUM(out_inv) FROM inventory WHERE product_id='" . $product_id . "'";
			$inventory_result = $db->query($inventory) or die($db->error);
			$inventory_row = $inventory_result->fetch_array();

			$inventory = $inventory_row['SUM(inn)'] - $inventory_row['SUM(out_inv)'];
			if ($inventory <= '0') {

				$count++;
			}
		} //loop ends here.
		echo $count;
	}
}//class ends here.