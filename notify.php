<?php
include 'config.php';
$c = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM bookings"));
echo $c[0];
