<?php

require_once('../system_load.php');

global $db;
$note = new Notes();
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);
    $query = "DELETE FROM filters  WHERE id='" . $data->id . "'";
    $result = $db->query($query) or die($db->error);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Filter Deleted Successfully',
    ]);
}
