<?php
include('system_load.php');
//This loads system.
//user Authentication.
authenticate_user('store_owner');

$user_id = $_SESSION['user_id'];
$important = new ImportantFunctions();
$label_id = null;
$order_no = null;
$orderData = null;
$orderFromLocal = null;
$page_title = 'Track Order'; //You can edit this to change your page title.
$istracking = false;
$orderNotShipped = false;

$events = '';
if (isset($_POST['order_no'])) {
    $orderNo = $_POST['order_no'];
    $orderResponse =   $important->getOrderTrackingStatus($orderNo);
    $orderFromLocal = $important->getOrderForShipengine($orderNo);
    // $orderID = $orderFromLocal['order_id'];
    $orderData = $important->getOrderDataThroughOrderIDShipengin($orderNo);
    if ($orderResponse != null) { //ORDER HAS BEEN SHIPPED
        $response =   $important->CallAPI('GET', '/v1/labels/' . $orderResponse['label_id'] . '/track');
        if ($response == null) {
            $message = 'Sorry, label not found';
        } else {
            $istracking = true;
            $label_id = $orderResponse['label_id'];
            $order_no = $orderResponse['order_no'];
            foreach ($response->events as $key => $value) {
                $events .= '<div class="col-md-12 alert alert-success bg-success">';
                $events .= '<div class="">';
                $events .= $value->description;
                $events .= '</div>';
                $events .= '<div class="pull-right">';
                $events .= date_format(date_create($value->occurred_at), 'Y/m/d H:i:s');
                $events .= '</div>';
                $events .= '</div>';
            }
        }
    } elseif ($orderData != null) {
        $orderNotShipped = true;
    } else {
        $message = 'Sorry, Order Not found';

    }
}

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
        <?php require_once("includes/sidebar_store.php"); //including sidebar file. 
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
                    echo '<div class="alert alert-danger">';
                    echo $message;
                    echo '</div>';
                }
                ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <form <?php $_SERVER['PHP_SELF'] ?> class="" role="form" method="POST">
                                <div class="form-group">
                                    <input type="text" id="order_no" name="order_no" class="form-control" placeholder="Enter Order No" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="form-control">
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php if ($istracking) { ?>
                        <div class="col-md-12 text-center" style="margin-bottom: 5px;">
                            <div class="trackingDetails">
                                <h3 class="text-center">Tracking Details</h3>
                                <div class="col-md-4">
                                    <div class="trackingNo ">
                                        <h5>Tracking# <?php echo $response->tracking_number; ?></h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="trackingNo ">
                                        <h5><span class="text-danger">Fulfilment:</span> <?php echo strtoupper($orderFromLocal['status']); ?></h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="trackingNo">
                                        <h5>Status: <?php echo $response->status_description; ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <div class="trackingDetails">
                                <div class="trackingNo pull-left">
                                    <h5>Label# <?php echo $label_id; ?></h5>
                                </div>
                                <div class="trackingNo pull-right">
                                    <h5>Order No# <?php echo $order_no; ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5" style="margin-top: 10px;"></div>
                        <div class="col-md-12 ">
                            <div class="carrier-description text-left">
                                <h5>Carrier Description</h5>
                            </div>
                            <p><?php echo $response->carrier_status_description; ?></p>

                        </div>
                        <!-- <div class="col-md-6">
                            <div class="carrier-description text-right">
                                <h5>Carrier Code</h5>
                            </div>
                            <p class="text-right"><?php echo $response->carrier_code; ?></p>
                        </div> -->

                        <br>
                        <div class="col-md-12">
                            <h4>Latest Events</h4>
                        </div>
                        <?php echo $events; ?>
                    <?php } ?>

                    <?php if ($orderNotShipped) { ?>
                        <div class="col-md-12 text-center" style="margin-bottom: 5px;">
                            <div class="trackingDetails">
                                <h3 class="text-center">Tracking Details</h3>
                                <div class="col-md-4">
                                    <div class="trackingNo ">
                                        <h5>Order# <?php echo $orderNo; ?></h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="trackingNo ">
                                        <h5><span class="text-danger">Fulfilment:</span> <?php echo strtoupper($orderFromLocal['status'] ?? 'Not-Assigned') ; ?></h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="trackingNo">
                                        <h5>Status: <?php echo strtoupper($orderFromLocal['status'] ?? 'Not-Assigned'); ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

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
    <!-- Javascripts -->

    <script src="../../assets/plugins/datatables/js/jquery.datatables.min.js"></script>
    <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="../../assets/js/pages/table-data.js"></script>


</body>