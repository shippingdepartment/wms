<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('utilisateur');
	//creating company object.
	$new_store = new Store;
	$warehouse_access = new warehouseAccess;
	$warehouse = new Warehouse;
	if (isset($_GET['w'])){
		$w = $_GET['w'];
	}
	else {
		HEADER('LOCATION: warehouses.php');
	}
	
	if(isset($_POST['update_warehouse'])) { 
		extract($_POST);
		$message = $warehouse->update_warehouse($w, $name_warh, $address, $city, $state, $country, $manager_name, $manager_phone);
	}//update ends here.
	
	//setting data if updating or editing.
	if(isset($w)) {
		$warehouse->set_warehouse($w);	
	} //level set ends here
	
	 $page_title = 'Edit Warehouse'; //You can edit this to change your page title.
	
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
			<?php require_once("includes/sidebar.php"); //including sidebar file. ?>
			<!-- End Side Bar 
            <!-- Page Content -->
            <div class="page-content">
				<!-- Header -->
				<?php require_once("includes/header.php"); //including sidebar file. ?>
				<!-- End Header -->
				
                <!-- Page Inner -->
				<div class="page-inner">
					<div class="page-title">
							<h3 class="breadcrumb-header"><?php echo $page_title; ?></h3>
					</div>
				
				<?php
					//display message if exist.
						if(isset($message) && $message != '') { 
							echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ';
							echo $message;
							echo '</div>';
						}
				?>
					<div class="panel panel-white alert alert-default">
						<div class="panel-heading clearfix">
							<div class="panel-body" alert alert-default >
								<form  id="wizardForm" class="form-horizontal"  action ="<?php $_SERVER['PHP_SELF']; ?>" method="POST" >
									
													<label for="input-Default" class="col-sm-3 control-label">Warehouse Name:</label>
													<div class="col-sm-9">
													<div class="form-group">
                                                        <input type="text" id="name-input" name="name_warh" class="form-control" placeholder="Name" value="<?php echo $warehouse->name; ?>"  required>
                                                    </div>
													</div>
													<label for="input-Default" class="col-sm-3 control-label">Warehouse Address:</label>
													<div class="col-sm-9">
                                                    <div class="form-group">
                                                        <input type="text" id="address-input" name="address" class="form-control" placeholder="Address (Street / Bloc )" value="<?php echo $warehouse->address; ?>" required>
                                                    </div>
													</div>
													<label for="input-Default" class="col-sm-3 control-label">City:</label>
													<div class="col-sm-9">
													<div class="form-group">
                                                        <input type="text" id="city-input" name="city" class="form-control" placeholder="City" value="<?php echo $warehouse->city; ?>" required>
                                                    </div>
													</div>
													<label for="input-Default" class="col-sm-3 control-label">State:</label>
													<div class="col-sm-9">
													<div class="form-group">
                                                        <input type="text" id="state-input" name="state" class="form-control" placeholder="State" value="<?php echo $warehouse->state; ?>" required>
                                                    </div>
													</div>
													<label for="input-Default" class="col-sm-3 control-label">Country:</label>
													<div class="col-sm-9">
													<div class="form-group">
                                                        <input type="text" id="country-input" name="country" class="form-control" placeholder="Country" value="<?php echo $warehouse->country; ?>" required>
                                                    </div>
													</div>
													<label for="input-Default" class="col-sm-3 control-label">Warehouse Manager:</label>
													<div class="col-sm-9">
													<div class="form-group">
                                                        <input type="text" id="manager-input" name="manager_name" class="form-control" placeholder="Manager Name" value="<?php echo $warehouse->manager; ?>" required>
                                                    </div>
													</div>
													<label for="input-Default" class="col-sm-3 control-label">Manager Phone:</label>
													<div class="col-sm-9">
													<div class="form-group">
                                                        <input type="text" id="phone-input" name="manager_phone" class="form-control" placeholder="Manager Contact Phone" value="<?php echo $warehouse->contact; ?>" onkeypress="return isNumberKey(event)" required>
                                                    </div>
													</div>
													<label for="input-Default" class="col-sm-3 control-label">Area:</label>
													<div class="col-sm-9">
    													<div class="form-group">
    														<div style="margin-bottom:15px;" class="input-group">
                                                                <input type="text" id="area-input" name="area" class="form-control" placeholder="Area of the warehouse " value="<?php echo $warehouse->surface; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">m<sup>2</sup></span>
    														</div>
    													</div>
													</div>
													<label for="input-Default" class="col-sm-3 control-label">Volume:</label>
													<div class="col-sm-9">
    													<div class="form-group">
    														<div style="margin-bottom:15px;" class="input-group">
                                                                <input type="text" id="volume-input" name="volume" class="form-control" placeholder="Volume of the warehouse " value="<?php echo $warehouse->volume; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">m<sup>3</sup></span>
    														</div>
    													</div>
													</div>
													<label for="input-Default" class="col-sm-3 control-label">Free Zone:</label>
													<div class="col-sm-9">
    													<div class="form-group">
    														<div style="margin-bottom:15px;" class="input-group">
                                                                <input type="text" id="area-freezone" name="freezone" class="form-control" placeholder="Volume of the Free Zone " value="<?php echo $warehouse->freezone; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">m<sup>3</sup></span>
    														</div>
    													</div>
													</div>
								
            										<div class="col-sm-12">
                    									<div class="form-group" style="text-align:center">
                    									<?php 
                    										if(isset($_GET['w'])){ 
                    											echo '<input type="hidden" name="edit_warehouse" value="'.$w.'" />';
                    											echo '<input type="hidden" name="update_warehouse" value="1" />'; 
                    										} 
                    									?>
                    										<button type="submit" id="submit" class="btn btn-info btn-addon"  value="Update Warehouse"> <i class="fa fa-refresh"></i> Update Warehouse</button>
                    										<a href="warehouses.php" class="btn btn-default" style="" > <i class="fa fa-times-circle"></i> Cancel</a>
                    														
                    									</div>
            									    </div>
								
                                            
                                </form>
									<script type="text/javascript">
										$(document).ready(function() {
										// validate the register form
										$("#add_user").validate();
										});
									</script>
									<script>
									function isNumberKey(evt)
									{
											var charCode = (evt.which) ? evt.which : evt.keyCode;
											if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
												return false;
												return true;
									}  
			
			
									function isNumericKey(evt)
									{
										var charCode = (evt.which) ? evt.which : evt.keyCode;
										if (charCode != 46 && charCode > 31 
										&& (charCode < 48 || charCode > 57))
										return true;
										return false;
									} 
									</script>
									
					
								
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
		
					
					<!-- Javascripts -->
        <script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
        <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
        <script src="../../assets/plugins/switchery/switchery.min.js"></script>
        <script src="../../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
        <script src="../../assets/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
        <script src="../../assets/js/space.min.js"></script>
        <script src="../../assets/js/pages/form-wizard.js"></script>
		 <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="../../assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
</body>
</html>
													