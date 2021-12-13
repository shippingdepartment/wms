<?php
	include('system_load.php');
	//This loads system.

	//user Authentication.
	authenticate_user('admin');
	//creating store object.
	if(isset($_POST['user_id']) && isset($_POST['warehouse_id']) ) { 
		if($_POST['user_id'] == '' && $_POST['warehouse_id'] == '') { 
			$message = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Please choose a User and a Warehouse !!</div> ';
		} else { 
			//test if at least one module is selected
			$moduleChecked = FALSE;
			for($i=0;$i<10;$i++) {
				if (isset($_POST['access_to'][$i])) {
					$moduleChecked = TRUE;
				}
			}
			if($moduleChecked == TRUE) {
				$message =  @$warehouse_access->add_warehouse_access($_POST['user_id'], $_POST['warehouse_id'], $_POST['access_to']);
			} else {
				$message = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> The user must have access to at least one module !!</div> ';
			}
		
		}
	}//add store access ends here.
	//delete access
	if(isset($_POST['delete_access']) && $_POST['delete_access'] != '') { 
		$message = $warehouse_access->delete_access($_POST['delete_access']);
	}
	
	//delete access ends here.	
	$page_title = "User Access"; //You can edit this to change your page title.
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
				<?php require_once("includes/header.php"); //including sidebar file. ?>
				<!-- End Header -->
				
                <!-- Page Inner -->
				<div class="page-inner">
					<div class="page-title">
							<h3 class="breadcrumb-header"> <p class="text-primary"><?php echo $page_title; ?></p></h3>
					</div>
				

				<?php
					if(isset($message) && $message != '') { 
						echo $message;
					}
				?>
				
			
				<div class="row" >
                     <div class="col-md-12">
					<div class="panel panel-white">
						<div class="panel-heading clearfix">
							<div class="panel-body" >
								
								<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
                                        1- Choose User and Warehouse from the lists below :
                                
								
                                <form name="grand_access" id="grand_access" action="warhaccess.php" method="post">
									<table cellpadding="20" style="padding:20px;" border="0" >
                    	
										
										<tr>
												<td style="padding:20px;">
													<select class="form-control" name="user_id" id="user_id"  required="required">
														<option selected value="">Select User</option>
															<?php $new_user->subscriber_access_options(); ?>
													</select>
												</td>
												<td style="padding:20px;">
													<select class="form-control" name="warehouse_id" id="warehouse_id" required="required">
														<option selected value="">Select Warehouse</option>
															<?php $warehouses->warehouse_options(); ?>
													</select>
												</td>
										</tr>
										<tr>
										<td colspan ="2">
										 2- Choose the modules that the user <span id="chosen_user"></span>can access to :
										 </td>
										</tr>
										<tr >
												<td colspan="2" style="padding:20px;font-size:15px" align="center">
													
													<input type="checkbox" name="access_to[]" class="chk" value="products" /> Products &nbsp;
													<input type="checkbox" name="access_to[]" class="chk" value="transfers" /> Transfers &nbsp;
													<input type="checkbox" name="access_to[]" class="chk" value="orders" /> Orders &nbsp;
													<input type="checkbox" name="access_to[]" class="chk" value="deliveries" /> Deliveries &nbsp;
													<input type="checkbox" name="access_to[]" class="chk" value="clients" /> Customers &nbsp;
													<input type="checkbox" name="access_to[]" class="chk" value="suppliers" /> Suppliers &nbsp;
													<input type="checkbox" name="access_to[]" class="chk" value="stock" /> Stock &nbsp;
													<input type="checkbox" name="access_to[]" class="chk" value="receptions" /> Receptions &nbsp;
													<input type="checkbox" name="access_to[]" class="chk" value="returns" /> Returns &nbsp;
													<input type="checkbox" name="access_to[]" class="chk" value="reports" /> Reports &nbsp;
													
													
												</td>
										</tr>
										<tr>
												<td style="padding:20px;" colspan="2" align="center"><input type="submit" class="btn btn-success " value="Grant Access" /></td>
												
										</tr>
                        
									</table>
								</form>
								
									
								
							</div>
							<div class="table-responsive" >
									<table id="example3" class="display table" style="width: 100%; cellspacing: 0;" >
										<thead>
											<tr>
												
												<th>Access ID</th>
												<th>User Full Name</th>
												
												<th>Warehouse</th>
												<th>Clients Access</th>
												<th>Products Access</th>
												<th>Transfers Access</th>
												<th>Orders Access</th>
												<th>Deliveries Access</th>
												<th>Suppliers Access</th>
												<th>Stock Access</th>
												<th>Receptions Access</th>
												<th>Returns Access</th>
												<th>Reports Access</th>
												<th>Action</th>
												
											
												
											</tr>
										</thead>
										<tbody>
										   <?php 
										   
										  
												$user->get_user_accesses();
										 
											?>
										</tbody>
										
									</table>
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

<script type="text/javascript">
		$(document).ready(function(e) {
			
			$("#user_id").change(function() {
				var i ;
				for(i=0; i<10; i++) {
					$('.chk')[i].checked = false;
				}
				/*$("#store_id").select2("val", null);*/
			});
			
			$("#user_id").bind("change", function () {
            $("#warehouse_id")[0].selectedIndex = 0;
			});
				
			$("#warehouse_id").change(function() {
				var i ;
				for(i=0; i<10; i++) {
					$('.chk')[i].checked = false;
				}
				$.ajax({
						 data: {
						  user_id: $("#user_id").val(),
						  store_id: $("#warehouse_id").val()
						 },
						 type: 'POST',
						 dataType: 'json',
						 url: 'includes/get_access_details.php',
						 success: function(response) {
						   var product_access = response.product_access;
						   var stock_access = response.stock_access;
						   var order_access = response.order_access;
						   var return_access = response.return_access;
						   var delivery_access = response.delivery_access;
						   var client_access = response.client_access;
						   var supplier_access = response.supplier_access;
						   var transfer_access = response.transfer_access;
						   var reception_access = response.reception_access;
						   var reports_access = response.reports_access;
						   
						   var details = false;
						  
						   if(product_access =='1') {
							   $('.chk')[0].checked = true;
							   details = true;
						   }
						   if(transfer_access =='1') {
							   $('.chk')[1].checked = true;
							   details = true;
						   }
						   if(order_access =='1') {
							   $('.chk')[2].checked = true;
							   details = true;
						   }
						   if(delivery_access =='1') {
							   $('.chk')[3].checked = true;
							   details = true;
						   }
						   if(client_access =='1') {
							   $('.chk')[4].checked = true;
							   details = true;
						   }
						   if(supplier_access =='1') {
							   $('.chk')[5].checked = true;
							   details = true;
						   }
						   if(stock_access =='1') {
							   $('.chk')[6].checked = true;
							   details = true;
						   }
						   if(reception_access =='1') {
							   $('.chk')[7].checked = true;
							   details = true;
						   }
						   if(return_access =='1') {
							   $('.chk')[8].checked = true;
							   details = true;
						   }
						   if(reports_access =='1') {
							   $('.chk')[9].checked = true;
							   details = true;
						   }
						   
						   
						   //if(details == true) {
							  // document.getElementById("access_details").disabled = false;
							  // document.getElementById("grant_access").disabled = true;
							  // document.getElementById("useraccess").value = $("#user_id").val();
							  // document.getElementById("storeaccess").value = $("#store_id").val();
						  // } else {
							  // document.getElementById("access_details").disabled = true;
							 //  document.getElementById("grant_access").disabled = false;
							 //  document.getElementById("useraccess").value = '';
							 //  document.getElementById("storeaccess").value = '';
						  // }
						   
						   
						   
						 }
				
				});
			});
		});
	
	
	 </script>
		
					
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
</body>
</html>
													