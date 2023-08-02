<?php

include('system_load.php');
//This loads system.
//user Authentication.
authenticate_user('subscriber');
$important = new ImportantFunctions();

$assignID = $_GET['assign_id'];
$cardId = $_GET['cart_id'];
$user_id = $_SESSION['user_id'];
$orderId = ($important->getOrderDataThroughAssignId($assignID))['order_id']; // store id;



// Check if the store ID is provided in the URL
if(isset($_GET['id'])) {
    $store_id = $_GET['id'];
    $_SESSION['store_id'] = $store_id; // Save the store ID in the session
} else {
    // Retrieve the store ID from the session
    if(isset($_SESSION['store_id'])) {
        $store_id = $_SESSION['store_id'];
    } else {
        // Handle the case when the store ID is not available
        // You can set a default value or redirect the user to a different page
    }
}

// echo "<pre>";
// print_r(($orderId['order_id']));
// echo "</pre>";
// exit;


$function_id = $user->get_user_info($user_id, "user_function");

$product = new Product();
$estimatedShippingCost = 0.0;
$shippingService = null;
$orderStatus = null;
$response = $important->CallAPI('GET', "orders/" . $orderId);
$shippingCarriers = $important->CallAPI('GET', 'carriers');

foreach ($shippingCarriers as $key => $carrier) {
    if ($carrier->code == 'ups_walleted' || $carrier->code == 'fedex' || $carrier->code == 'stamps_com') {
    } else {
        unset($shippingCarriers[$key]);
    }
}

// echo "<pre>";
// print_r(($shippingCarriers));
// echo "</pre>";
// exit;


$content = '';
$totalWeight = 0;
$totalSize = 0;
$totalItems = 0;

$details = '';
$isMediaMail = false;

foreach ($response->items as $key => $value) {
    $productname = $value->name;

    $product->moid_set_product_through_sku($value->sku);
    $details .= '<div class="row">';
    $details .= '<div class="col">';
    $details .= '<div class="card card-2">';
    $details .= '<div class="card-body">';
    $details .= '<div class="media">';
    $details .= '<div class="sq align-self-center "> </div>';
    $details .= '<div class="media-body my-auto text-right">';
    $details .= '<div class="row my-auto flex-column flex-md-row">';
    $details .= '<div class="col my-auto">';
    $details .= '<div class="col my-auto"> <small>SKU: ' . $value->sku . ' </small></div>';
    $details .= '</div>';
    $details .= '<div class="col-auto my-auto"> <small>Quantity: ' . $value->quantity . ' </small></div>';
    $details .= '<div class="col my-auto">' . $value->name .  '  (' . $value->lineItemKey . ')</div>';
    $details .= '<div class="col my-auto"> <small>Weight: ' . $product->pounds . 'lb ' . ($product->ounces * $value->quantity) . 'oz <br>Size: ' . $product->long_pr . 'l ' . $product->larg . 'w ' . ($product->haut * $value->quantity) . 'h' . ' </small></div>';
    $details .= ' <div class="col my-auto"> <small class="mb-0">' . $response->requestedShippingService  . '</small> </div>';
    $details .= ' <div class="col my-auto"> <h6 class="mb-0">' . floatVal($value->unitPrice) . ' '  . '</h6> </div>';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';

    $details .= '<hr class="my-3 ">';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';

    $totalWeight += ((($product->pounds) * 16) * $value->quantity) + ($product->ounces * $value->quantity);
    $tempSize = ($product->long_pr * $product->larg * ($product->haut * $value->quantity)) / 1728;
    $totalSize += $tempSize;

    $totalItems++;
    $isMediaMail = $product->isMediaMail;
}

$orderStatus = $important->getOrderStatus($response->orderId, $response->advancedOptions->storeId);
$carrierCode = '';
$carrierId = '';
// return;

// If the order is less than 16 oz then we can default to usps first class
// setting the postObject for shipping rates
$shippingObject = array(
    'carrierCode' => "stamps_com",
    'fromPostalCode' => '46226',
    'toCountry' => $response->shipTo->country,
    'toPostalCode' => $response->shipTo->postalCode,
    'toCity' => $response->shipTo->city,
    'toState' => $response->shipTo->state,
    'weight' => (array('value' => intval($totalWeight), 'unit' => 'ounce')),
    'dimensions' => (array('unit' => 'inch', 'length' => 1.0, 'width' => 1.0, 'height' => 1.0)),
);


$sf = array();

if ($totalWeight <= 16) {

    $shippingObject = array(
        'carrierCode' => "stamps_com",
        'fromPostalCode' => '46226',
        'toCountry' => $response->shipTo->country,
        'toPostalCode' => $response->shipTo->postalCode,
        'toCity' => $response->shipTo->city,
        'toState' => $response->shipTo->state,
        'weight' => (array('value' => intval($totalWeight), 'unit' => 'ounce')),
        'dimensions' => (array('unit' => 'inch', 'length' => 5.0, 'width' => 5.0, 'height' => 5.0)),
    );
    $shippingCarriers = $important->CallAPI('POST', 'shipments/getrates', json_encode($shippingObject));
    $estimatedShippingCost = $shippingCarriers[0]->shipmentCost;
    $shippingService = $shippingCarriers[0]->serviceName;
    $serviceCode = $shippingCarriers[0]->serviceCode;
    $carrierId = "stamps_com";
} else {
    foreach ($shippingCarriers as $key => $carrier) {
        if ($carrier->code == 'ups_walleted' || $carrier->code == 'fedex' || $carrier->code == 'stamps_com') {
            $shippingObject = array(
                'carrierCode' => $carrier->code,
                'fromPostalCode' => '46226',
                'toCountry' => $response->shipTo->country,
                'toPostalCode' => $response->shipTo->postalCode,
                'toCity' => $response->shipTo->city,
                'toState' => $response->shipTo->state,
                'weight' => (array('value' => intval($totalWeight), 'unit' => 'ounce')),
                'dimensions' => (array('unit' => 'inch', 'length' => 5.0, 'width' => 5.0, 'height' => 5.0)),
            );
            $shippingRates = $important->CallAPI('POST', 'shipments/getrates', json_encode($shippingObject));

            foreach ($shippingRates as $keyj => $ship) {

                if ($carrier->code == 'fedex') {
                    // echo "<pre>";
                    // print_r(($shippingRates));
                    // echo "</pre>";
                    // exit;

                    if ($ship->serviceCode == 'fedex_ground' || $ship->serviceCode == 'fedex_home_delivery' || $ship->serviceCode == 'fedex_2day' || $ship->serviceCode == 'fedex_first_overnight' || $ship->serviceCode == 'fedex_priority_overnight' || $ship->serviceCode == 'fedex_standard_overnight' || $ship->serviceCode == 'fedex_ground_international' || $ship->serviceCode == 'fedex_international_economy') {
                        $estimatedShippingCost = $ship->shipmentCost;
                        $shippingService = $ship->serviceName;
                        $packageType = 'Package';
                        $serviceCode = $ship->serviceCode;
                        $carrierId = $carrier->code;
                        $sf[] = array('amount' => $ship->shipmentCost, 'shippingService' => $ship->serviceName, 'serviceCode' => $ship->serviceCode, 'carrierId' => $carrier->code);
                    }
                }
                if ($carrier->code == 'stamps_com') {
                    if ($ship->serviceCode == 'usps_priority_mail' || ($product->isMediaMail ? $ship->serviceCode == 'usps_media_mail' : false)) {
                        $estimatedShippingCost = $ship->shipmentCost;
                        $shippingService = $ship->serviceName;
                        $packageType = 'Package';
                        $serviceCode = $ship->serviceCode;
                        $carrierId = $carrier->code;
                        $sf[] = array('amount' => $ship->shipmentCost, 'shippingService' => $ship->serviceName, 'serviceCode' => $ship->serviceCode, 'carrierId' => $carrier->code);
                    }
                }
                if ($carrier->code == 'ups_walleted') {
                    if ($ship->serviceCode == 'ups_next_day_air_saver' || $ship->serviceCode == 'ups_ground' || $ship->serviceCode == 'ups_2nd_day_air') {
                        $estimatedShippingCost = $ship->shipmentCost;
                        $shippingService = $ship->serviceName;
                        $packageType = 'Package';
                        $serviceCode = $ship->serviceCode;
                        $carrierId = $carrier->code;
                        $sf[] = array('amount' => $ship->shipmentCost, 'shippingService' => $ship->serviceName, 'serviceCode' => $ship->serviceCode, 'carrierId' => $carrier->code);
                    }
                }
            }
        }


        if ($sf) {
            $sfS =  min($sf);
            $estimatedShippingCost = ($sfS['amount']) . ' USD';
            $shippingService = $sfS['shippingService'];
            $serviceCode = $sfS['serviceCode'];
            $carrierId = $sfS['carrierId'];
        }
    }
}


?>

<html>

<head>
    <title>Purchasing Order Sheet</title>
    <link rel="stylesheet" type="text/css" media="all" href="reports.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- for Modal -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link href="../../../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid my-5 d-flex justify-content-center">
        <div class="card card-1">
            <div class="card-header bg-white">
                <div id="alertSuccess" class="alert alert-success d-none">

                </div>
                <div id="alertDanger" class="alert alert-danger d-none">

                </div>
                <div class="media flex-sm-row flex-column-reverse justify-content-between ">
                    <div class="col my-auto">
                        <h4 class="mb-0"><span class="change-color"><?php echo $response->advancedOptions->storeId ?></span> </h4>
                    </div>
                    <div class="col my-auto">
                        <h3 class="mb-0">Assign Shipments</h3>
                    </div>
                    <div class="col-auto text-center my-auto pl-0 pt-sm-4">
                        <p class="">Order# &nbsp;<?php echo $response->orderNumber ?></p>
                    </div>
                </div>
            </div>
            <div class="card-header bg-white">

                <input type="hidden" id="customerEmail" value="<?php echo $response->customerEmail ?>">
                <input type="hidden" id="toCountryCode" value="<?php echo $response->shipTo->country ?>">
                <input type="hidden" id="toPostalCode" value="<?php echo $response->shipTo->postalCode ?>">
                <input type="hidden" id="toCity" value="<?php echo $response->shipTo->city ?>">
                <input type="hidden" id="toStateProvince" value="<?php echo $response->shipTo->state ?>">
                <input type="hidden" id="totalWeight" value="<?php echo $totalWeight ?>">

                <div class="pull-left">
                    <p>Ship To: &nbsp; <?php echo $response->customerEmail ?></p>
                    <small><?php echo $response->shipTo->name; ?> <br> <?php echo $response->shipTo->street1; ?> <br>
                        <?php echo $response->shipTo->city . ' ' . $response->shipTo->state . ' ' . $response->shipTo->postalCode . ' ' . $response->shipTo->country; ?>
                    </small>
                </div>
                <div class="pull-right">
                    <p class=" Glasses">Order Status &nbsp; <span class="text-success"> <?php echo strtoupper($response->orderStatus) ?> </span><br>
                        </span></p>
                    <p class=" Glasses">Order Assigned To &nbsp; <span class="text-success"> <?php echo $important->getAssignedOrderUserName($response->orderNumber, $response->orderId, $response->advancedOptions->storeId) ?> </span><br>
                        </span></p>

                </div>
            </div>
            <div class="card-body ">
                <div class="row justify-content-between mb-3">
                    <div class="col-auto">
                        <h6 class="color-1 mb-0 change-color">Order Items</h6>
                    </div>
                    <div class="col-auto "> <small>Order Date: &nbsp;<?php echo date("m-d-Y", strtotime($response->orderDate)); ?></small> </div>
                </div>
                <?php
                echo $details;
                ?>
                <div class="w-100 d-flex mt-3 justify-content-between">

                    <span class="ml-3 text-muted">Total Weight: <?php echo $totalWeight . 'oz' ?></span>
                    <span class="ml-3 text-muted">Total Size: <?php echo number_format((float)$totalSize, 5, '.', '') . ' ft3' ?></span>
                    <span class="ml-3 text-muted">Bag Size: <?php echo $important->getBagSize($totalSize) ?></span>
                    <span class="ml-3 text-muted">Box Size: <?php echo $important->getBoxSize($totalSize) ?></span>

                </div>
                <hr>
                <div class="w-100 d-none ">
                    <small class="text-danger text-center">(Cheapest One)</small>
                </div>
                <div class="w-100 d-flex mt-1 justify-content-between d-none ">
                    <span id="estimatedShippingCost" class="ml-3 text-muted">Estimated Shipping Cost: <?php echo $estimatedShippingCost; ?></span> <!-- $estimatedShippingCost -->
                    <span id="shippingService" class="ml-3 text-muted"> Shipping Service: <?php echo $shippingService; ?></span> <!-- $shippingService -->
                    <span id="serviceCode" class="ml-3 text-muted"> Service Code: <?php echo $serviceCode; ?></span> <!-- $serviceCode -->
                    <!-- <span class="ml-3 text-muted"> Package Type: Package</span>$packageType -->
                </div>
                <div class="w-100 d-flex mt-1 justify-content-between mt-5 d-none ">
                    <select name="shipping_services" id="shippingServices" class="form-control">
                        <option selected value="">Select Shipping</option>

                        <?php $important->getShippingServices();
                        ?>
                    </select>
                </div>

                <div class="serviceCodes d-none">
                    <h6>Select Service Codes</h6>
                    <select name="service_codes" id="serviceCodesSelector" class="form-control">
                        <option selected value="">Select Service Codes</option>
                    </select>
                </div>


                <div class="row mt-4 ">
                    <div class="col">
                        <!-- <div class="row justify-content-between">
                            <div class="col-auto">
                                <p class="mb-1 text-dark"><b>Pricing Details</b></p>
                            </div>
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b>Total</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->payment_details->grand_total->amount . ' ' . strtoupper($response->payment_details->grand_total->currency) ?></p>
                            </div>
                        </div> -->

                        <div class="row justify-content-between">
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b> Tax</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->taxAmount  ?></p>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b> Shipping</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->shippingAmount  ?></p>
                            </div>
                        </div>

                        <div class="row justify-content-between">
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b> Total</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->orderTotal  ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row invoice ">
                    <div class="col">
                        <p class="mb-1"> <b> Customer Name</b> : <?php echo $response->billTo->name ?></p>
                        <p class="mb-1"><b> Customer Email</b> : <?php echo $response->customerEmail ?></p>
                        <p class="mb-1"> <b> Customer Phone</b> :<?php echo $response->billTo->phone ?? 'Not Available' ?></p>
                    </div>
                </div>
                <div class="media flex-sm-row flex-column-reverse justify-content-between ">
                    <div class="col my-auto">
                        <h4 class="mb-0"><span class="change-color"><?php echo $response->advancedOptions->storeId ?></span> </h4>
                        <div class="text-center">
                        <script>
                        // Function to handle the "Finish Order" button click
                        function finishOrder() {
                            // Implement the logic to show the "Finish Order" popup or perform any other desired action here
                        
                        }

                        // Function to handle the click on any of the "Print" buttons
                        function handlePrintButtonClick() {
                            // Show the "Finish Order" button
                            document.getElementById("confirmShipmentBtn").style.display = "inline";
                        }
                        </script>
                            <?php if ($orderStatus != 'Fulfilled' && $orderStatus != 'shipped') { ?>
                                <button type="button" onClick="window.location.reload();" class="btn btn-success">Refresh Order</button>
                            <?php } ?>
                            <?php if ((partial_access('admin') && isset($_SESSION['bulk_fulfillment']) && !$_SESSION['bulk_fulfillment']) || (partial_access('admin') && isset($_SESSION['bulk_fulfillment']) == 0)) { ?>
                                <button type="button" id="confirmShipmentBtn" class="btn btn-danger" style="display: none;" onclick="finishOrder()">Finish Order</button>
                                <button type="button" class="btn btn-danger" onclick="handlePrintButtonClick();">Print Shipping Label</button>
                                <button type="button" class="btn btn-danger" onclick="handlePrintButtonClick();">Print Packing List</button>
                                <button type="button" class="btn btn-danger" onclick="handlePrintButtonClick();">Print Both</button>
                                </div>
                            <?php } else { ?>       
                                <button type="button" id="confirmAssignShippingBtn" class="btn btn-danger">Assign Shipping</button>
                                <?php } ?>

                            <?php if ($orderStatus == 'inprogress' || $orderStatus == 'Not-Assigned') { ?>

                                <button id="cancelOrder" type="button" class="btn btn-primary <?php echo  $orderStatus == 'Not-Assigned' ? 'd-none' : '' ?> ">Cancel Order</button>
                                <!-- <button id="redoOrder" type="button" class="btn btn-danger">Change Address</button> -->

                            <?php } ?>

                            <?php if ($orderStatus == 'shipped') { ?>
                                <button id="redoOrder" type="button" class="btn btn-danger">Reship Order</button>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script>
    var totalItems = <?php echo $totalItems; ?>;
    var assignID = <?php echo $assignID; ?>;
    var totalWeight = "<?php echo ($totalWeight); ?>";
    var orderId = "<?php echo ($orderId); ?>";
    var isMediaMailEligible = "<?php echo ($isMediaMail); ?>";
    var serviceCode = "<?php echo ($serviceCode); ?>";
    var carrierId = "<?php echo ($carrierId); ?>";

    $('#confirmShipmentBtn').click(function(e) {
        paramJSON = {
            'service_code': serviceCode,
            'carrier_id': carrierId,
            'order_id': orderId,
            'assign_order_id': assignID
        }
        $.post(
            './shipengine/update_cart_ajax.php', {
                data: JSON.stringify(paramJSON),
            },
            function(data) {
              window.location.href = "store_orders_list.php?id=<?php echo $store_id; ?>"

                // var result = JSON.parse(data);
            }
        );


    });
    <?php

// Check if the store ID is provided in the URL
if(isset($_GET['id'])) {
    $store_id = $_GET['id'];
    $_SESSION['store_id'] = $store_id; // Save the store ID in the session
} else {
    // Retrieve the store ID from the session
    if(isset($_SESSION['store_id'])) {
        $store_id = $_SESSION['store_id'];
    } else {
        // Handle the case when the store ID is not available
        // You can set a default value or redirect the user to a different page
    }
}
?>  
    $('#confirmAssignShippingBtn').click(function(e) {
        paramJSON = {
            'service_code': serviceCode,
            'carrier_id': carrierId,
            'order_id': orderId,
            'assign_order_id': assignID
        }
        $.post(
            './shipengine/update_cart_ajax.php', {
                data: JSON.stringify(paramJSON),
            },
            function(data) {
              window.location.href = 'store_orders_list.php?id=<?php echo $store_id; ?>'

                // var result = JSON.parse(data);
            }
        );


    });
    $('#shippingServices').on('change', function() {
        const selected = ($(this).find(':selected').data("id"));
        let selectedShip = $(this).find(':selected').val();
        $('#shippingService').text('Shipping Service: ' + $("#shippingServices option:selected").text());
        var data;
        console.log(selectedShip);
        paramJSON = JSON.stringify({
            'carrierCode': selectedShip,
            'fromPostalCode': '46226',
            'toCountry': $('#toCountryCode').val(),
            'toPostalCode': $('#toPostalCode').val(),
            'toCity': $('#toCity').val(),
            'weight': {
                "value": $('#totalWeight').val(),
                'unit': "ounce",
            },
            'dimensions': {
                'unit': 'inch',
                'length': 1.0,
                'width': 1.0,
                'height': 1.0
            }
        });
        var myHeaders = new Headers();
        myHeaders.append("Host", "ssapi.shipstation.com");
        myHeaders.append("Authorization", "Basic " + btoa("c316b6a7b4934fe5a40de02259cb476b" + ":" + "f675644a314e4e44a8023bbc4be4e8cf"));
        myHeaders.append("Content-Type", "application/json");


        var requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: paramJSON,
            redirect: 'follow'
        };

        fetch("https://ssapi.shipstation.com/shipments/getrates", requestOptions)
            .then(response => response.json())
            .then(result => {
                console.log(result);
                $('.serviceCodes').removeClass('d-none');
                $('#serviceCodesSelector').empty()
                $.each(result, function(key, value) {
                    if (selectedShip == 'fedex') {
                        if (value.serviceCode == 'fedex_ground' || value.serviceCode == 'fedex_home_delivery' || value.serviceCode == 'fedex_2day' || value.serviceCode == 'fedex_first_overnight' || value.serviceCode == 'fedex_priority_overnight' || value.serviceCode == 'fedex_standard_overnight' || value.serviceCode == 'fedex_ground_international') {
                            console.log(value)
                            $('#serviceCodesSelector').append('<option data-shipmentCost="' + value.shipmentCost + '" value="' + value.serviceCode + '">' + value.serviceName + '</option>');
                        }
                    }
                    if (selectedShip == 'stamps_com') {
                        if (value.serviceName.includes('Package') || value.serviceName.includes('Package')) {
                            if (value.serviceCode === 'usps_media_mail' && isMediaMailEligible == '0') {
                                return;
                            }
                            $('#serviceCodesSelector').append('<option data-shipmentCost="' + value.shipmentCost + '" value="' + value.serviceCode + '">' + value.serviceName + '</option>');

                        }
                    }
                    if (selectedShip == 'ups_walleted') {
                        if (value.serviceCode == 'ups_next_day_air_saver' || value.serviceCode == 'ups_ground' || value.serviceCode == 'ups_2nd_day_air' || value.serviceCode == 'ups_standard_international' || value.serviceCode == "ups_worldwide_saver" || value.serviceCode == "ups_worldwide_expedited") {
                            $('#serviceCodesSelector').append('<option data-shipmentCost="' + value.shipmentCost + '" value="' + value.serviceCode + '">' + value.serviceName + '</option>');
                        }
                    }
                });

            })
            .catch(error => console.log('error', error));
    });
    $('#serviceCodesSelector').on('change', function() {
        let selectedServiceCodeEle = $(this).find(':selected');
        if (selectedServiceCodeEle) {
            $('#estimatedShippingCost').text('Estimated Shipping Cost: ' + selectedServiceCodeEle.attr('data-shipmentCost'));
            $('#serviceCode').text('Service Code: ' + selectedServiceCodeEle.val());
            serviceCode = selectedServiceCodeEle.val();
            carrierId = $("#shippingServices option:selected").data("id");
        } else {
            console.log('bahr gayaga');
            alert('This shipping method is not available');
        }
    });
</script>


</html>