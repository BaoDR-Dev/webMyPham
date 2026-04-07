<?php
//VnPay_NCBNCB
$payment_method = 'vnpay';
$totalAmount = $_SESSION['final_total'] ?? 0;
// Thông tin VNPAY
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://localhost:889/webbanhang/Order/thankYouVnpay";
$vnp_TmnCode = "NDEOIIOV"; // Mã website tại VNPAY
$vnp_HashSecret = "JTBFR24RYOZRUMWD14KR3SZ4E53SC5T1"; // Chuỗi bí mật

// Dữ liệu giao dịch
$vnp_TxnRef = rand(0, 9999); // Mã đơn hàng (duy nhất)
$vnp_OrderInfo = 'Nội dung thanh toán';
$vnp_OrderType = 'billpayment';
$vnp_Amount = $totalAmount * 100; // Đơn vị tiền nhỏ hơn (VND -> X100)
$vnp_Locale = 'vn';
$vnp_BankCode = 'NCB';
$vnp_IpAddr = $_SERVER['REMOTE_ADDR']; // Địa chỉ IP của khách hàng

// Tạo dữ liệu đầu vào
$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef
);

// Nếu có mã ngân hàng, thêm vào dữ liệu
if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}

// Sắp xếp dữ liệu đầu vào theo thứ tự key
ksort($inputData);

$query = "";
$hashdata = "";
$i = 0;
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

// Tạo URL thanh toán
$vnp_Url = $vnp_Url . "?" . $query;

// Tạo chữ ký bảo mật
if (isset($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

// Chuyển hướng người dùng đến URL VNPAY
header('Location: ' . $vnp_Url);
