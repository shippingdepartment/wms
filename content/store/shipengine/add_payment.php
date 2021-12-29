<?php

require_once('../system_load.php');


global $db;
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);
    global $db;
    $note = new Notes();
    $query = "INSERT into payments VALUES(NULL, '" . $data->order_no . "', '" . $data->amount_paid . "', '" . $data->txt_id . "', '" . $data->paid_by . "','" . date("Y-m-d H:i:s") . "', '" . $_SESSION['order_source_id'] . "')";
    $result = $db->query($query) or die($db->error);
    $note->add_note('Payment Received', 'You received a payment of amount $' . $data->amount_paid . ' from order# ' . $data->order_no);

    return true;
}
