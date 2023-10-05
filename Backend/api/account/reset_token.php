<?php

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../../vendor/autoload.php';
include '../random.php';

use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->safeload();

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $allheaders = getallheaders();
    $jwt = $allheaders['Authorization'];

    $secret_key = $_SERVER['SECRET'];
    $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
    $user_data = $user_data->data;

    $token = randomCode(32);
    $obj->update('users', ['access_token' => $token], "user_id={$user_data->id}");
    $result = $obj->getResult();
    if ($result[0] == 1) echo json_encode([
        'status' => 1,
        'message' => 'Token Reset Successful'
    ]); else echo json_encode([
        'status' => 0,
        'message' => 'Tonken Reset Failed'
    ]);

} else echo json_encode([
    'status' => 0,
    'message' => 'Access Denied',
]);
