<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    $data = json_decode($_GET['json']);

    $role = $data->role;
    $loca_id = $data->location_id;
    $id = $data->user_id;

    if ($role == 'GOD'){

        $obj->update('locations', ['manager_id' => $id], "location_id={$loca_id}");
        $res = $obj->getResult();
        if ($res[0] == 1){
            echo json_encode([
                'status' => 1,
                'message' => 'Add Manager to Location Successful'
            ]);
        }else{
            echo json_encode([
                'status' => 0,
                'message' => 'Add Manager to Location Failed Successful'
            ]);
        }
    }else{
        echo json_encode([
            'status' => 0,
            'message' => 'Insuffient Permission'
        ]);
    }
}
?>