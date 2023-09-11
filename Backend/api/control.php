<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include 'database/Database.php';

use \Firebase\JWT\JWT;

function readuser($id)
{
    $obj = new Database();
    $obj->select('users', 'access_token', null, "user_id={$id}", null, null);
    $res = $obj->getResult();
    return $res[0]['access_token'];
}

function readReservation($id, $res)
{
    $obj = new Database();
    $obj->select('reservations', 'user_id', null, "res_id={$res}", null, null);
    $res = $obj->getResult();
    return isset($res[0]['user_id']) == $id; #ยังไม่แน่ใจว่าได้ไหมเพราะยังไม่มี ข้อมูล ให้ทดสอบ
}

function test()
{
    $obj = new Database();
    try {
        $data = json_decode(file_get_contents("php://input"));

        $id = $data->id;
        $obj->select("users", "*", null, "user_id={$id}", null, null);
        $data = $obj->getResult();

        echo json_encode([
            'status' => 1,
            'message' => $data[0],
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 0,
            'message' => $e->getMessage(),
        ]);
    }
}

function booking_accept()
{
    $obj = new Database();
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {

        $data = json_decode(file_get_contents("php://input"));
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'];

        $secret_key = "Hilal ahmad khan";
        $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
        $user_data = $user_data->data;

        $role = $user_data->role;
        $id = $data->res_id;

        if ($role == "MANAGER" || $role == "GOD") {
            $obj->update('reservations', ['status' => 1], "res_id={$id}");
            $result = $obj->getResult();
            if ($result[0] == 1) {
                echo json_encode([
                    'status' => 1,
                    'message' => "Successfully",
                ]);
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => "Server Problem",
                ]);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "Wrong Role Denied",
            ]);
        }
    } else {
        echo json_encode([
            'status' => 0,
            'message' => "Access Denied",
        ]);
    }
}

function booking_cancel()
{
    $obj = new Database();

    if ($_SERVER["REQUEST_METHOD"] == 'POST') {

        $data = json_decode(file_get_contents("php://input"));
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'];

        $secret_key = "Hilal ahmad khan";
        $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
        $user_data = $user_data->data;

        #น่าจะต้องถามเพิ่มว่า res_id, user_id ตรงไหม
        $id = $data->res_id;

        if (readReservation($user_data->id, $id)) {
            $obj->update('reservations', ['status' => 2], "res_id={$id}");
            $result = $obj->getResult();

            echo json_encode([
                'status' => 1,
                'message' => "Successfully",
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "Server Problem",
            ]);
        }
    } else {
        echo json_encode([
            'status' => 0,
            'message' => "Access Denied",
        ]);
    }
}

function booking_create()
{
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
                if ($user_data->token == readuser($user_data->id)) {

                    if ($data->menu[0] != null) { #ถ้ามี menu มาให้ทำอันนี้ menu ต้องเป็น array[2]: array[0]=>menu_id, array[1]=>amount ex.[[1, 2], [9, 2]]
                        $tmp = "";
                        $obj->select('reservations', 'res_id', null, "table=$table_id and user_id=$id", 'res_id desc', 1);
                        $resutl = $obj->getResult();
                        $res_id = $resutl[0]['res_id'];

                        foreach ($data->menu as $menu) {
                            //[0] menu_id [1] จำนวน 
                            if ($menu == $data->menu[sizeof($data->menu) - 1]) {
                                $tmp .= "($res_id, $menu[0], $menu[1])";
                            } else {
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
}

function booking_modify()
{
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

        if (readReservation($user_data->id, $res_id)) {
            if ($data->menu[0] != null) {
                $menu = $data->menu;
                $obj->delete('orders', "res_id='{$res_id}'");
                $result = $obj->getResult();
                if ($result[0] == 1) {

                    $tmp = "";
                    foreach ($data->menu as $menu) {
                        //[0] menu_id [1] จำนวน 
                        if ($menu == $data->menu[sizeof($data->menu) - 1]) {
                            $tmp .= "($res_id, $menu[0], $menu[1])";
                        } else {
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
            $obj->update('reservations', ['arrival' => $arrival], "res_id='{$res_id}'");
            $result = $obj->getResult(); #อยากเช็คอันนี้ด้วย

        } else {
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
}

function booking_viewallFood()
{
    $obj = new Database();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        try {
            $data = json_decode(file_get_contents("php://input"));
            $id = htmlentities($data->location_id);

            $obj->select('menus', "*", null, "menu_id not in (select restricted_menu_id from restrictions where location_id = $id)", null, null);
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
}

function booking_viewallFoodOrder()
{
    $obj = new Database();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        try {
            $data = json_decode(file_get_contents("php://input"));
            $id = htmlentities($data->res_id);

            #น่าจะต้องถามเพิ่มว่า res_id, user_id ตรงไหม
            $obj->select('orders', "*", "menus using (menu_id)", "res_id='{$id}'", "category_id", null);
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
}

function booking_viewallLocation()
{
    $obj = new Database();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        try {
            $obj->select('locations', "location_id, name, address, open_time, close_time, status", null, "status = 1", "status, location_id", null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
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
}

function booking_viewallBooking()
{
    $obj = new Database();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        try {
            $allheaders = getallheaders();
            $jwt = $allheaders['Authorization'];

            $secret_key = "Hilal ahmad khan";
            $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
            $user_data = $user_data->data;

            $id = $user_data->id;
            $obj->select('reservations', "*", null, "user_id='{$id}'", 'res_id DESC', null);
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
}

function booking_viewdetailBooking()
{
    $obj = new Database();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        try {
            $data = json_decode(file_get_contents("php://input"));
            $allheaders = getallheaders();
            $jwt = $allheaders['Authorization'];

            $secret_key = "Hilal ahmad khan";
            $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
            $user_data = $user_data->data;

            $id = $user_data->id;
            $res_id = $data->res_id;
            if (readReservation($id, $res_id)) {
                $obj->select('reservations', "*", null, "res_id='{$res_id}'", null, null);
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
}

function booking_viewdetailLocation()
{
    $obj = new Database();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        try {
            $data = json_decode(file_get_contents("php://input"));
            $id = $data->location_id;

            $obj->select('locations', "*", null, "location_id = '{$id}'", null, null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
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
}
