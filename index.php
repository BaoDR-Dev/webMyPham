<?php
session_start();

// Require các file cần thiết
require_once 'app/config/database.php';
require_once 'app/helpers/SessionHelper.php';

// Lấy URL từ request
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Xác định tên controller
$controllerName = isset($url[0]) && $url[0] !== '' ? ucfirst($url[0]) . 'Controller' : 'DefaultController';

// Xác định tên action
$action = isset($url[1]) && $url[1] !== '' ? $url[1] : 'index';

// Đường dẫn đến controller
$controllerPath = 'app/controllers/' . $controllerName . '.php';

// Kiểm tra nếu file controller tồn tại
if (!file_exists($controllerPath)) {
    http_response_code(404);
    die('Controller not found: ' . htmlspecialchars($controllerName));
}

require_once $controllerPath;

// Kiểm tra nếu lớp controller tồn tại
if (!class_exists($controllerName)) {
    http_response_code(500);
    die('Controller class not found: ' . htmlspecialchars($controllerName));
}

// Tạo đối tượng controller
$controller = new $controllerName();

// Kiểm tra nếu action tồn tại trong controller
if (!method_exists($controller, $action)) {
    http_response_code(404);
    die('Action not found: ' . htmlspecialchars($action));
}

// Lấy các tham số còn lại

$params = array_slice($url, 2);

// Gọi phương thức với tham số (xử lý lỗi thiếu tham số)
try {
    call_user_func_array([$controller, $action], $params);
} catch (ArgumentCountError $e) {
    http_response_code(400);
    die('Invalid parameters for action: ' . htmlspecialchars($action));
} catch (Exception $e) {
    http_response_code(500);
    die('An error occurred: ' . $e->getMessage());
}

// require_once 'app/controllers/AccountController.php'; // Gọi controller cần thiết

// if (isset($_GET['action'])) {
//     $action = $_GET['action'];

//     // Tất cả hành động liên quan đến Account
//     if ($action === 'changePassword' || $action === 'login' || $action === 'register') {
//         $controller = new AccountController();

//         if (method_exists($controller, $action)) {
//             $controller->{$action}(); // Gọi action trong controller
//         } else {
//             echo "Hành động không tồn tại.";
//         }
//     } else {
//         echo "Hành động không hợp lệ.";
//     }
// }
