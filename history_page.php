<?php
include 'config.php';

$page = $_GET['page'] ?? 1;
$limit = 10; // 🔥 đổi 10 dòng / trang
$start = ($page - 1) * $limit;

// lấy filter giống file history_ajax.php
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

$whereSQL = count($where) > 0 ? implode(" AND ", $where) : "1";

// 🔥 ĐẾM TỔNG THEO FILTER (QUAN TRỌNG)
$sqlTotal = "SELECT COUNT(*) as total
             FROM bookings
             JOIN rooms ON bookings.room_id = rooms.id
             WHERE $whereSQL";

$resTotal = mysqli_query($conn, $sqlTotal);
$rowTotal = mysqli_fetch_assoc($resTotal);
$total = $rowTotal['total'];

$pages = ceil($total / $limit);
//Thêm để đếm tổng số hoạt động đặt phòng
echo "<div class='mb-2'>
        <span class='badge bg-info'>Tổng: $total</span>
      </div>";

// ===== HIỂN THỊ NÚT =====
for ($i = 1; $i <= $pages; $i++) {

    $active = ($i == $page) ? "btn-primary" : "btn-light";

    echo "<button class='btn btn-sm $active m-1'
            onclick='loadHistory($i)'>
            $i
          </button>";
}
