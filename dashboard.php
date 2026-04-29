<?php
include 'config.php';
include 'session.php';

if (!isset($_SESSION['user'])) header("location: login.php");

include 'navbar.php';

// ===== SORT =====
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$building_filter = isset($_GET['building']) ? $_GET['building'] : '';

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

// ===== PAGINATION =====
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// ===== WHERE FILTER =====
$where = "";
if ($building_filter != "") {
    $where = "WHERE rooms.building_id = '$building_filter'";
}
?>
<link rel="stylesheet" href="style.css">

<div class="container mt-4">

    <h3 class="text-center mb-4 main-title">📊 Dashboard hệ thống</h3>

    <!-- THỐNG KÊ -->
    <div class="row">
        <?php
        $r = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM rooms"));
        $b = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM bookings"));
        ?>

        <div class="col-md-6">
            <div class="card text-white bg-primary shadow">
                <div class="card-body text-center">
                    <h5>Tổng số phòng</h5>
                    <h2><?= $r[0] ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-success shadow">
                <div class="card-body text-center">
                    <h5>Tổng lượt đặt</h5>
                    <h2><?= $b[0] ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- BUTTON -->
    <div class="mt-4 text-center">
        <a href="export.php" class="btn btn-success px-4">⬇ Xuất Excel</a>
        <button onclick="togglePreview()" class="btn btn-primary px-4 ms-2">👁 Xem trước</button>
    </div>

    <!-- ===== LỌC DÃY ===== -->
    <div class="text-center mt-4">
        <form method="GET">
            <select name="building" onchange="this.form.submit()" class="form-select w-auto d-inline">
                <option value="">-- Tất cả dãy --</option>
                <?php
                $bld = mysqli_query($conn, "SELECT * FROM buildings");
                while ($b = mysqli_fetch_assoc($bld)) {
                    $selected = ($building_filter == $b['id']) ? "selected" : "";
                    echo "<option value='{$b['id']}' $selected>{$b['name']}</option>";
                }
                ?>
            </select>

            <input type="hidden" name="sort" value="<?= $sort ?>">
        </form>
    </div>

    <!-- THỐNG KÊ PHÒNG -->
    <div class="mt-4">
        <h5 class="text-center mb-3 text-primary">📋 Thống kê theo phòng</h5>

        <table class="table table-bordered text-center shadow">
            <thead class="table-dark">
                <tr>
                    <th>Id</th>
                    <th>Tòa nhà</th>
                    <th>Tên phòng</th>
                    <th>Số lượt đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $data = mysqli_query($conn, "
                    SELECT rooms.name, buildings.name AS building_name, COUNT(bookings.id) total
                    FROM rooms 
                    LEFT JOIN bookings ON rooms.id = bookings.room_id
                    LEFT JOIN buildings ON rooms.building_id = buildings.id
                    $where
                    GROUP BY rooms.id
                ");

                $i = 1;
                while ($row = mysqli_fetch_assoc($data)) {
                    echo "<tr>
                        <td>{$i}</td>
                        <td>{$row['building_name']}</td>
                        <td>{$row['name']}</td>
                        <td><span class='badge bg-success'>{$row['total']}</span></td>
                    </tr>";
                    $i++;
                }
                ?>
            </tbody>

            //tổng tất cả
            <tfoot>
                <?php
                if ($building_filter != "") {
                    $totalQuery = mysqli_query($conn, "
            SELECT COUNT(*) 
            FROM bookings 
            JOIN rooms ON bookings.room_id = rooms.id
            WHERE rooms.building_id = '$building_filter'
        ");
                } else {
                    $totalQuery = mysqli_query($conn, "SELECT COUNT(*) FROM bookings");
                }

                $totalAll = mysqli_fetch_row($totalQuery);
                ?>
                <tr>
                    <th colspan="3">Tổng tất cả</th>
                    <th class="text-danger"><?= $totalAll[0] ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- PREVIEW -->
    <div id="preview" class="mt-4" style="display: <?= isset($_GET['sort']) || isset($_GET['page']) ? 'block' : 'none'; ?>;">

        <h5 class="text-center mb-3 text-primary">📄 Xem trước dữ liệu Excel</h5>

        <!-- SORT + FILTER -->
        <div class="text-center mb-3">
            <form method="GET">
                <select name="sort" onchange="this.form.submit()" class="form-select w-auto d-inline">
                    <option value="id" <?= $sort == 'id' ? 'selected' : '' ?>>Theo ID</option>
                    <option value="room" <?= $sort == 'room' ? 'selected' : '' ?>>Theo phòng</option>
                    <option value="date" <?= $sort == 'date' ? 'selected' : '' ?>>Theo ngày</option>
                </select>

                <select name="building" onchange="this.form.submit()" class="form-select w-auto d-inline ms-2">
                    <option value="">Tất cả dãy</option>
                    <?php
                    $bld = mysqli_query($conn, "SELECT * FROM buildings");
                    while ($b = mysqli_fetch_assoc($bld)) {
                        $selected = ($building_filter == $b['id']) ? "selected" : "";
                        echo "<option value='{$b['id']}' $selected>{$b['name']}</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tòa nhà</th>
                    <th>Phòng</th>
                    <th>Lớp</th>
                    <th>Người đặt</th>
                    <th>Ngày</th>
                    <th>Thời gian</th>
                    <th>Số SV</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $sql = "
                    SELECT bookings.*, rooms.name AS room_name, buildings.name AS building_name
                    FROM bookings 
                    JOIN rooms ON bookings.room_id = rooms.id
                    LEFT JOIN buildings ON rooms.building_id = buildings.id
                    $where
                    ORDER BY $order
                    LIMIT $start, $limit
                ";

                $res = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_assoc($res)) {
                    $time = $row['time_start'] . " - " . $row['time_end'];

                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['building_name']}</td>
                        <td>{$row['room_name']}</td>
                        <td>{$row['class']}</td>
                        <td>{$row['user_name']}</td>
                        <td>{$row['date']}</td>
                        <td>{$time}</td>
                        <td>{$row['students']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- PAGINATION -->
        <div class="text-center mt-3">
            <?php
            //tổng 
            if ($building_filter != "") {
                $totalQuery = mysqli_query($conn, "SELECT COUNT(*) 
                                                   FROM bookings 
                                                   JOIN rooms ON bookings.room_id = rooms.id
                                                   WHERE rooms.building_id = '$building_filter'");
            } else {
                $totalQuery = mysqli_query($conn, "SELECT COUNT(*) FROM bookings");
            }

            $total = mysqli_fetch_row($totalQuery)[0];
            $pages = ceil($total / $limit);

            for ($i = 1; $i <= $pages; $i++) {
                echo "<a href='?sort=$sort&page=$i&building=$building_filter' 
                        class='btn btn-sm " . ($i == $page ? 'btn-primary' : 'btn-outline-primary') . "'>
                        $i
                      </a> ";
            }
            ?>
        </div>
    </div>

</div>

<script>
    function togglePreview() {
        const el = document.getElementById("preview");
        el.style.display = (el.style.display === "none") ? "block" : "none";
    }

    setInterval(() => {
        fetch("notify.php")
            .then(res => res.text())
            .then(data => {
                const el = document.getElementById("notify");
                if (el) el.innerText = data;
            });
    }, 2000);
</script>