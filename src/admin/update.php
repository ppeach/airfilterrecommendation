<?php
require_once(__DIR__.'/../includes/init.php');
// Start sessions
session_start();

// Redirect to login page if there's no sessions
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update = updateData();
    $auth_message = checktoken();
    if($update) {
        $message = array('status' => 'success', 'message' => 'Data updated successfully', 'data' => $update, 'token_status' => $auth_message);
    } else {
        $message = array('status' => 'error', 'message' => 'Error updating data', 'data' => $auth_message);
    }

    // echo json_encode($message);
    return print json_encode($message);

} elseif($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['sheetsid'])) {
    $sheetid = $_GET['sheetsid'];
    $id = $_GET['id'];
    if($id === 'sheet_id'){
        $update = manage_config($id,$sheetid);
        $message = array('status' => 'success', 'message' => 'Data updated successfully', 'data' => $sheetid);
    } else {
        $message = array('status' => 'error', 'message' => 'Invalid request', 'data' => 'There is something wrong with the request');
    }
    return print json_encode($message);

} else {
    $message = array('status' => 'error', 'message' => 'Invalid request', 'data' => 'There is something wrong with the request');
    return print json_encode($message);
}