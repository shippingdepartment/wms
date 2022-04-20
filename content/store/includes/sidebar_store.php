<?php
$user_id = $_SESSION['user_id'];
$function_id = $user->get_user_info($user_id, "user_function");
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="page-sidebar">
    <a class="logo-box" href="warehouse.php">
        <span><?php echo get_option('site_name'); ?></span>

        <i class="icon-close" id="sidebar-toggle-button-close"></i>
    </a>
    <div class="page-sidebar-inner">
        <div class="page-sidebar-menu">
            <ul class="accordion-menu">
                <li class="active-page">
                    <a href="store_owner_dashboard.php">
                        <i class="menu-icon icon-home4"></i><span>Dashboard</span>
                    </a>
                </li>


                <!-- from MAddy -->
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-credit-card"></i><span>Orders <i class="accordion-icon fa fa-angle-right"></i> </span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="store_owner_orders_list.php"><span class="text-success"><i class="fa fa-caret-right"></i> All Orders</span></a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-truck"></i><span>Tracking <i class="accordion-icon fa fa-angle-right"></i> </span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="track_order.php"><span class="text-success"><i class="fa fa-caret-right"></i> Track Order</span></a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-dollar"></i><span>Payments <i class="accordion-icon fa fa-angle-right"></i> </span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="store_payment_list.php"><span class="text-success"><i class="fa fa-caret-right"></i> Transaction History</span></a></li>
                    </ul>
                </li>

                <li>
                    <a href="voidLabels.php">
                        <i class="menu-icon fa fa-tag"></i><span>Void Label <i class="accordion-icon fa fa-angle-right"></i> </span>
                    </a>
                    <!-- <ul class="sub-menu">
                        <li><a href="store_payment_list.php"><span class="text-success"><i class="fa fa-caret-right"></i> Transaction History</span></a></li>
                    </ul> -->
                </li>

                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-list-ol"></i><span>Inventory <i class="accordion-icon fa fa-angle-right"></i></span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="inventproducts.php"><span class="text-success" title="Inventory by Warehouse"><i class="fa fa-caret-right"></i> Inventory</span></a></li>
                        <li><a href="lowstock.php"><span class="text-success" title="Low Stock"><i class="fa fa-caret-right"></i> Low Stock </span></a></li>
                        <li><a href="outstock.php"><span class="text-success" title="Low Stock"><i class="fa fa-caret-right"></i> Out Of Stock </span></a></li>
                        <li><a href="send_inventory.php"><span class="text-success" title="Send Inventory"><i class="fa fa-caret-right"></i> Send Inventory</span></a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-list-ol"></i><span>Deliveries<i class="accordion-icon fa fa-angle-right"></i></span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="send_shipping.php"><span class="text-success"><i class="fa fa-caret-right"></i> Shipping</span></a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-list-ol"></i><span>Return Labels<i class="accordion-icon fa fa-angle-right"></i></span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="return_label.php"><span class="text-success"><i class="fa fa-caret-right"></i> From Shipengine</span></a></li>
                        <li><a href="return_to_shipengine.php"><span class="text-success"><i class="fa fa-caret-right"></i> To Shipengine</span></a></li>
                    </ul>
                </li>



            </ul>
        </div>
    </div>
</div><!-- /Page Sidebar -->