<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$warehouse = new Warehouse;
	$product = new Product;
	$order = new Order;
	$supplier = new Supplier;
	
	$user_id = $_SESSION['user_id'];
	$function_id = $user->get_user_info($user_id,"user_function");
	
	if(partial_access('admin') || $warehouse_access->have_module_access('products')) {} else { 
		HEADER('LOCATION: warehouse.php?message=products');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} 
	
	$warehouses->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'Purshasing Reports';

	// Quantities and Percentages
	 $order_count = $order->order_count($_SESSION['warehouse_id']);
	 $rcp_count = $order->reception_count($_SESSION['warehouse_id']);
	 
	 $qty_ord = $order->count_products_ordered($_SESSION['warehouse_id']);
	 $qty_rcv = $order->count_products_received($_SESSION['warehouse_id']);
	
	
	
	// Product Movments
	
	
?>
<?php
 
 
 
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
		  <script src="../../assets/plugins/chartjs/loader.js"></script>
		<!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
		
				<script type="text/javascript">
				google.charts.load('current', {packages: ['corechart', 'bar']});
				google.charts.setOnLoadCallback(drawDualX);
				google.charts.setOnLoadCallback(drawChart);

				function drawDualX() {
					  var data = google.visualization.arrayToDataTable([
						['Purshasing Status', 'Quantity', { role: 'style' }, { role: 'annotation' }],
						<?php
							echo "['N# Orders', ".$order_count.", '#0d47a1','Total Qty'],";
							echo "['N# Receiv.', ".$rcp_count.", '#CC0000','Out of Stock'],";
							echo "['Prod. Ord.', ".$qty_ord.", '#FF8800','Alert Qty'],";
							echo "['Prod. Receiv', ".$qty_rcv.", '#007E33','Damaged Qty'],";
						?>
						
					  ]);
					  

					  var options = {
						title: 'Purshasing Status ',
						width: 520,
						height: 365,
						
					};
					  var materialChart = new google.charts.Bar(document.getElementById('piechart'));
					  materialChart.draw(data, options);
					}
					
					
				function drawChart() {

					var data = google.visualization.arrayToDataTable([
					  ['Label Zone', 'Purchased Quantity'],
					  <?php
							$data_chart = $order->top5_purchased_products_chart($_SESSION['warehouse_id']);
							echo $data_chart;
					  ?>
					]);
					
					var options = {
						title: 'TOP 5 Best Purchasing Products',
						chartArea: {width: '60%'},
						colors: ['#ffab91', '#0cb262'],
						hAxis: {
						  title: 'Quantities',
						  minValue: 0
						},
						width: 520,
						height: 297,
						vAxis: {
						  title: 'Products'
						}
					  };
					  var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
					  chart.draw(data, options);
				}
				
				</script>
				

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
      	var printContents = $("#printinventory").html();
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
					<div class="page-title"><h3 class="breadcrumb-header"><?php echo $page_title; ?></h3></div>
					<div class="alert alert-default" role="alert">
						<div id="main-wrapper">
							<div class="row">
								<div class="col-md-12">
								<div class="alert alert-info" ><h4>General Report</h4></div>
								</div>
								<div class="col-md-6">
									<div class="panel panel-white">
										<div class="panel-body">
											
												<div class="table-responsive">
												<table class="table">
													<thead>
														<tr>
															<th></th>
															<th></th>
															<th></th>
															<th width="150px"></th>
														</tr>
													</thead>
													<tbody>
													<?php if($order_count != 0 ) { $rate1 = $rcp_count*100/$order_count; $rate1= number_format($rate1,0);} else { $rate1 =0;} ?>
													
														<tr class="active" style="font-size:12px;color:#CC0000"></tr>
														<tr>
															<td scope="row">1</td>
															<td>N# Purshasing Orders</td>
															<td align="right"><?php  echo $order_count;?> orders</td>
															<td >
															</td>
														</tr>
														<tr>
															<th scope="row">2</th>
															<td>N# Receptions</td>
															<td align="right"> <?php echo $rcp_count.' receptions'; ?></td>
															
															<td>
															</td>
														</tr>
														<tr>
															<th scope="row"></th>
															<td colspan="2" align="right" style="font-size:12px;color:#CC0000"><b>Global Purshasing Rate (Orders) :</b></td>
															
															<td>
																
																<div class="progress progress-lg">
																<?php if ($rate1 <= 15) { ?>
																	<div class="progress-bar progress-bar-success progress-bar-striped active"  role="progressbar" aria-valuenow="<?php echo $rate1; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $rate1; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $rate1; ?>%</span>
																	</div>
																<?php } elseif (($rate1 > 15) && ($rate1 <= 50)){ ?>
																	<div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $rate1; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $rate1; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $rate1; ?>%</span>
																	</div>
																<?php } elseif ($rate1 > 50) { ?>
																	<div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $rate1; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $rate1; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $rate1; ?>%</span>
																	</div>
																
																<?php } ?>
																	
																</div>
															</td>
															
														</tr>
														<tr><tr class="active" style="font-size:12px;color:#CC0000"> </tr>
														<tr >
															<th scope="row">1</th>
															<td>Total Qty Ordered</td>
															<td align="right"><?php  echo $qty_ord ;?> units</td>
															
															<td>
																
															</td>
														</tr>
														<tr>
															<th scope="row">2</th>
															<td>Total Qty Received</td>
															<td align="right"><?php  echo $qty_rcv ;?> units</td>
															
														</tr>
														<tr>
															<th scope="row"></th>
															<td colspan="2" align="right" style="font-size:12px;color:#CC0000"><b>Non-Receiving Product Rate (Products) :</b></td>
															
															<?php if($qty_ord != 0 ) { $rate2 = $qty_rcv*100/$qty_ord; $rate2= number_format($rate2,0); } else { $rate2 = 0; } ?>
															<td>
																<div class="progress progress-lg">
																<?php if ($rate2 <= 10) { ?>
																	<div class="progress-bar progress-bar-success progress-bar-striped active"  role="progressbar" aria-valuenow="<?php echo $rate2; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $rate2; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $rate2; ?>%</span>
																	</div>
																<?php } elseif (($rate2 > 10) && ($rate2 <= 50)){ ?>
																	<div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $rate2; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $rate2; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $rate2; ?>%</span>
																	</div>
																<?php } elseif ($rate2 > 50) { ?>
																	<div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $rate2; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $rate2; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $rate2; ?>%</span>
																	</div>
																
																<?php } ?>
																	
																</div>
															</td>
														</tr>
														
													</tbody>
												</table>
											</div>
										</div>
									</div>
								
									
								</div>
								<div class="col-md-6">
									<div class="panel panel-white">
									<!-- General chart -->
											<div class="panel-body">
												<div id="piechart"></div>
											</div>
									</div>
									
							</div>
							</div>
							
							<!-- SECOND ROW --->
							<div class="row">
								<div class="col-md-12">
								<div class="alert alert-info" ><h4>Top 5 - Most Purchased Products</h4></div>
								</div>
								<div class="col-md-6">
									
								
									<div class="panel panel-white">
									<!-- Warehouse Movement chart -->
										 <div id="chart_div"></div>
									</div>
								</div>
								
								<div class="col-md-6">
									
									<div class="panel panel-white">
									<!-- Warehouse Movements Data -->
									<div class="panel-body">
											
												<div class="table-responsive">
												<table class="table">
													<thead>
														<tr>
															<th>#</th>
															<th>Product Code</th>
															<th>Product Name</th>
															<th>Quantity</th>
															<th>Percentage</th>
														</tr>
													</thead>
													<tbody>
														<?php $order->top5_purshased_products($_SESSION['warehouse_id']); ?>
													</tbody>
												</table>
											</div>
										</div>
									
									</div>
								</div>
							</div>
							<!-- THIRD ROW --->
							<script>
								$('#supplier_id').bind('change', function () {
									//post
									$("#myform").submit();
								});
							</script>
							
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info" ><h4>Orders Per Supplier</h4></div>
								</div>
								<div class="col-md-12">
									<div class="panel panel-white">
										<div class="panel-body">
											<form action="orderreports.php#history" class="form-horizontal" method='POST' name='myform'>
												<div class="form-group" >
														<label class="col-sm-3 control-label" style="text-align:center">Choose Supplier Name from the List :</label>
														<div class="col-sm-8">
														<select name="supplier_id" id="supplier_id"  class="form-control" onchange="this.form.submit();" >
																<option value="">-- Select Supplier Name --</option>
																<?=$supplier->supplier_options($supplier->supplier_id); ?>	
														</select>
														</div>
												</div>
											</form>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									
									<div class="panel panel-white" id="history">
									<!-- Warehouse Movements Data -->
									<div class="panel-body" >
									
												<div class="table-responsive">
												<table class="table">
													<thead>
														<tr>
															<td colspan="6" style="color:#CC0000;font-size:16px">
																<b><?php if (isset($_POST['supplier_id'])) { echo 'Orders of the Supplier : '. $supplier->get_supplier_info($_POST['supplier_id'],'full_name'); }  ?></b>
														</tr>
														<tr>
															<th>#</th>
															<th>Order N#</th>
															<th>Date</th>
															<th>Warehouse</th>
															<th>Supplier</th>
															<th>Delivery Date</th>
															<th>Items</th>
															<th>Details</th>
															<th>Approved</th>
															<th></th>
															<th>Received</th>
														</tr>
													</thead>
													<tbody>
														
														<?php 
														if (isset($_POST['supplier_id'])) {
															$order->list_all_orders($_SESSION['warehouse_id'], $_POST['supplier_id']);
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
        <script src="../../assets/plugins/chartjs/chart.min.js"></script>
		
        <script src="../../assets/plugins/d3/d3.min.js"></script>
        <script src="../../assets/plugins/nvd3/nv.d3.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.pie.min.js"></script>
        <script src="../../assets/js/space.min.js"></script>
        <script src="../../assets/js/pages/chart.js"></script>
		<!-- Javascripts -->
        
        <script src="../../assets/plugins/datatables/js/jquery.datatables.min.js"></script>
        <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script src="../../assets/js/pages/table-data.js"></script>
		<!--script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>-->
		
		
    
</body>               
