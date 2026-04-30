<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

if (!$conn) {
    die("Lỗi kết nối DB");
}

$sql = "
SELECT 
    r.id,
    r.name AS room_name,
    r.building_id AS building,

    CASE 
        WHEN EXISTS (
            SELECT 1 FROM bookings b
            WHERE b.room_id = r.id
            AND b.date = CURDATE()
            AND b.time_start <= CURRENT_TIME()
            AND b.time_end >= CURRENT_TIME()
        )
        THEN 'active'
        ELSE 'free'
    END AS status,

    (
        SELECT b.time_end
        FROM bookings b
        WHERE b.room_id = r.id
        AND b.date = CURDATE()
        AND b.time_start <= CURRENT_TIME()
        AND b.time_end >= CURRENT_TIME()
        LIMIT 1
    ) AS time_end

FROM rooms r
ORDER BY r.building_id, r.name
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL lỗi: " . mysqli_error($conn));
}

$data = [];

while ($row = mysqli_fetch_assoc($result)) {

    $remaining = "--";

    if ($row['status'] === 'active' && !empty($row['time_end'])) {

        // 🔥 FIX CHUẨN: ghép ngày + giờ
        $endDateTime = date("Y-m-d") . " " . $row['time_end'];

        $end = strtotime($endDateTime);
        $now = time();

        $diff = $end - $now;

        if ($diff > 0) {
            $remaining = gmdate("H:i:s", $diff);
        } else {
            $remaining = "00:00:00";
        }
    }

    $row['remaining'] = $remaining;

    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
