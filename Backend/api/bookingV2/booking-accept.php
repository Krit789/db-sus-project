<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'GET') {

    $data = json_decode($_GET['json']);

    $id = $data->res_id;
    $role = $data->role;

    if ($role == "MANAGER" || $role == "GOD") {
        $obj->update('reservations', ['status' => 1], "res_id={$id}");
        $result = $obj->getResult();
        if ($result[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => "Successfully",
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "Server Problem",
            ]);
        }
    } else {
        echo json_encode([
            'status' => 0,
            'message' => "Insufficient Permission",
        ]);
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => "Access Denied",
    ]);
}
