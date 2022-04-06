<?php
include('system_load.php');
//This loads system.

//user Authentication.
authenticate_user('subscriber');
//creating company object.

$product = new Product;
$note = new Notes;
$message = '';
if (isset($_POST['warehouse_id'])) {
	$_SESSION['warehouse_id'] = $_POST['warehouse_id'];
	if (partial_access('admin') || $warehouse_access->have_warehouse_access()) {
	} else {
		unset($_SESSION['warehouse_id']);
	}
} //setting company to use.

if (!isset($_SESSION['warehouse_id']) || $_SESSION['warehouse_id'] == '') {
	HEADER('LOCATION: warehouses.php?msg=1');
} //select company redirect ends here.

if (isset($_GET['msg']) && $_GET['msg'] == 'warforb') {
	$message = " You are not allowed to manage this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'prodforb') {
	$message = " You are not allowed to manage products in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'nwtr') {
	$message = " You are not allowed to create transfers in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'prdim') {
	$message = " You are not allowed to manage product settings in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'nword') {
	$message = " You are not allowed to add orders in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'nwdel') {
	$message = " You are not allowed to add deliveries in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'nwsret') {
	$message = " You are not allowed to return products in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'nwcust') {
	$message = " You are not allowed to create new customer in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'nwsupp') {
	$message = " You are not allowed to create new supplier in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'suppliers') {
	$message = " You are not allowed to view the suppliers page in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'lstcust') {
	$message = " You are not allowed to view the customers page in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'lstord') {
	$message = " You are not allowed to view the orders page in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'lstdlvr') {
	$message = " You are not allowed to view the deliveries page in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'lstrtn') {
	$message = " You are not allowed to view the returns page in this warehouse.";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'lstrpt') {
	$message = " You are not allowed to view the reports pages in this warehouse.";
}

if (isset($_POST['delete_warehouse']) && $_POST['delete_warehouse'] != '') {
	$message = $warehouses->delete_warehouse($_POST['delete_warehouse']);
} //delete ends here.

$warehouses->set_warehouse($_SESSION['warehouse_id']); //setting store.

$page_title = $warehouses->name; //You can edit this to change your page title.


//display message if exist.

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
	<link href="../../assets/plugins/uniform/css/default.css" rel="stylesheet" />
	<link href="../../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
	<link href="../../assets/plugins/nvd3/nv.d3.min.css" rel="stylesheet">

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
		<?php require_once("includes/sidebar.php"); //including sidebar file. 
		?>
		<!-- End Side Bar -->
		<!-- Page Content -->
		<div class="page-content">
			<!-- Header -->
			<?php require_once("includes/header.php"); //including sidebar file. 
			?>
			<!-- End Header -->

			<!-- Page Inner -->
			<div class="page-inner">
				<div class="page-title">
					<h3 class="breadcrumb-header">Dashboard - <?php echo $page_title; ?></h3>
				</div>
				<?php
				if ($message != '') {
					echo '<div class="alert alert-danger" style="font-size:16px"><i class="fa fa-exclamation-circle"></i>';
					echo $message;
					echo '</div>';
				}
				?>
				<div id="main-wrapper">
					<div class="row">
						<?php if (partial_access('admin')) { ?>
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-white stats-widget dashed-primary">
									<div class="panel-body">
										<div class="pull-left">
											<span class="stats-number" style="font-size:30px;color:#0d47a1"><?php echo $product->num_products($_SESSION['warehouse_id']); ?></span>
											<p class="stats-info" style="font-size:20px;color:#0d47a1"><?php if ($product->num_products($_SESSION['warehouse_id']) == 1) { ?>Product <?php } else { ?>Products <?php } ?></p>
										</div>
										<div class="pull-right">
											<i class="fa fa-barcode" style="font-size:48px;color:#0d47a1"></i>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
						<?php if (partial_access('admin')) { ?>
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-white stats-widget dashed-warning">
									<div class="panel-body">
										<div class="pull-left">
											<span class="stats-number" style="font-size:30px;color:#FF8800"><?php echo $product->products_alert_count(); ?></span>
											<p class="stats-info" style="font-size:20px;color:#FF8800">Stock Alert</p>
										</div>
										<div class="pull-right">
											<i class="fa fa-minus-square" style="font-size:48px;color:#FF8800"></i>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
						<?php if (partial_access('admin')) { ?>
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-white stats-widget dashed-danger">
									<div class="panel-body">
										<div class="pull-left">
											<span class="stats-number" style="font-size:30px;color:#CC0000"><?php echo $product->products_out_stock(); ?></span>
											<p class="stats-info" style="font-size:20px;color:#CC0000">Out Of Stock</p>
										</div>
										<div class="pull-right">
											<i class="fa fa-warning" style="font-size:48px;color:#CC0000"></i>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
						<?php if (partial_access('admin')) { ?>
							<div class="col-lg-3 col-md-6">
								<div class="panel panel-white stats-widget dashed-success">
									<div class="panel-body">
										<div class="pull-left">
											<span class="stats-number" style="font-size:30px;color:#007E33"><?php echo $note->notes_count(); ?></span>
											<p class="stats-info" style="font-size:20px;color:#007E33">Notifications</p>
										</div>
										<div class="pull-right">
											<i class="fa fa-bell-o" style="font-size:48px;color:#007E33"></i>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>

					</div><!-- Row -->
					<div class="row">
						<hr width="50%">
					</div>
					<div class="row">
						<?php if (partial_access('admin')) { ?>
							<div class="col-lg-3 col-md-3">

								<a style="display:block" href="users.php?t=all&s_s=<?php echo session_id(); ?>">
									<div class="panel warh-bloc" style="background-color:#2BBBAD;opacity:0.8">
										<div class="panel-body">

											<div class="pull-left">

												<p class="stats-info title-warh-bloc">Users</p>
											</div>
											<div class="pull-right icon-warh-bloc">
												<i class="fa fa-user-o"></i>
											</div>
										</div>

									</div>
								</a>
							</div>

						<?php } ?>
						<?php if (partial_access('admin')) { ?>

							<div class="col-lg-3 col-md-3">
								<a style="display:block" href="products.php?s_s=<?php echo session_id(); ?>">
									<div class="panel warh-bloc" style="background-color:#4285F4;opacity:0.8">
										<div class="panel-body">
											<div class="pull-left">

												<p class="stats-info title-warh-bloc">Products</p>
											</div>
											<div class="pull-right icon-warh-bloc">
												<i class="fa fa-barcode"></i>
											</div>
										</div>

									</div>
								</a>
							</div>
						<?php } ?>
						<?php if (partial_access('admin')) { ?>

							<div class="col-lg-3 col-md-3">
								<a style="display:block" href="categories.php?s_s=<?php echo session_id(); ?>">
									<div class="panel warh-bloc" style="background-color:#37474F;opacity:0.8">
										<div class="panel-body">
											<div class="pull-left">

												<p class="stats-info title-warh-bloc">Categories</p>
											</div>
											<div class="pull-right icon-warh-bloc">
												<i class="fa fa-sitemap"></i>
											</div>
										</div>

									</div>
								</a>
							</div>
						<?php } ?>

						<div class="col-lg-3 col-md-3">
							<a style="display:block warh-bloc" href="assigned_orders_list.php?t=user">
								<div class="panel warh-bloc" style="background-color:#ffbb33;opacity:0.8">
									<div class="panel-body">
										<div class="pull-left">

											<p class="stats-info title-warh-bloc">Orders</p>
										</div>
										<div class="pull-right icon-warh-bloc">
											<i class="fa fa-file-text"></i>
										</div>
									</div>

								</div>
							</a>
						</div>
						<div class="col-lg-3 col-md-3">
							<a style="display:block" href="inventproducts.php?s_s=<?php echo session_id(); ?>">
								<div class="panel warh-bloc" style="background-color:#aa66cc;opacity:0.8">
									<div class="panel-body">
										<div class="pull-left">

											<p class="stats-info title-warh-bloc">Stock</p>
										</div>
										<div class="pull-right icon-warh-bloc">
											<i class="fa fa-cubes"></i>
										</div>
									</div>

								</div>
							</a>
						</div>
						<div class="col-lg-3 col-md-3">
							<a style="display:block" href="deliveries.php?s_s=<?php echo session_id(); ?>">
								<div class="panel warh-bloc" style="background-color:#ff4444;opacity:0.8">
									<div class="panel-body">
										<div class="pull-left">

											<p class="stats-info title-warh-bloc">Deliveries</p>
										</div>
										<div class="pull-right icon-warh-bloc">
											<i class="fa fa-truck"></i>
										</div>
									</div>

								</div>
							</a>
						</div>

						<!--</div> Row 
                        <div class="row">-->

						<?php if (partial_access('admin')) { ?>

							<div class="col-lg-3 col-md-3">
								<a style="display:block" href="transfers.php?s_s=<?php echo session_id(); ?>">
									<div class="panel warh-bloc" style="background-color:#00C851;opacity:0.8">
										<div class="panel-body">
											<div class="pull-left">

												<p class="stats-info title-warh-bloc">Transfers</p>
											</div>
											<div class="pull-right icon-warh-bloc">
												<i class="fa fa-exchange"></i>
											</div>
										</div>

									</div>
								</a>
							</div>
						<?php } ?>
						<?php if (partial_access('admin')) { ?>

							<div class="col-lg-3 col-md-3">
								<a style="display:block" href="suppliers.php?s_s=<?php echo session_id(); ?>">
									<div class="panel warh-bloc" style="background-color:#e65100;opacity:0.8">
										<div class="panel-body">
											<div class="pull-left">

												<p class="stats-info title-warh-bloc">Suppliers</p>
											</div>
											<div class="pull-right icon-warh-bloc">
												<i class="fa fa-address-book"></i>
											</div>
										</div>

									</div>
								</a>
							</div>
						<?php } ?>
						<?php if (partial_access('admin')) { ?>

							<div class="col-lg-3 col-md-3">
								<a style="display:block" href="customers.php?s_s=<?php echo session_id(); ?>">
									<div class="panel warh-bloc" style="background-color:#0091ea;opacity:0.8">
										<div class="panel-body">
											<div class="pull-left">

												<p class="stats-info title-warh-bloc">Customers</p>
											</div>
											<div class="pull-right icon-warh-bloc">
												<i class="fa fa-address-card"></i>
											</div>
										</div>

									</div>
								</a>
							</div>


							<div class="col-lg-3 col-md-3">
								<a style="display:block" href="orders.php?s_s=<?php echo session_id(); ?>">
									<div class="panel warh-bloc" style="background-color:#3F729B;opacity:0.8">
										<div class="panel-body">
											<div class="pull-left">

												<p class="stats-info title-warh-bloc">Receptions</p>
											</div>
											<div class="pull-right icon-warh-bloc">
												<i class="fa fa-arrow-circle-o-down"></i>
											</div>
										</div>

									</div>
								</a>
							</div>
						<?php } ?>
						<div class="col-lg-3 col-md-3">
							<a style="display:block" href="return_label_list.php?s_s=<?php echo session_id(); ?>">
								<div class="panel warh-bloc" style="background-color:#c51162;opacity:0.8">
									<div class="panel-body">
										<div class="pull-left">

											<p class="stats-info title-warh-bloc">Returns</p>
										</div>
										<div class="pull-right icon-warh-bloc">
											<i class="fa fa-registered"></i>
										</div>
									</div>

								</div>
							</a>
						</div>
						<div class="col-lg-3 col-md-3">
							<a style="display:block warh-bloc" href="reports.php?s_s=<?php echo session_id(); ?>">
								<div class="panel warh-bloc" style="background-color:#4B515D;opacity:0.8">
									<div class="panel-body">
										<div class="pull-left">

											<p class="stats-info title-warh-bloc">Reports</p>
										</div>
										<div class="pull-right icon-warh-bloc">
											<i class="fa fa-bar-chart-o"></i>
										</div>
									</div>

								</div>
							</a>
						</div>
						<!--</div> Row 
                        <div class="row" style="height:50px">-->

					</div><!-- Row -->
				</div><!-- Main Wrapper -->
				<div class="page-footer">
					<?php
					require_once("includes/footer.php");
					?>
				</div>
			</div><!-- /Page Inner -->

		</div><!-- /Page Content -->
	</div><!-- /Page Container -->


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
</body>

</html>