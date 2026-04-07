<?php
// Bắt đầu session để có thể truy cập $_SESSION
session_start();

// Kiểm tra xem `resultCode` có tồn tại và có bằng 0 hay không
if (isset($_GET['resultCode']) && $_GET['resultCode'] == 0) {
    // Thanh toán thành công
    if (isset($_SESSION['transaction_id'])) {
        error_log("Thanh toán MoMo thành công với transaction_id: " . $_SESSION['transaction_id']);
    }

    // 🔥 THAY ĐỔI QUAN TRỌNG:
    // Chuyển hướng đến một PHƯƠNG THỨC CONTROLLER, không phải một file view.
    header('Location: /webbanhang/order/success'); // Giả sử bạn có route này
    exit;
} else {
    // Thanh toán thất bại
    $resultCode = $_GET['resultCode'] ?? 'Không xác định';
    $message = $_GET['message'] ?? 'Không có thông báo';
    error_log("Thanh toán thất bại. resultCode: $resultCode, message: $message");

    // 🔥 THAY ĐỔI QUAN TRỌNG:
    // Chuyển hướng đến một PHƯƠNG THỨC CONTROLLER, không phải một file view.
    header('Location: /webbanhang/order/failure'); // Giả sử bạn có route này
    exit;
}
