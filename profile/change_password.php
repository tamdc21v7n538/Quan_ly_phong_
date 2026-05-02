<?php
include __DIR__ . '/../config.php';
include '../session.php';

if (!isset($_SESSION['user'])) {
    header("location: ../login.php");
    exit();
}

$msg = "";
$email = $_SESSION['user'];

if (isset($_POST['submit'])) {

    $old = $_POST['old'];
    $new = $_POST['new'];

    // lấy user theo email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {

        // kiểm tra mật khẩu cũ
        if (password_verify($old, $user['password'])) {

            // mã hóa mật khẩu mới
            $newHash = password_hash($new, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $newHash, $email);

            if ($stmt->execute()) {
                $msg = "Đổi mật khẩu thành công!";
            } else {
                $msg = "Lỗi khi cập nhật!";
            }
        } else {
            $msg = "Sai mật khẩu cũ!";
        }
    } else {
        $msg = "Không tìm thấy người dùng!";
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <div class="col-md-5 mx-auto">
        <div class="card p-4 shadow">

            <h4 class="text-center">Đổi mật khẩu</h4>

            <?php if ($msg != "") { ?>
                <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
            <?php } ?>

            <form method="post">

                <input type="password" name="old" class="form-control mb-2" placeholder="Mật khẩu cũ" required>

                <input type="password" name="new" class="form-control mb-3" placeholder="Mật khẩu mới" required>

                <button name="submit" class="btn btn-primary w-100">
                    Cập nhật
                </button>

                <a href="../profile.php" class="btn btn-secondary w-100 mt-2">
                    Quay lại
                </a>

            </form>

        </div>
    </div>
</div>