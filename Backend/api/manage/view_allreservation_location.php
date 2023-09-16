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
    $id = $data->location_id;

    if ($role == "MANAGER" || $role == "GOD") {
        $obj->select("reservations", "*", null, "table_id in (select table_id from tables where location_id={$id})", "res_id desc", null);
        $result = $obj->getResult();

        echo json_encode([
            'status' => 1,
            'message' => $result
        ]);
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Insufficient Permission',
        ]);
    }
}
?>