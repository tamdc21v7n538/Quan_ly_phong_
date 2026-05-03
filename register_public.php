<?php
include 'config.php';
include 'session.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
//khung form giữa màn hình
<div class="container mt-5 col-md-4">
    //tạo bóng
    <div class="card p-4 shadow">
        <h3 class="text-center">Đăng ký</h3>

        <form method="POST">

            <input name="email" type="text" autocomplete="username" class="form-control mb-2" placeholder="Email" required value="">

            <!--ẩn ký tự-->
            <input name="password" type="password" autocomplete="new-password" class="form-control mb-2" placeholder="Password" required value="">

            <!-- CHỌN LỚP  -->
            <select name="class" class="form-control mb-2" required>
                <option value="">-- Chọn lớp --</option>
                <?php
                // lấy lớp từ DB bookings
                $classes = mysqli_query($conn, "
                    SELECT DISTINCT class 
                    FROM bookings 
                    WHERE class IS NOT NULL AND class != ''
                ");

                //lặp từng lớp để chọn
                while ($c = mysqli_fetch_assoc($classes)) {
                    echo "<option value='{$c['class']}'>{$c['class']}</option>";
                }
                ?>
            </select>

            <button name="register" class="btn btn-success w-100">Đăng ký</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">← Quay lại đăng nhập</a>
        </div>

        <?php
        // xử lý đăng ký, có mã hóa mật khẩu
        if (isset($_POST['register'])) {

            $email = safe($_POST['email']);
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $class = safe($_POST['class']);

            // CHECK EMAIL 
            $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

            if (mysqli_num_rows($check) > 0) {

                echo "<div class='alert alert-danger mt-2'>❌ Email đã tồn tại!</div>";
            } else {

                // CHECK LỚP CÓ TỒN TẠI 
                $checkClass = mysqli_query($conn, " SELECT DISTINCT class FROM bookings WHERE class = '$class'");

                //lớp k hợp lệ
                if (mysqli_num_rows($checkClass) == 0) {

                    echo "<div class='alert alert-danger mt-2'>❌ Lớp không tồn tại!</div>";
                } else {

                    //  INSERT USER 
                    $insert = mysqli_query($conn, "
                        INSERT INTO users(email,password,role,class) 
                        VALUES('$email','$pass','user','$class')
                    ");

                    if ($insert) {

                        header("location: login.php?success=1");
                        exit();
                    } else {

                        echo "<div class='alert alert-danger mt-2'>❌ Lỗi đăng ký!</div>";
                    }
                }
            }
        }
        ?>
    </div>
</div>