<?php include 'config.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="card p-4 shadow">
        <h3 class="text-center text-primary mb-3">Đặt phòng</h3>

        <form id="form" novalidate>

            <input name="user_name" class="form-control mb-2" placeholder="Tên người đặt" required>

            <select name="room" id="room" class="form-control mb-2" required>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM rooms");
                while ($r = mysqli_fetch_assoc($res)) {
                    echo "<option value='{$r['id']}' data-cap='{$r['capacity']}'>
                            {$r['name']} (Tối đa {$r['capacity']} người)
                          </option>";
                }
                ?>
            </select>

            <input type="number" name="students" id="students" class="form-control mb-2" placeholder="Số lượng người" required>

            <select name="purpose" class="form-control mb-2" required>
                <option value="">-- Chọn mục đích --</option>
                <option value="Học">Học</option>
                <option value="Thi">Thi</option>
                <option value="Họp">Họp</option>
            </select>

            <input type="date" name="date" id="date" class="form-control mb-2" required>
            <input type="time" name="start" id="start" class="form-control mb-2" required>
            <input type="time" name="end" id="end" class="form-control mb-2" required>

            <textarea name="note" class="form-control mb-2" placeholder="Ghi chú"></textarea>

            <button type="submit" class="btn btn-success w-100">Đặt phòng</button>
        </form>

        <!-- Hiển thị thông tin bên dưới -->
        <div id="preview" class="mt-3" style="display:none;">
            <div class="alert alert-info">
                📌 <b>Thông tin:</b><br>
                Phòng: <span id="p_room"></span><br>
                Thời gian: <span id="p_time"></span><br>
                ⏱ Thời lượng: <span id="p_duration" class="badge bg-success"></span>
            </div>
        </div>

        <!--  GỢI Ý PHÒNG -->
        <div id="suggest" class="mt-2"></div>

        <!-- LỊCH đang hoạt đông ngày đó calendar_ajax.php -->
        <div id="calendarBox" class="mt-3"></div>

        <div id="msg"></div>
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

    // ===== dùng AI gợi ý đặt phòng nếu sức chứa nhập không đủ =====
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

    // ===== LOAD LỊCH (AJAX) =====
    document.getElementById("date").addEventListener("change", function() {
        fetch("calendar_ajax.php?date=" + this.value)
            .then(r => r.text())
            .then(d => {
                document.getElementById("calendarBox").innerHTML = d;
            });
    });

    // ===== SUBMIT =====
    form.onsubmit = function(e) {
        e.preventDefault();

        let data = new FormData(form);
        let errors = [];

        let students = parseInt(data.get("students")) || 0;
        let room = document.getElementById("room");
        let capacity = parseInt(room.options[room.selectedIndex].dataset.cap) || 0;

        let now = new Date();
        let today = now.toISOString().split('T')[0];

        if (!data.get("user_name")) errors.push("Chưa nhập tên");
        if (!data.get("date")) errors.push("Chưa chọn ngày");
        if (!data.get("start")) errors.push("Chưa chọn giờ bắt đầu");
        if (!data.get("end")) errors.push("Chưa chọn giờ kết thúc");

        if (students > capacity) errors.push("Phòng không đủ sức chứa!");

        if (data.get("date") < today) errors.push("Ngày không hợp lệ");

        if (data.get("start") >= data.get("end"))
            errors.push("Giờ sai");

        // giới hạn 12h
        let t1 = new Date("1970-01-01T" + data.get("start"));
        let t2 = new Date("1970-01-01T" + data.get("end"));
        let hours = (t2 - t1) / (1000 * 60 * 60);

        if (hours > 12) errors.push("Không được đặt quá 12 giờ");

        if (errors.length > 0) {
            show(errors.join("<br>"), "danger");
            return;
        }

        // check trùng lịch
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

                // disable nút
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
</script>