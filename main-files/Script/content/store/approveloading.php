<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$warehouse = new Warehouse;
	$warehouseaccess = new WarehouseAccess;
	$delivery = new delivery;
	
	if (isset($_POST['approve_loading'])){
	$did = $_POST['approve_loading'];
	} elseif (isset($_GET['appr'])) {
		if ($_GET['appr']=='1'){
			$did = $_GET['did'];
		}
	
	}
	$warehouse->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'Approve Loading / DELIVERY N#: '.$did; //You can edit this to change your page title.
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
							
							echo $message;
							
						}
					?>
					<div class="row" >
						<div class="col-md-12">
							<div class="panel panel-white" >
								<div class="panel-body" >
									<form class="form-horizontal" action="" id="wizardForm" name="level" method="post">
										<div class="form-group">
										
											<label class="col-sm-2 control-label">Delivery N# :</label>
											<div class="col-sm-2">
												<input type="text" class="form-control" name="did"   Value="<?php echo $did; ?>" Disabled />
														
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label">Edition Date :</label>
											<div class="col-sm-2">
												<input type="text" class="form-control" name="datetime"   Value="<?php  $deliverydate = $delivery->get_delivery_info($did,'datetime'); $deliverydate = strtotime($deliverydate); echo date('d/m/Y', $deliverydate);  ?>" Disabled />
														
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label">Estimed Delivery Date :</label>
											<div class="col-sm-2">
												<input type="text" class="form-control" name="dateissued"   Value=" <?php  $issueddate = $delivery->get_delivery_info($did,'dateissued'); $issueddate = strtotime($issueddate); echo date('d/m/Y', $issueddate);  ?>" Disabled />
														
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label">Created By :</label>
											<div class="col-sm-8">
												<input type="text" class="form-control" name="username"   Value="<?php  $user_id= $delivery->get_delivery_info($did,'user_id'); $user_name = $user->get_user_info($user_id,'first_name').' '.$user->get_user_info($user_id,'last_name') ; echo $user_name;  ?>" Disabled />
														
											</div>
										</div>
									</form>
									
										<?php
										
											if (isset($_POST['nb'])){
													$nb= $_POST['nb'];
													for ($i = 0; $i < $nb; $i++) {
														$pid = $_POST['pid'][$i];
														$qty = $_POST['qty'][$i];
														$pname = $_POST['pname'][$i];
														$qtyapp = $_POST['qte_appr'][$i];
														$dateappr = date('Y-m-d');
														$delivery->add_loading_approve($did, $dateappr, $pid, $qty, $qtyapp);
														$delivery->add_inventory($dateappr,'0', $qtyapp, $pid, $did);
													}
													$delivery->update_loading_delivery($did);
													echo '<div class="alert alert-success">';
													echo 'Loading of Delivery N#: '.$did.' is successfully approved';
													echo '</div>';
													HEADER('LOCATION: deliveries.php');
													
													
											}
											
											
										?>
									
	<!--content here-->
									<form action="approveloading.php?appr=1&did=<?php echo $did; ?>" class="form-horizontal" method='POST' name='testform'>
	
										<table cellpadding="0" cellspacing="0" border="0" class="table-responsive table-hover table display table-bordered" id="example3" width="100%">
											<thead>
												<tr>
													<th bgcolor=gray><font color="#fff">Product Code</th>
													<th bgcolor=gray><font color="#fff">Product Name</th>
													<th bgcolor=gray><font color="#fff">Quantity Ordered</th>
													<th bgcolor=gray><font color="#fff">Quantity Approved </th>
													
												</tr>
											</thead>
											<tbody>
												<?php $delivery->approve_loading_details($did); ?>
											</tbody>
											
										</table> 
										
										<div align="center"><button type="submit" class="btn btn-info btn-addon"><i class="fa fa-save"></i> Approve and Save</button></div>
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
		<!-- Javascripts
        
        
	</body>
</html>