<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- ===== Xem ở trang nào ===== -->
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<style>
    .nav-link-custom {
        position: relative;
        margin-right: 5px;
        transition: 0.2s;
    }

    .nav-link-custom::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 0;
        height: 3px;
        background: #0d6efd;
        transition: 0.3s;
        border-radius: 5px;
    }

    .nav-link-custom.active::after {
        width: 100%;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand">Smart Booking</a>

        <div>

            <a href="dashboard.php"
                class="btn btn-light nav-link-custom <?= $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                Trang chủ
            </a>

            <a href="rooms.php"
                class="btn btn-light nav-link-custom <?= $currentPage == 'rooms.php' ? 'active' : ''; ?>">
                Phòng
            </a>

            <a href="booking.php"
                class="btn btn-light nav-link-custom <?= $currentPage == 'booking.php' ? 'active' : ''; ?>">
                Đặt phòng
            </a>

            <a href="stats.php"
                class="btn btn-warning nav-link-custom <?= $currentPage == 'stats.php' ? 'active' : ''; ?>">
                Thống kê
            </a>

            <a href="logout.php"
                class="btn btn-danger nav-link-custom <?= $currentPage == 'logout.php' ? 'active' : ''; ?>">
                Đăng xuất
            </a>

            <span id="notify" class="badge bg-danger ms-2">0</span>

        </div>
    </div>
</nav>