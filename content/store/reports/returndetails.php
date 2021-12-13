<?php
	include('../system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$delivery = new Delivery;
	$transfer = new Transfer;
	$warehouse_access = new WarehouseAccess;
	$warehouse = new Warehouse;
	$user = new Users;

?>	
<html>
	<head>
    	<title>Return Note</title>
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
        	<table width="100%" cellpadding="10px" border="0px">
            	<tr>
                	<td style="text-align:left;">
                    	<h2><?php echo $warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'name'); ?></h2>
                        <p class="phone">Tel: <?php echo $warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'contact'); ?><br />
                        Address: <?php echo $warehouse->get_warehouse_info($_SESSION['warehouse_id'], 'address'); ?> <br>
						<?php //echo $warehouse->get__info($_SESSION['store_id'], 'email'); ?>
                        </p>
                    </td>
                    <td style="text-align:right;">
                    	<h1 style="color:#CCC;">Return Note</h1>
                        <?php $mysqldate = strtotime($delivery->get_return_info($_GET['rid'], 'return_date')); ?>
                        <p>Date: <?php echo date('d-M-Y', $mysqldate); ?><br />
                        Return N# : <?php echo $_GET['rid']; ?><br />
                        <?php 
                        $did = $delivery->get_return_info($_GET['rid'], 'delivery_id');
                        $tid = $delivery->get_return_info($_GET['rid'], 'transfer_id');
                        if ($did==0) {
                            $reference = 'Transfer N# '.$tid;
                        } else {
                            $reference = 'Delivery N# '.$did;
                        }
                        ?>
                        Reference : <?php echo $reference ; ?><br />
                        <?php $agent_id = $delivery->get_return_info($_GET['rid'], 'user_id'); ?>
                        Created By: <?php echo $user->get_user_info($agent_id, 'first_name').' '.$user->get_user_info($agent_id, 'last_name'); ?><br>
						<?php 
						if ($did==0) {
                            $from_id = $transfer->get_transfer_info($tid, 'destination_id');
                            $warh_name = $warehouse->get_warehouse_info($from_id, 'name');
                            $from = ' Warehouse ('.$warh_name.')';
                        } else {
                            $from_id = $delivery->get_delivery_info($did, 'client_id');
                            $client_name = $client->get_client_info($from_id, 'full_name');
                            $from = ' Customer ('.$client_name.')';
                        }
						
						?>
						Return from : <?php echo $from; ?><br>
						
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
<?php $return_detail = $delivery->view_return_invoice($_GET['rid']); ?>
<table width="100%" cellpadding="5px" cellspacing="0" border="1">
	<tr bgcolor="#CCCCCC">
    	<th>Product ID</th>
        <th>Product Name</th>
        <th>Qty Ret.</th>
        <th>Qty Damaged</th>
         <th>Return Reason</th>
        <!--<th width="75">Total</th>-->
    </tr>
    <?php echo $return_detail['rows']; ?>
    
</table>
<div style="clear:both;"></div>
	<table width="100%" cellpadding="5px" cellspacing="0" border="0">
		<tr>
			<td align="left">
				<p style="margin-top:20px;">Signature Warehouse Agent</p>
			</td>
			<td align="left">
				<p style="margin-top:20px;">Signature Warehouse Manager</p>
			</td>
			<td align="right">
				<p style="margin-top:20px;">Signature + Name Sender</p>
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