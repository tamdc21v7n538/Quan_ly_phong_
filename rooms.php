<?php include 'config.php'; ?>
<?php include 'navbar.php'; ?>

<?php if (isset($_GET['msg'])) { ?>
    <script>
        alert(
            <?php
            if ($_GET['msg'] == 'add') echo '"Thêm phòng thành công!"';
            if ($_GET['msg'] == 'delete') echo '"Xóa phòng thành công!"';
            ?>
        );

        // 👉 xóa msg khỏi URL để không bị lặp khi reload
        window.history.replaceState(null, null, "rooms.php");
    </script>
<?php } ?>
<?php

if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $capacity = trim($_POST['capacity']);

    //  kiểm tra rỗng
    if ($name == "" || $capacity == "") {
        echo "<script>
            alert('Vui lòng nhập đầy đủ tên phòng và sức chứa!');
            window.history.back();
        </script>";
        exit;
    }

    //  kiểm tra số hợp lệ
    if (!is_numeric($capacity)) {
        echo "<script>
            alert('Sức chứa phải là số!');
            window.history.back();
        </script>";
        exit;
    }

    // ✔ insert
    mysqli_query($conn, "INSERT INTO rooms(name,capacity) VALUES('$name','$capacity')");

    mysqli_query($conn, "INSERT INTO room_logs(action,room_name) VALUES('Thêm','$name')");

    header("Location: rooms.php?msg=add");
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // lấy tên phòng trước khi xóa
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM rooms WHERE id=$id"));
    $name = $r['name'];

    mysqli_query($conn, "DELETE FROM rooms WHERE id=$id");

    // Lịch sử thêm xóa
    mysqli_query($conn, "INSERT INTO room_logs(action,room_name) VALUES('Xóa','$name')");

    header("Location: rooms.php?msg=delete");
    exit;
}
?>
<link rel="stylesheet" href="style.css">

<div class="container mt-4">

    <!-- ===== TIÊU ĐỀ ===== -->
    <h2 class="text-center mb-4 fw-bold text-primary">
        Quản lý phòng
    </h2>

    <!-- khoảng cách -->
    <div class="mb-4"></div>

    <!-- ===== FORM THÊM PHÒNG ===== -->
    <div class="d-flex justify-content-center align-items-center gap-4 mb-4">

        <!-- ICON TRÁI -->
        <div class="d-none d-md-block text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="100">
        </div>

        <!-- FORM -->
        <div class="card shadow p-4" style="max-width:500px; width:100%; border-radius:15px;">
            <h4 class="text-center text-success mb-3">
                Thêm phòng học
            </h4>

            <form method="POST">
                <input name="name" class="form-control mb-2 text-primary" placeholder="Tên phòng">
                <input name="capacity" class="form-control mb-3 text-primary" placeholder="Sức chứa">

                <button name="add" class="btn btn-success w-100">
                    ➕ Thêm
                </button>
            </form>
        </div>

        <!-- ICON PHẢI -->
        <div class="d-none d-md-block text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" width="100">
        </div>

    </div>

    <!-- ===== BẢNG DANH SÁCH ===== -->
    <h4 class="text-center text-white bg-primary p-2 rounded">
        Danh sách phòng
    </h4>

    <div class="card shadow p-3 mb-3">

        <table class="table table-bordered text-center align-middle">
            <tr class="table-success">
                <th>STT</th>
                <th>Phòng</th>
                <th>Sức chứa</th>
                <th>Hành động</th>
            </tr>

            <?php
            $stt = 1;
            $res = mysqli_query($conn, "SELECT * FROM rooms");

            while ($row = mysqli_fetch_assoc($res)) {
                echo "<tr>
                    <td>{$stt}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['capacity']}</td>
                    <td>
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
        </table>
    </div>

    <!-- ===== NÚT LỊCH SỬ ===== -->
    <div class="text-center mb-3">
        <button type="button" class="btn btn-primary" onclick="toggleHistory()">
            📜 Ẩn/Hiện lịch sử thêm/xóa phòng
        </button>
    </div>

    <!-- ===== LỊCH SỬ ===== -->
    <div id="historyBox" style="display:none;">

        <div class="card shadow p-3">

            <h5 class="text-center text-secondary mb-3">
                Lịch sử hoạt động
            </h5>

            <table class="table table-striped text-center">
                <tr class="table-warning">
                    <th>Hành động</th>
                    <th>Phòng</th>
                    <th>Thời gian</th>
                </tr>

                <?php
                $logs = mysqli_query($conn, "SELECT * FROM room_logs ORDER BY id DESC");

                while ($log = mysqli_fetch_assoc($logs)) {
                    echo "<tr>
                        <td>{$log['action']}</td>
                        <td>{$log['room_name']}</td>
                        <td>{$log['created_at']}</td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>

</div>
<script>
    function toggleHistory() {
        var box = document.getElementById("historyBox");

        if (box.style.display === "none") {
            box.style.display = "block";
        } else {
            box.style.display = "none";
        }
    }
</script>