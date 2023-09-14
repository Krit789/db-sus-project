<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:GET');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $data = json_decode($_GET['json']);

    $role = $data->role;
    $id = $data->location_id;
    $name = $data->name;
    $address = $data->address;
    $ot = $data->open_time;
    $ct = $data->close_time;
    $status = $data->status;

    if ($role == "MANAGER" || $role == "GOD") {
        $obj->update("locations", ['name' => $name, 'address' => $address, 'open_time' => $ot, 'close_time' => $ct, 'status' => $status], "location_id={$id}");
        $res = $obj->getResult();
        if ($res[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'Update Info Location Successful',
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "server problem", #ถ้ามันหาไม่เจอสัก row มันก็จะเข้าอันนี้
            ]);
        }
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Insuffient Permission'
        ]);
    }

} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
