<?php
	include('system_load.php');
	//This loads system.
	
	//user Authentication.
	authenticate_user('subscriber');
	//creating store object.
	
	
	
	//deactivate user if exist.
	if(isset($_POST['delete_user']) && $_POST['delete_user'] != '') { 
		$message = $new_user->delete_user($_POST['delete_user']);
	}
	
	//Activate user.
	if(isset($_POST['activate_user']) && $_POST['activate_user'] != '') { 
		$message = $new_user->activate_user($_POST['activate_user']);
	}
		
	$page_title = "Users"; //You can edit this to change your page title.
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
	<body class="page-sidebar-fixed page-header-fixed">
	<!-- Page Container -->
        <div class="page-container">
			<!-- Side Bar -->
			<?php 
			if(isset($_SESSION['warehouse_id'])){
			require_once("includes/sidebar.php"); //including sidebar file. 
			}else {
			require_once("includes/sidebaradmin.php"); //including sidebar file. 	
			}
			?>
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
				<?php
					//display message if exist.
					if(isset($message) && $message != '') { 
						//echo '<div class="alert alert-success">';
						echo $message;
						//echo '</div>';
					}
				?>
				
				
				
					<div class="row">
                     <div class="col-md-12">
					<div class="panel panel-white" >
					
					<?php if(partial_access('admin')) { ?>
					
					
					<div class="panel-body" >
                                    <a href="manager_user.php"  class="btn btn-info btn-addon" ><i class="fa fa-user"></i> Add New User</a>
                    </div><br/>
					<?php } ?>
					<div class="table-responsive">
					<table id="example3" class="display table" style="width: 100%; cellspacing: 0;">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Phone</th>
								<th>Email</th>
								<th>Login</th>
								<th>User Type</th>
								<th>Statut</th>
								<th>Last Connection</th>
								
								<?php if(partial_access('admin')) { ?><th>Edit</th>
								<th>Action</th><?php } ?>
							</tr>
						</thead>
						<tbody>
						   <?php 
							if ($_GET['t']=='all') {
								echo $new_user->list_users($_SESSION['user_type'],'');
							}
							elseif ($_GET['t']=='bn') {
								echo $new_user->list_users($_SESSION['user_type'],'banned');
							}
							elseif ($_GET['t']=='ds') {
								echo $new_user->list_users($_SESSION['user_type'],'deactivate');
							}
							else if ($_GET['t']=='sp') {
								echo $new_user->list_users($_SESSION['user_type'],'suspended');
							}
							else {
								echo $new_user->list_users($_SESSION['user_type'],'activate');
							}
						   ?>
						</tbody>
						
					</table>
					</div>
					</div>
					</br>
					</br>
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
