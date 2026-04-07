<?php

// Kiểm tra mã phản hồi từ VNPAY
if (isset($_GET['vnp_ResponseCode']) && $_GET['vnp_ResponseCode'] === '00') {
    // Giao dịch thành công
    header('Location: /webbanhang/app/views/order/thankYou.php');
} else {
    // Giao dịch thất bại, ghi log lỗi
    error_log("Giao dịch VNPAY thất bại - Mã lỗi: " . ($_GET['vnp_ResponseCode'] ?? 'Không có') . " - Thông báo: " . ($_GET['vnp_Message'] ?? 'Không có thông báo'));

    // Chuyển hướng tới trang Sorry
    header('Location: /webbanhang/app/views/order/Sorry.php');
    exit;
}
