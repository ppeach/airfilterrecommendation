<?php
require_once(__DIR__.'/../includes/init.php');

if(isset($_POST['click'])) {
    $country = $_POST['country'];
    $product = $_POST['product'];
    $link = $_POST['link'];
    if (!is_dir(__DIR__.'/../data/analytics/')) {
        mkdir(__DIR__.'/../data/analytics');
    }
    $data = json_decode(file_get_contents(__DIR__.'/../data/analytics/'.$country.'.json'), true);

    (empty($data[$product]['view'])) ? $view = 1 : $view = $data[$product]['view'] + 1;
    (empty($data['total'])) ? $total = 1 : $total = $data['total'] + 1;
    $now = date('Ymd');
    (empty($data['bydate'][$now])) ? $today = 1 : $today = $data['bydate'][$now] + 1;
    $bydate = array(
        $now => $today
    );
    $product_view = array(
        'link' => $link,
        'view' => $view
    );
    $hits = manageJson('total', $total, 'analytics/'.$country.'.json');
    $today = manageJson('bydate', $bydate, 'analytics/'.$country.'.json');
    $hit = manageJson($product, $product_view, 'analytics/'.$country.'.json');
    if($hit) {
        $message = array('status' => 'success', 'message' => 'Click event stored', 'product' => $product);
    } else {
        $message = array('status' => 'error', 'message' => 'Click not stored', 'error' => $hit);
    }

    return print json_encode($message);
}

header("Location: login.php");

?>