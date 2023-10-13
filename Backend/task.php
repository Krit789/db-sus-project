<?php
include './api/check.php';

$obj = new Database();

$obj->select("reservations", "*", null, "status='INPROGRESS'");
$result = $obj->getResult();

$time = date_create(substr($time, 0, 19));
date_add($time, date_interval_create_from_date_string("20 minutes"));

foreach ($result as $key) {
    $date = date_create(substr($key['arrival'], 0, 19));
    $date = date_format($date, "Y-m-d h:m:s");
    if ($time >= $date){
        $obj->update("reservations", ["status" => 2], "res_id={$key['res_id']}");
    }
}
