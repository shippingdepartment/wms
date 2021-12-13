<?php
	include('../system_load.php');
	authenticate_user('subscriber');
	$warehouse = new Warehouse;
	
?>
<html>
	<head>
    	<title>Inventory</title>
		<link rel="stylesheet" type="text/css" media="all" href="reports.css" />
		<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
        <link href="../../../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<style>
			
		@media print {
			#printButton {
				display: none;
			}
		}
		
		</style>
    </head>
    
<body>
	<h2><center>Warehouse Inventory</center></h2>
	
	<div id="reportContainer">
        <table width="100%" cellpadding="10px" border="0px">
			<tr>
				<td style="text-align:left;">
					Date : <?php echo date("d-m-Y - H:i:s"); ?><br/>
					Warehouse : <?php echo $warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'name'); ?> 
				</td> 
				<td colspan="2" align="right">
					<button id="printButton" class="btn btn-info" onClick="window.print();"><i class="fa fa-print"></i> Print</button>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="5px" cellspacing="0" border="1" style="font-size:13px">
			<thead>
				<tr bgcolor="#CCCCCC">
					<th>ID</th>
					<th>Description</th>
					<th>Unit</th>
					<th>Category</th>
					<th class="text-center">Alert</th>
					<th class="text-center">Qty On Hand</th>
					<?php if(partial_access('admin') || $function_id =='manager') { ?>
						<th class="text-center">Cost</th>
						<th class="text-center">Selling Price</th>
					<?php } ?>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			<?php   
				$product->list_products_inventory(); 
			?>
			</tbody>	
		</table>
		<div class="footer-div" >
			<h3 class="panel-title">
				<small>
					<b>&copy; Copyright <?php echo date('Y'); ?> - All Rights Reserved - <span class="text-info"><?php echo 'MYWAREHOUSE - V '.script_version() ?></span></b>
				</small>
			</h3>
		</div>
	</div>
	<div style="clear:both;"></div>
       <!--reportContainer Ends here.-->
</body>
</html>