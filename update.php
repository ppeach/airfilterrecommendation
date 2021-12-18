<?php define('BASEPATH', TRUE);
require_once('functions.php');

$key = '[your complex key'; // Replace with your generated key
$sheet_id = '[your sheet ID]';
$range = 'A1:K1000';

if(isset($_GET['key']) && $_GET['key'] === $key) {
    $update = updateData($sheet_id, $range);
    if($update) {
        $message = array('status' => 'success', 'message' => 'Data updated successfully');
    } else {
        $message = array('status' => 'error', 'message' => 'Error updating data');
    }

    echo json_encode($message);

} else {
    exit('You are not allowed to access this page.');
}