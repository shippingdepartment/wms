<?php
	include('system_load.php');
	//Including this file we load system.
	
	//user Authentication.
	authenticate_user('admin');
	
	//user level object
	$new_userlevel = new Userlevel;
	
	//installation form processing when submits.
	if(isset($_POST['settings_submit']) && $_POST['settings_submit'] == 'Yes') {
	//validation to check if fields are empty!
	if($_POST['site_name'] == '') { 
		$message = $language['site_name_empty'];
	} 
	else {
		
		set_option('site_url', "www.mysite.com"); //adding your own site url
		set_option('site_name', $_POST['site_name']);
		set_option('email_from', $_POST['email_from']);
		set_option('email_to', $_POST['email_to']);
		set_option('public_key', "");
		set_option('private_key', "");
		set_option('redirect_on_logout', "");
		set_option('language', $_POST['language']);
		set_option('skin', "");
		set_option('maximum_login_attempts', $_POST['maximum_login_attempts']);
		set_option('wrong_attempts_time', $_POST['wrong_attempts_time']);
		set_option('session_timeout', $_POST['session_timeout']);
		set_option('register_user_level', $_POST['register_user_level']);
		set_option('facebook_api_key', "");
		
		
		if(isset($_POST['notify_user_group'])) {
			set_option('notify_user_group', $_POST['notify_user_group']);
		} else { 
			set_option('notify_user_group', '0');
		}
		if(isset($_POST['register_verification'])) {
			set_option('register_verification', $_POST['register_verification']);
		} else { 
			set_option('register_verification', '0');
		}
		
		if(isset($_POST['disable_login'])) {
			set_option('disable_login', $_POST['disable_login']);
		} else { 
			set_option('disable_login', '0');
		}
		if(isset($_POST['disable_registration'])) {
			set_option('disable_registration', $_POST['disable_registration']);
		} else { 
			set_option('disable_registration', '0');
		}
		$message = $language['settings_saved1'];
		HEADER('LOCATION: companysettings.php?msg='.$message); 
		}//form validations
	}//form processing.

	//Page display settings.
	$page_title = $language['general_setting_page_title']; //You can edit this to change your page title.
	$sub_title = "";
	

    
?>
<?php
		
	$assigned_orders = isset($_SESSION['assigned_orders']) && $_SESSION['assigned_orders'] ? 'checked="checked"' : '';
	$cart_toggle = isset($_SESSION['cart_toggle']) && $_SESSION['cart_toggle'] ? 'checked="checked"' : '';
	$bulk_fulfillment = isset($_SESSION['bulk_fulfillment']) && $_SESSION['bulk_fulfillment'] ? 'checked="checked"' : '';
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settings_submit'])) {
		$assigned_orders = isset($_POST['assigned_orders']) && $_POST['assigned_orders'] == '1';

		// Store the checkbox value in a session variable
		$_SESSION['assigned_orders'] = $assigned_orders;

		$message = $language['settings_saved1'];
		HEADER('LOCATION: companysettings.php?msg='.$message); 
		}//form validations

			if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settings_submit'])) {
				$cart_toggle = isset($_POST['cart_toggle']) && $_POST['cart_toggle'] == '1';
		
				// Store the checkbox value in a session variable
				$_SESSION['cart_toggle'] = $cart_toggle;
		
				$message = $language['settings_saved1'];
				HEADER('LOCATION: companysettings.php?msg='.$message); 
				}//form validations
				if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settings_submit'])) {
					$bulk_fulfillment = isset($_POST['bulk_fulfillment']) && $_POST['bulk_fulfillment'] == '1';
			
					// Store the checkbox value in a session variable
					$_SESSION['bulk_fulfillment'] = $bulk_fulfillment;
			
					$message = $language['settings_saved1'];
					HEADER('LOCATION: companysettings.php?msg='.$message); 
					}//form validations
	
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
		<script src="../../assets/js/pages/toggleFunctionality.js"></script>
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
					if(isset($_GET['msg']) && $_GET['msg'] != '') { 
							echo '<div class="alert alert-success">';
							echo $_GET['msg'];
							echo '</div>';
					}
					if(isset($message) && $message != '') { 
							echo '<div class="alert alert-success">';
							echo $message;
							echo '</div>';
					}
				?>
					<div class="panel panel-white alert alert-default">
						<div class="panel-heading clearfix">
							<div class="panel-body" >
								<form  class="form-horizontal"  action ="<?php $_SERVER['PHP_SELF']; ?>" method="POST" >
								<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
                                        <strong>1- General Settings</strong>
                                </div>
								<div class="form-group">
									<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['site_name']; ?>:</label>
									<div class="col-sm-9">
                                    <input type="text" id="name-input" name="site_name" class="form-control" placeholder=" Company Name" value="<?php echo get_option('site_name'); ?>"  required>
									</div>
                                </div>
                                <div class="form-group">
									<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['default_system_language']; ?>:</label>
                                    <div class="col-sm-9">
									<select name="language" class="form-control">
										<option <?php if(get_option('language') == 'english'){ echo "selected='selected'"; } ?> value="english">English</option>
									</select>
									</div>
                                </div>
								<div class="form-group">
									<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['disable_login']; ?>:</label>
									<div class="col-sm-9">
										<input type="checkbox" class="form-control" style="text-align:left" name="disable_login" <?php if(get_option('disable_login') == '1'){echo 'checked="checked"'; }?> value="1" title="<?php echo $language['disable_login_check_title']; ?>" disabled />
									</div>
								</div>
                        
								<div class="form-group">
									<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['notify_user_group']; ?>:</label>
									<div class="col-sm-9">
										<input type="checkbox" class="form-control" style="text-align:left" name="notify_user_group" <?php if(get_option('notify_user_group') == '1'){echo 'checked="checked"'; }?> value="1" title="<?php echo $language['notify_user_group_title']; ?>" disabled />
									</div>
								</div>
                        
								<div class="form-group">
									<label for="input-Default" class="col-sm-3 control-label"><?php echo $language["default_system_user_type"]; ?>:</label>
									<div class="col-sm-9">
										<select name="register_user_level" class="form-control">
												<option value=""><?php echo $language["select_user_type"]; ?></option>
												<?php $new_userlevel->userlevel_options(get_option('register_user_level')); ?>	                            
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['assigned_orders']; ?>:</label>
									<div class="col-sm-9">
										<input type="checkbox" class="form-control" style="text-align:left" name="assigned_orders" <?php echo $assigned_orders; ?> value="1" title="<?php echo $language['assigned_orders']; ?>" />
									</div>
								</div>
									<div class="form-group">
										<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['cart_toggle']; ?>:</label>
										<div class="col-sm-9">
											<input type="checkbox" class="form-control" style="text-align:left" name="cart_toggle" <?php echo $cart_toggle; ?> value="1" title="<?php echo $language['cart_toggle']; ?>" />
										</div>
									</div>
									<div class="form-group">
										<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['bulk_fulfillment']; ?>:</label>
										<div class="col-sm-9">
											<input type="checkbox" class="form-control" style="text-align:left" name="bulk_fulfillment" <?php echo $bulk_fulfillment; ?> value="1" title="<?php echo $language['bulk_fulfillment']; ?>" />
										</div>
									</div>
									<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
											<strong>2- Mail Settings</strong>
									</div>
									<div class="form-group">
										<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['email_from']; ?>*:</label>
										<div class="col-sm-9">
											<input type="text" name="email_from" class="form-control" value="<?php echo get_option('email_from'); ?>" required />
										</div>
									</div>
							
									<div class="form-group">
										<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['reply_to']; ?>*:</label>
										<div class="col-sm-9">
											<input type="text" name="email_to" class="form-control" value="<?php echo get_option('email_to'); ?>" required />
										</div>
									</div>
							
									<div class="form-group">
										<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['activate_without_verification']; ?>:</label>
										<div class="col-sm-9">
											<input type="checkbox" name="register_verification" class="form-control" style="text-align:left" <?php if(get_option('register_verification') == '1'){echo 'checked="checked"'; }?> value="1" title="<?php echo $language['activate_without_2']; ?>" disabled />
										</div>
									</div>
									
									
									<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
											<strong>3- Login & Sessions</strong>
									</div>
							
									<div class="form-group">
										<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['session_timeout']; ?>:</label>
										<div class="col-sm-9">
											<input type="text" name="session_timeout" class="form-control" value="<?php echo get_option('session_timeout'); ?>" />
										</div>
									</div>
									
							
									<div class="form-group">
										<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['maximum_login_attempts']; ?>:</label>
										<div class="col-sm-9">
											<input type="text" name="maximum_login_attempts" class="form-control" value="<?php echo get_option('maximum_login_attempts'); ?>" />
										</div>
									</div>
							
									<div class="form-group">
										<label for="input-Default" class="col-sm-3 control-label"><?php echo $language['wrong_attempts_time']; ?>:</label>
										<div class="col-sm-9">
											<input type="text" name="wrong_attempts_time" class="form-control" value="<?php echo get_option('wrong_attempts_time'); ?>" />
										</div>
									</div>
							
									<div class="form-group">
									<div class="col-sm-offset-3 col-sm-9">
										<input type="hidden" name="settings_submit" value="Yes" />
										<button type="submit" id="submit" class="btn btn-info btn-addon" style="margin-top:10px;margin-bottom:-14px;" ><i class="fa fa-save"></i> <?php echo $language['submit_button']; ?></button>
										<a href="warehouse.php" class="btn btn-default" style="margin-top:10px;margin-bottom:-14px;" ><?php echo $language['cancel_button']; ?></a>
									</div>   
									</div>
									</form>
								
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
</html>

													