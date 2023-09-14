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
    $role = "GOD";
    $table_id = $data->table_id;

    if ($role == "MANAGER" || $role == "GOD") {
        $tmp = "";
        $count = 0;
        foreach ($table_id as $id) {
            if ($count == 0) {
                $tmp .= "{$id}";
            } else {
                $tmp .= ", {$id}";
            }
            $count++;
        }
        $obj->delete("tables", "table_id in ($tmp)");
        $result = $obj->getResult();
        if ($result[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => "Delete Table Successful"
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => "Delete Table Falied Successful"
            ]);
        }

    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Insufficient Permission',
        ]);
    }
}
?>