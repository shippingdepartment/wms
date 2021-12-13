<?php
	include('system_load.php');
	//@$cat=$_GET['cat'];
	//This loads system.
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$user = new Users;

	//$id=$_SESSION["store_id"];
	$user_id = $_SESSION['user_id'];
	$function_id = $user->get_user_info($user_id,"user_function");
		
	if(partial_access('admin') || $warehouse_access->have_module_access('warehouse')) {} else { 
		HEADER('LOCATION: warehouse.php?message=products');
	}
	
	if(!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') { 
		HEADER('LOCATION: warehouses.php?message=1');
	} //select company redirect ends here.
	

	if(isset($_POST['edit_transfer'])){ $page_title = 'Edit Transfer'; } else { $page_title = 'New Transfer';}; //You can edit this to change your page title.
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
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
		 

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
		
			<script type="text/javascript">
			jQuery(function($) {
				$('form[data-async]').on('submit', function(event) {
					
					var $form = $(this);
					var $target = $($form.attr('data-target'));

					$.ajax({
						type: $form.attr('method'),
						url: 'includes/otherprocesses.php',
						data: $form.serialize(),
						dataType: 'json',
			 
					success: function(response) {
						var message = response.message;
						var vendor_options = response.vendor_options;
						var vendor_id = response.vendor_id;
						
						$('#vendor_options').html(vendor_options);
						$("#vendor_options").select2().select2('val', vendor_id);
						$('#success_message').html('<div class="alert alert-success">'+message+'</div>');
					}
				});
				event.preventDefault();
			});
			});
		</script>	
			
    </head>
	<body>
	<style type="text/css">
	input:focus
	{
	background-color:#FFFACD;
	}
	select:focus
	{
	background-color:#FFFACD;
	}
	</style>
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
					<div class="page-title">
							<h3 class="breadcrumb-header"><?php echo $page_title; ?></h3>
					</div>
					<div>
					
						<?php
						//display message if exist.
							if(isset($_GET['message']) && $_GET['message'] != '') { 
								echo '<div class="alert alert-success">';
								echo $_GET['message'];
								echo '</div>';
							}
							if(isset($message) && $message != '') { 
								echo '<div class="alert alert-success">';
								echo $message;
								echo '</div>';
							}
						?>
					
					</div>
					<div class="panel panel-white">
						<div class="panel-heading clearfix">
							<div class="panel-body" >
										<form class="form-horizontal" form action="includes/transferprocess.php" id="testform" name="testform" method="post">
											<div class="form-group">
												<label class="col-sm-2 control-label">Date :</label>
												<div class="col-sm-6">
													<input type="text" name="datetransfer" class="form-control" Readonly value="<?php echo date('Y-m-d'); ?>" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label">From :</label>
												<div class="col-sm-6">
													<select id="vendor_options" name="source" class="form-control" style="width:100%"  >
																<option value=''></option>
															<?php $query2="SELECT DISTINCT name FROM warehouses where warehouse_id='".$_SESSION['warehouse_id']."' order by name"; 
																if($stmt = $db->query("$query2")){
																	while ($row2 = $stmt->fetch_assoc()) {
																		echo "<option selected value='$row2[warehouse_id]'>$row2[name]</option>";}																		
																}else{
																	echo $db->error;
																}
															
															?>														
																<?php //echo $warehouse->warehouse_list_transfer(); ?>	-->
													</select>	
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label">Destination :</label>
												<div class="col-sm-6">
													<select id="bureau" name="bureau" class="form-control" style="width:100%"  >
															<option  value=''></option>
															<?php $query2="SELECT DISTINCT warehouse_id, name FROM warehouses  order by name"; 
																if($stmt = $db->query("$query2")){
																	while ($row2 = $stmt->fetch_assoc()) {
																		echo "<option  value='$row2[warehouse_id]'>$row2[name]</option>";}																		
																}else{
																	echo $db->error;
																}
															
															?>														
																<?php //echo $warehouse->warehouse_list_transfer(); ?>	-->
													</select>	
												</div>
											</div>
											
												<div class="form-group">
													<label class="col-sm-2 control-label">Select Product:</label>
													<div class="col-sm-6">
														<select name="product" id="product_id"  class="form-control" >
															<option value="">-- Select Product --</option>
													 <?php $products->product_names($products->product_id); ?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-2 control-label">Quantity:</label>
													<div class="col-sm-6">
														<input type="text" name="quantity" id="quantity" class="form-control" placeholder="Enter Quantity"  />
														<input type="hidden" name="nb" id="nb" class="form-control"   />	
														<div id="add_product" class="btn btn-default">Add Product</div>
													</div>
												</div>
												
												
												
												
												
												
												

											   </div><!--add product Section-->
											</div><!--row ends here.-->
											<br />
											<div class="row">
												<div class="col-sm-9">
													<table id="items" class="table table-bordered table-striped" style="clear: both">
														<tbody>
														<tr>
															<th>Code</th>
															<th>Product</th>
															<th width="60">Qty</th>
															<!--<th width="60">Cost</th>-->
															<th>Warehouse</th>
															<th></th>
														</tr>
														
														<tr class='item-row'>
															<td colspan="6">Selected Products</td>
														</tr>
														</tbody>
													</table>
												</div>
												<div class="col-sm-3">
													<div class="well">
													  <button type="submit" class="btn btn-success" name="save" value="Save" /><i class="fa fa-save"></i> Save</button> &nbsp;<button type="submit" class="btn btn-default" name="print" value="Print" /><i class="fa fa-print"></i> Print</button>
													</div>
												</div>
											</div><!--product_Detail_row ends here.-->
										</form>
										
										<script type="text/javascript">

													function getProduct() {
														$.ajax({
														 data: {
														  product_id: $("#product_id").val(),
														  warehouse_id: $("#bureau").val()
														 },
														 type: 'POST',
														 dataType: 'json',
														 url: 'includes/get_transfer_data.php',
														 success: function(response) {
														   var product_name = response.product_name;
														   var product_manual_id = response.product_manual_id;
														   var warehouse_name = response.warehouse_name;
														   
														   
														   var product_id = $("#product_id").val();
														   var quantity = $("#quantity").val();
														   var magasin = $("#bureau").val();
														   //var cost = $("#cost").val();
														   //var warehouse_id = $("#warehouse_id").val();
														   var nb = Number($("#nb").val()) ;
														   nb += 1 ;
														 
														   var content_1 = "<tr class='item-row'><td>"+product_manual_id+"<input type='text' name='pid[]' value='"+product_id+"'></td>";
														   var content_2 = "<td>"+product_name+"</td>";
														   var content_3 = "<td><input type='text' readonly  name='qty[]' value='"+quantity+"'></td>;"//<a href='#' id='firstname' data-type='text' data-pk='1' data-placement='right' data-placeholder='Qty' data-title='Enter Qty' class='editable editable-click editable-empty' style='display:inline;'>"+quantity+"</a></td>";
														   //var content_4 = "<td><input type='text' readonly='readonly' class='cost' name='cost[]' value='"+cost+"'></td>";
														   var content_5 = "<td>"+warehouse_name+"<input type='text' name='wid[]' value='"+magasin+"'></td><td><a  href='javascript:;' title='Remove row'><i class='fa fa-remove' style='color:#FF0000'></i></a></td></tr>";
														   //var content_6 = 	"<td class='total'>"+cost*quantity+"</td></tr>";   
														   
														   $(".item-row:first").before(content_1+content_2+content_3+content_5);
														   /*$('#testform').each(function(){
																$("#quantity").val('');  
																var dropDown = document.getElementById("product_id");
																	dropDown.selectedIndex = 0;
															});*/
															 $("#nb").val(nb);
															
														}
														
														  
														});
													}
													$(document).ready(function(e) {
															$("#add_product").click(function() {
																
																	var e = document.getElementById("product_id");																	
																	var strProduct1 = e.options[e.selectedIndex].text;
																	if(document.testform.bureau.value =="") {  
																		alert("Please select the destination Warehouse !!");
																		
																		document.testform.bureau.focus();
																		return false;  
																	}
																	if(document.testform.product_id.value =="") {  
																		alert("Please select Product Name from the list !!");
																		
																		document.testform.product_id.focus();
																		return false;  
																	}
																if (document.testform.quantity.value == "")  
																	{  
																		alert("Please add the Quantity!!");
																		document.getElementById("quantity").focus();
																		return false;  
																	}
																	
																getProduct();
																
															});    
														//delete Row.
															$('#items').on('click', '.delme', function() {
															   $(this).parents('.item-row').remove();
															   //update_total();
															});
													});
														
												</script>
										
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
		 <!-- <script src="../../assets/js/space.min.js"></script>-->
</body>
</html>
													