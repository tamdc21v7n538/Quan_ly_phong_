<?php include 'config.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h3>Thống kê</h3>
    <canvas id="c"></canvas>
</div>

<?php
$data = mysqli_query($conn, "SELECT rooms.name,COUNT(bookings.id) total
FROM rooms LEFT JOIN bookings ON rooms.id=bookings.room_id
GROUP BY rooms.id");

$l = [];
$v = [];
while ($row = mysqli_fetch_assoc($data)) {
    $l[] = $row['name'];
    $v[] = $row['total'];
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById("c"), {
        type: 'bar',
        data: {
            labels: <?= json_encode($l) ?>,
            datasets: [{
                data: <?= json_encode($v) ?>
            }]
        }
    });
</script>