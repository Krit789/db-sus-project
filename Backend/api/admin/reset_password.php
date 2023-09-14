<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../random.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = json_decode($_GET['json']);

    $user = $data->id;
    $role = $data->role;
    $password = randomPassword(10);
    $new_password = password_hash($password, PASSWORD_DEFAULT);

    if ($role == "GOD") {

        $obj->update("users", ['password' => $new_password], "user_id={$user}");
        $result = $obj->getResult();

        if ($result[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => $passowrd,
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