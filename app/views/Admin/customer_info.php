<?php include 'app/views/shares/header.php'; ?>
<?php include 'app/views/shares/navbar.php'; ?>

<style>
    /* Styling chung */
    .section-title {
        color: #343a40;
        font-weight: 700;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--bs-primary);
        padding-left: 1rem;
    }

    /* Card Wrapper */
    .modern-card {
        background-color: #fff;
        border: none;
        border-radius: 0.75rem;
        /* Bo góc */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        /* Bóng đổ nhẹ nhàng */
        margin-bottom: 2rem;
        /* Khoảng cách giữa các card */
    }

    .modern-card .card-header {
        background-color: var(--bs-light);
        border-bottom: 1px solid var(--bs-border-color-subtle);
        font-weight: 600;
        color: #495057;
        padding: 1rem 1.5rem;
        border-top-left-radius: 0.75rem;
        /* Đảm bảo bo góc cho header */
        border-top-right-radius: 0.75rem;
    }

    .modern-card .card-body {
        padding: 1.5rem;
    }

    /* Thông tin khách hàng */
    .customer-info-list .list-group-item {
        background-color: transparent;
        /* Nền trong suốt */
        border: none;
        padding: 0.75rem 0;
        /* Khoảng cách dọc */
        font-size: 0.95rem;
    }

    .customer-info-list .list-group-item strong {
        color: #343a40;
        /* Màu đậm hơn cho nhãn */
        min-width: 120px;
        /* Đảm bảo căn chỉnh đều */
        display: inline-block;
    }

    /* Bảng đơn hàng */
    .order-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        /* Loại bỏ khoảng cách mặc định */
        margin-bottom: 0;
        --bs-table-bg: #fff;
        --bs-table-hover-bg: rgba(var(--bs-primary-rgb), 0.04);
        border-radius: 0.75rem;
        /* Bo góc cho bảng trong card */
        overflow: hidden;
        /* Đảm bảo các góc bo tròn */
    }

    .order-table thead th {
        background-color: #f8f9fa;
        /* Nền xám nhạt cho tiêu đề */
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6c757d;
        font-weight: 600;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
    }

    .order-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .order-table tbody tr:hover {
        background-color: var(--bs-table-hover-bg);
    }

    .order-table td {
        background-color: #fff;
        padding: 1rem 1.25rem;
        vertical-align: middle;
        font-size: 0.875rem;
        color: #495057;
        border-bottom: 1px solid #e9ecef;
        /* Đường viền dưới cho mỗi hàng */
    }

    .order-table tbody tr:last-child td {
        border-bottom: none;
        /* Xóa viền dưới cho hàng cuối cùng */
    }

    /* Status Select */
    .order-status-select {
        border-radius: 0.5rem;
        font-size: 0.8rem;
        padding: 0.35rem 0.75rem;
        border: 1px solid var(--bs-border-color);
        background-color: #f8f9fa;
        color: #495057;
        transition: all 0.2s ease;
    }

    .order-status-select:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.15rem rgba(var(--bs-primary-rgb), .25);
        background-color: #fff;
    }

    /* Coloring for status options */
    .order-status-select option[value="pending"] {
        color: var(--bs-secondary);
    }

    .order-status-select option[value="processing"] {
        color: var(--bs-info);
    }

    .order-status-select option[value="completed"] {
        color: var(--bs-success);
    }

    .order-status-select option[value="cancelled"] {
        color: var(--bs-danger);
    }

    /* Detail Button */
    .btn-detail-order {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-detail-order:hover {
        box-shadow: 0 2px 8px rgba(var(--bs-primary-rgb), 0.2);
    }

    /* Alerts */
    .alert-custom {
        border-radius: 0.75rem;
        padding: 1.5rem;
        font-size: 1rem;
    }

    .alert-warning-custom {
        background-color: #fff3cd;
        color: #664d03;
        border-color: #ffecb5;
    }

    .alert-danger-custom {
        background-color: #f8d7da;
        color: #842029;
        border-color: #f5c2c7;
    }
</style>

<div class="container mt-5 mb-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-person-fill fs-3 text-primary"></i>
        <h2 class="section-title mb-0">Thông Tin Khách Hàng</h2>
    </div>

    <?php if (isset($customerInfo)): ?>
        <div class="modern-card">
            <div class="card-header">
                Thông tin cá nhân
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush customer-info-list">
                    <li class="list-group-item d-flex align-items-center"><strong>Họ và Tên:</strong> <span class="ms-2"><?php echo htmlspecialchars($customerInfo->full_name); ?></span></li>
                    <li class="list-group-item d-flex align-items-center"><strong>Email:</strong> <span class="ms-2"><?php echo htmlspecialchars($customerInfo->email); ?></span></li>
                    <li class="list-group-item d-flex align-items-center"><strong>Số Điện Thoại:</strong> <span class="ms-2"><?php echo htmlspecialchars($customerInfo->phone_number); ?></span></li>
                    <li class="list-group-item d-flex align-items-center"><strong>Địa Chỉ:</strong> <span class="ms-2"><?php echo htmlspecialchars($customerInfo->address); ?></span></li>
                </ul>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-receipt-cutoff fs-3 text-primary"></i>
            <h3 class="section-title mb-0">Danh Sách Đơn Hàng</h3>
        </div>

        <?php if (!empty($orders)): ?>
            <div class="modern-card p-0 overflow-hidden">
                <table class="table order-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Mã Đơn</th>
                            <th style="width: 150px;">Ngày</th>
                            <th class="text-center" style="width: 180px;">Trạng Thái</th>
                            <th style="width: 150px;">Thanh Toán</th>
                            <th class="text-end" style="width: 150px;">Tổng Tiền</th>
                            <th class="text-center" style="width: 140px;">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="fw-semibold text-primary">#<?php echo $order['id']; ?></td>
                                <td class="text-muted"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td class="text-center">
                                    <form action="/webbanhang/Admin/updateOrderStatus3" method="post" class="d-inline-block">
                                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id'] ?? '') ?>">
                                        <select name="status" class="form-select order-status-select" onchange="this.form.submit()">
                                            <option value="pending" <?= ($order['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                            <option value="processing" <?= ($order['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                            <option value="completed" <?= ($order['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                            <option value="cancelled" <?= ($order['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-muted"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></td>
                                <td class="fw-bold text-end text-success"><?php echo number_format($order['total_amount'], 0, ',', '.') . ' VND'; ?></td>
                                <td class="text-center">
                                    <a href="/webbanhang/Admin/orderDetail/<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary btn-detail-order">
                                        <i class="bi bi-info-circle me-1"></i>Chi Tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning alert-custom alert-warning-custom" role="alert">
                <i class="bi bi-info-circle me-2"></i> Khách hàng này chưa có đơn hàng nào.
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-danger alert-custom alert-danger-custom" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Không tìm thấy thông tin khách hàng.
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>