<?php
include('system_load.php');

//user Authentication.
authenticate_user('store_owner');

$user = new Users;
$important = new ImportantFunctions();
$user_id = $_SESSION['user_id'];
$function_id = $user->get_user_info($user_id, "user_function");


$page_title = 'Send Shipping';

if (isset($_POST['save'])) {
    extract($_POST);
    $message =  $important->sendShippingFromStore($product_sku, $product_sku_quantity, $carton_id, $trackingNo);
    //     // $message = $warehouse->add_inventory($quantity, '0', $product, $_SESSION['warehouse_id'], $lot);
    //     // HEADER('LOCATION: addstock.php?message=Stock Added !!');
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
    <link href="../../assets/plugins/x-editable/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
    <link href="../../assets/plugins/x-editable/inputs-ext/typeaheadjs/lib/typeahead.js-bootstrap.css" rel="stylesheet">
    <link href="../../assets/plugins/x-editable/inputs-ext/address/address.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Theme Styles -->
    <link href="../../assets/css/space.min.css" rel="stylesheet">
    <link href="../../assets/css/custom.css" rel="stylesheet">


    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />

</head>

<body class="page-sidebar-fixed page-header-fixed">
    <div class="page-container">
        <?php require_once("includes/sidebar_store.php"); //including sidebar file. 
        ?>
        <div class="page-content">
            <?php require_once("includes/header.php"); //including sidebar file. 
            ?>
            <div class="page-inner">
                <div class="page-title">
                    <h3 class="breadcrumb-header"><?php echo $page_title; ?></h3>
                </div>
                <div>

                    <?php
                    //display message if exist.
                    if (isset($_GET['message']) && $_GET['message'] != '') {
                        echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ';
                        echo $_GET['message'];
                        echo '</div>';
                    }
                    if (isset($message) && $message != '') {
                        echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ';
                        echo $message;
                        echo '</div>';
                    }



                    ?>

                </div>

                <div class="panel panel-white alert alert-default" style="font-size:14px;color:#0d47a1">
                    <div class="panel-heading clearfix">
                        <div class="panel-body">
                            <form action="<?php $_SERVER['PHP_SELF'] ?>" class="form-horizontal" method='POST' name='testform' id="#sendShippingForm">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Date</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="date" class="form-control" Readonly value="<?php echo date('Y-m-d'); ?>" />
                                    </div>
                                </div>


                                <!-- TODO: NEED TO ADD MULTIPLE SELECT OPTION -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">SKU</label>
                                    <div class="col-sm-6">
                                        <?php echo $important->getProducts() ?>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label class="col-sm-2 control-label">Product Name</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="product_name" id="product_name" class="form-control" readonly />
                                    </div>
                                </div> -->

                                <!-- <div class="form-group">
                                    <label class="col-sm-2 control-label">Quantity</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="quantity" id="quantity" class="form-control" placeholder="Enter Quantity" />
                                    </div>
                                </div> -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Carton Id</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="carton_id" id="carton_id" class="form-control" placeholder="Carton Id" />
                                    </div>
                                </div>
                                <!-- Tracking Code is Optional -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tracking#</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="trackingNo" id="trackingNo" class="form-control" placeholder="Tracking Id" />
                                    </div>
                                </div>




                                <div class="row">

                                    <div class="col-sm-3">
                                        <div class="well">
                                            <center>
                                                <Button type="submit" class="btn btn-info btn-addon" name="save" value="Save"> <i class="fa fa-save"></i> Send Shipping </Button>
                                            </center>
                                        </div>
                                    </div>
                                    <!--product_Detail_row ends here.-->
                            </form>


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
</body>
<script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
<script src="../../assets/plugins/switchery/switchery.min.js"></script>
<script src="../../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="../../assets/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="../../assets/js/space.min.js"></script>
<script src="../../assets/plugins/jquery-mockjax-master/jquery.mockjax.js"></script>
<script src="../../assets/plugins/moment/moment.js"></script>
<script src="../../assets/js/pages/form-wizard.js"></script>
<script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="../../assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script src="../../assets/plugins/x-editable/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="../../assets/plugins/x-editable/inputs-ext/typeaheadjs/lib/typeahead.js"></script>
<script src="../../assets/plugins/x-editable/inputs-ext/typeaheadjs/typeaheadjs.js"></script>
<script src="../../assets/plugins/x-editable/inputs-ext/address/address.js"></script>
<script src="../../assets/js/pages/form-x-editable.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    var selectedSku = [];
    $(document).ready(function() {
        $('#product_sku').select2();
    });

    $('#product_sku').change(function(e) {
        var text = prompt("Please enter quantity");
        selectedSku.push(text);

    });

    $('form').submit(function(e) {
        $("<input />").attr("type", "hidden")
            .attr("name", "product_sku_quantity")
            .attr("value", (JSON.stringify(selectedSku)))
            .appendTo("form");
        return true;
    });
</script>

</html>