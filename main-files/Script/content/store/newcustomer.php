<?php
	include('system_load.php');
	//user Authentication.
	authenticate_user('subscriber');
	$client = new Client;
	$warehouse = new Warehouse;
	$warehouse_access = new WarehouseAccess;
	
	if(isset($_POST['edit_customer'])){ $page_title = 'Edit Customer'; } else { $page_title = 'New Customer';}; //You can edit this to change your page title.
	
	
	if($_SESSION['user_type'] != "admin") {	
		if( $warehouse_access->have_module_access('products') OR $function_id!='storem' OR $function_id!='manager' )  {
			HEADER('LOCATION: warehouse.php?msg=nwcust');
		}
	}
	if(isset($_POST['add_customer'])) {
		if($_POST['add_customer'] == '1') { 
			extract($_POST);
			$message = $client->add_client( $customer_name, $customer_code, $tax_customer, $mobile, $phone, $address, $city, $state, $zipcode, $country, $email, $status,'0');
		}
	}
	if(isset($_POST['edit_customer'])) {
		extract($_POST);
		$client->set_client($edit_customer);
	}
	if(isset($_POST['update_customer']) && $_POST['update_customer'] == '1') {
		extract($_POST);
		$message = $client->update_client($edit_customer, $customer_name, $tax_customer, $mobile, $phone, $address, $city, $state, $zipcode, $country, $email, $status);
	}
	
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="MywaResponsive Warehouse Management System  ">
        <meta name="keywords" content="warehouse,system">
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
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
		
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
		</style>  
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
		
 

</head>
 <body class="page-sidebar-fixed page-header-fixed">	
<!-- Page Container -->
        <div class="page-container">
			<?php 
			require_once("includes/sidebar.php"); //including sidebar file. 
			?>
            <div class="page-content">
				<?php
				require_once("includes/header.php"); //including sidebar file.
				?>
				<div class="page-inner">
					<div class="page-title">
                        <h3 class="breadcrumb-header"><?php echo $page_title; ?></h3>
					</div>
					<?php
						//display message if exist.
							if(isset($_GET['message']) && $_GET['message'] != '') { 
								echo $_GET['message'];
							}
							if(isset($message) && $message != '') {
								echo $message;
							}
						?>
					
							<div  class="panel panel-white alert alert-default" style="font-size:14px" >
								<div class="panel-heading clearfix">
									<div class="panel-body" >
							
										<form action="newcustomer.php" method='POST' name='testform' class="form-horizontal" id="testform">
													
													<?php if(isset($_POST['edit_customer'])){ ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Customer Code </label>
														<div class="col-sm-3">	
															<input type="text" class="form-control" name="customer_code" placeholder="Customer Code" value="<?php echo $client->client_code; ?>" disabled />
														</div>
													</div>
													<?php } else { ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Customer Code </label>
														<div class="col-sm-3">	
															<input type="text" class="form-control" name="customer_code" placeholder="Customer Code" value="<?php echo $client->client_code; ?>" Required />
														</div>
													</div>
													<?php } ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Customer Name </label>
														<div class="col-sm-8">	
															<input type="text" class="form-control" name="customer_name" placeholder="Full Name" value="<?php echo $client->full_name; ?>" Required />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Registration Number </label>
														<div class="col-sm-3">
															<input type="text" class="form-control" name="tax_customer" placeholder="Registration Number" value="<?php echo $client->business_title; ?>" />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Phone Number </label>
														<div class="col-sm-3">
															<input type="text" class="form-control" name="phone" placeholder="Phone Number" onkeypress="return isNumberKey(event)" value="<?php echo $client->phone; ?>" Required />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Mobile Number </label>
														<div class="col-sm-3">
															<input type="text" class="form-control" name="mobile" placeholder="Mobile Number" onkeypress="return isNumberKey(event)" value="<?php echo $client->mobile; ?>" />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Address </label>
														<div class="col-sm-8">
															<input type="text" class="form-control" name="address" placeholder="Address" value="<?php echo $client->address; ?>" Required />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">City </label>
														<div class="col-sm-3">
															<input type="text" class="form-control" name="city" placeholder="City" value="<?php echo $client->city; ?>" />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Governerate </label>
														<div class="col-sm-3">
															<input type="text" class="form-control" name="state" placeholder="Governerate" value="<?php echo $client->state; ?>"  />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Postal Code </label>
														<div class="col-sm-3">
															<input type="text" class="form-control" name="zipcode" placeholder="Postal Code"  value="<?php echo $client->zipcode; ?>" />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Country </label>
														<div class="col-sm-8">
															<input type="text" class="form-control" name="country" placeholder="Country" value="<?php echo $client->country; ?>" Required />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Email Address </label>
														<div class="col-sm-8">
															<input type="email" class="form-control" name="email" placeholder="Email" value="<?php echo $client->email; ?>" />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Customer Status </label>
														<div class="col-sm-8">
															<select class="form-control" name="status" required>
																<option value=''>Customer Status</option>
																<option value="1" <?php if($client->status == '1') { echo 'selected="selected"'; } ?>>Active</option>
																<option value="0" <?php if($client->status == '0') { echo 'selected="selected"'; } ?>>Inactive</option>
															</select>
														</div>
													</div>
																
													<div class="form-group"><center>
														<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
															<?php 
																if(isset($_POST['edit_customer'])){ 
																	echo '<input type="hidden" name="edit_customer" value="'.$_POST['edit_customer'].'" />';
																	echo '<input type="hidden" name="update_customer" value="1" />'; 
																	echo '<Button type="submit" class="btn btn-info btn-addon" name="update" value="" /> <i class="fa fa-refresh"></i> Update Customer details</Button>';
																} else { 
																	echo '<input type="hidden" name="add_customer" value="1" />';
																	echo '<Button type="submit" class="btn btn-info btn-addon" name="save" value="" /> <i class="fa fa-plus"></i> Add Customer details</Button>';
																} 
															?>
															
															</center>
														</div>
													</div>
												
												
										</form>
										<script>
											function isNumberKey(evt)
											{
													var charCode = (evt.which) ? evt.which : evt.keyCode;
													if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
														return false;
														return true;
											}  
					
					
											function isNumericKey(evt)
											{
												var charCode = (evt.which) ? evt.which : evt.keyCode;
												if (charCode != 46 && charCode > 31 
												&& (charCode < 48 || charCode > 57))
												return true;
												return false;
											} 
											</script>
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
		
		
    
</body>               

                       
