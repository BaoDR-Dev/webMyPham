<?php
// 1. Nhúng file thư viện (Quan trọng nhất)
// Dấu ../ nghĩa là lùi ra 1 cấp thư mục. Lùi 2 lần là ra tới thư mục gốc
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // --- CẤU HÌNH SERVER (Ví dụ dùng Gmail) ---
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Bật chế độ hiện lỗi chi tiết để dễ debug
    $mail->isSMTP();                        // Dùng giao thức SMTP
    $mail->Host       = 'smtp.gmail.com';   // Server Gmail
    $mail->SMTPAuth   = true;               // Bật xác thực

    // --- TÀI KHOẢN GMAIL ---
    $mail->Username   = 'cuahangthietbiyte247@gmail.com'; // <--- Thay email của bạn vào đây
    $mail->Password   = 'fkfb zmqv rref qtaa';       // <--- Thay Mật khẩu ứng dụng vào đây (Xem hướng dẫn dưới)

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Mã hóa
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8'; // Để gửi tiếng Việt không lỗi font

    // --- NGƯỜI GỬI & NGƯỜI NHẬN ---
    $mail->setFrom('cuahangthietbiyte247@gmail.com', 'Web Ban Hang'); // Email người gửi
    $mail->addAddress('homelander19102025@gmail.com', 'Khach Hang'); // <--- Thay email người nhận để test

    // --- NỘI DUNG EMAIL ---
    $mail->isHTML(true);
    $mail->Subject = 'Test gửi mail từ Laragon';
    $mail->Body    = 'Xin chào, đây là email test gửi từ <b>PHPMailer</b> thành công!';
    $mail->AltBody = 'Xin chào, đây là email test gửi từ PHPMailer thành công!';

    $mail->send();
    echo '<h1>Gửi email thành công!</h1>';
} catch (Exception $e) {
    echo "Lỗi không gửi được email. Chi tiết: {$mail->ErrorInfo}";
}
