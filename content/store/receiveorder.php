<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	$warehouse = new Warehouse;
	$warehouse_access = new WarehouseAccess;
	$order = new Order;
	$note = new Notes;
	@$oid = $_GET["oid"];
	if (isset($_POST['nb'])){
	$nb = $_POST['nb'];
	//echo $nb;
	}
	
	if(partial_access('admin') || $store_access->have_module_access('orders')) {} else { 
		HEADER('LOCATION: warehouse.php?message=warehouse');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} //select company redirect ends here.
	
	$warehouse->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'Reception of Purshasing Order N# : '.$oid; //You can edit this to change your page title.
	
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
        <link href="../../assets/plugins/uniform/css/default.css" rel="stylesheet"/>
        <link href="../../assets/plugins/switchery/switchery.min.css" rel="stylesheet"/>
        <link href="../../assets/plugins/nvd3/nv.d3.min.css" rel="stylesheet">
		<link href="../../assets/plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css"/>	
        <link href="../../assets/plugins/datatables/css/jquery.datatables_themeroller.css" rel="stylesheet" type="text/css"/>	
        <link href="../../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css"/>
		 <link href="../../assets/plugins/x-editable/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
        <link href="../../assets/plugins/x-editable/inputs-ext/typeaheadjs/lib/typeahead.js-bootstrap.css" rel="stylesheet">
        <link href="../../assets/plugins/x-editable/inputs-ext/address/address.css" rel="stylesheet">
      
        <!-- Theme Styles -->
        <link href="../../assets/css/space.min.css" rel="stylesheet">
        <link href="../../assets/css/custom.css" rel="stylesheet">
		
		  <script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
	<body class="page-sidebar-fixed page-header-fixed">
		<div class="page-container">
			<?php require_once("includes/sidebar.php"); //including sidebar file. ?>
            <div class="page-content">
				<?php require_once("includes/header.php"); //including sidebar file. ?>
                <!-- Page Inner -->
				<div class="page-inner">
					<div class="page-title">
                        <h3 class="breadcrumb-header"><?php echo $page_title; ?></h3>
					</div>
					<?php
						//display message if exist.
						if(isset($message) && $message != '') { 
							echo '<div class="alert alert-success">';
							echo $message;
							echo '</div>';
						}
					?>
					<div class="row" >
						<div class="col-md-12">
							<div class="panel panel-white" >
								<div class="panel-body" >
									<form class="form-horizontal" action="" id="wizardForm" name="level" method="post">
										<div class="form-group">
										
											<label class="col-sm-2 control-label">Order N# :</label>
											<div class="col-sm-2">
												<input type="text" class="form-control" name="did"   Value="<?php echo $oid; ?>" Disabled />
														
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label">Order Date :</label>
											<div class="col-sm-2">
												<input type="text" class="form-control" name="datetime"   Value="<?php  $orderdate = $order->get_order_info($oid,'datetime'); $orderdate = strtotime($orderdate); echo date('d-m-Y', $orderdate);  ?>" Disabled />
														
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label">Estimed Delivery Date :</label>
											<div class="col-sm-2">
												<input type="text" class="form-control" name="dateissued"   Value=" <?php  $deliverydate = $order->get_order_info($oid,'deliverydate'); $deliverydate = strtotime($deliverydate); echo date('d-m-Y', $deliverydate);  ?>" Disabled />
														
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label">Created By :</label>
											<div class="col-sm-8">
												<input type="text" class="form-control" name="username"   Value="<?php  $user_id= $order->get_order_info($oid,'agent_id'); $user_name = $user->get_user_info($user_id,'first_name').' '.$user->get_user_info($user_id,'last_name') ; echo $user_name;  ?>" Disabled />
														
											</div>
										</div>
										
									</form>
									
													<?php
													//display message if exist.
														if(isset($message) && $message != '') { 
															echo '<div class="alert alert-success">';
															echo $message;
															echo '</div>';
														}
														if (isset($_POST['nb'])){
																for ($i = 0; $i < $nb; $i++) {
																	$pid = $_POST['pid'][$i];
																	$qty = $_POST['qty'][$i];
																	$pname = $_POST['pname'][$i];
																	$qtyapp = $_POST['qte_appr'][$i];
																	$datetime = date('Y-m-d H:i:s');
																	/* 1- FOR EACH ROW, INSERT THE APPROVED DATA INTO THE DATABASE. */
																	$order->add_order_reception($oid, $datetime, $pid, $qty, $qtyapp);
																	/* 2- ADD THE QUANTITY RECEIVED TO THE WAREHOUSE */
																	$order->add_inventory($qtyapp, '0', $pid, $_SESSION['warehouse_id'], $oid);	
																}
																/*   3- PUT THE 'RECEIVED' FIELD IN THE "ORDERS" TABLE TO 1 INSTEAD OF ZERO */
																$order->receive_order($oid);
																/* 4- ADD NOTIFICATION */
																$note_title = 'New Order Received';
			                                                    $note_details = 'New Order received recently.';
			                                                    $note->add_note($note_title, $note_details );
																echo '<div class="alert alert-success">';
																echo 'Order N#: '.$oid.' received and saved successfully !!';
																echo '</div>';
																//   5- Redirection
																echo '<script>window.location="orders.php"</script>';
														}
													?>
	
									<form action="receiveorder.php?oid=<?php echo $oid; ?>" class="form-horizontal" method='POST' name='testform' >
										<table cellpadding="0" cellspacing="0" border="0" class="table-responsive table-hover table display table-bordered" id="wc_table" width="100%">
											<thead>
												<tr>
													<th bgcolor=gray><font color="#fff">Product Code</th>
													<th bgcolor=gray><font color="#fff">Product Name</th>
													<th bgcolor=gray><font color="#fff">Quantity Requested</th>
													<th bgcolor=gray><font color="#fff">Quantity Received </th>
													
												</tr>
											</thead>
											<tbody>
												<?php $order->receive_order_details($oid); ?>
											</tbody>
											
										</table> 
										<hr>
										<div align="center"><button type="submit" class="btn btn-info btn-addon"><i class="fa fa-save"></i> Submit Reception</button></div>
									</form>
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
		
	</body>
</html>
							