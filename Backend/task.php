<?php
include './api/check.php';
ini_set('zlib.output_compression', '1');
header('Content-Type:application/json');

$obj = new Database();
try {
    $time = date_create(substr($time, 0, 19));
    date_add($time, date_interval_create_from_date_string("-20 minutes"));
    $time = date_format($time, 'Y-m-d H:i:s');
    $obj->update("reservations", ['status' => 2], "arrival <= '{$time}' and status = 'INPROGRESS'");
    echo json_encode([
        'status' => 1,
        'message' => "Task Performed Successfully",
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 1,
        'message' => "Failed to Perform Task with following error: '$e'",
    ]);
}
exit();
?>