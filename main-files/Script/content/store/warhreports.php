<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$warehouse = new Warehouse;
	$product = new Product;
	
	$user_id = $_SESSION['user_id'];
	$function_id = $user->get_user_info($user_id,"user_function");
	
	if(partial_access('admin') || $warehouse_access->have_module_access('products')) {} else { 
		HEADER('LOCATION: warehouse.php?message=products');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} 
	
	$warehouses->set_warehouse($_SESSION['warehouse_id']); //setting store.
	 
	$page_title = 'Warehouse Reports'; 
	
?>
<?php
 $vol = floatval($warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'volume'));

 $occ_volume = floatval($warehouse->occuped_volume($_SESSION['warehouse_id']));
 $sec_area = floatval($warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'freezone'));
 $free_vol = floatval($warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'volume')) - ($occ_volume + $sec_area);
 //WAREHOUSE OCCUPANCY PERCENTAGE
 if(floatval($warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'volume')) >0 ) {
	$per_occ = ($occ_volume)*100/(floatval($warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'volume')));
 } else {  
	$per_occ = 0;  
 }
 //WAREHOUSE SECURITY VOLUME PERCENTAGE / FREE VOLUME PERCENTAGE
 if($vol >0) {
 $per_sec = $sec_area*100/$vol;
 $per_free = $free_vol*100/$vol;
 } else { 
	$per_sec = 0; 
	$per_free = 0 ;
 }
 //WAREHOUSE COST
 $cost_warehouse = floatval($warehouse->warehouse_cost($_SESSION['warehouse_id']));
 
 //WAREHOUSE MOVEMENTS DURING LAST 6 MONTHS
 /* 1-  MONTHS */
$date = new DateTime();
$date6= $date->format('F Y');
$month6=$date->format('m');
$year6=$date->format('Y');
$days6=cal_days_in_month(CAL_GREGORIAN, $month6, $year6);
//---
$date->sub(new DateInterval('P1M'));
$date5= $date->format('F Y');
$month5=$date->format('m');
$year5=$date->format('Y');
$days5=cal_days_in_month(CAL_GREGORIAN, $month5, $year5);
//---
$date->sub(new DateInterval('P1M'));
$date4= $date->format('F Y');
$month4=$date->format('m');
$year4=$date->format('Y');
$days4=cal_days_in_month(CAL_GREGORIAN, $month4, $year4);
//---
$date->sub(new DateInterval('P1M'));
$date3= $date->format('F Y');
$month3=$date->format('m');
$year3=$date->format('Y');
$days3=cal_days_in_month(CAL_GREGORIAN, $month3, $year3);
//---
$date->sub(new DateInterval('P1M'));
$date2= $date->format('F Y');
$month2=$date->format('m');
$year2=$date->format('Y');
$days2=cal_days_in_month(CAL_GREGORIAN, $month2, $year2);
//---
$date->sub(new DateInterval('P1M'));
$date1= $date->format('F Y');
$month1=$date->format('m');
$year1=$date->format('Y');
$days1=cal_days_in_month(CAL_GREGORIAN, $month1, $year1);
//---
$firstdate6 = $month6.'/1/'.$year6 ;
$enddate6 = $month6.'/'.$days6.'/'.$year6 ;
//---
$firstdate5 = $month5.'/1/'.$year5 ;
$enddate5 = $month5.'/'.$days5.'/'.$year6 ;
//---
$firstdate4 = $month4.'/1/'.$year4 ;
$enddate4 = $month4.'/'.$days4.'/'.$year4 ;
//---
$firstdate3 = $month3.'/1/'.$year3 ;
$enddate3 = $month3.'/'.$days3.'/'.$year3 ;
//---
$firstdate2 = $month2.'/1/'.$year2 ;
$enddate2 = $month2.'/'.$days2.'/'.$year2 ;
//---
$firstdate1 = $month1.'/1/'.$year1 ;
$enddate1 = $month1.'/'.$days1.'/'.$year1 ;
//---

/* 2-  QUANTITIES */
$qtyin2 = 0;
 $qtyin6 = $warehouse->get_inventory_inn($_SESSION['warehouse_id'], $firstdate6, $enddate6);
 $qtyout6 = $warehouse->get_inventory_out($_SESSION['warehouse_id'], $firstdate6, $enddate6);
 
 //--
 $qtyin5 = $warehouse->get_inventory_inn($_SESSION['warehouse_id'], $firstdate5, $enddate5);
 $qtyout5 = $warehouse->get_inventory_out($_SESSION['warehouse_id'], $firstdate5, $enddate5);
 //--
 $qtyin4 = $warehouse->get_inventory_inn($_SESSION['warehouse_id'], $firstdate4, $enddate4);
 $qtyout4 = $warehouse->get_inventory_out($_SESSION['warehouse_id'], $firstdate4, $enddate4);
 //--
 $qtyin3 = $warehouse->get_inventory_inn($_SESSION['warehouse_id'], $firstdate3, $enddate3);
 $qtyout3 = $warehouse->get_inventory_out($_SESSION['warehouse_id'], $firstdate3, $enddate3);
 //--
 $qtyin2 = $warehouse->get_inventory_inn($_SESSION['warehouse_id'], $firstdate2, $enddate2);
 $qtyout2 = $warehouse->get_inventory_out($_SESSION['warehouse_id'], $firstdate2, $enddate2);
 
 //--
 $qtyin1 = $warehouse->get_inventory_inn($_SESSION['warehouse_id'], $firstdate1, $enddate1);
 $qtyout1 = $warehouse->get_inventory_out($_SESSION['warehouse_id'], $firstdate1, $enddate1);
 //--
 
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
		
				<script type="text/javascript">
				google.charts.load('current', {'packages':['corechart']});
				google.charts.load('current', {packages: ['corechart', 'bar']});
				google.charts.setOnLoadCallback(drawChart);
				google.charts.setOnLoadCallback(drawBarColors);

				function drawChart() {

					var data = google.visualization.arrayToDataTable([
					  ['Label Zone', 'Volume'],
					  <?php
							echo "['Free Zone', ".$free_vol."],";
							echo "['Security Area', ".$sec_area."],";
							echo "['Occuped Areaa', ".$occ_volume."],";
					  ?>
					]);
					
					var options = {
						title: 'Warehouse Volume ',
						width: 520,
						height: 297,
						
					};
					
					var chart = new google.visualization.PieChart(document.getElementById('piechart'));
					
					chart.draw(data, options);
				}
				
				function drawBarColors() {
					  var data = google.visualization.arrayToDataTable([
						['Date', 'Products Entered', 'Products Delivered'],
						<?php
							echo "['".$date6."', ".$qtyin6.", ".$qtyout6."],";
							echo "['".$date5."', ".$qtyin5.", ".$qtyout5."],";
							echo "['".$date4."', ".$qtyin4.", ".$qtyout4."],";
							echo "['".$date3."', ".$qtyin3.", ".$qtyout3."],";
							echo "['".$date2."', ".$qtyin2.", ".$qtyout2."],";
							echo "['".$date1."', ".$qtyin1.", ".$qtyout1."],";
						?>
					  ]);

					  var options = {
						title: 'Movemets of Warehouse during last 6 months',
						chartArea: {width: '60%'},
						colors: ['#b0120a', '#ffab91'],
						hAxis: {
						  title: 'Quantities',
						  minValue: 0
						},
						width: 520,
						height: 297,
						vAxis: {
						  title: 'Months'
						}
					  };
					  var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
					  chart.draw(data, options);
    }
				</script>
				

        
		
	
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
														</tr>
													</thead>
													<tbody>
														<tr>
															<th scope="row">1</th>
															<td>Total Qty</td>
															<td align="right"><?php echo $product->total_qty($_SESSION['warehouse_id']);  ?> PCs</td>
														</tr>
														<tr>
															<th scope="row">2</th>
															<td>Warehouse Volume</td>
															<td align="right"><?php  echo number_format($vol,2);?> m<sup>3</td>
														</tr>
														<tr>
															<th scope="row">3</th>
															<td>Occuped Space</td>
															<td align="right"><?php  echo number_format($occ_volume,2);?> m<sup>3</td>
														</tr>
														<tr>
															<th scope="row">4</th>
															<td>Security Area</td>
															<td align="right"><?php  echo number_format($sec_area,2);?> m<sup>3</td>
														</tr>
														<tr>
															<th scope="row">5</th>
															<td>Free Volume</td>
															<td align="right"><?php  $free_vol = number_format($free_vol,2); echo $free_vol ;?> m<sup>3</td>
														</tr>
														<tr>
															<th scope="row">6</th>
															<td>Actual Cost</td>
															<td align="right"> <?php echo number_format($cost_warehouse,2).' $'; ?></td>
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
								<div class="alert alert-info" ><h4>Warehouse Movements during last 6 months</h4></div>
								</div>
								<div class="col-md-6">
									
								
									<div class="panel panel-white">
									<!-- Warehouse Movement chart -->
										 <div id="chart_div"></div>
									</div>
								</div>
								<?php
 
									 
									?>
								<div class="col-md-6">
									
									<div class="panel panel-white">
									<!-- Warehouse Movements Data -->
									<div class="panel-body">
											
												<div class="table-responsive">
												<table class="table">
													<thead>
														<tr>
															<th>#</th>
															<th>Date</th>
															<th>Quantity Entered</th>
															<th>Quantity Delivered</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<th scope="row">1</th>
															<td><?php echo $date6;?></td>
															<td align="right"><?php echo $qtyin6.' PCs'; ?></td>
															<td align="right"><?php echo $qtyout6.' PCs'; ?></td>
														</tr>
														<tr>
															<th scope="row">2</th>
															<td><?php echo $date5;?></td>
															<td align="right"><?php echo $qtyin5.' PCs'; ?></td>
															<td align="right"><?php echo $qtyout5.' PCs'; ?></td>
														</tr>
														<tr>
															<th scope="row">3</th>
															<td><?php echo $date4;?></td>
															<td align="right"><?php echo $qtyin4.' PCs'; ?></td>
															<td align="right"><?php echo $qtyout4.' PCs'; ?></td>
														</tr>
														<tr>
															<th scope="row">4</th>
															<td><?php echo $date3;?></td>
															<td align="right"><?php echo $qtyin3.' PCs'; ?></td>
															<td align="right"><?php echo $qtyout3.' PCs'; ?></td>
														</tr>
														<tr>
															<th scope="row">5</th>
															<td><?php echo $date2;?></td>
															<td align="right"><?php echo $qtyin2.' PCs'; ?></td>
															<td align="right"><?php echo $qtyout2.' PCs'; ?></td>
														</tr>
														<tr>
															<th scope="row">6</th>
															<td><?php echo $date1;?></td>
															<td align="right"><?php echo $qtyin1.' PCs'; ?></td>
															<td align="right"><?php echo $qtyout1.' PCs'; ?></td>
														</tr>
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
