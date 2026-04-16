<?php include 'config.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5 col-md-4">
    <div class="card p-4 shadow">
        <h3>Login</h3>

        <form method="POST">
            <input name="email" class="form-control mb-2" placeholder="Email">
            <input name="password" type="password" class="form-control mb-2" placeholder="Password">
            <button name="login" class="btn btn-primary w-100">Login</button>
        </form>

        <?php
        if (isset($_POST['login'])) {
            $email = safe($_POST['email']);
            $pass = md5($_POST['password']);

            $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pass'");

            if (mysqli_num_rows($res) > 0) {
                $user = mysqli_fetch_assoc($res);
                $_SESSION['user'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                header("location: dashboard.php");
            } else {
                echo "<div class='alert alert-danger mt-2'>Sai tài khoản!</div>";
            }
        }
        ?>
    </div>
</div>