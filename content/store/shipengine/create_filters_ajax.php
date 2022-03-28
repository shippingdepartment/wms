<?php

require_once('../system_load.php');

global $db;
$note = new Notes();
$important = new ImportantFunctions();
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);
    $response =   $important->storeFilters($data->store_id, $data->filter_name, $data->filter_sign, $data->filter_value, $data->store_name);
    if ($response) {
    }
}
