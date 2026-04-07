<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <h1 class="text-center">💳 Thanh toán đơn hàng</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-warning text-center">Giỏ hàng trống! Không thể thanh toán.</div>
        <div class="text-center">
            <a href="/webbanhang/Product/" class="btn btn-primary">Quay lại mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-6">
                <h3>Thông tin khách hàng</h3>
                <form method="POST" action="/webbanhang/Product/processCheckout">
                    <div class="form-group">
                        <label>Họ và Tên:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại:</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ giao hàng:</label>
                        <textarea name="address" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg btn-block">Xác nhận đặt hàng 🛍️</button>
                </form>
            </div>

            <div class="col-md-6">
                <h3>Đơn hàng của bạn</h3>
                <ul class="list-group">
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>
                        <span><?php echo number_format($subtotal, 0, ',', '.'); ?> VND</span>
                    </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <strong>Tổng tiền</strong>
                        <strong><?php echo number_format($total, 0, ',', '.'); ?> VND</strong>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>
