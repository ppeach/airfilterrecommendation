<?php

# collection of key terms so we only have to change in one place
# Variable names are in uppercase following Python convention for this - no idea
# if it applies to PHP, but it should


# Machine-readable stuff first - all lowercase please

# Basic yes and no, for boolean questions but we're not using boolean types here
$VALUE_YES = "yes";
$VALUE_NO = "no";

# Litres per second selectors
$VALUE_LPS_10 = "lps_10";
$VALUE_LPS_20 = "lps_20";
$VALUE_LPS_50 = "lps_50";

# Air changes per hour selectors
$VALUE_ACH_2 = "ach_2";
$VALUE_ACH_4 = "ach_4";
$VALUE_ACH_6 = "ach_6";
$VALUE_ACH_9 = "ach_9";
$VALUE_ACH_12 = "ach_12";

# Array of ACH values for the function to use to confirm we're in ACH mode
$VALUES_ACH = array(
    $VALUE_ACH_2,
    $VALUE_ACH_4,
    $VALUE_ACH_6,
    $VALUE_ACH_9,
    $VALUE_ACH_12,
);

# Measurements
$VALUE_CUBIC_METRE = "m3";
$VALUE_CUBIC_FOOT = "cubic";



# Human-readable now
$DISPLAY_SCHEDULE_YES = "Yes";
$DISPLAY_SCHEDULE_NO = "Not necessary";

$DISPLAY_DIY_YES = "Yes";
$DISPLAY_DIY_NO = "No";

$DISPLAY_WIFI_YES = "Yes";
$DISPLAY_WIFI_NO = "Not necessary";

$DISPLAY_PREFILTER_YES = "Yes";
$DISPLAY_PREFILTER_NO = "Not necessary";

# Litres per second selectors
$DISPLAY_LPS_10 = "10 L/person/second (Minimum, WHO recommendation)";
$DISPLAY_LPS_20 = "20 L/person/second (Ideal, non-vigorous activity)";
$DISPLAY_LPS_50 = "50 L/person/second (Ideal, vigorous activity eg Exercise, Singing)";

# Air changes per hour selectors
$DISPLAY_ACH_2 = "2 Air Changes per Hour (ACH)";
$DISPLAY_ACH_4 = "4 Air Changes per Hour (ACH)";
$DISPLAY_ACH_6 = "6 Air Changes per Hour (ACH)";
$DISPLAY_ACH_9 = "9 Air Changes per Hour (ACH)";
$DISPLAY_ACH_12 = "12 Air Changes per Hour (ACH)";


# Measurements
$DISPLAY_CUBIC_METRE = "m3";
$DISPLAY_CUBIC_FOOT = "cubic feet";

?>
