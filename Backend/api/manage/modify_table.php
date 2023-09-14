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
    $id = $data->table_id;
    $name = $data->name;
    $capa = $data->capacity;

    if ($role == "MANAGER" || $role == "GOD") {
        $obj->update("tables", ['name' => $name, 'capacity' => $capa], "table_id={$id}");

        $res = $obj->getResult();
        if ($res[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'Modify Table Successful',
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
