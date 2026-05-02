<?php
include __DIR__ . '/../config.php';
include '../session.php';

if (!isset($_SESSION['user'])) {
    header("location: ../login.php");
    exit();
}

$email = $_SESSION['user'];

if (!isset($_FILES['avatar'])) {
    die("Không có file");
}

$file = $_FILES['avatar'];

// kiểm tra lỗi
if ($file['error'] != 0) {
    die("Upload lỗi");
}

// tạo tên file
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newName = uniqid() . "." . $ext;

// thư mục lưu
$dir = "../uploads/avatars/";
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// move file
move_uploaded_file($file['tmp_name'], $dir . $newName);

// update DB
$stmt = $conn->prepare("UPDATE users SET avatar=? WHERE email=?");
$stmt->bind_param("ss", $newName, $email);
$stmt->execute();

header("Location: ../profile.php");
exit();
