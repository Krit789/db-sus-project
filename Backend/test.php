<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../../vendor/autoload.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
  
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
