<?php
	include('system_load.php');
	//user Authentication.
	authenticate_user('subscriber');
	
	if(isset($_POST['edit_order'])){ $page_title = 'Edit Order'; } else { $page_title = 'New Purshasing Order';}; //You can edit this to change your page title.
	
	
	if($_SESSION['user_type'] != "admin") {	
		if( $warehouse_access->have_module_access('products') OR $function_id!='storem' OR $function_id!='manager' )  {
			HEADER('LOCATION: warehouse.php?msg=nword');
		}
	}
	
?>

<?php if(isset($_GET['order_id'])) { ?>
	<script type="text/javascript">
		window.open('reports/view_order_invoice.php?order_id=<?php echo $_GET['order_id']; ?>', '_blank'); 
	</script>
<?php } ?>

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
		
 <!-- ********** JAVASCRIPT FUNCTIONS  ********** -->
<script type="text/javascript">
function AjaxFunction()
{
	var httpxml;
	try {
			// Firefox, Opera 8.0+, Safari
			httpxml=new XMLHttpRequest();
		}
	catch (e) {
		// Internet Explorer
		try {
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
    	}
		catch (e) 	{
			try {
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
		{
			if(httpxml.readyState==4)
				{
					//alert(httpxml.responseText);
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
							optn.value = myarray.data[i].product_id;  // 
							document.testform.product.options.add(optn);

						} 
				}
		} // end of function stateck
	var url="dd.php";
	var sid = document.getElementById('supplier_id').value;
	url=url+"?sid="+sid;
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
 }

</script>                  
<script type="text/javascript">

	function getProduct() {
		$.ajax({
			data: {
				product_id: $("#product_id").val(),
				warehouse_id: $("#warehouse_id").val()
			},
			type: 'POST',
			dataType: 'json',
			url: 'includes/get_order_data.php',
			success: function(response) {
				var product_name = response.product_name;
				var product_manual_id = response.product_manual_id;
				var warehouse_name = response.warehouse_name;
				var product_id = $("#product_id").val();
				var quantity = $("#quantity").val();
				var magasin = $("#warehouse_id").val();
				var warehouse_id = $("#warehouse_id").val();
				var content_1 = "<tr class='item-row'><td><div class='delete-wpr'>"+product_manual_id+"<input type='hidden' name='product_id[]' value='"+product_id+"'><a class='delme' href='javascript:;' title='Remove row'>X</a></div></td>";
				var content_2 = "<td>"+product_name+"</td>";
				var content_3 = "<td><input type='text' readonly='readonly' class='qty' name='qty[]' value='"+quantity+"'></td></tr>";
				$(".item-row:first").before(content_1+content_2+content_3);
				$('input[name=quantity').val('');
				$('select[name=product').val('');
			}
		});
	}
	
	$(document).ready(function(e) {
		$("#deliverydate").on("change",function(){
			var selectedDate = $(this).val();
			var ToDate = new Date();
			if (new Date(selectedDate).getTime() <= ToDate.getTime()) {
				alert("Estimated Delivery Date should be greater than the Order Date !");
				return false;
			}
		});
		
		$("#add_product").click(function() {	
			var e = document.getElementById("product_id");
			if(document.testform.supplier_id.value =="")
			{  
				alert("Please choose the Supplier !!");
				document.testform.supplier_id.focus();
				return false;  
			}
			if(document.testform.product.value =="")
			{  
				alert("Please choose a Product !!");
				document.testform.product.focus();
				return false;  
			}
			if (document.testform.quantity.value == "" || document.testform.quantity.value == 0)  
			{  
				alert("Please Enter the Quantity to add !!");
				document.getElementById("quantity").focus();
				return false;  
			}										
			getProduct();						
		});    
		
		$('#submodal').click(function () {
			//post
			if($("#code_supplier").val()=='') {
				alert("Supplier Code is required !");
				$("#code_supplier").setfocus();
				return false;
			} else if($("#supplier_name").val()=='') {
				alert("Supplier Name is required !");
				$("#supplier_name").setfocus();
				return false;
			} else if($("#tax_supplier").val()=='') {
				alert("Supplier Tax is required !");
				$("#tax_supplier").setfocus();
				return false;
			} else if($("#phone").val()=='') {
				alert("Supplier Phone is required !");
				$("#phone").setfocus();
				return false;
			} else if($("#address").val()=='') {
				alert("Supplier Address is required !");
				$("#address").setfocus();
				return false;
			} else if($("#city").val()=='') {
				alert("Supplier City is required !");
				$("#city").setfocus();
				return false;
			} else if($("#state").val()=='') {
				alert("Supplier State is required !");
				$("#state").setfocus();
				return false;
			} else if($("#zipcode").val()=='') {
				alert("Zip Code is required !");
				$("#zipcode").setfocus();
				return false;
			} else if($("#country").val()=='') {
				alert("Country is required !");
				$("#country").setfocus();
				return false;
			} else if($("#status").val()=='') {
				alert("Supplier Statue is required !");
				$("#status").setfocus();
				return false;
			} else {
				//this.form.submit();
				$("#form1").submit();
			}
		});
		//delete Product from the list .
		$('#items').on('click', '.delme', function() {
			$(this).parents('.item-row').remove();
		});
		
	});						
</script>
<!-- ********** END JAVASCRIPT FUNCTIONS  ********** -->

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
			<div class="row">
                <div class="col-md-12">
					<div  class="panel panel-white alert alert-default" style="font-size:16px" >
						<div class="panel-heading">
						<div class="panel-body" >
							<!-- Add new supplier modal starts here. -->
							<form id="form1" data-async data-target="#MyModal" action ="includes/addsupplier.php" method="POST" enctype="multipart/form-data"  >
								<div class="modal fade" id="addnewsupplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">New Supplier</h4>
                                            </div>
											<div id="success_message"></div>
											<div class="modal-body">
												<div class="form-group">
													<input type="text" class="form-control" name="code_supplier" id="code_supplier" placeholder="Supplier Code" value="" Required />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="supplier_name" id="supplier_name" placeholder="Full Name" value="" Required />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="tax_supplier" id="tax_supplier" placeholder="Registration Number" value="" />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="phone" id="phone" placeholder="Phone Number" value="" Required />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="mobile" id="mobile" placeholder="Mobile Number" value="" />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="address" id="address" placeholder="Address" value="" Required />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="city" id="city" placeholder="City" value="" />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="state" id="state" placeholder="State" value=""  />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="zipcode" id="zipcode" placeholder="Postal Code" value="" />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="country" id="country" placeholder="Country" value="" Required />
												</div>
												<div class="form-group">
													<input type="text" class="form-control" name="email" id="email" placeholder="Email" value="" Required />
												</div>
												<div class="form-group">
													<select class="form-control" name="status" id="status" required>
														<option value='' selected>Supplier Status</option>
														<option value="1" >Active</option>
														<option value="0" >Deactive</option>
													</select>
												</div>
												</div>
												
												<div class="form-group">
													<div class="modal-footer">
														<input type="hidden" name="add_supplier" value="1" />
														<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
														<button  id="submodal" name="submodal"  class="btn btn-info btn-addon"  ><i class="fa fa-plus"></i> Add Supplier</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</form>			
							<!-- ADD NEW SUPPLIER MODAL ENDS HERE.-->
							
							<!-- PROCEED WITH NEW ORDER DETAILS -->
							<form action="includes/orderprocess.php" method='POST' name='testform' class="form-horizontal" id="testform">
								<div class="form-group">
									<label class="col-sm-2 control-label">Order Date</label>
									<div class="col-sm-8">
										<input type="text" name="date" class="form-control" style="width:15em" Readonly value="<?php echo date('Y-m-d'); ?>" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">Estim. Deliv. Date</label>
									<div class="col-sm-8">
										<input type="date" name="deliverydate" id="deliverydate" class="form-control datepick" style="width:15em"   value="" Required />
										<span class="text-info"><small><i class="fa fa-question-circle"></i> This is to inform the supplier about the estimated date to deliver the Purchasing Order.</small> </span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">Ordre No#</label>
												<?php 
													$query = "SELECT MAX(order_id) as maxid FROM orders ";
													$result = $db->query($query) or die($db->error);
													$row = $result->fetch_array();
													$nb =  $row['maxid']; 
												?>
									<div class="col-sm-8">
										<input type="text" placeholder="Order N#" name="order_num" class="form-control" style="width:15em" value="<?php echo $nb+1 ; ?>" Disabled />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">Warehouse</label>
									<div class="col-sm-8">
										<select id="warehouse_id" name="source" class="form-control" style="width:100%"  Required title="Veuillez Choisir le Magasin Source !!" >
											<option value=''></option>
												<?php $query2="SELECT DISTINCT name FROM warehouses where warehouse_id='".$_SESSION['warehouse_id']."' order by name"; 
													if($stmt = $db->query($query2)){
														while ($row2 = $stmt->fetch_assoc()) {
															echo '<option selected value="'.$_SESSION['warehouse_id'].'">'.$_SESSION['warehouse_id'].'|'.$row2["name"].'</option>';
														}																		
													}else{
														echo $db->error;
													}	
												?>		
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">Choose Supplier</label>
									<div class="col-sm-8">
										<select name="supplier_id" id="supplier_id" class="form-control"  style="width:100%" onchange="AjaxFunction();" Required >
											<?php if (isset($_GET['sid'])) { ?>
											<option value=""><?php echo $supplier->get_supplier_info($_GET['sid'], 'full_name') ?></option>
											<?php } else { ?>
											<option value="">-- Choose Supplier --</option>
											<?php } ?>
											<?=$supplier->supplier_options($supplier->supplier_id); ?>	
										</select>
										<span class="text-info"><small><i class="fa fa-question-circle"></i> Note: When adding new supplier, you should also add products to this supplier before making New Order.</small> </span>
									</div>
									<div class="col-sm-2" style="text-align:left">
										<a class="btn btn-default btn-xs" data-toggle="modal" href="#addnewsupplier"><i class="fa fa-user" aria-hidden="true"></i> New Supplier</a>
									</div>
								</div>
								<center><hr width="50%"></center>
								<div class="form-group">
									<label class="col-sm-2 control-label">Select Product</label>
									<div class="col-sm-8">
										<select name="product" id="product_id" style="width:100%;" class="form-control" >
											<option value="">-- Select Product --</option>
												<?php $products->product_names($products->product_id); ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">Quantity</label>
									<div class="col-sm-6">
										<input type="number" min="0" step="1" name="quantity" id="quantity" class="form-control" style="width:15em" placeholder="Enter Quantity"  />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"></label>
									<div class="col-sm-6">
										<div id="add_product" class="btn btn-default"> <i class="fa fa-plus" ></i> Add Product to List</div>
									</div>
								</div>		
								<!--ADD PRODUCT SECTION-->
								<!--ROW STARTS HERE.-->
								<div class="row">
									<div class="col-sm-9">
										<table id="items" class="table table-condensed table-hover table-bordered">
											<tr>
												<th bgcolor=gray><font color="#fff">Product Code</th>
												<th bgcolor=gray><font color="#fff">Product Name</th>
												<th bgcolor=gray width="60"><font color="#fff">Qty</th>
												
											</tr>
											
											<tr class='item-row'>
												
											</tr>
										</table>
									</div>
									<div class="col-sm-3">
										<div class="well">
										  <Button type="submit" class="btn btn-info btn-addon" name="save" ><i class="fa fa-save" ></i> Save Order </Button>
										
										</div>
									</div>
								</div>
								<!--PRODUCT_DETAIL_ROW ENDS HERE.-->
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
</div></div>
	

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

                       
