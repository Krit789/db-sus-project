<?php
include 'check.php';

$test = readReservation(5, 1);
if ($test){
    echo "1";
}else{
    echo "2";
}
?>