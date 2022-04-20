<?php
include('system_load.php');

$important  = new ImportantFunctions();
$product = new Product();
$totalWeight = 0.0;
$storeName = null;
if (isset($_GET['order_no'])) {
    $orderId = $_GET['order_no']; // order id
    $orderNo;
    if ($orderId == null)
        return 'Something really bad happend';
    $response = $important->CallAPI('GET', "v-beta/sales_orders/" . $orderId);
    $orderNo = $response->external_order_number;
    foreach ($response->sales_order_items as $key => $item) {
        $totalWeight += $item->line_item_details->weight->value;
    }

    $printLabelObject = array(
        "is_return_label" => true,
        "shipment" => array(
            "service_code" => 'usps_priority_mail',
            "ship_from" => $response->ship_to,
            "ship_to" => array(
                "company_name" => "Shipping Department",
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
                        "value" => $totalWeight,
                        "unit" => "ounce"
                    ),
                ]
            ),
        ),

    );



    $response =  $important->CallAPI('POST', 'v1/labels/', json_encode($printLabelObject));
    if (isset($response->errors) && count($response->errors) > 0) {
        HEADER('LOCATION: buy_postage.php?message=error');
    }

    $isSaved = $important->storeCustomerReturnLabel($orderNo, $response->label_id, $response->tracking_status, $response->label_download->pdf);

    $URL = $response->label_download->pdf;


    echo "<script type='text/javascript'> let a= document.createElement('a');
        a.href= '{$URL}';
        a.click();</script>";

}
