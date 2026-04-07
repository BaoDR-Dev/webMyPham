<?php
// =========================================================
// PHẦN LOGIC NÀY BẠN CẦN ĐẶT TRONG CONTROLLER HOẶC TRƯỚC KHI INCLUDE VIEW
// ĐẢM BẢO CÁC BIẾN NÀY CÓ SẴN TRƯỚC KHI RENDER HTML
// VÍ DỤ:
// $customerInfo = ... lấy thông tin khách hàng từ DB
// $orders = ... lấy danh sách đơn hàng của khách hàng từ DB

$totalSpentCompletedOrders = 0; // Chỉ tính tổng tiền của các đơn hàng đã hoàn thành
$totalOrdersCount = count($orders ?? []); // Tổng số đơn hàng (tất cả trạng thái)
$completedOrdersCount = 0; // Đếm số đơn hàng đã hoàn thành
$firstOrderDate = null;
$lastOrderDate = null;

if (!empty($orders)) {
    foreach ($orders as $order) {
        // Chỉ tính tổng tiền và đếm số lượng cho các đơn hàng có trạng thái 'completed'
        if (($order->status ?? '') === 'completed') {
            $totalSpentCompletedOrders += $order->total_amount;
            $completedOrdersCount++;
        }

        $orderTimestamp = strtotime($order->created_at);

        // Tìm ngày đặt hàng đầu tiên và cuối cùng
        if ($firstOrderDate === null || $orderTimestamp < strtotime($firstOrderDate)) {
            $firstOrderDate = $order->created_at;
        }
        if ($lastOrderDate === null || $orderTimestamp > strtotime($lastOrderDate)) {
            $lastOrderDate = $order->created_at;
        }
    }
}

// END OF LOGIC BLOCK
// =========================================================
?>

<?php include 'app/views/shares/header.php'; ?>
<?php require_once __DIR__ . '/components/css_cus_detail.php'; ?>

<div class="container detail-page-wrapper">

    <!-- HEADER TRANG & NÚT QUAY LẠI - Đã tinh chỉnh vị trí và kiểu dáng -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <!-- HEADER TRANG -->
        <div>
            <div class="border-start border-4 border-primary ps-3">
                <h4 class="mb-1 fw-semibold" style="color:#6b67d2ff;">Chi tiết khách hàng</h4>
                <small class="text-muted">Thông tin cá nhân & lịch sử mua hàng của khách</small>
            </div>
        </div>
        <!-- NÚT QUAY LẠI -->
        <div class="mb-5">
            <a href="/webbanhang/Admin/manageUsers" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                Quay lại
            </a>
        </div>

    </div>

    <?php if (isset($customerInfo)): ?>

        <!-- ================== THÔNG TIN KHÁCH HÀNG & TỔNG QUAN ĐƠN HÀNG ================== -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="simple-card">
                    <div class="card-header-light">
                        <i class="bi bi-person me-2"></i>Thông tin cá nhân
                    </div>
                    <div class="card-body-default">
                        <ul class="list-group list-group-flush customer-detail-list">
                            <li class="list-group-item px-0">
                                <strong>Họ tên:</strong>
                                <span class="value-text"><?= htmlspecialchars($customerInfo->full_name) ?></span>
                            </li>
                            <li class="list-group-item px-0">
                                <strong>Email:</strong>
                                <span class="value-text"><?= htmlspecialchars($customerInfo->email) ?></span>
                            </li>
                            <li class="list-group-item px-0">
                                <strong>Ngày sinh:</strong>
                                <span class="value-text">
                                    <?= $customerInfo->birth_date
                                        ? date('d/m/Y', strtotime($customerInfo->birth_date))
                                        : '<span class="text-muted">Chưa cập nhật</span>' ?>
                                </span>
                            </li>
                            <li class="list-group-item px-0">
                                <strong>SĐT:</strong>
                                <span class="value-text"><?= htmlspecialchars($customerInfo->phone_number) ?></span>
                            </li>
                            <li class="list-group-item px-0 flex-column align-items-start">
                                <strong>Địa chỉ:</strong>
                                <div class="value-text address-text w-100">
                                    <?= htmlspecialchars($customerInfo->address) ?: '<span class="text-muted">Chưa cập nhật</span>' ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TỔNG QUAN ĐƠN HÀNG -->
            <div class="col-lg-6">
                <div class="simple-card">
                    <div class="card-header-light">
                        <i class="bi bi-bar-chart-line me-2"></i>Tổng quan đơn hàng
                    </div>
                    <div class="card-body-default overview-card-body" style="margin-top:-50px;">
                        <!-- Stat 1: Tổng số đơn hàng (tất cả trạng thái) -->
                        <div class=" overview-stat-item total-orders-stat">
                            <i class="bi bi-receipt stat-icon"></i>
                            <div>
                                <div class="stat-value"><?= $totalOrdersCount ?></div>
                                <div class="stat-label">Tổng số đơn hàng</div>
                            </div>
                        </div>

                        <!-- Stat 2: Tổng tiền đã chi (chỉ đơn hoàn thành) -->
                        <div class="overview-stat-item total-spent-stat">
                            <i class="bi bi-currency-dollar stat-icon"></i>
                            <div>
                                <div class="stat-value" style="font-size: 1rem;"><?= number_format($totalSpentCompletedOrders, 0, ',', '.') ?> VND</div>
                                <div class="stat-label" style="font-size: 0.75rem;">Tổng tiền đơn đã hoàn thành</div>
                            </div>
                        </div>

                        <!-- Stat 3: Ngày đặt hàng gần nhất -->
                        <div class="overview-stat-item last-order-stat">
                            <i class="bi bi-calendar-check stat-icon"></i>
                            <div>
                                <div class="stat-value">
                                    <?= $lastOrderDate ? date('d/m/Y', strtotime($lastOrderDate)) : '<span class="text-muted">N/A</span>' ?>
                                </div>
                                <div class="stat-label">Đơn hàng gần nhất</div>
                            </div>
                        </div>

                        <!-- Stat 4: Đơn hàng đã hoàn thành -->
                        <div class="overview-stat-item completed-orders-stat"> <!-- Changed class name here -->
                            <i class="bi bi-clipboard-check stat-icon"></i>
                            <div>
                                <div class="stat-value"><?= $completedOrdersCount ?></div>
                                <div class="stat-label">Đơn hàng đã hoàn thành</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================== LỊCH SỬ ĐƠN HÀNG ================== -->
        <div class="simple-card mb-5">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-outline-primary btn-sm"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#orderFilterOffcanvas">
                    <i class="bi bi-funnel"></i> Lọc đơn hàng
                </button>
            </div>
            <div class="card-header-light">
                <i class="bi bi-clock-history me-2"></i>Lịch sử đơn hàng
            </div>

            <div id="orderTableWrapper">
                <?php include __DIR__ . '/components/user_table_order.php'; ?>
            </div>


        <?php else: ?>
            <div class="alert alert-danger text-center alert-custom alert-danger-custom" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>Không tìm thấy thông tin khách hàng. Vui lòng kiểm tra lại ID khách hàng.</div>
            </div>
        <?php endif; ?>

        <?php include __DIR__ . '/components/filter_cus_detail.php'; ?>
        </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ✅ BẬT / TẮT INPUT THEO CHECKBOX
        document.querySelectorAll('.filter-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const fieldset = this.closest('.filter-group');
                const inputs = fieldset.querySelectorAll('.filter-input');

                inputs.forEach(input => {
                    input.disabled = !this.checked;

                    // reset value nếu tắt
                    if (!this.checked) {
                        if (input.tagName === 'SELECT') {
                            input.selectedIndex = -1;
                        } else {
                            input.value = '';
                        }
                    }
                });
            });
        });

        // ✅ SUBMIT FILTER
        const form = document.getElementById('orderFilterForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const params = new URLSearchParams(new FormData(form)).toString();

            fetch(`/webbanhang/Admin/userDetails2?${params}`)
                .then(res => {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.text();
                })
                .then(html => {
                    const wrapper = document.getElementById('orderTableWrapper');

                    if (!wrapper) {
                        console.warn('❗ Không tìm thấy orderTableWrapper');
                        return;
                    }

                    wrapper.innerHTML = html;

                    const offcanvasEl = document.getElementById('orderFilterOffcanvas');
                    if (offcanvasEl) {
                        const offcanvas =
                            bootstrap.Offcanvas.getInstance(offcanvasEl) ||
                            new bootstrap.Offcanvas(offcanvasEl);

                        offcanvas.hide();
                    }
                })

        });
        document.getElementById('resetFilterBtn').addEventListener('click', function() {

            const userId = document.querySelector('input[name="user_id"]').value;

            fetch(`/webbanhang/Admin/userDetails2?user_id=${userId}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('orderTableWrapper').innerHTML = html;

                    // reset form UI
                    document.getElementById('orderFilterForm').reset();
                    document.querySelectorAll('.filter-input').forEach(i => i.disabled = true);
                    document.querySelectorAll('.filter-toggle').forEach(t => t.checked = false);

                    const offcanvasEl = document.getElementById('orderFilterOffcanvas');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    offcanvas.hide();
                })
                .catch(() => alert('Lỗi tải dữ liệu'));
        });


    });
</script>



<?php include 'app/views/shares/footer.php'; ?>