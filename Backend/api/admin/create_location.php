<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = json_decode($_GET['json']);

    $role = $data->role;
    $name = $data->name;
    $address = $data->address;
    $ot = $data->open_time;
    $ct = $data->close_time;

    if ($role == 'GOD') {

        $obj->insert('locations', ['name' => $name, 'address' => $address, 'open_time' => $ot, 'close_time' => $ct, 'status' => 3]);
        $res = $obj->getResult();
        if ($res[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'Add Location Successful'
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Add Location Failed Successful'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Insuffient Permission'
        ]);
    }
}
?>