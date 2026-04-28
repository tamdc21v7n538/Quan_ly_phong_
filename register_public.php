<?php
include 'config.php';
include 'session.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<div class="container mt-5 col-md-4">
    <div class="card p-4 shadow">
        <h3 class="text-center">Đăng ký</h3>

        <form method="POST">
            <input name="email" class="form-control mb-2" placeholder="Email" required>
            <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>
            <button name="register" class="btn btn-success w-100">Đăng ký</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">← Quay lại đăng nhập</a>
        </div>

        <?php
        if (isset($_POST['register'])) {
            $email = safe($_POST['email']);
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // kiểm tra email tồn tại
            $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

            if (mysqli_num_rows($check) > 0) {
                echo "<div class='alert alert-danger mt-2'>Email đã tồn tại!</div>";
            } else {
                $insert = mysqli_query($conn, "INSERT INTO users(email,password,role) 
                                              VALUES('$email','$pass','user')");

                if ($insert) {
                    // 🔥 chuyển về login
                    header("location: login.php?success=1");
                    exit();
                } else {
                    echo "<div class='alert alert-danger mt-2'>Lỗi đăng ký!</div>";
                }
            }
        }
        ?>
    </div>
</div>