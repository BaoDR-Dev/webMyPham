<?php include 'app/views/shares/header.php'; ?>
<style>
    /* NÚT QUAY LẠI */
    .back-btn-wrapper {
        margin-bottom: 30px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #0d6efd;
        color: #0d6efd;
        background-color: #fff;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.25s ease;
    }

    .btn-back i {
        font-size: 14px;
    }

    .btn-back:hover {
        background-color: #0d6efd;
        color: #fff;
        transform: translateX(-3px);
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.25);
    }
</style>

<div class="order-details-container">
    <div class="container">
        <!-- TIÊU ĐỀ -->
        <div class="text-center mb-5">
            <h1 class="display-5">Chi Tiết Đơn Hàng</h1>
            <p class="text-muted">Xem lại thông tin và các sản phẩm trong đơn hàng của bạn.</p>
        </div>
        <div class="back-btn-wrapper">
            <a href="/webbanhang/order/index" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Quay lại danh sách đơn hàng
            </a>
        </div>

        <div class="row">
            <!-- CỘT BÊN TRÁI: THÔNG TIN CHUNG & GIAO HÀNG -->
            <div class="col-lg-4">
                <!-- THÔNG TIN ĐƠN HÀNG -->
                <div class="info-card-detail">
                    <h4><i class="fas fa-file-invoice-dollar me-2"></i>Thông Tin Đơn Hàng</h4>
                    <ul class="list-unstyled info-list">
                        <li>
                            <span class="label">Mã Đơn Hàng:</span>
                            <span class="value">#<?php echo htmlspecialchars($order->id ?? ''); ?></span>
                        </li>
                        <li>
                            <span class="label">Ngày Đặt:</span>
                            <span class="value"><?php echo date('d/m/Y H:i', strtotime($order->created_at ?? '')); ?></span>
                        </li>
                        <li>
                            <span class="label"> Phương Thức Thanh Toán:</span>
                            <span class="value">
                                <?php
                                // Danh sách đối chiếu tên phương thức
                                $payment_map = [
                                    'cash'  => 'Tiền mặt',
                                    'cod'   => 'Thanh toán khi nhận hàng (COD)',
                                    'momo'  => 'Ví Momo',
                                    'vnpay' => 'Ví VN Pay',
                                    'card'  => 'Thẻ ngân hàng'
                                ];

                                // Lấy giá trị từ object $order
                                $method = $order->payment_method ?? '';

                                // Hiển thị tên tiếng Việt
                                echo htmlspecialchars($payment_map[$method] ?? ucfirst($method));
                                ?>
                            </span>
                        </li>
                        <li>
                            <span class="label">Tạm Tính:</span>
                            <span class="value">
                                <?php
                                $subtotal =
                                    ($order->total_amount ?? 0)
                                    + ($order->discount_amount ?? 0)
                                    - ($order->shipping_fee ?? 0);
                                echo number_format($subtotal, 0, ',', '.');
                                ?> VND
                            </span>
                        </li>
                        <li>
                            <span class="label">Phí Vận Chuyển:</span>
                            <span class="value">
                                <?php if (($order->shipping_fee ?? 0) == 0): ?>
                                    <span class="text-success fw-bold">Freeship</span>
                                <?php else: ?>
                                    <?php echo number_format($order->shipping_fee, 0, ',', '.'); ?> VND
                                <?php endif; ?>
                            </span>
                        </li>
                        <li>
                            <span class="label">
                                <i class="bi bi-gift me-2"></i>Khuyến mãi
                            </span>
                            <span class="value text-primary fw-semibold">
                                <?= !empty($order->promotion_name)
                                    ? htmlspecialchars($order->promotion_name)
                                    : 'Không áp dụng'
                                ?>
                            </span>
                        </li>

                        <?php if (($order->discount_amount ?? 0) > 0): ?>
                            <li>
                                <span class="label">
                                    <i class="bi bi-tag me-2"></i>Giảm giá
                                </span>
                                <span class="value text-success fw-bold">
                                    -<?= number_format($order->discount_amount, 0, ',', '.'); ?> VND
                                </span>
                            </li>
                        <?php endif; ?>

                        <li>
                            <span class="label">Tổng Thanh Toán:</span>
                            <span class="value text-danger fw-bold">
                                <?php echo number_format($order->total_amount, 0, ',', '.'); ?> VND
                            </span>
                        </li>

                        <li>
                            <span class="label">Trạng Thái:</span>
                            <span class="value">
                                <?php
                                $status = $order->status ?? 'pending';
                                $statusText = ucfirst($status);
                                if ($status === 'pending') $statusText = 'Chờ xử lý';
                                if ($status === 'processing') $statusText = 'Đang giao';
                                if ($status === 'completed') $statusText = 'Hoàn thành';
                                if ($status === 'cancelled') $statusText = 'Đã hủy';
                                echo "<span class='status-badge status-{$status}'>{$statusText}</span>";
                                ?>
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- THÔNG TIN GIAO HÀNG -->
                <div class="info-card-detail">
                    <h4><i class="fas fa-shipping-fast me-2"></i>Thông Tin Giao Hàng</h4>
                    <ul class="list-unstyled info-list">
                        <li>
                            <span class="label"><i class="fas fa-user me-2"></i>Người Nhận:</span>
                            <span class="value"><?php echo htmlspecialchars($customerInfo->full_name ?? ''); ?></span>
                        </li>
                        <li>
                            <span class="label"><i class="fas fa-phone-alt me-2"></i>Điện Thoại:</span>
                            <!-- Sửa: Lấy SĐT từ $order thay vì $customerInfo -->
                            <span class="value"><?php echo htmlspecialchars($order->phone_number ?? 'Không có'); ?></span>
                        </li>
                        <li>
                            <span class="label"><i class="fas fa-map-marker-alt me-2"></i>Địa Chỉ:</span>
                            <!-- Sửa: Lấy Địa chỉ từ $order thay vì $customerInfo -->
                            <span class="value"><?php echo htmlspecialchars($order->address ?? 'Không có'); ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- CỘT BÊN PHẢI: DANH SÁCH SẢN PHẨM -->
            <div class="col-lg-8">
                <div class="info-card-product">
                    <h4><i class="fas fa-box-open me-2"></i>Danh Sách Sản Phẩm</h4>
                    <?php if (!empty($orderDetails)): ?>
                        <?php foreach ($orderDetails as $detail): ?>
                            <div class="product-item-details">
                                <!-- Hình ảnh sản phẩm -->
                                <img src="/webbanhang/<?php echo htmlspecialchars($detail['product_image']); ?>" alt="<?php echo htmlspecialchars($detail['product_name']); ?>" class="product-image-details">

                                <div class="product-info">
                                    <h6><?php echo htmlspecialchars($detail['product_name']); ?></h6>
                                    <p class="text-muted mb-0">Số lượng: <?php echo $detail['quantity']; ?></p>
                                    <p class="text-muted mb-0">Đơn giá: <?php echo number_format($detail['price'], 0, ',', '.'); ?> VND</p>
                                </div>

                                <div class="product-price-section">
                                    <p class="fw-bold mb-2"><?php echo number_format($detail['quantity'] * $detail['price'], 0, ',', '.'); ?> VND</p>

                                    <!-- Nút đánh giá -->
                                    <?php if ($order->status === 'completed'): ?>
                                        <?php if ($detail['is_reviewed'] == 1): ?>
                                            <a href="/webbanhang/Rating/edit/<?php echo $detail['product_id']; ?>/<?php echo $order->id; ?>" class="btn btn-sm btn-outline-success">Sửa Đánh Giá</a>
                                        <?php else: ?>
                                            <a href="/webbanhang/Rating/create/<?php echo $detail['product_id']; ?>/<?php echo $order->id; ?>" class="btn btn-sm btn-primary">Viết Đánh Giá</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>Chờ Hoàn Thành</button>
                                    <?php endif; ?>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-danger">Không có sản phẩm nào trong đơn hàng này.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>