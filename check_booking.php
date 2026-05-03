<?php
// trả json tvieetj
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

// sửa lỗi dấu
mysqli_set_charset($conn, "utf8");

// lấy dl từ ajax
$room  = $_POST['room']  ?? null;
$date  = $_POST['date']  ?? null;
$start = $_POST['start'] ?? null;
$end   = $_POST['end']   ?? null;

// ktra thíu dl, trả js
if (!$room || !$date || !$start || !$end) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu dữ liệu"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// placeholder CHỐNG SQL INJECTION
$stmt = $conn->prepare("
    SELECT id FROM bookings 
    WHERE room_id = ? 
    AND date = ?
    AND (time_start < ? AND time_end > ?)
");
//ssss 4 biến đều là string truyền dl an toàn vào SQL
$stmt->bind_param("ssss", $room, $date, $end, $start);
$stmt->execute();

//lấy kq
$result = $stmt->get_result();

// trả kq
if ($result->num_rows > 0) {
    echo json_encode([
        "status" => "exist",
        "message" => "Trùng lịch"
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "status" => "ok",
        "message" => "Có thể đặt"
    ], JSON_UNESCAPED_UNICODE);
}

// đóng kết nối
$stmt->close();
mysqli_close($conn);
