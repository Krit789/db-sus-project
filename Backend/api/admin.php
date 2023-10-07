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

        if (isset($user_data['user_id']) && isset($user_data['role'])) {
            $role = $user_data['role'];
            switch ($type) {
                case 1: # Administrator เรียกดู user ทั้งหมด
                    if ($role == "GOD") {

                        $obj->select("users", "user_id, first_name, last_name, email, telephone, role, created_on, status", null, null, "role, user_id", null);
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
                    $user = $data->u_id;
                    $obj->select('users', 'role', null, "user_id={$user}");
                    $user_role = $obj->getResult()[0]['role'];
                    $status = $data->u_status;

                    if ($user == $user_data['user_id']) {
                        echo json_encode([
                            'status' => 0,
                            'message' => "You can't " . (($status == 1) ? "activate" : "suspend") . " yourself!",
                        ]);
                        break;
                    }

                    if ($role == "GOD") {
                        $obj->update("users", ['status' => $status], "user_id={$user}");
                        $result = $obj->getResult();


                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "Account " . (($status == 1) ? "activated" : "suspended") . " successfully",
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Unable to " . (($status == 1) ? "activated" : "suspended") . " this account",
                        ]);
                        if ($user_role == "MANAGER" && $status == 2) {
                            $obj->update('locations', ['manager_id' => null], "manager_id={$user}");
                        }
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 3: # Administrator reset password user ข้อมูล password จะอยู่ที่ message ตอนนี้
                    //ต้องส่งข้อมูล user_id
                    $user = $data->u_id;

                    if ($user == $user_data['user_id']) {
                        echo json_encode([
                            'status' => 0,
                            'message' => "You can't reset your own password! You can change your password in account page",
                        ]);
                        break;
                    }

                    if ($role == "GOD") {
                        $password = randomPassword(10);
                        $new_password = password_hash($password, PASSWORD_DEFAULT);
                        $obj->update("users", ['password_hash' => $new_password], "user_id={$user}");
                        $result = $obj->getResult();

                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => $password,
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Unable to reset password for this user",
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 4: # Administrator ต้องการเพิ่มสาขา
                    //ต้องส่งข้อมูล name, address, ot, ct #ot = open_time, ct = close_time

                    $layout_img = null;

                    $name = $obj->mysqli->real_escape_string($data->name);
                    $address = $obj->mysqli->real_escape_string($data->address);
                    $ot = $obj->mysqli->real_escape_string($data->open_time);
                    $ct = $obj->mysqli->real_escape_string($data->close_time);

                    if (isset($data->layout_img)) {
                        $layout_img = $obj->mysqli->real_escape_string($data->layout_img);
                    }

                    if ($role == 'GOD') {
                        $insertion_row = ['name' => $name, 'address' => $address, 'open_time' => $ot, 'close_time' => $ct, 'status' => 3, 'creation_date' => $time];
                        if (isset($data->layout_img)) {
                            if (!is_null($data->layout_img)) {
                                $insertion_row['layout_img_url'] = $layout_img;
                            }
                        }

                        $obj->insert('locations', $insertion_row);
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Location Added Successfully'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Failed to add new location'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 5: # Administrator เรียกดู menu ทั้งหมด
                    if ($role == "GOD") {

                        $obj->selectAndJoin("menus", "menu_id `m_id`, item_name `m_name`, item_desc `m_desc`, price `m_price`, img_url `m_img`, category_id `c_id`, menu_category.name `c_name`", "menu_category ON (category_id = mc_id)", null, null, null, null);
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
                    $name = $obj->mysqli->real_escape_string($data->c_name);

                    if ($role == 'GOD') {

                        $obj->insert('menu_category', ['name' => $name]);
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Menu Category added Successfully'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Failed to add Menu Category'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 7: # Administrator ต้องการเพิ่มเมนู
                    //ต้องส่งข้อมูล name, price, (Optional)[desc, category_id, img_url];
                    $name = $obj->mysqli->real_escape_string($data->m_name);
                    $price = $data->price;

                    $desc = null;
                    $cate_id = null;
                    $url = null;

                    if (isset($data->m_desc)) {
                        $desc = $obj->mysqli->real_escape_string($data->m_desc);
                    }
                    if (isset($data->m_category)) {
                        $cate_id = $data->m_category;
                    }
                    if (isset($data->img_url)) {
                        $url = $obj->mysqli->real_escape_string($data->img_url);
                    }

                    if ($role == 'GOD') {
                        $insertion_row = ['item_name' => $name, 'price' => $price];
                        if (isset($data->m_desc)) {
                            $insertion_row['item_desc'] = $desc;
                        }

                        if (isset($data->m_category)) {
                            if ($data->m_category != 0) {
                                $insertion_row['category_id'] = $cate_id;
                            }
                        }

                        if (isset($data->img_url)) {
                            $insertion_row['img_url'] = $url;
                        }
                        $obj->insert('menus', $insertion_row);
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Menu Added Successfully'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Failed to add menu'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 8: # Administrator ต้องการแก้ไขข้อมูลของเมนู
                    //ต้องส่งข้อมูล menu_id, name, price, desc, category_id, img_url;
                    $id = $data->menu_id;
                    $name = $obj->mysqli->real_escape_string($data->m_name);
                    $price = $data->price;
                    $desc = null;
                    $cate_id = null;
                    $url = null;
                    if (isset($data->m_desc)) {
                        $desc = $obj->mysqli->real_escape_string($data->m_desc);
                    }
                    if (isset($data->m_category)) {
                        $cate_id = $data->m_category;
                    }
                    if (isset($data->img_url)) {
                        $url = $obj->mysqli->real_escape_string($data->img_url);
                    }

                    if ($role == 'GOD') {
                        $insertion_row = ['item_name' => $name, 'price' => $price];
                        if (isset($data->m_desc)) {
                            $insertion_row['item_desc'] = $desc;
                        }

                        if (isset($data->m_category)) {
                            if ($cate_id == 0) {
                                $insertion_row['category_id'] = NULL;
                            } else {
                                $insertion_row['category_id'] = $cate_id; // Set category_id to $cate
                            }
                        }

                        if (isset($data->img_url)) {
                            $insertion_row['img_url'] = $url;
                        }

                        $obj->update('menus', $insertion_row, "menu_id={$id}");
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Menu updated successfully!'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Failed to update this menu'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 9: # Administrator ต้องการลบเมนู
                    //ต้องส่งข้อมูล menu_id
                    $id = $data->menu_id;

                    if ($role == "GOD") {

                        $obj->delete("menus", "menu_id={$id}");
                        $result = $obj->getResult();
                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "Menu deleted successfully"
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Unable to delete this menu"
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 10: # Administrator กำหนด role ของ user
                    //ต้องส่งข้อมูล user_id, role_user เป็น เลข {1: 'USER', 2:'MANAGER', 3:'GOD'}
                    $new_role = $data->u_role;
                    $old_role = $data->ou_role;
                    $id = $data->u_id;

                    if ($id == $user_data['user_id']) {
                        echo json_encode([
                            'status' => 0,
                            'message' => 'You can\'t change your own role!'
                        ]);
                        break;
                    }

                    if ($role == 'GOD') {
                        $obj->update('users', ['role' => $new_role], "user_id={$id}");
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Role Changed Successfully'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Failed to change user role'
                        ]);
                        if ($old_role == 'MANAGER' && $new_role != $old_role) {
                            $obj->update('locations', ['manager_id' => null], "manager_id={$id}");
                        }
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 11: # Administrator กำหนดให้ manager ไปดูแล location
                    //ต้องส่งข้อมูล user_id, location_id
                    $u_id = null;
                    $loca_id = $data->l_id;
                    if ($data->u_id != 0) {
                        $u_id = $data->u_id;
                    }

                    if ($role == 'GOD') {
                        $obj->update('locations', ['manager_id' => $u_id], "location_id={$loca_id}");
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Manager Successfully Assigned to Location ID: ' . $loca_id . ' .'
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Manager Assignment Failed'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 12: # Administrator เรียกดูการจองทั้งหมด
                    if ($role == "GOD") {
                        $obj->select("reservations", "res_id, reservations.status `res_status`, cus_count, arrival, create_time AS `res_on`, location_id `loc_id`, locations.name `loc_name`, address `loc_addr`, open_time, close_time, user_id, first_name, last_name,table_id, tables.name `table_name`", 'tables using (table_id) join users using (user_id) join locations using (location_id)', null, "reservations.status desc, arrival", null);
                        $result = $obj->getResult();

                        if ($result) echo json_encode([
                            'status' => 1,
                            'message' => $result
                        ]);
                        else echo json_encode([
                            'status' => 1,
                            'message' => array()
                        ]);
                    } else if ($role == "MANAGER") {
                        $u_id = $user_data['user_id'];
                        $obj->select("reservations", "res_id, reservations.status `res_status`, cus_count, arrival, create_time AS `res_on`, location_id `loc_id`, locations.name `loc_name`, address `loc_addr`, open_time, close_time, user_id, first_name, last_name,table_id, tables.name `table_name`", 'tables using (table_id) join users using (user_id) join locations using (location_id)', "location_id IN (SELECT location_id FROM locations WHERE manager_id=$u_id)", "reservations.status desc, arrival", null);
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
                case 14: # Administrator แก้ไขประเภทเมนู
                    //ต้องส่งข้อมูล mc_id และ name
                    $cate_id = $data->c_id;
                    $new_name = $obj->mysqli->real_escape_string($data->c_name);

                    if ($role == 'GOD') {
                        $obj->update('menu_category', ['name' => $new_name], "mc_id={$cate_id}");
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Menu Category successfully renamed to \'' . $c_name . '\''
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => 'Failed to rename menu category'
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 15: # Administrator ลบประเภทเมนู
                    //ต้องส่งข้อมูล mc_id
                    $cate_id = $data->c_id;

                    if ($role == 'GOD') {

                        $obj->delete("menu_category", "mc_id={$cate_id}");
                        $result = $obj->getResult();
                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "Menu Category deleted successfully"
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Unable to delete this menu category"
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 16: # Administrator เรียกดูประเภทเมนูทั้งหมด
                    //ต้องส่งข้อมูล mc_id
                    if ($role == 'GOD') {
                        $obj->select("menu_category", "mc_id `c_id`, name `c_name`", null, null, null, null, null);
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
                case 17: # Administrator เรียกดูสาขาทั้งหมด รวมทุกสถานะ
                    if ($role == 'GOD') {
                        $obj->selectAndJoin('locations', "location_id `l_id`, name `l_name`, address `l_addr`, open_time `l_open_time`, close_time `l_close_time`, locations.status `l_status`, layout_img_url `l_layout_img`, manager_id `l_mgr_id`, first_name `mgr_fn`, last_name `mgr_ln`, telephone `mgr_tel`, email `mgr_email`", "users ON (users.user_id = locations.manager_id)", null, null, "locations.status", null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
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
                    } else if ($role == 'MANAGER') {
                        $u_id = $user_data['user_id'];
                        $obj->selectAndJoin('locations', "location_id `l_id`, name `l_name`, address `l_addr`, open_time `l_open_time`, close_time `l_close_time`, locations.status `l_status`, layout_img_url `l_layout_img`, manager_id `l_mgr_id`, first_name `mgr_fn`, last_name `mgr_ln`, telephone `mgr_tel`, email `mgr_email`", "users ON (users.user_id = locations.manager_id)", null, "manager_id=$u_id", "locations.status", null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
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
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 18: # Administrator เรียกดู user ประเภท MANAGER ทั้งหมดที่มีสถานะเป็น ACTIVE
                    if ($role == 'GOD') {
                        $obj->select("users", "user_id `u_id`, CONCAT(first_name, ' ', last_name) `u_name`, email `u_email`", null, "role=2 AND status=1", null, null, null);
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
                case 19: # Administrator เรียกดู locations ที่ MANAGER เป็นคนดูแล (ต้องส่ง u_id ของ manager มา)
                    $mgr_id = $data->mgr_id;

                    if ($role == 'GOD') {
                        $obj->select("locations", "location_id `l_id`, name `l_name`", null, "manager_id={$mgr_id}", null, null, null);
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
                case 20: # Administrator ลบสาขาทิ้ง
                    //ต้องส่งข้อมูล l_id
                    $loc_id = $data->l_id;

                    if ($role == 'GOD') {

                        $obj->delete("locations", "location_id=$loc_id");
                        $result = $obj->getResult();
                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "Location deleted successfully"
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Unable to delete this location"
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 21: # Administrator, Manager Report ทุกสาขา
                    //start, end, (Administrator Optional) loc_id ส่งแบบ [1, 2, 3, 4]
                    $start = substr($data->start, 0, 19);
                    $end = substr($data->end, 0, 19);

                    if (!$start){$start = '0000-00-00 00:00:00';}
                    if (!$end){$end = '9999-12-31 23:59:59';}

                    $message = "You don't Specify Location";
                    $tmp = "";
                    if (isset($data->loc_id)) {
                        foreach ($data->loc_id as $i) {
                            $tmp .= "{$i},";
                        }
                    }

                    $tmp = substr($tmp, 0, -1);

                    if ($role == 'GOD' && $tmp == "") {
                        $obj->select("locations", "location_id, locations.name as `l_name`, locations.address as `l_address`, users.first_name as `manager_fname`, users.last_name as `manager_lname`, res_id, arrival, sum(ifNULL(tmp.sum_price, 0)) as `balance_paid`, count(tmp.menu_id) as `menu_amount`", null, null, 'location_id, arrival', null, "left outer join (select location_id, res_id, arrival, menu_id, (menus.price*orders.amount) as `sum_price` from reservations join orders using (res_id) join tables using (table_id) join menus using (menu_id) right outer join locations using (location_id) where reservations.status = 1) as `tmp` using (location_id) join users on (users.user_id = locations.manager_id)", 'location_id, res_id');
                        $res = $obj->getResult();
                        $obj->select("locations", "location_id, locations.name as `l_name`, locations.address as `l_address`, users.first_name as `manager_fname`, users.last_name as `manager_lname`, sum(ifNULL(tmp.sum_price, 0)) as `total_earning`, count(distinct tmp.res_id) as `reservation_amount`", null, null, 'location_id', null, "left outer join (select location_id, res_id, arrival, menu_id, (menus.price*orders.amount) as `sum_price` from reservations join orders using (res_id) join tables using (table_id) join menus using (menu_id) right outer join locations using (location_id) where reservations.status = 1) as `tmp` using (location_id) join users on (users.user_id = locations.manager_id)", 'location_id');
                        $res2 = $obj->getResult();
                        //$res บอกแต่ละ reservation ว่าทำเงินได้เท่าไร
                        //$res2 รวม reservation ทั้งหมดในสาขา และบอกจำนวนเงินที่ทำได้ใน สาขา นั้น 
                        echo json_encode([
                            'status' => 1,
                            'message' => [$res, $res2]
                        ]);
                    } else if ($role == 'MANAGER' || ($role == "GOD" && $tmp != "")) {
                        $u_loc = array();
                        $obj->select("locations", 'location_id', null, "manager_id={$user_data['user_id']}");
                        foreach ($obj->getResult() as $i) array_push($u_loc, $i['location_id']);
                        if (isset($data->loc_id)) {
                            foreach ($data->loc_id as $i) {
                                if ($role == "GOD") break;
                                if (!in_array($i, $u_loc)) {
                                    $tmp = "";
                                    $message = "You don't have permission to create report in these locations";
                                    break;
                                }
                            }
                        }


                        if ($tmp != "") {
                            $obj->select("locations", "location_id, locations.name as `l_name`, locations.address as `l_address`, users.first_name as `manager_fname`, users.last_name as `manager_lname`, res_id, arrival, sum(ifNULL(tmp.sum_price, 0)) as `balance_paid`, count(tmp.menu_id) as `menu_amount`", null, "location_id in ($tmp)", 'location_id, arrival', null, "left outer join (select location_id, res_id, arrival, menu_id, (menus.price*orders.amount) as `sum_price` from reservations join orders using (res_id) join tables using (table_id) join menus using (menu_id) right outer join locations using (location_id) where reservations.status = 1 and reservations.arrival between '$start' and '$end') as `tmp` using (location_id) join users on (users.user_id = locations.manager_id)", 'location_id, res_id');
                            $res = $obj->getResult();
                            $obj->select("locations", "location_id, locations.name as `l_name`, locations.address as `l_address`, users.first_name as `manager_fname`, users.last_name as `manager_lname`, sum(ifNULL(tmp.sum_price, 0)) as `total_earning`, count(distinct tmp.res_id) as `reservation_amount`", null, "location_id in ($tmp)", 'location_id', null, "left outer join (select location_id, res_id, arrival, menu_id, (menus.price*orders.amount) as `sum_price` from reservations join orders using (res_id) join tables using (table_id) join menus using (menu_id) right outer join locations using (location_id) where reservations.status = 1 and reservations.arrival between '$start' and '$end') as `tmp` using (location_id) join users on (users.user_id = locations.manager_id)", 'location_id');
                            $res2 = $obj->getResult();
                            //$res บอกแต่ละ reservation ว่าทำเงินได้เท่าไร
                            //$res2 รวม reservation ทั้งหมดในสาขา และบอกจำนวนเงินที่ทำได้ใน สาขา นั้น 
                            echo json_encode([
                                'status' => 1,
                                'message' => [$res, $res2]
                            ]);
                        } else {
                            echo json_encode([
                                'status' => 0,
                                'message' => $message
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => "You don't have permission to create report"
                        ]);
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
