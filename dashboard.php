<?php include 'config.php'; ?>
<?php if (!isset($_SESSION['user'])) header("location: login.php"); ?>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h3>Dashboard</h3>

    <?php
    $r = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM rooms"));
    $b = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM bookings"));

    echo "<div class='alert alert-info'>Rooms: $r[0]</div>";
    echo "<div class='alert alert-success'>Bookings: $b[0]</div>";
    ?>

    <a href="export.php" class="btn btn-success">Xuất Excel</a>
</div>

<script>
    setInterval(() => {
        fetch("notify.php")
            .then(res => res.text())
            .then(data => {
                document.getElementById("notify").innerText = data;
            });
    }, 2000);
</script>