  <!--Top Onclick area Starts here-->
	<div class="settings-pane collapse" id="collapseExample">
		<a href="#collapseExample" data-toggle="collapse" data-animate="true">
			&times;
		</a>
		<div class="settings-pane-inner">
			<div class="row">
				<div class="col-md-4">
					<div class="user-info">
						<div class="user-image">
							<a href="edit_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>">
								<img src="<?php echo $profile_img; ?>" class="img-responsive img-circle" />
							</a>
						</div>
						<div class="user-details">
							<h3>
								<a href="extra-profile.html"><?php echo $new_user->get_user_info($_SESSION['user_id'], 'first_name'); ?> <?php echo $new_user->get_user_info($_SESSION['user_id'], 'last_name'); ?></a>
								<!-- Available statuses: is-online, is-idle, is-busy and is-offline -->
								<span class="user-status is-online"></span>
							</h3>
							<p class="user-title"><?php echo $new_user->get_user_info($_SESSION['user_id'], 'email'); ?></p>
							<div class="user-links">
								<a href="edit_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-primary">Edit Profile</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4 link-blocks-env">
					<div class="links-block left-sep">
						<h4>
							<span>Basic Information</span>
						</h4>
						
						<ul class="list-unstyled">
							<li>
								<strong>Gender: </strong> <?php echo $new_user->get_user_info($_SESSION['user_id'], 'gender'); ?>
							</li>
                            <li>
								<strong>Date of birth: </strong> <?php echo $new_user->get_user_info($_SESSION['user_id'], 'date_of_birth'); ?>
							</li>
                            <li>
								<strong>Your IP: </strong> <?php $ipAddress = get_client_ip(); echo $ipAddress; ?>
							</li>
						</ul>
					</div>
					
					
				</div>
				<!--Third Column Starts Here-->
                <div class="col-md-4 link-blocks-env">
					
					
					<div class="links-block left-sep">
						<h4>
							<a href="#">
								<span>About me!</span>
							</a>
						</h4>
						<p><?php echo $new_user->get_user_info($_SESSION['user_id'], 'description'); ?></p>
					</div>
				</div>
			</div><!--row-->
		</div>
	</div>
