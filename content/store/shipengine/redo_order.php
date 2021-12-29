<?php

require_once('../system_load.php');

global $db;
$note = new Notes();
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);
    $query = "SELECT * FROM assign_order WHERE order_no='" . $data->order_no . "'";

    $result = $db->query($query) or die($db->error);
    if ($result->num_rows > 0) {
        $query = "UPDATE  assign_order SET status='" . $data->status . "' WHERE order_no='" . $data->order_no . "'";
        $result = $db->query($query) or die($db->error);

        $note->add_note('Order Canceled', 'Order# ' . $data->order_no . ' has been ' . $data->status);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'Order Cancel Successfully',
        ]);
    } else {
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'message' => 'Some error occured',
        ]);
    }
}
