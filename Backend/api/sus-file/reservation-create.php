<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../../vendor/autoload.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"));
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'];
        $secret_key = "Hilal ahmad khan";
        $user_data = JWT::decode($jwt, $secret_key, array('HS256'));

        $user_id = $user_data->data->user_id;
        $table_id = $data->table_id;

        $obj->insert('reservations', ['table_id' => $table_id, 'user_id' => $user_id, 'status' => 3]);
        $result = $obj->getResult();
        if ($result[0] == 1) {

            #ต้องใส่ก้อน orders
            if ($data->menu[0] != null){
                $tmp = "";
                $obj->select('tables', 'location_id', null, "table=$table_id", null, null); #ยังไม่เสร็จ
                foreach ($data->menu as $menu){
                    if ($menu == $data->menu[sizeof($data->menu)-1]){
                        $tmp .= "($res_id, $menu[0], $menu[1])";
                    }else{
                        $tmp .= "($res_id, $menu[0], $menu[1]),";
                    }
                }
                $obj->insertlagacy('orders', 'res_id, user_id, amount', $tmp);
                # ต้องเช็คว่าเข้าไปไหมด้วย
            }

            echo json_encode([
                'status' => 1,
                'message' => "Reservation Add Successfully",
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "Server Problem",
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
