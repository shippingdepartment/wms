<?php

require_once('../system_load.php');

global $db;

$warehouse = new Warehouse();
$important = new ImportantFunctions();
$note = new Notes();
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);
    $query = "UPDATE send_shipping SET is_approve=1 WHERE carton_id='" . $data->id . "'";
    $result = $db->query($query) or die($db->error);

    $shipments =  $important->getShipmentsFromCartonId($data->id);
    while ($row = $shipments->fetch_array()) {
        extract($row);
        $productId = $important->getProductId($product_id);
        $important->add_inventory($quantity, 0, $productId);
    }
    // extract($result);


    $note->add_note('Shipment Approved', 'id# ' . $data->id . ' has been approved');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Shipment Request has been approved successfully & Inventory Added',
    ]);
}
