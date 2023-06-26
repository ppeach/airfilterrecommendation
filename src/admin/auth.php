<?php
require_once(__DIR__.'/../includes/init.php');
// Start sessions
session_start();

// Validate JWT, get user detail and set sessions
if(isset($_POST['credential'])) {
  $data = verify_jwtToken($_POST['credential']);
  $data = json_decode($data, true);

  // if($data['aud'] == CLIENT_ID && $data['email_verified'] == true && $data['email'] == ADMIN_EMAIL) {
  if($data['aud'] == CLIENT_ID && $data['email_verified'] == true) {
    $_SESSION['user_email'] = $data['email'];
    $_SESSION['user_name'] = $data['name'];
    $_SESSION['user_picture'] = $data['picture'];
    $_SESSION['client_id'] = $data['aud'];
    $_SESSION['signin_message'] = 'Signed in successfully';
    header("Location: ".REDIRECT_URL);
  } else {
    $_SESSION['error'] = 'Invalid credential';
    header("Location: login.php");
  }
}

// Get refresh token
if(isset($_GET['code'])) {
  $data = get_refreshToken($_GET['code']);
  $_SESSION['refresh_token'] = $data['refresh_token'];
  $_SESSION['scope'] = $data['scope'];
  $_SESSION['auth_message'] = 'Refresh token generated successfully';

  if(array_key_exists('error', $data)) {
    $_SESSION['refresh_token'] = $data['error'];
    $_SESSION['scope'] = $data['error_description'];
  }

  header("Location: ".REDIRECT_URL);
}

// Signout and destroy sessions
if(isset($_GET['signout']) && $_GET['signout'] == true) {
  session_destroy();
  session_start();
  $_SESSION['signout_message'] = 'Signed out successfully';
  return print json_encode(array('success' => true));
}

// Redirect to login page or admin dashboard if accessing directly
return header("Location: ".REDIRECT_URL);

?>