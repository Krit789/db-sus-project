<?php

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../../vendor/autoload.php';
include '../check.php';

use Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input", true));
    $email = htmlentities($data->email);
    $password = htmlentities($data->password);

    // $token = randomCode(32);

    $obj->select('users', 'user_id', null, "email='{$email}'", null, null);

    $user_id = $obj->getResult();
    if ($user_id != NULL) {

        $user_id = $user_id[0]['user_id'];

        $obj->select('users', '*', null, "email='{$email}'", null, null);
        $datas = $obj->getResult();
        foreach ($datas as $data) {
            $id = $data['user_id'];
            $fn = $data['first_name'];
            $ln = $data['last_name'];
            $em = $data['email'];
            $tele = $data['telephone'];
            $role = $data['role'];
            $token = $data['access_token'];
            $status = $data['status'];
            if (!password_verify($password, $data['password_hash']) || $status == 'SUSPENDED') echo json_encode([
                'status' => 0,
                'message' => 'Invalid Credentials',
            ]); else {
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
                        'role' => $role,
                        'token' => $token
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
        // $secret_key = "Hilal ahmad khan";
        // $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
        // $data = $user_data->data;
        // if ($data->role == "USER"){
        //     echo "1";
        // }
        // echo $data->role;

    } else echo json_encode([
        'status' => 0,
        'message' => 'No No User in Database',
    ]);

} else echo json_encode([
    'status' => 0,
    'message' => 'Access Denied',
]);
