<style>
    .order-list-scroll {
        max-height: 600px;
        /* chiều cao tối đa */
        overflow-y: auto;
        /* bật scroll dọc */
        padding-right: 8px;
        /* tránh che nội dung bởi scrollbar */
    }

    /* ===== Scrollbar đẹp (Chrome, Edge) ===== */
    .order-list-scroll::-webkit-scrollbar {
        width: 8px;
    }

    .order-list-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .order-list-scroll::-webkit-scrollbar-thumb {
        background: #b5b5b5;
        border-radius: 10px;
    }

    .order-list-scroll::-webkit-scrollbar-thumb:hover {
        background: #888;
    }

    /* ===== Firefox ===== */
    .order-list-scroll {
        scrollbar-width: thin;
        scrollbar-color: #b5b5b5 #f1f1f1;
    }
</style>
<div class="order-list-scroll">
    <?php if (empty($orders)): ?>
        <div class="empty-orders-alert">
            <h4 class="text-muted">Không tìm thấy đơn hàng phù hợp!</h4>
            <p class="text-muted">Vui lòng thử lại với một bộ lọc khác hoặc đặt lại bộ lọc.</p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-card-header">
                    <!-- SỬA Ở ĐÂY: Dùng $order->id thay vì $order['id'] -->
                    <span class="order-id">Đơn hàng #<?php echo htmlspecialchars($order->id ?? ''); ?></span>

                    <?php
                    $status_class = 'badge-unknown';
                    $status_text = 'Không xác định';
                    // SỬA Ở ĐÂY: Dùng $order->status
                    $status = $order->status ?? '';

                    switch ($status) {
                        case 'pending':
                            $status_class = 'badge-pending';
                            $status_text = 'Chờ xử lý';
                            break;
                        case 'processing':
                            $status_class = 'badge-processing';
                            $status_text = 'Đang xử lý';
                            break;
                        case 'completed':
                            $status_class = 'badge-completed';
                            $status_text = 'Hoàn tất';
                            break;
                        case 'cancelled':
                            $status_class = 'badge-cancelled';
                            $status_text = 'Đã hủy';
                            break;
                    }
                    ?>
                    <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                </div>
                <div class="order-card-body">
                    <div class="order-info-grid">
                        <div class="info-item">
                            <span class="label"><i class="bi bi-calendar3 me-2"></i>Ngày Tạo</span>
                            <span class="value">
                                <?php
                                // SỬA Ở ĐÂY: Dùng $order->created_at
                                $createdAt = $order->created_at ?? null;
                                echo $createdAt ? htmlspecialchars(date("d/m/Y H:i", strtotime($createdAt))) : 'N/A';
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label"><i class="bi bi-credit-card me-2"></i>Thanh Toán</span>
                            <span class="value">
                                <?php
                                // 1. Tạo danh sách đối chiếu: 'giá_trị_trong_db' => 'Tên hiển thị'
                                $payment_map = [
                                    'cash'  => 'Tiền mặt',
                                    'cod'   => 'Thanh toán khi nhận hàng (COD)',
                                    'momo'  => 'Ví Momo',
                                    'vnpay' => 'VN Pay',
                                    'card'  => 'Thẻ ngân hàng'
                                ];

                                // 2. Lấy giá trị gốc từ đơn hàng (ví dụ: 'momo')
                                $raw_method = $order->payment_method ?? '';

                                // 3. In ra tên tiếng Việt. Nếu không tìm thấy trong danh sách thì in giá trị gốc
                                echo htmlspecialchars($payment_map[$raw_method] ?? ucfirst($raw_method));
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">
                                <i class="bi bi-gift me-2"></i>Khuyến mãi
                            </span>

                            <span class="value text-primary fw-semibold">
                                <?= !empty($order->promotion_name)
                                    ? htmlspecialchars($order->promotion_name)
                                    : 'Không áp dụng'
                                ?>
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="label"><i class="bi bi-receipt me-2"></i>Tổng Tiền</span>
                            <span class="value fw-bold text-danger">
                                <?php
                                // SỬA Ở ĐÂY: Dùng $order->total_amount
                                $amount = $order->total_amount ?? 0;
                                echo number_format((float)$amount, 0, ',', '.') . ' VND';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="order-card-footer">
                    <!-- SỬA Ở ĐÂY: Dùng $order->id -->
                    <a href="/webbanhang/Order/viewOrderDetails/<?php echo htmlspecialchars($order->id ?? ''); ?>" class="btn btn-dark btn-sm">
                        Xem Chi Tiết<i class="bi bi-arrow-right-short ms-2"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>