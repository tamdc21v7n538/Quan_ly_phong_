<?php
include __DIR__ . '/../config.php';
include '../session.php';

if (!isset($_SESSION['user'])) {
    header("location: ../login.php");
    exit();
}

$email = $_SESSION['user'];

// xóa bằng prepared statement tạo câu SQL xóa user
$stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
// nếu lệnh sql chạy thành công
if ($stmt->execute()) {

    session_destroy();

    header("location: ../login.php");
    exit();
} else {
    echo "Lỗi khi xóa tài khoản!";
}
