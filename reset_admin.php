<?php
include 'config.php';

$email = 'tamdc21v7n538@vlvh.ctu.edu.vn';
$newPass = password_hash('123456', PASSWORD_DEFAULT);

mysqli_query($conn, "UPDATE users SET password='$newPass' WHERE email='$email'");

echo "OK";
