<?php define('BASEPATH', TRUE);
require_once('functions.php');

$key = config('update_key');

if(isset($_GET['key']) && $_GET['key'] === $key) {
    $update = updateData();
    if($update) {
        $message = array('status' => 'success', 'message' => 'Data updated successfully', 'data' => $update);
    } else {
        $message = array('status' => 'error', 'message' => 'Error updating data');
    }

    echo json_encode($message);

} else {
    exit('You are not allowed to access this page.');
}