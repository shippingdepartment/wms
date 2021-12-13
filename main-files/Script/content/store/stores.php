<?php
	include('system_load.php');
	//This loads system.
	
	//user Authentication.
	authenticate_user('subscriber');
	//creating store object.
	
	if(isset($_GET['message']) && $_GET['message'] != '') { 
		$message = 'Please select your store.';
	}//Message ends here select store
	
	//delete store if exist.
	if(isset($_POST['delete_store']) && $_POST['delete_store'] != '') { 
		$message = $new_store->delete_store($_POST['delete_store']);
	}//delete account.
		
	$page_title = "Stores"; //You can edit this to change your page title.
	//require_once("includes/header.php"); //including header file.

    /*display message if exist.
        if(isset($message) && $message != '') { 
            echo '<div class="alert alert-success">';
            echo $message;
            echo '</div>';
        }*/
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
      
        <!-- Theme Styles -->
        <link href="../../assets/css/space.min.css" rel="stylesheet">
        <link href="../../assets/css/custom.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
	<body>
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
				<?php
					//display message if exist.
					if(isset($message) && $message != '') { 
						echo '<div class="alert alert-success">';
						echo $message;
						echo '</div>';
					}
				?>
					<div class="row">
                     <div class="col-md-12">
					<div class="panel panel-white">
					<?php if(partial_access('admin')) { ?>
					
					<div class="panel-heading">
                                    <a href="manage_store.php"><h4 class="panel-title">Warehouses</h4></a>
                    </div>
					<div class="panel-body">
                                    <button type="button" class="btn btn-success m-b-sm" data-toggle="modal" data-target="#myModal">Add new Warehouse</button>
                                    <!-- Modal -->
                                    <form id="add-row-form" action="javascript:void(0);">
                                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <input type="text" id="name-input" class="form-control" placeholder="Name" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" id="position-input" class="form-control" placeholder="Position" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="number" id="age-input" class="form-control" placeholder="Age" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" id="date-input" class="form-control date-picker" placeholder="Start Date" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="number" id="salary-input" class="form-control" placeholder="Salary" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" id="add-row" class="btn btn-success">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
					
					
					<?php } ?>
					<div class="table-responsive">
					<table id="example3" class="display table" style="width: 100%; cellspacing: 0;">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Type</th>
								<th>City</th>
								<th>Phone</th>
								<th>Email</th>
								<th>Currency</th>
								<th>Logo</th>
								<th>Select</th>
								<?php if(partial_access('admin')) { ?><th>Edit</th>
								<th>Delete</th><?php } ?>
							</tr>
						</thead>
						<tbody>
						   <?php echo $new_store->list_stores(); ?>
						</tbody>
					</table>
					</div>
					</div>
					</br>
					</br>
					</div>
					</div>
					</div>
					<?php
						require_once("includes/footer.php");
					?>
					
				</div>
			</div>
			
		</div>
		<!-- Javascripts -->
        <script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
        <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
        <script src="../../assets/plugins/switchery/switchery.min.js"></script>
        <script src="../../assets/plugins/d3/d3.min.js"></script>
        <script src="../../assets/plugins/nvd3/nv.d3.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.pie.min.js"></script>
        <script src="../../assets/plugins/chartjs/chart.min.js"></script>
        <script src="../../assets/js/space.min.js"></script>
        <script src="../../assets/js/pages/dashboard.js"></script>
		<!-- Javascripts -->
        
        <script src="../../assets/plugins/datatables/js/jquery.datatables.min.js"></script>
        <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script src="../../assets/js/pages/table-data.js"></script>
		
		
    
</body>               
