<?php

require_once('../system_load.php');

global $db;

$warehouse = new Warehouse();
$important = new ImportantFunctions();
$note = new Notes();
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);
    $query = "UPDATE send_shipping SET is_approve=1 WHERE id='" . $data->id . "'";
    $result = $db->query($query) or die($db->error);

    $query = "SELECT * FROM send_shipping WHERE id='" . $data->id . "'  LIMIT 1";
    $result = $db->query($query) or die($db->error);
    ($result = $result->fetch_array());

    extract($result);
    $productId = $important->getProductId($product_id);
    if ($productId == null) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found',
        ]);
    }
    $important->add_inventory($quantity, 0, $productId);

    $note->add_note('Shipment Approved', 'id# ' . $data->id . ' has been approved');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Shipment Request has been approved successfully',
    ]);
}
