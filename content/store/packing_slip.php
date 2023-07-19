<?php

include('system_load.php');
//This loads system.
//user Authentication.
authenticate_user('subscriber');
$user_id = $_SESSION['user_id'];
$orderId = $_GET['order_id']; // store id;
$function_id = $user->get_user_info($user_id, "user_function");


$important = new ImportantFunctions();
$product = new Product();

$response = $important->CallAPI('GET', "v-beta/sales_orders/" . $orderId);


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

    $details .= '<hr class="my-3 ">';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';
    $details .= '</div>';

  
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
	<style>
			
		@media print {
			#printButton {
				display: none;
			}
		}
		
		</style>

</head>

<body>
    <div class="container-fluid my-5 d-flex justify-content-center">
        <div class="card card-1">
            <div class="card-header bg-white">
                <div class="media flex-sm-row flex-column-reverse justify-content-between ">
                    <div class="col my-auto">
                        <h4 class="mb-0"><span class="change-color"><?php echo $response->order_source->order_source_nickname ?></span> </h4>
                    </div>
                    <div class="col my-auto d-none" id="isEveryThingDone" id="isEveryThingDonee">

                        Select Cart
                        <select name="cart_option" id="cart_option">
                            <option value="" disabled>Select</option>
                            <?php
                            foreach ($currentCarts as $key => $value) {
                                echo '  <option value="' . $value . '">' . $value . '</option>';
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
                    Ship To: &nbsp; <small><?php echo $response->ship_to->name; ?> <br> <?php echo $response->ship_to->address_line1; ?> <br>
                        <?php echo $response->ship_to->city_locality . ' ' . $response->ship_to->state_province . ' ' . $response->ship_to->postal_code . ' ' . $response->ship_to->country_code; ?>
                    </small>

                </div>
                <div class="pull-right">
                    <div class="row invoice ">
                        <div class="col">
                            <p class="mb-1"> <b> Customer Name</b> : <?php echo $response->customer->name ?></p>
                            <p class="mb-1"><b> Customer Email</b> : <?php echo $response->customer->email ?></p>
                            <p class="mb-1"> <b> Customer Phone</b> :<?php echo $response->ship_to->phone ?? 'Not Available' ?></p>
                        </div>
                    </div>
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

                <hr>


                <div class="row mt-4">
                    <div class="col">
                    <button id="printButton" class="btn btn-info" onClick="window.print();"><i class="fa fa-print"></i> Print</button>


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

            </div>

        </div>
    </div>




</body>

</html>