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

            <!-- ===== CHỌN DÃY (THÊM) ===== -->
            <div class="input-group mb-2">
                <span class="input-group-text">🏢</span>
                <select id="building" class="form-control">
                    <option value="">-- Chọn dãy --</option>
                    <?php
                    $b = mysqli_query($conn, "SELECT * FROM buildings");
                    while ($row = mysqli_fetch_assoc($b)) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- ===== PHÒNG (GIỮ NGUYÊN + THÊM data-building) ===== -->
            <div class="input-group mb-2">
                <span class="input-group-text">🏫</span>
                <select name="room" id="room" class="form-control" required disabled>
                    <option value="">-- Chọn dãy trước --</option>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM rooms");
                    while ($r = mysqli_fetch_assoc($res)) {
                        echo "<option value='{$r['id']}' 
                              data-cap='{$r['capacity']}'
                              data-building='{$r['building_id']}'>
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

    <!-- ===== LỊCH SỬ (GIỮ NGUYÊN) ===== -->
    <div class="text-center mt-4">
        <button class="btn btn-warning" onclick="toggleHistory()">
            📜 Xem lịch sử đặt phòng
        </button>
    </div>

    <div id="historyBox" style="display:none;" class="mt-4">

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

        <div id="historyTable"></div>
        <div id="pagination" class="text-center mt-2"></div>
    </div>
</div>

<script>
    let form = document.getElementById("form");
    let btn = form.querySelector("button");
    let room = document.getElementById("room");
    let building = document.getElementById("building");

    // ===== CHỌN DÃY -> LỌC PHÒNG =====
    building.addEventListener("change", function() {
        let val = this.value;

        room.disabled = !val;
        room.value = "";

        for (let opt of room.options) {
            if (!opt.value) continue;

            opt.style.display = (opt.dataset.building === val) ? "block" : "none";
        }
    });

    document.querySelectorAll("#form input, #form select").forEach(el => {
        el.addEventListener("change", showPreview);
    });

    function showPreview() {
        let start = document.getElementById("start").value;
        let end = document.getElementById("end").value;

        if (!start || !end || !room.value) return;

        let roomName = room.options[room.selectedIndex].text;

        let t1 = new Date("1970-01-01T" + start);
        let t2 = new Date("1970-01-01T" + end);
        let diff = (t2 - t1) / (1000 * 60); // phút
        let h = Math.floor(diff / 60);
        let m = diff % 60;

        let text = "";
        if (h > 0) text += h + " giờ ";
        if (m > 0) text += m + " phút";

        document.getElementById("p_duration").innerText = text.trim();


        document.getElementById("preview").style.display = "block";
        document.getElementById("p_room").innerText = roomName;
        document.getElementById("p_time").innerText = start + " → " + end;

    }

    // ===== GỢI Ý (KHÔNG PHÁ CODE) =====
    document.getElementById("students").addEventListener("input", function() {
        let students = this.value;
        let best = "";

        for (let opt of room.options) {
            if (opt.style.display === "none") continue;

            let cap = opt.dataset.cap;
            if (students <= cap) {
                best = opt.text;
                break;
            }
        }

        document.getElementById("suggest").innerHTML =
            best ? `<div class="alert alert-success">💡 Gợi ý: ${best}</div>` : "";
    });

    // ===== GIỮ NGUYÊN TOÀN BỘ AJAX =====
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
        let capacity = parseInt(room.options[room.selectedIndex].dataset.cap) || 0;

        let now = new Date();
        let today = now.toISOString().split('T')[0];

        if (!data.get("user_name")) errors.push("Chưa nhập tên");
        if (!data.get("date")) errors.push("Chưa chọn ngày");
        if (!data.get("start")) errors.push("Chưa chọn giờ bắt đầu");
        if (!data.get("end")) errors.push("Chưa chọn giờ kết thúc");

        if (students > capacity) errors.push("Phòng không đủ sức chứa!");
        if (data.get("date") < today) errors.push("Ngày không hợp lệ");
        if (data.get("start") >= data.get("end")) errors.push("Giờ sai");

        if (errors.length > 0) {
            show(errors.join("<br>"), "danger");
            return;
        }

        fetch("check_booking.php", {
                method: "POST",
                body: data
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === "exist") {
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

                        // ✅ reload lại trang sau 3s (để user thấy thông báo)
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    });
            });
    }

    function show(msg, type) {
        document.getElementById("msg").innerHTML =
            `<div class="alert alert-${type} mt-2">${msg}</div>`;
    }

    function toggleHistory() {
        let box = document.getElementById("historyBox");

        if (box.style.display === "none") {
            box.style.display = "block";
            loadHistory(1);
        } else {
            box.style.display = "none";
        }
    }

    function loadHistory(page) {
        let date = document.getElementById("filterDate").value;
        let name = document.getElementById("searchName").value;
        let room = document.getElementById("searchRoom").value;

        fetch("history_ajax.php?page=" + page + "&date=" + date + "&name=" + name + "&room=" + room)
            .then(r => r.text())
            .then(d => document.getElementById("historyTable").innerHTML = d);

        fetch("history_page.php?page=" + page + "&date=" + date + "&name=" + name + "&room=" + room)
            .then(r => r.text())
            .then(d => document.getElementById("pagination").innerHTML = d);
    }

    document.getElementById("filterDate").oninput = () => loadHistory(1);
    document.getElementById("searchName").oninput = () => loadHistory(1);
    document.getElementById("searchRoom").oninput = () => loadHistory(1);
</script>