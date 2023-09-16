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
    $role_user = $data->role_user;
    $id = $data->user_id;

    if ($role == 'GOD') {

        $obj->update('users', ['role' => $role_user], "user_id={$id}");
        $res = $obj->getResult();
        if ($res[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'Change Role Successful'
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Change Role Failed Successful'
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