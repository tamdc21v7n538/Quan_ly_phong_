<?php
include 'config.php';
include 'session.php';

//  KIỂM TRA LOGIN, chưa  
if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit;
}

// KIỂM TRA QUYỀN 
if ($_SESSION['role'] != 'admin') {
    die("❌ Không có quyền!");
}

// XỬ LÝ FORM  bấm nút tạo tk
if (isset($_POST['register'])) {
    //lấy email safe lọc dl
    $email = safe($_POST['email']);
    //mã hóa pass
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    //lấy quyền
    $role = $_POST['role'];

    // 👉 class chỉ cần khi là user
    $class = isset($_POST['class']) ? safe($_POST['class']) : '';

    //  CHECK EMAIL tránh trùng
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    //nếu đã tồn tại- báo lỗi
    if (mysqli_num_rows($check) > 0) {
        header("Location: register.php?error=exists");
        exit;
    }

    //NẾU USER → CHECK LỚP 
    if ($role == 'user') {

        //bắt buộc chọn lớp
        if (empty($class)) {
            header("Location: register.php?error=noclass");
            exit;
        }
        //Ktra lớp có tồn tại
        $checkClass = mysqli_query($conn, "
            SELECT DISTINCT class 
            FROM bookings 
            WHERE class = '$class'
        ");

        //lớp không hợp lệ
        if (mysqli_num_rows($checkClass) == 0) {
            header("Location: register.php?error=invalidclass");
            exit;
        }

        // lưu USER + CLASS
        $result = mysqli_query($conn, "
            INSERT INTO users(email,password,role,class) 
            VALUES('$email','$pass','$role','$class')
        ");
    } else {

        // admin không cần lớp
        $result = mysqli_query($conn, "
            INSERT INTO users(email,password,role) 
            VALUES('$email','$pass','$role')
        ");
    }

    //insert thành công
    if ($result) {
        header("Location: register.php?success=1");
        exit;
    } else {
        header("Location: register.php?error=db");
        exit;
    }
}

include 'navbar.php';
?>

<link rel="stylesheet" href="style.css">

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title">Tạo tài khoản</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


        <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container mt-5 col-md-5">
        <div class="card p-4 shadow">

            <h3 class="text-center text-primary mb-3">👤 Tạo tài khoản</h3>

            <!-- THÔNG BÁO -->
            <?php if (isset($_GET['success'])) { ?>
                <div class="alert alert-success">✅ Tạo tài khoản thành công!</div>
            <?php } ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'exists') { ?>
                <div class="alert alert-danger">❌ Email đã tồn tại!</div>
            <?php } ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'noclass') { ?>
                <div class="alert alert-danger">❌ Chưa chọn lớp!</div>
            <?php } ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'invalidclass') { ?>
                <div class="alert alert-danger">❌ Lớp không tồn tại!</div>
            <?php } ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'db') { ?>
                <div class="alert alert-danger">❌ Lỗi database!</div>
            <?php } ?>

            <!-- FORM -->
            <form method="POST" autocomplete="off">

                <!-- EMAIL -->
                <input name="email" type="text" autocomplete="username"
                    class="form-control mb-2" placeholder="Email" required value="">

                <!-- PASSWORD -->
                <div class="input-group mb-2">
                    <input name="password" type="password" id="pass"
                        autocomplete="new-password"
                        class="form-control" placeholder="Password" required value="">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">👁</button>
                </div>

                <!-- ROLE -->
                <select name="role" id="role" class="form-control mb-2">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>

                <!-- ===== CHỌN LỚP (ẨN BAN ĐẦU) ===== -->
                <select name="class" id="classBox" class="form-control mb-3" style="display:none;">
                    <option value="">-- Chọn lớp --</option>
                    <?php
                    $classes = mysqli_query($conn, "
                    SELECT DISTINCT class 
                    FROM bookings 
                    WHERE class IS NOT NULL AND class != ''
                ");
                    while ($c = mysqli_fetch_assoc($classes)) {
                        echo "<option value='{$c['class']}'>{$c['class']}</option>";
                    }
                    ?>
                </select>

                <button name="register" class="btn btn-success w-100">
                    Tạo tài khoản
                </button>

            </form>

        </div>
    </div>

    <script>
        // 👁 show/hide pass
        function togglePass() {
            const p = document.getElementById("pass");
            p.type = (p.type === "password") ? "text" : "password";
        }

        // 🔥 HIỆN CLASS KHI CHỌN USER
        const role = document.getElementById("role");
        const classBox = document.getElementById("classBox");

        role.addEventListener("change", function() {
            if (this.value === "user") {
                classBox.style.display = "block";
            } else {
                classBox.style.display = "none";
                classBox.value = "";
            }
        });

        // load lần đầu
        if (role.value === "user") {
            classBox.style.display = "block";
        }
    </script>

</body>

</html>