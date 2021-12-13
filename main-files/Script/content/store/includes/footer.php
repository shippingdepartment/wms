<div class="row">
	<div class="panel-heading" role="tab" id="headingTwo">
		
		<h6 class="panel-title" style="align:right">
			<small>
				<b>&copy; Copyright <?php echo date('Y'); ?> - All Rights Reserved - <span class="text-info"><?php echo 'MYWAREHOUSE - V '.script_version() ?></span></b>
			</small>
		</h6>
		
		
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	function confirm_delete() { 
		var del = confirm('Are you sure you want to perform this action?');
		if(del == true) { 
			return true;
		} else { 
			return false;
		}
	}//delete_confirmation ends here.
	
	//confirm delete user_error
	function confirm_deactivate_user() { 
		var del = confirm('Are you sure you want to DEACTIVATE this user?');
		if(del == true) { 
			return true;
		} else { 
			return false;
		}
	}
	
	//confirm activate user
	function confirm_activate_user() { 
		var del = confirm('Are you sure you want to ACTIVATE this user?');
		if(del == true) { 
			return true;
		} else { 
			return false;
		}
	}
</script>

