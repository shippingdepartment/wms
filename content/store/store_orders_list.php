<?php
include('system_load.php');
//This loads system.
//user Authentication.
authenticate_user('subscriber');

$user_id = $_SESSION['user_id'];
$store_id = $_GET['id']; // store id;
$function_id = $user->get_user_info($user_id, "user_function");

if ($_SESSION['user_type'] != "admin") {
    if ($function_id != 'storem' or $function_id != 'manager') {
        HEADER('LOCATION: warehouse.php?msg=lstcust');
    }
}

$important = new ImportantFunctions();
// return;
$user = new Users();

$response = $important->CallAPI('GET', "orders?storeId=" . $store_id . "&orderStatus=awaiting_shipment&sortBy=OrderDate&sortDir=DESC&pageSize=300");
$content = '';
// var_dump($response);
// return;

// $filters = $important->getSpecificStoreFilters($store_id);



// $content .= '</tr>';
if (false) {
    while ($row = $filters->fetch_array()) {
        extract($row);
        if ($filter_name == 'country') {
            foreach ($response->sales_orders as $key => $value) {
                if ($filter_sign == '==') {
                    if ($filter_value ==  $value->ship_to->country_code) {
                        $product = new Product();
                        foreach ($value->sales_order_items as $key => $lineItems) {
                            $lineItemsDetails = $lineItems->line_item_details;
                            $resp = $product->moidAddProduct(
                                $lineItemsDetails->sku,
                                $lineItemsDetails->name,
                                $lineItemsDetails->weight->unit,
                                $lineItems->price_summary->estimated_tax->amount,
                                $lineItems->price_summary->unit_price->amount,
                                $lineItems->price_summary->unit_price->amount,
                                100,
                                $value->external_order_number,
                                $store_id,
                            );
                        }
                        if ($value->sales_order_status->fulfillment_status == 'unfulfilled') {
                            $lastOrderSourceId = $important->getLastOrderId();
                            $isOrderExists = $important->checkIfOrderExists($value->external_order_number, $value->sales_order_id);
                            if ($isOrderExists) {
                                $orderStatus =  $important->getOrderStatus($value->external_order_number, $value->sales_order_id);
                                if ($orderStatus == 'Fulfilled')
                                    continue;
                            } else {
                                $important->assignOrdersTORandom($value->external_order_number, $value->sales_order_id, $value->order_source->order_source_id);
                            }


                            $isAssigned = $important->checkOrderIsAssigned($value->sales_order_id) == true ? 'Assigned' : 'Not-Assigend';
                            $content .= '<tr class="">';
                            $content .= '<td>';
                            $content .= $value->external_order_number;
                            $content .= '</td><td>';
                            $content .= count($value->sales_order_items) > 1 ? 'Multiple' : $value->sales_order_items[0]->line_item_details->name;
                            $content .= '</td>';
                            $content .= '</td><td>';
                            $content .= count($value->sales_order_items) > 1 ? 'Multiple' : $value->sales_order_items[0]->line_item_details->sku;
                            $content .= '</td>';
                            $content .= '</td><td>';
                            $content .=  date("m-d-Y", strtotime($value->order_date));
                            $content .= '</td><td>';
                            $content .= $isAssigned;
                            $content .= '</td>';
                            $content .= '<td > ' . $value->sales_order_id;
                            $content .= '</td>';
                            $content .= '<td> <a href="shipengine/order_details.php?id=' . $value->sales_order_id . '" target="_self"><i class="fa fa-eye" style="font-size:16px"></i></a>';

                            $content .= '</td>';
                            $content .= '</tr>';
                        }
                    }
                } else {
                    if ($filter_value !=  $value->ship_to->country_code) {
                        $product = new Product();
                        foreach ($value->sales_order_items as $key => $lineItems) {
                            $lineItemsDetails = $lineItems->line_item_details;
                            $resp = $product->moidAddProduct(
                                $lineItemsDetails->sku,
                                $lineItemsDetails->name,
                                $lineItemsDetails->weight->unit,
                                $lineItems->price_summary->estimated_tax->amount,
                                $lineItems->price_summary->unit_price->amount,
                                $lineItems->price_summary->unit_price->amount,
                                100,
                                $value->external_order_number,
                                $store_id,
                            );
                        }
                        if ($value->sales_order_status->fulfillment_status == 'unfulfilled') {
                            $lastOrderSourceId = $important->getLastOrderId();
                            $isOrderExists = $important->checkIfOrderExists($value->external_order_number, $value->sales_order_id);
                            if ($isOrderExists) {
                                $orderStatus =  $important->getOrderStatus($value->external_order_number, $value->sales_order_id);
                                if ($orderStatus == 'Fulfilled')
                                    continue;
                            } else {
                                $important->assignOrdersTORandom($value->external_order_number, $value->sales_order_id, $value->order_source->order_source_id);
                            }


                            $isAssigned = $important->checkOrderIsAssigned($value->sales_order_id) == true ? 'Assigned' : 'Not-Assigend';
                            $content .= '<tr class="">';
                            $content .= '<td>';
                            $content .= $value->external_order_number;
                            $content .= '</td><td>';
                            $content .= count($value->sales_order_items) > 1 ? 'Multiple' : $value->sales_order_items[0]->line_item_details->name;
                            $content .= '</td>';
                            $content .= '</td><td>';
                            $content .= count($value->sales_order_items) > 1 ? 'Multiple' : $value->sales_order_items[0]->line_item_details->sku;
                            $content .= '</td>';
                            $content .= '</td><td>';
                            $content .=  date("m-d-Y", strtotime($value->order_date));
                            $content .= '</td><td>';
                            $content .= $isAssigned;
                            $content .= '</td>';
                            $content .= '<td > ' . $value->sales_order_id;
                            $content .= '</td>';
                            $content .= '<td> <a href="shipengine/order_details.php?id=' . $value->sales_order_id . '" target="_self"><i class="fa fa-eye" style="font-size:16px"></i></a>';

                            $content .= '</td>';
                            $content .= '</tr>';
                        }
                    }
                }
            }
        } else if ($filter_name == 'shipping_service') {
            foreach ($response->sales_orders as $key => $value) {
                if ($filter_sign == '==') {
                    if ($filter_value ==  $value->sales_order_items[0]->requested_shipping_options->shipping_service) {
                        $product = new Product();
                        foreach ($value->sales_order_items as $key => $lineItems) {
                            $lineItemsDetails = $lineItems->line_item_details;
                            $resp = $product->moidAddProduct(
                                $lineItemsDetails->sku,
                                $lineItemsDetails->name,
                                $lineItemsDetails->weight->unit,
                                $lineItems->price_summary->estimated_tax->amount,
                                $lineItems->price_summary->unit_price->amount,
                                $lineItems->price_summary->unit_price->amount,
                                100,
                                $value->external_order_number,
                                $store_id,
                            );
                        }
                        if ($value->sales_order_status->fulfillment_status == 'unfulfilled') {
                            $lastOrderSourceId = $important->getLastOrderId();
                            $isOrderExists = $important->checkIfOrderExists($value->external_order_number, $value->sales_order_id);
                            if ($isOrderExists) {
                                $orderStatus =  $important->getOrderStatus($value->external_order_number, $value->sales_order_id);
                                if ($orderStatus == 'Fulfilled')
                                    continue;
                            } else {
                                $important->assignOrdersTORandom($value->external_order_number, $value->sales_order_id, $value->order_source->order_source_id);
                            }


                            $isAssigned = $important->checkOrderIsAssigned($value->sales_order_id) == true ? 'Assigned' : 'Not-Assigend';
                            $content .= '<tr class="">';
                            $content .= '<td>';
                            $content .= $value->external_order_number;
                            $content .= '</td><td>';
                            $content .= count($value->sales_order_items) > 1 ? 'Multiple' : $value->sales_order_items[0]->line_item_details->name;
                            $content .= '</td>';
                            $content .= '</td><td>';
                            $content .= count($value->sales_order_items) > 1 ? 'Multiple' : $value->sales_order_items[0]->line_item_details->sku;
                            $content .= '</td>';
                            $content .= '</td><td>';
                            $content .=  date("m-d-Y", strtotime($value->order_date));
                            $content .= '</td><td>';
                            $content .= $isAssigned;
                            $content .= '</td>';
                            $content .= '<td > ' . $value->sales_order_id;
                            $content .= '</td>';
                            $content .= '<td> <a href="shipengine/order_details.php?id=' . $value->sales_order_id . '" target="_self"><i class="fa fa-eye" style="font-size:16px"></i></a>';

                            $content .= '</td>';
                            $content .= '</tr>';
                        }
                    }
                } else {
                    if ($filter_value !=  $value->sales_order_items[0]->requested_shipping_options->shipping_service) {
                        $product = new Product();
                        foreach ($value->sales_order_items as $key => $lineItems) {
                            $lineItemsDetails = $lineItems->line_item_details;
                            $resp = $product->moidAddProduct(
                                $lineItemsDetails->sku,
                                $lineItemsDetails->name,
                                $lineItemsDetails->weight->unit,
                                $lineItems->price_summary->estimated_tax->amount,
                                $lineItems->price_summary->unit_price->amount,
                                $lineItems->price_summary->unit_price->amount,
                                100,
                                $value->external_order_number,
                                $store_id,
                            );
                        }
                        if ($value->sales_order_status->fulfillment_status == 'unfulfilled') {
                            $lastOrderSourceId = $important->getLastOrderId();
                            $isOrderExists = $important->checkIfOrderExists($value->external_order_number, $value->sales_order_id);
                            if ($isOrderExists) {
                                $orderStatus =  $important->getOrderStatus($value->external_order_number, $value->sales_order_id);
                                if ($orderStatus == 'Fulfilled')
                                    continue;
                            } else {
                                $important->assignOrdersTORandom($value->external_order_number, $value->sales_order_id, $value->order_source->order_source_id);
                            }


                            $isAssigned = $important->checkOrderIsAssigned($value->sales_order_id) == true ? 'Assigned' : 'Not-Assigend';
                            $content .= '<tr class="">';
                            $content .= '<td>';
                            $content .= $value->external_order_number;
                            $content .= '</td><td>';
                            $content .= count($value->sales_order_items) > 1 ? 'Multiple' : $value->sales_order_items[0]->line_item_details->name;
                            $content .= '</td>';
                            $content .= '</td><td>';
                            $content .= count($value->sales_order_items) > 1 ? 'Multiple' : $value->sales_order_items[0]->line_item_details->sku;
                            $content .= '</td>';
                            $content .= '</td><td>';
                            $content .=  date("m-d-Y", strtotime($value->order_date));
                            $content .= '</td><td>';
                            $content .= $isAssigned;
                            $content .= '</td>';
                            $content .= '<td > ' . $value->sales_order_id;
                            $content .= '</td>';
                            $content .= '<td> <a href="shipengine/order_details.php?id=' . $value->sales_order_id . '" target="_self"><i class="fa fa-eye" style="font-size:16px"></i></a>';

                            $content .= '</td>';
                            $content .= '</tr>';
                        }
                    }
                }
            }
        }
    }
} else {
    foreach ($response->orders as $key => $value) {
        if ($value && !empty($value->items[0])) {
            $product = new Product();
            foreach ($value->items as $key => $lineItems) {
                $resp = $product->moidAddProduct(
                    $lineItems->sku,
                    $lineItems->name,
                    $lineItems->weight != null ? $lineItems->weight->units : "ounces",
                    $lineItems->taxAmount != null ? $lineItems->taxAmount : 0.0,
                    $lineItems->unitPrice,
                    $lineItems->unitPrice,
                    100,
                    $value->orderId,
                    $store_id,
                );
            }
            $lastOrderSourceId = $important->getLastOrderId();
            $isOrderExists = $important->checkIfOrderExists($value->orderNumber, $value->orderId);
            if ($isOrderExists) {
                $orderStatus =  $important->getOrderStatus($value->orderNumber, $value->orderId);
            } else {
                $important->assignOrdersTORandom($value->orderNumber, $value->orderId, $value->advancedOptions->storeId);
            }


            $isAssigned = $important->checkOrderIsAssigned($value->orderId) == true ? 'Assigned' : 'Not-Assigend';
            $content .= '<tr class="">';
            $content .= '<td>';
            $content .= $value->orderNumber;
            $content .= '</td><td>';
            $content .= count($value->items) > 1 ? 'Multiple' : mb_strimwidth($value->items[0]->name, 0, 80, '....');
            $content .= '</td>';
            $content .= '</td><td>';
            $content .= count($value->items) > 1 ? 'Multiple' : $value->items[0]->sku;
            $content .= '</td>';
            $content .= '</td><td>';
            $content .=  date("m-d-Y", strtotime($value->createDate));
            $content .= '</td><td>';
            $content .= $isAssigned;
            $content .= '</td>';
            $content .= '<td > ' . $value->orderId;
            $content .= '</td>';
            $content .= '<td> <a href="shipengine/order_details.php?id=' . $value->orderId . '" target="_self"><i class="fa fa-eye" style="font-size:16px"></i></a>';

            $content .= '</td>';
            $content .= '</tr>';
        }
    }
}


$page_title = 'Orders List'; //You can edit this to change your page title.


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Responsive Admin Dashboard Template">
    <meta name="keywords" content="admin,dashboard">
    <meta name="author" content="stacks">
    <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title -->
    <title><?php echo $page_title; ?></title>

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="../../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="../../assets/plugins/icomoon/style.css" rel="stylesheet">
    <link href="../../assets/plugins/uniform/css/default.css" rel="stylesheet" />
    <link href="../../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
    <link href="../../assets/plugins/nvd3/nv.d3.min.css" rel="stylesheet">
    <link href="../../assets/plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/plugins/datatables/css/jquery.datatables_themeroller.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />

    <!-- Theme Styles -->
    <link href="../../assets/css/space.min.css" rel="stylesheet">
    <link href="../../assets/css/custom.css" rel="stylesheet">

    <script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
    <script type="text/javascript" src="../../assets/js/export/tableExport.js"></script>
    <script type="text/javascript" src="../../assets/js/export/jquery.base64.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
</head>

<body class="page-sidebar-fixed page-header-fixed">


    <!-- Page Container -->
    <div class="page-container">
        <!-- Side Bar -->
        <?php require_once("includes/sidebar.php"); //including sidebar file. 
        ?>
        <!-- End Side Bar -->
        <!-- Page Content -->
        <div class="page-content">
            <!-- Header -->
            <?php require_once("includes/header.php"); //including sidebar file. 
            ?>
            <!-- End Header -->

            <!-- Page Inner -->
            <div class="page-inner">
                <div class="page-title">
                    <h3 class="breadcrumb-header"><?php echo $page_title; ?></h3>
                </div>
                <?php
                //display message if exist.
                if (isset($message) && $message != '') {
                    echo '<div class="alert alert-success">';
                    echo $message;
                    echo '</div>';
                }
                ?>


                <!-- <label style="margin-top:25px">Assign User</label> -->
                <!-- $user->getUsersForAssignOrders()  -->
                <!-- <button class="btn btn-primary" style="margin-top: 10px;" onclick="AssignUser()">Assigned User</button> -->


                <div class="row">
                    <div class="col-md-12">
                        <!-- <div class="panel panel-white"> -->
                        <!-- <div class="panel-body"> -->

                        <!-- <a href="reports/listCustomers.php" target="_blank" class="btn btn-info btn-addon"> <i class="fa fa-print"></i> Print Customer List</a>-->
                        <!-- <a class="btn btn-info btn-addon" onClick="$('#example3').tableExport({type:'excel',escape:'false'});"> <i class="fa fa-file-excel-o"></i> Export to CSV</a>  -->
                        <!-- </div> -->
                        <!-- </div> -->
                        </br>

                        <div class="panel-body" id="printlist">

                            <div class="table-responsive">
                                <table id="example3" class="display table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Item</th>
                                            <th>Item SKU</th>
                                            <th>Order Date</th>
                                            <th>Is Assigned</th>
                                            <th>Order Source Id</th>
                                            <th>Actions</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        echo $content;
                                        // $client->list_clients();

                                        ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                    </br>
                    </br>
                </div>
            </div>
        </div>
        <div class="page-footer">
            <?php
            require_once("includes/footer.php");
            ?>
        </div>

    </div>
    </div>

    </div>
    <!-- Javascripts -->

    <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
    <script src="../../assets/plugins/switchery/switchery.min.js"></script>
    <script src="../../assets/plugins/d3/d3.min.js"></script>
    <script src="../../assets/plugins/nvd3/nv.d3.min.js"></script>
    <script src="../../assets/plugins/flot/jquery.flot.min.js"></script>
    <script src="../../assets/plugins/flot/jquery.flot.time.min.js"></script>
    <script src="../../assets/plugins/flot/jquery.flot.symbol.min.js"></script>
    <script src="../../assets/plugins/flot/jquery.flot.resize.min.js"></script>
    <script src="../../assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="../../assets/plugins/flot/jquery.flot.pie.min.js"></script>
    <script src="../../assets/plugins/chartjs/chart.min.js"></script>
    <script src="../../assets/js/space.min.js"></script>
    <script src="../../assets/js/pages/dashboard.js"></script>
    <!-- Javascripts -->

    <script src="../../assets/plugins/datatables/js/jquery.datatables.min.js"></script>
    <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="../../assets/js/pages/table-data.js"></script>


    <script>
        $('#example3').dataTable({
            'iDisplayLength': 100,
            'select': {
                style: 'multi'
            },
            "order": [
                [0, "desc"]
            ]

        });

        $(document).ready(function() {
            var table = $('#example3').DataTable();

            $('#example3 tbody').on('click', 'tr', function() {
                $(this).toggleClass('selected');
            });

            $('#button').click(function() {
                var table = $('#example3').DataTable();
                console.log(table.rows('.selected').data());
                // alert(table.rows('.selected').data().length + ' row(s) selected');
            });
        });

        function AssignUser() {
            var table = $('#example3').DataTable();
            var list = [];
            var data = table.rows('.selected').data();
            var selectedUser = $('#assigned_user').val();
            if (data.length == 0) {
                alert('Please select the order first');
                return;
            } else {

                $.each(data, function(index, value) {
                    var obj = {
                        'user_id': selectedUser,
                        'order_source_id': value[6],
                        'order_no': value[0]
                    }
                    list.push(obj);
                });
                var params = {
                    myarray: list,

                };

                var paramJSON = JSON.stringify(params);

                // return;

                $.post(
                    'shipengine/assign_orders_ajax.php', {
                        data: paramJSON
                    },
                    function(data) {
                        // var result = JSON.parse(data);
                    }
                );
                // $.ajax({
                //     url: 'classes/functions.php?assigned=user',
                //     method: 'POST',
                //     data: paramJSON,
                //     contentType: 'application/json',
                //     success: function(data) {
                //         alert('data send successfully');
                //     },
                //     error: function(XMLHttpRequest, textStatus, errorThrown) {
                //         alert(errorThrown);
                //     }
                // });

            }

        }
    </script>
</body>