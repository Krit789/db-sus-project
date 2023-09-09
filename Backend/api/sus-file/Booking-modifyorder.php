<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type:application/json');
include '../database/Database.php';

$obj = new Database();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $data = json_decode(file_get_contents("php://input"));

    $id = $data->res_id;
    $arrival = $data->arrival;

    if ($data->menu[0] != null){
        $menu = $data->menu;
        $obj->delete('orders', "res_id='{$id}'");
        $result = $obj->getResult();
        if ($result[0] == 1) {
            
            $tmp = "";
            foreach ($data->menu as $menu){
                //[0] menu_id [1] จำนวน 
                if ($menu == $data->menu[sizeof($data->menu)-1]){
                    $tmp .= "($res_id, $menu[0], $menu[1])";
                }else{
                    $tmp .= "($res_id, $menu[0], $menu[1]),";
                }
            }
            $obj->insertlagacy('orders', 'res_id, menu_id, amount', $tmp);

            $resutl = $obj->getResult(); #อยากเช็คแต่ยังก่อน

        } else {
            echo json_encode([
                'status' => 0,
                'message' => "Server Problem",
            ]);
        }
    }
    #เพิ่มเวลานัดอีก+++++++++
    $obj->update('reservations', ['arrival'=>$arrival], "res_id='{$id}'");
    $result = $obj->getResult(); #อยากเช็คอันนี้ด้วย

} else {
    echo json_encode([
        'status' => 0,
        'message' => "Access Denied",
    ]);
}
