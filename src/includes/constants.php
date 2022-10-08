<?php

# collection of key terms so we only have to change in one place
# Variable names are in uppercase following Python convention for this - no idea
# if it applies to PHP, but it should


# Machine-readable stuff first - all lowercase please

# Basic yes and no, for boolean questions but we're not using boolean types here
$VALUE_YES = "yes";
$VALUE_NO = "no";

# Litres per second selectors
$VALUE_LPS_10 = "10_lps";
$VALUE_LPS_20 = "20_lps";
$VALUE_LPS_50 = "50_lps";

# Air changes per hour selectors
$VALUE_ACH_6 = "ach";

$VALUES_ACH = array(
    $VALUE_ACH_6,
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
$DISPLAY_ACH_6 = "6 Air Changes per Hour (ACH)";

# Measurements
$DISPLAY_CUBIC_METRE = "m3";
$DISPLAY_CUBIC_FOOT = "cubic feet";

?>
