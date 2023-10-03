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
        $ispermission = false;
        $user_data = readuserwithtoken($access_token);
        // error_log(json_encode($data));
        // error_log($user_data['user_id']);

        #ถ้าไม่มีข้อมูลใน database จะขึ้น server problem

        if (isset($user_data['user_id']) && isset($user_data['role'])) {
            $role = $user_data['role'];
            $id = $user_data['user_id'];
            switch ($type) {
                case 1: # Manager เรียกสาขาทั้งหมดที่ตัวเองดูแล
                    if ($role == "MANAGER") {
                        $obj->select('locations', "*", null, "manager_id=$id", 'status', null); #ยังไม่รู้ว่าจะแสดงยังไง `status` enum('OPERATIONAL','MAINTENANCE','OUTOFORDER')
                        $res = $obj->getResult();
                        if ($res) echo json_encode([
                            'status' => 1,
                            'message' => $res
                        ]);
                        else echo json_encode([
                            'status' => 1,
                            'message' => array() #ถ้ามันหาไม่เจอสัก row มันก็จะเข้าอันนี้
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 2: # Administrator, Manager แก้ไขข้อมูลสาขาตัวเอง
                    //ต้องส่งข้อมูล location_id, name, address, ot, ct, status  #ot = open_time, ct = close_time |||| status ส่งเป็น int {1: 'OPERATIONAL', 2: 'MAINTENANCE', 3: 'OUTOFORDER'}
                    $layout_img = null;

                    $loc_id = $data->location_id;
                    $loc_name = $obj->mysqli->real_escape_string($data->loc_name);
                    $address = $obj->mysqli->real_escape_string($data->address);
                    $open_time = $obj->mysqli->real_escape_string($data->open_time);
                    $close_time = $obj->mysqli->real_escape_string($data->close_time);
                    $status = $data->status;

                    if (isset($data->layout_img)){
                        $layout_img = $obj->mysqli->real_escape_string($data->layout_img);
                    }
                    
                    if ($role == "MANAGER" || $role == "GOD") {
                        $obj->update("locations", ['name' => $loc_name, 'address' => $address, 'open_time' => $open_time, 'close_time' => $close_time, 'status' => $status, 'layout_img_url' => $layout_img], "location_id={$loc_id}");
                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Successfully update location info',
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Failed to update location info", #ถ้ามันหาไม่เจอสัก row มันก็จะเข้าอันนี้
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 3: # Administrator, Manager ดูข้อมูล menu ของสาขาตัวเอง จะดึงข้อมูลสองอย่าง 1) menu_id ทั้งหมด, 2) menu_id ที่ห้าม; ex [[$result, $result2]]
                    //ต้องส่งข้อมูล location_id
                    if (!isset($data->l_id)) {
                        echo json_encode([
                            'status' => 0,
                            'message' => "Location ID is missing"
                        ]);
                        break;
                    }

                    $loc_id = $data->l_id;


                    if ($role == "MANAGER" || $role == "GOD") {

                        $obj->select("menus", "menu_id `m_id`, item_name `m_name`, category_id `c_id`, mc.name `c_name`, price `m_price`", "menu_category mc ON (category_id = mc.mc_id)", null, null, null);
                        $all_menu = $obj->getResult();

                        $obj->select("restrictions", "menu_id `m_id`, item_name `m_name`, category_id `c_id`, mc.name `c_name`, price `m_price`", "menus USING (menu_id) JOIN menu_category mc ON (category_id = mc.mc_id)", "location_id=$loc_id", 'menu_id', null);
                        $restricted_menu = $obj->getResult();

                        echo json_encode([
                            'status' => 1,
                            'message' => [$all_menu, $restricted_menu]
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 4: # Administrator, Manager ลบ หรือ เพิ่ม menu ที่ต้องการในสาขาที่เลือก ส่งแค่ menu_id ที่จะต้องการให้ไม่มีในสาขาเป็นรูปแบบ [1,2,3,4,5,6,7,8,9] ตัวเดียวก็ [1]
                    //ต้องส่งข้อมูล location_id, menu
                    $loc_id = $data->location_id;
                    $menu = $data->menu;

                    if ($role == "MANAGER" || $role == "GOD") {
                        $tmp = "";
                        $count = 0;
                        foreach ($menu as $menus) {
                            if ($count != sizeof($menu) - 1) {
                                $tmp .= "($loc_id,$menus), ";
                            } else {
                                $tmp .= "($loc_id,$menus)";
                            }
                            $count += 1;
                        }
                        $obj->delete('restrictions', "location_id=$loc_id");
                        $obj->insertlegacy('restrictions', 'location_id, menu_id', $tmp);

                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Menu Restricted Successfully',
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "No matching menu found!", #ถ้ามันหาไม่เจอสัก row มันก็จะเข้าอันนี้
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 5: # Administrator, Manager เพิ่มโต๊ะ
                    //ต้องส่งข้อมูล location_id, name, capacity
                    $loc_id = $data->location_id;
                    $name = $obj->mysqli->real_escape_string($data->name);
                    $capa = $data->capacity;

                    if ($role == "MANAGER" || $role == "GOD") {
                        $obj->insert("tables", ['name' => $name, 'capacity' => $capa, 'location_id' => $loc_id]);
                        $result = $obj->getResult();
                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "Add Table Successful"
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Add Table Falied Successful"
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 6: # Administrator, Manager ลบโต๊ะ ใส่ table_id มาในรูปแบบ [1, 2, 3, 4, 5] ตัวเดียวก็ [1]
                    //ต้องส่งข้อมูล table_id
                    $role = $user_data['role'];
                    $table_id = $data->table_id;

                    if ($role == "MANAGER" || $role == "GOD") {
                        $tmp = "";
                        $count = 0;
                        foreach ($table_id as $t_id) {
                            if ($count == 0) {
                                $tmp .= "$t_id";
                            } else {
                                $tmp .= ", $t_id";
                            }
                            $count++;
                        }
                        $obj->delete("tables", "table_id in ($tmp)");
                        $result = $obj->getResult();
                        if ($result[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => "Delete Table Successful"
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Delete Table Falied Successful"
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 7: # Administrator, Manager แก้ไขโต๊ะ ใส่ table_id
                    //ต้องส่งข้อมูล table_id, name, capacity
                    $role = $user_data['role'];
                    $t_id = $data->table_id;
                    $t_name = $data->t_name;
                    $t_capacity = $data->capacity;

                    if ($role == "MANAGER" || $role == "GOD") {
                        $obj->update("tables", ['name' => $t_name, 'capacity' => $t_capacity], "table_id=$t_id");

                        $res = $obj->getResult();
                        if ($res[0] == 1) echo json_encode([
                            'status' => 1,
                            'message' => 'Modify Table Successful',
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            'message' => "Modify Table Failed", #ถ้ามันหาไม่เจอสัก row มันก็จะเข้าอันนี้
                        ]);
                    } else {
                        $ispermission = !$ispermission;
                    }
                    break;
                case 8: # Administrator, Manager ดูการจองทั้งหมดใน location_id(สาขา) ที่เลือก
                    //ต้องส่งข้อมูล location_id
                    $loc_id = $data->location_id;

                    if ($role == "MANAGER" || $role == "GOD") {
                        $obj->select("reservations", "*", "users using (user_id)", "table_id in (select table_id from tables where location_id=$loc_id)", "res_id desc");
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
                case 9: #Manager เรียกดูการจองในสาขาที่ตัวเองดูแล
                    if ($role == "MANAGER") {
                        $obj->select("locations", "manager_id", null, "manager_id=" . $id, "manager_id");
                        $result = $obj->getResult();
                        $tmp = "";
                        for ($i = 0; $i < sizeof($result); $i++) {
                            if ($i == 0) {
                                $tmp .= $result[$i]['manager_id'];
                                continue;
                            }
                            $tmp .= ", " . $result[$i]['manager_id'];
                        }
                        $obj->select("reservations", "*", "tables using (table_id)", "location_id in ($tmp)");
                        $result = $obj->getResult();
                        if ($result) echo json_encode([
                            'status' => 1,
                            'message' => $result
                        ]);
                        else echo json_encode([
                            'status' => 1,
                            'message' => array()
                        ]);
                    }
                    break;
                case 10: #Administrator, Manager ต้องการดู table ทั้งหมดในสาขาที่เลิอก
                    //ต้องส่งข้อมูล location_id
                    $loc_id = $data->l_id;
                    if ($role == 'Manager' || $role == 'GOD') {
                        $obj->select("tables", "table_id, name, capacity", null, "location_id=$loc_id", 'table_id');
                        $res = $obj->getResult();
                        if ($res) echo json_encode([
                            "status" => 1,
                            "message" => $res
                        ]);
                        else echo json_encode([
                            'status' => 0,
                            "message" => array()
                        ]);
                    }else $ispermission = !$ispermission;
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
    'message' => 'Access Denied'
]);
