<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    try {
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'];

        $secret_key = "Hilal ahmad khan";
        $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
        $user_data = $user_data->data;

        $id = $user_data->id;
        $obj->select('reservations', "*", null, "user_id='{$id}'", 'res_id DESC', null);
        $res = $obj->getResult();
        if ($res) {
            echo json_encode([
                'status' => 1,
                'message' => $res,
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "server problem",
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 0,
            'message' => $e->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
?>