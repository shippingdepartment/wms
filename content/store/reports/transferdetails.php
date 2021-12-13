<?php
	include('../system_load.php');
	authenticate_user('subscriber');
	
	$transfer = new Transfer;
	$warehouse_access = new WarehouseAccess;
	$warehouse = new Warehouse;
	$user = new Users;
	
?>	
<html>
	<head>
    	<title>Transfer Sheet</title>
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
    	<div id="reportContainer">
        	<table width="100%" cellpadding="10px" border="0px" style="font-size:13px">
            	<tr>
                	<td style="text-align:left;">
                    	<h2><?php echo $warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'name'); ?></h2>
                        <p class="phone">Tel: <?php echo $warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'contact'); ?><br />
                        Address: <?php echo $warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'address'); ?> <br>
						<?php //echo $warehouse->get__info($_SESSION['store_id'], 'email'); ?>
                        </p>
                    </td>
                    <td style="text-align:right;">
                    	<h1 style="color:#CCC;">Transfer Sheet</h1>
                        <?php $mysqldate = strtotime($transfer->get_transfer_info($_GET['tid'], 'datetime')); ?>
                        <p>Date: <?php echo date('d-M-Y', $mysqldate); ?><br />
                        Transfer # : <?php echo $_GET['tid']; ?><br />
                        <?php $agent_id = $transfer->get_transfer_info($_GET['tid'], 'agent_id'); ?>
                        Created By: <?php echo $user->get_user_info($agent_id, 'first_name').' '.$user->get_user_info($agent_id, 'last_name'); ?><br>
						<?php $destination = $transfer->get_transfer_info($_GET['tid'], 'destination_id'); ?>
						Destination#: <?php echo $warehouse->get_warehouse_info($destination, 'name'); ?><br>
						<!--Payment Type: <?php //echo $purchase->get_purchase_info($_GET['purchase_id'], 'payment_status'); ?>-->
                        </p>
                        
                    </td>
                </tr>
				<tr>
					<td colspan="2" align="right">
					<button id="printButton" class="btn btn-info" onClick="window.print();"><i class="fa fa-print"></i> Print</button>
					</td>
				</tr>
            </table>
            <br />
<?php $transfer_detail = $transfer->view_transfer_invoice($_GET['tid']); ?>
<table width="100%" cellpadding="5px" cellspacing="0" border="1" style="font-size:13px">
	<tr bgcolor="#CCCCCC">
    	<th>Product ID</th>
        <th>Product Name</th>
        <!--<th>Cost</th>-->
        <th>Qty</th>
        <!--<th width="75">Total</th>-->
    </tr>
    <?php echo $transfer_detail['rows']; ?>
    
</table>
<div style="clear:both;"></div>
	<table width="100%" cellpadding="5px" cellspacing="0" border="0" style="font-size:13px">
		<tr>
			<td align="left">
				<p style="margin-top:20px;">Signature Warehouse Agent</p>
			</td>
			<td align="left">
				<p style="margin-top:20px;">Signature Warehouse Manager</p>
			</td>
			<td align="right">
				<p style="margin-top:20px;">Signature + Name Receiver</p>
			</td>
		</tr>
	</table>
	<div class="footer-div" >
			<h3 class="panel-title">
				<small>
					<b>&copy; Copyright <?php echo date('Y'); ?> - All Rights Reserved - <span class="text-info"><?php echo 'MYWAREHOUSE - V '.script_version() ?></span></b>
				</small>
			</h3>
		</div>
</div><!--reportContainer Ends here.-->
</body>
</html>