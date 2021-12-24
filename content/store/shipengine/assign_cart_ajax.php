<?php

require_once('../system_load.php');


global $db;
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);




    //IF INVENTORY AVAILABLE
    $query = "INSERT into cart_assigning VALUES(NULL, '" . $data->assign_order_id . "', '" . date('Y/m/d H:i:s') . "','" . $data->cart . "', '" . $_SESSION['user_id'] . "', '" . $data->service_code . "', '" . $data->carrier_id . "', '" . $data->total_weight . "')";
    $result = $db->query($query) or die($db->error);
    $query = "UPDATE  assign_order SET status='picked' WHERE ID='" . $data->assign_order_id . "'";
    $result = $db->query($query) or die($db->error);
}
