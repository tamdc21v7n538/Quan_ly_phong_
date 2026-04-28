<?php

include 'config.php';


/* =========================
   CHECK INPUT
========================= */
$r = $_POST['room'] ?? null;
$d = $_POST['date'] ?? null;
$s = $_POST['start'] ?? null;
$e = $_POST['end'] ?? null;

$user = $_POST['user_name'] ?? '';
$students = $_POST['students'] ?? 0;
$purpose = $_POST['purpose'] ?? '';
$note = $_POST['note'] ?? '';
$class = $_POST['class'] ?? '';


/* Validate bắt buộc */
if (!$r || !$d || !$s || !$e) {
    die("Thiếu dữ liệu đặt phòng!");
}


/* =========================
   CHECK TRÙNG GIỜ
========================= */
$check = mysqli_query($conn, "
    SELECT * FROM bookings 
    WHERE room_id = '$r' 
    AND date = '$d'
    AND (time_start < '$e' AND time_end > '$s')
");

if (!$check) {
    die("Query lỗi: " . mysqli_error($conn));
}

if (mysqli_num_rows($check) > 0) {
    echo "Trùng giờ!";
    exit;
}

/* =========================
   INSERT BOOKING
========================= */
$sql = "
INSERT INTO bookings (
    room_id,
    date,
    time_start,
    time_end,
    user_name,
    students,
    purpose,
    note,
    class
) VALUES (
    '$r',
    '$d',
    '$s',
    '$e',
    '$user',
    '$students',
    '$purpose',
    '$note',
    '$class'
)
";

$insert = mysqli_query($conn, $sql);

if ($insert) {
    echo "Đặt thành công!";
} else {
    echo "Lỗi insert: " . mysqli_error($conn);
}
