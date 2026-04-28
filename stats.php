<?php include 'config.php'; ?>
<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="style.css">

<div class="container mt-4">
    <h3 class="text-center main-title ">Biểu đồ thống kê số lượt đặt phòng</h3>
    <div class="chart-box">
        <canvas id="c"></canvas>
    </div>
</div>

<?php
$sql = "SELECT rooms.name, COUNT(bookings.id) AS total
        FROM rooms
        LEFT JOIN bookings ON rooms.id = bookings.room_id
        GROUP BY rooms.id";

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
                    text: 'Thống kê số lượt đặt theo phòng'
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