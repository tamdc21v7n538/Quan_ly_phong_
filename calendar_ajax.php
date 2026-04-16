<!-- Hiển thị lịch dùng cho booking.php-->
<?php
include 'config.php';

$date = $_GET['date'];

$res = mysqli_query($conn, "SELECT * FROM bookings WHERE date='$date'");

echo "<div class='alert alert-warning'>📅 Lịch ngày $date</div>";

while ($r = mysqli_fetch_assoc($res)) {
    echo "<div class='badge bg-danger m-1'>
        {$r['time_start']} - {$r['time_end']}
    </div>";
}
