<?php

//header('Content-Type: text/html; charset=UTF-8');
include('system_load.php');
//This loads system.

//user Authentication.
authenticate_user('admin');

//User object.
$new_user = new Users;
$u = '';
//user level object
$new_userlevel = new Userlevel;
if (isset($_GET['t']) && isset($_GET['u'])) {
	if ($_GET['t'] == 'ed') {
		$u =	$_GET['u'];
	} else {
		HEADER('LOCATION: warehouse.php');
	}
}


//#####################################
if (isset($_GET['t']) && $_GET['t'] == 'ed') {

	if (isset($_POST['password']) && $_POST['password'] != '') {
		if ($_POST['password'] == $_POST['confirm_password']) {
			$password_set = $_POST['password'];
		} else {
			$message = "<div class='alert alertdanger'><i class='fa fa-exclamation-triangle'> The password does not match.</div>";
		}
	} else {
		$password_set = '';
	}
	if (isset($_POST['update_user']) && $_POST['update_user'] == '1') {
		extract($_POST);
		if ($password != $confirm_password) {
			$message = "<div class='alert alertdanger'><i class='fa fa-exclamation-triangle'></i> The password does not match.</div>";
		} else {
			$message = $new_user->update_user($u, $user_type, $first_name, $last_name, $gender, $date_of_birth, $address, $mobile, $phone, $username, $email, $password_set, $status, $user_type, $user_function);
		}
	}
} //update user submission.

if (isset($_GET['t']) && $_GET['t'] == 'ed') {
	$new_user->set_user($u, $_SESSION['user_type'], $_SESSION['user_id']);
} //setting user data if editing. 	

//add user processing.
if (isset($_POST['add_user']) && $_POST['add_user'] == '1') {
	extract($_POST);
	if ($first_name == '') {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> The First Name is required !</div>";
	} else if ($last_name == '') {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> The Last Name is required !</div>";
	} else if ($mobile == '') {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> The Mobile Number is required !</div>";
	} else if ($username == '') {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> The Username is required !</div>";
	} else if ($email == '') {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> The Email is required !</div>";
	} else if ($password == '') {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> The password can not be empty !</div>";
	} else if ($password != $confirm_password) {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> The 2 passwords does not mutch!</div>";
	} else if ($status == '0') {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> Please select the status of the user !</div>";
	} else if ($user_type == '0') {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> Please select the Type of user !</div>";
	} else if ($user_function == '') {
		$message = "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> Please select the user Function !</div>";
	} else {
		$message = $new_user->add_user($first_name, $last_name, $gender, date('Y-m-d', strtotime($date_of_birth)), $address, $mobile, $phone, $username, $email, $password, $status, $user_type, $user_function);
		HEADER('LOCATION: users.php?t=all');
	}
} //add user processing ends here.

if (isset($_GET['t']) && $_GET['t'] == 'ed') {
	$page_title = 'Edit User';
} else {
	$page_title = 'New User';
} //page title set.
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
	<link href="../../assets/plugins/uniform/css/default.css" rel="stylesheet" />
	<link href="../../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
	<link href="../../assets/plugins/nvd3/nv.d3.min.css" rel="stylesheet">
	<link href="../../assets/plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css" />
	<link href="../../assets/plugins/datatables/css/jquery.datatables_themeroller.css" rel="stylesheet" type="text/css" />
	<link href="../../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />

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
		if (isset($_SESSION['warehouse_id'])) {
			require_once("includes/sidebar.php");
		} else {
			require_once("includes/sidebaradmin.php");
		}
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
					<h3 class="breadcrumb-header"><?php echo $page_title; ?></h3>
				</div>

				<?php
				//display message if exist.
				if (isset($message) && $message != '') {
					//echo '<div class="alert alert-success">';
					echo $message;
					//echo '</div>';
				}
				?>


				<div class="panel panel-white alert alert-default">
					<div class="panel-heading clearfix">
						<div class="panel-body">
							<form id="wizardForm" class="form-horizontal" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
								<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
									<strong>1- General Information</strong>
								</div>

								<div class="form-group">
									<div class="col-sm-10">
										<input type="text" id="name-input" name="first_name" class="form-control" placeholder="Full Name" value="<?php if (isset($_POST['add_user'])) {
																																						echo $first_name;
																																					} else {
																																						echo $new_user->first_name;
																																					} ?>" required>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-10">
										<input type="text" id="address-input" name="last_name" class="form-control" placeholder="Last Name" value="<?php if (isset($_POST['add_user'])) {
																																						echo $last_name;
																																					} else {
																																						echo $new_user->last_name;
																																					} ?>" required>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-10">
										<select class="form-control" name="gender" required>
											<option value=''>Select Gender</option>
											<option value="Male" <?php if (isset($_POST['add_user']) && ($gender == 'Male')) {
																		echo 'selected="selected"';
																	} else {
																		if ($new_user->gender == 'Male') {
																			echo 'selected="selected"';
																		}
																	} ?>>Male</option>
											<option value="Female" <?php if (isset($_POST['add_user']) && ($gender == 'Female')) {
																		echo 'selected="selected"';
																	} else {
																		if ($new_user->gender == 'Female') {
																			echo 'selected="selected"';
																		}
																	} ?>>Female</option>
										</select>
									</div>
								</div>
								<div class="form-group">

									<div class="col-sm-10">
										<input type="text" class="form-control date-picker" name="date_of_birth" placeholder="Date of Birth " value="<?php if (isset($_POST['add_user'])) {
																																							echo $date_of_birth;
																																						} else {
																																							echo $new_user->date_of_birth;
																																						} ?>" />
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-10">
										<textarea name="address" class="form-control" placeholder="Address"><?php if (isset($_POST['add_user'])) {
																												echo $address;
																											} else {
																												echo $new_user->address;
																											} ?></textarea>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-10">
										<input type="text" class="form-control" id="phone-input" name="mobile" onkeypress="return isNumberKey(event)" placeholder="Mobile Number" value="<?php if (isset($_POST['add_user'])) {
																																																echo $mobile;
																																															} else {
																																																echo $new_user->mobile;
																																															} ?>" required />
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-10">
										<input type="text" class="form-control" id="phone-input" name="phone" onkeypress="return isNumberKey(event)" placeholder="Work Phone Number" value="<?php if (isset($_POST['add_user'])) {
																																																echo $phone;
																																															} else {
																																																echo $new_user->phone;
																																															} ?>" required />
									</div>
								</div>
								<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
									<strong>2- User Account Details</strong>
								</div>
								<div class="form-group">
									<div class="col-sm-10">
										<input type="email" class="form-control" name="email" placeholder="Email" value="<?php if (isset($_POST['add_user'])) {
																																echo $email;
																															} else {
																																echo $new_user->email;
																															} ?>" required="required" />
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-10">
										<input type="text" class="form-control" name="username" placeholder="User Name" value="<?php if (isset($_POST['add_user'])) {
																																	echo $username;
																																} else {
																																	echo $new_user->username;
																																} ?>" required="required" />
									</div>
								</div>
								<?php if (isset($_GET['t']) && $_GET['t'] == 'ed') { ?>
									<div class="form-group">
										<div class="col-sm-10">
											<input type="password" class="form-control" name="password" placeholder="Password" value="" /><small class="text-primary"><i class="fa fa-question-circle"></i> Leave blank if you don't want to change password</small>
										</div>
									</div>

									<div class="form-group">
										<div class="col-sm-10">
											<input type="password" class="form-control" name="confirm_password" placeholder="Re-Type Password" value="" />
										</div>
									</div>
								<?php } else { ?>
									<div class="form-group">
										<div class="col-sm-10">
											<input type="password" class="form-control" name="password" placeholder="Password" value="" required="required" />
										</div>
									</div>

									<div class="form-group">
										<div class="col-sm-10">
											<input type="password" class="form-control" name="confirm_password" placeholder="Re-Type Password" value="" required="required" />
										</div>
									</div>
								<?php } ?>
								<div class="form-group">
									<div class="col-sm-10">
										<select name="status" required="required" class="form-control" id="status" class="required">
											<option value="0">Select Status</option>
											<option <?php if (isset($_POST['add_user']) && ($status == 'activate')) {
														echo 'selected="selected"';
													} else {
														if ($new_user->status == 'activate') {
															echo 'selected="selected"';
														}
													} ?> value="activate">Active</option>
											<option <?php if (isset($_POST['add_user']) && ($status == 'deactivate')) {
														echo 'selected="selected"';
													} else {
														if ($new_user->status == 'deactivate') {
															echo 'selected="selected"';
														}
													} ?> value="deactive">Deactive</option>
											<option <?php if (isset($_POST['add_user']) && ($status == 'banned')) {
														echo 'selected="selected"';
													} else {
														if ($new_user->status == 'banned') {
															echo 'selected="selected"';
														}
													} ?> value="banned">Banned</option>
											<option <?php if (isset($_POST['add_user']) && ($status == 'suspended')) {
														echo 'selected="selected"';
													} else {
														if ($new_user->status == 'suspended') {
															echo 'selected="selected"';
														}
													} ?> value="suspended">Suspended</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-10">
										<select name="user_type" class="form-control" required="required" id="user_type" class="required">
											<option value="">Select Type</option>
											<option <?php if (isset($_POST['add_user']) && ($user_type == 'admin')) {
														echo 'selected="selected"';
													} else {
														if ($new_user->user_type == 'admin') {
															echo 'selected="selected"';
														}
													} ?> value="admin">Admin</option>
											<?php $new_userlevel->userlevel_options($new_user->user_type); ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-10">
										<select name="user_function" class="form-control" required="required" id="user_function" class="required">
											<option value="">Select Function</option>
											<option <?php if (isset($_POST['add_user']) && ($user_function == 'admin')) {
														echo 'selected="selected"';
													} else {
														if ($new_user->user_function == 'admin') {
															echo 'selected="selected"';
														}
													} ?> value="admin">Admin</option>
											<option <?php if (isset($_POST['add_user']) && ($user_function == 'manager')) {
														echo 'selected="selected"';
													} else {
														if ($new_user->user_function == 'manager') {
															echo "selected='selected'";
														}
													} ?> value="manager">General Manager</option>
											<option <?php if (isset($_POST['add_user']) && ($user_function == 'storem')) {
														echo 'selected="selected"';
													} else {
														if ($new_user->user_function == 'storem') {
															echo "selected='selected'";
														}
													} ?> value="storem">Store Manager</option>
											<option <?php if (isset($_POST['add_user']) && ($user_function == 'storea')) {
														echo 'selected="selected"';
													} else {
														if ($new_user->user_function == 'storea') {
															echo "selected='selected'";
														}
													} ?> value="storea">Store Agent</option>

										</select>
									</div>
								</div>

								<div class="alert alert-default" style="font-size:16px;color:#0d47a1" role="alert">
									<div class="form-group" style="text-align:center">
										<?php
										if (isset($_GET['t']) && $_GET['t'] == 'ed') {
											echo '<input type="hidden" name="edit_user" value="' . $u . '" />';
											echo '<input type="hidden" name="update_user" value="1" />';
										} else {
											echo '<input type="hidden" name="add_user" value="1" />';
										}
										?>
										<button type="submit" id="submit" class="btn btn-info btn-addon" value="<?php if (isset($_GET['t']) && $_GET['t'] == 'ed') {
																													echo 'Update User';
																												} else {
																													echo 'Add User';
																												} ?>"><?php if (isset($_GET['t']) && $_GET['t'] == 'ed') {
																																																							echo '<i class="fa fa-refresh"></i> Update User';
																																																						} else {
																																																							echo '<i class="fa fa-plus"></i> Add User';
																																																						} ?></button>
										<button type="button" class="btn btn-default">Cancel</button>

									</div>
								</div>

							</form>
							<script type="text/javascript">
								$(document).ready(function() {
									// validate the register form
									$("#add_user").validate();
								});
							</script>
							<script>
								function isNumberKey(evt) {
									var charCode = (evt.which) ? evt.which : evt.keyCode;
									if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
										return false;
									return true;
								}


								function isNumericKey(evt) {
									var charCode = (evt.which) ? evt.which : evt.keyCode;
									if (charCode != 46 && charCode > 31 &&
										(charCode < 48 || charCode > 57))
										return true;
									return false;
								}
							</script>



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
	<script src="../../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
	<script src="../../assets/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
	<script src="../../assets/js/space.min.js"></script>
	<script src="../../assets/js/pages/form-wizard.js"></script>
	<script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script src="../../assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
</body>

</html>