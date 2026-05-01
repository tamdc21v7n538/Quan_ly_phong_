<?php
include 'config.php';

// ===== PHÂN TRANG =====
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// ===== PHÂN TRANG HISTORY =====
$limit_his = 10;
$page_his = isset($_GET['page_his']) ? (int)$_GET['page_his'] : 1;
$start_his = ($page_his - 1) * $limit_his;


// ===== THÊM PHÒNG =====
if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $capacity = trim($_POST['capacity']);
    $building_name = trim($_POST['building_name']);

    if ($name == "" || $capacity == "" || $building_name == "") {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!');history.back();</script>";
        exit;
    }

    if (!is_numeric($capacity)) {
        echo "<script>alert('Sức chứa phải là số!');history.back();</script>";
        exit;
    }

    $check = mysqli_query($conn, "SELECT id FROM buildings WHERE name='$building_name'");

    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $building_id = $row['id'];
    } else {
        mysqli_query($conn, "INSERT INTO buildings(name) VALUES('$building_name')");
        $building_id = mysqli_insert_id($conn);
    }

    mysqli_query($conn, "INSERT INTO rooms(name,capacity,building_id) 
                         VALUES('$name','$capacity','$building_id')");

    mysqli_query($conn, "INSERT INTO room_logs(action,room_name,building_id) 
                         VALUES('Thêm','$name','$building_id')");

    echo "<script>alert('Thêm phòng thành công!');window.location='rooms.php';</script>";
    exit;
}


// ===== SỬA PHÒNG =====
if (isset($_POST['update'])) {

    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $capacity = trim($_POST['capacity']);
    $building_name = trim($_POST['building_name']);

    if ($name == "" || $capacity == "" || $building_name == "") {
        echo "<script>alert('Nhập đủ thông tin!');history.back();</script>";
        exit;
    }

    if (!is_numeric($capacity)) {
        echo "<script>alert('Sức chứa phải là số!');history.back();</script>";
        exit;
    }

    $check = mysqli_query($conn, "SELECT id FROM buildings WHERE name='$building_name'");

    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $building_id = $row['id'];
    } else {
        mysqli_query($conn, "INSERT INTO buildings(name) VALUES('$building_name')");
        $building_id = mysqli_insert_id($conn);
    }

    mysqli_query($conn, "UPDATE rooms 
                         SET name='$name', capacity='$capacity', building_id='$building_id'
                         WHERE id=$id");

    echo "<script>alert('Cập nhật thành công!');window.location='rooms.php';</script>";
    exit;
}


// ===== XÓA =====
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $r = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT name, building_id FROM rooms WHERE id=$id
    "));
    $name = $r['name'];
    $building_id = $r['building_id'];

    mysqli_query($conn, "DELETE FROM rooms WHERE id=$id");

    mysqli_query($conn, "INSERT INTO room_logs(action,room_name,building_id) 
                         VALUES('Xóa','$name','$building_id')");

    echo "<script>alert('Xóa phòng thành công!');window.location='rooms.php';</script>";
    exit;
}

include 'navbar.php';
?>

<link rel="stylesheet" href="style.css">

<div class="container mt-4">

    <h2 class="text-center mb-4 fw-bold text-primary">
        Quản lý phòng
    </h2>

    <!-- ===== FORM ===== -->
    <div class="d-flex justify-content-center align-items-center gap-4 mb-4">

        <div class="d-none d-md-block text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="100">
        </div>

        <div class="card shadow p-4" style="max-width:500px; width:100%; border-radius:15px;">
            <h4 class="text-center text-success mb-3">
                Thêm phòng học
            </h4>

            <form method="POST">

                <input list="buildingList" name="building_name"
                    class="form-control mb-2"
                    placeholder="Nhập hoặc chọn tòa (vd: A1)">

                <datalist id="buildingList">
                    <?php
                    $b = mysqli_query($conn, "SELECT * FROM buildings");
                    while ($row = mysqli_fetch_assoc($b)) {
                        echo "<option value='{$row['name']}'>";
                    }
                    ?>
                </datalist>

                <input name="name" class="form-control mb-2 text-primary" placeholder="Tên phòng">
                <input name="capacity" class="form-control mb-3 text-primary" placeholder="Sức chứa">

                <button name="add" class="btn btn-success w-100">
                    ➕ Thêm
                </button>
            </form>
        </div>

        <div class="d-none d-md-block text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" width="100">
        </div>

    </div>

    <!-- ===== LỌC ===== -->
    <div class="mb-3 text-center">
        <select id="filterBuilding" class="form-select w-50 mx-auto">
            <option value="">-- Lọc theo tòa nhà --</option>
            <?php
            $b = mysqli_query($conn, "SELECT * FROM buildings");
            while ($row = mysqli_fetch_assoc($b)) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select>
    </div>

    <!-- ===== TABLE ===== -->
    <h4 class="text-center text-white bg-primary p-2 rounded">
        Danh sách phòng
    </h4>

    <div class="card shadow p-3 mb-3">

        <table class="table table-bordered text-center align-middle">
            <tr class="table-success">
                <th>STT</th>
                <th>Tòa nhà</th>
                <th>Phòng</th>
                <th>Sức chứa</th>
                <th>Hành động</th>
            </tr>

            <tbody id="roomTable">
                <?php
                $stt = $start + 1;

                $res = mysqli_query($conn, "
                SELECT rooms.*, buildings.name AS building_name
                FROM rooms
                LEFT JOIN buildings ON rooms.building_id = buildings.id
                LIMIT $start, $limit
                ");

                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr data-building='{$row['building_id']}'>
                    <td>{$stt}</td>
                    <td>{$row['building_name']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['capacity']}</td>
                    <td>

                    <button class='btn btn-warning btn-sm'
                    onclick=\"editRoom('{$row['id']}','{$row['name']}','{$row['capacity']}','{$row['building_name']}')\">
                    Sửa
                    </button>

                    <a href='rooms.php?delete={$row['id']}'
                    class='btn btn-danger btn-sm'
                    onclick='return confirm(\"Xóa phòng này?\")'>
                    Xóa
                    </a>

                    </td>
                    </tr>";
                    $stt++;
                }
                ?>
            </tbody>
        </table>



        <!-- ===== PHÂN TRANG ===== -->
        <?php
        $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM rooms"))['total'];
        $pages = ceil($total / $limit);
        ?>

        <div class="text-center mt-3">
            <?php for ($i = 1; $i <= $pages; $i++) { ?>
                <a href="rooms.php?page=<?= $i ?>"
                    class="btn btn-sm <?= ($i == $page) ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= $i ?>
                </a>
            <?php } ?>
        </div>

    </div>

    <!-- ===== EDIT FORM ===== -->
    <div id="editBox" style="display:none;" class="card shadow p-3">
        <h5 class="text-center text-warning">Sửa phòng</h5>

        <form method="POST">
            <input type="hidden" name="id" id="edit_id">

            <input list="buildingList" name="building_name" id="edit_building"
                class="form-control mb-2">

            <input name="name" id="edit_name" class="form-control mb-2">
            <input name="capacity" id="edit_capacity" class="form-control mb-3">

            <button name="update" class="btn btn-warning w-100">Cập nhật</button>
        </form>
    </div>

    <!-- ===== HISTORY ===== -->
    <div class="text-center mb-3">
        <button class="btn btn-primary" onclick="toggleHistory()">
            📜 Ẩn/Hiện lịch sử
        </button>
    </div>

    <div id="historyBox" style="display:none;">
        <div class="card shadow p-3">
            <table class="table table-bordered text-center">
                <tr>
                    <th>Hành động</th>
                    <th>Tòa nhà</th>
                    <th>Phòng</th>
                    <th>Thời gian</th>
                </tr>

                <?php
                //LIMIT phân trang
                $logs = mysqli_query($conn, "
                SELECT room_logs.*, buildings.name AS building_name
                FROM room_logs
                LEFT JOIN buildings ON room_logs.building_id = buildings.id
                ORDER BY room_logs.id DESC
                LIMIT $start_his, $limit_his 
                ");

                while ($log = mysqli_fetch_assoc($logs)) {
                    echo "<tr>
                        <td>{$log['action']}</td>
                        <td>{$log['building_name']}</td>
                        <td>{$log['room_name']}</td>
                        <td>{$log['created_at']}</td>
                    </tr>";
                }
                ?>
            </table>
            <!-- ===== PHÂN TRANG LỊCH SỬ ===== -->
            <?php
            $total_his = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM room_logs"))['total'];
            $pages_his = ceil($total_his / $limit_his);
            ?>

            <div class="text-center mt-3">
                <?php for ($i = 1; $i <= $pages_his; $i++) { ?>
                    <a href="rooms.php?page=<?= $page ?>&page_his=<?= $i ?>"
                        class="btn btn-sm <?= ($i == $page_his) ? 'btn-success' : 'btn-outline-success' ?>">
                        <?= $i ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

</div>

<script>
    document.getElementById("filterBuilding").addEventListener("change", function() {
        let val = this.value;
        let rows = document.querySelectorAll("#roomTable tr");

        rows.forEach(row => {
            row.style.display = (val === "" || row.dataset.building === val) ? "" : "none";
        });
    });

    function toggleHistory() {
        let box = document.getElementById("historyBox");
        box.style.display = (box.style.display === "none") ? "block" : "none";
    }

    function editRoom(id, name, capacity, building) {
        document.getElementById("editBox").style.display = "block";
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_capacity").value = capacity;
        document.getElementById("edit_building").value = building;

        window.scrollTo({
            top: document.body.scrollHeight,
            behavior: 'smooth'
        });
    }

    //KHÔNG CẦN ẤN ẨN HIỆN KHI CHUYỂN TRANG LỊCH SỬ
    function toggleHistory() {
        let box = document.getElementById("historyBox");

        if (box.style.display === "none") {
            box.style.display = "block";
            localStorage.setItem("history_open", "1");
        } else {
            box.style.display = "none";
            localStorage.setItem("history_open", "0");
        }
    }

    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);

        // ưu tiên mở nếu có phân trang
        if (urlParams.has('page_his') || localStorage.getItem("history_open") === "1") {
            document.getElementById("historyBox").style.display = "block";
        }
    };
</script>