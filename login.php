<?php
include 'config.php';
include 'session.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS custom -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">

    <div class="card login-card shadow">
        <h3 class="text-center mb-3">🔐 Login</h3>

        <form method="POST">
            <input name="email" class="form-control mb-3" placeholder="Email" required>

            <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>

            <!-- QUÊN MẬT KHẨU -->
            <div class="text-end mb-3">
                <a href="forgot_password.php" class="forgot-link text-primary">
                    Quên mật khẩu?
                </a>
            </div>

            <button name="login" class="btn btn-primary w-100">Login</button>
        </form>

        <!-- ĐĂNG KÝ -->
        <div class="text-center mt-3">
            <a href="register_public.php" class="btn btn-outline-secondary w-100">
                Tạo tài khoản
            </a>
        </div>

        <?php
        if (isset($_POST['login'])) {
            $email = safe($_POST['email']);
            $pass = $_POST['password'];

            $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

            if (mysqli_num_rows($res) > 0) {
                $user = mysqli_fetch_assoc($res);

                if (password_verify($pass, $user['password'])) {

                    $_SESSION['user'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    header("location: dashboard.php");
                    exit();
                } else {
                    echo "<div class='alert alert-danger mt-3 text-center'>Sai mật khẩu!</div>";
                }
            } else {
                echo "<div class='alert alert-danger mt-3 text-center'>Email không tồn tại!</div>";
            }
        }
        ?>
    </div>

</body>

</html>