<?php

require_once('../system_load.php');

global $db;
$note = new Notes();

$query = "SELECT * FROM assign_order WHERE order_no='" . $_GET['order_no'] . "'";

$result = $db->query($query) or die($db->error);
if ($result->num_rows > 0) {
    $query = "UPDATE  assign_order SET status='" . $_GET['status'] . "' WHERE order_no='" . $_GET['order_no'] . "'";
    $result = $db->query($query) or die($db->error);

    $note->add_note('Order Canceled', 'Order# ' . $_GET['order_no'] . ' has been ' . $_GET['status']);
    HEADER('LOCATION: ../assigned_orders_list.php?message=Canceled Successfully');


  
} else {
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => false,
        'message' => 'Some error occured',
    ]);
}
