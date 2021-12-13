<?php
	include('system_load.php');
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$warehouse = new Warehouse;
	$warehouseaccess = new WarehouseAccess;
	$transfer = new Transfer;
	$note = new Notes;
	$mess='';
	
	if (isset($_GET['tid'])){
	$tid = $_GET['tid'];
	} else {
		$tid = $_POST['tid'];
	}
	
	
	$warehouse_from = $transfer->get_transfer_info($tid,'warehouse_id');
	$warehouse->set_warehouse($_SESSION['warehouse_id']); //setting store.
	if ($warehouse_from != $_SESSION['warehouse_id']) {
		$mess='2';
		
	} else {
	if (isset($_POST['nb'])){
			$nb=$_POST['nb'];
			$datetime = date('Y-m-d');
			$return_id = $delivery->add_return_transfer($tid, $datetime);
			for ($i = 0; $i < $nb; $i++) {
				$pid = $_POST['pid'][$i];
				$qty = $_POST['qty'][$i];
				$pname = $_POST['pname'][$i];
				$qtyapp = $_POST['qte_appr'][$i];
				$cause = $_POST['cause'][$i];
				$qtydmg = $_POST['qte_dmg'][$i];
				
				$qt = $qtyapp - $qtydmg;
				$delivery->add_return_details($return_id, $pid, $qtyapp, $cause, $qtydmg);
				//Add Inventory
				$delivery->add_inventory_return($datetime, $qt, 0, $pid, $_SESSION['warehouse_id'], $return_id);
				}
				$note_title = 'New Return Received';
			    $note_details = 'New Return #: '.$return_id.'( from Transfer N#: '.$tid.')';
			    $note->add_note($note_title, $note_details );
				$mess='1';
				
				//   Redirection
				echo '<script>window.location="returns.php"</script>';
													
	}										
	}
	
	
	
	
	
	 
	$page_title = 'Stock Return from Transfer N#: '.$tid; //You can edit this to change your page title.
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
		 <link href="../../assets/plugins/x-editable/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
        <link href="../../assets/plugins/x-editable/inputs-ext/typeaheadjs/lib/typeahead.js-bootstrap.css" rel="stylesheet">
        <link href="../../assets/plugins/x-editable/inputs-ext/address/address.css" rel="stylesheet">
      
        <!-- Theme Styles -->
        <link href="../../assets/css/space.min.css" rel="stylesheet">
        <link href="../../assets/css/custom.css" rel="stylesheet">
		
		  <script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
		<style type="text/css">
textarea:hover, textarea:focus, #items td.total-value textarea:hover, #items td.total-value textarea:focus, .delme:hover { background-color:#EEFF88; }

#items input[type=text] {width:60px;border:0px;}
.delete-wpr { position: relative; }
.delme { display: block; color: #000; text-decoration: none; position: absolute; background: #EEEEEE; font-weight: bold; padding: 0px 3px; border: 1px solid; top: -6px; left: -22px; font-family: Verdana; font-size: 12px; }

input:focus
	{
	background-color:#FFFACD;
	}
	select:focus
	{
	background-color:#FFFACD;
	}
.table-responsive thead th{
    background: gray;
	color: #000000;
}
</style>  
    </head>
	<body class="page-sidebar-fixed page-header-fixed">
		<div class="page-container">
			<?php require_once("includes/sidebar.php"); //including sidebar file. ?>
            <div class="page-content">
				<?php require_once("includes/header.php"); //including sidebar file. ?>
                <!-- Page Inner -->
				<div class="page-inner">
					<div class="page-title">
                        <h3 class="breadcrumb-header"><?php echo $page_title; ?></h3>
					</div>
					<?php
						//display message if exist.
						if(isset($message) && $message != '') {
							echo $message;
						}
						elseif($mess=='1') {
							echo '<div class="alert alert-success">';
							echo 'Retur N#: '.$return_id.' created successfully ';
							echo '</div>';
							echo '<script>window.location="returns.php"</script>';
						} elseif($mess=='2') {
							echo '<div class="alert alert-danger">';
							echo 'This Transfer is not creating from this Warehouse ';
							echo '</div>';
							
						}
						
					?>
					<div class="row" >
						<div class="col-md-12">
							<div class="panel panel-white alert alert-default" >
								<div class="panel-body" >
								<form class="form-horizontal" action="newreturn.php" id="wizardForm" name="level" method="post">
									<div class="form-group">
									
										<label class="col-sm-2 control-label">Transfer N# :</label>
										<div class="col-sm-2">
											<input type="text" class="form-control" name="did"   value="<?php if($mess!='2'){ echo $tid; }?>" Disabled />
													
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Edition Date :</label>
										<div class="col-sm-2">
											<input type="text" class="form-control" name="datetime"   value="<?php if($mess!='2'){ echo $transfer->get_transfer_info($tid,'datetime'); } ?>" Disabled />
													
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Transfer Date :</label>
										<div class="col-sm-2">
											<input type="text" class="form-control" name="dateissued"   value="<?php if($mess!= '2'){ echo  $transfer->get_transfer_info($tid,'datetime'); } ?>" Disabled />
													
										</div>
									</div>
									<?php $wareh_id = $transfer->get_transfer_info($tid,'destination_id'); 
									 $warehouse = new Warehouse;
									 $warehouse_name = $warehouse->get_warehouse_info($wareh_id,'name');
									 ?>
									<div class="form-group">
										<label class="col-sm-2 control-label">Warehouse :</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="client"   value="<?php if($mess!='2'){ echo $warehouse_name;} ?>" Disabled />
													
										</div>
									</div>
								</form>
									 
									 
									<?php
										//display message if exist.
											if(isset($message) && $message != '') { 
												echo $message;
											}
											
									?>
									
									<form action="returntransfer.php?tid=<?php echo $tid; ?>" class="form-horizontal" method='POST' name='testform'>
	
										<table cellpadding="0" cellspacing="0" border="0" class="table-responsive table-hover table display table-bordered" id="example3" width="100%">
											<thead >
												<tr>
													<th width="12%"><font color="#fff">Product Code</th>
													<th width="30%"><font color="#fff">Product Name</th>
													<th width="10%"><font color="#fff">Qty Delivered</th>
													<th width="10%"><font color="#fff">Qty Returned </th>
													<th><font color="#fff">Return Reason </th>
													<th width="12%" ><font color="#fff">Qty Damaged <i class="fa fa-question-circle" title="Qty damaged from the returned quantity"></i> </th>
													
												</tr>
											</thead>
											<tbody>
												<?php if($mess!='2'){ $delivery->return_transfer_details($tid); } ?>
											</tbody>
											
										</table> 
										
										<div align="center"><button type="submit" class="btn btn-info btn-addon"><i class="fa fa-save"></i>  Approve and Save</button></div>
									</form>
    
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
		<!-- JAVASCRIPT -->
		<script>
		/* CHECK RETURNED QUANTITY */
		function checkReturnedQty(indice) {
			
			var fieldIdQty = "#qty"+indice;
			var fieldIdRet = "#qteapp"+indice;
			
			if ( parseInt($(fieldIdRet).val()) > parseInt($(fieldIdQty).val()) ) {
				alert("Returned Quantity could not be greater than Delivered Quantity");
				$(fieldIdRet).val('');
				return false;
			}
			
		}
		/* CHECK DAMAGED QUANTITY */
		function checkDamagedQty(indice) {
			
			var fieldIdDmg = "#qtedmg"+indice;
			var fieldIdRet = "#qteapp"+indice;
			if ( $(fieldIdRet).val()=='') {
				alert("You have to enter Returned Quantity before entering the Damaged Quantity");
				$(fieldIdDmg).val('');
				$(fieldIdRet).focus();
				return false;
			} else {
				if ( parseInt($(fieldIdDmg).val()) > parseInt($(fieldIdRet).val()) ) {
					alert("Damaged Quantity could not be greater than Returned Quantity");
					$(fieldIdDmg).val('');
					return false;
				}
			}
			
		}
		
		</script>
      
        <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
        <script src="../../assets/plugins/switchery/switchery.min.js"></script>
        <script src="../../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
        <script src="../../assets/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
        <script src="../../assets/js/space.min.js"></script>
		<script src="../../assets/plugins/jquery-mockjax-master/jquery.mockjax.js"></script>
        <script src="../../assets/plugins/moment/moment.js"></script>
        <script src="../../assets/js/pages/form-wizard.js"></script>
		 <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="../../assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
		<script src="../../assets/plugins/x-editable/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
        <script src="../../assets/plugins/x-editable/inputs-ext/typeaheadjs/lib/typeahead.js"></script>
        <script src="../../assets/plugins/x-editable/inputs-ext/typeaheadjs/typeaheadjs.js"></script>
        <script src="../../assets/plugins/x-editable/inputs-ext/address/address.js"></script>
        <script src="../../assets/js/pages/form-x-editable.js"></script>
		<!-- Javascripts
        
        
	</body>
</html>