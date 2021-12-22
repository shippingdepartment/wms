<?php

require_once('../system_load.php');


global $db;
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);
    $myarray = $data->myarray;
    foreach ($myarray as $key => $value) {
        $query = "INSERT into assign_order VALUES(NULL, '" . $value->user_id . "', '" . $value->order_source_id . "','" . $value->order_no . "')";
        $result = $db->query($query) or die($db->error);
    }
}
