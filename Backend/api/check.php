<?php
date_default_timezone_set("Asia/Bangkok");
include 'database/Database.php';
include 'random.php';

$time = date("Y-m-d H:i:s", time());
function readuser($id)
{
    $obj = new Database();
    $obj->select('users', 'access_token', null, "user_id={$id}", null, null);
    $res = $obj->getResult();
    return $res[0]['access_token'];
}

function readuserwithtoken($token)
{
    $obj = new Database();
    $obj->select('users', '*', null, "access_token='{$token}'", null, null);
    $res = $obj->getResult();
    if (isset($res[0])){
        return $res[0];
    }
    return null;
}

function readReservation($id, $res)
{
    $obj = new Database();
    $obj->select('reservations', 'user_id', null, "res_id={$res}", null, null);
    $res = $obj->getResult();
    return isset($res[0]['user_id']) == $id; #ยังไม่แน่ใจว่าได้ไหมเพราะยังไม่มี ข้อมูล ให้ทดสอบ
}

?>