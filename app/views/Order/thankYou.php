<?php include __DIR__ . '/../shares/header.php'; ?>
<?php include __DIR__ . '/../shares/navbar.php'; ?>

<div class="container">
    <div class="order-success-card">

        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>

        <h1>Đặt Hàng Thành Công!</h1>

        <p class="lead mt-3">
            Cảm ơn bạn đã tin tưởng và mua sắm tại cửa hàng của chúng tôi.
            Đơn hàng của bạn đã được xác nhận.
        </p>

        <!-- Hiển thị thông tin đơn hàng quan trọng -->
        <div class="order-details-summary">
            <p>Mã đơn hàng của bạn là:
                <strong>#<?php echo htmlspecialchars($order->id ?? 'N/A'); ?></strong>
            </p>
            <p>
                Một email xác nhận chi tiết đã được gửi đến:
                <strong><?php echo htmlspecialchars($account->email ?? 'email của bạn'); ?></strong>
            </p>
        </div>


        <!-- Các bước tiếp theo -->
        <div class="next-steps">
            <h5 class="mb-3">Các bước tiếp theo:</h5>
            <ul>
                <li><i class="bi bi-arrow-right-circle-fill"></i> Chúng tôi đang xử lý và chuẩn bị sản phẩm.</li>
                <li><i class="bi bi-arrow-right-circle-fill"></i> Bạn sẽ nhận được thông báo khi đơn hàng bắt đầu được giao.</li>
                <li><i class="bi bi-arrow-right-circle-fill"></i> Bạn có thể xem lại chi tiết đơn hàng bất cứ lúc nào trong lịch sử mua hàng.</li>
            </ul>
        </div>

        <!-- Các nút hành động -->
        <div class="cta-buttons">
            <a href="/webbanhang/Order/viewOrderDetails/<?php echo htmlspecialchars($order->id ?? ''); ?>" class="btn btn-success btn-lg">
                <i class="bi bi-receipt me-2"></i>Xem Chi Tiết Đơn Hàng
            </a>
            <a href="/webbanhang/product/home" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i>Tiếp Tục Mua Sắm
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../shares/footer.php'; ?>