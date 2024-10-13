<?php
require_once(__DIR__.'/../includes/init.php');
// Start sessions
session_start();

if ((!isset($_SESSION['user_email']) || isset($_SESSION['user_email'])) && (isset($_GET['key']) && $_GET['key'] === UPDATE_KEY)) {
    error_reporting(0);
    // Bypass session by using update key for database update
    $update = updateData();
    $auth_message = checktoken();
    if($update) {
        $message = array('status' => 'success', 'message' => 'Data updated successfully', 'data' => $update, 'token_status' => $auth_message);
    } else {
        $message = array('status' => 'error', 'message' => 'Error updating data', 'data' => $auth_message);
    }

    return print '<pre>' .json_encode($message, JSON_PRETTY_PRINT). '<pre>';

} elseif(!isset($_SESSION['user_email'])){
    // Redirect to login page if there's no sessions
    header("Location: login.php");
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update database from admin dashboard
    $update = updateData();
    $auth_message = checktoken();
    if($update) {
        $message = array('status' => 'success', 'message' => 'Data updated successfully', 'data' => $update, 'token_status' => $auth_message);
    } else {
        $message = array('status' => 'error', 'message' => 'Error updating data', 'data' => $auth_message);
    }

    return print json_encode($message);

} elseif($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['sheetsid'])) {
    // Update google sheets ID from admin dashboard
    $sheetid = $_GET['sheetsid'];
    $id = $_GET['id'];
    if($id === 'sheet_id'){
        $update = manageJson($id, $sheetid, 'config/config.json');
        $message = array('status' => 'success', 'message' => 'Data updated successfully', 'data' => $sheetid);
    } else {
        $message = array('status' => 'error', 'message' => 'Invalid request', 'data' => 'There is something wrong with the request');
    }
    return print json_encode($message);
} else {
    $message = array('status' => 'error', 'message' => 'Invalid request', 'data' => 'There is something wrong with the request');
    return print json_encode($message);
}