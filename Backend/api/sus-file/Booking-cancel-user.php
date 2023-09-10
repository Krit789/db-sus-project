<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../check.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $data = json_decode(file_get_contents("php://input"));
    $allheaders = getallheaders();
    $jwt = $allheaders['Authorization'];

    $secret_key = "Hilal ahmad khan";
    $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
    $user_data = $user_data->data;

    #น่าจะต้องถามเพิ่มว่า res_id, user_id ตรงไหม
    $id = $data->res_id;

    if (readReservation($user_data->id, $id)){
        $obj->update('reservations', ['status' => 2], "res_id={$id}");
        $result = $obj->getResult();

        echo json_encode([
            'status' => 1,
            'message' => "Successfully",
        ]);
    }else{
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
