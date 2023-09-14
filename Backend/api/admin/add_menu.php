<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = json_decode($_GET['json']);

    $role = $data->role;
    $name = $data->m_name;
    $price = $data->price;

    $desc = null;
    $cate_id = null;
    $url = null;

    if (isset($data->m_desc)) {
        $desc = $data->m_desc;
    }
    if (isset($data->m_category)) {
        $cate_id = $data->m_category;
    }
    if (isset($data->img_url)) {
        $url = $data->img_url;
    }

    if ($role == 'GOD') {

        $obj->insert('locations', ['item_name' => $name, 'item_desc' => $desc, 'category_id' => $cate_id, 'price' => $price, 'img_url' => $url]);
        $res = $obj->getResult();
        if ($res[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'Add Menu Successful'
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Add Menu Failed Successful'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Insuffient Permission'
        ]);
    }
}
?>