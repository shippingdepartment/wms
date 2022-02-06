<?php

include('../system_load.php');
//This loads system.
//user Authentication.
authenticate_user('subscriber');

$user_id = $_SESSION['user_id'];

$orderId = $_GET['id']; // store id;

$function_id = $user->get_user_info($user_id, "user_function");

// 

$important = new ImportantFunctions();
$product = new Product();
$estimatedShippingCost = 0.0;
$shippingService = null;
$orderStatus = null;
$response = $important->CallAPI('GET', "v-beta/sales_orders/" . $orderId);
$shippingCarriers = $important->CallAPI('GET', 'v1/carriers');
// var_dump($shippingCarriers);
// return;

$content = '';
$totalWeight = 0;
$totalSize = 0;
$totalItems = 0;

$details = '';
$assignID = 0;
if (isset($_GET['assign_id'])) {
    $assignID = $_GET['assign_id'];
}
foreach ($response->sales_order_items as $key => $value) {
    $productname = $value->line_item_details->name;

    $product->moid_set_product_through_sku($value->line_item_details->sku);
    $details .= '<div class="row">';
    $details .= '<div class="col">';
    $details .= '<div class="card card-2">';
    $details .= '<div class="card-body">';
    $details .= '<div class="media">';
    $details .= '<div class="sq align-self-center "> </div>';
    $details .= '<div class="media-body my-auto text-right">';
    $details .= '<div class="row my-auto flex-column flex-md-row">';
    $details .= '<div class="col my-auto">';
    $details .= '<div class="col my-auto"> <small>SKU: ' . $value->line_item_details->sku . ' </small></div>';
    $details .= '</div>';
    $details .= '<div class="col-auto my-auto"> <small>Quantity: ' . $value->quantity . ' </small></div>';
    $details .= '<div class="col my-auto">' . $value->line_item_details->name . '</div>';
    $details .= '<div class="col my-auto"> <small>Weight: ' . $product->pounds . 'lb ' . ($product->ounces * $value->quantity) . 'oz <br>Size: ' . $product->long_pr . 'l ' . $product->larg . 'w ' . ($product->haut * $value->quantity) . 'h' . ' </small></div>';
    $details .= ' <div class="col my-auto"> <small class="mb-0">' . $value->requested_shipping_options->shipping_service  . '</small> </div>';
    $details .= ' <div class="col my-auto"> <h6 class="mb-0">' . $value->price_summary->unit_price->amount . ' ' . strtoupper($value->price_summary->unit_price->currency) . '</h6> </div>';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '<div class="text-right"> <button id=' . $value->line_item_details->sku . ' type="button" class="btn btn-success" data-product-quantity=' . $value->quantity . '  data-product-name=' . $productname . ' data-sku=' . $value->line_item_details->sku . ' onclick="verifyThePick(this)" > Verify Pick </button> </div>';

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
$orderStatus = $important->getOrderStatus($response->external_order_number, $response->sales_order_id);
$carrierCode = '';
$carrierId = '';
// return;

// If the order is less than 16 oz then we can default to usps first class
// setting the postObject for shipping rates
$shippingObject = array(
    'carrier_ids' => ["se-647551"],
    'from_country_code' => 'US',
    'from_postal_code' => '46226',
    'to_country_code' => $response->ship_to->country_code,
    'to_postal_code' => $response->ship_to->postal_code,
    'to_city_locality' => $response->ship_to->city_locality,
    'to_state_province' => $response->ship_to->state_province,
    'weight' => (array('value' => intval($totalWeight), 'unit' => 'ounce')),
    'dimensions' => (array('unit' => 'inch', 'length' => 1.0, 'width' => 1.0, 'height' => 1.0)),
);


$sf = [];

if ($totalWeight <= 16) {

    $shippingObject = array(
        'carrier_ids' => ["se-647551"],
        'from_country_code' => 'US',
        'from_postal_code' => '46226',
        'to_country_code' => $response->ship_to->country_code,
        'to_postal_code' => $response->ship_to->postal_code,
        'to_city_locality' => $response->ship_to->city_locality,
        'to_state_province' => $response->ship_to->state_province,
        'weight' => (array('value' => intval($totalWeight), 'unit' => 'ounce')),
        'dimensions' => (array('unit' => 'inch', 'length' => 5.0, 'width' => 5.0, 'height' => 5.0)),
    );
    $shippingCarriers = $important->CallAPI('POST', 'v1/rates/estimate', json_encode($shippingObject));
    $estimatedShippingCost = $shippingCarriers[1]->shipping_amount->amount . ' ' . strtoupper($shippingCarriers[1]->shipping_amount->currency);
    $shippingService = $shippingCarriers[1]->carrier_friendly_name;
    $packageType = $shippingCarriers[1]->package_type;
    $serviceCode = $shippingCarriers[1]->service_code;
    $carrierId = "se-647551";
} else {
    // unset($shippingCarriers->carriers[0]);

    foreach ($shippingCarriers->carriers as $key => $carrier) {
        if ($carrier->carrier_id == 'se-647512') {
            continue; //ignoring the FEDX SHIPPING
        }
        $shippingObject = array(
            'carrier_ids' => [$carrier->carrier_id],
            'from_country_code' => 'US',
            'from_postal_code' => '78756',
            'to_country_code' => $response->ship_to->country_code,
            'to_postal_code' => $response->ship_to->postal_code,
            'to_city_locality' => $response->ship_to->city_locality,
            'to_state_province' => $response->ship_to->state_province,
            'weight' => (array('value' => intval($totalWeight), 'unit' => 'ounce')),
            'dimensions' => (array('unit' => 'inch', 'length' => 5.0, 'width' => 5.0, 'height' => 5.0)),
        );
        $shippingCarriers = $important->CallAPI('POST', 'v1/rates/estimate', json_encode($shippingObject));

        foreach ($shippingCarriers as $keyj => $ship) {
            //usps_media_mail is not required
            if ($ship->service_code != 'usps_media_mail') {
                $estimatedShippingCost = $ship->shipping_amount->amount . ' ' . strtoupper($ship->shipping_amount->currency);
                $shippingService = $ship->carrier_friendly_name;
                $packageType = 'Package';
                $serviceCode = $ship->service_code;
                $carrierId = $carrier->carrier_id;
                $sf[] = array('amount' => $ship->shipping_amount->amount, 'shippingService' => $ship->carrier_friendly_name, 'serviceCode' => $ship->service_code, 'carrierId' => $carrier->carrier_id);
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
                        <h4 class="mb-0"><span class="change-color"><?php echo $response->order_source->order_source_nickname ?></span> </h4>
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
                        <p class="">Order# &nbsp;<?php echo $response->external_order_number ?></p>
                    </div>
                </div>
            </div>
            <div class="card-header bg-white">
                <div class="pull-left">
                    <p>Ship To: &nbsp; <?php echo $response->customer->email ?></p>
                    <small><?php echo $response->ship_to->name; ?> <br> <?php echo $response->ship_to->address_line1; ?> <br>
                        <?php echo $response->ship_to->city_locality . ' ' . $response->ship_to->state_province . ' ' . $response->ship_to->postal_code . ' ' . $response->ship_to->country_code; ?>
                    </small>
                </div>
                <div class="pull-right">
                    <p class=" Glasses">Payment Status &nbsp; <span class="text-success"> <?php echo strtoupper($response->sales_order_status->payment_status) ?> </span><br>
                        </span></p>

                </div>
            </div>
            <div class="card-body">
                <div class="row justify-content-between mb-3">
                    <div class="col-auto">
                        <h6 class="color-1 mb-0 change-color">Order Items</h6>
                    </div>
                    <div class="col-auto "> <small>Order Date: &nbsp;<?php echo date("m-d-Y", strtotime($response->order_date)); ?></small> </div>
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
                    <span class="ml-3 text-muted">Estimated Shipping Cost: <?php echo $estimatedShippingCost ?></span>
                    <span class="ml-3 text-muted"> Shipping Service: <?php echo $shippingService ?></span>
                    <span class="ml-3 text-muted"> Service Code: <?php echo $serviceCode ?></span>
                    <span class="ml-3 text-muted"> Package Type: <?php echo $packageType ?></span>
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
                                <p class="mb-1"><?php echo $response->payment_details->estimated_tax->amount . ' ' . strtoupper($response->payment_details->estimated_tax->currency) ?></p>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b> Shipping</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->payment_details->estimated_shipping->amount . ' ' . strtoupper($response->payment_details->estimated_shipping->currency) ?></p>
                            </div>
                        </div>

                        <div class="row justify-content-between">
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b> Total</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->payment_details->grand_total->amount . ' ' . strtoupper($response->payment_details->grand_total->currency) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row invoice ">
                    <div class="col">
                        <p class="mb-1"> <b> Customer Name</b> : <?php echo $response->customer->name ?></p>
                        <p class="mb-1"><b> Customer Email</b> : <?php echo $response->customer->email ?></p>
                        <p class="mb-1"> <b> Customer Phone</b> :<?php echo $response->ship_to->phone ?? 'Not Available' ?></p>
                    </div>
                </div>
                <div class="media flex-sm-row flex-column-reverse justify-content-between ">
                    <div class="col my-auto">
                        <h4 class="mb-0"><span class="change-color"><?php echo $response->order_source->order_source_nickname ?></span> </h4>
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
        var assignID = <?php echo $assignID; ?>;
        var serviceCode = "<?php echo ($serviceCode); ?>";
        var carrierId = "<?php echo ($carrierId); ?>";
        var totalWeight = "<?php echo ($totalWeight); ?>";


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
        var orderNo = "<?php echo  $response->external_order_number ?>";
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
</script>

</html>