<?php
include 'config.php';
include 'session.php';

if ($_SESSION['role'] != 'user') header("location: dashboard.php");

include 'navbar.php';

// ===== GET =====
$sort = $_GET['sort'] ?? 'id';
$building = $_GET['building'] ?? '';

// ===== ORDER =====
switch ($sort) {
    case 'room':
        $order = "rooms.name ASC";
        break;
    case 'date':
        $order = "bookings.date DESC";
        break;
    case 'building':
        $order = "buildings.name ASC";
        break;
    default:
        $order = "bookings.id ASC";
}

// ===== WHERE =====
$where = "";
if (!empty($building)) {
    $where = "WHERE rooms.building_id = " . (int)$building;
}
?>

<style>
    body {
        background: #051631;
    }

    .main-title {
        color: #0d6efd;
        font-weight: bold;
    }

    table {
        border-radius: 10px;
        overflow: hidden;
    }
</style>

<div class="container mt-4">
    <h3 class="text-center mb-4 main-title">📋 Trang người dùng (chỉ xem)</h3>

    <!-- ===== FILTER + SORT ===== -->
    <div class="text-center mb-4">
        <form method="GET" class="d-inline">

            <!-- LỌC DÃY -->
            <select name="building" onchange="this.form.submit()" class="form-select w-auto d-inline">
                <option value="">-- Tất cả dãy --</option>
                <?php
                $bld = mysqli_query($conn, "SELECT * FROM buildings");
                while ($b = mysqli_fetch_assoc($bld)) {
                    $selected = ($building == $b['id']) ? "selected" : "";
                    echo "<option value='{$b['id']}' $selected>{$b['name']}</option>";
                }
                ?>
            </select>

            <!-- SORT -->
            <select name="sort" onchange="this.form.submit()" class="form-select w-auto d-inline ms-2">
                <option value="id" <?= $sort == 'id' ? 'selected' : '' ?>>Sắp xếp theo ID</option>
                <option value="room" <?= $sort == 'room' ? 'selected' : '' ?>>Sắp xếp theo phòng</option>
                <option value="date" <?= $sort == 'date' ? 'selected' : '' ?>>Sắp xếp theo ngày</option>
                <option value="building" <?= $sort == 'building' ? 'selected' : '' ?>>Sắp xếp theo dãy</option>
            </select>

        </form>
    </div>

    <table class="table table-bordered table-striped text-center shadow">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tòa nhà</th>
                <th>Phòng</th>
                <th>Người đặt</th>
                <th>Ngày</th>
                <th>Thời gian</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $sql = "SELECT 
                        bookings.*, 
                        rooms.name AS room_name,
                        buildings.name AS building_name
                    FROM bookings 
                    JOIN rooms ON bookings.room_id = rooms.id
                    LEFT JOIN buildings ON rooms.building_id = buildings.id
                    $where
                    ORDER BY $order";

            $res = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($res)) {

                $time = $row['time_start'] . " - " . $row['time_end'];

                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['building_name']}</td>
                    <td>{$row['room_name']}</td>
                    <td>{$row['user_name']}</td>
                    <td>{$row['date']}</td>
                    <td><span class='badge bg-info text-dark'>{$time}</span></td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>