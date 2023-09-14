<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:GET');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $data = json_decode($_GET['json']);

    $id = $data->manager_id;
    $role = $data->role;

    if ($role == "MANAGER") {
        $obj->select('locations', "*", null, "manager_id={$id}", 'status', null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
        $res = $obj->getResult();
        if ($res) {
            echo json_encode([
                'status' => 1,
                'message' => $res,
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
