<?php include 'config.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h3>Quản lý phòng</h3>

    <form method="POST" class="mb-3">
        <input name="name" class="form-control mb-2" placeholder="Tên phòng">
        <input name="capacity" class="form-control mb-2" placeholder="Sức chứa">
        <button name="add" class="btn btn-primary">Thêm</button>
    </form>

    <?php
    if (isset($_POST['add'])) {
        mysqli_query($conn, "INSERT INTO rooms(name,capacity) VALUES('{$_POST['name']}','{$_POST['capacity']}')");
    }
    ?>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Phòng</th>
            <th>Sức chứa</th>
        </tr>

        <?php
        $res = mysqli_query($conn, "SELECT * FROM rooms");
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
<td>{$row['id']}</td>
<td>{$row['name']}</td>
<td>{$row['capacity']}</td>
</tr>";
        }
        ?>
    </table>
</div>