<!-- check trùng giờ dùng cho booking.php-->
<?php
include 'config.php';

$room = $_POST['room'];
$date = $_POST['date'];
$start = $_POST['start'];
$end = $_POST['end'];

$q = mysqli_query($conn, "
SELECT * FROM bookings 
WHERE room_id='$room' 
AND date='$date'
AND (time_start < '$end' AND time_end > '$start')
");

echo mysqli_num_rows($q) > 0 ? "exist" : "ok";
