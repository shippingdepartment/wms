<?php

class ImportantFunctions
{
    public $base_url = 'http://api.shipengine.com/';

    function CallAPI($method, $url, $data = false)
    {

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

        $query = "SELECT * from assign_order ORDER by id DESC";
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
            $content .= '</td>';
            $content .= '<td>';
            $content .= '<a href="shipengine/order_details.php?id=' . $order_id . '" target="_self"><i class="fa fa-eye" style="font-size:16px"></i></a>';
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
        $lastOrderAssignedUserId = ($lastAssignOrderResult->fetch_array())[0];

        do {
            $randomIdQuery = 'SELECT user_id from users ORDER BY RAND() LIMIT 1';
            $result = $db->query($randomIdQuery) or die($db->error);

            $currentUser = ($result->fetch_array())[0];
        } while ($currentUser == $lastOrderAssignedUserId);

        $query = "INSERT into assign_order VALUES(NULL, '" . $currentUser . "', '" . $orderId . "','" . $orderNo . "')";
        $result = $db->query($query) or die($db->error);
    }
}
