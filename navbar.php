<?php
include 'session.php';

if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
    <div class="container">

        <!-- BRAND -->
        <a class="navbar-brand text-white fw-bold">Smart Booking</a>

        <div class="d-flex align-items-center gap-2">

            <!-- ===== ADMIN MENU ===== -->
            <?php if ($_SESSION['role'] == 'admin') { ?>

                <a href="dashboard.php" class="btn btn-light <?= $currentPage == 'dashboard.php' ? 'active' : ''; ?>">Trang chủ</a>

                <a href="rooms.php" class="btn btn-light <?= $currentPage == 'rooms.php' ? 'active' : ''; ?>">Phòng</a>

                <a href="booking.php" class="btn btn-light <?= $currentPage == 'booking.php' ? 'active' : ''; ?>">Đặt phòng</a>

                <a href="stats.php" class="btn btn-warning <?= $currentPage == 'stats.php' ? 'active' : ''; ?>">Thống kê</a>

            <?php } ?>

            <!-- ===== USER MENU ===== -->
            <?php if ($_SESSION['role'] == 'user') { ?>
                <a href="dashboard.php" class="btn btn-light <?= $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                    Trang chủ
                </a>
            <?php } ?>

            <!-- ===== NOTIFICATION ===== -->
            <div class="dropdown ms-2">
                <button class="btn btn-light position-relative" data-bs-toggle="dropdown">
                    🔔
                    <span id="notifyCount"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        0
                    </span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end" id="notifyList" style="width:300px;">
                    <li class="text-center text-muted">Đang tải...</li>
                </ul>
            </div>

            <!-- ===== USER AVATAR DROPDOWN ===== -->
            <div class="dropdown ms-2">

                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown">

                    <!-- avatar -->
                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['user']; ?>&background=0d6efd&color=fff"
                        width="30" height="30"
                        class="rounded-circle">

                    <span class="d-none d-md-inline">
                        <?= $_SESSION['user']; ?>
                    </span>

                </button>

                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <a class="dropdown-item" href="profile.php">
                            👤 Thông tin tài khoản
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <li>
                            <a class="dropdown-item text-primary" href="register.php">
                                ➕ Tạo admin
                            </a>
                        </li>
                    <?php } ?>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item text-danger" href="logout.php">
                            🚪 Đăng xuất
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </div>
</nav>

<!-- ===== NOTIFICATION SCRIPT ===== -->
<script>
    function loadNotify() {
        fetch("notify.php")
            .then(res => res.json())
            .then(data => {

                let read = JSON.parse(localStorage.getItem("readNotify") || "[]");

                let unread = data.filter(item => !read.includes(item.id));

                document.getElementById("notifyCount").innerText = unread.length;

                let html = "";

                if (data.length === 0) {
                    html = "<li class='text-center text-muted'>Không có thông báo</li>";
                } else {
                    data.forEach(n => {

                        let isRead = read.includes(n.id) ? "text-muted" : "";

                        html += `
                        <li>
                            <a href="#" class="dropdown-item notify-item ${isRead}" data-id="${n.id}">
                                ${n.message}
                            </a>
                        </li>
                    `;
                    });
                }

                document.getElementById("notifyList").innerHTML = html;
            });
    }

    // click mark as read
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("notify-item")) {

            e.preventDefault();

            let id = parseInt(e.target.dataset.id);

            let read = JSON.parse(localStorage.getItem("readNotify") || "[]");

            if (!read.includes(id)) {
                read.push(id);
                localStorage.setItem("readNotify", JSON.stringify(read));
            }

            e.target.classList.add("text-muted");

            let count = document.getElementById("notifyCount");
            count.innerText = Math.max(0, parseInt(count.innerText) - 1);
        }
    });

    loadNotify();
    setInterval(loadNotify, 2000);
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>