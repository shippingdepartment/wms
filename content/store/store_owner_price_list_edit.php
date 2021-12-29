<?php
include('system_load.php');
//This loads system.
//user Authentication.
// authenticate_user('store_owner');


// $user_id = $_SESSION['user_id'];
// $function_id = $user->get_user_info($user_id, "user_function");

if (partial_access('admin')) {
} else {
    HEADER('LOCATION: warehouse.php?message=products');
}

if (!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') {
    HEADER('LOCATION: warehouses.php?message=1');
} //select company redirect ends here.


$page_title = 'Store Pricing List'; //You can edit this to change your page title.
$important = new ImportantFunctions();
$priceData = null;
if (isset($_GET['id']) && $_GET['id'] != '') {
    $pricingId = $_GET['id'];

    $priceData = $important->getStorePrices($pricingId);
}

if (isset($_POST['shouldSave']) && $_POST['shouldSave'] == '1') {
    $firstItemPrice = $_POST['first_item_price'];
    $eachItemPrice = $_POST['each_item_price'];

    $isUpdated =  $important->editStorePrice($firstItemPrice, $eachItemPrice, $pricingId);
    if ($isUpdated) {
        $message = 'Price edited successfully';
    } else {
        $message = 'Some error occured';
    }
    $priceData = $important->getStorePrices($pricingId);
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

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <div class="panel-body">
                                <form class="form-horizontal" action="<?php $_SERVER['PHP_SELF'] ?>" id="wizardForm" name="level" method="post">
                                    <input type="hidden" value="1" name="shouldSave">
                                    <div class="form-group">
                                        <div class="col-sm-10">
                                            <label for="first_item_price">First Item Price ($)</label>
                                            <input type="text" class="form-control" name="first_item_price" placeholder="First Item Price" value="<?php echo $priceData['first_item_price'] ?>" required="required" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-10">
                                            <label for="each_item_price">Each Item Price ($)</label>
                                            <input class="form-control" placeholder="Each Item Price" name="each_item_price" value="<?php echo $priceData['each_item_price']; ?>" />
                                        </div>
                                    </div>

                                    <input type="submit" name="" class="btn btn-success" id="">
                            </div>

                            </form>
                        </div>
                    </div>
                </div>
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