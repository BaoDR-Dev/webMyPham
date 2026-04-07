<?php

// Kiểm tra mã phản hồi từ VNPAY
if (isset($_GET['vnp_ResponseCode']) && $_GET['vnp_ResponseCode'] === '00') {
    // Giao dịch thành công
    header('Location: ' . BASE_URL . '/order/thankYou');
} else {
    // Giao dịch thất bại, ghi log lỗi
    error_log("Giao dịch VNPAY thất bại - Mã lỗi: " . ($_GET['vnp_ResponseCode'] ?? 'Không có') . " - Thông báo: " . ($_GET['vnp_Message'] ?? 'Không có thông báo'));

    // Chuyển hướng tới trang Sorry
    header('Location: ' . BASE_URL . '/order/sorry');
    exit;
}
