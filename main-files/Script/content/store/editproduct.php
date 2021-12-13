<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('utilisateur');
	//creating company object.
	$new_store = new Store;
	$warehouse_access = new warehouseAccess;
	$warehouse = new Warehouse;
	$product = new Product;
	$supplier = new Supplier;
	$category = new ProductCategory;
	/*if (isset($_GET['p'])){
		$p = $_GET['p'];
	}
	else {
		HEADER('LOCATION: warehouses.php');
	}*/
	if (isset($_POST['edit_product'])) {
	$p=$_POST['edit_product'];
	$product->set_product($p);
	$cat_id = $product->get_product_info($p,'category_id');
	$supp_id = $product->get_product_info($p,'supplier_id');
	$unit = $product->get_product_info($p,'product_unit');
	}
	if(isset($_POST['update_product'])) {
		if ($_POST['update_product'] =='1') {
		extract($_POST);
		$message = $product->update_product($prodid, $product_name, $unit, $category_id, $cost, $price, $tax_id, $alert_units, $length, $width, $height, $weight);
		}
	}//update ends here.
	
	//setting data if updating or editing.
	
	
	 $page_title = 'Edit Product'; //You can edit this to change your page title.
	
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
							echo '<script>window.location="products.php?s_s='.session_id().'";</script>';
						}
				?>
				<div class="panel panel-white alert alert-default">
						<div class="panel-heading clearfix">
							<div class="panel-body" >
								<form  id="wizardForm" class="form-horizontal " style="font-size:14px;color:#0d47a1"  action ="<?php $_SERVER['PHP_SELF']; ?>" method="POST" >
									
													<label for="input-Default" class="col-sm-2 control-label">Product Code / Barcode:</label>
													<div class="col-sm-10">
														<div class="form-group">
															<input type="hidden" name="prodid" value="<?php if (isset($p) && ($p!='')){ echo $p;} ?>" />
															<input type="text" id="code_product" name="code_product" class="form-control" placeholder="Name" value="<?php echo $product->product_manual_id; ?>" Readonly  required>
														</div>
													</div>
													<label for="input-Default" class="col-sm-2 control-label">Product Name:</label>
													<div class="col-sm-10">
														<div class="form-group">
															<input type="text" id="product_name" name="product_name" class="form-control" placeholder="Address (Street / Bloc )" value="<?php echo $product->product_name; ?>" required>
														</div>
													</div>
													<label for="input-Default" class="col-sm-2 control-label">Supplier:</label>
													<div class="col-sm-10">
														<div class="form-group">
															<select id="supplier_id" name="supplier_id" class="form-control" style="width:100%" Disabled  >
																		
																		<?php $supplier->supplier_options_list($supp_id); ?>	
																	</select>
														</div>
													</div>
													<label for="input-Default" class="col-sm-2 control-label">Unit:</label>
													<div class="col-sm-10">
														<div class="form-group">
															<select id="unit" name="unit" class="form-control" style="width:100%"   >
																			<option  value=''>Choose Product Unit</option>
																			<option  value='unit' <?php if ($unit =='unit') { echo 'selected'; }?>>Unit</option>
																			<option  value='box' <?php if ($unit =='box') { echo 'selected';} ?>>Box</option>
																			<option  value='kg' <?php if ($unit =='kg') { echo 'selected';} ?>>Kg</option>
																			<option  value='cm' <?php if ($unit =='cm') { echo 'selected';} ?>>Cm</option>
																			<option  value='liter' <?php if ($unit =='liter') { echo 'selected';} ?>>Liter</option>
																			
																		</select>	
														</div>
													</div>
													<label for="input-Default" class="col-sm-2 control-label">Category:</label>
													<div class="col-sm-10">
														<div class="form-group">
															<select id="category_id" name="category_id" class="form-control" style="width:100%"  required >
																		
																		<?php $category->category_options_retreived($cat_id); ?>	
																		</select>	
														</div>
													</div>
													<label for="input-Default" class="col-sm-2 control-label">Cost:</label>
													<div class="col-sm-10">
														<div class="form-group">
															<div style="margin-bottom:15px;" class="input-group">
																<input type="text" id="cost" name="cost" class="form-control" placeholder="Product Cost" value="<?php echo $product->product_cost; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">$</sup></span>
																</div>
														</div>
													</div>
													<label for="input-Default" class="col-sm-2 control-label">Selling Price:</label>
													<div class="col-sm-10">
														<div class="form-group">
															<div style="margin-bottom:15px;" class="input-group">
															<input type="text" id="price" name="price" class="form-control" placeholder="Product Selling Price" value="<?php echo $product->product_selling_price; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">$</span>
															</div>
														</div>
													</div>
													<label for="input-Default" class="col-sm-2 control-label">VAT Rate:</label>
													<div class="col-sm-10">
														<div class="form-group">
															<div style="margin-bottom:15px;" class="input-group">
																<input type="text" id="tax_id" name="tax_id" class="form-control" placeholder="Tax Rate" value="<?php echo $product->tax_id; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">%</span>
															</div>
														</div>
													</div>
													<label for="input-Default" class="col-sm-2 control-label">Alert Qty:</label>
													<div class="col-sm-10">
														<div class="form-group">
																<input type="text" id="alert_units" name="alert_units" class="form-control" placeholder="Alert Quantity" onkeypress="return isNumberKey(event)" value="<?php echo $product->alert_units; ?>" required>
														
														</div>
													</div>
													<label for="input-Default" class="col-sm-2 control-label"></label>
													<div class="col-sm-10 text-primary">
														<b>  Product Dimensions ( Important for storage calculations )  </b>
													</div>
															
															<div class="form-group">
																<label for="input-Default" class="col-sm-2 control-label">Length:</label>
																<div class="col-sm-10">
																<div style="margin-bottom:15px;" class="input-group">
																	<input type="text" id="length" name="length" class="form-control" placeholder="Length" value="<?php echo $product->long_pr; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">cm</sup></span>
																</div>
																</div>
															</div>
															<div class="form-group">
																<label for="input-Default" class="col-sm-2 control-label">Width:</label>
																<div class="col-sm-10">
																<div style="margin-bottom:15px;" class="input-group">
																	<input type="text" id="width" name="width" class="form-control" placeholder="Width" value="<?php echo $product->larg; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">cm</sup></span>
																</div>
																</div>
															</div>
															<div class="form-group">
																<label for="input-Default" class="col-sm-2 control-label">height:</label>
																<div class="col-sm-10">
																<div style="margin-bottom:15px;" class="input-group">
																	<input type="text" id="height" name="height" class="form-control" placeholder="Height" value="<?php echo $product->haut; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">cm</sup></span>
																</div>
																</div>
															</div>
															<div class="form-group">
																<label for="input-Default" class="col-sm-2 control-label">Weight:</label>
																<div class="col-sm-10">
																<div style="margin-bottom:15px;" class="input-group">
																	<input type="text" id="weight" name="weight" class="form-control" placeholder="Weight" value="<?php echo $product->poids; ?>" onkeypress="return isNumberKey(event)" required><span class="input-group-addon">kg</span>
																</div>
																</div>
															</div>
													
								
													<div class="form-group" style="text-align:center">
													<?php 
														if(isset($_POST['edit_product'])){ 
															//echo '<input type="hidden" name="edit_product" value="'.$p.'" />';
															echo '<input type="hidden" name="update_product" value="1" />'; 
														} 
													?>
														<button type="submit" id="submit" class="btn btn-info btn-addon"  value="Update Product"> <i class="fa fa-refresh"></i> Update Product</button>
														<a href="products.php"  class="btn btn-default" > <i class="fa fa-times-circle"></i> Cancel</a>
																		
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
													