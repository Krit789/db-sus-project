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
    $res_code = $data->res_code;

    if ($role == "MANAGER" || $role == "GOD") {
        $obj->select('reservations', "*", null, "res_id={$id} and res_code={$res_code}", null, null);
        $result = $obj->getResult();

        if ($result[0] == 1) {

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
                'message' => "Code not match"
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
