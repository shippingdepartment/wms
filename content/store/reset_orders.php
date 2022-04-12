<?php

include('system_load.php');
//This loads system.

//user Authentication.
authenticate_user('subscriber');
//creating company object.

$important = new ImportantFunctions();
$message = '';

if (partial_access('admin')) {
    $important->resetOrdersAll();
    HEADER('LOCATION: stores_list.php?msg=order_reset');
} else {
    HEADER('LOCATION: warehouses.php?msg=warforb');
}
