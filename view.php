<?php
include 'config.php';
include 'session.php';

if ($_SESSION['role'] != 'user') header("location: dashboard.php");

include 'navbar.php';

// LẤY GIÁ TRỊ SẮP XẾP
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';

// XỬ LÝ ORDER BY
switch ($sort) {
    case 'room':
        $order = "rooms.name ASC";
        break;
    case 'date':
        $order = "bookings.date DESC";
        break;
    default:
        $order = "bookings.id ASC";
}
?>
<style>
    /* Nền toàn trang */
    body {
        background: #051631;
    }

    /* Tiêu đề chính */
    .main-title {
        color: #0d6efd;
        font-weight: bold;
    }

    /* Card đẹp hơn */
    .card {
        border-radius: 15px;
    }

    /* Bảng */
    table {
        border-radius: 10px;
        overflow: hidden;
    }

    /* Preview box */
    #preview {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container mt-4">
    <h3 class="text-center mb-4 main-title">📋 Trang người dùng (chỉ xem)</h3>

    <!--  CHỌN SẮP XẾP -->
    <div class="text-center mb-4">
        <form method="GET" class="d-inline">
            <select name="sort" onchange="this.form.submit()" class="form-select w-auto d-inline">
                <option value="id" <?= $sort == 'id' ? 'selected' : '' ?>>Sắp xếp theo ID</option>
                <option value="room" <?= $sort == 'room' ? 'selected' : '' ?>>Sắp xếp theo phòng</option>
                <option value="date" <?= $sort == 'date' ? 'selected' : '' ?>>Sắp xếp theo ngày</option>
            </select>
        </form>
    </div>

    <table class="table table-bordered table-striped text-center shadow">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Phòng</th>
                <th>Người đặt</th>
                <th>Ngày</th>
                <th>Thời gian hoạt động</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $sql = "SELECT bookings.*, rooms.name 
                    FROM bookings 
                    JOIN rooms ON bookings.room_id = rooms.id
                    ORDER BY $order";

            $res = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($res)) {

                $time = $row['time_start'] . " - " . $row['time_end'];

                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['user_name']}</td>
                    <td>{$row['date']}</td>
                    <td><span class='badge bg-info text-dark'>{$time}</span></td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>