<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$warehouse = new Warehouse;
	$warehouseaccess = new WarehouseAccess;
	$delivery = new Delivery;
	$transfer = new Transfer;
	
	if($_SESSION['user_type'] != "admin") {	
		if( $warehouse_access->have_module_access('products') OR $function_id!='storem' OR $function_id!='manager' )  {
			HEADER('LOCATION: warehouse.php?msg=nwsret');
		}
	}
	
	if ( isset ($_POST['reference']) && $_POST['reference'] <> "") {
		if($_POST['returntype']=='delivery') {
			if ($delivery->delivery_exist($_POST['reference']) ) {
				HEADER('LOCATION: returndelivery.php?did='.$_POST['reference'].'');
			}
			else{
				$message ='<div class="alert alert-danger"> There is No Delivery with such Number: <b>'.$_POST['reference'].'</b></div>';
			}
		}
		elseif ($_POST['returntype']=='transfer') {
			if ($transfer->transfer_exist($_POST['reference']) ) {
				HEADER('LOCATION: returntransfer.php?tid='.$_POST['reference'].'');
			}
			else{
				$message ='<div class="alert alert-danger"> There is No Transfer with such Number: <b>'.$_POST['reference'].'</b></div>';
			}
		}
	}
	
	$warehouse->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'New Stock Return'; //You can edit this to change your page title.
	//require_once("includes/header.php"); //including header file.
	?>
	<style type="text/css">
textarea:hover, textarea:focus, #items td.total-value textarea:hover, #items td.total-value textarea:focus, .delme:hover { background-color:#EEFF88; }

#items input[type=text] {width:60px;border:0px;}
.delete-wpr { position: relative; }
.delme { display: block; color: #000; text-decoration: none; position: absolute; background: #EEEEEE; font-weight: bold; padding: 0px 3px; border: 1px solid; top: -6px; left: -22px; font-family: Verdana; font-size: 12px; }

input:focus
	{
	background-color:#FFFACD;
	}
	select:focus
	{
	background-color:#FFFACD;
	}
</style>  
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
							<div class="panel panel-white alert alert-default" >
								<div class="panel-heading clearfix">
								<div class="panel-body" >
									 
									 <form class="form-horizontal" action="newreturn.php" id="wizardForm" name="level" method="post">
										<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
										Please select the Type of Return and the Reference Number then submit to get Details
										</div>
										<div class="form-group">
													<label class="col-sm-2 control-label">Return Type :</label>
													<div class="col-sm-6">
													<select name="returntype" id="returntype" class="form-control" style="width:100%" Required >
															<option value=""></option>
															<option value="delivery" <?php  if(isset($_POST['returntype']) && $_POST['returntype'] == 'delivery') { echo 'selected="selected"'; } ?> > Returned Delivery</option>
															<option value="transfer" <?php  if(isset($_POST['returntype']) && $_POST['returntype'] == 'transfer') { echo 'selected="selected"'; } ?> > Returned Transfer</option>
															
													</select>
													</div>
										</div>
										<div class="form-group">
													<label class="col-sm-2 control-label">Transfer / Delivery N# :</label>
													<div class="col-sm-2">
													<input type="text" class="form-control" name="reference"  placeholder="N #" value="<?php if (isset($_POST['reference'])) {echo $_POST['reference'];} ?>" required="required" />
													
													</div>
													<!--<a class="btn btn-default btn-xs" data-toggle="modal" href=""><i class="fa fa-info" aria-hidden="true"></i> Get Details</a>-->
													
											</div>
									 
										
										
										<div align="center"><button type="submit" class="btn btn-info btn-addon"><i class="fa fa-search"></i> Submit & Find Details</button></div>
									</form>

										
									
	<!--content here-->
									
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