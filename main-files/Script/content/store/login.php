<?php
// if language session is already exist, then delete iterator_apply
if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != '') { 
		HEADER('LOCATION: dashboard.php');
	} 
if (isset($_SESSION['language'])) {
	session_start();
	session_unset();
	session_destroy();
}
if (file_exists("includes/db_connect.php")){
	include('system_load.php');
} else {
	HEADER('LOCATION: install.php');
}
	//This loads system.	
	$new_user = new Users; //creating user object.
	
	//Activation of user confirm email id
	if(isset($_GET['confirmation_code']) && $_GET['confirmation_code'] != '' && $_GET['user_id'] != '') {
		$confirmation_code = $_GET['confirmation_code'];
		$user_id = $_GET['user_id'];
		$message = $new_user->match_confirm_code($confirmation_code,$user_id);
	}
	
	
	if(isset($_POST['login']) && $_POST['login']==1) { 
		extract($_POST);
		if($email == '') { 
			$message = $language['login_email_error'];
		} else if($password == '') { 
			$message = $language['login_password_error'];
		}//validation ends here.
		$message = $new_user->login_user($email, $password);
		if($message == 1) {
			/*if(get_option('disable_login') == '1' && $new_user->user_type != 'admin') { 
				$message = $language['login_disabled_temporary'];	
			} else {*/
			$_SESSION['user_id'] = $new_user->user_id;
			$_SESSION['first_name'] = $new_user->first_name;
			$_SESSION['last_name'] = $new_user->last_name;
			$_SESSION['username'] = $new_user->username;
			$_SESSION['email'] = $new_user->email;
			$_SESSION['status'] = $new_user->status;
			$_SESSION['user_type'] = $new_user->user_type;
			$_SESSION['timeout'] = time();
			//Setting user meta information.
			$user_ip = get_client_ip();//Function is inside function.php to get ip
			$new_user->set_user_meta($_SESSION['user_id'], 'last_login_time', date("Y-m-d H:i:s")); //setting last login time.
			$new_user->set_user_meta($_SESSION['user_id'], 'last_login_ip', $user_ip); //setting last login IP.
			$new_user->set_user_meta($_SESSION['user_id'], 'login_attempt', '0'); //On login success default loign attempt is 0.
			$new_user->set_user_meta($_SESSION['user_id'], 'login_lock', 'No'); //setting last login time.
			
			$message = $language['login_success_message'];
			redirect_user($new_user->user_type); //Checks authentication and redirect user as per his/her level.
			//}
		}//setting session variables if user loged in successful!
	}//login process ends here if form submits

	$page_title = $language['login_title']; //You can edit this to change your page title.
	$sub_title = "Please login below to access the dashboard.";


	
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
        <title>MyWarehouse</title>

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
    </head>
    <body>
        
        <!-- Page Container -->
        <div class="page-container">
                <!-- Page Inner -->
				
                <div class="page-inner login-page">
                    <div id="main-wrapper" class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6 col-md-3 login-box">
                                <h4 class="login-title">Sign in to your account</h4>
								<?php
									if(isset($message) && $message != '') { 
										echo '<div class="alert alert-danger">';
										echo $message;
										echo '</div>';
									}
									if(isset($_GET['message']) && $_GET['message'] != '') { 
										echo '<div class="alert alert-danger">';
										echo $_GET['message'];
										echo '</div>';
									}				
								?>
                                <form action="<?php $_SERVER['PHP_SELF']?>" id="login_form" name="login" method="post">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Email address</label>
                                        <input type="email" class="form-control" id="exampleInputEmail1" name="email" required="required">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Password</label>
                                        <input type="password" class="form-control" id="exampleInputPassword1" name="password" required="required">
                                    </div>
									 <div class="form-group">
									<input type="hidden" value="1" name="login" />
									<input type="submit" class="form-control" style="background-color:#0d47a1;color:#FFF" value="Login" />
									</div>
									
									<script>
										$(document).ready(function() {
											// validate the register form
											$("#login_form").validate();
										});
									</script>
                                    <div class="form-group">
                                    <!--<a href="register.php" class="form-control" style="border:dashed 1px #0d47a1;text-align:center;color:#0d47a1">Register</a><br>-->
									</div>
                                    <a  class="text-info small"><small></small>Forgot your password? Please, contact your administrator !!</small></a>
                                </form>
                            </div>
                        </div>
                    </div>
            </div><!-- /Page Content -->
        </div><!-- /Page Container -->
        
        
        <!-- Javascripts -->
        <script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
        <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
        <script src="../../assets/plugins/switchery/switchery.min.js"></script>
        <script src="../../assets/js/space.min.js"></script>
    </body>
</html>