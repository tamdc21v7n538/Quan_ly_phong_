<?php include 'config.php'; ?>
<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="style.css">

<div class="container mt-4">
    <h3 class="text-center main-title ">Biểu đồ thống kê số lượt đặt phòng</h3>

    <!-- ===== FILTER ===== -->
    <form method="GET" class="row mb-3 text-center">
        <div class="col-md-3">
            <select name="building" class="form-control">
                <option value="">-- Tất cả dãy --</option>
                <?php
                $b = mysqli_query($conn, "SELECT * FROM buildings");
                while ($row = mysqli_fetch_assoc($b)) {
                    $selected = (isset($_GET['building']) && $_GET['building'] == $row['id']) ? "selected" : "";
                    echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <input type="date" name="from" class="form-control"
                value="<?= $_GET['from'] ?? '' ?>">
        </div>

        <div class="col-md-3">
            <input type="date" name="to" class="form-control"
                value="<?= $_GET['to'] ?? '' ?>">
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary w-100">Lọc</button>
        </div>
    </form>

    <div class="chart-box">
        <canvas id="c"></canvas>
    </div>
</div>

<?php
/* =====================
   FILTER CHUNG
===================== */
$where = [];

if (!empty($_GET['from'])) {
    $where[] = "bookings.date >= '" . $_GET['from'] . "'";
}

if (!empty($_GET['to'])) {
    $where[] = "bookings.date <= '" . $_GET['to'] . "'";
}

/* =====================
   CHỌN DÃY
===================== */
$building_id = $_GET['building'] ?? "";

/* =====================
   SQL
===================== */
if (!empty($building_id)) {

    // 👉 CHỌN 1 DÃY → CHỈ PHÒNG TRONG DÃY ĐÓ
    $where[] = "rooms.building_id = " . (int)$building_id;

    $whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "SELECT rooms.name, COUNT(bookings.id) AS total
            FROM rooms
            LEFT JOIN bookings ON rooms.id = bookings.room_id
            $whereSQL
            GROUP BY rooms.id";
} else {

    // 👉 TẤT CẢ → GỘP THEO DÃY
    $whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "SELECT buildings.name, COUNT(bookings.id) AS total
            FROM buildings
            LEFT JOIN rooms ON buildings.id = rooms.building_id
            LEFT JOIN bookings ON rooms.id = bookings.room_id
            $whereSQL
            GROUP BY buildings.id";
}

$result = mysqli_query($conn, $sql);

$labels = [];
$values = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['name'];
        $values[] = (int)$row['total'];
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById("c");

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Số lượt đặt phòng',
                data: <?= json_encode($values) ?>,
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1',
                    '#20c997',
                    '#fd7e14'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: 'Thống kê số lượt đặt'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>