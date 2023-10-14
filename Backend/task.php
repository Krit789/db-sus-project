<?php
include './api/check.php';
$obj = new Database();

$time = date_create(substr($time, 0, 19));
date_add($time, date_interval_create_from_date_string("-20 minutes"));
$time = date_format($time, 'Y-m-d H:i:s');
$obj->update("reservations", ['status' => 2], "arrival <= '{$time}' and status = 'INPROGRESS'");
?>