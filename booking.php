<?php include 'config.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="card p-4 shadow">
        <h3>Đặt phòng</h3>

        <form id="form">
            <select name="room" class="form-control mb-2">
                <?php
                $res = mysqli_query($conn, "SELECT * FROM rooms");
                while ($r = mysqli_fetch_assoc($res)) {
                    echo "<option value='{$r['id']}'>{$r['name']}</option>";
                }
                ?>
            </select>

            <input type="date" name="date" class="form-control mb-2">
            <input type="time" name="start" class="form-control mb-2">
            <input type="time" name="end" class="form-control mb-2">

            <button class="btn btn-success w-100">Đặt phòng</button>
        </form>

        <div id="msg"></div>
    </div>
</div>

<script>
    document.getElementById("form").onsubmit = function(e) {
        e.preventDefault();

        fetch("book_ajax.php", {
                method: "POST",
                body: new FormData(this)
            })
            .then(r => r.text())
            .then(d => {
                document.getElementById("msg").innerHTML = "<div class='alert alert-info mt-2'>" + d + "</div>";
            });
    }
</script>