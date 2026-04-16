<?php
include 'config.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data.xls");

$res = mysqli_query($conn, "SELECT * FROM bookings");

while ($r = mysqli_fetch_assoc($res)) {
    echo implode("\t", $r) . "\n";
}
