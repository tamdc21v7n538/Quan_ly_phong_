<?php
include 'config.php';
include 'session.php';

if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit();
}

$email = $_SESSION['user'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Không tìm thấy người dùng");
}

/* =========================
   AVATAR LOGIC
========================= */
$avatarPath = $user['avatar'] ?? '';

if (!empty($avatarPath) && file_exists("uploads/avatars/$avatarPath")) {
    $avatarUrl = "uploads/avatars/$avatarPath";
} else {
    $prefix = explode('@', $user['email'])[0];
    $initials = strtoupper(substr($prefix, 0, 2));

    $avatarUrl = "https://ui-avatars.com/api/?name=$initials&background=0d6efd&color=fff";
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-body text-center">

                    <!-- AVATAR -->
                    <img src="<?= $avatarUrl ?>"
                        class="rounded-circle mb-3"
                        width="100"
                        height="100">

                    <h4><?= htmlspecialchars($user['name']); ?></h4>

                    <p class="text-muted">
                        Email: <?= htmlspecialchars($user['email']); ?>
                    </p>

                    <p class="text-muted">
                        Vai trò: <?= htmlspecialchars($user['role']); ?>
                    </p>

                    <hr>

                    <!-- FORM UPLOAD -->
                    <form action="profile/upload_avatar.php"
                        method="POST"
                        enctype="multipart/form-data"
                        id="avatarForm">

                        <!-- input ẩn -->
                        <input type="file"
                            name="avatar"
                            id="avatarInput"
                            accept="image/*"
                            hidden
                            onchange="showFileName()">

                        <!-- nút mở file -->
                        <button type="button"
                            class="btn btn-primary w-100"
                            onclick="document.getElementById('avatarInput').click();">
                            📤 Chọn ảnh đại diện
                        </button>

                        <!-- tên file -->
                        <div id="fileName" class="mt-2 text-muted"></div>

                        <!-- nút submit (ẩn lúc đầu) -->
                        <button type="submit"
                            id="uploadBtn"
                            class="btn btn-success w-100 mt-2"
                            style="display:none;">
                            Upload ảnh
                        </button>

                    </form>

                    <hr>

                    <!-- ACTION -->
                    <div class="d-grid gap-2">

                        <a href="profile/change_password.php" class="btn btn-warning">
                            🔑 Đổi mật khẩu
                        </a>

                        <button class="btn btn-danger" onclick="confirmDelete()">
                            🗑 Xóa tài khoản
                        </button>

                        <a href="dashboard.php" class="btn btn-secondary">
                            ← Quay lại
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        if (confirm("Bạn có chắc muốn xóa tài khoản?")) {
            window.location.href = "profile/delete_account.php";
        }
    }

    // hiện tên file + nút upload
    function showFileName() {
        let input = document.getElementById("avatarInput");
        let fileName = document.getElementById("fileName");
        let uploadBtn = document.getElementById("uploadBtn");

        if (input.files.length > 0) {
            fileName.innerText = "📁 " + input.files[0].name;
            uploadBtn.style.display = "block";
        } else {
            fileName.innerText = "";
            uploadBtn.style.display = "none";
        }
    }
</script>