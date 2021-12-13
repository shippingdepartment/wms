<?php
	include('../system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$transfer = new Transfer;
	$warehouse_access = new WarehouseAccess;
	$warehouse = new Warehouse;
	$user = new Users;
	
	$tid =$_GET['transfer_id'];	
	
	
	
	
?>	
<html>
	<head>
    	<title>Transfer Reception Sheet</title>
        <link rel="stylesheet" type="text/css" media="all" href="reports.css" />
		<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
        <link href="../../../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="HandheldFriendly" content="true" />
		<meta name="MobileOptimized" content="width" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=2.0, user-scalable=no" />
		<style>
			
		@media print {
			#printButton {
				display: none;
			}
		}
		
		</style>
    </head>
    
    <body  >
    	<div id="reportContainer"   >
			<table width="100%" cellpadding="10px" border="0px" style="font-size:13px">
				<?php //$order_id = $delivery->get_delivery_info($did, 'BD'); ?>
            	<?php //$order_id = $order->get_bdnum_info($bdid, 'order_id'); ?>
				<tr>
					<td width="40%">
					</td>
					<td width="60%" align="left"> <h2 style="color:#CCC;"><b>Transfer Reception Sheet N# <?php echo $tid; ?></b></h2> <h4> <!--(Ordre N&deg;# <?php //echo $order_id; ?>) - <?php //echo $store->get_store_info($sid, 'store_name'); ?>--> </h4></td>
				</tr>
			</table>
			<?php 
			$source_id = $transfer->get_transfer_info($tid,'warehouse_id');
			$destination_id = $transfer->get_transfer_info($tid,'destination_id');
			?>
        	<table width="100%" cellpadding="5px" border="0px" style="font-size:13px">
				<tr style="outline: thin solid">
                	<td colspan="2"style="text-align:left;" width="50%">
                    	<h4> <u>From Warehouse</u></h4>
						<h4><?php echo $warehouse->get_warehouse_info($source_id, 'name'); ?></h4>
						<p class="phone"><u>Phone: </u><?php echo $warehouse->get_warehouse_info($source_id, 'contact'); ?><br />
                        <u>Address: </u><?php echo $warehouse->get_warehouse_info($source_id, 'address'); ?> <?php echo $warehouse->get_warehouse_info($source_id, 'city'); ?> <?php echo $warehouse->get_warehouse_info($source_id, 'state'); ?> <?php echo $warehouse->get_warehouse_info($source_id, 'country'); ?><br>
						
                        </p>
                    </td>
					<td colspan="2"style="text-align:left;" width="50%">
                    	<h4> <u>To Warehouse</u></h4>
						<h4><?php echo $warehouse->get_warehouse_info($destination_id, 'name'); ?></h4>
						<p class="phone"><u>Phone: </u><?php echo $warehouse->get_warehouse_info($destination_id, 'contact'); ?><br />
                        <u>Address: </u><?php echo $warehouse->get_warehouse_info($destination_id, 'address'); ?> <?php echo $warehouse->get_warehouse_info($destination_id, 'city'); ?> <?php echo $warehouse->get_warehouse_info($destination_id, 'state'); ?> <?php echo $warehouse->get_warehouse_info($destination_id, 'country'); ?><br>
						
                        </p>
                    </td>
                    
                </tr>
				<tr>
				</tr>
				
				
            	<tr style="outline: thin solid">
					<td width="20%" valign="top">   
						
						<u>Transfer N# </u><br/>
						<u>Transfer Date   </u><br/>
						<u>Delivery Date </u></br>
					</td>	
					<td valign="top">
						
						<?php $mysqldate = strtotime($transfer->get_transfer_info($tid, 'datetime')); ?>
						<?php $agent_id = $transfer->get_transfer_info($tid, 'agent_id'); ?>
						<?php $approvedate = strtotime($transfer->get_transfer_details_info($tid, 'date_approve')); ?>
						<?php //$delivery = strtotime($transfer->get_transfer_details_info($tid, 'delivery')); ?>
                    	 
						<?php echo ': '.$tid; ?> <br/>
						 <?php echo ': '.date('d-M-Y', $approvedate); ?>    <br/>
						 <?php //if($delivery !="" ) {echo ': '.date('d-M-Y', $delivery); } else { echo ': ND'; }?> <br>
						 
                        </p>
                        
                    </td>
                	<td width="20%" valign="top">
                    	
						<u>Reception N# </u></br>
						<u>Reception Date</u></br>
						<u>Received By</u></br>
                       
                    </td>
                    
					<td valign="top">
						
						
						<?php $agent_id = $transfer->get_transfer_details_info($tid, 'agent_id'); ?>
                    	 <?php //$sid = $transfer->get_transfer_details_info($tid, 'store_id'); ?>
						 <?php $date_br = strtotime($transfer->get_reception_details_info($tid, 'date_reception')); ?>
						 <?php $received_by = $transfer->get_reception_details_info($tid, 'agent_id'); ?>
						 <?php $agent_name = $user->get_user_info($received_by, 'first_name').' '.$user->get_user_info($received_by, 'last_name') ; ?>
						 
						<?php echo ': '.$tid; ?> <br/>
						 <?php echo ': '.date('d-M-Y', $date_br); ?>    <br/>
						 <?php echo ': '.$agent_name; ?> <br>
						 
                        </p>
                        
                    </td>
                </tr>
				<tr>
					<td colspan="4" align="right">
					<button id="printButton" class="btn btn-info" onClick="window.print();"><i class="fa fa-print"></i> Print</button>
					</td>
				</tr>
            </table>
            <br />


<table width="100%" cellpadding="5px" cellspacing="0" border="1" style="font-size:13px">
	<tr bgcolor="#CCCCCC">
    	<th>Product Code</th>
        <th>Product Name</th>
        <!--<th>Cost</th>-->
        <th>Delivered Qty</th>
		<th>Received Qty</th>
		<th>Notes</th>
        <!--<th width="75">Total</th>-->
    </tr>
    <?php echo $transfer->received_transfer_invoice($tid); ?>
    
</table>

<table width="100%" cellpadding="5px" cellspacing="0" align="right" style="font-size:13px">
	<tr>
    	<td>
        	<strong>Remark:</strong><br />
            <div style="width:100%; min-height:70px; border:1px solid #000; padding:5px;"></div>
			
        </td>
       
    </tr>
</table >
<div style="clear:both;"></div>
	<table width="100%" cellpadding="5px" cellspacing="0" style="font-size:13px">
		<tr>
			<td><br><b> Signature of Receiver</b> </td>
			<td><br><b> Signature of the Carrier Agent</b></td>
			<td><br><b> Signature of Stock Manager (Source Warehouse)</b></td>
		</tr>
	</table>
	<div class="footer-div" >
			<h3 class="panel-title">
				<small>
					<b>&copy; Copyright <?php echo date('Y'); ?> - All Rights Reserved - <span class="text-info"><?php echo 'MYWAREHOUSE - V '.script_version() ?></span></b>
				</small>
			</h3>
		</div>
</div>
</body>
</html>