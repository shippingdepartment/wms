<?php
	include('system_load.php');
	
	//This loads system.
	//user Authentication.
	authenticate_user('subsriber');
	//creating company object.
	$user = new Users;
	$warehouse = new Warehouse;
	
	//$id=$_SESSION["store_id"];
	$user_id = $_SESSION['user_id'];
	$function_id = $user->get_user_info($user_id,"user_function");
	
	if($_SESSION['user_type'] != "admin") {	
		if( $warehouse_access->have_module_access('products') OR $function_id!='storem' OR $function_id!='manager' )  {
			HEADER('LOCATION: warehouse.php?message=products');
		}
	}
	
	if(isset($_POST['save']))
	{
		extract($_POST);
			$message = $warehouse->add_inventory($quantity, '0', $product, $_SESSION['warehouse_id'], $lot);
			HEADER('LOCATION: addstock.php?message=Stock Added !!');
	}	


$page_title = 'Add Stock';
	
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
					if(isset($_GET['message']) && $_GET['message'] != '') { 
						echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ';
						echo $_GET['message'];
						echo '</div>';
					}
					if(isset($message) && $message != '') { 
						echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ';
						echo $message;
						echo '</div>';
					}
				?>
				
					<div class="panel panel-white alert alert-default" style="font-size:14px;color:#0d47a1">
						<div class="panel-heading clearfix">
							<div class="panel-body" >
										<form class="form-horizontal" action="<?php $_SERVER['PHP_SELF']?>" id="wizardForm" name="level" method="post">
											<div class="form-group">
												<label class="col-sm-2 control-label">Date :</label>
												<div class="col-sm-6">
												
												<input type="text" name="date" class="form-control datepick" style="width:10em" Disabled value="<?php echo date('Y-m-d'); ?>" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label">Destination :</label>
												<div class="col-sm-6">
												<select id="vendor_options" name="source" class="form-control" style="width:100%" onchange="AjaxFunction();" Disabled >
													<?php $query2="SELECT DISTINCT warehouse_id,name FROM warehouses WHERE warehouse_id='".$_SESSION['warehouse_id']."' order by name"; 
														if($stmt = $db->query("$query2")){
															while ($row2 = $stmt->fetch_assoc()) {
																echo "<option selected value='$row2[warehouse_id]'>$row2[name]</option>";}
																//echo  "<option value='$row2[warehouse_id]'>$row2[name]</option>";}

															
														}else{
															echo $db->error;
														}
													
													?>
												</select>
												</div>												
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label">Reference :</label>
												<div class="col-sm-6">
												<input type="text" name="lot" id="lot" class="form-control" style="width:10em" placeholder="Reference" Required  />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label">Select Product :</label>
												<div class="col-sm-6">
												<select name="product" id="product_id" style="width:100%" class="form-control" Required >
													<option value="">-- Select Product --</option>
													 <?php $products->product_options($products->product_id); ?>
												</select>
												</div>
													
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label">Quantity to Add :</label>
												<div class="col-sm-6">
												<input type="text" name="quantity" id="quantity" class="form-control" style="width:10em" placeholder="Quantity" Required /> 
												</div>
											</div>
											<div class="form-group"><center>
												<div class="alert alert-default">
													<Button type="submit" class="btn btn-info btn-addon" name="save"  /> <i class="fa fa-plus"></i> Add Quatity to Stock</Button> </center>
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
        
        <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
        <script src="../../assets/plugins/switchery/switchery.min.js"></script>
        <script src="../../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
        <script src="../../assets/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
        <script src="../../assets/js/space.min.js"></script>
        --<script src="../../assets/js/pages/form-wizard.js"></script>
		 <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="../../assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
		<script src="../../assets/js/pages/form-elements.js"></script>
		
		
		
</body>
</html>
													