<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$user = new Users;
	$supplier = new Supplier;
	$category = new ProductCategory;
	
	$user_id = $_SESSION['user_id'];
	$function_id = $user->get_user_info($user_id,"user_function");
	
	if(partial_access('admin') || $warehouse_access->have_module_access('products')) {} else { 
		HEADER('LOCATION: warehouse.php?message=products');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} //select company redirect ends here.
	
	if(isset($_POST['delete_product']) && $_POST['delete_product'] != '') { 
		$message = $product->delete_product($_POST['delete_product']);
	}//delete account.
	
	$warehouses->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'Products'; //You can edit this to change your page title.
	
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
					if(isset($_GET['msg']) && $_GET['msg'] != '') { 
						if ($_GET['msg']=='np')
						echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ';
						echo 'New Product added Successfully ';
						echo '</div>';
						
					}
				?>
				<div class="row">
                    <div class="col-md-12">
						<div class="panel panel-white" >
					
							<?php if(partial_access('admin')) { ?>
					
					
							<div class="panel-body" >
									
                                    <a type="button" class="btn btn-primary btn-addon" data-toggle="modal" data-target="#myModal"> <i class="fa fa-plus"></i> Add new Product</a>
									<a href="newsupplier.php" class="btn btn-info btn-addon" style="float:right; margin-left: 15px;" > <i class="fa fa-plus"></i> Add New Supplier </a>
									<a href="newcategory.php" class="btn btn-info btn-addon" style="float:right" > <i class="fa fa-plus"></i> Add New Category </a>
									<br/><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> To add new product, category & supplier should be added beforehand </small><br/><br/>
                                    <!-- Modal Product -->
                                    <form id="add-row-form" data-async data-target="#myModal" action ="includes/addproduct.php" method="POST" enctype="multipart/form-data" role="form" >
										<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
														
														<h4 class="modal-title" id="myModalLabel">New Product</h4><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> To add new product, category & supplier should be added beforehand </small>
														
													</div>
													<div id="success_message"></div>
													<div class="modal-body">
														<div class="form-group">
															<input type="text" id="code_product" name="code_product" class="form-control" placeholder="Barcode" onkeypress="return isNumberKey(event)" required>
														</div>
														<div class="form-group">
															<input type="text" id="product_name" name="product_name" class="form-control" placeholder="Product Name" required>
														</div>
														
														<div class="form-group">
															
																<select id="supplier_id" name="supplier_id" class="form-control" style="width:100%" onchange="getvalues(this.value)" required >
																	<option  value=''>Choose Supplier</option>
																	<?php 
																	    $query1="SELECT DISTINCT supplier_id, full_name FROM suppliers  order by full_name"; 
																        if($stmt1 = $db->query("$query1")){
																	        while ($row1 = $stmt1->fetch_assoc()) {
																		        echo "<option  value='$row1[supplier_id]'>$row1[full_name]</option>";}																		
																        }else{
																	            echo $db->error;
																        }
															
															?>		
																</select>
																
														</div>
														
														<div class="form-group">
																	<select id="unit" name="unit" class="form-control" style="width:100%" required >
																		<option  value=''>Choose Product Unit</option>
																		<option  value='unit'>Unit</option>
																		<option  value='box'>Box</option>
																		<option  value='kg'>Kg</option>
																		<option  value='cm'>Cm</option>
																		<option  value='liter'>Liter</option>
																		
																	</select>	
														</div>
														<div class="form-group">
																	<select id="category_id" name="category_id" class="form-control" style="width:100%" required >
																	<option  value=''> Choose Category</option>
																	<?php 
																	    $query2="SELECT DISTINCT category_id, category_name FROM product_categories  order by category_name"; 
																        if($stmt2 = $db->query("$query2")){
																	        while ($row2 = $stmt2->fetch_assoc()) {
																		        echo "<option  value='$row2[category_id]'>$row2[category_name]</option>";}																		
																        }else{
																	            echo $db->error;
																        }
															
															?>			
																	</select>	
														</div>
														<div class="form-group">
															<div style="margin-bottom:15px;" class="input-group">
															<input type="text" id="cost" name="cost" class="form-control" placeholder="Product Cost" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">$</sup></span>
															</div>
														</div>
														<div class="form-group">
															<div style="margin-bottom:15px;" class="input-group">
															<input type="text" id="price" name="price" class="form-control" placeholder="Product Selling Price" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">$</span>
															</div>
														</div>
														<div class="form-group">
															<div style="margin-bottom:15px;" class="input-group">
															<input type="text" id="tax_id" name="tax_id" class="form-control" placeholder="VAT Rate" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">%</span>
															</div>
														</div>
														<div class="form-group">
															<input type="text" id="alert_units" name="alert_units" class="form-control" placeholder="Alert Quantity" onkeypress="return isNumberKey(event)" required>
														</div>
														<div class="alert alert-default" role="alert">
															<p class="text-primary" ><b>  Product Dimensions ( Important for storage calculations )  </b></p>
															<div class="form-group">
																<div style="margin-bottom:15px;" class="input-group">
																	<input type="text" id="length" name="length" class="form-control" placeholder="Length" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">cm</sup></span>
																</div>
															</div>
															<div class="form-group">
																<div style="margin-bottom:15px;" class="input-group">
																	<input type="text" id="width" name="width" class="form-control" placeholder="Width" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">cm</sup></span>
																</div>
															</div>
															<div class="form-group">
																<div style="margin-bottom:15px;" class="input-group">
																	<input type="text" id="height" name="height" class="form-control" placeholder="Height" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">cm</sup></span>
																</div>
															</div>
															<div class="form-group">
																<div style="margin-bottom:15px;" class="input-group">
																	<input type="text" id="weight" name="weight" class="form-control" placeholder="Weight" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">kg</span>
																</div>
															</div>
														</div>
														
													</div>
													<div class="modal-footer alert">
														<input type="hidden" name="add_product" value="1" />
														<button type="submit" id="submit" class="btn btn-info btn-addon"> <i class="fa fa-plus"></i> Add Product</button>
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
												<th>Description</th>
												
												<th>Unit</th>
												<th>Category</th>
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
										  // if ($function_id =='admin' || $function_id =='commercialm' || $function_id == 'pofm' || $function_id =='pofa')  
											//	$product->list_all_products(); 
											//else {
												$product->list_products(); 
										//	}
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
