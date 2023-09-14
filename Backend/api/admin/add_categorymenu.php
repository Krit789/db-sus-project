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
    $name = $data->name;

    if ($role == 'GOD') {

        $obj->insert('menu_category', ['name' => $name]);
        $res = $obj->getResult();
        if ($res[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'Add Category Menu Successful'
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Add Category Menu Failed Successful'
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