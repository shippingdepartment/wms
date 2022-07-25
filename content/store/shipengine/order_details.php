<?php

include('../system_load.php');
//This loads system.
//user Authentication.
authenticate_user('subscriber');
$user_id = $_SESSION['user_id'];

$orderId = $_GET['id']; // store id;

$function_id = $user->get_user_info($user_id, "user_function");



$important = new ImportantFunctions();
// $important->getShippingServices();
$product = new Product();
$estimatedShippingCost = 0.0;
$shippingService = null;
$orderStatus = null;
$response = $important->CallAPI('GET', "orders/" . $orderId);
$shippingCarriers = $important->CallAPI('GET', 'carriers');
// echo "<pre>";
// print_r(($shippingCarriers));
// echo "</pre>";
// exit;

$content = '';
$totalWeight = 0;
$totalSize = 0;
$totalItems = 0;

$details = '';
$assignID = 0;
if (isset($_GET['assign_id'])) {
    $assignID = $_GET['assign_id'];
} else {
    $assignID = $important->getAssignOrderId($orderId);
}

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
    $details .= '<div class="text-right"> <button id=' . $value->sku . ' type="button" class="btn btn-success" data-product-quantity=' . $value->quantity . '  data-product-name=' . $productname . ' data-sku=' . $value->sku . ' onclick="verifyThePick(this)" > Verify Pick </button> </div>';

    $details .= '<hr class="my-3 ">';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';

    $totalWeight += ((($product->pounds) * 16) * $value->quantity) + ($product->ounces * $value->quantity);
    $tempSize = ($product->long_pr * $product->larg * ($product->haut * $value->quantity)) / 1728;
    $totalSize += $tempSize;

    $totalItems++;
}

$currentCarts = $important->getFreeCarts();
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


$sf = [];

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
    // echo "<pre>";
    //  print_r(($shippingCarriers));
    // echo "</pre>";
    // exit;

    $estimatedShippingCost = $shippingCarriers[0]->shipmentCost;
    $shippingService = $shippingCarriers[0]->serviceName;
    // $packageType = $shippingCarriers[0]->package_type;
    $serviceCode = $shippingCarriers[0]->serviceCode;
    $carrierId = "stamps_com";
} else {
    foreach ($shippingCarriers as $key => $carrier) {
        // if ($carrier->carrier_id == 'se-647512') {
        //     continue; //ignoring the FEDX SHIPPING
        // }
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
        $shippingRates = $important->CallAPI('POST', 'shipments/getrates', json_encode($shippingObject));
        //         echo "<pre>";
        //  print_r(($shippingRates));
        // echo "</pre>";
        // exit;
        foreach ($shippingRates as $keyj => $ship) {
            //usps_media_mail is not required
            if ($ship->serviceCode != 'usps_media_mail') {
                $estimatedShippingCost = $ship->shipmentCost;
                $shippingService = $ship->serviceName;
                $packageType = 'Package';
                $serviceCode = $ship->serviceCode;
                $carrierId = $carrier->code;
                $sf[] = array('amount' => $ship->shipmentCost, 'shippingService' => $ship->serviceName, 'serviceCode' => $ship->serviceCode, 'carrierId' => $carrier->code);
            }
        }
    }
    $sf =  min($sf);
    // echo "<pre>";
    //  print_r(($sf));
    // echo "</pre>";
    // exit;
    $estimatedShippingCost = $sf['amount'] . ' USD';
    $shippingService = $sf['shippingService'];
    $serviceCode = $sf['serviceCode'];
    $carrierId = $sf['carrierId'];
    // echo "<pre>";
    // print_r(min($sf));
    // echo "</pre>";
    // exit;
}




?>

<html>

<head>
    <title>Purshasing Order Sheet</title>
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
                    <div class="col my-auto d-none" id="isEveryThingDone">

                        Select Cart
                        <select name="cart_option" id="cart_option">
                            <option value="" disabled>----Select----</option>
                            <?php
                            foreach ($currentCarts as $key => $value) {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                            ?>


                        </select>

                        <button type="button" id="confirmedBtn" style="margin-left: 20px;;" class="btn btn-success">Done</button>

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
            <div class="card-body">
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
                <div class="w-100">
                    <small class="text-danger text-center">(Cheapest One)</small>
                </div>
                <div class="w-100 d-flex mt-1 justify-content-between">
                    <span id="estimatedShippingCost" class="ml-3 text-muted">Estimated Shipping Cost: <?php echo $estimatedShippingCost; ?></span> <!-- $estimatedShippingCost -->
                    <span id="shippingService" class="ml-3 text-muted"> Shipping Service: <?php echo $shippingService; ?></span> <!-- $shippingService -->
                    <span id="serviceCode" class="ml-3 text-muted"> Service Code: <?php echo $serviceCode; ?></span> <!-- $serviceCode -->
                    <!-- <span class="ml-3 text-muted"> Package Type: Package</span>$packageType -->
                </div>
                <div class="w-100 d-flex mt-1 justify-content-between mt-5">
                    <select name="shipping_services" id="shippingServices" class="form-control">
                        <option selected value="">Select Shipping</option>

                        <?php $important->getShippingServices();
                        ?>
                    </select>
                </div>


                <div class="row mt-4">
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
                            <?php if ($orderStatus != 'Fulfilled' && $orderStatus != 'shipped') { ?>
                                <button type="button" onClick="window.location.reload();" class="btn btn-success">Refresh Order</button>
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
            <!-- <div class="card-footer">
                <div class="jumbotron-fluid">
                    <div class="row justify-content-between ">
                        <div class="col-sm-auto col-auto my-auto"><img class="img-fluid my-auto align-self-center " src="https://i.imgur.com/7q7gIzR.png" width="115" height="115"></div>
                        <div class="col-auto my-auto ">
                            <h2 class="mb-0 font-weight-bold">TOTAL PAID</h2>
                        </div>
                        <div class="col-auto my-auto ml-auto">
                            <h1 class="display-3 ">&#8377; 5,528</h1>
                        </div>
                    </div>
                    <div class="row mb-3 mt-3 mt-md-0">
                        <div class="col-auto border-line"> <small class="text-white">PAN:AA02hDW7E</small></div>
                        <div class="col-auto border-line"> <small class="text-white">CIN:UMMC20PTC </small></div>
                        <div class="col-auto "><small class="text-white">GSTN:268FD07EXX </small> </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="verifyPickModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="informQuantity" class="text-muted"></span>
                    <input type="hidden" name="" id="requiredQuantity">
                    <input type="hidden" name="" id="sku">
                    <input type="text" class="form-control mt-3" id="enteredQuantity" placeholder="Enter Quantity">
                    <div id="errorMsg" class="errorMsg text-center d-none">
                        <p class="text-danger ">Invalid Quantity Selected</p>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="confirmPick" class="btn btn-primary">Confirm Pick</button>
                </div>
            </div>
        </div>
    </div>

</body>
<script>
    var pickedItems = 0;
    var totalItems = <?php echo $totalItems; ?>;

    var assignID = <?php echo $assignID; ?>;
    var serviceCode = "<?php echo ($serviceCode); ?>";
    var carrierId = "<?php echo ($carrierId); ?>";
    var totalWeight = "<?php echo ($totalWeight); ?>";
    var orderId = "<?php echo ($orderId); ?>";


    function verifyThePick(e) {
        $('#exampleModalLongTitle').text(e.getAttribute('data-sku'))
        $('#informQuantity').text('Quantity ' + e.getAttribute('data-product-quantity'));
        $('#requiredQuantity').val(e.getAttribute('data-product-quantity'));
        $('#sku').val(e.getAttribute('data-sku'));
        $('#verifyPickModal').modal('toggle');
    }

    $('#confirmPick').click(function(e) {
        var requiredQuantity = $('#requiredQuantity').val();
        var enteredQuantity = $('#enteredQuantity').val();
        var sku = $('#sku').val();
        if (requiredQuantity === enteredQuantity) {
            $('#' + sku).addClass('d-none');
            $('#errorMsg').addClass('d-none');

            $('#verifyPickModal').modal('toggle');
            $('#isEveryThingDone').removeClass('d-none');

        } else {
            $('#errorMsg').removeClass('d-none');
            return;
        }


        $('#enteredQuantity').val(null);

    });
    $('#cart_option').on('change', function() {
        selectedCart = this.value;
    });
    $('#confirmedBtn').click(function(e) {
        paramJSON = {
            'assign_order_id': parseInt(assignID),
            'cart': $('#cart_option').find(":selected").text(),
            'service_code': serviceCode,
            'carrier_id': carrierId,
            'total_weight': totalWeight,
        }
        $.post(
            'assign_cart_ajax.php', {
                data: JSON.stringify(paramJSON),
            },
            function(data) {
                window.location.href = "../assigned_orders_list.php"
                // var result = JSON.parse(data);
            }
        );


    });

    $('#cancelOrder').click(function(e) {
        e.preventDefault();
        var isConfirmed = confirm("Are you Confirm ?");
        if (!isConfirmed) {
            return;
        }
        var orderNo = "<?php echo  $response->orderId ?>";
        paramJSON = {
            'order_no': orderNo,
            'status': 'canceled',
        }
        $.post(
            'redo_order.php', {
                data: JSON.stringify(paramJSON),
            },
            function(data) {
                var response = (data);
                if (response.success === true) {
                    $('#alertSuccess').text('Order canceled successfully');
                    $('#alertSuccess').removeClass('d-none');
                    setTimeout(function() {
                        window.location.href = "../assigned_orders_list.php"
                    }, 2000);

                } else {
                    $('#alertDanger').text('Some Error Occured');
                    $('#alertDanger').removeClass('d-none');
                }

            }
        );
    });


    $('#shippingServices').on('change', function() {
        const selected = ($(this).find(':selected').data("id"));
        $('#shippingService').text('Shipping Service: ' + $("#shippingServices option:selected").text());
        var data;
        console.log($(this).find(':selected').val());
        paramJSON = JSON.stringify({
            'carrierCode': $(this).find(':selected').val(),
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
                // TODO: WORKING ON SHIPPING PART
                // $.each(result, function(key, value) {
                //     console.log(value)
                //     data = value[0];
                //     console.log('andr agay');
                //     return false;
                // })
                var lowest = Number.POSITIVE_INFINITY;
                // var highest = Number.NEGATIVE_INFINITY;
                var tmp;
                var shippingObject
                for (var i = result.length - 1; i >= 0; i--) {
                    tmp = result[i].shipmentCost;
                    if (tmp < lowest) {
                        lowest = tmp;
                        shippingObject = result[i];
                    }
                    // if (tmp > highest) highest = tmp;
                }
                if (shippingObject) {
                    $('#estimatedShippingCost').text('Estimated Shipping Cost: ' + shippingObject.shipmentCost)
                    $('#serviceCode').text('Service Code: ' + shippingObject.serviceCode);
                    serviceCode = shippingObject.serviceCode;
                    carrierId = $("#shippingServices option:selected").data("id");
                } else {
                    console.log('bahr gayaga');
                    alert('This shipping method is not available');
                }
            })
            .catch(error => console.log('error', error));
    });
</script>


</html>