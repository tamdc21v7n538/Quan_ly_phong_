<?php
// ===== HEADER JSON =====
header('Content-Type: application/json; charset=utf-8');

// ===== INCLUDE DB =====
require_once 'config.php';

// ===== SET UTF8 =====
mysqli_set_charset($conn, "utf8");

// ===== LẤY DATA (AN TOÀN) =====
$room  = $_POST['room']  ?? null;
$date  = $_POST['date']  ?? null;
$start = $_POST['start'] ?? null;
$end   = $_POST['end']   ?? null;

// ===== VALIDATE =====
if (!$room || !$date || !$start || !$end) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu dữ liệu"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ===== PREPARED STATEMENT (CHỐNG SQL INJECTION) =====
$stmt = $conn->prepare("
    SELECT id FROM bookings 
    WHERE room_id = ? 
    AND date = ?
    AND (time_start < ? AND time_end > ?)
");

$stmt->bind_param("ssss", $room, $date, $end, $start);
$stmt->execute();

$result = $stmt->get_result();

// ===== TRẢ KẾT QUẢ =====
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

// ===== CLOSE =====
$stmt->close();
mysqli_close($conn);
