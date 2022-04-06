<?php

require_once('../system_load.php');

global $db;
$note = new Notes();
$important = new ImportantFunctions();
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);
    $query = "UPDATE  shipping_labels SET is_return=1 WHERE label_id='" . $data->label_id . "'";
    $result = $db->query($query) or die($db->error);
    $important->storeReturnLabelList($data->label_id, $data->label_link, $data->status, $data->tracking_number);
    $note->add_note('Label returned', 'label# ' . $data->label_id . ' has been returned');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Returned Label Successfully',
    ]);
}
