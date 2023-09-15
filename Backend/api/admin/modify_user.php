<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = json_decode($_GET['json']);

    $user = $data->id;
    $role = $data->role;
    $status = $data->status;

    if ($role == "GOD") {

        $obj->update("users",['status' => $status] , "user_id={$user}");
        $result = $obj->getResult();

        if ($result[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => "User Delete Successfully",
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
            'message' => 'Insufficient Permission'
        ]);
    }
}
?>