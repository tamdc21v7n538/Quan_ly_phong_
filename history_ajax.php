<?php
include 'config.php';

/* =====================
   PAGINATION LỊCH SỬ ĐẶT PHÒNG
===================== */
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;

/* =====================
   FILTER
===================== */
$date = $_GET['date'] ?? '';
$name = $_GET['name'] ?? '';
$room = $_GET['room'] ?? '';

$where = [];

if ($date != "") {
    $where[] = "bookings.date = '$date'";
}

if ($name != "") {
    $where[] = "bookings.user_name LIKE '%$name%'";
}

if ($room != "") {
    $where[] = "rooms.name LIKE '%$room%'";
}

/* =====================
   WHERE SQL
===================== */
$whereSQL = count($where) > 0 ? implode(" AND ", $where) : "1";

/* =====================
   QUERY (THÊM BUILDING)
===================== */
$sql = "SELECT 
            bookings.*,
            rooms.name AS room_name,
            buildings.name AS building_name
        FROM bookings
        JOIN rooms ON bookings.room_id = rooms.id
        LEFT JOIN buildings ON rooms.building_id = buildings.id
        WHERE $whereSQL
        ORDER BY bookings.id DESC
        LIMIT $start, $limit";

$res = mysqli_query($conn, $sql);

if (!$res) {
    die("SQL Error: " . mysqli_error($conn));
}

/* =====================
   TABLE HEADER
===================== */
echo "<table class='table table-bordered text-center'>
<tr class='table-dark'>
    <th>Người đặt</th>
    <th>Lớp</th>
    <th>Tòa nhà</th>
    <th>Phòng</th>
    <th>Ngày</th>
    <th>Giờ</th>
    <th>Số người</th>
    <th>Mục đích</th>
    <th>Ghi chú</th>
</tr>";

/* =====================
   DATA ROWS
===================== */
while ($row = mysqli_fetch_assoc($res)) {
    echo "<tr>
        <td>{$row['user_name']}</td>
        <td>{$row['class']}</td>
        <td>{$row['building_name']}</td>
        <td>{$row['room_name']}</td>
        <td>{$row['date']}</td>
        <td>{$row['time_start']} - {$row['time_end']}</td>
        <td>{$row['students']}</td>
        <td>{$row['purpose']}</td>
        <td>{$row['note']}</td>
    </tr>";
}

echo "</table>";
