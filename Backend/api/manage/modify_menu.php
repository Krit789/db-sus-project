<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:GET');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $data = json_decode($_GET['json']);

    $role = $data->role;
    $id = $data->location_id;
    $menu = $data->menu;

    if ($role == "MANAGER" || $role == "GOD") {
        $tmp = "";
        $count = 0;
        foreach ($menu as $menus){
            if ($count != sizeof($menu)-1){
                $tmp .= "({$id},{$menus}), ";
            }else{
                $tmp .= "({$id},{$menus})";
            }
            $count += 1;
        }
        $obj->delete('restrictions', "location_id={$id}");
        $obj->insertlagacy('restrictions', 'location_id, menu_id', $tmp);

        $res = $obj->getResult();
        if ($res[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'Modify Menus Successful',
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "server problem", #ถ้ามันหาไม่เจอสัก row มันก็จะเข้าอันนี้
            ]);
        }

    }else{
        echo json_encode([
            'status' => 0,
            'message' => 'Insuffient Permission'
        ]);
    }

} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
