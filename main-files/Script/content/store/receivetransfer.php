<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$warehouse = new Warehouse;
	$warehouse_access = new WarehouseAccess;
	$transfer = new Transfer;
	$note = new Notes;
	@$tid = $_GET["tid"];
	//echo $store_id;
	//$q = $_POST['qty_app'];
	if (isset($_POST['nb'])){
	$nb = $_POST['nb'];
	//echo $nb;
	}
	
	if(partial_access('admin') || $store_access->have_module_access('transfer')) {} else { 
		HEADER('LOCATION: warehouse.php?message=warehouse');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} //select company redirect ends here.

	$warehouse->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'Transfert receiving : Transfert N#: '.$tid; //You can edit this to change your page title.
	//require_once("includes/header.php"); //including header file.
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
							<div class="panel panel-white alert alert-default" style="font-size:16px" >
								<div class="panel-body" >
									<form class="form-horizontal" action="" id="wizardForm" name="level" method="post">
									<div class="form-group">
									
										<label class="col-sm-2 control-label">Transfert N# :</label>
										<div class="col-sm-2">
											<input type="text" class="form-control" name="did"   Value="<?php echo $tid; ?>" Disabled />
													
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Edition Date :</label>
										<div class="col-sm-2">
											<input type="text" class="form-control" name="datetime"   Value="<?php  $datedelivery = $transfer->get_transfer_info($tid,'datetime'); $datedelivery = strtotime($datedelivery); echo date('d-m-Y', $datedelivery);  ?>" Disabled />
													
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Created By :</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="dateissued"   Value="<?php  $user_id= $transfer->get_transfer_info($tid,'agent_id'); $user_name = $user->get_user_info($user_id,'first_name').' '.$user->get_user_info($user_id,'last_name') ; echo $user_name;  ?>" Disabled />
													
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
																	$destination_warehouse = $_SESSION['warehouse_id'];
																	
																	$datetime = date('Y-m-d H:i:s');
																	/* 1- for each row, insert the approved data in the database */
																	$transfer->add_transfer_reception($tid, $datetime, $pid, $qty, $qtyapp);
																	// 2- Add new product to the destination warehouse
																	$new_product = $transfer->add_product_to_destination_warehouse($pid, $destination_warehouse);	
																	// 3- Decrease the quantity of the source warehouse and put it on hold until the reception is approved
																	$transfer->add_inventory($qtyapp, '0', $new_product, $tid);	
																}
																//   3- mettre le champs 'received' dans la table "transfert" a 1 au lieu de zero ( il faut tout d'abord creer le champs )
																$transfer->receive_transfer($tid);
																$note_title = 'New Transfer Received';
			                                                    $note_details = 'New Transfer #: '.$tid.' received recently.';
			                                                    $note->add_note($note_title, $note_details );
																//   4- afficher un message succes etse rediriger vers la page "transfer"
																echo '<div class="alert alert-success">';
																echo 'Transfer N#: '.$tid.' received and saved successfully !!';
																echo '</div>';
																//   5- Redirection
																echo '<script>window.location="transferreceived.php"</script>';
																
																
														}
														
														
													?>
	
									<form action="receivetransfer.php?tid=<?php echo $tid; ?>" class="form-horizontal" method='POST' name='testform' >
										<table cellpadding="0" cellspacing="0" border="0" class="table-responsive table-hover table display table-bordered" id="wc_table" width="100%">
											<thead>
												<tr>
													<th bgcolor=gray><font color="#fff">Product Code</th>
													<th bgcolor=gray><font color="#fff">Product Name</th>
													<th bgcolor=gray><font color="#fff">Quantity Sent</th>
													<th bgcolor=gray><font color="#fff">Quantity Received </th>
													
												</tr>
											</thead>
											<tbody>
												<?php $transfer->receive_transfer_details($tid); ?>
											</tbody>
											
										</table> 
										<hr>
										<div align="center"><button type="submit" class="btn btn-info btn-addon"><i class="fa fa-check-circle-o" ></i> Submit Receiving</button></div>
									</form>
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
		<!-- Javascripts
        
        <script src="../../assets/plugins/datatables/js/jquery.datatables.min.js"></script>
        <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script src="../../assets/js/pages/table-data.js"></script> -->
	</body>
</html>
							