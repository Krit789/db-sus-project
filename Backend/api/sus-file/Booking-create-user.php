<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../../vendor/autoload.php';
include '../check.php';
include '../random.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"));
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'];

        $secret_key = "Hilal ahmad khan";
        $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
        $user_data = $user_data->data;

        $table_id = $data->table_id;
        $id = $user_data->id;
        $arrival = $data->arrival;
        $customer_count = $data->cus_count;

        #จะเกิดอะไรขึ้น ถ้า customer กดจองที่ location_id, table_id เหมือนกัน
        $obj->insert('reservations', ['table_id' => $table_id, 'user_id' => $id, 'arrival' => $arrival, 'status' => 3, 'cus_count' => $customer_count, 'res_code' => randomRescode(8)]);
        $result = $obj->getResult();
        if ($result[0] == 1) {
            if ($user_data->token == readuser($user_data->id)){

                if ($data->menu[0] != null){ #ถ้ามี menu มาให้ทำอันนี้ menu ต้องเป็น array[2]: array[0]=>menu_id, array[1]=>amount ex.[[1, 2], [9, 2]]
                    $tmp = "";
                    $obj->select('reservations', 'res_id', null, "table=$table_id and user_id=$id", 'res_id desc', 1);
                    $resutl = $obj->getResult();
                    $res_id = $resutl[0]['res_id'];

                    foreach ($data->menu as $menu){
                        //[0] menu_id [1] จำนวน 
                        if ($menu == $data->menu[sizeof($data->menu)-1]){
                            $tmp .= "($res_id, $menu[0], $menu[1])";
                        }else{
                            $tmp .= "($res_id, $menu[0], $menu[1]),";
                        }
                    }

                    $obj->insertlagacy('orders', 'res_id, menu_id, amount', $tmp);
                    # ต้องเช็คว่าเข้าไปไหมด้วย ??? หรือป่าว? ??

                    $resutl = $obj->getResult();
                    // if ($resutl[0] == 1){
                        # Nothing just hanging around. เอาไว้เช็คว่าเข้าไหม ไม่ใช้
                    // }
                }

                echo json_encode([
                    'status' => 1,
                    'message' => "Booking Add Successfully",
                ]);

            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Token Problem',
                ]);
            }

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
