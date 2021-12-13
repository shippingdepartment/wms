<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$order = new Order;
	
	$user_id = $_SESSION['user_id'];
	$function_id = $user->get_user_info($user_id,"user_function");
	
	if(partial_access('admin') || $warehouse_access->have_module_access('orders')) {} else { 
		HEADER('LOCATION: warehouse.php?message=lstord');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} //select company redirect ends here.
	
	if(isset($_POST['delete_order']) && $_POST['delete_order'] != '') { 
		$message = $order->delete_order($_POST['delete_order']);
	}//delete account.
	
	$warehouses->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'Purshasing Orders'; //You can edit this to change your page title.
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
		
		<script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
		<script type="text/javascript" src="../../assets/js/export/tableExport.js"></script>
		<script type="text/javascript" src="../../assets/js/export/jquery.base64.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
	<body class="page-sidebar-fixed page-header-fixed">
	<script type="text/javascript">
	$(document).on('click',"#print",function(){
      	var originalContents = $("body").html();
      	var printContents = $("#printorders").html();
      	$("body").html(printContents);
    	window.print();
     	$("body").html(originalContents);
        return false;
		//window.print();
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
					if(isset($message) && $message != '') { 
						echo '<div class="alert alert-success">';
						echo $message;
						echo '</div>';
					}
				?>
				
				
				
					<div class="row" >
                     <div class="col-md-12">
					<div class="panel panel-white" >
					
					
					
					
					<div class="panel-body"  >
						<?php 
									if( $_SESSION['user_type'] == "admin"  OR  $function_id=='storem' OR $function_id=='manager' )  { ?>
						<div style="float:left">
							<a href="neworder.php"  class="btn btn-info btn-addon" ><i class="fa fa-plus"></i> Add New Order</a>
						</div>
						<div style="float:right">
									<?php } else { ?>
						<div style="float:left">
									<?php } ?>
                                     <a href="reports/listOrders.php" target="_blank"  class="btn btn-info btn-addon"  > <i class="fa fa-print"></i> Print Orders List</a>
                    
                                    <a   class="btn btn-info btn-addon" onClick ="$('#example3').tableExport({type:'excel',escape:'false'});" > <i class="fa fa-file-excel-o"></i> Export to CSV</a>
						</div>
                    </div>
					</br>
					<div class="panel-body"   >
					
					<div  >
					<table id="example3" class="table display" style="width: 100%; cellspacing: 0;" >
						<thead>
							<tr>
								<th>#</th>
								<th>Order #</th>
								<th>Date</th>
								<th>Warehouse</th>
								<th>Supplier</th>
								<th>Delivery Date</th>
								<th>Items</th>
								<th>Details</th>
								<th>Approved</th>
								<th>Received</th>
								<th></th>
								
							</tr>
						</thead>
						<tbody>
						   <?php 
						  
								$order->list_all_orders($_SESSION['warehouse_id'], 0); 
							
							?>
						</tbody>
						
					</table>
					</div>
					</div>
					</div>
					</br>
					</br>
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
