<?php
$user_id = $_SESSION['user_id'];
$function_id = $user->get_user_info($user_id, "user_function");

?>
<?php

// Check if the store ID is provided in the URL
if(isset($_GET['id'])) {
    $store_id = $_GET['id'];
    $_SESSION['store_id'] = $store_id; // Save the store ID in the session
} else {
    // Retrieve the store ID from the session
    if(isset($_SESSION['store_id'])) {
        $store_id = $_SESSION['store_id'];
    } else {
        // Handle the case when the store ID is not available
        // You can set a default value or redirect the user to a different page
    }
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .page-sidebar-menu {
        margin-top: 30px !important;
    }
</style>
<div class="page-sidebar">
    <a class="logo-box" href="warehouse.php">
        <span><?php echo get_option('site_name'); ?></span>

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
                <?php if (partial_access('admin')) { ?>
                    <li>
                        <a href="companysettings.php">
                            <i class="menu-icon icon-settings"></i><span>General Settings</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if (partial_access('admin')) { ?>
                    <li>
                        <a href="reset_orders.php" onclick="return confirm('Are you sure?')">
                            <i class="menu-icon icon-settings"></i><span>Reset Orders</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if (partial_access('admin')) { ?>
                    <li>
                        <a href="javascript:void(0)">
                            <i class="menu-icon icon-users"></i><span>Users</span><i class="accordion-icon fa fa-angle-right"></i>
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
                <?php if (partial_access('admin')) { ?>
                    <li>
                        <a href="warehouses.php?ui=<?php echo $_SESSION['warehouse_id']; ?>">
                            <i class="menu-icon fa fa-building"></i><span>Warehouses</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0)">
                            <i class="menu-icon fa fa-barcode"></i><span>Products <i class="accordion-icon fa fa-angle-right"></i></span>
                        </a>
                        <ul class="sub-menu">
                            <li><a href="products.php"><span class="text-success"><i class="fa fa-caret-right"></i> Products</span></a></li>
                            <li><a href="categories.php"><span class="text-success"><i class="fa fa-caret-right"></i> Categories</span></a></li>
                            <?php if (partial_access('admin') or $function_id == 'storem' or $function_id == 'manager') { ?>
                                <li><a href="dimensions.php"><span class="text-success"><i class="fa fa-caret-right"></i> Product Settings</span></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-credit-card"></i><span>Orders <i class="accordion-icon fa fa-angle-right"></i> </span>
                    </a>
                    <ul class="sub-menu">
                    <li><a href="store_orders_list.php?id=<?php echo $store_id; ?>"><span class="text-success"><i class="fa fa-caret-right"></i> All Orders</span></a></li>
                        <?php if (partial_access('admin') && isset($_SESSION['assigned_orders']) && $_SESSION['assigned_orders']) { ?>
                            <li><a href="assigned_orders_list.php"><span class="text-success"><i class="fa fa-caret-right"></i> Orders Assigned To Me</span></a></li>
                        <?php } ?>
                        <!--
                        <?php if (isset($_SESSION['assigned_orders']) && $_SESSION['assigned_orders']) { ?>
                            <li><a href="assigned_orders_list.php?t=user"><span class="text-success"><i class="fa fa-caret-right"></i> Orders Assigned To Me</span></a></li>
                        <?php } ?> -->
                        <li><a href="buy_postage.php"><span class="text-success"><i class="fa fa-caret-right"></i> Buy Postage</span></a></li>
                        <li><a href="finished_orders.php"><span class="text-success"><i class="fa fa-caret-right"></i> Finished Orders</span></a></li>
                        <!-- <li><a href="orders.php"><span class="text-success"><i class="fa fa-caret-right"></i> View Orders</span></a></li>
                        <li><a href="ordersbysupplier.php"><span class="text-success"><i class="fa fa-caret-right"></i> Orders Per Supplier</span></a></li> -->
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-list-ol"></i><span>Inventory <i class="accordion-icon fa fa-angle-right"></i></span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="inventproducts.php"><span class="text-success" title="Inventory by Warehouse"><i class="fa fa-caret-right"></i> Inventory</span></a></li>
                        <li><a href="lowstock.php"><span class="text-success" title="Low Stock"><i class="fa fa-caret-right"></i> Low Stock </span></a></li>
                        <li><a href="outstock.php"><span class="text-success" title="Low Stock"><i class="fa fa-caret-right"></i> Out Of Stock </span></a></li>
                        <li><a href="addstock.php"><span class="text-success"><i class="fa fa-caret-right"></i> Add Stock</span></a></li>
                        <li><a href="updatestock.php"><span class="text-success"><i class="fa fa-caret-right"></i> Update Stock</span></a></li>
                        <li><a href="receive_shipments.php"><span class="text-success"><i class="fa fa-caret-right"></i> Receive Shipments</span></a></li>
                        <li><a href="receive_inventory_request.php"><span class="text-success"><i class="fa fa-caret-right"></i> Received Request</span></a></li>

                    </ul>
                </li>


                <?php
                    /*
                    <!-- Return Section -->
                    <li>
                        <a href="javascript:void(0)">
                            <i class="menu-icon fa fa-truck"></i><span>Return <i class="accordion-icon fa fa-angle-right"></i> </span>
                        </a>
                        <ul class="sub-menu">
                            <li><a href="return_label_list.php"><span class="text-success"><i class="fa fa-caret-right"></i> Return labels</span></a></li>
                            
                            <?php // if (partial_access('admin') or $function_id == 'storem' or $function_id == 'manager') { ?>
                                <!-- Display only for admin, store manager, or manager -->
                                <!-- <li><a href="newreturn.php"><span class="text-success"><i class="fa fa-caret-right"></i> New Stock Return</span></a></li> -->
                            <?php // } ?>

                            <li><a href="returns.php"><span class="text-success"><i class="fa fa-caret-right"></i> All Returns</span></a></li>
                        </ul>
                    </li>
                    -->
                    */
                    ?>

                <!-- Reports Section
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-bar-chart"></i><span>Reports <i class="accordion-icon fa fa-angle-right"></i> </span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="warhreports.php"><span class="text-success"><i class="fa fa-caret-right"></i> Warehouse Reports</span></a></li>
                        <li><a href="productreports.php"><span class="text-success"><i class="fa fa-caret-right"></i> Product Reports</span></a></li>
                        <li><a href="orderreports.php"><span class="text-success"><i class="fa fa-caret-right"></i> Order Reports</span></a></li>
                        <li><a href="deliveryreports.php"><span class="text-success"><i class="fa fa-caret-right"></i> Delivery Reports</span></a></li>
                    </ul>
                </li>
                -->

                    <?php if (false) { ?>
                        <li>
                            <a href="javascript:void(0)">
                                <i class="menu-icon fa fa-exchange"></i><span>Transfers <i class="accordion-icon fa fa-angle-right"></i> </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="newtransfer.php"><span class="text-success"><i class="fa fa-caret-right"></i> New Transfer</span></a></li>
                                <li><a href="transfers.php"><span class="text-success"><i class="fa fa-caret-right"></i> Transfers Sent</span></a></li>
                                <li><a href="transferreceived.php"><span class="text-success"><i class="fa fa-caret-right"></i> Transfers Received</span></a></li>


                            </ul>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <i class="menu-icon fa fa-address-book"></i><span>Suppliers <i class="accordion-icon fa fa-angle-right"></i> </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="suppliers.php"><span class="text-success"><i class="fa fa-caret-right"></i> All Suppliers</span></a></li>
                                <?php if (partial_access('admin') or $function_id == 'storem' or $function_id == 'manager') { ?>
                                    <li><a href="newsupplier.php"><span class="text-success"><i class="fa fa-caret-right"></i> New Supplier</span></a></li>
                                <?php } ?>

                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0)">
                                <i class="menu-icon fa fa-address-card"></i><span>Customers <i class="accordion-icon fa fa-angle-right"></i> </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="customers.php"><span class="text-success"><i class="fa fa-caret-right"></i> All Customers</span></a></li>
                                <?php if (partial_access('admin') or $function_id == 'storem' or $function_id == 'manager') { ?>
                                    <li><a href="newcustomer.php"><span class="text-success"><i class="fa fa-caret-right"></i> New Customer</span></a></li>
                                <?php } ?>

                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0)">
                                <i class="menu-icon fa fa-credit-card"></i><span>Purshasing Orders <i class="accordion-icon fa fa-angle-right"></i> </span>
                            </a>
                            <ul class="sub-menu">
                                <?php if (partial_access('admin') or $function_id == 'storem' or $function_id == 'manager') { ?>
                                    <li><a href="neworder.php"><span class="text-success"><i class="fa fa-caret-right"></i> New Order</span></a></li>
                                <?php } ?>
                                <li><a href="orders.php"><span class="text-success"><i class="fa fa-caret-right"></i> View Orders</span></a></li>
                                <li><a href="ordersbysupplier.php"><span class="text-success"><i class="fa fa-caret-right"></i> Orders Per Supplier</span></a></li>

                            </ul>
                        </li>
                    <?php } ?>

                    <!-- from MAddy -->


                    <?php if (partial_access('admin')) { ?>
                        <li>
                            <a href="javascript:void(0)">
                                <i class="menu-icon fa fa-bar-chart"></i><span>Store Owner <i class="accordion-icon fa fa-angle-right"></i> </span>

                            </a>
                            <ul class="sub-menu">
                                <li><a href="stores_list.php"><span class="text-success"><i class="fa fa-caret-right"></i> View Stores</span></a></li>
                                <li><a href="store_owner_price_list.php"><span class="text-success"><i class="fa fa-caret-right"></i>Adjust Price</span></a></li>
                                <li><a href="store_owner_owes.php"><span class="text-success"><i class="fa fa-caret-right"></i>Store Owner Reports</span></a></li>
                                <li><a href="create_filters.php"><span class="text-success"><i class="fa fa-caret-right"></i>Create Filters</span></a></li>

                                <!-- <li><a href="productreports.php"><span class="text-success"><i class="fa fa-caret-right"></i> Product Reports</span></a></li>
                                <li><a href="orderreports.php"><span class="text-success"><i class="fa fa-caret-right"></i> Order Reports</span></a></li>
                                <li><a href="deliveryreports.php"><span class="text-success"><i class="fa fa-caret-right"></i> Delivery Reports</span></a></li> -->
                            </ul>
                        </li>
                    <?php } ?>

                    <?php /*
                        <!-- Payments Section -->
                        if (partial_access('admin')) {
                            ?>
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-dollar"></i><span>Payments<i class="accordion-icon fa fa-angle-right"></i> </span>
                                </a>
                                <ul class="sub-menu">
                                    <li><a href="all_payment_list.php"><span class="text-success"><i class="fa fa-caret-right"></i>Payment List</span></a></li>
                                </ul>
                            </li>
                            <?php
                        }
                        */
                    ?>

            </ul>
        </div>
    </div>
</div> <!-- /Page Sidebar -->