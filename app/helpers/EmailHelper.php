<?php
// helpers/EmailHelper.php

// 1. SỬA ĐƯỜNG DẪN: Dùng __DIR__ để nối chuỗi cho chính xác tuyệt đối
// Đi ra khỏi 'helpers' (..) -> ra khỏi 'app' (..) -> vào 'vendor'
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{
    private static function configureMailer()
    {
        $mail = new PHPMailer(true);

        // 2. CẤU HÌNH SERVER
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // <--- SỬA LẠI: Phải là smtp.gmail.com
        $mail->SMTPAuth   = true;

        // Tài khoản Gmail của bạn
        $mail->Username   = 'cuahangthietbiyte247@gmail.com';
        // Mật khẩu ứng dụng (Đã đúng format, giữ nguyên)
        $mail->Password   = 'fkfb zmqv rref qtaa';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // 3. NGƯỜI GỬI:
        // Lưu ý: Gmail thường bắt buộc 'setFrom' phải trùng với 'Username' để tránh vào Spam
        // Bạn nên để email gửi là chính email gmail của bạn
        $mail->setFrom('cuahangthietbiyte247@gmail.com', 'Y Te 24/7');

        return $mail;
    }

    // app/helpers/EmailHelper.php

    public static function sendVerificationEmail($recipientEmail, $otp)
    {
        try {
            $mail = self::configureMailer();

            $mail->addAddress($recipientEmail);
            $mail->isHTML(true);
            $mail->Subject = 'Mã xác thực đăng ký (OTP)';
            $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; text-align: center;'>
                <h2>Xác thực tài khoản</h2>
                <p>Mã OTP của bạn là:</p>
                <h1 style='color: #0d6efd; letter-spacing: 5px; font-size: 32px;'>{$otp}</h1>
                <p>Mã này sẽ hết hạn sau <b>5 phút</b>.</p>
                <p>Tuyệt đối không chia sẻ mã này cho bất kỳ ai.</p>
            </div>
        ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Gửi OTP thất bại: {$mail->ErrorInfo}");
            return false;
        }
    }

    public static function sendPasswordResetEmail($recipientEmail, $token)
    {
        try {
            $mail = self::configureMailer();
            $resetLink = "http://localhost:889/webbanhang/account/resetPassword?token=" . urlencode($token);

            $mail->addAddress($recipientEmail);
            $mail->isHTML(true);
            $mail->Subject = 'Yêu cầu Đặt lại Mật khẩu';
            $mail->Body    = "<h2>Yêu cầu Đặt lại Mật khẩu</h2>"
                . "<p>Vui lòng nhấp vào liên kết dưới đây để đặt lại mật khẩu:</p>"
                . "<p><a href='{$resetLink}' style='padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px;'>Đặt lại Mật khẩu</a></p>"
                . "<p>Liên kết này sẽ <b>hết hạn sau 1 giờ</b>.</p>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Gửi email đặt lại mật khẩu thất bại: {$mail->ErrorInfo}");
            return false;
        }
    }

    // Thêm hàm mới này vào cuối class EmailHelper
    public static function sendOrderConfirmationEmail(
        $recipientEmail,
        $orderId,
        $totalAmount,       // Tổng thanh toán CUỐI CÙNG khách phải trả
        $cartItems,
        $address,
        $phone,
        $full_name,
        $shippingFee,
        $discountAmount = 0, // THÊM MỚI: Số tiền được giảm
        $promotionName = ''  // THÊM MỚI: Tên mã giảm giá
    ) {
        try {
            $mail = self::configureMailer();

            $mail->addAddress($recipientEmail);
            $mail->isHTML(true);
            $mail->Subject = "Xác nhận đơn hàng #{$orderId} - Y Tế 24/7";

            // =============================
            // 1. DANH SÁCH SẢN PHẨM & TÍNH TẠM TÍNH
            // =============================
            $itemsHtml = '';
            $calculatedSubtotal = 0; // Tính lại tổng tiền hàng thực tế

            foreach ($cartItems as $item) {
                $productName = $item['name'] ?? ('Sản phẩm #' . ($item['product_id'] ?? ''));
                $price = (float) ($item['price'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);
                $itemTotal = $price * $quantity;
                $calculatedSubtotal += $itemTotal;

                $itemsHtml .= "
                <tr>
                    <td style='padding:8px;border-bottom:1px solid #ddd;'>{$productName}</td>
                    <td style='padding:8px;border-bottom:1px solid #ddd;text-align:center;'>{$quantity}</td>
                    <td style='padding:8px;border-bottom:1px solid #ddd;text-align:right;'>"
                    . number_format($price, 0, ',', '.') . " VNĐ</td>
                    <td style='padding:8px;border-bottom:1px solid #ddd;text-align:right;'>"
                    . number_format($itemTotal, 0, ',', '.') . " VNĐ</td>
                </tr>";
            }

            // =============================
            // 2. ĐỊNH DẠNG HIỂN THỊ
            // =============================
            $shippingFormatted = ($shippingFee > 0)
                ? number_format($shippingFee, 0, ',', '.') . ' VNĐ'
                : '<span style="color:green;">Miễn phí</span>';

            // Dòng hiển thị khuyến mãi (Chỉ hiện nếu có giảm giá)
            $discountHtml = '';
            if ($discountAmount > 0) {
                $label = !empty($promotionName) ? "Giảm giá ({$promotionName})" : "Giảm giá";
                $discountHtml = "
                <tr>
                    <td colspan='3' style='padding:10px;text-align:right;color:#d9534f;'>{$label}:</td>
                    <td style='padding:10px;text-align:right;color:#d9534f;'>-" . number_format($discountAmount, 0, ',', '.') . " VNĐ</td>
                </tr>";
            }

            // =============================
            // 3. NỘI DUNG EMAIL
            // =============================
            $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #eee;padding:20px;color:#333;'>
            <div style='text-align:center;'>
                <h2 style='color:#0d6efd;'>Cảm ơn bạn đã đặt hàng!</h2>
                <p>Xin chào <strong>{$full_name}</strong>, đơn hàng <strong>#{$orderId}</strong> của bạn đã được đặt thành công và đang chờ xử lý.</p>
            </div>

            <div style='background:#f9f9f9;padding:15px;margin:20px 0;border-radius:6px;border-left:4px solid #0d6efd;'>
                <p style='margin:0;'><strong>Thông tin giao hàng</strong></p>
                <p style='margin:5px 0 0 0;'>📍 Địa chỉ: {$address}</p>
                <p style='margin:5px 0 0 0;'>📞 Số điện thoại: {$phone}</p>
            </div>

            <table style='width:100%;border-collapse:collapse;margin-bottom:20px;'>
                <thead>
                    <tr style='background:#f2f2f2;'>
                        <th style='padding:10px;text-align:left;'>Sản phẩm</th>
                        <th style='padding:10px;text-align:center;'>SL</th>
                        <th style='padding:10px;text-align:right;'>Đơn giá</th>
                        <th style='padding:10px;text-align:right;'>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan='3' style='padding:10px;text-align:right;border-top:2px solid #eee;'>Tạm tính:</td>
                        <td style='padding:10px;text-align:right;border-top:2px solid #eee;'>" . number_format($calculatedSubtotal, 0, ',', '.') . " VNĐ</td>
                    </tr>
                    <tr>
                        <td colspan='3' style='padding:10px;text-align:right;'>Phí vận chuyển:</td>
                        <td style='padding:10px;text-align:right;'>{$shippingFormatted}</td>
                    </tr>
                    {$discountHtml}
                    <tr>
                        <td colspan='3' style='padding:15px;text-align:right;font-weight:bold;font-size:16px;'>Tổng thanh toán:</td>
                        <td style='padding:15px;text-align:right;font-weight:bold;font-size:18px;color:#d9534f;'>
                            " . number_format($totalAmount, 0, ',', '.') . " VNĐ
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div style='text-align:center;font-size:12px;color:#777;margin-top:30px;'>
                <p>Chúng tôi sẽ sớm liên hệ qua điện thoại để xác nhận lịch giao hàng.</p>
                <p>Trân trọng,<br><strong>Đội ngũ Y Tế 24/7</strong></p>
            </div>
        </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Gửi email đơn hàng thất bại: " . $mail->ErrorInfo);
            return false;
        }
    }
}
