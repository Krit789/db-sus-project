<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $data = json_decode($_GET['json']);

    $role = $data->role;
    $id = $data->location_id;

    if ($role == "MANAGER" || $role == "GOD"){

        $obj->select("menus", "*", null, null, null, null);
        $result = $obj->getResult();

        $obj->select("restrictions", "menu_id", null, "location_id={$id}", 'menu_id', null);
        $result2 = $obj->getResult();

        echo json_encode([
            'status' => 1,
            'message' => [$result, $result2]
        ]);
    }else{
        echo json_encode([
            'status' => 0,
            'message' => 'Insufficient Permission',
        ]);
    }
}
?>