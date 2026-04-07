<?php include 'app/views/shares/header.php'; ?>
<?php include 'app/views/shares/navbar.php'; ?>

<div class="container mt-4">
    <!-- ======================= BẮT ĐẦU KHỐI HEADER MỚI ======================= -->
    <div class="page-header d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">

        <!-- Tiêu đề và mô tả (bên trái) -->
        <div>
            <h1 class="h3 mb-1">Lịch Sử Đơn Hàng</h1>
            <p class="text-muted mb-0">Xem lại và quản lý tất cả các đơn hàng đã đặt của bạn.</p>
        </div>

        <!-- Nút Lọc (bên phải) -->
        <div class="mt-3 mt-md-0">
            <button class="btn btn-filter-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                <i class="bi bi-funnel-fill me-2"></i>Bộ lọc
            </button>
        </div>

    </div>
    <!-- ======================= KẾT THÚC KHỐI HEADER MỚI ======================= -->

    <!-- Offcanvas bộ lọc bên phải -->
    <div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel"
        style="width: 380px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="bi bi-funnel-fill me-2 text-primary"></i>Bộ lọc đơn hàng
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
        </div>

        <div class="offcanvas-body bg-light">

            <!-- Form lọc -->
            <form id="orderFilterForm" class="px-1">

                <!-- Nhóm 1: Mã đơn -->
                <div class="filter-section mb-4 p-3 bg-white rounded shadow-sm">
                    <div class="form-check d-flex align-items-center mb-3">
                        <input class="form-check-input filter-toggle" type="checkbox" id="filterOrderId" name="enable_order_id">
                        <label class="form-check-label fw-semibold ms-2" for="filterOrderId">
                            <i class="bi bi-hash me-1"></i> Mã đơn hàng
                        </label>
                    </div>

                    <div class="row g-2 ms-1">
                        <div class="col-6">
                            <label class="form-label small">Từ</label>
                            <input type="number" name="min_order_id" class="form-control filter-input" placeholder="VD: 1001" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Đến</label>
                            <input type="number" name="max_order_id" class="form-control filter-input" placeholder="VD: 1050" disabled>
                        </div>
                    </div>
                </div>

                <!-- Nhóm 2: Trạng thái -->
                <div class="filter-section mb-4 p-3 bg-white rounded shadow-sm">
                    <div class="form-check d-flex align-items-center mb-3">
                        <input class="form-check-input filter-toggle" type="checkbox" id="filterStatus" name="enable_status">
                        <label class="form-check-label fw-semibold ms-2" for="filterStatus">
                            <i class="bi bi-check-circle me-1"></i> Trạng thái
                        </label>
                    </div>

                    <select name="status[]" class="form-select filter-input" multiple disabled>
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>

                <!-- Nhóm 3: Khoảng tiền -->
                <div class="filter-section mb-4 p-3 bg-white rounded shadow-sm">
                    <div class="form-check d-flex align-items-center mb-3">
                        <input class="form-check-input filter-toggle" type="checkbox" id="filterAmount" name="enable_amount">
                        <label class="form-check-label fw-semibold ms-2" for="filterAmount">
                            <i class="bi bi-cash-stack me-1"></i> Khoảng tiền
                        </label>
                    </div>

                    <div class="row g-2 ms-1">
                        <div class="col-6">
                            <label class="form-label small">Từ (VNĐ)</label>
                            <input type="number" name="min_amount" class="form-control filter-input" placeholder="0" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Đến (VNĐ)</label>
                            <input type="number" name="max_amount" class="form-control filter-input" placeholder="500000" disabled>
                        </div>
                    </div>
                </div>

                <!-- Nhóm 4: Ngày tạo -->
                <div class="filter-section mb-4 p-3 bg-white rounded shadow-sm">
                    <div class="form-check d-flex align-items-center mb-3">
                        <input class="form-check-input filter-toggle" type="checkbox" id="filterDate" name="enable_date">
                        <label class="form-check-label fw-semibold ms-2" for="filterDate">
                            <i class="bi bi-calendar-date me-1"></i> Ngày tạo đơn
                        </label>
                    </div>

                    <div class="row g-2 ms-1">
                        <div class="col-6">
                            <label class="form-label small">Từ ngày</label>
                            <input type="date" name="start_date" class="form-control filter-input" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Đến ngày</label>
                            <input type="date" name="end_date" class="form-control filter-input" disabled>
                        </div>
                    </div>
                </div>

                <!-- Nhóm 5: Phương thức thanh toán -->
                <div class="filter-section mb-4 p-3 bg-white rounded shadow-sm">
                    <div class="form-check d-flex align-items-center mb-3">
                        <input class="form-check-input filter-toggle" type="checkbox" id="filterPayment" name="enable_payment_method">
                        <label class="form-check-label fw-semibold ms-2" for="filterPayment">
                            <i class="bi bi-credit-card-2-front me-1"></i> Phương thức thanh toán
                        </label>
                    </div>

                    <select name="payment_methods[]" class="form-select filter-input" multiple disabled>
                        <option value="cash">Tiền mặt</option>
                        <option value="vnpay">VN Pay</option>
                        <option value="momo">Ví Momo</option>
                    </select>
                </div>

                <!-- Nút hành động -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-50">
                        <i class="bi bi-funnel-fill me-1"></i> Lọc
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-50" id="resetFilterBtn">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                    </button>
                </div>

            </form>
        </div>
    </div>


    <!-- KẾT QUẢ ĐƠN HÀNG -->
    <?php if (empty($orders)): ?>
        <div class="alert alert-warning text-center">Bạn chưa có đơn hàng nào!</div>
        <div class="text-center">
            <a href="/webbanhang/Product/index" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="order-table-wrapper">
            <?php include 'app/views/order/listorder.php'; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- HÀM ĐÓNG OFFCANVAS (CÁCH MỚI: GIẢ LẬP CLICK) ---
        const closeOffcanvas = () => {
            // Tìm nút đóng (nút X) bên trong offcanvas
            const closeBtn = document.querySelector('#filterOffcanvas .btn-close');

            if (closeBtn) {
                // Tự động kích hoạt sự kiện click vào nút đóng
                closeBtn.click();
            } else {
                // Dự phòng: Nếu không tìm thấy nút X thì dùng lệnh Bootstrap
                const offcanvasEl = document.getElementById('filterOffcanvas');
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (bsOffcanvas) bsOffcanvas.hide();
            }
        };

        // Bật/tắt input (làm mờ/sáng) theo checkbox
        document.querySelectorAll('.filter-toggle').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const container = this.closest('.filter-section');
                const inputs = container.querySelectorAll('.filter-input');
                inputs.forEach(input => {
                    input.disabled = !this.checked;
                    input.style.opacity = this.checked ? 1 : 0.5;
                });
            });
            // Kích hoạt lại trạng thái ban đầu khi tải trang
            checkbox.dispatchEvent(new Event('change'));
        });

        // --- XỬ LÝ SỰ KIỆN LỌC (NÚT LỌC) ---
        document.getElementById('orderFilterForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Chặn tải lại trang
            const formData = new FormData(this);

            // Gọi AJAX
            fetch('/webbanhang/Order/ajaxFilterForUser', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(html => {
                    // 1. Cập nhật giao diện danh sách đơn hàng
                    document.querySelector('.order-table-wrapper').innerHTML = html;

                    // 2. Đóng bảng lọc ngay lập tức
                    closeOffcanvas();
                })
                .catch(err => console.error('Lỗi khi lọc:', err));
        });

        // --- XỬ LÝ SỰ KIỆN ĐẶT LẠI (NÚT RESET) ---
        document.getElementById('resetFilterBtn').addEventListener('click', function() {
            const form = document.getElementById('orderFilterForm');
            form.reset(); // Xóa trắng form

            // Reset trạng thái mờ/sáng của các ô nhập liệu
            document.querySelectorAll('.filter-toggle').forEach(cb =>
                cb.dispatchEvent(new Event('change'))
            );

            // Gọi AJAX lấy lại tất cả đơn hàng
            fetch('/webbanhang/Order/ajaxFilterForUser')
                .then(res => res.text())
                .then(html => {
                    document.querySelector('.order-table-wrapper').innerHTML = html;

                    // Đóng bảng lọc ngay lập tức
                    closeOffcanvas();
                })
                .catch(err => console.error('Lỗi khi đặt lại:', err));
        });
    });
</script>


<?php include 'app/views/shares/footer.php'; ?>