<?php

include('system_load.php');
include_once 'includes/config.php';

//This loads system.
//user Authentication.
authenticate_user('store_owner');

$user_id = $_SESSION['user_id'];

$orderId = $_GET['id']; // store id;


$important = new ImportantFunctions();
$product = new Product();
$estimatedShippingCost = 0.0;
$shippingService = null;
$storeSourceId = null;
$response = $important->CallAPI('GET', "v-beta/sales_orders/" . $orderId);
$orderSourceId = $response->order_source->order_source_id;


$storePrices = $important->getStorePricesFromSourceId($orderSourceId);


$content = '';
$totalWeight = 0;
$totalSize = 0;
$totalItems = 0;

$details = '';
$assignID = 0;

$shipmentPayment = 0.0;
$paymentFromProduct = 0.0;
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
    $details .= '<hr class="my-3 ">';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';

    $totalWeight += ((($product->pounds) * 16) * $value->quantity) + ($product->ounces * $value->quantity);
    $tempSize = ($product->long_pr * $product->larg * ($product->haut * $value->quantity)) / 1728;
    $totalSize += $tempSize;

    if ($key == 0) {
        $paymentFromProduct = $storePrices['first_item_price'];
    } else {
        $paymentFromProduct += ($storePrices['each_item_price'] * $value->quantity);
    }

    $totalItems++;
}


$currentCarts = $important->getFreeCarts();
$carrierCode = '';
$carrierId = '';

$shippingObject = $important->getOrderTrackingStatus($response->external_order_number);
$orderPaidData = $important->orderPaidData($response->external_order_number);

if ($shippingObject != null) {
    $shipmentPayment = $shippingObject['shipment_cost'] + $paymentFromProduct;
}

$orderStatus = ($important->getOrderStatus($response->external_order_number, $response->sales_order_id));

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
                    Your payment has been done.
                </div>
                <div id="alertDanger" class="alert alert-danger d-none">
                    Failed, Your payment has some issues
                </div>
                <div class="media flex-sm-row flex-column-reverse justify-content-between ">
                    <div class="col my-auto">
                        <h4 class="mb-0"><span class="change-color"><?php echo $response->order_source->order_source_nickname ?></span> </h4>
                        <div class="text-center">
                            <?php if ($orderStatus != 'Fulfilled' && $orderStatus != 'shipped' ) { ?>
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
                    <p class=" Glasses">Payment Status &nbsp; <span class="text-success"> <?php echo strtoupper($response->sales_order_status->payment_status) ?> </span>

                        <br>
                        Order Status &nbsp;<span class="text-success"> <?php echo strtoupper($orderStatus); ?></span>


                        <?php if ($orderPaidData != null) { ?>
                            <br>
                            Transaction# &nbsp;<span class="text-success"> <?php echo $orderPaidData['txt_id'] ?></span>
                            <br>
                            Amount: &nbsp;<span class="text-success">$ <?php echo $orderPaidData['amount_paid']  ?> </span>


                        <?php } ?>
                    </p>
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
                        <!-- <div class="row justify-content-between">
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b> Shipping</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $shippingObject['shipment_cost'] . ' ' . strtoupper($response->payment_details->estimated_shipping->currency) ?></p>
                            </div>
                        </div> -->

                        <div class="row justify-content-between">
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b> Total</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->payment_details->grand_total->amount ?? 0.0 + $shippingObject['shipment_cost'] ?? 0.0 . ' ' . strtoupper($response->payment_details->grand_total->currency) ?></p>
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
            </div>
            <?php if (($important->getOrderStatus($response->external_order_number, $response->sales_order_id) == 'shipped') && $orderPaidData == null) { ?>
                <div class="card-footer">
                    <div class="jumbotron-fluid">
                        <div class="row justify-content-between ">
                            <div class="col-auto my-auto ">
                                <div class="w-80 text-center">
                                    <h5 type="button" class="">Pay $<?php echo $shipmentPayment; ?></h5>
                                    <div id="paypal-button-container" class="mt-5"></div>
                                </div>

                            </div>

                        </div>



                    </div>
                </div>
            <?php } ?>



        </div>
    </div>




</body>

<script src="https://www.paypal.com/sdk/js?client-id=AYDx_HSMTriwXkUkWbWbK2vfx8Vhatr_z_gHaK1n0i-qBvTQB5kpvCoWDPWtSQKb_ML-5VoWXNsC4uiH">
    // Required. Replace YOUR_CLIENT_ID with your sandbox client ID.
</script>

<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            // This function sets up the details of the transaction, including the amount and line item details.
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: "<?php echo $shipmentPayment ?>"
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            // This function captures the funds from the transaction.
            return actions.order.capture().then(function(details) {
                // This function shows a transaction success message to your buyer.
                alert('Transaction completed by ' + details.purchase_units[0].payments.captures[0].id);
                console.log(details);

                //STATUS => details.status
                //TXTID => details.purchase_units[0].id
                //amountCharg => details.purchase_units[0].amount.value
                //paidBy => details.payer.email_address

                if (details.status === 'COMPLETED') {
                    paramJSON = {
                        'order_no': "<?php echo $response->external_order_number ?>",
                        'amount_paid': details.purchase_units[0].amount.value,
                        'txt_id': details.purchase_units[0].payments.captures[0].id,
                        'paid_by': details.payer.email_address,

                    }
                    $.post(
                        'shipengine/add_payment.php', {
                            data: JSON.stringify(paramJSON),
                        },
                        function(data) {
                            console.log(data);
                            $('#alertSuccess').removeClass('d-none');
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);

                        }
                    );
                } else {
                    $('#alertDanger').removeClass('d-none');

                }
            });
        }
    }).render('#paypal-button-container');
    //This function displays Smart Payment Buttons on your web page.


    $('#redoOrder').click(function() {
        var orderNo = "<?php echo  $response->external_order_number ?>";
        paramJSON = {
            'order_no': orderNo,
            'status': 'reship',
        }
        $.post(
            'shipengine/redo_order.php', {
                data: JSON.stringify(paramJSON),
            },
            function(data) {
                var response = (data);
                if (response.success === true) {
                    $('#alertSuccess').text('Order reship request has been sent successfully');
                    $('#alertSuccess').removeClass('d-none');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);

                } else {
                    $('#alertDanger').text('Some Error Occured');
                    $('#alertDanger').removeClass('d-none');
                }

            }
        );
    });

    $('#cancelOrder').click(function() {
        var orderNo = "<?php echo  $response->external_order_number ?>";
        paramJSON = {
            'order_no': orderNo,
            'status': 'canceled',
        }
        $.post(
            'shipengine/redo_order.php', {
                data: JSON.stringify(paramJSON),
            },
            function(data) {
                var response = (data);
                if (response.success === true) {
                    $('#alertSuccess').text('Order canceled successfully');
                    $('#alertSuccess').removeClass('d-none');
                    setTimeout(function() {
                        window.location.reload();
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