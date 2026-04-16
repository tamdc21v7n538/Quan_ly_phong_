<?php
include 'config.php';

$r = $_POST['room'];
$d = $_POST['date'];
$s = $_POST['start'];
$e = $_POST['end'];

/* Thêm dữ liệu cho bookings */
$user = $_POST['user_name'] ?? '';
$students = $_POST['students'] ?? 0;
$purpose = $_POST['purpose'] ?? '';
$note = $_POST['note'] ?? '';

/* check trùng giờ*/
$check = mysqli_query($conn, "SELECT * FROM bookings 
WHERE room_id='$r' AND date='$d'
AND (time_start<'$e' AND time_end>'$s')");

if (mysqli_num_rows($check) > 0) {
    echo "Trùng giờ!";
} else {

    /*  insert có thêm cột  */
    mysqli_query($conn, "INSERT INTO bookings(
        room_id,
        date,
        time_start,
        time_end,
        user_name,
        students,
        purpose,
        note
    ) VALUES(
        '$r',
        '$d',
        '$s',
        '$e',
        '$user',
        '$students',
        '$purpose',
        '$note'
    )");

    echo "Đặt thành công!";
}
