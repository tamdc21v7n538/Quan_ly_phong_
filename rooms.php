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

    // ❌ kiểm tra rỗng
    if ($name == "" || $capacity == "") {
        echo "<script>
            alert('Vui lòng nhập đầy đủ tên phòng và sức chứa!');
            window.history.back();
        </script>";
        exit;
    }

    // ❌ kiểm tra số hợp lệ
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

<div class="container mt-4">
    <h3>Quản lý phòng</h3>

    <form method="POST" class="mb-3">
        <input name="name" class="form-control mb-2" placeholder="Tên phòng">
        <input name="capacity" class="form-control mb-2" placeholder="Sức chứa">
        <button name="add" class="btn btn-primary">Thêm</button>
    </form>


    <table class="table table-bordered">
        <tr>
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
        <td>" . $stt++ . "</td>
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
        }
        ?>
    </table>
    <hr>
    <div id="historyBox" style="display:none;">
        <h4>Lịch sử thêm / xóa phòng</h4>

        <table class="table table-striped">
            <tr>
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
    <button type="button" class="btn btn-info mb-3" onclick="toggleHistory()">
        📜 Ẩn/Hiện lịch sử thêm/xóa phòng
    </button>
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