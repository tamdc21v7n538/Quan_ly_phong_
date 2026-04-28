<?php include 'config.php'; ?>
<?php include 'navbar.php'; ?>

<link rel="stylesheet" href="style.css">

<div class="container mt-5">
    <div class="card p-4 shadow-lg fade-in">
        <h3 class="text-center fw-bold mb-4 text-info">🚀 Đặt phòng </h3>

        <form id="form" method="POST" novalidate>

            <div class="input-group mb-2">
                <span class="input-group-text">👤</span>
                <input name="user_name" class="form-control" placeholder="Tên người đặt" required>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text">🏷️</span>
                <input list="classList" name="class" id="class" class="form-control" placeholder="Nhập hoặc chọn lớp" required>

                <datalist id="classList">
                    <?php
                    $resClass = mysqli_query($conn, "SELECT DISTINCT class FROM bookings WHERE class IS NOT NULL AND class != ''");
                    while ($c = mysqli_fetch_assoc($resClass)) {
                        echo "<option value='{$c['class']}'>";
                    }
                    ?>
                </datalist>
            </div>


            <div class="input-group mb-2">
                <span class="input-group-text">🏫</span>
                <select name="room" id="room" class="form-control" required>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM rooms");
                    while ($r = mysqli_fetch_assoc($res)) {
                        echo "<option value='{$r['id']}' data-cap='{$r['capacity']}'>
                                {$r['name']} (Tối đa {$r['capacity']} người)
                              </option>";
                    }
                    ?>
                </select>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text">👥</span>
                <input type="number" name="students" id="students" class="form-control" placeholder="Số lượng người" required>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text">🎯</span>
                <select name="purpose" class="form-control" required>
                    <option value="">-- Chọn mục đích --</option>
                    <option value="Học">Học</option>
                    <option value="Thi">Thi</option>
                    <option value="Họp">Họp</option>
                </select>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text">📅</span>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>

            <div class="row">
                <div class="col">
                    <input type="time" name="start" id="start" class="form-control mb-2" required>
                </div>
                <div class="col">
                    <input type="time" name="end" id="end" class="form-control mb-2" required>
                </div>
            </div>

            <textarea name="note" class="form-control mb-3" placeholder="Ghi chú"></textarea>

            <button type="submit" class="btn btn-success w-100">🚀 Đặt phòng</button>
        </form>

        <div id="preview" class="mt-3" style="display:none;">
            <div class="alert fade-in">
                📌 <b>Thông tin:</b><br>
                Phòng: <span id="p_room"></span><br>
                Thời gian: <span id="p_time"></span><br>
                ⏱ Thời lượng: <span id="p_duration" class="badge bg-dark"></span>
            </div>
        </div>

        <div id="suggest" class="mt-2"></div>
        <div id="calendarBox" class="mt-3"></div>
        <div id="msg"></div>
    </div>
    <!-- ===== NÚT ẨN/HIỆN LỊCH SỬ ===== -->
    <div class="text-center mt-4">
        <button class="btn btn-warning" onclick="toggleHistory()">
            📜 Xem lịch sử đặt phòng
        </button>
    </div>

    <!-- ===== KHUNG LỊCH SỬ ===== -->
    <div id="historyBox" style="display:none;" class="mt-4">

        <!-- bộ lọc -->
        <div class="row mb-3">
            <div class="col">
                <input type="date" id="filterDate" class="form-control">
            </div>
            <div class="col">
                <input type="text" id="searchName" class="form-control" placeholder="Tên người đặt">
            </div>
            <div class="col">
                <input type="text" id="searchRoom" class="form-control" placeholder="Tên phòng">
            </div>
            <div class="col">
                <button class="btn btn-primary w-100" onclick="loadHistory(1)">
                    🔍 Lọc
                </button>
            </div>
        </div>

        <!-- bảng -->
        <div id="historyTable"></div>

        <!-- phân trang -->
        <div id="pagination" class="text-center mt-2"></div>
    </div>
</div>

<script>
    let form = document.getElementById("form");
    let btn = form.querySelector("button");

    document.querySelectorAll("#form input, #form select").forEach(el => {
        el.addEventListener("change", showPreview);
    });

    function showPreview() {
        let room = document.getElementById("room");
        let start = document.getElementById("start").value;
        let end = document.getElementById("end").value;

        if (!start || !end) return;

        let roomName = room.options[room.selectedIndex].text;

        let t1 = new Date("1970-01-01T" + start);
        let t2 = new Date("1970-01-01T" + end);
        let hours = (t2 - t1) / (1000 * 60 * 60);

        document.getElementById("preview").style.display = "block";
        document.getElementById("p_room").innerText = roomName;
        document.getElementById("p_time").innerText = start + " → " + end;
        document.getElementById("p_duration").innerText = hours + " giờ";
    }

    document.getElementById("students").addEventListener("input", function() {
        let students = this.value;
        let room = document.getElementById("room");

        let best = "";
        for (let opt of room.options) {
            let cap = opt.dataset.cap;
            if (students <= cap) {
                best = opt.text;
                break;
            }
        }

        document.getElementById("suggest").innerHTML =
            best ? `<div class="alert alert-success">💡 Gợi ý: ${best}</div>` : "";
    });

    document.getElementById("date").addEventListener("change", function() {
        fetch("calendar_ajax.php?date=" + this.value)
            .then(r => r.text())
            .then(d => {
                document.getElementById("calendarBox").innerHTML = d;
            });
    });

    form.onsubmit = function(e) {
        e.preventDefault();

        let data = new FormData(form);
        let errors = [];

        let students = parseInt(data.get("students")) || 0;
        let room = document.getElementById("room");
        let capacity = parseInt(room.options[room.selectedIndex].dataset.cap) || 0;

        let now = new Date();
        let today = now.getFullYear() + "-" +
            String(now.getMonth() + 1).padStart(2, '0') + "-" +
            String(now.getDate()).padStart(2, '0');

        if (!data.get("user_name")) errors.push("Chưa nhập tên");
        if (!data.get("date")) errors.push("Chưa chọn ngày");
        if (!data.get("start")) errors.push("Chưa chọn giờ bắt đầu");
        if (!data.get("end")) errors.push("Chưa chọn giờ kết thúc");

        if (students > capacity) errors.push("Phòng không đủ sức chứa!");
        if (data.get("date") < today) errors.push("Ngày không hợp lệ");
        if (data.get("start") >= data.get("end")) errors.push("Giờ sai");

        let t1 = new Date("1970-01-01T" + data.get("start"));
        let t2 = new Date("1970-01-01T" + data.get("end"));
        let hours = (t2 - t1) / (1000 * 60 * 60);

        if (hours > 12) errors.push("Không được đặt quá 12 giờ");

        if (errors.length > 0) {
            show(errors.join("<br>"), "danger");
            return;
        }

        fetch("check_booking.php", {
                method: "POST",
                body: data
            })
            .then(r => r.text())
            .then(check => {
                if (check == "exist") {
                    show("❌ Trùng lịch phòng!", "danger");
                    return;
                }

                btn.disabled = true;
                btn.innerText = "Đang xử lý...";

                fetch("book_ajax.php", {
                        method: "POST",
                        body: data
                    })
                    .then(r => r.text())
                    .then(d => {
                        show("✅ " + d, "success");
                        form.reset();
                        document.getElementById("preview").style.display = "none";

                        btn.disabled = false;
                        btn.innerText = "Đặt phòng";
                    });
            });
    }

    function show(msg, type) {
        document.getElementById("msg").innerHTML =
            `<div class="alert alert-${type} mt-2">${msg}</div>`;
    }
    // ===== ẨN / HIỆN =====
    function toggleHistory() {
        let box = document.getElementById("historyBox");

        if (box.style.display === "none") {
            box.style.display = "block";

            // 👉 mở là load luôn (không cần lọc)
            loadHistory(1);

        } else {
            box.style.display = "none";
        }
    }

    // ===== LOAD LỊCH SỬ =====
    function loadHistory(page) {

        let date = document.getElementById("filterDate").value;
        let name = document.getElementById("searchName").value;
        let room = document.getElementById("searchRoom").value;

        fetch("history_ajax.php?page=" + page +
                "&date=" + date +
                "&name=" + name +
                "&room=" + room)
            .then(r => r.text())
            .then(d => {
                document.getElementById("historyTable").innerHTML = d;
            });

        //Thêm date name room cho phân trang không lặp dữ liệu
        fetch("history_page.php?page=" + page +
                "&date=" + date +
                "&name=" + name +
                "&room=" + room)
            .then(r => r.text())
            .then(d => {
                document.getElementById("pagination").innerHTML = d;
            });
    }

    //Lọc
    document.getElementById("filterDate").oninput = () => loadHistory(1);
    document.getElementById("searchName").oninput = () => loadHistory(1);
    document.getElementById("searchRoom").oninput = () => loadHistory(1);
</script>