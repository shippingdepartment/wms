<?php 
	/*include('system_load.php');
	//This loads system.

	//user Authentication.
	authenticate_user('subscriber');*/
?>
<div class="page-sidebar">
                <a class="logo-box" href="index.html">
                    <span><?php echo get_option('site_name'); ?></span>
                    <!--<i class="icon-radio_button_unchecked" id="fixed-sidebar-toggle-button"></i>-->
                    <i class="icon-close" id="sidebar-toggle-button-close"></i>
                </a>
                <div class="page-sidebar-inner">
                    <div class="page-sidebar-menu">
                        <ul class="accordion-menu">
                            <li class="active-page">
                                <a href="warehouse.php">
                                    <i class="menu-icon icon-home4"></i><span>Dashboard</span>
                                </a>
                            </li>
							<?php if(partial_access('admin')) { ?>
							<li>
                                <a href="companysettings.php">
                                    <i class="menu-icon icon-settings"></i><span>General Settings</span>
                                </a>
                            </li>
							<?php } ?>
                            <li>
                                <a href="warehouses.php">
                                    <i class="menu-icon icon-inbox"></i><span>Warehouses</span>
                                </a>
                            </li>
							<?php if(partial_access('admin')) { ?>
							<li>
                                <a href="javascript:void(0)">
                                    <i class="menu-icon icon-users"></i><span>Users</span><i class="accordion-icon fa fa-angle-left"></i>
                                </a>
								<ul class="sub-menu">
									<li><a href="users.php?t=all">All Users</a></li>
									<li><a href="users.php?t=bn">Banned Users</a></li>
									<li><a href="users.php?t=ds">Desactive Users</a></li>
									<li><a href="users.php?t=sp">Suspended Users</a></li>
								</ul>
                            </li>
							<li>
                                <a href="warhaccess.php">
                                    <i class="menu-icon icon-user"></i><span>User Access</span>
                                </a>
                            </li>
							<?php } ?>
                            
                        </ul>
                    </div>
                </div>
            </div><!-- /Page Sidebar -->
			
			