<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include 'check.php';
$obj = new Database();
ini_set('zlib.output_compression', '1');

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
                    $sum_p = 0;
                    $role = $user_data['role'];
                    $res_code = $data->res_code;

                    if ($role == "MANAGER" || $role == "GOD") {
                        $obj->select('reservations', "res_id, user_id, ifNULL(point_used, 0) as `point_used`", null, "res_code='{$res_code}' and status = 3", null, null);
                        $result = $obj->getResult();
                        if (isset($result[0])) {

                            $obj->select('users', 'points', null, "user_id=$result[0]['user_id']");
                            $point = $obj->getResult()[0]['points'];

                            $obj->select('orders', '*', null, "res_id='{$result[0]['res_id']}'");
                            $result1 = $obj->getResult();
                            foreach ($result1 as $datas) {
                                $sum_p += $datas['item_price'] * $datas['amount'];
                            }
                            $point += floor($sum_p / 20);
                            $point -= $result[0]['point_used'];

                            $obj->update('users', ['points' => $point], "user_id={$result[0]['user_id']}");
                            $obj->update('reservations', ['status' => 1], "res_id={$result[0]['res_id']}");
                            $result = $obj->getResult();
                            if ($result[0] == 1) {
                                echo json_encode([
                                    'status' => 1,
                                    'message' => "Reseravtion Accepted",
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
                                'message' => "Code not found"
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
                        $obj->rawUpdate('users', "points = points + (SELECT point_used FROM reservations WHERE res_id=$id)", "user_id=$user");
                        $result = $obj->getResult();

                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "Cancellation Successful",
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Cancellation Failed",
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => "Insufficient Permission",
                        ]);
                    }
                    break;
                case 3: #Customer ทำการจอง
                    //ต้องส่งข้อมูล table_id, arrival, cus_count, point_used, menu(Optional)
                    $table_id = $data->table_id;
                    $user = $user_data['user_id'];
                    $arrival = $data->arrival;
                    $customer_count = $data->cus_count;
                    $point_u = null;
                    $insertion_row = ['table_id' => $table_id, 'user_id' => $user, 'arrival' => $arrival, 'status' => 3, 'cus_count' => $customer_count, 'res_code' => randomCode(8), 'create_time' => $time];
                    if (isset($data->point_used)){
                        $insertion_row['point_used'] = $data->point_used;
                    }

                    $obj->insert('reservations', $insertion_row);
                    $result = $obj->getResult();
                    if ($result[0] == 1) {

                        if (isset($data->menu[0])) { #ถ้ามี menu มาให้ทำอันนี้ menu ต้องเป็น array[2]: array[0]=>menu_id, array[1]=>amount ex.[[1, 2], [9, 2]]
                            $tmp = "";
                            if (isset($data->point_used)){ // Reduce User Point upon reservation completion
                                $obj->rawUpdate('users', "points = points - $data->point_used", "user_id=$user");
                                $result = $obj->getResult();
                                error_log(json_encode($result));
                            }
                            $obj->select('reservations', 'res_id', null, "table_id=$table_id and user_id=$user", 'res_id desc', 1);
                            $result = $obj->getResult();
                            $res_id = $result[0]['res_id'];

                            foreach ($data->menu as $menu) {
                                //[0] menu_id [1] จำนวน
                                $obj->select('menus', 'price', null, "menu_id={$menu->id}");
                                $price = $obj->getResult()[0]['price'];

                                if ($menu == $data->menu[sizeof($data->menu) - 1]) {
                                    $tmp .= "($res_id, $menu->id, $menu->amount, $price)";
                                } else {
                                    $tmp .= "($res_id, $menu->id, $menu->amount, $price),";
                                }
                            }
                            // error_log($tmp);
                            $obj->insertlegacy('orders', 'res_id, menu_id, amount, item_price', $tmp);
                            # ต้องเช็คว่าเข้าไปไหมด้วย ??? หรือป่าว? ??

                            $result = $obj->getResult();
                            // if ($result[0] == 1){
                            # Nothing just hanging around. เอาไว้เช็คว่าเข้าไหม ไม่ใช้
                            // }

                        }

                        echo json_encode([
                            'status' => 1,
                            'message' => "Booking Added Successfully",
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => "Server Problem",
                        ]);
                    }
                    break;
                case 4: #Customer ต้องการแก้ไขการจอง
                    //ต้องส่งข้อมูล res_id, table_id, arrival, cus_count, point_used, menu(จะต้องส่งอันนี้มาก็ต่อเมื่อ Customer แก้ไขข้อมูลตัวเอง)
                    $user = $user_data['user_id'];
                    $res_id = $data->res_id;
                    $table_id = $data->table_id;
                    $arrival = $data->arrival;
                    $customer_count = $data->cus_count;
                    $point_u = $data->point_used;

                    $date = date_create(substr($arrival, 0, 19));
                    date_add($date, date_interval_create_from_date_string("-1 hours"));
                    $date = date_format($date, "Y-m-d h:m:s");

                    $date2 = date_create(substr($arrival, 0, 19));
                    date_add($date2, date_interval_create_from_date_string("1 hours"));
                    $date2 = date_format($date2, "Y-m-d h:m:s");

                    $obj->select("reservations", "res_id", null, "table_id={$table_id} and status = 3 and arrival between '{$date}' and '{$date2}' and user_id != {$user}");
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
                                $obj->insertlegacy('orders', 'res_id, menu_id, amount', $tmp);

                                $result = $obj->getResult(); #อยากเช็คแต่ยังก่อน
                            }
                            $obj->update('reservations', ['arrival' => $arrival, 'table_id' => $table_id, 'cus_count' => $customer_count], "res_id='{$res_id}'");
                            if ($point_u != NULL) $obj->update('reservations', ['point_used' => $point_u], "res_id='{$res_id}'");
                            $result = $obj->getResult();
                            if ($result[0] == 1) {
                                echo json_encode([
                                    'status' => 1,
                                    'message' => 'Reservation Modified Successfully'
                                ]);
                            } else {
                                echo json_encode([
                                    'status' => 0,
                                    'message' => 'Reservation Modification Failed'
                                ]);
                            }
                        } else {
                            echo json_encode([
                                'status' => 0,
                                'message' => 'Customer ID mismatch',
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => 'This time slot is already taken'
                        ]);
                    }
                    break;
                case 5: #เรียกดูอาหารของ location_id ที่ส่งมา ทั้งหมด
                    //ต้องส่งข้อมูล location_id
                    $id = $data->location_id;

                    $obj->selectAndJoin('menus', "menu_id as `id`, item_name, item_desc, mc_id, price, img_url, name `mc_name`", "menu_category mc on (menus.category_id = mc.mc_id)", null, "menu_id not in (select menu_id from restrictions where location_id = {$id})", null, null);
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
                    $obj->select('orders', "menu_id `m_id`, item_name `m_name`, price `m_price`, amount `m_amount`", "menus using (menu_id)", "res_id={$id}", null, null);
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
                case 7: # Customer ต้องการเรียกดูสาขาทั้งหมด เอาแค่สาขาที่ OPERATIONAL
                    $obj->select('locations', "location_id, name, address, open_time, close_time, status, layout_img_url", null, "status=1", "status", null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
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
                    $time_range = '';
                    if (isset($data->range)) {
                        $range = $data->range; // {'all', 'previous', 'upcoming'}
                        if ($range == 1) { // Previous
                            $time_range = "arrival < '" . date('Y-m-d H:i:s') . "'";
                        } else if ($range == 2) { // Upcoming
                            $time_range = "arrival >= '" . date('Y-m-d H:i:s') . "'";
                        } else {
                            $time_range = ''; // or any other default value
                        }
                    }
                    $obj->select('reservations', "res_id, arrival, reservations.status as `res_status`, cus_count, res_code, point_used, tables.name as `table_name`, tables.capacity as `table_capacity`, locations.name as `location_name`, locations.address as `location_address`, locations.status as `location_status`", "tables using (table_id) join locations using (location_id)", "user_id={$id} " . ($time_range ? 'AND ' . $time_range : ''), 'res_id DESC', null);
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

                    $obj->select('locations', "location_id, name, address, open_time, close_time, status, layout_img_url", null, "location_id = {$id}", null, null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
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

                    $check_close = date_create(substr($arrival, 0, 19));
                    date_add($check_close, date_interval_create_from_date_string("30 minutes"));
                    $check_close = date_create(date_format($check_close, "H:i:s"));

                    $check_open = date_create(substr($arrival, 0, 19));
                    $check_open = date_create(date_format($check_open, "H:i:s"));

                    $obj->select("locations", 'open_time, close_time', null, "location_id={$location_id}");
                    $result = $obj->getResult();

                    $ot = date_create($result[0]['open_time']);
                    $ot = date_create(date_format($ot, "H:i:s"));
                    $ct = date_create($result[0]['close_time']);
                    $ct = date_create(date_format($ct, "H:i:s"));

                    if ($check_open >= $ot && $check_close <= $ct) {

                        $date = date_create(substr($arrival, 0, 19));
                        date_add($date, date_interval_create_from_date_string("-1 hours"));
                        $date = date_format($date, "Y-m-d H:i:s");

                        $date2 = date_create(substr($arrival, 0, 19));
                        date_add($date2, date_interval_create_from_date_string("1 hours"));
                        $date2 = date_format($date2, "Y-m-d H:i:s");

                        $obj->select('reservations', 'reservations.table_id', 'tables using (table_id) join locations using (location_id)', "location_id={$location_id} and reservations.status = 3 and arrival between '{$date}' and '{$date2}'");
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
                        if ($tmp != "") {
                            $tmp = "table_id not in ({$tmp}) and ";
                        }
                        $obj->select('tables', '*', null, "{$tmp} table_id in (select table_id from tables where location_id = {$location_id})", 'table_id');
                        $result = $obj->getResult();
                        if ($result) echo json_encode([
                            'status' => 1,
                            'message' => $result
                        ], JSON_NUMERIC_CHECK);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "No table available on location or no tables available on selected time"
                        ], JSON_NUMERIC_CHECK);
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => "Unavailable Time"
                        ], JSON_NUMERIC_CHECK);
                    }
                    break;
                case 12: #เรียกข้อมูลของตัวเอง
                    $id = $user_data['user_id'];
                    $obj->select('users', "user_id `id`, first_name, last_name, email, telephone, points", null, "user_id=$id", null, 1);
                    $res = $obj->getResult();
                    if ($res) {
                        echo json_encode([
                            'status' => 1,
                            'message' => $res[0],
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 1,
                            'message' => array()
                        ]);
                    }
                    break;
                case 13: # Update User Data
                    $fn = $data->first_name;
                    $ln = $data->last_name;
                    $email = $data->email;
                    $tele = $data->tel_num;
                    $password = $data->pswd;
                    $id = $user_data['user_id'];

                    if (password_verify($password, $user_data['password_hash'])) {
                        if ($user_data['email'] != $email) {
                            $obj->select("users", "email", null, "email='{$email}'", null, 1); // Check for duplicate email
                            $is_email = $obj->getResult();
                            if (isset($is_email[0]['email']) == $email) {
                                echo json_encode([
                                    'status' => 2,
                                    'message' => 'Email already Exists',
                                ]);
                            }
                        } else {
                            $obj->update('users', ['first_name' => $fn, 'last_name' => $ln, 'telephone' => $tele, 'email' => $email], "user_id={$id}");
                            $result = $obj->getResult();
                            if ($result[0] == 1) {
                                echo json_encode([
                                    'status' => 1,
                                    'message' => "User Updated Successfully",
                                ]);
                            } else {
                                echo json_encode([
                                    'status' => 0,
                                    'message' => "Failed to update this user",
                                ]);
                            }
                        }
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => 'Invalid Password, Unable to update user data.',
                        ]);
                    }
                    break;
                case 14: # Update User Password
                    $password = $data->pswd;
                    $new_password = $data->new_pswd;
                    $id = $user_data['user_id'];

                    if (password_verify($password, $user_data['password_hash'])) {
                        $obj->update('users', ['password_hash' => password_hash($new_password, PASSWORD_DEFAULT)], "user_id={$id}");
                        $result = $obj->getResult();
                        if ($result[0] == 1) {
                            echo json_encode([
                                'status' => 1,
                                'message' => "User Password Changed Successfully",
                            ]);
                        } else {
                            echo json_encode([
                                'status' => 0,
                                'message' => "Failed to change this user password",
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => 'Invalid Password, Unable to update user data.',
                        ]);
                    }
                    break;
                case 15: #เรียกดูการจองทั้งหมดของเจ้าของ Account
                    $id = $user_data['user_id'];
                    $obj->select('reservations', "status", null, "user_id={$id}", 'res_id DESC', null, null);
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
                case 16:
                    $id = $user_data['user_id'];
                    $token = randomCode(32);
                    $obj->update('users', ['access_token' => $token], "user_id=$id");
                    $result = $obj->getResult();
                    if ($result[0] == 1) echo json_encode([
                        'status' => 1,
                        'message' => 'Token Reset Successful'
                    ]);
                    else echo json_encode([
                        'status' => 0,
                        'message' => 'Tonken Reset Failed'
                    ]);
                    break;
            }
            exit;
        } else {
            echo json_encode([
                'status' => 999,
                'message' => 'Provided Token was Not Found' #ให้ออกจาระบบ แล้วไป login ใหม่
            ], JSON_NUMERIC_CHECK);
            exit;
        }
    } else {
        // error_log($data);
        echo json_encode([
            'status' => 998,
            'message' => 'Invalid Data Provided' #ให้ออกจาระบบ แล้วไป login ใหม่
        ], JSON_NUMERIC_CHECK);
        exit;
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied'
    ], JSON_NUMERIC_CHECK);
}
