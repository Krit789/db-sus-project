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

    if ($role == "GOD") {

        $obj->select("reservations", "*", 'tables using (table_id)', null, "res_id desc", null);
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