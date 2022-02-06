<?php

class ImportantFunctions
{
    public $base_url = 'http://api.shipengine.com/';

    function CallAPI($method, $url, $data = false, $changeURL = false)
    {
        if ($changeURL)
            $this->base_url = '';
        // echo '<pre>';

        // print_r($data);
        // echo '</pre>';

        // return;

        $apiKey = 'YCMccKJkFczSrSWMb21zY2lJCugPtJNlgwO+XTDX9Jk';
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                // if ($data)
                // $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'API-Key: ' . $apiKey,
            'Content-Type: application/json',
        ));
        // curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $this->base_url . $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result);
    }

    public function getBagSize($size)
    {
        if ($size <= 0.02) {
            return '5 x 7';
        } else if ($size > 0.02 && $size <= 0.037) {
            return '6 x 9';
        } else if ($size > 0.037 && $size <= 0.074) {
            return '10 x 13';
        } else if ($size > 0.074 && $size <= 0.1875) {
            return '14.5 x 19';
        } else if ($size > 0.1875 && $size <= 0.33) {
            return '19 x 24';
        } else if ($size > 0.33 && $size <= 0.6944) {
            return '24 x 30';
        } else
            return 'X';
    }

    public function getBoxSize($size)
    {
        if ($size <= 0.02) {
            return 'X';
        } else if ($size > 0.02 && $size <= 0.037) {
            return '4 x 4 x 4';
        } else if ($size > 0.037 && $size <= 0.074) {
            return '8 x 4 x 4';
        } else if ($size > 0.074 && $size <= 0.1875) {
            return '9 x 6 x 6';
        } else if ($size > 0.1875 && $size <= 0.33) {
            return '12 x 8 x 6';
        } else if ($size > 0.33 && $size <= 0.6944) {
            return '12 x 10 x 10';
        } else if ($size > 0.6944 && $size <= 1)
            return '12 x 12 x 12';
        else {
            return '13 x 13 x 13';
        }
    }

    public function getAllUserAssignedOrders()
    {
        global $db;
        $query = "SELECT * from assign_order WHERE status='inprogress' OR  status='reship' OR  status='canceled' ORDER by id DESC";
        $result = $db->query($query) or die($db->error);
        $content = '';
        $user = new Users();
        while ($row = $result->fetch_array()) {
            extract($row);
            $userName = $user->get_user_info($user_id, 'first_name');
            $userName .= ' ' . $user->get_user_info($user_id, 'last_name');
            $content .= '<tr class="">';
            $content .= '<td>';
            $content .= $order_no;
            $content .= '</td><td>';
            $content .= $userName;
            $content .= '</td><td>';
            $content .= $status;
            $content .= '</td>';
            $content .= '<td>';
            $content .= '<a href="shipengine/order_details.php?id=' . $order_id . '&assign_id=' . $id . '" target="_self"><i class="fa fa-eye" style="font-size:16px"></i></a>';
            $content .= '</td>';
            $content .= '</tr>';
        }
        echo $content;
    }

    public function checkOrderIsAssigned($order_id)
    {
        global $db;
        $order_detail = "SELECT * from assign_order WHERE order_id='" . $order_id . "'";
        $result = $db->query($order_detail) or die($db->error);
        $num_rows = $result->num_rows;
        if ($num_rows > 0)
            return true;
        else
            return false;
    }

    public function assignOrdersTORandom($orderNo, $orderId, $storeId)
    {
        global $db;
        $lastAssignOrder = 'SELECT user_id from assign_order ORDER BY id DESC LIMIT 1 ';
        $lastAssignOrderResult = $db->query($lastAssignOrder) or die($db->error);
        if ($lastAssignOrderResult->num_rows > 0) {
            $lastOrderAssignedUserId = intval(($lastAssignOrderResult->fetch_array())[0]);
        } else {
            $lastOrderAssignedUserId = 1;
        }
        $randomIdQuery = 'SELECT user_id from users WHERE user_type <> "store_owner"  ORDER BY RAND() LIMIT 1';
        $result = $db->query($randomIdQuery) or die($db->error);
        $currentUser = ($result->fetch_array())[0];

        while (intval($currentUser) == $lastOrderAssignedUserId) {
            $randomIdQuery = 'SELECT user_id  from users WHERE user_type <> "store_owner" ORDER BY RAND() LIMIT 1';
            $result = $db->query($randomIdQuery) or die($db->error);
            $currentUser = ($result->fetch_array())[0];
        }
        $now =  date("d-m-Y - H:i:s");


        $query = "INSERT into assign_order VALUES(NULL, '" . $currentUser . "', '" . $orderId . "', '" . $orderNo . "', 'inprogress','" . $storeId . "', '".$now."', '".$now."')";

        $result = $db->query($query) or die($db->error);
    }

    public function getLastOrderId()
    {
        global $db;
        $lastAssignOrder = 'SELECT order_no from assign_order ORDER BY id DESC LIMIT 1 ';
        $lastAssignOrderResult = $db->query($lastAssignOrder) or die($db->error);
        if ($lastAssignOrderResult->num_rows > 0)
            return ($lastAssignOrderResult->fetch_array())[0];
        return 0;
    }

    public function checkIfOrderExists($orderNo, $orderId)
    {
        global $db;
        $query = "SELECT * from assign_order WHERE order_id='" . $orderId . "' AND order_no='" . $orderNo . "' ";
        // $lastAssignOrder = 'SELECT * from assign_order WHERE order_id ORDER BY id DESC LIMIT 1 ';
        $lastAssignOrderResult = $db->query($query) or die($db->error);
        if ($lastAssignOrderResult->num_rows > 0)
            return true;
        return false;;
    }

    public function getCurrentUserAssignedOrders()
    {
        global $db;
        $query = "SELECT * from assign_order WHERE user_id='" . $_SESSION['user_id'] . "' AND status='inprogress' OR  status='reship' OR  status='canceled' ORDER by id DESC";
        $result = $db->query($query) or die($db->error);
        $content = '';
        $user = new Users();
        while ($row = $result->fetch_array()) {
            extract($row);
            $userName = $user->get_user_info($user_id, 'first_name');
            $userName .= ' ' . $user->get_user_info($user_id, 'last_name');
            $content .= '<tr class="">';
            $content .= '<td>';
            $content .= $order_no;
            $content .= '</td><td>';
            $content .= $userName;
            $content .= '</td><td>';
            $content .= $status;
            $content .= '</td>';
            $content .= '<td>';
            $content .= '<a href="shipengine/order_details.php?id=' . $order_id . '&assign_id=' . $id . '" target="_self"><i class="fa fa-eye" style="font-size:16px"></i></a>';
            $content .= '</td>';
            $content .= '</tr>';
        }
        echo $content;
    }


    public function getCurrentUserAssignedOrdersWithCart()
    {
        global $db;
        $query = "SELECT * from cart_assigning WHERE user_id='" . $_SESSION['user_id'] . "'  ORDER by cart ";
        $result = $db->query($query) or die($db->error);
        $content = '';
        $user = new Users();
        while ($row = $result->fetch_array()) {
            extract($row);
            $query = "SELECT * from assign_order WHERE id='" . $assign_order_id . "'  ";
            $res = $db->query($query) or die($db->error);
            $data = ($res->fetch_array());


            $userName = $user->get_user_info($user_id, 'first_name');
            $userName .= ' ' . $user->get_user_info($user_id, 'last_name');
            $content .= '<tr class="">';
            $content .= '<td>';
            $content .= $data['order_no'];
            $content .= '</td><td>';
            $content .= $userName;

            $content .= '</td><td>';
            $content .= $cart;
            $content .= '</td>';
            $content .= '<td>';
            $content .= '<a href="buy_postage_work.php?assign_id=' . $assign_order_id . '&cart_id=' . $id . '"><i class="fa fa-tag" style="font-size:16px"></i></a> / <a href=packing_slip.php?order_id=' . $data['order_id'] . '  onclick="window.open(this.href).print(); return false"><i class="fa fa-print" style="font-size:16px"></i></a>';
            $content .= '</td>';
            $content .= '</tr>';
        }
        echo $content;
    }

    public function getDataThroughAssignId($id)
    {
        global $db;
        $query = "SELECT * from assign_order WHERE id='" . $id . "'  LIMIT 1";
        $result = $db->query($query) or die($db->error);
        return $result->fetch_array();
    }

    public function getDataThroughCartAssigning($id)
    {
        global $db;
        $query = "SELECT * from cart_assigning WHERE id='" . $id . "'  LIMIT 1";
        $result = $db->query($query) or die($db->error);
        return $result->fetch_array();
    }

    public function storeShippingLabelInfo($label_id, $shipping_id, $ship_date, $tracking_number, $pdf, $assingId, $order_no, $shipmentCost, $store_id)
    {
        global $db;
        $query = "INSERT into shipping_labels VALUES(NULL, '" . $label_id . "', '" . $shipping_id . "', '" . $ship_date . "' , '" . $tracking_number . "', '" . $pdf . "', '" . $_SESSION['user_id'] . "', '" . $order_no . "', '" . $shipmentCost . "', '" . $store_id . "' )";
        $result = $db->query($query) or die($db->error);
        $query = "UPDATE  assign_order SET status='shipped' , updated_at=now() WHERE ID='" . $assingId . "'";
        $result = $db->query($query) or die($db->error);
        $query = "DELETE FROM  cart_assigning  WHERE assign_order_id='" . $assingId . "'";
        $result = $db->query($query) or die($db->error);
    }

    public function getFreeCarts()
    {

        $totalCarts = array();
        global $db;
        $query = "SELECT * FROM carts WHERE active = 1";
        $result = $db->query($query) or die($db->error);

        while ($row = $result->fetch_array()) {
            extract($row);
            $totalCarts[] = $name;
        }

        $query = "SELECT * from cart_assigning ";
        $result = $db->query($query) or die($db->error);

        while ($row = $result->fetch_array()) {
            extract($row);

            $present  = array_search($cart, $totalCarts);
            if ($present !== false) {
                unset($totalCarts[$present]);
            }
        }


        return $totalCarts;
    }


    public function getUserFinishedOrderData()
    {
        global $db;
        $query = "SELECT * from shipping_labels WHERE user_id='" . $_SESSION['user_id'] . "'  ORDER by id ";
        $result = $db->query($query) or die($db->error);
        $content = '';
        $user = new Users();
        $packingUrl = '#';
        while ($row = $result->fetch_array()) {
            extract($row);
            $orderNoQuery = "SELECT * FROM assign_order WHERE user_id='" . $_SESSION['user_id'] . "' AND order_no='" . $order_no . "' LIMIT 1";
            $orderNoQueryResult = $db->query($orderNoQuery) or die($db->error);
            if ($orderNoQueryResult->num_rows > 0) {
                $data = ($orderNoQueryResult->fetch_array());
                $packingUrl = 'packing_slip.php?order_id=' . $data['order_id'];
            }

            $content .= '<tr class="">';
            $content .= '<td>';
            $content .= $order_no;
            $content .= '</td><td>';
            $content .= $label_id;

            $content .= '</td><td>';
            $content .= $shipment_id;
            $content .= '</td>';
            $content .= '<td>';
            $content .= $tracking_number;
            $content .= '</td>';
            $content .= '<td>';
            $content .= '<a href=' . $pdf . ' download target="_blank"><i class="fa fa-tag" style="font-size:16px"></i></a> / <a href=' . $packingUrl . '  onclick="window.open(this.href).print(); return false"><i class="fa fa-print" style="font-size:16px"></i></a>';
            $content .= '</td>';
            $content .= '</tr>';
        }
        echo $content;
    }

    function add_inventory($inn, $out_inv, $product_id)
    {
        global $db;
        //$datetime = strtotime(date());
        $datetime = date('Y-m-d');
        $query = "INSERT into inventory(inventory_id, dateinventory, inn, out_inv, product_id, warehouse_id) VALUES(NULL, '" . $datetime . "', '" . $inn . "', '" . $out_inv . "', '" . $product_id . "', '" . $_SESSION['warehouse_id'] . "')";
        $result = $db->query($query) or die($db->error);
        return $db->insert_id;
    }


    //for store owners

    function getOrderSourceIdForCurrentOwner()
    {
        global $db;
        $query = "SELECT * from store WHERE user_id='" . $_SESSION['user_id'] . "' LIMIT 1";
        $result = $db->query($query) or die($db->error);
        return $result->fetch_array()['store_source_id'];
    }

    function getTotalOrdersCount()
    {
        $response = $this->CallAPI('GET', 'v-beta/sales_orders?order_source_id=' . $_SESSION['order_source_id']);
        return $response->total;
        // echo "<pre>";
        // print_r($response->total);
        // echo "</pre>";
        // exit;
    }

    public function getOrderStatus($orderNo, $orderId)
    {
        global $db;
        $query = "SELECT * from assign_order WHERE order_id='" . $orderId . "' AND order_no='" . $orderNo . "' LIMIT 1";
        $result = $db->query($query) or die($db->error);
        if ($result->num_rows > 0)
            return $result->fetch_array()['status'];
        else
            return null;
    }

    function getOrderTrackingStatus($orderNo)
    {
        global $db;
        $query = "SELECT * from shipping_labels WHERE order_no='" . $orderNo . "' LIMIT 1";
        $result = $db->query($query) or die($db->error);
        if ($result->num_rows > 0) {
            return $result->fetch_array();
        }
        return null;
    }

    //getting orderDATA FROM SHIPENGINE THROUGH ORDER_ID
    public function getOrderDataThroughOrderIDShipengin($orderId)
    {
        if (isset($orderId) && $orderId != '') {
            $response = $this->CallAPI('GET', 'v-beta/sales_orders?external_order_id' . $orderId);
            return $response;
        }
        return null;
    }

    public function getOrderForShipengine($orderNo)
    {
        global $db;
        $query = "SELECT * from assign_order WHERE order_no='" . $orderNo . "' LIMIT 1";
        $result = $db->query($query) or die($db->error);
        if ($result->num_rows > 0) {
            return $result->fetch_array();
        }
        return null;
    }


    public function updateOrderShipengine($orderId, $object)
    {
        $response =  $this->CallAPI('PUT', 'v-beta/sales_orders/' . $orderId, $object);
        return $response;
    }

    public function getProductsByStoreId()
    {
        global $db;
        $content = '';
        $query = "SELECT * from products WHERE store_id='" . $_SESSION['order_source_id'] . "' ORDER by product_name ASC";
        $result = $db->query($query) or die($db->error);
        $content .= '<select name="product_sku" class="form-control" >';
        while ($row = $result->fetch_array()) {
            $content .= '<option selected="selected" value=' . $row['product_manual_id'] . '>' . $row['product_manual_id'] . '</option>';
        }
        $content .= '</select>';

        echo $content;
        // echo "<pre>";
        // print_r($result->fetch_array());
        // echo "<pre";
    }

    public function getStoreByItsId($store_id)
    {
        global $db;
        $query = "SELECT * from store WHERE store_source_id='" . $store_id . "' LIMIT 1";
        $result = $db->query($query) or die($db->error);

        return $result->fetch_array();
    }

    public function getStoreListPrices()
    {
        global $db;
        $user = new Users();
        $query = "SELECT * from store_price ";
        $content = '';
        $result = $db->query($query) or die($db->error);


        if ($result->num_rows > 0) {
            $count = 0;
            while ($row = $result->fetch_array()) {
                extract($row);
                $count++;
                $storeData = $this->getStoreByItsId($row['store_source_id']);
                $userDATA =  $user->get_user_info($storeData['user_id'], 'first_name') . ' ' .  $user->get_user_info($storeData['user_id'], 'last_name');

                $content .= '<tr>';
                $content .= '<td>';
                $content .= $count;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $userDATA;
                $content .= '</td><td>';
                $content .= '$' . $row['first_item_price'];
                $content .= '</td><td>';
                $content .= '$' . $row['each_item_price'];
                $content .= '</td>';
                $content .= '<td>';
                $content .= '<a href="store_owner_price_list_edit.php?id=' . $id . '"><i class="fa fa-edit" style="font-size:16px"></i></a> ';

                $content .= '</td>';
            }
            $content .= '</tr>';

            echo $content;
        } else {
            echo 'Hello world';
        }
    }

    function getStorePrices($id)
    {
        global $db;
        $query = "SELECT * from store_price where id='" . $id . "' LIMIT 1";
        $result = $db->query($query) or die($db->error);
        if ($result) {
            return $result->fetch_array();
        }
        return false;
    }

    function getStorePricesFromSourceId($store_source_id)
    {
        global $db;
        $query = "SELECT * from store_price where store_source_id='" . $store_source_id . "' LIMIT 1";
        $result = $db->query($query) or die($db->error);
        if ($result) {
            return $result->fetch_array();
        }
        return null;
    }

    function editStorePrice($firstItemPrice, $eachItemPrice, $id)
    {
        global $db;

        $query = "UPDATE store_price SET first_item_price='" . $firstItemPrice . "' , each_item_price='" . $eachItemPrice . "' WHERE id='" . $id . "'";
        $result = $db->query($query) or die($db->error);


        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    function orderPaidData($orderNo)
    {
        global $db;

        $query = "SELECT * FROM payments WHERE order_no='" . $orderNo . "' LIMIT 1";
        $result = $db->query($query) or die($db->error);

        if ($result->num_rows > 0)
            return $result->fetch_array();
        else
            return false;
    }

    function getTransactionHistoryForStore()
    {
        global $db;
        $query = "SELECT * from payments where store_source_id='" . $_SESSION['order_source_id'] . "'";
        $content = '';
        $result = $db->query($query) or die($db->error);


        if ($result->num_rows > 0) {
            $count = 0;
            while ($row = $result->fetch_array()) {
                extract($row);
                $count++;
                $storeData = $this->getStoreByItsId($row['store_source_id']);

                $content .= '<tr>';
                $content .= '<td>';
                $content .= $count;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $order_no;
                $content .= '</td><td>';
                $content .= '$' . $amount_paid;
                $content .= '</td><td>';
                $content .= $txt_id;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $paid_by;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $created_at;
                $content .= '</td>';
            }
            $content .= '</tr>';

            echo $content;
        } else {
            // echo 'Hello world';
        }
    }

    function getTransactionHistoryForAdmin()
    {
        global $db;
        $query = "SELECT * from payments ";
        $content = '';
        $result = $db->query($query) or die($db->error);


        if ($result->num_rows > 0) {
            $count = 0;
            while ($row = $result->fetch_array()) {
                extract($row);
                $count++;
                $storeData = $this->getStoreByItsId($row['store_source_id']);

                $content .= '<tr>';
                $content .= '<td>';
                $content .= $count;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $order_no;
                $content .= '</td><td>';
                $content .= '$' . $amount_paid;
                $content .= '</td><td>';
                $content .= $txt_id;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $paid_by;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $created_at;
                $content .= '</td>';
            }
            $content .= '</tr>';

            echo $content;
        } else {
            echo 'Hello world';
        }
    }

    function storeOwes($orderNo, $storeName, $shippingCost, $quantities)
    {
        global $db;
        $datetime = date('Y-m-d');

        $query = "INSERT into store_owner_owes VALUES(NULL, '" . $orderNo . "', '" . $storeName . "', '" . $shippingCost . "', '" . $quantities . "', '" . $datetime . "')";

        $result = $db->query($query) or die($db->error);
    }

    function getStoreOwes()
    {
        global $db;
        $content = '';
        $query = 'SELECT * FROM store_owner_owes';
        $result = $db->query($query) or die($db->error);

        if ($result->num_rows > 0) {
            $count = 0;
            while ($row = $result->fetch_array()) {
                extract($row);
                $count++;

                $content .= '<tr>';
                $content .= '<td>';
                $content .= $count;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $store_name;
                $content .= '</td><td>';
                $content .=  $order_no;
                $content .= '</td><td>';
                $content .= $shipping_cost;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $quantities;
                $content .= '</td>';
                $content .= '<td>';
                $content .= $created_at;
                $content .= '</td>';
            }
            $content .= '</tr>';

            echo $content;
        }
    }

    function getLabelsForStores($store_id)
    {
        global $db;
        $query = "SELECT * from shipping_labels WHERE store_id='" . $store_id . "' AND is_void=0  ORDER by id ";
        $result = $db->query($query) or die($db->error);
        $content = '';
        while ($row = $result->fetch_array()) {
            extract($row);
            $content .= '<tr class="">';
            $content .= '<td>';
            $content .= $order_no;
            $content .= '</td><td>';
            $content .= $label_id;

            $content .= '</td><td>';
            $content .= $tracking_number;
            $content .= '</td>';
            $content .= '<td>';
            $content .= $shipment_id;
            $content .= '</td>';
            $content .= '<td>';
            $content .= '<button id="voidLabelBtn" value=' . $label_id . ' class="btn btn-danger" onclick="voidLabel(this.value)" >Void Label</button>';
            $content .= '</td>';
            $content .= '</tr>';
        }
        return $content;
    }

    public function getStoreShippingAlerts($store_id)
    {
        global $db;
        $query = "SELECT * from assign_order WHERE store_id='" . $store_id . "' AND status='Fulfilled'  ORDER by id ";
        $orderList = array();
        $result = $db->query($query) or die($db->error);

        while ($row = $result->fetch_array()) {
            extract($row);
            $now = date("Y-m-d H:i:s");
            $now = new DateTime($now);
            $date2 = new DateTime($updated_at);
            $diff = $date2->diff($now);
            $hours = $diff->h;
            $hours = $hours + ($diff->days * 24);

            if ($hours >= 36) {
                $arr = array("order_no" => $order_no, "updated_at" => $updated_at, "status" => $status);
                $orderList[] = $row;
            }
        }


        return $orderList;
    }


    function checkProductExist($sku)
    {
        global $db;
        $query = "SELECT * from products WHERE product_manual_id='" . $sku . "'  ";
        $lastAssignOrderResult = $db->query($query) or die($db->error);
        if ($lastAssignOrderResult->num_rows > 0)
            return true;
        return false;;
    }
    function sendInventoryFromStore($sku, $tracking, $quantity)
    {
        //Transfer save krwana hai..
        global $db;
        $productExists = $this->checkProductExist($sku);
        if ($productExists) {
            $now = date("Y-m-d H:i:s");
            $query = "INSERT into send_inventory VALUES(NULL, '" . $_SESSION['order_source_id'] . "','" . $sku . "', '" . $quantity . "', '" . $tracking . "', '" . $now . "',0)";
            $result = $db->query($query) or die($db->error);
            return 'Inventory send successfully';
        } else {
            return 'Product Not Found';
        }
    }
}
