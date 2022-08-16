<?php

require_once('../system_load.php');
$important  = new ImportantFunctions();


global $db;
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);

    $query = "UPDATE cart_assigning SET service_code='" . $data->service_code . "', carrier_id='" . $data->carrier_id . "' WHERE assign_order_id='" . $data->assign_order_id . "'";
    $result = $db->query($query) or die($db->error);

    $assignResponse = ($important->getDataThroughAssignId($data->assign_order_id));
    if ($assignResponse == null) {
        return 'Something bad happend, Please try again later';
    }
    $cartData = $important->getDataThroughCartAssigningByAssignId($data->assign_order_id);
    if ($cartData == null)
        return 'Something really bad happend';

    $response = $important->CallAPI('GET', "orders/" . $data->order_id);
    $storeID = $response->advancedOptions->storeId;
    $skus = array();
    foreach ($response->items as $key => $value) {
        $skus[] =  $value->sku;
        $id =   $product->get_product_info_through_sku($value->sku, 'product_id');
        $important->add_inventory(0, $value->quantity, $id);
        $totalQuantities += $value->quantity;
    }
    $date = date('Y-m-d H:i:s');
    $carrierID =  $cartData['carrier_id'];
    $serviceID = $cartData['service_code'];
    $shipDate = $date;
    $totalWeight = $cartData['total_weight'];
    $printLabelObjecst = "{\n  \"orderId\": $data->order_id,\n  \"carrierCode\": \"$carrierID\",\n  \"serviceCode\": \"$serviceID\",\n  \"packageCode\": \"package\",\n  \"confirmation\": \"none\",\n  \"shipDate\": \"$shipDate\",\n  \"weight\": {\n    \"value\": \"$totalWeight\",\n    \"units\": \"ounces\"\n  },\n  \"dimensions\": null,\n  \"insuranceOptions\": null,\n  \"internationalOptions\": null,\n  \"advancedOptions\": null,\n  \"testLabel\":\"false\"\n}";

    $response =  $important->CallAPI('POST', 'orders/createlabelfororder', ($printLabelObjecst));

    if ($response->ExceptionMessage) {
        echo '<div class="alert alert-danger">';
        echo $response->ExceptionMessage;
        echo '</div>';
        return;
    }

    $pdf_decoded = base64_decode($response->labelData);
    $pdf = fopen('./printLabels/'.$response->shipmentId . '.pdf', 'w');
    fwrite($pdf, $pdf_decoded);

    $file = $response->shipmentId . '.pdf';

    $pathname = $file;
    $important->storeShippingLabelInfo($response->label_id, $response->shipmentId, $response->shipDate, $response->trackingNumber, $pathname, $assignId, $assignResponse['order_no'], $response->shipmentCost, $storeID);
}
