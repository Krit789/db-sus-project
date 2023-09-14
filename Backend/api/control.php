<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include 'database/Database.php';

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $control = $data->control;

    // $allheaders = getallheaders();
    // $jwt = $allheaders['Authorization'];

    // $secret_key = "Hilal ahmad khan";
    // $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
    // $user_data = $user_data->data;

    #ถ้าไม่มีข้อมูลใน database จะขึ้น server problem

    switch ($control) {
        case 1: #Manager ยืนยันว่ามาตามที่จอง 
            //ต้องส่งข้อมูล res_id, res_code
            $json = array('res_id' => $data->res_id, 'role' => $user_data->role, 'res_code' => $data->res_code);
            $json = json_encode($json);
            header("location: bookingV2/booking-accept.php?json={$json}");
            break;
        case 2: #Customer ทำการยกเลิกการจอง หรือ Manager กับ Admin ทำการยกเลิกการจองนี้ 
            //ต้องส่งข้อมูล res_id
            $json = array('res_id' => $data->res_id, 'id' => $user_data->id, 'role' => $user_data->role);
            $json = json_encode($json);
            header("location: bookingV2/booking-cancel.php?json={$json}");
            break;
        case 3: #Customer ทำการจอง 
            //ต้องส่งข้อมูล table_id, arrival, cus_count, menu(Optional)
            $json = array('table_id' => $data->table_id, 'id' => $user_data->id, 'token' => $user_data->token, 'arrival' => $data->arrival, 'cus_count' => $data->cus_count, 'menu' => $data->menu);
            $json = json_encode($json);
            header("location: bookingV2/booking-create-user.php?json={$json}");
            break;
        case 4: #Customer ต้องการแก้ไขการจอง 
            //ต้องส่งข้อมูล res_id, arrival, menu(จะต้องส่งอันนี้มาก็ต่อเมื่อ Customer แก้ไขข้อมูลตัวเอง)
            $json = array('id' => $user_data->id, 'res_id' => $data->res_id, 'arrival' => $data->arrival, 'menu' => $data->menu);
            $json = json_encode($json);
            header("location: bookingV2/booking-modify-user.php?json={$json}");
            break;
        case 5: #เรียกดูอาหารของ location_id ที่ส่งมา ทั้งหมด
            $json = array('location_id' => $data->location_id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewallfood-location.php?json={$json}");
            break;
        case 6: #เรียกดูอาหารการจองล่วงหน้าด้วย res_id
            $json = array('res_id' => $data->res_id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewallfoodorder-user.php?json={$json}");
            break;
        case 7: # Customer, Administrator ต้องการเรียกดูสาขาทั้งหมด 
            header("location: bookingV2/booking-viewalllocation.php");
            break;
        case 8: #เรียกดูการจองทั้งหมดของเจ้าของ Account
            $json = array('id' => $user_data->id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewbooking.php?json={$json}");
            break;
        case 9: # Customer, Manger, Administrator เรียกดูข้อมูลรายละเอียดการจองที่ เรา เลือก 
            //ต้องส่งข้อมูล res_id
            $json = array('id' => $user_data->id, 'res_id' => $data->res_id, 'role' => $user_data->role);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewdetailbooking-user.php?json={$json}");
            break;
        case 10: #เรียกดูข้อมูลรายละเอียดสาขาที่ เรา เลือก 
            //ต้องส่งข้อมูล location_id
            $json = array('location_id' => $data->location_id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewdetailselected-location.php?json={$json}");
            break;


        case 20: # Administrator เรียกดู user ทั้งหมด
            $json = array('role' => $user_data->role);
            $json = json_encode($json);
            header("location: admin/view_users.php?json={$json}");
            break;
        case 21: # Administrator ลบ user ทิ้ง 
            //ต้องส่งข้อมูล user_id
            $json = array('id' => $data->user_id);
            $json = json_encode($json);
            header("location: admin/delete_user.php?json={$json}");
            break;
        case 22: # Administrator reset password user ข้อมูล password จะอยู่ที่ message ตอนนี้
            //ต้องส่งข้อมูล user_id
            $json = array('id' => $data->user_id);
            $json = json_encode($json);
            header("location: admin/reset_password.php?json={$json}");
            break;


        case 31: # Manager เรียกสาขาทั้งหมดที่ตัวเองดูแล ||||||| Administrator เรียก control ที่ 7
            $json = array('manager_id' => $user_data->user_id, 'role' => $user_data->role);
            $json = json_encode($json);
            header("location: manage/view_location_manager.php?json={$json}");
            break;
        case 32: # Administrator, Manager แก้ไขข้อมูลสาขาตัวเอง
            //ต้องส่งข้อมูล location_id, name, address, ot, ct, status
            $json = array('location_id' => $data->location_id, 'name' => $data->name, 'address' => $data->address, 'open_time' => $data->ot, 'close_time' => $data->ct, 'status' => $data->status, 'role' => $user_data->role);
            $json = json_encode($json);
            header("location: manage/modify_location.php?json={$json}");
            break;
        case 33: # Administrator, Manager ดูข้อมูล menu ของสาขาตัวเอง จะดึงข้อมูลสองอย่าง 1) menu_id ทั้งหมด, 2) menu_id ที่ห้าม; ex [[$result, $result2]]
            //ต้องส่งข้อมูล location_id
            $json = array('location_id' => $data->location_id, 'role' => $user_data->role);
            $json = json_encode($json);
            header("location: manage/view_menu.php?json={$json}");
            break;
        case 34: # Administrator, Manager ลบ หรือ เพิ่ม menu ที่ต้องการในสาขาที่เลือก ส่งแค่ menu_id ที่จะต้องการให้ไม่มีในสาขาเป็นรูปแบบ [1,2,3,4,5,6,7,8,9] ตัวเดียวก็ [1]
            //ต้องส่งข้อมูล location_id, menu
            $json = array('location_id' => $data->location_id, 'role' => $user_data->role, 'menu' => $data->menu);
            $json = json_encode($json);
            header("location: manage/modify_menu.php?json={$json}");
            break;
        case 35: # Administrator, Manager เพิ่มโต๊ะ
            //ต้องส่งข้อมูล location_id, name, capacity
            $json = array('role' => $user_data->role, 'name' => $data->name, 'capacity' => $data->capacity, 'location_id' => $data->location_id);
            $json = json_encode($json);
            header("location: manage/add_table.php?json={$json}");
            break;
        case 36: # Administrator, Manager ลบโต๊ะ ใส่ table_id มาในรูปแบบ [1, 2, 3, 4, 5] ตัวเดียวก็ [1]
            //ต้องส่งข้อมูล table_id
            $json = array('role' => $user_data->role, 'table_id' => $data->table_id);
            $json = json_encode($json);
            header("location: manage/delete_table.php?json={$json}");
            break;
        case 37: # Administrator, Manager แก้ไขโต๊ะ ใส่ table_id
            //ต้องส่งข้อมูล table_id, name, capacity
            $json = array('role' => $user_data->role, 'table_id' => $data->table_id, 'name' => $data->name, 'capacity' => $data->capacity);
            $json = json_encode($json);
            header("location: manage/modify_table.php?json={$json}");
            break;
    }
}