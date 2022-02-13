<?php
include('system_load.php');

$important  = new ImportantFunctions();
$product = new Product();
$totalQuantities = 0;
$storeName = null;
if (isset($_GET['assign_id']) && isset($_GET['cart_id'])) {
    $assignId = $_GET['assign_id'];
    $cartId = $_GET['cart_id'];
    $assignResponse = ($important->getDataThroughAssignId($assignId));
    if ($assignResponse == null) {
        return 'Something bad happend, Please try again later';
    }
    $orderId = ($important->getDataThroughAssignId($assignId)['order_id']);
    $cartData = $important->getDataThroughCartAssigning($cartId);
    if ($cartData == null)
        return 'Something really bad happend';

    $response = $important->CallAPI('GET', "v-beta/sales_orders/" . $orderId);
    $storeName = $response->order_source->order_source_nickname;
    $storeID = $response->order_source->order_source_id;
    $skus = array();
    foreach ($response->sales_order_items as $key => $value) {
        $skus[] =  $value->line_item_details->sku;
        $id =   $product->get_product_info_through_sku($value->line_item_details->sku, 'product_id');
        $important->add_inventory(0, $value->quantity, $id);
        $totalQuantities += $value->quantity;
    }





    $printLabelObject = array(
        "label_format" => "pdf",
        "shipment" => array(
            "carrier_id" => $cartData['carrier_id'],
            "service_code" => $cartData['service_code'],
            "ship_from" => array(
                "company_name" => "Shipping Department",
                "name" => "Cody Howell",
                "phone" => "4802720000",
                "address_line1" => "4841 industrial parkway",
                "city_locality" => "Indianapolis",
                "state_province" => "IN",
                'country_code' => 'US',
                'postal_code' => '46226',
                "address_residential_indicator" => "no"
            ),
            "packages" => array(
                [
                    "package_code" => "package",
                    "weight" => array(
                        "value" => intval($cartData['total_weight']),
                        "unit" => "ounce"
                    ),
                ]
            ),
        ),

    );



    $response =  $important->CallAPI('POST', 'v-beta/labels/sales_order/' . $orderId, json_encode($printLabelObject));
    if (count($response->errors) > 0) {
        HEADER('LOCATION: buy_postage.php?message=error');
    }



    $important->storeOwes($assignResponse['order_no'], $storeName, $response->shipment_cost->amount, $totalQuantities);
    $important->storeShippingLabelInfo($response->label_id, $response->shipment_id, $response->ship_date, $response->tracking_number, $response->label_download->pdf, $assignId, $assignResponse['order_no'], $response->shipment_cost->amount, $storeID);

    $URL = $response->label_download->pdf;


    echo "<script type='text/javascript'> let a= document.createElement('a');
        a.href= '{$URL}';
      
        a.click();</script>";




    // echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
    // echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
}
