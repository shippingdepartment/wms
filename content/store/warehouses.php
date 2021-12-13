<?php
	include('system_load.php');
	//This loads system.
	
	//user Authentication.
	authenticate_user('subscriber');
	//creating store object.
	
	if(isset($_GET['msg']) && $_GET['msg'] != '') { 
		if ($_GET['msg'] =="nw") {
		$message = "<div class='alert alert-success'> <i class='fa fa-check-circle'></i> New warehouse Added Successfully !! <br> <p class='text-info'> <i class='fa fa-exclamation-circle'></i> Please select a warehouse from the list to acceed</p></div>";
		$message .="<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> Don't forget to add User Access to this warehouse !! Click the 'User Access' menu on sidebar and proceed</div> ";
		}
		else {
		$message = "<div class='alert alert-info'>Please select your Warehouse !!</div>";
		}
	}//Message ends here select store
	
	//delete store if exist.
	if(isset($_POST['delete_warehouse']) && $_POST['delete_warehouse'] != '') { 
		$message = $warehouses->disable_warehouse($_POST['delete_warehouse']);
		
	}//delete account.
		
	$page_title = "Warehouses"; //You can edit this to change your page title.
	
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
			<?php 
			if(isset($_SESSION['warehouse_id'])){
			require_once("includes/sidebar.php"); //including sidebar file. 
			}else {
			require_once("includes/sidebaradmin.php"); //including sidebar file. 	
			}
			?>
		
			<!-- End Side Bar 
            <!-- Page Content -->
            <div class="page-content">
				<!-- Header -->
				
				<?php
				require_once("includes/header.php"); //including sidebar file.
				
				?>
				<!-- End Header -->
				
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
				
				
				
					<div class="row">
                     <div class="col-md-12">
					<div class="panel panel-white" >
					
					<?php if(partial_access('admin')) { ?>
					
					
					<div class="panel-body" >
                                    <button type="button" class="btn btn-info btn-addon" data-toggle="modal" data-target="#myModal1"> <i class="fa fa-plus"></i> Add new Warehouse</button>
                                    <!-- Modal -->
                                    <form id="add-row-form" data-async data-target="#myModal1" action ="includes/addwarehouse.php" method="POST" enctype="multipart/form-data" role="form" >
                                    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel1">New Warehouse</h4>
                                                </div>
												<div id="success_message"></div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <input type="text" id="name-input" name="name_warh" class="form-control" placeholder="Name" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" id="address-input" name="address" class="form-control" placeholder="Address (Street / Bloc )" required>
                                                    </div>
													<div class="form-group">
                                                        <input type="text" id="city-input" name="city" class="form-control" placeholder="City" required>
                                                    </div>
													<div class="form-group">
                                                        <input type="text" id="state-input" name="state" class="form-control" placeholder="State" required>
                                                    </div>
													<div class="form-group">
                                                        <input type="text" id="country-input" name="country" class="form-control" placeholder="Country" required>
                                                    </div>
													<div class="form-group">
                                                        <input type="text" id="manager-input" name="manager_name" class="form-control" placeholder="Manager Name" required>
                                                    </div>
													<div class="form-group">
                                                        <input type="text" id="phone-input" name="manager_phone" class="form-control" placeholder="Manager Contact Phone" onkeypress="return isNumberKey(event)" required>
                                                    </div>
													<div class="form-group">
														<div style="margin-bottom:15px;" class="input-group">
                                                        <input type="text" id="area-input" name="area" class="form-control" placeholder="Area of the warehouse " onkeypress="return isNumberKey(event)" required><span class="input-group-addon">m<sup>2</sup></span>
														</div>
													</div>
													<div class="form-group">
														<div style="margin-bottom:15px;" class="input-group">
                                                        <input type="text" id="volume-input" name="volume" class="form-control" placeholder="Volume of the warehouse " onkeypress="return isNumberKey(event)" required><span class="input-group-addon">m<sup>3</sup></span>
														</div>
													</div>
                                                   <div class="form-group">
														<div style="margin-bottom:15px;" class="input-group">
                                                        <input type="text" id="area-freezone" name="freezone" class="form-control" placeholder="Volume of the Free Zone " onkeypress="return isNumberKey(event)" required><span class="input-group-addon">m<sup>3</sup></span>
														</div>
													</div>
                                                </div>
                                                <div class="modal-footer">
													<input type="hidden" name="add_warehouse" value="1" />
                                                    <button type="submit" id="submit" class="btn btn-info btn-addon"> <i class="fa fa-plus"></i> Add Warehouse</button>
													<button type="button" class="btn btn-default" data-dismiss="modal"> <i class="fa fa-times-circle"></i> Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
					
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
									
					<?php } ?>
					<div class="table-responsive">
					<table id="example3" class="display table" style="width: 100%; cellspacing: 0;">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Street/Bloc</th>
								<th>City</th>
								<th>State</th>
								<th>Country</th>
								<th>Manager</th>
								<th>Phone</th>
								<th>Select</th>
								<?php if(partial_access('admin')) { ?><th>Edit</th>
								<th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
						<?php
						
						    echo $warehouses->list_warehouses();
						
						
						?>
						</tbody>
						
					</table>
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
