<?php include 'app/views/shares/header.php'; ?> <div class="container py-5"> <!-- TIÊU ĐỀ -->
    <div class="text-center mb-5 confirmation-page-title">
        <h1 class="PageTitle">Xác Nhận Đơn Hàng</h1>
        <p>Vui lòng kiểm tra lại thông tin trước khi hoàn tất.</p>
    </div>
    <form action="/webbanhang/Order/finalizeCheckout" method="POST">
        <div class="row g-4">
            <!-- ================== CỘT TRÁI ================== -->
            <div class="col-lg-5 d-flex flex-column">
                <!-- Thông Tin Giao Hàng -->
                <div class="info-card mb-4">
                    <div class="info-card-header"> <i class="fas fa-user-circle"></i>
                        <h5>Thông Tin Giao Hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="customer-info-grid">
                            <div class="label">Họ và Tên:</div>
                            <div class="value"><?= htmlspecialchars($customerInfo->full_name); ?></div>
                            <div class="label">Email:</div>
                            <div class="value"><?= htmlspecialchars($customerInfo->email); ?></div> <label class="label">Số Điện Thoại:</label> <input type="tel" name="phone_number" class="form-control" value="<?= htmlspecialchars($customerInfo->phone_number ?? '') ?>" required> <label class="label">Địa Chỉ:</label> <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($customerInfo->address ?? '') ?>" required>
                        </div>
                    </div>
                </div> <!-- Thanh toán -->
                <div class="info-card">
                    <div class="info-card-header"> <i class="fas fa-credit-card"></i>
                        <h5>Phương Thức Thanh Toán</h5>
                    </div>
                    <input type="hidden" name="promotion_id"
                        value="<?= (int)($selected_promo_id ?? 1) ?>">

                    <div class="card-body">
                        <div class="vstack gap-3"> <label class="payment-option"> <input type="radio" name="payment_method" value="cash" checked> <img src="/webbanhang/public/images/tienmat.jpg"> <span>Thanh toán khi nhận hàng</span> </label> <label class="payment-option"> <input type="radio" name="payment_method" value="momo_qr"> <img src="/webbanhang/public/images/momoqr.webp"> <span>MoMo QR</span> </label> <label class="payment-option"> <input type="radio" name="payment_method" value="momo_atm"> <img src="/webbanhang/public/images/momo.webp"> <span>Tài khoản MoMo</span> </label> <label class="payment-option"> <input type="radio" name="payment_method" value="vnpay"> <img src="/webbanhang/public/images/vnpay.png"> <span>VNPAY</span> </label> </div> <button class="btn btn-primary w-100 mt-4 fw-bold"> HOÀN TẤT ĐƠN HÀNG </button>
                    </div>
                </div>
            </div> <!-- ================== CỘT PHẢI (ĐÃ TỐI ƯU) ================== -->
            <div class="col-lg-7">
                <div class="bg-white border-0 shadow-sm rounded-4 p-4">

                    <!-- HEADER -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Tóm tắt đơn hàng</h5>
                        <span class="badge bg-light text-dark border rounded-pill">
                            <?= count($_SESSION['cartItems']) ?> loại sản phẩm
                        </span>
                    </div>

                    <!-- DANH SÁCH SẢN PHẨM -->
                    <div class="vstack gap-4 mb-4">
                        <?php foreach ($_SESSION['cartItems'] as $item): ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <img src="/webbanhang/<?= htmlspecialchars($item['image']); ?>"
                                            width="70" height="70" class="rounded-3 border object-fit-cover">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                                            <?= $item['quantity']; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($item['name']); ?></div>
                                        <div class="text-muted small">
                                            Đơn giá: <?= number_format($item['price'], 0, ',', '.'); ?>đ
                                        </div>
                                    </div>
                                </div>
                                <div class="fw-bold text-primary">
                                    <?= number_format($item['quantity'] * $item['price'], 0, ',', '.'); ?>đ
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-4 opacity-50">

                    <!-- CHỌN KHUYẾN MÃI -->
                    <div class="mb-4 bg-light p-3 rounded-3">
                        <label class="form-label small fw-bold text-muted mb-2">
                            <i class="fas fa-ticket-alt me-1"></i> Mã khuyến mãi
                        </label>

                        <select class="form-select border-0 shadow-none"
                            onchange="window.location.href='?promotion_id=' + this.value">

                            <!-- MẶC ĐỊNH -->
                            <option value="1" <?= ($selected_promo_id == 1) ? 'selected' : '' ?>>
                                Không áp dụng khuyến mãi
                            </option>

                            <!-- CHỈ HIỂN THỊ KHUYẾN MÃI CÒN HIỆU LỰC -->
                            <?php foreach ($userPromotions as $up): ?>
                                <?php
                                // Ẩn khuyến mãi nếu status = 0
                                if ((int)($up->status ?? 0) !== 1) {
                                    continue;
                                }
                                ?>
                                <option value="<?= $up->promotion_id ?>"
                                    <?= ($selected_promo_id == $up->promotion_id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($up->promotion_name ?? $up->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>



                        <?php if ($applied_promo && $selected_promo_id != 1): ?>
                            <div class="small text-success mt-2 fw-bold">
                                <i class="bi bi-patch-check-fill me-1"></i> Đang áp dụng ưu đãi thành công
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TÍNH TOÁN TIỀN -->
                    <?php
                    $totalAmount = $_SESSION['totalAmount'];
                    $shippingFee = ($totalAmount > 3000000) ? 0 : 150000;
                    $discount_amount = 0;

                    if ($applied_promo) {
                        if ($applied_promo->promotion_type_id == 1) // Giảm %
                            $discount_amount = $totalAmount * ($applied_promo->discount_value / 100);
                        elseif ($applied_promo->promotion_type_id == 2) // Giảm tiền mặt
                            $discount_amount = $applied_promo->discount_value;
                        elseif ($applied_promo->promotion_type_id == 3) // Freeship
                            $discount_amount = $shippingFee;
                    }

                    $finalTotal = $totalAmount + $shippingFee - $discount_amount;

                    // CẬP NHẬT LẠI SESSION ĐỂ TRANG THANH TOÁN LẤY DỮ LIỆU CHÍNH XÁC
                    $_SESSION['shipping_fee'] = $shippingFee;
                    $_SESSION['discount_amount'] = $discount_amount;
                    $_SESSION['final_total'] = $finalTotal;
                    $_SESSION['applied_promotion_id'] = $selected_promo_id;
                    ?>

                    <div class="vstack gap-2 text-muted small">
                        <div class="d-flex justify-content-between">
                            <span>Tạm tính (Sản phẩm)</span>
                            <span class="text-dark fw-medium"><?= number_format($totalAmount, 0, ',', '.'); ?>đ</span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Phí giao hàng</span>
                            <span class="text-dark fw-medium">
                                <?= $shippingFee == 0 ? '<span class="text-success fw-bold">Miễn phí</span>' : number_format($shippingFee, 0, ',', '.') . 'đ'; ?>
                            </span>
                        </div>

                        <?php if ($discount_amount > 0): ?>
                            <div class="d-flex justify-content-between text-danger fw-bold">
                                <span>Giảm giá <?= ($applied_promo && $applied_promo->promotion_type_id == 3) ? '(Freeship)' : '' ?></span>
                                <span>-<?= number_format($discount_amount, 0, ',', '.'); ?>đ</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TỔNG CỘNG CUỐI CÙNG -->
                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center bg-dark text-white p-4 rounded-4 shadow">
                        <div>
                            <div class="fw-bold fs-5">Tổng thanh toán</div>
                            <div class="small opacity-75">Đã bao gồm VAT & Phí ship</div>
                        </div>
                        <div class="fs-2 fw-bolder">
                            <?= number_format($finalTotal, 0, ',', '.'); ?>đ
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </form>
</div> <?php include 'app/views/shares/footer.php'; ?>