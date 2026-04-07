<?php include 'app/views/shares/header.php'; ?>
<?php include 'app/views/shares/navbar.php'; ?> <!-- Đảm bảo navbar được include -->

<!-- Custom CSS for subtle badges and dashed borders -->
<style>
    /* Viền gạch ngang mờ cho từng hàng trong bảng sản phẩm */
    .border-bottom-dashed {
        border-bottom: 1px dashed #e9ecef;
        /* Màu nhạt hơn */
    }

    tbody tr:last-child .border-bottom-dashed {
        border-bottom: none !important;
    }

    /* Custom subtle badge colors (if Bootstrap 5.3+ bg-*-subtle not fully supported or desired) */
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

    .badge.bg-info-subtle {
        background-color: #e2f7fa !important;
    }

    .badge.text-info {
        color: #0dcaf0 !important;
    }

    .badge.bg-warning-subtle {
        background-color: #fff9e6 !important;
    }

    .badge.text-warning {
        color: #ffc107 !important;
    }

    .badge.bg-muted-subtle {
        background-color: #f8f9fa !important;
    }

    .badge.text-muted {
        color: #6c757d !important;
    }

    /* Ensure sticky header on product table if it were stand-alone */
    .card-body .table-responsive .sticky-top {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: var(--bs-table-bg);
    }
</style>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="border-start border-4 border-primary ps-3">
            <h4 class="mb-1 fw-semibold" style="color:#6b67d2ff;">Chi tiết đơn hàng</h4>
            <small class="text-muted">Thông tin đơn, khách hàng và sản phẩm</small>
        </div>

        <a href="/webbanhang/Admin/userDetails?user_id=<?= htmlspecialchars($customerInfo->id ?? '') ?>"
            class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i>
            Quay lại
        </a>
    </div>




    <?php if (isset($order)): ?>
        <!-- Box: Thông Tin Đơn Hàng -->
        <div class="shadow-lg mb-5 rounded-3 border overflow-hidden">
            <div class="card-header bg-primary text-white py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i> Thông Tin Đơn Hàng</h5>
            </div>
            <div class="card-body px-4 py-4 bg-white">
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="mb-2 d-flex align-items-center"> <!-- Thêm d-flex align-items-center -->
                            <strong class="me-2"><i class="bi bi-tag me-2 text-primary"></i> Mã Đơn Hàng:</strong> <!-- Đã xóa text-muted -->
                            <span class="fw-semibold text-dark">#<?= htmlspecialchars($order->id ?? 'N/A') ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2 d-flex align-items-center"> <!-- Thêm d-flex align-items-center -->
                            <strong class="me-2"><i class="bi bi-person me-2 text-info"></i> Khách Hàng:</strong> <!-- Đã xóa text-muted -->
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($order->full_name ?? ($customerInfo->full_name ?? 'N/A')) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2 d-flex align-items-center"> <!-- Thêm d-flex align-items-center -->
                            <strong class="me-2"><i class="bi bi-telephone me-2 text-success"></i> SĐT Nhận Hàng:</strong> <!-- Đã xóa text-muted -->
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($order->phone_number ?? 'N/A') ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2 d-flex align-items-center"> <!-- Thêm d-flex align-items-center -->
                            <strong class="me-2"><i class="bi bi-geo-alt me-2 text-danger"></i> Địa Chỉ Nhận Hàng:</strong> <!-- Đã xóa text-muted -->
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($order->address ?? 'N/A') ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2 d-flex align-items-center"> <!-- Thêm d-flex align-items-center -->
                            <strong class="me-2"><i class="bi bi-calendar-event me-2 text-secondary"></i> Ngày Đặt:</strong> <!-- Đã xóa text-muted -->
                            <span class="fw-semibold text-dark"><?= date('d/m/Y H:i', strtotime($order->created_at ?? '1970-01-01 00:00:00')) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2 d-flex align-items-center"> <!-- Thêm d-flex align-items-center -->
                            <strong class="me-2"><i class="bi bi-wallet me-2 text-warning"></i> Phương Thức Thanh Toán:</strong> <!-- Đã xóa text-muted -->
                            <?php
                            $method = $order->payment_method ?? '';
                            echo match ($method) {
                                'momo' => '<span class="badge bg-danger-subtle text-danger fw-normal"> MoMo</span>',
                                'vnpay' => '<span class="badge bg-primary-subtle text-primary fw-normal"> VnPay</span>',
                                'payUrl' => '<span class="badge bg-danger-subtle text-danger fw-normal"> MoMo</span>',
                                'cash' => '<span class="badge bg-success-subtle text-success fw-normal"> Tiền mặt</span>',
                                default => '<span class="badge bg-secondary-subtle text-secondary fw-normal">Không xác định</span>'
                            };
                            ?>
                        </p>
                    </div>
                    <?php if (!empty($order->promotion_name)): ?>
                        <div class="col-md-6">
                            <p class="mb-2 d-flex align-items-center">
                                <strong class="me-2">
                                    <i class="bi bi-gift me-2 text-danger"></i> Khuyến mãi:
                                </strong>
                                <span class="fw-semibold text-primary">
                                    <?= htmlspecialchars($order->promotion_name); ?>
                                </span>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if (($order->discount_amount ?? 0) > 0): ?>
                        <div class="col-md-6">
                            <p class="mb-2 d-flex align-items-center">
                                <strong class="me-2">
                                    <i class="bi bi-tag me-2 text-success"></i> Giảm giá:
                                </strong>
                                <span class="fw-semibold text-success">
                                    -<?= number_format($order->discount_amount, 0, ',', '.'); ?> ₫
                                </span>
                            </p>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-12">
                        <p class="mb-2 d-flex align-items-center"> <!-- Thêm d-flex align-items-center -->
                            <strong class="me-2"><i class="bi bi-info-circle me-2 text-dark"></i> Trạng Thái:</strong> <!-- Đã xóa text-muted -->
                            <?php
                            $status = $order->status ?? '';
                            $statusBadgeClass = match ($status) {
                                'pending' => 'bg-secondary-subtle text-secondary',
                                'processing' => 'bg-info-subtle text-info',
                                'completed' => 'bg-success-subtle text-success',
                                'cancelled' => 'bg-danger-subtle text-danger',
                                default => 'bg-dark-subtle text-dark'
                            };
                            echo '<span class="badge ' . $statusBadgeClass . ' fw-normal">';
                            echo match ($status) {
                                'pending' => 'Chờ xử lý',
                                'processing' => 'Đang xử lý',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                                default => 'Không xác định'
                            };
                            echo '</span>';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Box: Danh Sách Sản Phẩm -->
        <div class="shadow-lg mb-5 rounded-3 border overflow-hidden">
            <div class="card-header bg-light py-3 px-4">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-basket me-2 text-success"></i>
                    Sản phẩm trong đơn
                </h5>
            </div>

            <div class="card-body p-0 bg-white">
                <?php if (!empty($orderDetails)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase small text-muted fw-semibold">
                                <tr>
                                    <th class="text-center" style="width:60px">STT</th>
                                    <th class="text-center" style="width:110px">Mã SP</th>
                                    <th>Tên sản phẩm</th>
                                    <th class="text-center" style="width:100px">Số lượng</th>
                                    <th class="text-end" style="width:140px">Đơn giá</th>
                                    <th class="text-end" style="width:150px">Thành tiền</th>
                                    <th class="text-center" style="width:130px">Đánh giá</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $totalAmount = 0;
                                $stt = 1;
                                foreach ($orderDetails as $detail):
                                    $itemTotal = ($detail['quantity'] ?? 0) * ($detail['price'] ?? 0);
                                    $totalAmount += $itemTotal;
                                ?>
                                    <tr class="border-bottom-dashed">
                                        <td class="text-center fw-semibold"><?= $stt++; ?></td>

                                        <td class="text-center text-muted">
                                            #<?= htmlspecialchars($detail['product_id'] ?? 'N/A'); ?>
                                        </td>

                                        <td class="fw-semibold">
                                            <?= htmlspecialchars($detail['product_name'] ?? 'N/A'); ?>
                                        </td>

                                        <td class="text-center">
                                            <?= htmlspecialchars($detail['quantity'] ?? 0); ?>
                                        </td>

                                        <td class="text-end text-secondary">
                                            <?= number_format($detail['price'] ?? 0, 0, ',', '.'); ?> ₫
                                        </td>

                                        <td class="text-end fw-bold text-dark">
                                            <?= number_format($itemTotal, 0, ',', '.'); ?> ₫
                                        </td>

                                        <td class="text-center">
                                            <?php if (($order->status ?? '') === 'completed'): ?>
                                                <?php if (($detail['is_reviewed'] ?? 0) == 1): ?>
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Đã đánh giá
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                        <i class="bi bi-pencil-square me-1"></i> Chưa đánh giá
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning">
                                                    <i class="bi bi-hourglass-split me-1"></i> Chờ hoàn thành
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted fst-italic py-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Không có sản phẩm nào trong đơn hàng này
                    </p>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light py-3 px-4">
                <?php
                $shippingFee = $order->shipping_fee ?? 0;
                $discount    = $order->discount_amount ?? 0;
                $subtotal    = $totalAmount;
                $grandTotal  = $subtotal + $shippingFee - $discount;

                ?>

                <div class="d-flex justify-content-end">
                    <div style="min-width: 320px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-semibold">
                                <?= number_format($subtotal, 0, ',', '.'); ?> ₫
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="fw-semibold">
                                <?php if ($shippingFee == 0): ?>
                                    <span class="text-success">Freeship</span>
                                <?php else: ?>
                                    <?= number_format($shippingFee, 0, ',', '.'); ?> ₫
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if ($discount > 0): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Giảm giá:</span>
                                <span class="fw-semibold text-success">
                                    -<?= number_format($discount, 0, ',', '.'); ?> ₫
                                </span>
                            </div>
                        <?php endif; ?>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between fs-5">
                            <span class="fw-bold">Tổng thanh toán:</span>
                            <span class="fw-bold text-success">
                                <?= number_format($grandTotal, 0, ',', '.'); ?> ₫
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    <?php else: ?>
        <div class="alert alert-danger text-center mt-5" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Không tìm thấy thông tin đơn hàng hoặc đơn hàng không tồn tại.
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>