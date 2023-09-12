<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../check.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    try {
        $data = json_decode($_GET['json']);

        $user = $data->id;
        $res_id = $data->res_id;

        if (readReservation($user, $res_id)) {
            $obj->select('reservations', "*", null, "res_id={$res_id}", null, null);
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
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'ID Customer not match',
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