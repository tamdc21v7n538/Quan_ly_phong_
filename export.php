<?php
include 'config.php';

// Xuất Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=booking_report.xls");

// UTF-8 fix
echo "\xEF\xBB\xBF";
echo "<meta charset='UTF-8'>";

// Tiêu đề
echo "<h2 style='text-align:center;'>THỐNG KÊ ĐẶT PHÒNG</h2>";

// Bảng
echo "
<table border='1' style='border-collapse:collapse; width:100%;'>
<tr style='background:#d9d9d9; font-weight:bold; text-align:center;'>
    <th style='text-align:center;'>ID</th>
    <th style='text-align:center;'>Tên phòng</th>
    <th style='text-align:center;'>Lớp</th>
    <th style='text-align:center;'>Người đặt</th>
    <th style='text-align:center;'>Ngày</th>
    <th style='text-align:center;'>Thời gian</th>
    <th style='text-align:center;'>Số SV</th>
    <th style='text-align:center;'>Mục đích</th>
    <th style='text-align:center;'>Trạng thái</th>
    <th style='text-align:center;'>Ghi chú</th>
</tr>
";

// Query
$sql = "SELECT 
    bookings.id,
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
ORDER BY bookings.id ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Lỗi SQL: " . mysqli_error($conn));
}

$total = 0;

// Data
while ($row = mysqli_fetch_assoc($result)) {

    $time = $row['time_start'] . " - " . $row['time_end'];
    $status = ($row['status'] == 'active') ? 'Đang sử dụng' : 'Hoàn thành';

    echo "
    <tr>
        <td style='text-align:center;'>{$row['id']}</td>
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

// Tổng
echo "
<tr style='font-weight:bold; background:#f2f2f2;'>
    <td colspan='6' style='text-align:center;'>TỔNG SỐ LƯỢT ĐẶT</td>
    <td colspan='4' style='text-align:center;'>{$total}</td>
</tr>
";

echo "</table>";
