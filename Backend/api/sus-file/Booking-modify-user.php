<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../check.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $data = json_decode(file_get_contents("php://input"));
    $allheaders = getallheaders();
    $jwt = $allheaders['Authorization'];

    $secret_key = "Hilal ahmad khan";
    $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
    $user_data = $user_data->data;

    #น่าจะต้องถามเพิ่มว่า res_id, user_id ตรงไหม
    $res_id = $data->res_id;
    $arrival = $data->arrival;

    if (readReservation($user_data->id, $res_id)){
        if ($data->menu[0] != null){
            $menu = $data->menu;
            $obj->delete('orders', "res_id='{$res_id}'");
            $result = $obj->getResult();
            if ($result[0] == 1) {
                
                $tmp = "";
                foreach ($data->menu as $menu){
                    //[0] menu_id [1] จำนวน 
                    if ($menu == $data->menu[sizeof($data->menu)-1]){
                        $tmp .= "($res_id, $menu[0], $menu[1])";
                    }else{
                        $tmp .= "($res_id, $menu[0], $menu[1]),";
                    }
                }
                $obj->insertlagacy('orders', 'res_id, menu_id, amount', $tmp);
    
                $resutl = $obj->getResult(); #อยากเช็คแต่ยังก่อน
    
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => "Server Problem",
                ]);
            }
        }

        #เพิ่มเวลานัดอีก+++++++++
        $obj->update('reservations', ['arrival'=>$arrival], "res_id='{$res_id}'");
        $result = $obj->getResult(); #อยากเช็คอันนี้ด้วย
    
    }else{
        echo json_encode([
            'status' => 0,
            'message' => 'ID Customer not match',
        ]);
    }
    
} else {
    echo json_encode([
        'status' => 0,
        'message' => "Access Denied",
    ]);
}
