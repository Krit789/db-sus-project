<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include 'check.php';
$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->type) && isset($data->token)) {
        $type = $data->type;
        $access_token = $data->token;
        $user_data = readuserwithtoken($access_token);

        #ถ้าไม่มีข้อมูลใน database จะขึ้น server problem

        if (isset($user_data['user_id'])) {
            switch ($type) {
                case 1: #Manager ยืนยันว่ามาตามที่จอง
                    //ต้องส่งข้อมูล res_id, res_code
                    $role = $user_data['role'];
                    $res_code = $data->res_code;

                    if ($role == "MANAGER" || $role == "GOD") {
                        $obj->select('reservations', "*", null, "res_code={$res_code}", null, null);
                        $result = $obj->getResult();

                        if ($result[0] == 1) {

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
                                'message' => "Code not match"
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => "Insufficient Permission",
                        ]);
                    }
                    break;
                case 2: #Customer ทำการยกเลิกการจอง หรือ Manager กับ Admin ทำการยกเลิกการจองนี้
                    //ต้องส่งข้อมูล res_id
                    $id = $data->res_id;
                    $user = $user_data['user_id'];
                    $role = $user_data['role'];

                    if (readReservation($user, $id) || $role == "MANAGER" || $role == "GOD") {
                        $obj->update('reservations', ['status' => 2], "res_id={$id}");
                        $result = $obj->getResult();

                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "Successfully",
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Failed Successful",
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => "Wrong ID or Role",
                        ]);
                    }
                    break;
                case 3: #Customer ทำการจอง
                    //ต้องส่งข้อมูล table_id, arrival, cus_count, menu(Optional)
                    $table_id = $data->table_id;
                    $user = $user_data['user_id'];
                    $arrival = $data->arrival;
                    $customer_count = $data->cus_count;

                    $date = date_create(substr($arrival, 0, 19));
                    date_add($date, date_interval_create_from_date_string("-1 hours"));
                    $date = date_format($date, "Y-m-d h:m:s");

                    $date2 = date_create(substr($arrival, 0, 19));
                    date_add($date2, date_interval_create_from_date_string("1 hours"));
                    $date2 = date_format($date2, "Y-m-d h:m:s");

                    $obj->select("reservations", "*", null, "table_id={$table_id} and status = 3 and arrival between '{$date}' and '{$date2}'");
                    $result = $obj->getResult();

                    if (!isset($result[0])) {

                        $obj->insert('reservations', ['table_id' => $table_id, 'user_id' => $user, 'arrival' => $arrival, 'status' => 3, 'cus_count' => $customer_count, 'res_code' => randomCode(8)]);
                        $result = $obj->getResult();
                        if ($result[0] == 1) {

                            if (isset($data->menu[0])) { #ถ้ามี menu มาให้ทำอันนี้ menu ต้องเป็น array[2]: array[0]=>menu_id, array[1]=>amount ex.[[1, 2], [9, 2]]
                                $tmp = "";
                                $obj->select('reservations', 'res_id', null, "table=$table_id and user_id=$user", 'res_id desc', 1);
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
                                'message' => "Server Problem",
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => 'Has someone reserved this time'
                        ]);
                    }

                    break;
                case 4: #Customer ต้องการแก้ไขการจอง
                    //ต้องส่งข้อมูล res_id, table_id, arrival, cus_count, menu(จะต้องส่งอันนี้มาก็ต่อเมื่อ Customer แก้ไขข้อมูลตัวเอง)
                    $user = $user_data['user_id'];
                    $res_id = $data->res_id;
                    $table_id = $data->table_id;
                    $arrival = $data->arrival;
                    $customer_count = $data->cus_count;

                    $date = date_create(substr($arrival, 0, 19));
                    date_add($date, date_interval_create_from_date_string("-1 hours"));
                    $date = date_format($date, "Y-m-d h:m:s");

                    $date2 = date_create(substr($arrival, 0, 19));
                    date_add($date2, date_interval_create_from_date_string("1 hours"));
                    $date2 = date_format($date2, "Y-m-d h:m:s");

                    $obj->select("reservations", "*", null, "table_id={$table_id} and status = 3 and arrival between '{$date}' and '{$date2}' and user_id != {$user}");
                    $result = $obj->getResult();

                    if (!isset($result[0])) {
                        if (readReservation($user, $res_id)) {

                            if (isset($data->menu[0])) {
                                $obj->delete('orders', "res_id={$res_id}");
                                $result = $obj->getResult();
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
                            }
                            $obj->update('reservations', ['arrival' => $arrival, 'table_id' => $table_id, 'cus_count' => $customer_count], "res_id='{$res_id}'");
                            $result = $obj->getResult();
                            if ($result[0] == 1) {
                                echo json_encode([
                                    'status' => 1,
                                    'message' => 'Modify Reservation Successful'
                                ]);
                            } else {
                                echo json_encode([
                                    'status' => 0,
                                    'message' => 'Modify Reservation Failed Successful'
                                ]);
                            }
                        } else {
                            echo json_encode([
                                'status' => 0,
                                'message' => 'ID Customer not match',
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => 'Has someone reserved this time'
                        ]);
                    }
                    break;
                case 5: #เรียกดูอาหารของ location_id ที่ส่งมา ทั้งหมด
                    //ต้องส่งข้อมูล location_id
                    $id = $data->location_id;

                    $obj->select('menus', "*", null, "menu_id not in (select menu_id from restrictions where location_id = {$id})", null, null);
                    $res = $obj->getResult();
                    if ($res) {
                        echo json_encode([
                            'status' => 1,
                            'message' => $res,
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 1,
                            'message' => array(),
                        ]);
                    }
                    break;
                case 6: #เรียกดูอาหารการจองล่วงหน้าด้วย res_id
                    //ต้องส่งข้อมูล res_id
                    $id = $data->res_id;

                    #น่าจะต้องถามเพิ่มว่า res_id, user_id ตรงไหม
                    $obj->select('orders', "*", "menus using (menu_id)", "res_id={$id}", null, null);
                    $res = $obj->getResult();
                    if ($res) {
                        echo json_encode([
                            'status' => 1,
                            'message' => $res,
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 1,
                            'message' => array()
                        ]);
                    }
                    break;
                case 7: # Customer ต้องการเรียกดูสาขาทั้งหมด
                    $obj->select('locations', "*", null, null, "status", null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
                    $res = $obj->getResult();
                    if ($res) {
                        echo json_encode([
                            'status' => 1,
                            'message' => $res,
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 1,
                            'message' => array() #ถ้ามันหาไม่เจอสัก row มันก็จะเข้าอันนี้
                        ]);
                    }
                    break;
                case 8: #เรียกดูการจองทั้งหมดของเจ้าของ Account
                    $id = $user_data['user_id'];
                    $obj->select('reservations', "*", null, "user_id={$id}", 'res_id DESC', null);
                    $res = $obj->getResult();
                    if ($res) {
                        echo json_encode([
                            'status' => 1,
                            'message' => $res,
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 1,
                            'message' => array(),
                        ]);
                    }
                    break;
                case 9: # Customer, Manger, Administrator เรียกดูข้อมูลรายละเอียดการจองที่ เรา เลือก
                    //ต้องส่งข้อมูล res_id
                    $user = $user_data['user_id'];
                    $res_id = $data->res_id;
                    $role = $user_data['role'];

                    if (readReservation($user, $res_id) || $role == "MANAGER" || $role == "GOD") {
                        $obj->select('reservations', "*", null, "res_id={$res_id}", null, null);
                        $res = $obj->getResult();
                        if ($res) {
                            echo json_encode([
                                'status' => 1,
                                'message' => $res,
                            ]);
                        } else {
                            echo json_encode([
                                'status' => 1,
                                'message' => array()
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => 'ID Customer not match',
                        ]);
                    }
                    break;
                case 10: #เรียกดูข้อมูลรายละเอียดสาขาที่ เรา เลือก
                    //ต้องส่งข้อมูล location_id
                    $id = $data->location_id;

                    $obj->select('locations', "*", null, "location_id = {$id}", null, null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
                    $res = $obj->getResult();
                    if ($res) {
                        echo json_encode([
                            'status' => 1,
                            'message' => $res,
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 1,
                            'message' => array()
                        ]);
                    }
                    break;
                case 11: #Customer เรียกดู Table ทั้งหมดของ location ที่เลือก
                    //ต้องส่งข้อมูล arrival, location_id
                    $arrival = $data->arrival;
                    $location_id = $data->location_id;

                    $date = date_create(substr($arrival, 0, 19));
                    date_add($date, date_interval_create_from_date_string("-1 hours"));
                    $date = date_format($date, "Y-m-d h:m:s");

                    $date2 = date_create(substr($arrival, 0, 19));
                    date_add($date2, date_interval_create_from_date_string("1 hours"));
                    $date2 = date_format($date2, "Y-m-d h:m:s");

                    $obj->select('reservations', 'reservations.table_id', 'tables using (table_id) join locations using (location_id)', "location_id={$location_id} and status = 3 and arrival between '{$date}' and '{$date2}'");
                    $result = $obj->getResult();
                    $tmp = "";
                    $count = 0;
                    for ($i = 0; $i < sizeof($result); $i++) {
                        if ($count == 0) {
                            $tmp .= "{$result[$i]['table_id']}";
                        } else {
                            $tmp .= ", {$result[$i]['table_id']}";
                        }
                        $count++;
                    }
                    $obj->select('tables', '*', null, "table_id not in ({$tmp}) and table_id in (select table_id from tables where location_id = {$location_id})", 'table_id');
                    $result = $obj->getResult();
                    if ($result) echo json_encode([
                        'status' => 1,
                        'message' => $result
                    ]);
                    else echo json_encode([
                        'status' => 0,
                        'message' => "Don't have any tables or No Tables Available in Time"
                    ]);
            }
            exit;
        } else {
            echo json_encode([
                'status' => 999,
                'message' => 'Provided Token was Not Found' #ให้ออกจาระบบ แล้วไป login ใหม่
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'status' => 998,
            'message' => 'Invalid Data Provided' #ให้ออกจาระบบ แล้วไป login ใหม่
        ]);
        exit;
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied'
    ]);
}
// $allheaders = getallheaders();
// $jwt = $allheaders['Authorization'];

// $secret_key = "Hilal ahmad khan";
// json_decode($user_data) = JWT::decode($jwt, $secret_key, array('HS256'));