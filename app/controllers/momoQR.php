<?php

$this->ensureSessionStarted();
$totalAmount = $_SESSION['final_total'] ?? 0;

$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = 'MOMOBKUN20180529';
$accessKey = 'klm05TvNBzhg7h7j';
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
$orderInfo = "Thanh toán qua MoMo";
$orderId = time() . "";
$redirectUrl = "http://localhost:889/webbanhang/Order/thankYouMoMo";  // Chuyển về Controller xử lý thankyou
$ipnUrl = "http://localhost:889/webbanhang/Order/thankYouMoMo";
$extraData = "";

$requestId = time() . "";
$requestType = "captureWallet";

$rawHash = "accessKey=$accessKey&amount=$totalAmount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
$signature = hash_hmac("sha256", $rawHash, $secretKey);

$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    "storeId" => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $totalAmount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
);

$result = $this->execPostRequest($endpoint, json_encode($data));

if (!$result) {
    echo "Không nhận được phản hồi từ MoMo.";
    return;
}

$jsonResult = json_decode($result, true);

if (!isset($jsonResult['payUrl'])) {
    echo "Lỗi khi lấy payUrl từ MoMo: ";
    var_dump($jsonResult);
    return;
}

// Chuyển hướng người dùng sang trang thanh toán MoMo
header('Location: ' . $jsonResult['payUrl']);
exit;
