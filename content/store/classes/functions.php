<?php

class ImportantFunctions
{
    public $base_url = 'http://api.shipengine.com/';

    function CallAPI($method, $url, $data = false)
    {
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
        $query = "SELECT * from assign_order WHERE status='new' ORDER by id DESC";
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

    public function assignOrdersTORandom($orderNo, $orderId)
    {
        global $db;
        $lastAssignOrder = 'SELECT user_id from assign_order ORDER BY id DESC LIMIT 1 ';
        $lastAssignOrderResult = $db->query($lastAssignOrder) or die($db->error);
        if ($lastAssignOrderResult->num_rows > 0) {
            $lastOrderAssignedUserId = intval(($lastAssignOrderResult->fetch_array())[0]);
        } else {
            $lastOrderAssignedUserId = 1;
        }
        $randomIdQuery = 'SELECT user_id from users ORDER BY RAND() LIMIT 1';
        $result = $db->query($randomIdQuery) or die($db->error);
        $currentUser = ($result->fetch_array())[0];

        while (intval($currentUser) == $lastOrderAssignedUserId) {
            $randomIdQuery = 'SELECT user_id from users ORDER BY RAND() LIMIT 1';
            $result = $db->query($randomIdQuery) or die($db->error);
            $currentUser = ($result->fetch_array())[0];
        }



        $query = "INSERT into assign_order VALUES(NULL, '" . $currentUser . "', '" . $orderId . "', '" . $orderNo . "', 'new')";

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
        $query = "SELECT * from assign_order WHERE user_id='" . $_SESSION['user_id'] . "' AND status='new' ORDER by id DESC";
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

    // public function getAssignOrderData($id)
    // {
    //     global $db;
    //     $query = "SELECT * from assign_order WHERE id='" . $id . "'  ";
    //     $result = $db->query($query) or die($db->error);
    //     return 

    // }

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
            $content .= '<a href="buy_postage_work.php?assign_id=' . $assign_order_id . '&cart_id=' . $id . '"><i class="fa fa-print" style="font-size:16px"></i></a> ';
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

    public function storeShippingLabelInfo($label_id, $shipping_id, $ship_date, $tracking_number, $pdf, $assingId, $order_no)
    {
        global $db;
        $query = "INSERT into shipping_labels VALUES(NULL, '" . $label_id . "', '" . $shipping_id . "', '" . $ship_date . "' , '" . $tracking_number . "', '" . $pdf . "', '" . $_SESSION['user_id'] . "', '" . $order_no . "')";
        $result = $db->query($query) or die($db->error);
        $query = "UPDATE  assign_order SET status='printed' WHERE ID='" . $assingId . "'";
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
            if ($orderNoQueryResult->num_rows>0) {
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

    function add_inventory($inn=0, $out_inv, $product_id) {
		global $db;
		//$datetime = strtotime(date());
		$datetime = date('Y-m-d');
		$query = "INSERT into inventory(inventory_id, dateinventory, inn, out_inv, product_id, warehouse_id) VALUES(NULL, '".$datetime."', '".$inn."', '".$out_inv."', '".$product_id."', '".$_SESSION['warehouse_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;	
	}
}
