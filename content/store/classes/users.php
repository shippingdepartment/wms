<?php
//users Class
class Users
{
	public $user_id;
	public $first_name;
	public $last_name;
	public $gender;
	public $date_of_birth;
	public $address1;
	public $address2;
	public $city;
	public $state;
	public $country;
	public $zip_code;
	public $mobile;
	public $phone;
	public $username;
	public $email;
	public $profile_image;
	public $description;
	public $status;
	public $user_type;
	public $address;
	public $user_function;

	function set_user_meta($user_id, $term, $value)
	{
		global $db;
		$query = "SELECT * from user_meta WHERE user_id='" . $user_id . "'";
		$result = $db->query($query) or die($db->error);
		$rows = $result->num_rows;

		if ($rows > 0) {
			//We have to update existing record. 
			$query = 'UPDATE user_meta SET
   	    			' . $term . ' = "' . $value . '"
			WHERE user_id="' . $user_id . '"';
		} else {
			//we have to add new record.
			$query = 'INSERT into user_meta(user_meta_id, user_id, ' . $term . ') VALUES(NULL, "' . $user_id . '", "' . $value . '")';
		}
		$result = $db->query($query) or die($db->error);
	} //set user meta information.

	function subscriber_options()
	{
		$query = "SELECT * from users WHERE user_type='subscriber' ORDER by first_name ASC";
		$result = $db->query($query) or die($db->error);

		$options = '';

		while ($row = $result->fetch_array()) {
			extract($row);
			//$options .= '<option value="'.$user_id.'">'.$user_id.' | '.$first_name.'</option>';
			if ($user_id == $row['user_id']) {
				$options .= '<option selected="selected" value="' . $row['user_id'] . '">' . $row['user_id'] . ' | ' . $row['first_name'] . ' ' . $row['last_name'] . ' </option>';
			} else {
				$options .= '<option value="' . $row['user_id'] . '">' . $row['user_id'] . ' | ' . $row['first_name'] . ' ' . $row['last_name'] . ' </option>';
			}
		}

		//while loop ends here.
		echo $options;
	}

	function subscriber_access_options()
	{
		global $db;
		$query = "SELECT * from users WHERE user_type='subscriber' ORDER by first_name ASC";
		$result = $db->query($query) or die($db->error);

		$options = '';

		while ($row = $result->fetch_array()) {
			extract($row);
			$options .= '<option value="' . $row['user_id'] . '">' . $row['user_id'] . ' | ' . $row['first_name'] . ' ' . $row['last_name'] . ' </option>';
		}

		//while loop ends here.
		echo $options;
	}

	function get_user_meta($user_id, $term)
	{
		global $db;
		$query = "SELECT * from user_meta WHERE user_id='" . $user_id . "'";
		$result = $db->query($query) or die($db->error);
		if ($row = $result->fetch_array()) {
			return $row[$term];
		} else {
			return '';
		}
	} //get user email ends here.

	function get_functions()
	{
		global $db;
		$query = 'SELECT * from functions ORDER by function_name ASC';
		$result = $db->query($query) or die($db->error);
		$options = '';

		while ($row = $result->fetch_array()) {

			$options .= '<option selected="selected" value="' . $row['function_name'] . '">' . $row['function_name'] . '</option>';
		}

		echo $options;
	}

	function get_user_info($user_id, $term)
	{
		global $db;
		$query = "SELECT * from users WHERE user_id='" . $user_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	} //get user email ends here.

	function register_user($first_name, $last_name, $user_type, $username, $email, $password)
	{
		global $db;
		global $language;
		//Check if user already exist
		$query = "SELECT * from users WHERE email='" . $email . "'";
		$result = $db->query($query);

		$num_user = $result->num_rows;

		if ($num_user > 0) {
			return '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . $language["email_exit_user_err"] . ' <strong>' . $email . '</strong> ' . $language["already_REgistered"] . '</div>';
			exit();
		}
		//username validation
		$query = "SELECT * from users WHERE username='" . $username . "'";
		$result = $db->query($query);

		$num_user = $result->num_rows;

		if ($num_user > 0) {
			return '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . $language["username_couldniot_add"] . ' <strong>' . $username . '</strong> ' . $language["already_REgistered"] . '</div>';
			exit();
		}
		$registration_date = date('Y-m-d');
		$password = md5($password);
		$activation_key = substr(md5(uniqid(rand(), true)), 16, 16);

		if (get_option('register_verification') != '1') {
			$status = "deactivate";
		} else {
			$status = "activate";
		}
		if ($user_type == 'admin') {
			$user_type = get_option('notify_user_group');
		}
		//adding user into database
		$query = "INSERT INTO users(user_id, first_name,last_name,username,email,password,activation_key,date_register,user_type,status) VALUES (NULL, '$first_name', '$last_name', '$username', '$email', '$password', '$activation_key', '$registration_date', '$user_type', '$status')";
		$result = $db->query($query) or die($db->error);
		$user_id = $db->insert_id;
		//Email to user
		$site_url = get_option('site_url');

		$email_message = $language["email_register_1"] . "<br />";
		$email_message .= $language["email_register_2"] . ": <strong> " . $username . '</strong><br>';
		$email_message .= $language["email_register_3"] . "<br />";
		$email_message .= "<a href='" . $site_url . "login.php?confirmation_code=" . $activation_key . "&user_id=" . $user_id . "'>" . $language["email_register_4"] . "</a>";
		$email_message .= "<br><br>" . $language["email_register_5"];

		$message = $email_message;
		$mailto = $email;
		$subject = $language["email_register_6"];

		send_email($mailto, $subject, $message);
		//Notify other users of same level on new registration.
		if (get_option('notify_user_group') == '1') :
			//message object.
			$subject = "New user registration.";
			$message = "<h2>New user on your user group.</h2>";
			$message .= "<p><strong>Name: </strong>" . $first_name . " " . $last_name . "</p>";
			$message .= "<p><strong>Email: </strong>" . $email . "</p>";
			$message .= "<p><strong>Username: </strong>" . $username . "</p>";

			$message_obj = new Messages;
			$message_obj->level_message($user_type, $subject, $message);
		endif;
		return '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' . $language['registrat_success'] . '</div>';
	} //register_user ends here.

	function facebook_login_register($first_name, $last_name, $gender, $email, $user_type)
	{
		global $db; //starting database object.
		global $language;

		$query = "SELECT * from users WHERE email='" . $email . "' OR username='" . $email . "'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;

		$registration_date = date('Y-m-d');
		$pass = randomPassword();
		$password = md5($pass);
		$status = 'activate';

		if ($user_type == 'admin') {
			$user_type = get_option('register_user_level');
		}

		if ($num_rows == 0) {
			$query = "INSERT INTO users(user_id, first_name,last_name,gender,email,password,date_register,user_type,status) VALUES (NULL, '$first_name', '$last_name', '$gender', '$email', '$password', '$registration_date', '$user_type', '$status')";
			$result = $db->query($query) or die($mysqli->error);
			$user_id = $db->insert_id;

			$email_msg = '<h1>' . $language['fb_reg_thanks'] . '.</h1>';
			$email_msg .= '<p>' . $language['fb_reg_des'] . '</p>';
			$email_msg .= '<p>Email:' . $email . '<br>';
			$email_msg .= 'Password:' . $pass . '</p>';
			$email_msg .= get_option('site_url');

			$subject = $language['fb_reg_thanks'] . ' | ' . get_option('site_name');

			send_email($email, $subject, $email_msg);
			//Notification to user group on new registration.
			if (get_option('notify_user_group') == '1') :
				//message object.
				$subject = "New user registration.";
				$message = "<h2>New user on your user group.</h2>";
				$message .= "<p><strong>Name: </strong>" . $first_name . " " . $last_name . "</p>";
				$message .= "<p><strong>Email: </strong>" . $email . "</p>";
				$message .= "<p><strong>Username: </strong>" . $username . "</p>";

				$message_obj = new Messages;
				$message_obj->level_message($user_type, $subject, $message);
			endif;
			$num_rows = 1;
		} //if user do not exist register user.

		if ($num_rows > 0) {
			$row = $result->fetch_array();

			if ($row['status'] == 'deactivate') {
				$message = $language["not_active_yet_em"];
			} else if ($row['status'] == 'activate') {
				extract($row);
				$this->user_id = $user_id;
				$this->first_name = $first_name;
				$this->last_name = $last_name;
				$this->username = $username;
				$this->email = $email;
				$this->status = $status;
				$this->user_type = $user_type;
				if ($profile_image != '') {
					$this->profile_image = $profile_image;
				} else {
					$this->profile_image = 'images/thumb.png';
				}

				$message = 1;
			} else {
				$message = $language["ban_suspend_login_con"];
			}
		} else {
			$message = $language["could_not_find_email"];
		}
		return $message;
	} //login func ends here.

	function login_user($email, $password)
	{
		global $db; //starting database object.
		global $language;
		$password = md5($password); //Converting input password to md5 to match in database.

		$query = "SELECT * from users WHERE email='" . $email . "' OR username='" . $email . "'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;


		if ($num_rows > 0) {
			$row = $result->fetch_array();


			$lock_account = $this->get_user_meta($row['user_id'], 'login_lock');
			if ($lock_account != 'No') {
				// if( $lock_account + get_option('wrong_attempts_time') * 60 > time()) { 
				// 	return $language['wrong_attempt_lock'];
				// 	exit();
				// } else { 
				$this->set_user_meta($row['user_id'], 'login_attempt', '0');
				$this->set_user_meta($row['user_id'], 'login_lock', 'No'); //setting last login time.
				// }
			}

			if ($row['password'] == $password) {
				if ($row['status'] == 'deactivate') {
					$message = $language["not_active_yet_em"];
				} else if ($row['status'] == 'activate') {
					extract($row);
					$this->user_id = $user_id;
					$this->first_name = $first_name;
					$this->last_name = $last_name;
					$this->username = $username;
					$this->email = $email;
					$this->status = $status;
					$this->user_type = $user_type;
					/*if($profile_image != '') { 
					$this->profile_image = $profile_image;
					} else { 
					$this->profile_image = 'images/thumb.png';
					}*/
					$message = 1;
				} else {
					$message = $language["ban_suspend_login_con"];
				}
			} else {
				$message = $language["password_do_not_match_err"];
				$login_attempt = $this->get_user_meta($row['user_id'], 'login_attempt') + 1;
				$this->set_user_meta($row['user_id'], 'login_attempt', $login_attempt);
				if ($login_attempt >= get_option('maximum_login_attempts')) {
					$this->set_user_meta($row['user_id'], 'login_lock', time());
				}
			}
		} else {
			$message = $language["could_not_find_email"];
		}
		return $message;
	} //login func ends here.

	function delete_user($user_id)
	{
		global $db;
		global $language;
		$user_type = $this->get_user_info($_SESSION['user_id'], 'user_type');
		if ($user_type == 'admin') {
			$query = 'UPDATE users SET status="deactivate" WHERE user_id="' . $user_id . '"';
			$result = $db->query($query) or die($db->error);
			$message = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' . $language["user_delete_succ"] . '</div>';
		} else {
			$message = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . $language["cannot_delete_user_err"] . '</div>';
		}
		return $message;
	} //delete level ends here.

	function activate_user($user_id)
	{
		global $db;
		global $language;
		$user_type = $this->get_user_info($_SESSION['user_id'], 'user_type');
		if ($user_type == 'admin') {
			$query = 'UPDATE users SET status="activate" WHERE user_id="' . $user_id . '"';
			$result = $db->query($query) or die($db->error);
			$message = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' . $language["user_activate_succ"] . '</div>';
		} else {
			$message = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . $language["cannot_activate_user_err"] . '</div>';
		}
		return $message;
	} //delete level ends here.

	function list_users($user_type, $status)
	{
		global $db;
		global $language;
		if ($user_type == 'admin') {
			if ($status != '') {
				$query = 'SELECT * from users where status ="' . $status . '" ORDER by user_id ASC';
			} else {
				$query = 'SELECT * from users ORDER by user_id ASC';
			}
			$result = $db->query($query) or die($db->error);
			$content = '';
			$count = 0;
			while ($row = $result->fetch_array()) {
				extract($row);
				$count++;
				if ($count % 2 == 0) {
					$class = 'even';
				} else {
					$class = 'odd';
				}
				$content .= '<tr class="' . $class . '">';
				$content .= '<td>';
				$content .= $count;
				$content .= '</td><td>';
				$content .= $first_name . ' ' . $last_name;
				$content .= '</td><td>';
				$content .= $phone;
				$content .= '</td><td>';
				$content .= $email;
				$content .= '</td><td>';
				$content .= $username;
				$content .= '</td><td>';
				$content .= ucfirst($user_type);
				$content .= '</td><td>';
				$content .= ucfirst($status);
				$content .= '</td><td>';
				if ($this->get_user_meta($user_id, 'last_login_time') == '') {
					$content .= 'Never';
				} else {
					$content .= time_elapsed_string($this->get_user_meta($user_id, 'last_login_time'));
				}
				$content .= '</td><td>';

				$content .= '<form method="post" name="edit" action="manager_user.php?t=ed&u=' . $user_id . '">';
				$content .= '<input type="hidden" name="edit_user" value="' . $user_id . '">';
				$content .= '<button type="submit" style="margin-right:5px;" class="btn btn-info" value="' . $language["edit"] . '"><i class="fa fa-edit" aria-hidden="true"></i></button>';
				$content .= '</form>';
				$content .= '</td><td>';
				if ($status == 'activate') {
					$content .= '<form method="post" name="delete" onsubmit="return confirm_activate_user();" action="">';
					$content .= '<input type="hidden" name="delete_user" value="' . $user_id . '">';
					$content .= '<button type="submit" class="btn btn-danger" value="' . $language["delete"] . '"><i class="fa fa-times-circle" aria-hidden="true" title="Deactivate User"></i></button>';
					$content .= '</form>';
				} else {
					$content .= '<form method="post" name="delete" onsubmit="return confirm_activate_user();" action="">';
					$content .= '<input type="hidden" name="activate_user" value="' . $user_id . '">';
					$content .= '<button type="submit" class="btn btn-success" value="' . $language["delete"] . '"><i class="fa fa-check-circle" aria-hidden="true" title="Activate User"></i></button>';
					$content .= '</form>';
				}
				$content .= '</td>';
				$content .= '</tr>';
				unset($class);
			} //loop ends here.
		} else {
			$content = $language["cannot_i_user"];
		}
		echo $content;
	} //list_levels ends here.

	function getUsersForAssignOrders()
	{
		global $db;

		$query = 'SELECT * from users ORDER by user_id ASC';

		$result = $db->query($query) or die($db->error);
		$content = '<select id="assigned_user" name="users" class="form-control mt-3"> ';

		while ($row = $result->fetch_array()) {
			extract($row);


			$content .= '<option value="' . $user_id . '">' . $first_name . ' ' . $last_name;
			$content .= '</option>"';
		}
		$content .= '</select>';
		echo $content;
	}

	function get_total_users($condition)
	{
		global $db;
		global $language;
		if ($_SESSION['user_type'] == 'admin') {
			if ($condition == 'all') {
				$query = "SELECT * from users";
			} else {
				$query = "SELECT * from users WHERE status='" . $condition . "'";
			}
			$result = $db->query($query) or die($db->error);
			$num_rows = $result->num_rows;
			echo $num_rows;
		} else {
			echo $language["cannot_view_this_list"];
		}
	} //prints total registered users.

	function edit_profile($user_id, $first_name, $last_name, $gender, $date_of_birth, $address1, $address2, $city, $state, $country, $zip_code, $mobile, $phone, $username, $email, $password, $profile_image, $description)
	{
		global $db;
		global $language;

		$current_email = $this->get_user_info($_SESSION['user_id'], 'email');
		$current_username = $this->get_user_info($_SESSION['user_id'], 'username');

		if ($email != $current_email) {
			$query = "SELECT * from users WHERE email='" . $email . "'";
			$result = $db->query($query);

			$num_user = $result->num_rows;

			if ($num_user > 0) {
				return $language["email_exit_user_err"] . ' <strong>' . $email . '</strong> ' . $language["already_REgistered"];
				exit();
			}
		}

		if ($current_username != $username) {
			//username validation
			$query = "SELECT * from users WHERE username='" . $username . "'";
			$result = $db->query($query);

			$num_user = $result->num_rows;

			if ($num_user > 0) {
				return $language["username_couldniot_add"] . ' <strong>' . $username . '</strong> ' . $language["already_REgistered"];
				exit();
			}
		}
		if ($password == '') {
			$query = 'UPDATE users SET
   	    			first_name = "' . $first_name . '",
					last_name = "' . $last_name . '",
					gender = "' . $gender . '",
					date_of_birth = "' . $date_of_birth . '",
					address1 = "' . $address1 . '",
					address2 = "' . $address2 . '",
					city = "' . $city . '",
					state = "' . $state . '",
					country = "' . $country . '",
					zip_code = "' . $zip_code . '",
					mobile = "' . $mobile . '",
					phone = "' . $phone . '",
					username = "' . $username . '",
					email = "' . $email . '",
					profile_image = "' . $profile_image . '",
					description = "' . $description . '"
			WHERE user_id="' . $user_id . '"';
		} else {
			$query = 'UPDATE users SET
   	    			first_name = "' . $first_name . '",
					last_name = "' . $last_name . '",
					gender = "' . $gender . '",
					date_of_birth = "' . $date_of_birth . '",
					address1 = "' . $address1 . '",
					address2 = "' . $address2 . '",
					city = "' . $city . '",
					state = "' . $state . '",
					country = "' . $country . '",
					zip_code = "' . $zip_code . '",
					mobile = "' . $mobile . '",
					phone = "' . $phone . '",
					username = "' . $username . '",
					email = "' . $email . '",
					password = "' . md5($password) . '",
					profile_image = "' . $profile_image . '",
					description = "' . $description . '"
			WHERE user_id="' . $user_id . '"';
		}
		$result = $db->query($query) or die($db->error);
		return $language["user_update_success"];
	} //update user ends here.

	function set_user($user_id, $user_type, $login_user)
	{
		global $db;
		global $language;
		if ($user_type == 'admin') {
			$query = 'SELECT * from users WHERE user_id="' . $user_id . '"';
		} else if ($user_id == $login_user) {
			$query = 'SELECT * from users WHERE user_id="' . $user_id . '"';
		} else {
			echo $language["trying_do_to_illegal"];
		}
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		//results ends here.
		$this->user_id = $row['user_id'];
		$this->first_name = $row['first_name'];
		$this->last_name = $row['last_name'];
		$this->gender = $row['gender'];
		$this->date_of_birth = $row['date_of_birth'];
		$this->address = $row['address'];
		$this->mobile = $row['mobile'];
		$this->phone = $row['phone'];
		$this->username = $row['username'];
		$this->email = $row['email'];
		$this->status = $row['status'];
		$this->user_type = $row['user_type'];
		$this->user_function = $row['user_function'];
	} //level set ends here.

	function update_user($user_id, $user_type_ses, $first_name, $last_name, $gender, $date_of_birth, $address, $mobile, $phone, $username, $email, $password, $status, $user_type, $user_function)
	{
		global $db;
		global $language;

		$current_email = $this->get_user_info($user_id, 'email');
		$current_username = $this->get_user_info($user_id, 'username');

		if ($email != $current_email) {
			$query = "SELECT * from users WHERE email='" . $email . "'";
			$result = $db->query($query);

			$num_user = $result->num_rows;

			if ($num_user > 0) {
				return $language["email_exit_user_err"] . ' <strong>' . $email . '</strong> ' . $language["already_REgistered"];
				exit();
			}
		}

		if ($current_username != $username) {
			//username validation
			$query = "SELECT * from users WHERE username='" . $username . "'";
			$result = $db->query($query);

			$num_user = $result->num_rows;

			if ($num_user > 0) {
				return $language["username_couldniot_add"] . ' <strong>' . $username . '</strong> ' . $language["already_REgistered"];
				exit();
			}
		}
		//updating user info.
		//if($user_type_ses == 'admin') { 
		if ($password == '') {
			$query = 'UPDATE users SET
   	    			first_name = "' . $first_name . '",
					last_name = "' . $last_name . '",
					gender = "' . $gender . '",
					date_of_birth = "' . $date_of_birth . '",
					address = "' . $address . '",
					mobile = "' . $mobile . '",
					phone = "' . $phone . '",
					username = "' . $username . '",
					email = "' . $email . '",
					status = "' . $status . '",
					user_type = "' . $user_type . '",
					user_function = "' . $user_function . '"
			WHERE user_id="' . $user_id . '"';
		} else {
			$query = 'UPDATE users SET
   	    			first_name = "' . $first_name . '",
					last_name = "' . $last_name . '",
					gender = "' . $gender . '",
					date_of_birth = "' . $date_of_birth . '",
					address = "' . $address . '",
					mobile = "' . $mobile . '",
					phone = "' . $phone . '",
					username = "' . $username . '",
					email = "' . $email . '",
					password = "' . md5($password) . '",
					status = "' . $status . '",
					user_type = "' . $user_type . '",
					user_function = "' . $user_function . '"
			WHERE user_id="' . $user_id . '"';
		}
		$result = $db->query($query) or die($db->error);
		return $language["user_update_success"];
		/*} else { 
			return $language["you_have_no_rights"];
		}*/
	} //update user ends here.

	function reset_pass_user($user_id, $confirmation_code, $new_pass)
	{
		global $db;
		global $language;
		$query = "SELECT * from users WHERE user_id='" . $user_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();

		$new_pass = md5($new_pass);
		if ($confirmation_code == $row['activation_key']) {
			$query = 'UPDATE users SET password="' . $new_pass . '",activation_key="" WHERE user_id="' . $user_id . '"';
			$row = $db->query($query) or die($db->error);
			$message = $language["password_reset_msg"];
		} else {
			$message = $language["activation_key_expire"];
		}
		return $message;
	} //reset password function ends here.	

	function match_confirm_code($confirmation_code, $user_id)
	{
		global $db;
		global $language;
		//Getting Confirmation Code from database.
		$query = "SELECT * from users WHERE user_id='" . $user_id . "'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();

		if ($row['activation_key'] == $confirmation_code) {
			if ($row['status'] == 'suspend' || $row['status'] == 'ban') {
				$message = $language["suspend_help"];
			} else {
				$status = 'activate';
				$query = 'UPDATE users SET status="' . $status . '",activation_key="" WHERE user_id="' . $user_id . '"';
				$row = $db->query($query) or die($db->error);
				$message = $language["activation_succ_ms"];
			}
		} else {
			$message = $language["cannot_activate_acc_1"];
		}
		return $message;
	} //function  close

	function forgot_user($email)
	{
		global $db;
		global $language;
		$query = "SELECT * from users WHERE email='" . $email . "' OR username='" . $email . "'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;

		if ($num_rows > 0) {
			$row = $result->fetch_array();
			$user_id = $row['user_id'];
			$email = $row['email'];
		} else {
			return $language["email_not_in_system"];
			exit();
		}
		$activation_key = substr(md5(uniqid(rand(), true)), 16, 16);
		$query = 'UPDATE users SET activation_key="' . $activation_key . '" WHERE user_id="' . $user_id . '"';
		$result = $db->query($query) or die($db->error);

		$site_url = get_option('site_url');
		$email_message = $language["reset_your_pass_1"] . "<br />";
		$email_message .= $language["click_link_reset_pass"] . "<br />";
		$email_message .= "<a href='" . $site_url . "forgot.php?confirmation_code=" . $activation_key . "&user_id=" . $user_id . "'>" . $language["email_register_4"] . "</a>";
		$email_message .= "<br><br>" . $language["email_register_5"];
		$message = $email_message;
		$mailto = $email;
		$subject = $language["reset_your_pass1"];

		send_email($mailto, $subject, $message);

		return $language["check_email_rest_pass"];
	} //forgot password function endsh ere.

	function add_user($first_name, $last_name, $gender, $date_of_birth, $address, $mobile, $phone, $username, $email, $password, $status, $user_type, $user_function)
	{
		global $db;
		global $language;
		//Check if user already exist
		$query = "SELECT * from users WHERE email='" . $email . "'";
		$result = $db->query($query) or die($db->error);

		$num_user = $result->num_rows;
		if ($num_user > 0) {
			return '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . $language["email_exit_user_err"] . ' <strong>' . $email . '</strong> ' . $language["already_REgistered"] . '</div>';
			exit();
		}
		//username validation
		$query = "SELECT * from users WHERE username='" . $username . "'";
		$result = $db->query($query);

		$num_user = $result->num_rows;

		if ($num_user > 0) {
			return '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . $language["username_couldniot_add"] . ' <strong>' . $username . '</strong> ' . $language["already_REgistered"] . '</div>';
			exit();
		}
		$registration_date = date('Y-m-d');
		$password_con = md5($password);

		//Running Query to add user.
		$query = "INSERT into users VALUES(NULL, '" . $first_name . "', '" . $last_name . "', '" . $gender . "', '" . $date_of_birth . "', '" . $address . "', '" . $mobile . "', '" . $phone . "', '" . $username . "', '" . $email . "', '" . $password_con . "', '" . $status . "', '', '" . date('Y-m-d') . "', '" . $user_type . "', '" . $user_function . "', 1)";
		$result = $db->query($query) or die($db->error);
		//Email to user
		$site_url = get_option('site_url');

		$email_message = $language["your_account_registered"] . "<br />";
		$email_message .= $language["use_following_details"];
		$email_message .= "<br><a href='" . $site_url . "'>" . $language["email_register_4"] . "</a><br>";
		$email_message .= $language["email_or_username"] . " <strong>" . $email . "</strong><br>";
		$email_message .= $language["password"] . ": <strong>" . $password . "</strong>";

		$message = $email_message;
		$mailto = $email;
		$subject = $language["registration_details"];

		send_email($mailto, $subject, $message);

		//Notify other users of same level on new registration.
		if (get_option('notify_user_group') == '1') :
			//message object.
			$subject = "New user registration.";
			$message = "<h2>New user on your user group.</h2>";
			$message .= "<p><strong>Name: </strong>" . $first_name . " " . $last_name . "</p>";
			$message .= "<p><strong>Email: </strong>" . $email . "</p>";
			$message .= "<p><strong>Username: </strong>" . $username . "</p>";

			$message_obj = new Messages;
			$message_obj->level_message($user_type, $subject, $message);
		endif;
		return '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' . $language["user_add_details_sent"] . ' <b>' . $email . '</b></div>';
	} //add user function ends here.

	function get_user_accesses()
	{
		global $db;
		global $language;
		$user = new Users;
		$warehouse = new Warehouse;
		$user_type = $_SESSION['user_type'];

		if ($user_type == 'admin') {
			$query = 'SELECT * from warehouse_access ORDER by access_id ASC';
			$result = $db->query($query) or die($db->error);
			$content = '';
			$count = 0;
			while ($row = $result->fetch_array()) {
				extract($row);
				$count++;
				if ($count % 2 == 0) {
					$class = 'even';
				} else {
					$class = 'odd';
				}
				$content .= '<tr class="' . $class . '">';
				$content .= '<td>';
				$content .= $access_id;
				$content .= '</td><td>';
				$user_name = $user->get_user_info($user_id, 'first_name') . ' ' . $user->get_user_info($user_id, 'last_name');
				$content .= $user_name;
				$content .= '</td><td>';
				$warehouse_name = $warehouse->get_warehouse_info($warehouse_id, 'name');
				$content .= $warehouse_name;
				$content .= '</td><td>';
				if ($clients == 1) {
					$cl = "Yes";
				} else {
					$cl = "No";
				}
				$content .= $cl;
				$content .= '</td><td>';
				if ($products == 1) {
					$pr = "Yes";
				} else {
					$pr = "No";
				}
				$content .= $pr;
				$content .= '</td><td>';
				if ($transfers == 1) {
					$tr = "Yes";
				} else {
					$tr = "No";
				}
				$content .= $tr;
				$content .= '</td><td>';
				if ($orders == 1) {
					$or = "Yes";
				} else {
					$or = "No";
				}
				$content .= $or;
				$content .= '</td><td>';
				if ($deliveries == 1) {
					$de = "Yes";
				} else {
					$de = "No";
				}
				$content .= $de;
				$content .= '</td><td>';
				if ($suppliers == 1) {
					$sp = "Yes";
				} else {
					$sp = "No";
				}
				$content .= $sp;
				$content .= '</td><td>';
				if ($stock == 1) {
					$sk = "Yes";
				} else {
					$sk = "No";
				}
				$content .= $sk;
				$content .= '</td><td>';
				if ($receptions == 1) {
					$rc = "Yes";
				} else {
					$rc = "No";
				}
				$content .= $rc;
				$content .= '</td><td>';
				if ($returns == 1) {
					$rn = "Yes";
				} else {
					$rn = "No";
				}
				$content .= $rn;
				$content .= '</td><td>';
				if ($reports == 1) {
					$rp = "Yes";
				} else {
					$rp = "No";
				}
				$content .= $rp;
				$content .= '</td>';
				$content .= '<td><form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_access" value="' . $row['access_id'] . '">';
				$content .= '<input type="hidden" name="user_id_access" value="' . $row['user_id'] . '">';
				$content .= '<input type="hidden" name="store_id_access" value="' . $row['warehouse_id'] . '">';
				$content .= '<button type="submit" class="btn btn-danger btn-sm" value="Delete"><i class="fa fa-trash"></i>';
				$content .= '</form></td>';
				$content .= '</tr>';
			} //loop ends here.

			echo $content;
		}
	} //list_levels ends here.


}//class ends here.