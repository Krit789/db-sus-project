<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../check.php';

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'GET') {

    $data = json_decode($_GET['json']);

    #น่าจะต้องถามเพิ่มว่า res_id, user_id ตรงไหม
    $id = $data->res_id;
    $user = $data->id;

    if (readReservation($user, $id)) {
        $obj->update('reservations', ['status' => 2], "res_id={$id}");
        $result = $obj->getResult();

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
