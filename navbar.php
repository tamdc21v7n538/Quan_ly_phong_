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

        <a class="navbar-brand text-white fw-bold">Smart Booking</a>

        <div class="d-flex align-items-center gap-2">

            <?php if ($_SESSION['role'] == 'admin') { ?>
                <a href="dashboard.php" class="btn btn-light <?= $currentPage == 'dashboard.php' ? 'active' : ''; ?>">Trang chủ</a>
                <a href="rooms.php" class="btn btn-light <?= $currentPage == 'rooms.php' ? 'active' : ''; ?>">Phòng</a>
                <a href="booking.php" class="btn btn-light <?= $currentPage == 'booking.php' ? 'active' : ''; ?>">Đặt phòng</a>
                <a href="stats.php" class="btn btn-warning <?= $currentPage == 'stats.php' ? 'active' : ''; ?>">Thống kê</a>
            <?php } ?>

            <?php if ($_SESSION['role'] == 'user') { ?>
                <a href="dashboard.php" class="btn btn-light <?= $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                    Trang chủ
                </a>
            <?php } ?>

            <!-- ===== TRẠNG THÁI PHÒNG NÂNG CAO ===== -->
            <div class="dropdown ms-2">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    🏢 Trạng thái phòng
                </button>

                <div class="dropdown-menu dropdown-menu-end p-3" style="width:500px;">

                    <!-- filter -->
                    <select id="filterBuilding" class="form-select mb-2">
                        <option value="">Tất cả dãy</option>
                    </select>

                    <!-- table -->
                    <table class="table table-sm table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Dãy</th>
                                <th>Phòng</th>
                                <th>Trạng thái</th>
                                <th>Còn lại</th>
                            </tr>
                        </thead>
                        <tbody id="roomTable">
                            <tr>
                                <td colspan="4">Đang tải...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

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

            <!-- USER -->
            <div class="dropdown ms-2">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown">

                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['user']; ?>&background=0d6efd&color=fff"
                        width="30" height="30"
                        class="rounded-circle">

                    <span class="d-none d-md-inline">
                        <?= $_SESSION['user']; ?>
                    </span>

                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php">👤 Thông tin tài khoản</a></li>

                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <li><a class="dropdown-item text-primary" href="register.php">➕ Tạo admin</a></li>
                    <?php } ?>

                    <li><a class="dropdown-item text-danger" href="logout.php">🚪 Đăng xuất</a></li>
                </ul>
            </div>

        </div>
    </div>
</nav>

<!-- ===== SCRIPT TRẠNG THÁI ===== -->
<script>
    let allRooms = [];

    function loadRooms() {
        fetch('/classroom-booking/api_room_status.php')
            .then(res => res.json())
            .then(data => {

                allRooms = data;

                // fill filter
                let buildings = [...new Set(data.map(r => r.building))];
                let filter = document.getElementById("filterBuilding");

                filter.innerHTML = '<option value="">Tất cả dãy</option>';
                buildings.forEach(b => {
                    filter.innerHTML += `<option value="${b}">Dãy ${b}</option>`;
                });

                renderTable();
            });
    }

    function renderTable() {
        let filter = document.getElementById("filterBuilding").value;
        let html = "";

        allRooms
            .filter(r => !filter || r.building == filter)
            .forEach(room => {

                let status = room.status === 'active' ?
                    '<span class="text-success">Đang hoạt động</span>' :
                    '<span class="text-secondary">Trống</span>';

                let remain = room.remaining || "--";

                html += `
            <tr>
                <td>${room.building}</td>
                <td>${room.room_name}</td>
                <td>${status}</td>
                <td>
                    ${remain}
                    ${room.status === 'active' ? `<span title="Xem chi tiết" class="text-danger room-detail" data-id="${room.room_id || room.id}" style="cursor:pointer">❗</span>` : ''}
                </td>
            </tr>
            `;
            });

        document.getElementById("roomTable").innerHTML = html;
    }

    // filter change
    document.addEventListener("change", function(e) {
        if (e.target.id === "filterBuilding") {
            renderTable();
        }
    });

    // click detail
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("room-detail")) {

            let id = e.target.dataset.id;

            fetch("/classroom-booking/api_room_detail.php?room_id=" + id)
                .then(res => res.json())
                .then(data => {

                    if (data.status !== "success") {
                        alert(data.message || "Không có dữ liệu");
                        return;
                    }

                    const d = data.data;

                    alert(
                        "Phòng: " + d.room_name +
                        "\nNgười đặt: " + d.username +
                        "\nThời gian: " + d.time_start + " - " + d.time_end +
                        "\nLớp: " + d.class +
                        "\nSố SV: " + d.students +
                        "\nMục đích: " + d.purpose

                    );
                })
                .catch(err => {
                    console.log(err);
                    alert("Lỗi API chi tiết");
                });
        }
    });

    loadRooms();
    setInterval(loadRooms, 5000);
</script>

<!-- ===== NOTIFICATION SCRIPT ===== -->
<script>
    function loadNotify() {
        fetch("notify.php")
            .then(res => res.json())
            .then(data => {

                let read = JSON.parse(localStorage.getItem("readNotify") || "[]");
                let unread = data.filter(item => !read.includes(item.id));

                document.getElementById("notifyCount").innerText = unread.length;

                let html = data.length === 0 ?
                    "<li class='text-center text-muted'>Không có thông báo</li>" :
                    data.map(n => `
                    <li>
                        <a href="#" class="dropdown-item notify-item ${read.includes(n.id) ? "text-muted" : ""}" data-id="${n.id}">
                            ${n.message}
                        </a>
                    </li>
                `).join("");

                document.getElementById("notifyList").innerHTML = html;
            });
    }

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>