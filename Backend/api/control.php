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
        case 1: #Manager ยืนยันว่ามาตามที่จอง ต้องส่งข้อมูล res_id, res_code
            $json = array('res_id' => $data->res_id, 'role' => $user_data->role, 'res_code' => $data->res_code);
            $json = json_encode($json);
            header("location: bookingV2/booking-accept.php?json={$json}");
            break;
        case 2: #Customer ทำการยกเลิกการจอง หรือ Manager กับ Admin ทำการยกเลิกการจองนี้ ต้องส่งข้อมูล res_id
            $json = array('res_id' => $data->res_id, 'id' => $user_data->id, 'role' => $user_data->role);
            $json = json_encode($json);
            header("location: bookingV2/booking-cancel.php?json={$json}");
            break;
        case 3: #Customer ทำการจอง ต้องส่งข้อมูล table_id, arrival, cus_count, menu(Optional)
            $json = array('table_id' => $data->table_id, 'id' => $user_data->id, 'token' => $user_data->token, 'arrival' => $data->arrival, 'cus_count' => $data->cus_count, 'menu' => $data->menu);
            $json = json_encode($json);
            header("location: bookingV2/booking-create-user.php?json={$json}");
            break;
        case 4: #Customer ต้องการแก้ไขการจอง ต้องส่งข้อมูล res_id, arrival, menu(จะต้องส่งอันนี้มาก็ต่อเมื่อ Customer แก้ไขข้อมูลตัวเอง)
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
            header("location: bookingV2/booking-viewallfoodorder-user?json={$json}");
            break;
        case 7: #เรียกดูสาขาทั้งหมด
            header("location: bookingV2/booking-viewalllocation.php");
            break;
        case 8: #เรียกดูการจองทั้งหมดของคนที่ login
            $json = array('id' => $user_data->id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewbooking?json={$json}");
            break;
        case 9: #เรียกดูข้อมูลรายละเอียดการจองที่ เรา เลือก ต้องส่งข้อมูล res_id
            $json = array('id' => $user_data->id, 'res_id' => $data->res_id, 'role' => $user_data->role);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewdetailbooking-user?json={$json}");
            break;
        case 10: #เรียกดูข้อมูลรายละเอียดสาขาที่ เรา เลือก ต้องส่งข้อมูล location_id
            $json = array('location_id' => $data->location_id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewdetailselected-location?json={$json}");
            break;


        case 20: # Administrator เรียกดู user ทั้งหมด
            $json = array('role' => $user_data->role);
            $json = json_encode($json);
            header("location: admin/view_users.php?json={$json}");
            break;
        case 21: # Administrator ลบ user ทิ้ง ต้องส่งข้อมูล user_id
            $json = array('id' => $data->user_id);
            $json = json_encode($json);
            header("location: admin/view_users.php?json={$json}");
            break;
    }
}