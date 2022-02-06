<?php
include('system_load.php');
//This loads system.

//user Authentication.
authenticate_user('store_owner');
//creating company object.

$message = '';


$page_title = $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; //You can edit this to change your page title.


//display message if exist.

$important = new ImportantFunctions();
$product = new Product();

$totalOrdersCount = $important->getTotalOrdersCount();

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

    <!-- Theme Styles -->
    <link href="../../assets/css/space.min.css" rel="stylesheet">
    <link href="../../assets/css/custom.css" rel="stylesheet">

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
        <?php if (partial_access('store_owner')) require_once("includes/sidebar_store.php");
        else require_once("includes/sidebar.php"); //including sidebar file. 
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
                    <h3 class="breadcrumb-header">Dashboard - <?php echo $page_title;
                                                                echo ' ' . $_SESSION['order_source_id']; ?></h3>
                </div>
                <?php
                if ($message != '') {
                    echo '<div class="alert alert-danger" style="font-size:16px"><i class="fa fa-exclamation-circle"></i>';
                    echo $message;
                    echo '</div>';
                }
                ?>
                <div id="main-wrapper">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-white stats-widget dashed-primary">
                                <div class="panel-body">
                                    <div class="pull-left">
                                        <span class="stats-number" style="font-size:30px;color:#0d47a1"><?php echo $totalOrdersCount ?></span>
                                        <p class="stats-info" style="font-size:20px;color:#0d47a1">Orders </p>
                                    </div>
                                    <div class="pull-right">
                                        <i class="fa fa-barcode" style="font-size:48px;color:#0d47a1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-white stats-widget dashed-warning">
                                <div class="panel-body">
                                    <div class="pull-left">
                                        <span class="stats-number" style="font-size:30px;color:#FF8800"><?php $product->getStockAlertCount(); ?></span>
                                        <p class="stats-info" style="font-size:20px;color:#FF8800">Stock Alert</p>
                                    </div>
                                    <div class="pull-right">
                                        <i class="fa fa-minus-square" style="font-size:48px;color:#FF8800"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-white stats-widget dashed-danger">
                                <div class="panel-body">
                                    <div class="pull-left">
                                        <span class="stats-number" style="font-size:30px;color:#CC0000"><?php $product->getOutOfStockCount(); ?></span>
                                        <p class="stats-info" style="font-size:20px;color:#CC0000">Out Of Stock</p>
                                    </div>
                                    <div class="pull-right">
                                        <i class="fa fa-warning" style="font-size:48px;color:#CC0000"></i>
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <a href="shipping_alert.php">
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-white stats-widget dashed-primary">
                                    <div class="panel-body">
                                        <div class="pull-left">
                                            <span class="stats-number" style="font-size:30px;color:black"><?php echo count($important->getStoreShippingAlerts($_SESSION['order_source_id'])); ?></span>
                                            <p class="stats-info" style="font-size:20px;color:black">Shipping Alert</p>
                                        </div>
                                        <div class="pull-right">
                                            <i class="fa fa-truck" style="font-size:48px;color:black"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>

                    </div><!-- Row -->
                    <div class="row">
                        <hr width="50%">
                    </div>

                    <!-- <div class="row pull-left">
                        Stock Alert
                        <div class="col-md-12">
                             $product->list_product_alert_shipengine();
                            
                        </div>
                    </div> -->



                </div><!-- Main Wrapper -->
                <div class="page-footer">
                    <?php
                    require_once("includes/footer.php");
                    ?>
                </div>
            </div><!-- /Page Inner -->

        </div><!-- /Page Content -->
    </div><!-- /Page Container -->


    <!-- Javascripts -->
    <script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
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
</body>

</html>