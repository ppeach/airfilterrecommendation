<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Get config key
function config($key)
{
    $file = file_get_contents("data/config/config.json");
    $deco = json_decode($file, true);
    return $deco[$key];
}

// Get Refresh token from google OAuth
function generate_token()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/oauth2/v4/token");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "client_secret=" . config('client_secret') . "&grant_type=refresh_token&refresh_token=" . config('refresh_token') . "&client_id=" . config('client_id'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    file_put_contents("data/config/token.json", $result);
    return json_decode($result, true);
}

// Check if the token is expired and get new refesh token
function checkexp()
{
    $fpath = "data/config/token.json";
    if(file_exists($fpath) && filemtime($fpath) + 3600 > time()){
        $data = json_decode(file_get_contents($fpath), true);
        if(isset($data["access_token"])){
            $token = $data["access_token"];
        } else {
            $data = generate_token();
            $token = $data["access_token"];
        }
    } else {
        $data = generate_token();
        $token = $data["access_token"];
    }
    return $token;
}

// Check if there's no error in token
function checktoken()
{
    $fpath = "data/config/token.json";
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

// Get data from google sheets
function getSheets($sheets_id, $token, $list=true, $range=null, $majorDimension=null)
{
    if($list){
        $url = "https://sheets.googleapis.com/v4/spreadsheets/" . $sheets_id;
    } elseif($majorDimension) {
        $url = "https://sheets.googleapis.com/v4/spreadsheets/" . $sheets_id ."/values/". $range ."?majorDimension=". $majorDimension . "&valueRenderOption=UNFORMATTED_VALUE";
    } else {
        $url = "https://sheets.googleapis.com/v4/spreadsheets/" . $sheets_id ."/values/". $range . "?valueRenderOption=UNFORMATTED_VALUE";
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
    $countries_path = 'data/config/countries.json';
    if(file_exists($countries_path)){
        $dcountries = json_decode(file_get_contents($countries_path), true);
    } else {
        $token = checkexp();
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
        return ($item['Model'] && $item[$cost] && $item['CADR (m3/hr)'] && $item['Noise (dBA)'] != "");
    });

    // Add currency data
    foreach ($data as $key => $value) {
        $data[$key]['Cost'] = $data[$key][$cost];
        unset($data[$key][$cost]);
        $data[$key]['currency'] = substr($cost,5);
        $fmt = new NumberFormatter( 'en_US@currency='.$data[$key]['currency'], NumberFormatter::CURRENCY );
        $data[$key]['currency_format'] = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
    }

    sort($data);

    return $data;
}

// Get data from google sheets by country
function getHepa($country)
{
    $sheets_id  = config('sheet_id');
    $range      = config('sheet_range');
    $hepapath   = 'data/db/'.$country.'.json';

    // Check if the country-name.json is exist, else create it
    if(file_exists($hepapath) && json_decode(file_get_contents($hepapath), true) != null){
        $data = json_decode(file_get_contents($hepapath), true);
    } else {
        if(file_exists($hepapath)){
            unlink($hepapath);
        }
        $token = checkexp();
        // Get data from Google Sheets
        $data = getSheets($sheets_id, $token, false, $range);

        $data = CFdata($data);

        file_put_contents($hepapath, json_encode($data));
    }

    return $data;
}

// Calculate ACH and get Total Cost
function calculateACH($data, $ach, $types=array(), $achs=array())
{
    // Calculate ACH and Total Cost
    foreach($data as $key => $value){
        if($ach == 'ach'){
            if($achs['room_type'] == 'm3'){
                $ach_unit = (6 * $achs['room_size'])/$value[$types['cadrm3']];
                $ach_value = (ceil($ach_unit) * $value[$types['cadrm3']])/$achs['room_size'];
                $ach_value_min = ((ceil($ach_unit) - 1) * $value[$types['cadrm3']])/$achs['room_size'];
            } else {
                $ach_unit = (6 * $achs['room_size'])/$value[$types['cadrcubic']]/60;
                $ach_value = (ceil($ach_unit) * $value[$types['cadrcubic']] * 60)/$achs['room_size'];
                $ach_value_min = ((ceil($ach_unit) - 1) * $value[$types['cadrcubic']] * 60)/$achs['room_size'];
            }
        } else {
            $lps = (int)trim($ach, '_lps');
            $ach_unit = ($lps * $achs['no_off_occ'])/$value[$types['cadrlitre']];
            $ach_value = (ceil($ach_unit) * $value[$types['cadrlitre']])/$achs['no_off_occ'];
            $ach_value_min = ((ceil($ach_unit) - 1) * $value[$types['cadrlitre']])/$achs['no_off_occ'];
        }
        $ach_needs = ($ach_unit < 0) ? 1 : ceil($ach_unit);
        $data[$key]['ACH unit'] = $ach_unit;
        $data[$key]['ACH'] = round($ach_value, 1);
        $data[$key]['ACH -1'] = round($ach_value_min, 1);
        $data[$key]['ACH needs'] = $ach_needs;
        $data[$key]['Total Cost'] = $value[$types['cost']] * $ach_needs;
        $data[$key]['Total dBA'] = totaldBA($ach_needs, $value[$types['noisedba']]);
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
    $range = config('sheet_range');
    $token = checkexp();

    // Update countries.json
    $countries_path = 'data/config/countries.json';
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

    // Remove all country json files
    $hepafiles = glob('data/db/*.{json}', GLOB_BRACE);
    foreach($hepafiles as $hepafile){
        unlink($hepafile);
    }

    // Generate new country json files
    foreach($countries as $country){
        $hepapath = 'data/db/'.$country.'.json';

        // Check if there's still a country-name.json file and delete it
        if(file_exists($hepapath)){
            unlink($hepapath);
        }

        // Get data from Google Sheets
        $data = getSheets($sheets_id, $token, false, $country.'!'.$range);
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