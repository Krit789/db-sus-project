<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include 'database/Database.php';

use Firebase\JWT\JWT;

if ($_SERVER["REQUEST_METHOD"] == 'POST'){
    $data = json_decode(file_get_contents("php://input"));
    $control = $data->control;

    // $allheaders = getallheaders();
    // $jwt = $allheaders['Authorization'];

    // $secret_key = "Hilal ahmad khan";
    // $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
    // $user_data = $user_data->data;

    switch ($control){
        case 1:
            $json = array('res_id'=>$data->res_id, 'role'=>$user_data->role);
            $json = json_encode($json);
            header("location: bookingV2/booking-accept.php?json={$json}");
            break;
        case 2:
            $json = array('res_id'=>$data->res_id, 'role'=>$user_data->id);
            $json = json_encode($json);
            header("location: bookingV2/booking-cancel-user.php?json={$json}");
            break;
        case 3:
            $json = array('table_id'=>$data->table_id, 'id'=>$user_data->id, 'token'=>$user_data->token, 'arrival'=> $data->arrival, 'cus_count'=>$data->cus_count, 'menu'=>$data->menu);
            $json = json_encode($json);
            header("location: bookingV2/booking-create-user.php?json={$json}");
            break;
        case 4:
            $json = array('id'=>$user_data->id, 'res_id'=>$data->res_id, 'arrival'=>$data->arrival, 'menu'=>$data->menu);
            $json = json_encode($json);
            header("location: bookingV2/booking-modify-user.php?json={$json}");
            break;
        case 5:
            $json = array('location_id'=>$data->location_id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewallfood-location.php?json={$json}");
            break;
        case 6:
            $json = array('res_id'=>$data->res_id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewallfoodorder-user?json={$json}");
            break;
        case 7:
            header("location: bookingV2/booking-viewalllocation-user.php");
            break;
        case 8:
            $json = array('id'=>$user_data->id);
            $json = json_encode($json);
            header("location: bookingV2/booking-ViewBooking-user?json={$json}");
            break;
        case 9:
            $json = array('id'=>$user_data->id, 'res_id'=>$data->res_id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewdetailbooking-user?json={$json}");
            break;
        case 10:
            $json = array('location_id'=>$data->location_id);
            $json = json_encode($json);
            header("location: bookingV2/booking-viewdetailselected-location?json={$json}");
            break;
    }
}