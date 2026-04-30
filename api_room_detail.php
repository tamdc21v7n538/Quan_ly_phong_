<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// kiểm tra kết nối DB
if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Không kết nối được database"
    ]);
    exit;
}

// kiểm tra tham số
if (!isset($_GET['room_id']) || !is_numeric($_GET['room_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "room_id không hợp lệ"
    ]);
    exit;
}

$room_id = intval($_GET['room_id']);

// dùng prepared statement để an toàn
$sql = "
SELECT 
    r.name AS room_name,
    b.user_name AS username,
    b.date,
    b.time_start,
    b.time_end,
    b.purpose,
    b.class,
    b.students
FROM bookings b
JOIN rooms r ON b.room_id = r.id
WHERE b.room_id = ?
AND b.date = CURDATE()
AND b.time_start <= CURRENT_TIME()
AND b.time_end >= CURRENT_TIME()
LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Prepare failed",
        "detail" => mysqli_error($conn)
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $room_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = mysqli_fetch_assoc($result);

// nếu không có phòng đang hoạt động
if (!$data) {
    echo json_encode([
        "status" => "empty",
        "message" => "Phòng hiện đang trống"
    ]);
    exit;
}

// trả dữ liệu OK
echo json_encode([
    "status" => "success",
    "data" => $data
], JSON_UNESCAPED_UNICODE);
