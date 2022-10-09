<?php
require_once('includes/init.php');

// $api_key = config('api_key');

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Set data from POST
    $country = $_POST['country'];
    $max_an = $_POST['max-an'];
    $wifi = $_POST['wifi'];
    $ach  = $_POST['ach'];
    $room_size = $_POST['room-size'];
    $rms_type = $_POST['m3-or-cu'];
    $no_of_occ = $_POST['no-of-occ'];
    $prefltr = $_POST['prefilter'];

    // Get data from google sheets or json file
    $data = getHepa($country);

    // Filter data based on user input
    // Filter by max acceptable noise
    $filter_result = array_filter($data, function($item) use ($max_an){
        if($max_an != 0){
            return ($item['Noise (dBA)'] <= $max_an);
        } else {
            return true;
        }
    });

    // Filter by wifi
    $filter_result = array_filter($filter_result, function($item) use ($wifi){
        if($wifi != 'Not fussed'){
            return ($item['Wifi'] == $wifi);
        } else {
            return true;
        }
    });

    // Filter by prefilter
    $filter_result = array_filter($filter_result, function($item) use ($prefltr){
        if($prefltr != 'Not fussed'){
            return ($item['Prefilter'] == $prefltr);
        } else {
            return true;
        }
    });

    // Calculate ACH and Total Cost
    $types = array(
        'cadrm3'    => $cadr_m3,
        'cadrcubic' => $cadr_cubic,
        'cadrlitre' => $cadr_litre,
        'cost'      => $cost
    );
    $achs   = array(
        'room_size' => $room_size,
        'room_type' => $rms_type,
        'no_off_occ'=> $no_of_occ
    );
    $hepa_result = calculateACH($filter_result, $ach, 999999, $types, $achs);

    // Get total result
    $total = count($hepa_result);

    // Get result in json format
    $result = array(
        'total' => $total,
        'data' => $hepa_result
    );
    
    echo json_encode($result);

} elseif(isset($_GET['country'])) {

    // Set data for Countries dropdown
    echo json_encode(countries($sheets_id));

} else {
    $result = array(
        'error' => 'Please use POST method'
    );
    
    echo json_encode($result);
}