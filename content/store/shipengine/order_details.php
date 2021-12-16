<?php

include('../system_load.php');
//This loads system.
//user Authentication.
authenticate_user('subscriber');

$user_id = $_SESSION['user_id'];

$orderId = $_GET['id']; // store id;

$function_id = $user->get_user_info($user_id, "user_function");

if ($_SESSION['user_type'] != "admin") {
    if ($function_id != 'storem' or $function_id != 'manager') {
        HEADER('LOCATION: warehouse.php?msg=lstcust');
    }
}

$important = new ImportantFunctions();

$response = $important->CallAPI('GET', "v-beta/sales_orders/" . $orderId);
$content = '';
// var_dump($response);
// return;


?>

<html>

<head>
    <title>Purshasing Order Sheet</title>
    <link rel="stylesheet" type="text/css" media="all" href="reports.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js">
    <link href="../../../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid my-5 d-flex justify-content-center">
        <div class="card card-1">
            <div class="card-header bg-white">
                <div class="media flex-sm-row flex-column-reverse justify-content-between ">
                    <div class="col my-auto">
                        <h4 class="mb-0"><span class="change-color"><?php echo $response->order_source->order_source_nickname ?></span> </h4>
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
                        Fullfillment Status &nbsp;<span style="color:red;"> <?php echo strtoupper($response->sales_order_status->fulfillment_status) ?></span></p>

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

                foreach ($response->sales_order_items as $key => $value) {
                    $details = '';
                    $details = '<div class="row">';
                    $details .= '<div class="col">';
                    $details .= '<div class="card card-2">';
                    $details .= '<div class="card-body">';
                    $details .= '<div class="media">';
                    $details .= '<div class="sq align-self-center "> </div>';
                    $details .= '<div class="media-body my-auto text-right">';
                    $details .= '<div class="row my-auto flex-column flex-md-row">';
                    $details .= '<div class="col my-auto">';
                    $details .= '<h6 class="mb-0">' . $value->line_item_details->name . '</h6>';
                    $details .= '</div>';
                    $details .= '<div class="col-auto my-auto"> <small>Quantity: ' . $value->quantity . ' </small></div>';
                    $details .= '<div class="col my-auto"> <small>SKU: ' . $value->line_item_details->sku . ' </small></div>';
                    $details .= '<div class="col my-auto"> <small>Weight: ' . $value->line_item_details->weight->value . ' ' . $value->line_item_details->weight->unit . ' </small></div>';
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
                    echo $details;
                }

                ?>



                <div class="row mt-4">
                    <div class="col">
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <p class="mb-1 text-dark"><b>Pricing Details</b></p>
                            </div>
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b>Total</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->payment_details->grand_total->amount . ' ' .strtoupper( $response->payment_details->grand_total->currency) ?></p>
                            </div>
                        </div>

                        <div class="row justify-content-between">
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b>Estimated Tax</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->payment_details->estimated_tax->amount . ' ' .strtoupper( $response->payment_details->estimated_tax->currency) ?></p>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="flex-sm-col text-right col">
                                <p class="mb-1"><b>Estimated Shipping</b></p>
                            </div>
                            <div class="flex-sm-col col-auto">
                                <p class="mb-1"><?php echo $response->payment_details->estimated_shipping->amount . ' ' .strtoupper( $response->payment_details->estimated_shipping->currency) ?></p>
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
</body>

</html>