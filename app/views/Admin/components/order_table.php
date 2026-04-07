<style>
    /* CSS tùy chỉnh để làm đẹp thêm */
    .table-responsive .table {
        border-collapse: separate;
        /* Cho phép border-spacing */
        border-spacing: 0;
        /* Loại bỏ khoảng cách cell mặc định */
    }

    /* Viền gạch ngang mờ cho từng hàng */
    .border-bottom-dashed {
        border-bottom: 1px dashed #e0e0e0;
        /* Viền mỏng, gạch ngang */
    }

    /* Loại bỏ viền gạch ngang cuối cùng */
    tbody tr:last-child .border-bottom-dashed {
        border-bottom: none !important;
    }

    /* Đảm bảo sticky header hoạt động */
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
        /* Đảm bảo header nằm trên các nội dung khác khi dính */
        background-color: var(--bs-table-bg);
        /* Hoặc màu nền cố định của header */
    }

    /* Điều chỉnh màu nền và chữ cho badge payment method */
    .badge.bg-danger-subtle {
        background-color: #fcebeb !important;
    }

    .badge.text-danger {
        color: #dc3545 !important;
    }

    .badge.bg-primary-subtle {
        background-color: #e6f0ff !important;
    }

    .badge.text-primary {
        color: #0d6efd !important;
    }

    .badge.bg-success-subtle {
        background-color: #e7f7ed !important;
    }

    .badge.text-success {
        color: #198754 !important;
    }

    .badge.bg-secondary-subtle {
        background-color: #f0f2f5 !important;
    }

    .badge.text-secondary {
        color: #6c757d !important;
    }

    /* Màu nền động cho select trạng thái */
    .bg-secondary.bg-opacity-25 {
        background-color: rgba(108, 117, 125, 0.15) !important;
    }

    .bg-info.bg-opacity-25 {
        background-color: rgba(13, 202, 240, 0.15) !important;
    }

    .bg-success.bg-opacity-25 {
        background-color: rgba(25, 135, 84, 0.15) !important;
    }

    .bg-danger.bg-opacity-25 {
        background-color: rgba(220, 53, 69, 0.15) !important;
    }

    .text-end.py-3 {
        color: #6b7280;
    }

    .text-center.py-3 {
        color: #6b7280;
    }
</style>

<div class="table-responsive">
    <table class="table user-table align-middle">
        <thead class="table-light small text-uppercase text-muted fw-semibold sticky-top"> <!-- Thêm sticky-top ở đây -->
            <tr>
                <th class="text-center py-3" style="width:80px">Mã</th>
                <th class="py-3" style="width:auto; color: #6b7280;">Khách hàng</th> <!-- width:auto vẫn ổn, text-nowrap bị xóa ở body -->
                <th class="text-end py-3" style="width:130px">Tổng tiền</th>
                <th class="text-center py-3" style="width:120px">Thanh toán</th>
                <th class="text-center py-3" style="width:140px">Trạng thái</th>
                <th class="text-center py-3" style="width:160px">Ngày đặt</th>
                <th class="text-center py-3" style="width:120px">Chi tiết</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($orders)) : ?>
                <?php foreach ($orders as $order) : ?>
                    <tr class="border-bottom-dashed">
                        <td class="text-center fw-bold text-primary py-3">#<?= htmlspecialchars($order['id'] ?? '') ?></td>

                        <td class="text-start py-3" style="width: 200px;"> <!-- Đã xóa text-nowrap ở đây -->
                            <i class="bi bi-person-circle me-1 text-secondary"></i>
                            <?= htmlspecialchars($order['full_name'] ?? '') ?>
                        </td>

                        <td class="text-end fw-bolder text-danger py-3" style="white-space: nowrap;">
                            <?= number_format((float)($order['total_amount'] ?? 0), 0, ',', '.') ?>₫
                        </td>

                        <td class="text-center py-3">
                            <?php
                            echo match ($order['payment_method'] ?? '') {
                                'momo' => '<span class="badge bg-danger-subtle text-danger fw-normal"><i class="bi bi-phone-fill me-1"></i> MoMo</span>',
                                'vnpay' => '<span class="badge bg-primary-subtle text-primary fw-normal"><i class="bi bi-credit-card-fill me-1"></i> VnPay</span>',
                                'cash' => '<span class="badge bg-success-subtle text-success fw-normal"><i class="bi bi-cash me-1"></i> Tiền mặt</span>',
                                default => '<span class="badge bg-secondary-subtle text-secondary fw-normal">Khác</span>'
                            };
                            ?>
                        </td>

                        <td class="text-center py-3">
                            <?php
                            $statusClass = match ($order['status'] ?? '') {
                                'pending' => 'secondary',
                                'processing' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'dark'
                            };
                            ?>

                            <form method="post" action="/webbanhang/Admin/updateOrderStatus2" class="d-inline-block">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id'] ?? '') ?>">
                                <select name="status"
                                    class="form-select form-select-sm text-center fw-semibold border-0 rounded-pill bg-<?= $statusClass ?> bg-opacity-25 text-<?= $statusClass ?>"
                                    onchange="this.form.submit()" style="min-width: 120px; max-width: 150px;">
                                    <option value="pending" <?= ($order['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                    <option value="processing" <?= ($order['status'] ?? '') == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                    <option value="completed" <?= ($order['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                    <option value="cancelled" <?= ($order['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </form>
                        </td>

                        <td class="text-center text-muted small text-nowrap py-3">
                            <?= date('d/m/Y H:i', strtotime($order['created_at'] ?? '1970-01-01 00:00:00')) ?>
                        </td>

                        <td class="text-center py-3">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/webbanhang/Admin/orderDetail/<?= htmlspecialchars($order['id'] ?? '') ?>"
                                    class="btn btn-sm btn-outline-primary shadow-sm"
                                    title="Chi tiết đơn hàng">
                                    <i class="bi bi-list-ul"></i>
                                </a>
                                <!-- Nút "Thông tin khách hàng" đã bị xóa trong mã bạn cung cấp. Nếu bạn muốn thêm lại, hãy dùng code dưới đây: -->
                                <!--
                                <a href="/webbanhang/Admin/customerInfo/<?= htmlspecialchars($order['id'] ?? '') ?>"
                                    class="btn btn-sm btn-outline-info shadow-sm"
                                    title="Thông tin khách hàng">
                                    <i class="bi bi-person-vcard-fill"></i>
                                </a>
                                -->
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted fst-italic">
                        <i class="bi bi-info-circle me-2"></i> Không có đơn hàng nào được tìm thấy.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>