<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include 'database/Database.php';

if ($_SERVER["REQUEST_METHOD"] == 'GET') {
    $data = json_decode($_GET['json']);
    if (isset($data->menu[0]))
        foreach ($data->menu as $datas) {
            echo $datas[0] . " " . $datas[1] . ";";
        }
}
?>