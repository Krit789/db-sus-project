<?php

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header('Content-Type:application/json');
include '../database/Database.php';
include '../vendor/autoload.php';

use Firebase\JWT\JWT;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method: GET ,POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->safeload();

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") try {
    $allheaders = getallheaders();
    $jwt = $allheaders['Authorization'];

    $secret_key = $_SERVER['SECRET'];
    $user_data = JWT::decode($jwt, $secret_key, array('HS256'));
    $data = $user_data->data;
    echo json_encode([
        'status' => 1,
        'message' => $data,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 0,
        'message' => $e->getMessage(),
    ]);
} else echo json_encode([
    'status' => 0,
    'message' => 'Access Denied',
]);
