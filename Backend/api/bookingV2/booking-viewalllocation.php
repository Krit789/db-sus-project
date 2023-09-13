<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:GET');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    try {
        $obj->select('locations', "location_id, name, address, open_time, close_time, status", null, null, "status, location_id", null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
        $res = $obj->getResult();
        if ($res) {
            echo json_encode([
                'status' => 1,
                'message' => $res,
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "server problem", #ถ้ามันหาไม่เจอสัก row มันก็จะเข้าอันนี้
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