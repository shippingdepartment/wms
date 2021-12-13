<?php
	//Page display settings.
	$page_title = "Installation!"; //You can edit this to change your page title.
	$message='';
	// if installation already exist
	if ( file_exists("includes/db_connect.php")){
		require_once("includes/functions.php"); //option functions file.
		require_once("includes/db_connect.php"); //Database connection file.
		require_once("includes/options.php"); //Database connection file.
		//Check if installation is already complete.
		$installation = get_option('installation');
		if($installation == 'Yes') { 
			HEADER('LOCATION: dashboard.php');
			exit();
		}
	} else {
	//installation form processing when submits.
				if(isset($_POST['install']) && $_POST['install_submit'] == 'Yes') {
					extract($_POST);
					//validation to check if fields are empty!
					if($hostname == '') { 
						echo '<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Database Host should not be empty !</div></p>';
					} elseif($dbname == '') { 
						echo '<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Database Name should not be empty !</div></p>';
					} elseif($username == '') { 
						echo '<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Database Password should not be empty !</div></p>';
					} elseif($site_url == '') { 
						echo '<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Site url cannot be empty!</div></p>';
					} else if($email_from == '') { 
						echo '<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Email from cannot be empty!</div>';
					} else if($email_to == '') { 
						echo '<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Reply to cannot be empty!</div>';
					} else if($warh_name == '') { 
						echo '<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Warehouse name cannot be empty!</div>';
					} else if($email == '') { 
						echo '<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Admin email cannot be empty!</div>';
					} else if($password == '') { 
						echo '<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Admin Password cannot be empty!</div>';
					} else {
						@$db1 = new mysqli($hostname, $username, $dbpass, $dbname);
						if($db1->connect_errno > 0){
							$message .='<br><br><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Unable to connect to database ['.$db1->connect_error.']</div>';
						}	else {
							$message .=' <h4 style="margin-top:25px;">Data is being installed now ... Please wait ...</h4>';										
		
							// Create config files
							$myfile = fopen("includes/db_connect.php", "w") or die("Unable to open config file!");
																				
							$data = '<?php'.PHP_EOL;
							$data .='define ("DB_HOST", "'.$hostname.'");'.PHP_EOL; 
							$data .='define ("DB_USER", "'.$username.'");'.PHP_EOL;
							$data .='define ("DB_PASS", "'.$dbpass.'");'.PHP_EOL;
							$data .='define ("DB_NAME", "'.$dbname.'");'.PHP_EOL;
							$data .='$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);'.PHP_EOL;
							$data .='if($db->connect_errno > 0){'.PHP_EOL;
							$data .='die("Unable to connect to database [".$db->connect_error."]");'.PHP_EOL;
							$data .='}'.PHP_EOL;
							$data .='?>'.PHP_EOL;
							fwrite($myfile, $data);
							fclose($myfile);
							$myfile1 = fopen("includes/config.php", "w") or die("Unable to open config file!");
							$data1 = '<?php'.PHP_EOL;
							$data1 .='$dbhost_name = "'.$hostname.'";'.PHP_EOL; 
							$data1 .='$database = "'.$dbname.'";'.PHP_EOL;
							$data1 .='$username = "'.$username.'";'.PHP_EOL;
							$data1 .='$password = "'.$dbpass.'";'.PHP_EOL;
							$data1 .='try {'.PHP_EOL;
							$data1 .='$dbo = new PDO("mysql:host=".$dbhost_name.";dbname=".$database, $username, $password);'.PHP_EOL;
							$data1 .='} catch (PDOException $e) {'.PHP_EOL;
							$data1 .='print "Error!: " . $e->getMessage() . "<br/>";'.PHP_EOL;
							$data1 .='die();'.PHP_EOL;
							$data1 .='}'.PHP_EOL;
							$data1 .='?>'.PHP_EOL;
							fwrite($myfile1, $data1);
							fclose($myfile1);
							require_once("includes/functions.php"); //option functions file.
							require_once("includes/db_connect.php"); //Database connection file.
							require_once("includes/options.php"); //Database connection file.

								//Install tables
							require_once("includes/database_installation.php");
							set_option('redirect_on_logout', 'index.php');
							set_option('register_user_level', 'subscriber');
							set_option('session_timeout', '180');
							set_option('maximum_login_attempts', '10');
							set_option('wrong_attempts_time', '10');
							//adding site url
							set_option('site_url', $site_url);
							set_option('site_name', $site_name);
							set_option('email_from', $email_from);
							set_option('email_to', $email_to);
							set_option('installation', 'Yes');
							set_option('version', '1.4');
							set_option('language', $language);
							set_option('redirect_on_logout', 'index.php');
							set_option('register_user_level', 'subscriber');
							set_option('session_timeout', $session_timeout);
							set_option('maximum_login_attempts', $maximum_login_attempts);
							set_option('wrong_attempts_time', $wrong_attempts_time);
							install_admin($first_name, $last_name, $email, $password);
							insert_warehouse($warh_name, $warh_address, $warh_country, $warh_area, $warh_volume, $warh_freezone);
							//form validations
							$message .='<div class="alert alert-success" role="alert" style="font-size:16px">';
							$message .='<i class="fa fa-check-circle"></i> Congratulations! Installation finished successfully...</div>';
													   
							$message .='<p align="center"><a href="login.php" class="btn btn-primary"><i class="fa fa-sign-in" aria-hidden="true"></i> GO TO LOGIN PAGE </p>';
							
						}
									
																	
			}
		}
	}
	
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
        

        <!-- Styles -->
        <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
        <link href="../../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="../../assets/plugins/icomoon/style.css" rel="stylesheet">
        <link href="../../assets/plugins/uniform/css/default.css" rel="stylesheet"/>
        <link href="../../assets/plugins/switchery/switchery.min.css" rel="stylesheet"/>
      
        <!-- Theme Styles -->
        <link href="../../assets/css/space.min.css" rel="stylesheet">
        <link href="../../assets/css/custom.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
		<style>
		.numberCircle {
			border-radius: 50%;
			behavior: url(PIE.htc); /* remove if you don't care about IE8 */
			width: 36px;
			height: 36px;
			padding: 8px;
			background: #fff;
			border: 2px solid black;
			color: black;
			text-align: center;
			font: 32px Arial, sans-serif;
			display: inline-block;
		}
		.larger {
			font-size: 32px
		}
		
		</style>
		
    </head>
    <body >
        <!-- Page Content -->
        <div class="container-fluid">
			<!--<div class="page-content">-->
			
				<div class="container-fluid" >
                    <div class="panel panel-white alert alert-default" style="margin-top:20px">
						<div class="panel-heading clearfix">
							<div class="panel-body" >
								<h3 class="text-info text-center">MyWarehouse - V1.4 - Installation</h3>
							</div>
						</div>
					<?php
					
					if(isset($message) && $message != '') { 
						echo $message;
						exit();
						
					}
				?>
					</div>
					<div id="main-wrapper">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-white">
									<div class="panel-body">
																	
																	
															
														
														
														
														
									
									
										<div id="rootwizard">
											<ul class="nav nav-tabs" role="tablist">
												<li role="presentation" class="active"><a href="#tab1" data-toggle="tab"><span class="fa-stack"><span class="fa fa-circle fa-stack-2x" style="color:#4286f4"></span><span class="fa-stack-1x" style="color:white">1</span></span>Database Information</a></li>
												<li role="presentation"><a href="#tab2" data-toggle="tab"><span class="fa-stack"><span class="fa fa-circle fa-stack-2x" style="color:#4286f4"></span><span class="fa-stack-1x" style="color:white">2</span></span>Company Settings</a></li>
												<li role="presentation"><a href="#tab3" data-toggle="tab"><span class="fa-stack"><span class="fa fa-circle fa-stack-2x" style="color:#4286f4"></span><span class="fa-stack-1x" style="color:white">3</span></span>Warehouse Settings</a></li>
												<li role="presentation"><a href="#tab4" data-toggle="tab"><span class="fa-stack"><span class="fa fa-circle fa-stack-2x" style="color:#4286f4"></span><span class="fa-stack-1x" style="color:white">4</span></span>Administrator Settings</a></li>
												<li role="presentation"><a href="#tab5" data-toggle="tab"><span class="fa-stack"><span class="fa fa-circle fa-stack-2x" style="color:#4286f4"></span><span class="fa-stack-1x" style="color:white">5</span></span>System Information</a></li>
												<li role="presentation"><a href="#tab6" data-toggle="tab"><span class="fa-stack"><span class="fa fa-circle fa-stack-2x" style="color:#4286f4"></span><span class="fa-stack-1x" style="color:white">6</span></span>Installation ...</a></li>
											</ul>
											<div class="progress progress-sm m-t-sm">
												<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
												</div>
											</div>
											<form id="wizardForm" class="form-horizontal" name="set_install" action="install.php" method="post">
												<div class="tab-content">
													<div class="tab-pane fade active  fade in" id="tab1">
														<div class="row m-b-lg">
															<div class="col-md-6">
																<div class="row">
																	<div class="form-group col-md-12">
																		<label for="exampleInputName"><b>Host Name * </b></label>
																		<input type="text" class="form-control" name="hostname" value="<?php if(isset($_POST['hostname'])){ echo $_POST['hostname']; } else { echo 'localhost'; } ?>"  Required />
																		
																	</div>
																	<div class="form-group col-md-12">
																		<label for="exampleInputEmail"><b>Database Name *</b></label>
																		<input class="form-control" type="text" name="dbname" value="<?php if(isset($_POST['dbname'])){ echo $_POST['dbname']; } ?>"  Required />
																	</div>
																	<div class="form-group col-md-12">
																		<label for="exampleInputName"><b>User Name * </b></label>
																		<input type="text" class="form-control" name="username" value="<?php if(isset($_POST['username'])){ echo $_POST['username']; } else { echo 'root'; } ?>" Required  />
																	</div>
																	<div class="form-group col-md-12">
																		<label for="exampleInputName"><b>Database Password * </b></label>
																		<input type="text" class="form-control" name="dbpass" value="<?php if(isset($_POST['dbpass'])){ echo $_POST['dbpass']; } ?>" />
																	</div>
																	
																</div>
															</div>
															<div class="col-md-6">
																<h3>Database Configuration</h3>
																<p><span class="text-danger"> <i class="fa fa-exclamation-circle"></i><b> Important :</b><span class="text-default"> You should have created a database and assigned the user privileges.</span></span></p>
																<p><span class="text-default">Then the installation of the tables and the configuration files will be done automatically along this installation page.</p></span>
																
															</div>
														</div>
														<ul class="pager wizard">
															<li class="previous"><a href="#" class="btn btn-default">Previous</a></li>
															<li class="next"><a href="#" class="btn-default">Next</a></li>
														</ul>
													</div>
													<!-- second tab -->
													<div class="tab-pane" id="tab2">
														<div class="row m-b-lg">
															<div class="col-md-6">
																<div class="row">
																	<div class="form-group col-md-12">
																		<label for="exampleInputEmail"><b>Company Name *</b></label>
																		<input class="form-control" type="text" name="site_name" value="<?php if(isset($_POST['site_name'])){ echo $_POST['site_name']; } ?> " Required />
																	</div>
																	<div class="form-group col-md-12">
																		<label for="exampleInputName"><b>Company Website *</b></label>
																		<input type="text" class="form-control" name="site_url" value="<?php if(isset($_POST['site_url'])){ echo $_POST['site_url']; } ?> " required /><small style="color:#4286f4">Please include / at end of site url e.g http://mywarehouse.com/ , http://localhost/ </small>
																	</div>
																	<div class="form-group col-md-12">
																		<label for="exampleInputName"><b>Language:</b></label>
																		<select name="language" class="form-control">
																			<option value="english" selected>English</option>
																		</select>
																		
																	</div>
																	<div class="form-group col-md-12">
																		<label for="exampleInputName"><b>Email From*:</b></label>
																		<input class="form-control" type="email" name="email_from" value="<?php if(isset($_POST['email_from'])){ echo $_POST['email_from']; } ?>"  required />
																		<small style="color:#4286f4">Email from which you send emails </small>
																		
																	</div>
																	<div class="form-group col-md-12">
																		<label for="exampleInputName"><b>Reply To*:</b></label>
																		<input class="form-control" type="email" name="email_to" value="<?php if(isset($_POST['email_to'])){ echo $_POST['email_to']; } ?>"  required />
																		<small style="color:#4286f4">Email on which you receive emails </small>
																	</div>
																	
																	
																	
																</div>
																
															</div>
															<div class="col-md-6">
																<h3>Personal Info</h3>
																<p>Enter the company details.</p>
																<p>Email Credits are important to send emails between users and also for contacting suppliers, customers, ...</p>
																
															</div>
														</div>
														<ul class="pager wizard">
															<li class="previous"><a href="#" class="btn btn-default">Previous</a></li>
															<li class="next"><a href="#" class="btn btn-default">Next</a></li>
														</ul>
													</div>
													
													<div class="tab-pane fade" id="tab3">
														<div class="row m-b-lg">
															<div class="col-md-6">
																<div class="row">
																	<div class="form-group col-md-12">
																		<label for="exampleInputEmail"><b> Default Warehouse Name *</b></label>
																		<input class="form-control" type="text" name="warh_name" value="<?php if(isset($_POST['warh_name'])){ echo $_POST['warh_name']; } ?>"  Required />
																	</div>
																	<div class="form-group col-md-12">
																		<label for="exampleInputName"><b>Default Warehouse Address </b></label>
																		<input type="text" class="form-control" name="warh_address" value="<?php if(isset($_POST['warh_address'])){ echo $_POST['warh_address']; } ?>"  />
																	</div>
																	<div class="form-group col-md-12">
																		<label for="exampleInputName"><b>Default Location (country) </b></label>
																		<input type="text" class="form-control" name="warh_country" value="<?php if(isset($_POST['warh_country'])){ echo $_POST['warh_country']; } ?>"  />
																	</div>
																	<div class="form-group col-md-3">
																		<label for="exampleInputName"><b>Warh. Area </b></label>
																		<div style="margin-bottom:15px;" class="input-group">
																		<input type="number" class="form-control" name="warh_area" value="<?php if(isset($_POST['warh_area'])){ echo $_POST['warh_area']; } ?>"  Required /><span class="input-group-addon">m<sup>2</sup></span>
																		</div>
																	</div>
																	<div class="form-group col-md-3">
																		<label for="exampleInputName"><b>Warh. Volume </b></label>
																		<div style="margin-bottom:15px;" class="input-group">
																		<input type="number" class="form-control" name="warh_volume" value="<?php if(isset($_POST['warh_volume'])){ echo $_POST['warh_volume']; } ?>"  Required /><span class="input-group-addon">m<sup>3</sup></span>
																		</div>
																	</div>
																	<div class="form-group col-md-3">
																		<label for="exampleInputName"><b>Warh. Free Zone </b></label>
																		<div style="margin-bottom:15px;" class="input-group">
																		<input type="number" class="form-control" name="warh_freezone" value="<?php if(isset($_POST['warh_freezone'])){ echo $_POST['warh_freezone']; } ?>"  Required /><span class="input-group-addon">m<sup>3</sup></span>
																		</div>
																		<small class="text-info"><i class="fa fa-question-circle"></i> Free zone is important for each warehouse. It is the area in which workers and machinery move freely.</small>
																	</div>
																	
																	
																	
																</div>
															</div>
															<div class="col-md-6">
																<h3>Warehouse Info</h3>
																<p>At least one Warehouse should be created for first access. After, you can create the number you want of warehouses and users</p>
																<p>The warehouse dimensions (Area, volume, free zone ) are not compulsory but strongly required. If you don't enter them here, you can add them later.</p>
															</div>
														</div>
														<ul class="pager wizard">
															<li class="previous"><a href="#" class="btn btn-default">Previous</a></li>
															<li class="next"><a href="#" class="btn btn-default">Next</a></li>
														</ul>
													</div>
													
													
													
													<div class="tab-pane fade" id="tab4">
														<div class="row">
															<div class="col-md-3">
																
																<div class="m-t-md">
																	<address>
																		<strong>Admin Information</strong><br>
																		Basic Information<br>
																		Required to connect as Admin<br>
																	</address>
																</div>
															</div>
															<div class="col-md-9">
																<div class="form-group col-md-12">
																	<label for="exampleInputName">First Name*:</label>
																	<input class="form-control" type="text" name="first_name" value="<?php if(isset($_POST['first_name'])){ echo $_POST['first_name']; } ?>"  Required />
																	
																</div>
																<div class="form-group col-md-12">
																	<label for="exampleInputName">Last Name*:</label>
																	<input class="form-control" type="text" name="last_name" value="<?php if(isset($_POST['last_name'])){ echo $_POST['last_name']; } ?>"  Required />
																	
																</div>
																<div class="form-group col-md-12">
																	<label for="exampleInputName">Email*:</label>
																	<input class="form-control" type="email" name="email" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } ?>"  required />
																</div>
																<div class="form-group col-md-12">
																	<label for="exampleInputName">Password*:</label>
																	<input class="form-control" type="password" name="password" value="<?php if(isset($_POST['password'])){ echo $_POST['password']; } ?>"  required />
																</div>
															</div>
														</div>
														<ul class="pager wizard">
															<li class="previous"><a href="#" class="btn btn-default">Previous</a></li>
															<li class="next"><a href="#" class="btn btn-default">Next</a></li>
														</ul>
													</div>
													<div class="tab-pane fade" id="tab5">
														<div class="row">
															<div class="col-md-12">
																	<div class="form-group col-md-12">
																		<label for="input-Default" class="col-sm-3 control-label">Session Time out:</label>
																		<div class="col-sm-6">
																			<input type="text" name="session_timeout" class="form-control" value="<?php if(isset($_POST['session_timeout'])){ echo $_POST['session_timeout'];} else { echo '180'; } ?>" /><small style="color:#4286f4">Minutes after that session will be closed if no action from the user. </small>
																		</div>
																	</div>
															</div>
															<div class="col-md-12">
																	<div class="form-group col-md-12">
																		<label for="input-Default" class="col-sm-3 control-label">Disable Registration:</label>
																		<div class="col-sm-6">
																			<input type="checkbox" name="disable_registration" class="form-control" style="text-align:left"  value="1" title="Disable Registration" disabled />
																		</div>
																	</div>
															</div>
													
															<div class="col-md-12">
																	<div class="form-group col-md-12">
																		<label for="input-Default" class="col-sm-3 control-label">Maximun Login Attemps:</label>
																		<div class="col-sm-6">
																			<input type="text" name="maximum_login_attempts" required class="form-control" value="<?php if(isset($_POST['maximum_login_attempts'])){ echo $_POST['maximum_login_attempts'];} else { echo '5'; } ?>" /><small style="color:#4286f4">Number of times user could re-try to login </small>
																		</div>
																	</div>
															</div>
													
															<div class="col-md-12">
																	<div class="form-group col-md-12">
																		<label for="input-Default" class="col-sm-3 control-label">Wrong Attemps Time (min):</label>
																		<div class="col-sm-6">
																			<input type="text" name="wrong_attempts_time" required class="form-control" value="<?php if(isset($_POST['wrong_attempts_time'])){ echo $_POST['wrong_attempts_time'];} else { echo '3'; } ?>" /><small style="color:#4286f4">Period of time (in minutes) during which the login is blocked after reaching the maximum login attempts. </small>
																		</div>
																	
																	</div>
															</div>
														</div>
														<ul class="pager wizard">
															<li class="previous"><a href="#" class="btn btn-default">Previous</a></li>
															<input type="hidden" name="install_submit" value="Yes" />
															<li class="next"><button type="submit" name="install" class="btn btn-info btn-addon" >Install Now</button></li>
														</ul>
													</div>
													<div class="tab-pane fade" id="tab6">
														<div class="alert alert-info m-t-sm m-b-lg" role="alert" style="font-size:16px">
														    Installion in process ... Please wait ...
														</div>
													</div>
												</div>
											</form>
											<script>
												$(document).ready(function() {
													// validate the Installation form
													$("#wizardForm").validate();
												});
											</script>
										</div>
									</div>
								</div>
							</div>
						</div><!-- Row -->
					</div><!-- Main Wrapper -->
					<!-- Page Inner -->
                </div><!-- Main Wrapper 
            </div><!-- /Page Inner -->
        </div><!-- /Page Content -->
        
        
        
       <!-- Javascripts -->
        <script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
        <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
        <script src="../../assets/plugins/switchery/switchery.min.js"></script>
        <script src="../../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
        <script src="../../assets/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
        <script src="../../assets/js/space.min.js"></script>
        <script src="../../assets/js/pages/form-wizard.js"></script>
    </body>
</html>