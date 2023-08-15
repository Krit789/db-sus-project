<?php

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../../vendor/autoload.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input", true));
    $email = htmlentities($data->email);
    $password = htmlentities($data->password);

    $obj->select('users', '*', null, "email='{$email}'", null, null);
    $datas = $obj->getResult();
    foreach ($datas as $data) {
        $id = $data['user_id'];
        $fn = $data['first_name'];
        $ln = $data['last_name'];
        $em = $data['email'];
        $tele = $data['telephone'];
        $role = $data['role'];
        if (!password_verify($password, $data['password_hash'])) {
            echo json_encode([
                'status' => 0,
                'message' => 'Invalid Credentials',
            ]);
        } else {
            $payload = [
                'iss' => "localhost",
                'aud' => 'localhost',
                'exp' => time() + 1000, //10 mint
                'data' => [
                    'id' => $id,
                    'fn' => $fn,
                    'ln' => $ln,
                    'email' => $email,
                    'tele' => $tele,
                    'role' => $role
                ],
            ];

            $secret_key = "Hilal ahmad khan";
            $jwt = JWT::encode($payload, $secret_key, 'HS256');
            echo json_encode([
                'status' => 1,
                'jwt' => $jwt,
                'message' => 'Login Successfully',
            ]);
        }
    }

    #การดึงข้อมูล
    $secret_key = "Hilal ahmad khan";
    $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
    $data = $user_data->data;
    // echo $data->fn;

} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
