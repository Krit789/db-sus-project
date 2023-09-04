<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $data = json_decode(file_get_contents("php://input"));
    $id = $data->res_id;
    $obj->delete("reservations", "res_id='{$id}'");
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
        'message' => "Access Denied",
    ]);
}
