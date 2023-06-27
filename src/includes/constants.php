<?php
# collection of key terms so we only have to change in one place
# Variable names are in uppercase following Python convention for this - no idea
# if it applies to PHP, but it should

# Machine-readable stuff first - all lowercase please

# Basic yes and no, for boolean questions but we're not using boolean types here
$VALUE_YES = 'yes';
$VALUE_NO = 'no';

# Array ACH options and values
$ACH_OPTIONS = array(
    '2' => '2 Air Changes per Hour (ACH)',
    '4' => '4 Air Changes per Hour (ACH)',
    '6' => '6 Air Changes per Hour (ACH)',
    '9' => '9 Air Changes per Hour (ACH)',
    '12' => '12 Air Changes per Hour (ACH)'
);

# Array L/p/s options and values
$LPS_OPTIONS = array(
    '10' => '10 L/person/second (Minimum, WHO recommendation)',
    '20' => '20 L/person/second (Ideal, non-vigorous activity)',
    '50' => '50 L/person/second (Ideal, vigorous activity eg Exercise, Singing)'
);

# Measurements
$MEASUREMENT_OPTIONS = array(
    'm3' => 'm3',
    'cubic' => 'cubic feet'
);

# Human-readable now
$DISPLAY_SCHEDULE_YES = 'Yes';
$DISPLAY_SCHEDULE_NO = 'Not necessary';

$DISPLAY_DIY_YES = 'Yes';
$DISPLAY_DIY_NO = 'No';

$DISPLAY_WIFI_YES = 'Yes';
$DISPLAY_WIFI_NO = 'Not necessary';

$DISPLAY_PREFILTER_YES = 'Yes';
$DISPLAY_PREFILTER_NO = 'Not necessary';

# Filter replacement schedule
$FRS_OPTIONS = array(
    '6' => 'Every 6 months',
    '12' => 'Every 12 months',
    '24' => 'Every 24 months'
);

# Assumed filter lifetime
$AFL_OPTIONS = array(
    '1' => '1 year',
    '2' => '2 years',
    '3' => '3 years',
    '4' => '4 years',
    '5' => '5 years',
    '6' => '6 years',
    '7' => '7 years',
    '8' => '8 years',
    '9' => '9 years',
    '10' => '10 years'
);

# SVG Icons
$SVG_INFO = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="blue" class="bi bi-info-circle-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path></svg>';

?>
