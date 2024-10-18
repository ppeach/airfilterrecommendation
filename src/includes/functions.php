<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require('constants.php');

// Get config key
function config($key, $type=null)
{
    $cpath = __DIR__."/../data/config";
    $file = file_get_contents($cpath."/config.json");
    if($type == 'token'){
        $file = file_get_contents($cpath."/refresh_token.json");
    }
    $deco = json_decode($file, true);
    return $deco[$key];
}

// Verify jwt token from google OAuth
function verify_jwtToken($token)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://oauth2.googleapis.com/tokeninfo',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'id_token='.$token,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

// Get refresh token from google OAuth
function get_refreshToken($code)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://oauth2.googleapis.com/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'client_id='.CLIENT_ID.'&client_secret='.CLIENT_SECRET.'&code='.$code.'&grant_type=authorization_code&redirect_uri='.urlencode(REDIRECT_URL.'/auth.php'),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $data = json_decode($response, true);
    file_put_contents(__DIR__."/../data/config/refresh_token.json", $response);

    return $data;
}

function checkRefreshToken()
{
    $fpath = __DIR__."/../data/config/refresh_token.json";
    if(file_exists($fpath)){
        $data = json_decode(file_get_contents($fpath), true);

        if(array_key_exists("error", $data)){
            $message = array('status' => 'error', 'message' => $data);
        } else {
            $message = array(
                'status' => 'ok',
                'refresh_token' => $data['refresh_token'],
                'scope' => $data['scope']);
        }
    } else {
        $message = array('status' => 'error', 'message' => 'Refresh Token file not found');
    }
    return $message;
}

// Get access token from google OAuth
function generate_accessToken()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, OAUTHURL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "client_secret=" . CLIENT_SECRET . "&grant_type=refresh_token&refresh_token=" . config('refresh_token', 'token') . "&client_id=" . CLIENT_ID);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    file_put_contents(__DIR__."/../data/config/access_token.json", $result);
    return json_decode($result, true);
}

// Check if the token is expired and get new refesh token
function check_accessTokenExp()
{
    $fpath = __DIR__."/../data/config/access_token.json";
    if(file_exists($fpath) && filemtime($fpath) + 3600 > time()){
        $data = json_decode(file_get_contents($fpath), true);
        if(isset($data["access_token"])){
            $token = $data["access_token"];
        } else {
            $data = generate_accessToken();
            $token = $data["access_token"];
        }
    } else {
        $data = generate_accessToken();
        $token = $data["access_token"];
    }
    return $token;
}

// Check if there's no error in token
function checktoken()
{
    $fpath = __DIR__."/../data/config/access_token.json";
    if(file_exists($fpath)){
        $data = json_decode(file_get_contents($fpath), true);

        if(array_key_exists("error", $data)){
            $message = array('status' => 'token error', 'message' => $data);
        } else {
            $message = array('status' => 'token valid', 'message' => 'token_scope ' . $data["scope"]);
        }
    } else {
        $message = array('status' => 'token error', 'message' => 'Token file not found');
    }
    return $message;
}

// Manage JSON files inside config folder
function manageJson($key, $value, $file)
{
    $file = __DIR__."/../data/".$file;
    $data = [];
    if(file_exists($file)){
        $data = json_decode(file_get_contents($file), true);
    }
    $data[$key] = $value;
    file_put_contents($file, json_encode($data));
    return $data;
}

// Get data from google sheets
function getSheets($sheets_id, $token, $countries=true, $range=null, $majorDimension=null)
{
    $range = ($range != null) ? urlencode($range) : $range;
    if($countries){
        $url = SHEETSAPI . $sheets_id . "?fields=sheets.properties(title,gridProperties)";
    } elseif($majorDimension) {
        $url = SHEETSAPI . $sheets_id ."/values/". $range ."?majorDimension=". $majorDimension . "&valueRenderOption=UNFORMATTED_VALUE";
    } else {
        $url = SHEETSAPI . $sheets_id ."/values/". $range . "?valueRenderOption=UNFORMATTED_VALUE";
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$token,
    ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);
    if(isset($data["values"])){
        return $data["values"];
    } else {
        return $data["sheets"];
    }
}

// Get sheets name from google sheets and set to countries array
function countries($sheets_id)
{
    // Check if the countries.json is exist, else create it
    $countries_path = __DIR__.'/../data/config/countries.json';
    if(file_exists($countries_path)){
        $dcountries = json_decode(file_get_contents($countries_path), true);
    } else {
        $token = check_accessTokenExp();
        // Get data from Google Sheets
        $dcountries = getSheets($sheets_id, $token);
        file_put_contents($countries_path, json_encode($dcountries));
    }

    // Set data for Countries dropdown
    $countries = array_column($dcountries, 'properties');
    $countries = array_column($countries, 'title');
    $countries = array_unique($countries);
    sort($countries);
    
    return $countries;
}

// Format dan Clean up data from google sheets
function CFdata($data)
{
    // Extract title from data
    $keys = array_values($data[0]);
    $cost = $keys[1];

    // Remove title from data
    array_shift($data);

    // Assign key to each array value
    foreach ($data as $key => $values) {
        foreach ($values as $k => $value) {
            $data[$key][$keys[$k]] = $value;
            unset($data[$key][$k]);
        }
    }

    // Clean up null model
    $data = array_filter($data, function($item) use ($cost) {
        return ($item['Model'] && $item[$cost] && $item['CADR (m3/hr)'] && $item['Noise (dBA)'] && ($item['CADR (Litre/sec)'] || $item['CADR (Cubic feet/min)']) != "");
    });
    
    // Add currency data
    global $CURRENCY_SYMBOLS;
    foreach ($data as $key => $value) {
        $data[$key]['Cost'] = $data[$key][$cost];
        unset($data[$key][$cost]);
        $data[$key]['currency'] = substr($cost,5);
        $data[$key]['currency_format'] = $CURRENCY_SYMBOLS[$data[$key]['currency']];
    }

    sort($data);
    
    return $data;
}

// Get data from google sheets by country
function getHepa($country)
{
    $sheets_id  = config('sheet_id');
    $hepapath   = __DIR__.'/../data/db/'.$country.'.json';

    // Check if the country-name.json is exist, else create it
    if(file_exists($hepapath) && json_decode(file_get_contents($hepapath), true) != null){
        $data = json_decode(file_get_contents($hepapath), true);
    } else {
        if(file_exists($hepapath)){
            unlink($hepapath);
        }
        $token = check_accessTokenExp();
        // Get data from Google Sheets
        $data = getSheets($sheets_id, $token, false, $country);

        $data = CFdata($data);

        file_put_contents($hepapath, json_encode($data));
    }

    return $data;
}

// Calculate filter replacement cost schedule
function calculateFRC($filtercost, $devices, $months, $lifetime){
    // Device lifetime (default 4 years or 48 months)
    $lifetime = $lifetime * 12;

    // Replacement schedule will be minus 1 because the device come with filter installed
    $schedule = ($lifetime / $months) - 1;

    // Calculate FRC
    // Filter cost x No. of devices x Replacement schedule
    $frc = $filtercost * $devices * $schedule;

    return $frc;
}

// Calculate electricity cost
function calculateEC($tariff, $watts, $devices)
{
    // Constant use 24hrs per day, 7 days per week and 365 days per year (8760 hrs per year)
    // Office hours usually use 8 hrs per day, 52 weeks or 260 work days (2080 hrs per year)
    // School hours based on 8 hrs per day, 5 days per week and 39 weeks per year (1560 hrs per year)
    $hours = 8760;
    $schoolhours = 1560;
    $officehours = 2080;

    //kwH = (watts x hours) / 1000
    // watts is user defined input
    $normal = (($watts * $hours) / 1000) * $tariff * $devices;
    $school = (($watts * $schoolhours) / 1000) * $tariff * $devices;
    $office = (($watts * $officehours) / 1000) * $tariff * $devices;

    $energyCost = array(
        'normal' => $normal,
        'school' => $school,
        'office' => $office
    );

    return $energyCost;
}

// Calculate Total cost of ownership
function calculateTCO($upfront_cost, $filter_replacement_cost, $total_energy_cost)
{
    // Total cost of ownership = Upfront cost + Total filter replacement cost + Total electricity cost
    $tco = $upfront_cost + $filter_replacement_cost + $total_energy_cost;
    return $tco;
}

// Calculate ACH and get Total Cost
function calculateACH($data, $ach, $max_units, $min_cadr, $types=array(), $achs=array())
{
    global $ACH_OPTIONS, $MEASUREMENT_OPTIONS;
    // Calculate ACH and Total Cost
    foreach($data as $key => $value){
        if(array_key_exists($ach, $ACH_OPTIONS)){
            $ach_proxy = intval(preg_replace('~\D~', '', $ach)) - 0.6;
            if($achs['room_type'] == $MEASUREMENT_OPTIONS['m3']){
                $ach_unit = ($ach_proxy * $achs['room_size'])/$value[$types['cadrm3']];
                $ach_value = (ceil($ach_unit) * $value[$types['cadrm3']])/$achs['room_size'];
                $ach_value_min = ((ceil($ach_unit) - 1) * $value[$types['cadrm3']])/$achs['room_size'];
            } else {
                $ach_unit = ($ach_proxy * $achs['room_size'])/$value[$types['cadrcubic']]/60;
                $ach_value = (ceil($ach_unit) * $value[$types['cadrcubic']] * 60)/$achs['room_size'];
                $ach_value_min = ((ceil($ach_unit) - 1) * $value[$types['cadrcubic']] * 60)/$achs['room_size'];
            }
        } else {
            $lps = intval(preg_replace('~\D~', '', $ach));
            $ach_unit = ($lps * $achs['no_off_occ'])/$value[$types['cadrlitre']];
            $ach_value = (ceil($ach_unit) * $value[$types['cadrlitre']])/$achs['no_off_occ'];
            $ach_value_min = ((ceil($ach_unit) - 1) * $value[$types['cadrlitre']])/$achs['no_off_occ'];
        }
        $ach_needs = ($ach_unit < 0) ? 1 : max(ceil($ach_unit), ceil($min_cadr / $value[$types['cadrm3']]));
        $data[$key]['ACH unit'] = $ach_unit;
        $data[$key]['ACH'] = round($ach_value, 1);
        $data[$key]['ACH -1'] = round($ach_value_min, 1);
        $data[$key]['ACH needs'] = $ach_needs;
        $data[$key]['Total Cost'] = $value[$types['cost']] * $ach_needs;
        $data[$key]['Total dBA'] = totaldBA($ach_needs, $value[$types['noisedba']]);

        if ($ach_needs > $max_units) {
            unset($data[$key]);
        }
    }

    // Sort Total Cost (low to High)
    usort($data, function($a, $b) {
        return $a['Total Cost'] - $b['Total Cost'];
    });

    return $data;
}

function totaldBA($x, $y)
{
    // Equation is 10 * log10(10^(Y/10) * X)
    // Y = dBA value
    // X = number of devices
    $result = 10 * log10(pow(10, ($y/10)) * $x);
    return round($result, 1);
}

// Function to update data from google sheets
function updateData()
{
    $sheets_id = config('sheet_id');
    $token = check_accessTokenExp();

    // Update countries.json
    $countries_path = __DIR__.'/../data/config/countries.json';
    if(file_exists($countries_path)){
        unlink($countries_path);
    }
    
    // Get data from Google Sheets
    $dcountries = getSheets($sheets_id, $token);
    file_put_contents($countries_path, json_encode($dcountries));

    $countries = array_column($dcountries, 'properties');
    $countries = array_column($countries, 'title');
    $countries = array_unique($countries);
    sort($countries);

    if (!is_dir(__DIR__.'/../data')) {
        mkdir(__DIR__.'/../data');
    }
    
    if (!is_dir(__DIR__.'/../data/db')) {
        mkdir(__DIR__.'/../data/db');
    }
    
    // Remove all country json files
    $hepafiles = glob(__DIR__.'/../data/db/*.{json}', GLOB_BRACE);
    foreach($hepafiles as $hepafile){
        unlink($hepafile);
    }
    
    // Generate new country json files
    foreach($countries as $country){
        $hepapath = __DIR__.'/../data/db/'.$country.'.json';
        
        // Check if there's still a country-name.json file and delete it
        if(file_exists($hepapath)){
            unlink($hepapath);
        }
    
        // Get data from Google Sheets
        $data = getSheets($sheets_id, $token, false, $country);
        $data = CFdata($data);
        file_put_contents($hepapath, json_encode($data, JSON_UNESCAPED_UNICODE));
        $result = array(
            'country' => $country,
            'message' => 'Data updated successfully',
            'json_path' => $hepapath
        );
        $results[] = $result;
    }

    return $results;
}

// Analytics for Buy now button
function getAnalytics(){
    $analytics = [];
    $files = glob(__DIR__.'/../data/analytics/*.{json}', GLOB_BRACE);
    foreach($files as $file){
        $country = basename($file, '.json');
        $data = json_decode(file_get_contents($file), true);
        (isset($data['bydate'][date('Ymd')])) ? $today = $data['bydate'][date('Ymd')] : $today = 0;
        $analytic = array(
            'countries' => array(
                'name' => $country,
                'total' => $data['total'],
                'today' => $today
            )
        );
        $analytics[] = $analytic;
    }

    return $analytics;
}

function getAnalyticCountry($country){
    $file = __DIR__.'/../data/analytics/'.$country.'.json';
    $data = json_decode(file_get_contents($file), true);
    $products = [];
    foreach($data as $key => $product){
        if($key === 'total' || $key === 'bydate'){
            continue;
        }
        $products[] = array(
            'product' => $key,
            'link' => $product['link'],
            'view' => $product['view']
        );
    }

    return $products;
}
