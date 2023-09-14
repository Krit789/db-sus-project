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
    $name = $data->name;
    $capa = $data->capacity;

    if ($role == "MANAGER" || $role == "GOD") {
        $obj->insert("tables", ['name' => $name, 'capacity' => $capa, 'location_id' => $id]);
        $result = $obj->getResult();
        if ($result[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => "Add Table Successful"
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "Add Table Falied Successful"
            ]);
        }

    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Insufficient Permission',
        ]);
    }
}
?>