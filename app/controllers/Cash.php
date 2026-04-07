<?php
// File: app/controllers/Cash.php

// 1. Lấy dữ liệu từ Session (Sử dụng ?? để tránh lỗi Undefined)
$account_id      = $_SESSION['account_id'];
$cartItems       = $_SESSION['cartItems'];
$totalAmount     = $_SESSION['final_total'] ?? 0; // Số tiền sau giảm giá
$payment_method  = $_SESSION['payment_method'] ?? 'cash';
$address         = $_SESSION['address'] ?? '';
$phone_number    = $_SESSION['phone_number'] ?? '';
$shippingFee     = $_SESSION['shipping_fee'] ?? 0;
$promotion_id    = $_SESSION['applied_promotion_id'] ?? null;
$discount_amount = $_SESSION['discount_amount'] ?? 0;
$transaction_id  = $_SESSION['transaction_id'] ?? uniqid('cash_');
$promoName = '';

// Nếu có dùng mã, lấy tên mã để hiện trong email
if ($promotion_id) {
    $promo = $this->promotionModel->getById($promotion_id);
    $promoName = $promo ? $promo->name : '';
}
// Kiểm tra lại lần cuối trước khi ghi DB để tránh lỗi Column cannot be null
if ($totalAmount <= 0) {
    error_log("Lỗi: Tổng tiền thanh toán bằng 0.");
    header("Location: /webbanhang/cart/error");
    exit;
}

// 2. Tạo đơn hàng
$orderId = $this->orderModel->createOrder(
    $account_id,
    $totalAmount,
    $transaction_id,
    $payment_method,
    $address,
    $phone_number,
    $shippingFee,
    $promotion_id,
    $discount_amount
);

if (!$orderId) {
    error_log("Tạo đơn hàng thất bại cho tài khoản ID: " . $account_id);
    header("Location: /webbanhang/cart/error");
    exit;
}

// 3. Tạo chi tiết đơn hàng và Trừ tồn kho
foreach ($cartItems as $item) {
    $this->orderModel->createOrderDetail($orderId, $item['product_id'], $item['quantity'], $item['price']);
    $this->productModel->decreaseProductQuantity($item['product_id'], $item['quantity']);
}

// 4. Nếu có dùng khuyến mãi -> Đánh dấu đã sử dụng để không dùng lại được nữa
if ($promotion_id) {
    $this->accountPromotionModel->usePromotion($account_id, $promotion_id);
}

// 5. Lấy thông tin tài khoản để gửi mail
$account = $this->accountModel->getAccountById($account_id);

if ($account && !empty($account->email)) {
    $helperPath = __DIR__ . '/../helpers/EmailHelper.php';
    if (file_exists($helperPath)) {
        require_once $helperPath;
        EmailHelper::sendOrderConfirmationEmail(
            $account->email,
            $orderId,
            $totalAmount,    // Số tiền cuối khách đã trả
            $cartItems,
            $address,
            $phone_number,
            $account->full_name,
            $shippingFee,
            $discount_amount, // Tham số mới 1
            $promoName        // Tham số mới 2
        );
    }
}

// 6. Dọn dẹp giỏ hàng & Session
$this->cartModel->clearSelectedCartItems($account_id);
$this->updateCartSession($account_id);

unset(
    $_SESSION['cartItems'],
    $_SESSION['totalAmount'],
    $_SESSION['final_total'],
    $_SESSION['payment_method'],
    $_SESSION['transaction_id'],
    $_SESSION['address'],
    $_SESSION['phone_number'],
    $_SESSION['applied_promotion_id'],
    $_SESSION['discount_amount']
);

// 7. Hiển thị trang cảm ơn
$order = $this->orderModel->getOrderById($orderId);
include 'app/views/order/thankYou.php';
return;
