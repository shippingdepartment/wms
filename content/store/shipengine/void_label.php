<?php

require_once('../system_load.php');

global $db;
$note = new Notes();
if (isset($_POST['data'])) {
    $data = json_decode($_POST["data"]);
    $query = "UPDATE  shipping_labels SET is_void=1 WHERE label_id='" . $data->label_id . "'";
    $result = $db->query($query) or die($db->error);
    $note->add_note('Label Void', 'label# ' . $data->label_id . ' has been voided');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Label voided Successfully',
    ]);
}
