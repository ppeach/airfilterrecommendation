<?php define('BASEPATH', TRUE);
require_once('functions.php');

// Disable error_reporting - Important for production
error_reporting(0);

// Initialize sheets title
$model      = 'Model';
$cost       = 'Cost';
$cadr_m3    = 'CADR (m3/hr)';
$cadr_litre = 'CADR (Litre/sec)';
$cadr_cubic = 'CADR (Cubic feet/min)';
$noise_dBA  = 'Noise (dBA)';
$filterCost = 'Filter cost';
$title_wifi = 'Wifi';
$image      = 'Image';
$details    = 'Details';
$buy        = 'Buy';
$buyfilter  = 'Buy filter';
$prefilter  = 'Prefilter';
$notes      = 'Notes';
$diy        = 'DIY';

// Set data for Countries dropdown
$countries  = countries(config('sheet_id'));

// Set data for Max Acceptable Noise
$maxANoise  = array('30', '35', '40', '45', '50', '55', '60');

// Define constants for OAuth2 authentication and Google Sheets API
define('OAUTHURL', 'https://www.googleapis.com/oauth2/v4/token');
define('SHEETSAPI', 'https://sheets.googleapis.com/v4/spreadsheets/');