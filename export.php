<?php
include 'config.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=bookings.xls");

echo "Room\tDate\tStart\tEnd\n";

$res = mysqli_query($conn,"
SELECT rooms.name, bookings.*
FROM bookings JOIN rooms ON rooms.id=bookings.room_id
");

while($row=mysqli_fetch_assoc($res)){
echo $row['name']."\t".$row['date']."\t".$row['time_start']."\t".$row['time_end']."\n";
}
?>