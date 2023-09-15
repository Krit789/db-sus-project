<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = json_decode($_GET['json']);

    $id = $data->menu_id;
    $role = $data->role;
    $name = $data->m_name;
    $price = $data->price;
    $desc = $data->m_desc;
    $cate_id = $data->m_category;
    $url = $data->img_url;

    if ($role == 'GOD') {

        $obj->update('menus', ['item_name' => $name, 'item_desc' => $desc, 'category_id' => $cate_id, 'price' => $price, 'img_url' => $url], "menu_id={$id}");
        $res = $obj->getResult();
        if ($res[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'Modify Menu Successful'
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Modify Menu Failed Successful'
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