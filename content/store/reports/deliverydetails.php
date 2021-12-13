<?php
	//This loads system.
	include('../system_load.php');
	//user Authentication.
	authenticate_user('subscriber');
	
	//$transfer = new Transfer;
	$warehouse_access = new WarehouseAccess;
	$warehouse = new Warehouse;
	$user = new Users;
	$delivery = new Delivery;
	$client = new Client;
?>	
<html>
	<head>
    	<title>Delivery Note</title>
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
                    	<h1 style="color:#CCC;">Delivery Note</h1>
                        <?php $mysqldate = strtotime($delivery->get_delivery_info($_GET['did'], 'datetime')); ?>
                        <p>Date: <?php echo date('d-M-Y', $mysqldate); ?><br />
                        Delivery N# : <?php echo $_GET['did']; ?><br />
						<?php $st = $delivery->get_delivery_info($_GET['did'], 'delivered'); 
							if ( $st=='0' ) {
								$status ='Not Delivered Yet';
							} elseif ( $st=='1' )  {
								$status ='Delivered';
							}
						?>
						
						Status # : <?php echo '<span style="color:CC0000"><b>'.$status.'</b></span>'; ?><br />
                        <?php $agent_id = $delivery->get_delivery_info($_GET['did'], 'user_id');  ?>
						
                        Created By: <?php echo $user->get_user_info($agent_id, 'first_name').' '.$user->get_user_info($agent_id, 'last_name'); ?><br /> 
						<?php //$destination = $transfer->get_transfer_info($_GET['oid'], 'destination_id'); 
						$code_client = $delivery->get_delivery_info($_GET['did'], 'client_id');
						$nom_client = mysqli_real_escape_string($db, $client->get_client_info($code_client, 'full_name'));
						$tel = $client->get_client_info($code_client, 'phone');
						$adress = $client->get_client_info($code_client, 'address');
						?>
						Customer#: <?php echo $code_client.' - '.$nom_client;?><br>
						Contact :<?php echo $adress.' - TEL : '.$tel;?><br>
						
                        </p>
                        
                    </td>
                </tr>
				<tr>
					<td colspan="2" align="right">
					<button id="printButton" class="btn btn-info" onClick="window.print();"><i class="fa fa-print"></i> Print</button>
					</td>
				</tr>
            </table>
            
<?php $delivery_detail = $delivery->view_delivery_invoice($_GET['did']); ?>
<table width="100%" cellpadding="5px" cellspacing="0" border="1">
	<tr bgcolor="#CCCCCC">
    	<th>Product ID</th>
        <th>Product Name</th>
        <th>Qty</th>
        <th>Total Weight</th>
		
        <!--<th width="75">Total</th>-->
    </tr>
    <?php echo $delivery_detail['rows']; ?>
    
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
				<p style="margin-top:20px;">Signature Sales Manager</p>
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