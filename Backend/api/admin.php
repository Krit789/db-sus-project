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
        $ispermission = false;

        #ถ้าไม่มีข้อมูลใน database จะขึ้น server problem

        if (isset($user_data['user_id'])) {
            switch ($type) {
                case 1: # Administrator เรียกดู user ทั้งหมด
                    $role = $user_data['role'];

                    if ($role == "GOD") {

                        $obj->select("users", "user_id, first_name, last_name, email, telephone, role, created_on, status", null, null, "user_id", null);
                        $result = $obj->getResult();

                        if ($result) echo json_encode([
                            'status' => 1,
                            'message' => $result
                        ]);
                        else echo json_encode([
                            'status' => 1,
                            'message' => array()
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 2: # Administrator แก้ไขสถานะ user
                    //ต้องส่งข้อมูล user_id, status เป็นตัวเลข {1: "ACTIVE", 2: "SUSPENDED"}
                    $user = $user_data['user_id'];
                    $role = $user_data['role'];
                    $status = $data->status;

                    if ($role == "GOD") {

                        $obj->update("users", ['status' => $status], "user_id={$user}");
                        $result = $obj->getResult();

                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "User Delete Successfully",
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Server Problem",
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 3: # Administrator reset password user ข้อมูล password จะอยู่ที่ message ตอนนี้
                    //ต้องส่งข้อมูล user_id
                    $user = $user_data['user_id'];
                    $role = $user_data['role'];
                    $password = randomPassword(10);
                    $new_password = password_hash($password, PASSWORD_DEFAULT);

                    if ($role == "GOD") {

                        $obj->update("users", ['password' => $new_password], "user_id={$user}");
                        $result = $obj->getResult();

                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => $passowrd,
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Server Problem",
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 4: # Administrator ต้องการเพิ่มสาขา
                    //ต้องส่งข้อมูล name, address, ot, ct #ot = open_time, ct = close_time
                    $role = $user_data['role'];
                    $name = $data->name;
                    $address = $data->address;
                    $ot = $data->open_time;
                    $ct = $data->close_time;

                    if ($role == 'GOD') {

                        $obj->insert('locations', ['name' => $name, 'address' => $address, 'open_time' => $ot, 'close_time' => $ct, 'status' => 3, 'creation_date' => $time]);
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Add Location Successful'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Add Location Failed Successful'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 5: # Administrator เรียกดู menu ทั้งหมด
                    $role = $user_data['role'];

                    if ($role == "GOD") {

                        $obj->select("menus", "*", "menu_category ON (category_id = mc_id)", null, null, null);
                        $result = $obj->getResult();

                        if ($result) echo json_encode([
                            'status' => 1,
                            'message' => $result
                        ]);
                        else echo json_encode([
                            'status' => 1,
                            'message' => array()
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 6: # Administrator ต้องการเพิ่มประเภทเมนู
                    //ต้องส่งข้อมูล name
                    $role = $user_data['role'];
                    $name = $data->name;

                    if ($role == 'GOD') {

                        $obj->insert('menu_category', ['name' => $name]);
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Add Category Menu Successful'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Add Category Menu Failed Successful'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 7: # Administrator ต้องการเพิ่มเมนู
                    //ต้องส่งข้อมูล name, price, (Optional)[desc, category_id, img_url];
                    $role = $user_data['role'];
                    $name = $data->m_name;
                    $price = $data->price;

                    $desc = null;
                    $cate_id = null;
                    $url = null;

                    if ($data->m_desc != null) {
                        $desc = $data->m_desc;
                    }
                    if ($data->m_category != null) {
                        $cate_id = $data->m_category;
                    }
                    if ($data->img_url != null) {
                        $url = $data->img_url;
                    }

                    if ($role == 'GOD') {

                        $obj->insert('menus', ['item_name' => $name, 'item_desc' => $desc, 'category_id' => $cate_id, 'price' => $price, 'img_url' => $url]);
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Add Menu Successful'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Add Menu Failed Successful'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 8: # Administrator ต้องการแก้ไขข้อมูลของเมนู
                    //ต้องส่งข้อมูล menu_id, name, price, desc, category_id, img_url;
                    $id = $data->menu_id;
                    $role = $user_data['role'];
                    $name = $data->m_name;
                    $price = $data->price;
                    $desc = $data->m_desc;
                    $cate_id = $data->m_category;
                    $url = $data->img_url;

                    if ($role == 'GOD') {

                        $obj->update('menus', ['item_name' => $name, 'item_desc' => $desc, 'category_id' => $cate_id, 'price' => $price, 'img_url' => $url], "menu_id={$id}");
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Modify Menu Successful'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Modify Menu Failed Successful'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 9: # Administrator ต้องการลบเมนู
                    //ต้องส่งข้อมูล menu_id
                    $role = $user_data['role'];
                    $id = $data->menu_id;

                    if ($role == "GOD") {

                        $obj->delete("menus", "menu_id={$id}");
                        $result = $obj->getResult();
                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "Delete Menu Successful"
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Delete Menu Falied Successful"
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 10: # Administrator กำหนดหน้าที่ของ user
                    //ต้องส่งข้อมูล user_id, role_user เป็น เลข {1: 'USER', 2:'MANAGER', 3:'GOD'}
                    $role = $user_data['role'];
                    $role_user = $data->role_user;
                    $id = $user_data['user_id'];

                    if ($role == 'GOD') {

                        $obj->update('users', ['role' => $role_user], "user_id={$id}");
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Change Role Successful'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Change Role Failed Successful'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 11: # Administrator กำหนดให้ manager ไปดูแล location
                    //ต้องส่งข้อมูล user_id, location_id
                    $role = $user_data['role'];
                    $loca_id = $data->location_id;
                    $id = $data->user_id;

                    if ($role == 'GOD') {

                        $obj->update('locations', ['manager_id' => $id], "location_id={$loca_id}");
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Add Manager to Location Successful'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Add Manager to Location Failed Successful'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 12: # Administrator เรียกดูการจองทั้งหมด
                    $role = $user_data['role'];

                    if ($role == "GOD") {

                        $obj->select("reservations", "res_id, reservations.status `res_status`, cus_count, arrival, create_time AS `res_on`, location_id `loc_id`, locations.name `loc_name`, address `loc_addr`, open_time, close_time, user_id, first_name, last_name,table_id, tables.name `table_name`", 'tables using (table_id) join users using (user_id) join locations using (location_id)', null, "arrival, status desc", null);
                        $result = $obj->getResult();

                        if ($result) echo json_encode([
                            'status' => 1,
                            'message' => $result
                        ]);
                        else echo json_encode([
                            'status' => 1,
                            'message' => array()
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 13: # Administrator เรียกดูสาขาทั้งหมด และข้อมูลของผู้จัดการ
                    //
                    $role = $user_data['role'];

                    if ($role == "GOD") {
                        $obj->select("locations", "location_id, name, address, open_time, close_time, locations.status, creation_date, users.user_id, first_name, last_name, email, telephone, role, users.status", null, null, null, null, "left outer join users on (locations.manager_id = users.user_id)");
                        $result = $obj->getResult();

                        if ($result)
                            echo json_encode([
                                'status' => 1,
                                'message' => $result
                            ]);
                        else
                            echo json_encode([
                                'status' => 1,
                                'message' => array()
                            ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                default:
                    throw new Exception('Unexpected value');
            }
            if ($ispermission) echo json_encode([
                'status' => 0,
                'message' => 'Insufficient Permission',
            ]);
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
} else echo json_encode([
    'status' => 0,
    'message' => "Access Denied"
]);
// $allheaders = getallheaders();
// $jwt = $allheaders['Authorization'];

// $secret_key = "Hilal ahmad khan";
// json_decode($user_data) = JWT::decode($jwt, $secret_key, array('HS256'));