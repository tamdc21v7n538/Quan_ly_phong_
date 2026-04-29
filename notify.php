<?php
// ⚠️ KHÔNG có khoảng trắng trước dòng này

// ===== HEADER JSON =====
header('Content-Type: application/json; charset=utf-8');

// ===== INCLUDE DB =====
require_once 'config.php';

// ===== SET UTF8 =====
mysqli_set_charset($conn, "utf8");

// ===== QUERY (THÊM buildings) =====
$sql = "
    SELECT 
        bookings.id, 
        rooms.name AS room_name,
        buildings.name AS building_name,
        bookings.date, 
        bookings.time_start 
    FROM bookings 
    JOIN rooms ON bookings.room_id = rooms.id
    LEFT JOIN buildings ON rooms.building_id = buildings.id
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

    // format giờ đẹp
    $time = substr($row['time_start'], 0, 5);

    // nếu chưa có tòa thì tránh lỗi null
    $building = $row['building_name'] ?? 'Chưa có tòa';

    $data[] = [
        "id" => (int)$row['id'],
        "message" => "🏢 {$building} | 🏫 {$row['room_name']} - {$row['date']} ({$time})"
    ];
}

// ===== OUTPUT =====
echo json_encode($data, JSON_UNESCAPED_UNICODE);

// ===== CLOSE DB =====
mysqli_close($conn);
