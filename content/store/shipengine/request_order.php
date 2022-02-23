<?php

require_once('../system_load.php');

global $db;
$note = new Notes();
    $query = "UPDATE  users SET is_request=IF(is_request=1, 0, 1) WHERE user_id='" . $_SESSION['user_id'] . "'";
    $result = $db->query($query) or die($db->error);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Request updated successfully',
    ]);

