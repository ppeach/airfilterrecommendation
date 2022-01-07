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
$prefilter  = 'Prefilter';

// Set data for Countries dropdown
$countries  = countries(config('sheet_id'));

// Set data for Max Acceptable Noise
$maxANoise  = array('30', '40', '45', '50', '55', '60');