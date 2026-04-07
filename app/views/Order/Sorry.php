<?php include __DIR__ . '/../shares/header.php'; ?>
<?php include __DIR__ . '/../shares/navbar.php'; ?>

<div class="container">
    <div class="payment-fail-card">

        <div class="fail-icon">
            <i class="bi bi-x-lg"></i>
        </div>

        <h1>Thanh Toán Thất Bại</h1>

        <p class="lead mt-3">
            Đừng lo lắng, bạn chưa bị trừ bất kỳ khoản phí nào.
        </p>
        <p class="text-muted">
            Đã có lỗi xảy ra trong quá trình xử lý thanh toán của bạn. Vui lòng kiểm tra lại thông tin và thử lại.
        </p>

        <!-- Hướng dẫn khắc phục sự cố -->
        <div class="troubleshooting-box">
            <h5 class="mb-3">Một số lý do phổ biến:</h5>
            <ul>
                <li><i class="bi bi-credit-card-fill"></i> Thông tin thẻ (số thẻ, ngày hết hạn, CVV) nhập chưa chính xác.</li>
                <li><i class="bi bi-exclamation-triangle-fill"></i> Tài khoản không đủ số dư để thực hiện giao dịch.</li>
                <li><i class="bi bi-shield-lock-fill"></i> Thẻ của bạn đã bị ngân hàng từ chối vì lý do bảo mật.</li>
            </ul>
        </div>

        <!-- Các nút hành động -->
        <div class="cta-buttons">
            <a href="/webbanhang/Order/detailsPreview" class="btn btn-warning btn-lg">
                <i class="bi bi-arrow-clockwise me-2"></i>Thử Lại Thanh Toán
            </a>
            <a href="/webbanhang/Cart/index" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-cart3 me-2"></i>Quay Lại Giỏ Hàng
            </a>
        </div>

        <div class="mt-4">
            <small class="text-muted">Nếu vẫn gặp sự cố, vui lòng <a href="/webbanhang/contact">liên hệ hỗ trợ</a>.</small>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../shares/footer.php'; ?>