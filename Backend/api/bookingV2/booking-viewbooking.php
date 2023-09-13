<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:GET');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    try {
        $data = json_decode($_GET['json']);

        $id = $data->id;
        $obj->select('reservations', "*", null, "user_id={$id}", 'res_id DESC', null);
        $res = $obj->getResult();
        if ($res) {
            echo json_encode([
                'status' => 1,
                'message' => $res,
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "server problem",
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 0,
            'message' => $e->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
?>