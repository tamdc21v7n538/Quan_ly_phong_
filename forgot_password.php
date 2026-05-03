<?php
include 'config.php';
include 'session.php';

include 'mail/mail_config.php';
include 'mail/mail_helper.php';

$error = "";
$success = false;

if (isset($_POST['reset'])) {

    $email = safe($_POST['email']);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {

        // tạo mật khẩu mới
        $newPass = substr(md5(rand()), 0, 8);
        $hashed = password_hash($newPass, PASSWORD_DEFAULT);

        mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE email='$email'");

        // gửi mail qua helper
        $subject = "Khôi phục mật khẩu";
        $body = "Mật khẩu mới của bạn là: <b>$newPass</b>";

        //gọi sendMail trog mail_helper.php
        if (sendMail($email, $subject, $body)) {
            $success = true;
        } else {
            $error = "Gửi mail thất bại!";
        }
    } else {
        $error = "Email không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background: #f1f3f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            width: 380px;
            border-radius: 15px;
            padding: 25px;
        }

        .icon-lock {
            font-size: 40px;
            color: #0d6efd;
        }

        .title {
            font-weight: bold;
            color: #0d6efd;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn {
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="card shadow">

        <div class="text-center mb-2">
            <i class="fa-solid fa-unlock-keyhole icon-lock"></i>
        </div>

        <h4 class="text-center title mb-3">Khôi phục mật khẩu</h4>

        <form method="POST">
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="Nhập email" required>
            </div>

            <button name="reset" class="btn btn-primary w-100">
                🔄 Gửi mật khẩu mới
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">← Quay lại đăng nhập</a>
        </div>

        <?php if ($success) { ?>
            <div class="alert alert-success mt-3 text-center">
                ✅ Mật khẩu mới đã gửi về email!
            </div>
        <?php } ?>

        <?php if ($error) { ?>
            <div class="alert alert-danger mt-3 text-center">
                ❌ <?= $error ?>
            </div>
        <?php } ?>

    </div>

</body>

</html>