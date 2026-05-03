<?php
$conn = mysqli_connect("127.0.0.1", "root", "", "booking", 3307);
session_start();

function safe($data)
{
    //nối với PHP
    global $conn;
    return mysqli_real_escape_string($conn, $data);
}
