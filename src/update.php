<?php
require_once('includes/config.php');

$key = config('update_key');

if(isset($_GET['key']) && $_GET['key'] === $key) {
    $update = updateData();
    $auth_message = checktoken();
    if($update) {
        $message = array('status' => 'success', 'message' => 'Data updated successfully', 'data' => $update, 'token_status' => $auth_message);
    } else {
        $message = array('status' => 'error', 'message' => 'Error updating data', 'token_status' => $auth_message);
    }

    echo json_encode($message);

} else {
    exit('You are not allowed to access this page.');
}