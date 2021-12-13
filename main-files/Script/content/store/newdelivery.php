<?php
	include('system_load.php');
	//This loads system.
	
	//user Authentication.
	authenticate_user('subscriber');
	//creating user object.
	$user = new Users;
	$user_id = $_SESSION['user_id'];
	$function_id = $user->get_user_info($user_id,"user_function");
	// user permissions
	if($_SESSION['user_type'] != "admin") {	
		if( $warehouse_access->have_module_access('products') OR $function_id!='storem' OR $function_id!='manager' )  {
			HEADER('LOCATION: warehouse.php?msg=nwdel');
		}
	}
	

	if(isset($_POST['edit_delivery'])){ $page_title = 'Edit Delivery Request'; } else { $page_title = 'New Delivery Request';}; //You can edit this to change your page title.
	
?>


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
<script type="text/javascript">
function AjaxFunction()
{
	var httpxml;
	try
		{
			// Firefox, Opera 8.0+, Safari
			httpxml=new XMLHttpRequest();
		}
	catch (e)
		{
			// Internet Explorer
				try
   			 		{
						httpxml=new ActiveXObject("Msxml2.XMLHTTP");
    				}
				catch (e)
    				{
						try
							{
								httpxml=new ActiveXObject("Microsoft.XMLHTTP");
							}
						catch (e)
							{
							alert("Your browser does not support AJAX!");
							return false;
							}
					}
		}
	function stateck() 
		{
			if(httpxml.readyState==4)
				{
					
					var myarray = JSON.parse(httpxml.responseText);
					// Remove the options from 2nd dropdown list 
					for(j=document.testform.product.options.length-1;j>=0;j--)
						{
							document.testform.product.remove(j);
						}
					for (i=0;i<myarray.data.length;i++)
						{
							var optn = document.createElement("OPTION");
							optn.text = myarray.data[i].product_name;
							optn.value = myarray.data[i].product_id;  // You can change this to subcategory 
							document.testform.product.options.add(optn);

						} 
				}
		} // end of function stateck
	var url="dd.php";
	var wid = document.getElementById('bureau').value;
	url=url+"?wid="+wid;
	url=url+"&sid="+Math.random();
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
 }

</script>                  

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
<Body class="page-sidebar-fixed page-header-fixed">
 <div class="page-container">
 <?php require_once("includes/sidebar.php"); //including sidebar file. ?>
  <div class="page-content">
  <?php require_once("includes/header.php"); //including header file. ?>
  <div class="page-inner">
					<div class="page-title">
							<h3 class="breadcrumb-header"><?php echo $page_title; ?></h3>
					</div>
					<div>
					
						<?php
						//display message if exist.
							if(isset($_GET['message']) && $_GET['message'] != '') { 
								echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ';
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
					
<div class="panel panel-white alert alert-default">
	<div class="panel-heading clearfix">
		<div class="panel-body " >
			<form action="includes/processdelivery.php" class="form-horizontal " method='POST' name='testform'>
				<div class="form-group">             
					<label class="col-sm-2 control-label">Edition Date</label>
					<div class="col-sm-6">
						<input type="text" name="date" class="form-control" style="width:15em" Readonly value="<?php echo date('d-m-Y'); ?>" />
					</div>
				</div>
                <input type="hidden" placeholder="Memo" name="memo" class="form-control" value="" />
				<div class="form-group">
					<label class="col-sm-2 control-label">Issued Date</label>
					<div class="col-sm-6">
						<input type="text" name="deliverydate" id="deliverydate" class="form-control date-picker" style="width:15em" Required  value="<?php if(isset($_POST['deliverydate'])) { echo $_POST['deliverydate']; } ?>" />
						<span class="text-info"><small><i class="fa fa-question-circle"></i> Estimated Date of delivery from the warehouse </small></span>
					</div>
				</div>				
				<div class="form-group">
					<label class="col-sm-2 control-label">Delivery No#</label>
						<?php 
							$query = "SELECT MAX(delivery_id) as maxid FROM deliveries ";
							$result = $db->query($query) or die($db->error);
							$row = $result->fetch_array();
							$nb =  $row['maxid']; 
						?>
					<div class="col-sm-6">
						<input type="text" placeholder="Delivery N#" name="order_num" class="form-control" style="width:15em" value="<?php echo $nb+1 ; ?>" Disabled />
					</div>
				</div> 
				<div class="form-group">
					<label class="col-sm-2 control-label">Client Order N#</label>
					<div class="col-sm-6">
						<input type="text" name="clientorder" class="form-control" style="width:15em"  value="" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">From Warehouse</label>
					<div class="col-sm-6">
						<select id="vendor_options" name="source" class="form-control" style="width:100%"  >
							<option value=''></option>
								<?php $query2="SELECT DISTINCT name FROM warehouses where warehouse_id='".$_SESSION['warehouse_id']."' order by name"; 
									if($stmt = $db->query("$query2")){
										while ($row2 = $stmt->fetch_assoc()) {
											echo "<option selected value='$row2[warehouse_id]'>$row2[name]</option>";
										}																		
									}else {
										echo $db->error;
									}				
								?>
						</select>	
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">To Client</label>
					<div class="col-sm-6">
						<select id="bureau" name="bureau" class="form-control" style="width:100%" required >
							<option  value=''></option>
							<?php $query3="SELECT * FROM clients  order by full_name"; 
							if($stmt3 = $db->query("$query3")){
								while ($row3 = $stmt3->fetch_assoc()) {
									echo "<option  value='$row3[client_id]'>$row3[full_name]</option>";}																		
							} else {
									echo $db->error;
							}							
							?>		
						</select>	
					</div>
				</div>
<!-- JAVASCRIPT -->
<script type="text/javascript">

/* GET PRODUCT DETAILS -*/
function getProduct() {
	$.ajax({
	 data: {
	  product_id: $("#product_id").val(),
	  warehouse_id: $("#bureau").val()
	 },
	 type: 'POST',
	 dataType: 'json',
	 url: 'includes/get_delivery_data.php',
	 success: function(response) {
	   var product_name = response.product_name;
	   var product_manual_id = response.product_manual_id;
	   var warehouse_name = response.warehouse_name;
	   
	   var product_id = $("#product_id").val();
	   var quantity = $("#quantity").val();
	   var magasin = $("#bureau").val();
	   //var cost = $("#cost").val();
	   var warehouse_id = $("#warehouse_id").val();
	   
	   var content_1 = "<tr class='item-row'><td><div class='delete-wpr'>"+product_manual_id+"<input type='hidden' name='product_id[]' value='"+product_id+"'><a class='delme' href='javascript:;' title='Remove row'><i class='fa fa-remove' style='color:#FF0000'></i></a></div></td>";
	   var content_2 = "<td>"+product_name+"</td>";
	   var content_3 = "<td><input type='text' readonly='readonly' class='qty' name='qty[]' value='"+quantity+"'></td>";
	    
	   
	   $(".item-row:first").before(content_1+content_2+content_3);
	  // $("#product_id").val('');
	  $("#product_id").val('');
	   $("#quantity").val('');
	   
	    $("testform").reset();
	 
	   }
	});
}
	$(document).ready(function(e) {
    	/* CHECK IF DELIVERY DATE IS BIGGER THAN EDITION DATE */
		$("#deliverydate").on("change",function(){
			var selectedDate = $(this).val();
			var ToDate = new Date();
			if (new Date(selectedDate).getTime() <= ToDate.getTime()) {
				alert("Estimated delivery Date from the warehouse should be greater than the Edition Date !");
				return false;
			}
		});
		
		/* WHEN ADDING A PRODUCT TO THE LIST */
		$("#add_product").click(function() {
			
				var e = document.getElementById("product_id");
				
				var strProduct1 = e.options[e.selectedIndex].text;
				if(document.testform.bureau.value =="") {  
					alert("Please select the Client !!");
					document.testform.bureau.focus();
					return false;  
				}
				if(document.testform.product_id.value =="") {  
					alert("Please select Product Name from the list !!");
					document.testform.product_id.focus();
					return false;  
				}
				if (document.testform.quantity.value == "" || document.testform.quantity.value == 0) {  
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
       
				<hr width="50%" />
				<div class="form-group">
					<label class="col-sm-2 control-label">Select Product</label>
					<div class="col-sm-6">
						<select name="product" id="product_id" style="width:400px;" class="form-control" >
							<option value="">-- Select Product --</option>
							<?php $products->product_names($products->product_id); ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Quantity</label>
					<div class="col-sm-6">
						<input type="number" min="0" step="1" name="quantity" id="quantity"  class="form-control" style="width:15em" placeholder="Enter Quantity"  />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-6">
						<div id="add_product" class="btn btn-default"> <i class="fa fa-plus" ></i> Add Product</div>
					</div>
				 </div> 
				<br />
				<div class="row">
					<div class="col-sm-9">
						<table id="items" class="table table-condensed table-hover table-bordered">
							<tr>
								<th bgcolor=gray><font color="#fff">Code</th>
								<th bgcolor=gray><font color="#fff">Product Name</th>
								<th bgcolor=gray><font color="#fff" width="60">Qty</th>
							</tr>
							<tr class='item-row'>
								<!--  PRODUCTS WILL BE ADDED HERE WHEN CLICKING ON 'ADD PRODUCT' BUTTON -->
							</tr>
						</table>
					</div>
					<div class="col-sm-3">
						<div class="well">
						  <Button type="submit" class="btn btn-info btn-addon" name="save" value="Save" ><i class="fa fa-save"></i> Save Delivery </Button>
						  
						</div>
					</div>
				</div><!--product_Detail_row ends here.-->
			</form> 
			<div class="page-footer">
					<?php
						require_once("includes/footer.php");
					?>
			</div>
		</div>
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