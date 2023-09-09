<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $data = json_decode(file_get_contents("php://input"));
    $allheaders = getallheaders();
    $jwt = $allheaders['Authorization'];

    $secret_key = "Hilal ahmad khan";
    $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
    $user_data = $user_data->data;

    $id = $user_data->id;
    $fn = $data->fn;
    $ln = $data->ln;
    $email = $data->email;
    $password = $data->password;
    $new_password = password_hash($password, PASSWORD_DEFAULT);
    $tele = $data->telephone;
    
    $obj->select("users", "email", null, "email='{$email}'", null, null);
    $is_email = $obj->getResult();
    if (isset($is_email[0]['email']) == $email) {
        echo json_encode([
            'status' => 2,
            'message' => 'Email already Exists',
        ]);
    }else{
        $obj->update('users', ['first_name' => $fn, 'last_name' => $ln, 'telephone' => $tele, 'password_hash' => $new_password], "user_id={$id}");
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
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => "Access Denied",
    ]);
}
