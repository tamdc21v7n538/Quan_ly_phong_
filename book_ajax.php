<?php
include 'config.php';

$r = $_POST['room'];
$d = $_POST['date'];
$s = $_POST['start'];
$e = $_POST['end'];

$check = mysqli_query($conn, "SELECT * FROM bookings 
WHERE room_id='$r' AND date='$d'
AND (time_start<'$e' AND time_end>'$s')");

if (mysqli_num_rows($check) > 0) {
    echo "Trùng giờ!";
} else {
    mysqli_query($conn, "INSERT INTO bookings(room_id,date,time_start,time_end)
VALUES('$r','$d','$s','$e')");
    echo "Đặt thành công!";
}
