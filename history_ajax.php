<?php
include 'config.php';

$page = $_GET['page'] ?? 1;
$limit = 10;
$start = ($page - 1) * $limit;

$date = $_GET['date'] ?? '';
$name = $_GET['name'] ?? '';
$room = $_GET['room'] ?? '';


$where = [];

if ($date != "") {
    $where[] = "bookings.date='$date'";
}

if ($name != "") {
    $where[] = "bookings.user_name LIKE '%$name%'";
}

if ($room != "") {
    $where[] = "rooms.name LIKE '%$room%'";
}

// ghép điều kiện
$whereSQL = count($where) > 0 ? implode(" AND ", $where) : "1";

$sql = "SELECT bookings.*, rooms.name AS room_name 
        FROM bookings 
        JOIN rooms ON bookings.room_id = rooms.id
        WHERE $whereSQL
        ORDER BY bookings.id DESC 
        LIMIT $start,$limit";
$res = mysqli_query($conn, $sql);

echo "<table class='table table-bordered text-center'>
<tr class='table-dark'>
<th>Người đặt</th>
<th>Phòng</th>
<th>Ngày</th>
<th>Giờ</th>
</tr>";

while ($row = mysqli_fetch_assoc($res)) {
    echo "<tr>
        <td>{$row['user_name']}</td>
        <td>{$row['room_name']}</td>
        <td>{$row['date']}</td>
        <td>{$row['time_start']} - {$row['time_end']}</td>
    </tr>";
}

echo "</table>";
