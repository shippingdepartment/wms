<?php
	//add User info box if user is signed in.
	if(partial_access('all')): 
		require_once('collapseuserinfo.php');
	endif;	
	$note = new Notes;
?>
<div class="page-header ">
	<!--<div class="alert alert-default" role="alert">-->
                    <div class="search-form ">
                        <form action="#" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control search-input" placeholder="Type something...">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" id="close-search" type="button"><i class="icon-close"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <nav class="navbar navbar-default">
                        <div class="container-fluid">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <div class="navbar-header">
                                <div class="logo-sm">
                                    <a href="javascript:void(0)" id="sidebar-toggle-button"><i class="fa fa-bars"></i></a>
                                    <a class="logo-box" href="#"><span>MyWarehouse</span></a>
                                </div>
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                    <i class="fa fa-angle-down"></i>
                                </button>
                            </div>
                        
                            <!-- Collect the nav links, forms, and other content for toggling -->
                        
                            <div class="collapse navbar-collapse " id="bs-example-navbar-collapse-1">
                                <ul class="nav navbar-nav">
                                    <li><a href="javascript:void(0)" id="collapsed-sidebar-toggle-button"><i class="fa fa-bars"></i> Collapse Sidebar</a></li>
                                    <li><a href="javascript:void(0)" id="toggle-fullscreen"><i class="fa fa-expand"></i>  Full Screen</a></li>
                                    <li><a href="javascript:void(0)" id="search-button"><i class="fa fa-search"></i>  Search&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; | </a></li>
									<li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo '<i class="fa fa-user"></i> Welcome '.$user->get_user_info($_SESSION['user_id'], 'first_name'). ' ' .$user->get_user_info($_SESSION['user_id'], 'last_name'); ?></a>
                                        <ul class="dropdown-menu">
                                            
                                            <li><a href="includes/logout.php">Log Out</a></li>
                                        </ul>
                                    </li>
									<?php if (isset($_SESSION['warehouse_id'])) { ?>
									<li class="dropdown">
                                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bell"></i> Notifications <sup><span class="badge"><?php echo $note->notes_count(); ?></span></sup> </a>
                                        <ul class="dropdown-menu dropdown-lg dropdown-content">
                                            <li class="drop-title">Notifications <span class="badge"><?php echo $note->notes_count(); ?></span><a href="#" class="drop-title-link"><i class="fa fa-angle-right"></i></a></li>
                                            <li class="slimscroll dropdown-notifications">
                                                <ul class="list-unstyled dropdown-oc">
													<?php $notes_obj->notes_widget(); ?>
                                                    
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
									<?php } ?>
                                </ul>
                                <ul class="nav navbar-nav navbar-right">
                                   
                                    
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user->get_user_info($_SESSION['user_id'], 'first_name'). ' ' .$user->get_user_info($_SESSION['user_id'], 'last_name'); ?></a>
                                        <ul class="dropdown-menu">
                                            
                                            <li><a href="includes/logout.php">Log Out</a></li>
											
                                        </ul>
                                    </li>
                                </ul>
                            </div><!-- /.navbar-collapse -->
                        </div><!-- /.container-fluid -->
                    </nav>
					
 </div>