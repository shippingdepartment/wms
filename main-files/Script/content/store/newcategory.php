<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	$note = new Notes;
	
	$product_category = new ProductCategory;
	
	if(partial_access('admin') || $store_access->have_module_access('products')) {} else { 
		HEADER('LOCATION: warehouse.php?message=products');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} 
	
	
	//updating category
	if(isset($_POST['update_category'])) { 
		extract($_POST);
		$message = $product_category->update_category($edit_category,$category_name, $category_description);
	}//update ends here.
	//setting category data if updating or editing.
	if(isset($_POST['edit_category'])) {
		$product_category->set_category($_POST['edit_category']);	
	} //category edit ends here
	if(isset($_POST['add_category'])) {
		$add_category = $_POST['add_category'];
		if($add_category == '1') { 
			extract($_POST);
			$message = $product_category->add_category($category_name, $category_description);
			$note_title = 'New Category Added';
			$note_details = 'New Category: '.$category_name.' added to this warehouse';
			$note->add_note($note_title, $note_details );
		}
		
	}
	
	if(isset($_POST['edit_category'])){ $page_title = 'Edit Product Category'; } else { $page_title = 'Add Product Category';}; //You can edit this to change your page title.
	
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
						if(isset($message) && $message != '') { 
							echo '<div class="alert alert-success">';
							echo $message;
							echo '</div>';
						}
				?>
					<div class="panel panel-white">
						<div class="panel-heading clearfix">
							<div class="panel-body" >
										<form class="form-horizontal" action="<?php $_SERVER['PHP_SELF']?>" id="wizardForm" name="level" method="post">
											<div class="form-group">
													<div class="col-sm-10">
													<input type="text" class="form-control" name="category_name"  placeholder="Product Category name" value="<?php echo $product_category->category_name; ?>" required="required" />
													</div>
											</div>
											  
											 <div class="form-group">
													<div class="col-sm-10">
													<textarea class="form-control" placeholder="Category description" name="category_description"><?php echo $product_category->category_description; ?></textarea>
													</div>
											</div>
											<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
												<div class="form-group" style="text-align:center">
												<?php 
												if(isset($_POST['edit_category'])){ 
													echo '<input type="hidden" name="edit_category" value="'.$_POST['edit_category'].'" />';
													echo '<input type="hidden" name="update_category" value="1" />'; 
												} else { 
													echo '<input type="hidden" name="add_category" value="1" />';
												} ?>
												<button type="submit" class="btn btn-info btn-addon" value="" > <?php if(isset($_POST['edit_category'])){ echo '<i class="fa fa-refresh"></i> Update Category'; } else { echo '<i class="fa fa-plus"></i> Add Category';} ?> </button>
												</div>
											</div>
										</form>
										<script>
											$(document).ready(function() {
												// validate the register form
												$("#add_category").validate();
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
													