<?php
include('system_load.php');
//This loads system.
//user Authentication.
// authenticate_user('subscriber');   

$user_id = $_SESSION['user_id'];
$function_id = $user->get_user_info($user_id, "user_function");
$content = '';
if ($_SESSION['user_type'] != "admin") {
    if ($function_id != 'storem' or $function_id != 'manager') {
        HEADER('LOCATION: warehouse.php?msg=lstcust');
    }
}

$important = new ImportantFunctions();

$response = $important->getFilters();
if ($response != false) {
    while ($row = $response->fetch_assoc()) {
        extract($row);
        if ($active) {
            $content .= '<tr class="">';
            $content .= '<td>';
            $content .= $id;
            $content .= '</td><td>';
            $content .= $store_name;
            $content .= '</td><td>';
            $content .= $filter_name;
            $content .= '</td>';
            $content .= '</td>';
            $content .= '</td><td>';
            $content .= $filter_sign;
            $content .= '</td><td>';
            $content .= $filter_value;
            $content .= '</td>';
            $content .= '</td><td>';
            $content .= '<button id="deleteFilter" value=' . $id . ' class="btn btn-danger" onclick="deleteFilter(this.value)" >Delete Filter</button>';
            $content .= '</td>';
        }
        $content .= '</tr>';
    }
}





// var_dump($content);
// return;
// if (!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') {
//     HEADER('LOCATION: warehouses.php?message=1');
// }

//$warehouses->set_warehouse($_SESSION['warehouse_id']); //setting store.

$page_title = 'Create Filters'; //You can edit this to change your page title.



// FROM MADDY




// TILL MADDY

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
                    echo '<div id="alertSuccess" class="alert alert-success">';
                    echo $message;
                    echo '</div>';
                }
                ?>



                <div class="row">
                    <div class="col-md-12">
                        <!-- <div class="panel panel-white"> -->




                        <!-- <div class="panel-body"> -->

                        <!-- <a href="reports/listCustomers.php" target="_blank" class="btn btn-info btn-addon"> <i class="fa fa-print"></i> Print Customer List</a>
                                        <a class="btn btn-info btn-addon" onClick="$('#example3').tableExport({type:'excel',escape:'false'});"> <i class="fa fa-file-excel-o"></i> Export to CSV</a> -->
                        <!-- </div> -->
                        <!-- </div> -->
                        </br>

                        <div class="panel-body" id="printlist">
                            <p>Please select the customer</p>
                            <select name="customer" id="customer" class="form-control">
                                <?php $important->getOrderSourcesOptions(); ?>
                            </select>

                            <div class="row mt-5" style="margin-top: 10px;">
                                <div class="col-md-4">
                                    <label for="filter_name">Select Filter</label>
                                    <select name="filter_name" class="form-control" id="filter_name">
                                        <option value="">Select Filter</option>
                                        <option value="country">Country</option>
                                        <option value="sku">SKU</option>
                                        <option value="shipping_service">Shipping Service</option>
                                    </select>
                                </div>


                                <div class="col-md-4">
                                    <label for="filter_sign">Select Sign</label>
                                    <select name="filter_sign" class="form-control" id="filter_sign">
                                        <option value="">Select Sign</option>
                                        <option value="==">Equal</option>
                                        <option value="!=">Not Equal</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="filter_value">Select Value</label>
                                    <select name="filter_value" class="form-control" id="filter_value">
                                        <option value="">Select Value</option>
                                    </select>
                                </div>


                            </div>
                            <div style="width: 100%;;">
                                <button style="float:right;margin-top:20px" class="btn btn-secondary" id="createFilterBtn">Create Filter</button>
                            </div>
                            <div class="table-responsive mt-3" style="margin-top:150px">
                                <table id="example3" class="display table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Order Source Name</th>
                                            <th>Filter Name</th>
                                            <th>Filter Sign</th>
                                            <th>Filter Value</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        echo $content;
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
            paging: false
        });

        var countryOptions = {
            'usa': 'US',
            'ca': 'CA'
        }
        var skuOptions = {
            'Blackout-9-10': 'Blackout-9-10',
            'Storm-9-10': 'Storm-9-10',
            'BlackBandanna-9-10': 'BlackBandanna-9-10'
        }

        var shippingOptions = {
            'USA Domestic Shipping (3-5 Days)': 'USA Domestic Shipping (3-5 Days)',
            'Free USA Domestic Shipping (3-5 Days)': 'Free USA Domestic Shipping (3-5 Days)',
            'No Rush - Overseas Warehouse (3-4 weeks)': 'No Rush - Overseas Warehouse (3-4 weeks)',
            'Express Shipping (1 - 5 business days)': 'Express Shipping (1 - 5 business days)'
        }

        $('#filter_name').on('change', function() {
            var $el = $("#filter_value");
            if (this.value === 'country') {
                $el.empty();
                $.each(countryOptions, function(key, value) {
                    $el.append($("<option></option>")
                        .attr("value", value).text(key));
                });
            } else if (this.value === 'sku') {
                $el.empty();
                $.each(skuOptions, function(key, value) {
                    $el.append($("<option></option>")
                        .attr("value", value).text(key));
                });
            } else if (this.value === 'shipping_service') {
                $el.empty();
                $.each(shippingOptions, function(key, value) {
                    $el.append($("<option></option>")
                        .attr("value", value).text(key));
                });
            }
        });

        $('#createFilterBtn').click(function(e) {
            e.preventDefault();
            this.disabled = true;
            this.innerText = 'Sending…';
            paramJSON = {
                'filter_name': $('#filter_name :selected').val(),
                'filter_sign': $('#filter_sign :selected').val(),
                'filter_value': $('#filter_value :selected').val(),
                'store_id': $('#customer :selected').val(),
                'store_name': $('#customer :selected').text()
            }
            $.post(
                'shipengine/create_filters_ajax.php', {
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
        });

        function deleteFilter($id) {
            $('#deleteFilter').disabled = true;
            $('#deleteFilter').innerText = "Deleting";
            paramJSON = {
                'id': $id,
            }
            $.post(
                'shipengine/delete_filters.php', {
                    data: JSON.stringify(paramJSON),
                },
                function(data) {

                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
            );
        }
    </script>

</body>