<?php
// ⚠️ KHÔNG có khoảng trắng trước dòng này

// ===== HEADER JSON =====
header('Content-Type: application/json; charset=utf-8');

// ===== INCLUDE DB =====
require_once 'config.php';

// ===== SET UTF8 =====
mysqli_set_charset($conn, "utf8");

// ===== QUERY =====
$sql = "
    SELECT 
        bookings.id, 
        rooms.name, 
        bookings.date, 
        bookings.time_start 
    FROM bookings 
    JOIN rooms ON bookings.room_id = rooms.id
    ORDER BY bookings.id DESC
    LIMIT 10
";

$result = mysqli_query($conn, $sql);

// ===== CHECK LỖI QUERY =====
if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($conn)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ===== BUILD DATA =====
$data = [];

while ($row = mysqli_fetch_assoc($result)) {

    // format giờ đẹp hơn
    $time = substr($row['time_start'], 0, 5);

    $data[] = [
        "id" => (int)$row['id'],
        "message" => "Phòng {$row['name']} - {$row['date']} ({$time})"
    ];
}

// ===== OUTPUT =====
echo json_encode($data, JSON_UNESCAPED_UNICODE);

// ===== CLOSE DB =====
mysqli_close($conn);
