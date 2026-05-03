<?php
include 'config.php';

// ===== LẤY FILTER DÃY =====
$building = isset($_GET['building']) ? $_GET['building'] : '';

// ===== EXPORT EXCEL attachment → bắt tải xuống filename=... → tên file Excel khi tải về
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=booking_report.xls");

// fix UTF-8
echo "\xEF\xBB\xBF";
echo "<meta charset='UTF-8'>";

// Tiêu đề
echo "<h2 style='text-align:center;'>THỐNG KÊ ĐẶT PHÒNG</h2>";

// ===== TABLE HEADER =====
echo "
<table border='1' style='border-collapse:collapse; width:100%;'>
<tr style='background:#d9d9d9; font-weight:bold; text-align:center;'>
    <th>ID</th>
    <th>Tòa nhà</th>
    <th>Tên phòng</th>
    <th>Lớp</th>
    <th>Người đặt</th>
    <th>Ngày</th>
    <th>Thời gian</th>
    <th>Số SV</th>
    <th>Mục đích</th>
    <th>Trạng thái</th>
    <th>Ghi chú</th>
</tr>
";

// có đk lọc thì lọc
$where = "";
if ($building != "") {
    $where = "WHERE rooms.building_id = '$building'";
}

//truy vấn sql xếp tăng dần theo id
$sql = "
SELECT 
    bookings.id,
    buildings.name AS building_name,
    rooms.name AS room_name,
    bookings.class,
    bookings.user_name,
    bookings.date,
    bookings.time_start,
    bookings.time_end,
    bookings.students,
    bookings.purpose,
    bookings.status,
    bookings.note
FROM bookings
JOIN rooms ON bookings.room_id = rooms.id
LEFT JOIN buildings ON rooms.building_id = buildings.id
$where
ORDER BY bookings.id ASC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Lỗi SQL: " . mysqli_error($conn));
}

$total = 0;

// x lí d liệu ghép tg r in
while ($row = mysqli_fetch_assoc($result)) {

    $time = $row['time_start'] . " - " . $row['time_end'];
    $status = ($row['status'] == 'active') ? 'Đang sử dụng' : 'Hoàn thành';

    echo "
    <tr>
        <td style='text-align:center;'>{$row['id']}</td>
        <td style='text-align:center;'>{$row['building_name']}</td>
        <td style='text-align:center;'>{$row['room_name']}</td>
        <td style='text-align:center;'>{$row['class']}</td>
        <td style='text-align:center;'>{$row['user_name']}</td>
        <td style='text-align:center;'>{$row['date']}</td>
        <td style='text-align:center;'>{$time}</td>
        <td style='text-align:center;'>{$row['students']}</td>
        <td style='text-align:center;'>{$row['purpose']}</td>
        <td style='text-align:center;'>{$status}</td>
        <td style='text-align:center;'>{$row['note']}</td>
    </tr>
    ";

    $total++;
}

// ===== TOTAL =====
echo "
<tr style='font-weight:bold; background:#f2f2f2;'>
    <td colspan='7' style='text-align:center;'>TỔNG SỐ LƯỢT ĐẶT</td>
    <td colspan='4' style='text-align:center;'>{$total}</td>
</tr>
";

echo "</table>";
