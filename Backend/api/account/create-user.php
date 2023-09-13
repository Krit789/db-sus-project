<?php

//    add headers

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';
include '../random.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $data = json_decode(file_get_contents("php://input", true));

    $first_name = htmlentities($data->fn);
    $last_name = htmlentities($data->ln);
    $email = htmlentities($data->email);
    $password = htmlentities($data->password);
    $new_password = password_hash($password, PASSWORD_DEFAULT);
    $telephone = htmlentities($data->tele);
    $role = htmlentities($data->role);
    $token = randomCode(32);
    // check user by email

    $obj->select("users", "*", null, "email='{$email}'", null, null);
    $is_email = $obj->getResult();
    if (isset($is_email[0]['email']) == $email) {
        echo json_encode([
            'status' => 2,
            'message' => 'Email already Exists',
        ]);
    } else {
        $obj->insert('users', ['first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'password_hash' => $new_password, 'telephone' => $telephone, 'role' => $role, 'access_token' => $token]);
        $data = $obj->getResult();
        if ($data[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'User add Successfully',
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Server Problem',
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
