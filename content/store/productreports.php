<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$warehouse = new Warehouse;
	$product = new Product;
	$delivery = new Delivery;
	
	$user_id = $_SESSION['user_id'];
	$function_id = $user->get_user_info($user_id,"user_function");
	
	if(partial_access('admin') || $warehouse_access->have_module_access('products')) {} else { 
		HEADER('LOCATION: warehouse.php?message=products');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} 
	
	$warehouses->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'Product Reports';

	// Quantities and Percentages
	 $prod = $product->num_products($_SESSION['warehouse_id']);
	 $alert_qty = $product->products_alert_stock($_SESSION['warehouse_id']);
	 $out_qty = $product->products_out_stock($_SESSION['warehouse_id']);
	 $dmg_qty = $product->damaged_products($_SESSION['warehouse_id']);
	 $total_qty = $product->total_qty($_SESSION['warehouse_id']);
	/* $dmg_qty = $dmg_qty ;
	 $out_qty = $out_qty;
	 $alert_qty = $alert_qty ;*/
	
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
						['Product Division in Warehouse', 'Product Division', { role: 'style' }, { role: 'annotation' }],
						<?php
							echo "['Total Quantity', ".$total_qty.", '#0d47a1','Total Qty'],";
							echo "['Out of Stock', ".$out_qty.", '#CC0000','Out of Stock'],";
							echo "['Alert Qty', ".$alert_qty.", '#FF8800','Alert Qty'],";
							echo "['Damaged Qty', ".$dmg_qty.", '#007E33','Damaged Qty'],";
						?>
						
					  ]);
					  

					  var options = {
						title: 'Warehouse Volume ',
						width: 520,
						height: 365,
						
					};
					  var materialChart = new google.charts.Bar(document.getElementById('piechart'));
					  materialChart.draw(data, options);
					}
					
					
				function drawChart() {

					var data = google.visualization.arrayToDataTable([
					  ['Label Zone', 'Selling Quantity'],
					  <?php
							$data_chart = $delivery->top10_delivered_products_chart($_SESSION['warehouse_id']);
							echo $data_chart;
					  ?>
					]);
					
					var options = {
						title: 'TOP 10 Best Selling Products',
						chartArea: {width: '60%'},
						colors: ['#0cb262', '#ffab91'],
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
															<th width="230px"></th>
														</tr>
													</thead>
													<tbody>
													<?php if($total_qty > 0 ) { $perc_alert = $alert_qty*100/$total_qty; $perc_alert= number_format($perc_alert,2);} else { $perc_alert =0;} ?>
													<?php if($total_qty > 0 ) { $perc_out = $out_qty*100/$prod; $perc_out= number_format($perc_out,2); } else { $perc_out =0;}  ?>
														<tr class="active" style="font-size:12px;color:#CC0000">
															<td scope="row"></td>
															<td><b>N# Products</b></td>
															<td align="right"><b><?php  echo $prod;?> products</td></b>
															<td >
															</td>
														</tr>
														<tr>
															<th scope="row">1</th>
															<td>Alert Stock</td>
															<td align="right"> <?php echo $alert_qty.' products'; ?></td>
															
															<td>
															
																<div class="progress progress-lg">
																<?php if ($perc_alert <= 25) { ?>
																	<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $perc_alert; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_alert; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_alert; ?>%</span>
																	</div>
																<?php } elseif (($perc_alert > 25) && ($perc_alert <= 65)){ ?>
																	<div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $perc_alert; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_alert; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_alert; ?>%</span>
																	</div>
																
																<?php } elseif ($perc_alert > 65){ ?>
																	<div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $perc_alert; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_alert; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_alert; ?>%</span>
																	</div>
																<?php } ?>
																	
																</div>
															</td>
														</tr>
														<tr>
															<th scope="row">2</th>
															<td>Out of Stock</td>
															<td align="right"><?php  echo $out_qty;?> products</td>
															<td>
																
																<div class="progress progress-lg">
																<?php if ($perc_out <= 15) { ?>
																	<div class="progress-bar progress-bar-success progress-bar-striped active"  role="progressbar" aria-valuenow="<?php echo $perc_out; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_out; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_out; ?>%</span>
																	</div>
																<?php } elseif (($perc_out > 15) && ($perc_out <= 50)){ ?>
																	<div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $perc_out; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_out; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_out; ?>%</span>
																	</div>
																<?php } elseif ($perc_out > 50) { ?>
																	<div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $perc_out; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_out; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_out; ?>%</span>
																	</div>
																
																<?php } ?>
																	
																</div>
															</td>
															
														</tr>
														<tr class="active" style="font-size:12px;color:#CC0000">
															<th scope="row"></th>
															<td><b>Total Quantity</b></td>
															<td align="right"><b><?php $qty_tt = $total_qty + $dmg_qty; echo $qty_tt ;?> units</b></td>
															
															<td>
																
															</td>
														</tr>
														<tr>
															<th scope="row">1</th>
															<td>On Hand</td>
															<td align="right"><?php $onhand = $qty_tt - $dmg_qty ; echo $onhand ;?> units</td>
															<?php if($total_qty != 0 ) { $perc_onhand = $onhand*100/$qty_tt; $perc_onhand= number_format($perc_onhand,2); } else { $perc_onhand = 0; } ?>
															
															<td>
																<div class="progress progress-lg">
																<?php if ($perc_onhand <= 15) { ?>
																	<div class="progress-bar progress-bar-danger progress-bar-striped active"  role="progressbar" aria-valuenow="<?php echo $perc_onhand; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_onhand; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_onhand; ?>%</span>
																	</div>
																<?php } elseif (($perc_onhand > 20) && ($perc_onhand <= 55)){ ?>
																	<div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $perc_onhand; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_onhand; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_onhand; ?>%</span>
																	</div>
																<?php } elseif ($perc_onhand > 55) { ?>
																	<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $perc_onhand; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_onhand; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_onhand; ?>%</span>
																	</div>
																<?php } ?>
																	
																</div>
															</td>
														</tr>
														<tr>
															<th scope="row">2</th>
															<td>Quantity Damaged</td>
															<td align="right"><?php  echo $dmg_qty ;?> units</td>
															<?php if($total_qty != 0 ) { $perc_dmg = $dmg_qty*100/$total_qty; $perc_dmg= number_format($perc_dmg,2); } else { $perc_dmg = 0; } ?>
															<td>
																<div class="progress progress-lg">
																<?php if ($perc_dmg <= 10) { ?>
																	<div class="progress-bar progress-bar-success progress-bar-striped active"  role="progressbar" aria-valuenow="<?php echo $perc_dmg; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_dmg; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_dmg; ?>%</span>
																	</div>
																<?php } elseif (($perc_dmg > 10) && ($perc_dmg <= 50)){ ?>
																	<div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $perc_dmg; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_dmg; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_dmg; ?>%</span>
																	</div>
																<?php } elseif ($perc_dmg > 50) { ?>
																	<div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $perc_dmg; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc_dmg; ?>%;">
																		<span style="font-size:11px;color:#0d47a1"><?php echo $perc_dmg; ?>%</span>
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
								<div class="alert alert-info" ><h4>Top 10 - Best Selling Products</h4></div>
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
														<?php $delivery->top10_delivered_products($_SESSION['warehouse_id']); ?>
													</tbody>
												</table>
											</div>
										</div>
									
									</div>
								</div>
							</div>
							<!-- THIRD ROW --->
							<script>
								$('#product_id').bind('change', function () {
									//post
									$("#myform").submit();
								});
							</script>
							
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info" ><h4>Product History</h4></div>
								</div>
								<div class="col-md-12">
									<div class="panel panel-white">
										<div class="panel-body">
											<form action="productreports.php#history" class="form-horizontal" method='POST' name='myform'>
												<div class="form-group" >
														<label class="col-sm-3 control-label" style="text-align:center">Choose Product from the List :</label>
														<div class="col-sm-8">
														<select name="product_id" id="product_id"  class="form-control" onchange="this.form.submit();" >
																<option value="">-- Select Product --</option>
																<?php $products->product_names($products->product_id); ?>
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
																<b><?php if (isset($_POST['product_id'])) { echo $product->get_product_info($_POST['product_id'],'product_name'); }  ?></b>
														</tr>
														<tr>
															<th>#</th>
															<th>Date</th>
															<th>Product Name</th>
															<th>Operation</th>
															<th>Qty</th>
															<th>Reference</th>
														</tr>
													</thead>
													<tbody>
														
														<?php 
														if (isset($_POST['product_id'])) {
															$product->product_mvt($_POST['product_id']);
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
