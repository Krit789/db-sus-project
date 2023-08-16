<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include 'api/database/Database.php';
include 'vendor/autoload.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
  $data = json_decode(file_get_contents("php://input"));
  $table_id = $data->table_id;
  $obj->select('users', 'first_name', null, "user_id=$table_id", null, null);
  $result = $obj->getResult();
  // foreach ($result as $datas){
  //   echo $datas['first_name'];
  // }
  echo $result[0]['first_name'];
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
