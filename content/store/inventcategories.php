<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$user = new Users;
	
	$user_id = $_SESSION['user_id'];
	$function_id = $user->get_user_info($user_id,"user_function");
	
	if(partial_access('admin') || $warehouse_access->have_module_access('products')) {} else { 
		HEADER('LOCATION: warehouse.php?message=products');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} //select company redirect ends here.
	
	/*if(isset($_POST['delete_product']) && $_POST['delete_product'] != '') { 
		$message = $product->delete_product($_POST['delete_product']);
	}*///delete account.
	
	$warehouses->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'Products'; //You can edit this to change your page title.
	//require_once("includes/header.php"); //including header file.
	
    /*display message if exist.
        if(isset($message) && $message != '') { 
            echo '<div class="alert alert-success">';
            echo $message;
            echo '</div>';
        }*/
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
		
		
        <link href="../../assets/plugins/summernote-master/summernote.css" rel="stylesheet" type="text/css"/>
        <link href="../../assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" type="text/css"/>
        <link href="../../assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css"/>
        <link href="../../assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css"/>
      
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
	<script type="text/javascript">
				$(document).ready(function() {
						// On change of the dropdown do the ajax
						$("#product").change(function() {
							$.ajax({
									// Change the link to the file you are using
									url: 'includes/get_dimensions_data.php',
									type: 'post',
									// This just sends the value of the dropdown
									data: { product: $(this).val() },
									success: function(response) {
										// Parse the jSON that is returned
										// Using conditions here would probably apply
										// incase nothing is returned
										var Vals    =   JSON.parse(response);
										// These are the inputs that will populate
										$('input[name="long"]').val(Vals.long);
										$('input[name="larg"]').val(Vals.larg);
										$('input[name="haut"]').val(Vals.haut);
										$('input[name="poids"]').val(Vals.poids);
										//$('input[name="typ"]').val(Vals.typ);
										//$("textarea[name='address']").val(Vals.address);
									}
							});
						});
					});
				</script>
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
					if(isset($_GET['message']) && $_GET['message'] != '') { 
						echo '<div class="alert alert-success">';
						echo $_GET['message'];
						echo '</div>';
					}
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
                                    <button  id="print"  class="btn btn-success" >Print Inventory</button>
                    
                                    <button href="manager_user.php?u=nw"  class="btn btn-success" >Export CSV</button>
							</div>
							</br>
							<div class="panel-body"  >
							
								
									<label >Select Product</label>
									<select name="product" id="product" class="form-control"  Required  >
										<option value="">Select Product</option>
											<?php $products->product_options($products->product_id); ?>
									</select>
								
							</div>
							<br>
							<br>
							<div class="panel-body" id="printinventory"  >
							<div class="table-responsive" >
									<table id="example3" class="display table" style="width: 100%; cellspacing: 0;" >
										<thead>
											<tr>
												<th>ID</th>
												<th>Description</th>
												
												<th>Unit</th>
												<th>Category</th>
												<th>Image</th>
												<th>Alert</th>
												<th>Qty On Hand</th>
												<?php if(partial_access('admin') || $function_id =='unitm') { ?>
												<th>Cost</th>
												
												<th>Selling Price</th>
												<?php } ?>
												<th>Status</th>
												<?php if(partial_access('admin')) { ?><th>Edit</th>
												<th>Delete</th><?php } ?>
											</tr>
										</thead>
										<tbody>
										   <?php 
										   if ($function_id =='commerciala' || $function_id =='commercialm' || $function_id == 'pofm' || $function_id =='pofa')  
												$product->list_all_products(); 
											else {
												$product->list_products(); 
											}
											?>
										</tbody>
										
									</table>
								</div>
							</div>
							</div>
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
					
					<!-- Javascripts -->
        
        <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
        <script src="../../assets/plugins/switchery/switchery.min.js"></script>
        <script src="../../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
        <script src="../../assets/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
        <script src="../../assets/js/space.min.js"></script>
        <!--<script src="../../assets/js/pages/form-wizard.js"></script>-->
		 <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="../../assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
		<script src="../../assets/js/pages/form-elements.js"></script>
</body>
</html>
													