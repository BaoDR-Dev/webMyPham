<?php
require_once('app/config/database.php');
require_once('app/models/OrderModel.php');
require_once('app/models/CartModel.php');
require_once('app/models/AccountModel.php'); // Thêm AccountModel
require_once('app/models/ProductModel.php');
require_once('app/models/OrderDetailsModel.php');
require_once('app/models/AccountPromotionModel.php');
require_once('app/models/PromotionModel.php');

class OrderController
{
    private $db;
    private $orderModel;
    private $cartModel;
    private $accountModel;
    private $productModel;
    private $orderDetailsModel; // Thêm OrderDetailsModel
    private $accountPromotionModel;
    private $promotionModel;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->orderModel = new OrderModel($this->db);
        $this->cartModel = new CartModel($this->db);
        $this->accountModel = new AccountModel($this->db);
        $this->productModel = new ProductModel($this->db);
        $this->orderDetailsModel = new OrderDetailsModel($this->db); // Khởi tạo OrderDetailsModel
        $this->accountPromotionModel = new AccountPromotionModel($this->db);
        $this->promotionModel = new PromotionModel($this->db);
    }


    private function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Hiển thị danh sách đơn hàng
    public function index()
    {
        $this->ensureSessionStarted();

        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            include 'app/views/account/login.php';
            return;
        }

        $orders = $this->orderModel->getOrdersByAccountId($account_id);

        include 'app/views/order/orders.php';
    }

    public function cancelOrder($orderId)
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;

        if (!$account_id) {
            echo "Bạn cần đăng nhập để tiếp tục.";
            return;
        }

        $order = $this->orderModel->getOrderById($orderId);

        if (!$order || $order->account_id != $account_id) {
            echo "Không tìm thấy đơn hàng hoặc bạn không có quyền.";
            return;
        }

        if ($order->status !== 'pending') {
            echo "Chỉ đơn hàng ở trạng thái 'Chưa xử lý' mới có thể hủy.";
            return;
        }

        $this->orderModel->updateOrderStatus($orderId, 'cancelled');

        // 👇 Gọi lại danh sách đơn hàng sau khi huỷ
        return $this->index();
    }



    private function updateCartSession($account_id)
    {
        $cartItems = $this->cartModel->getCartItems($account_id);
        $_SESSION['cart'] = [];
        foreach ($cartItems as $item) {
            $_SESSION['cart'][$item['product_id']] = [
                'name'         => $item['name'],
                'price'        => $item['price'],
                'image'        => $item['image'],
                'quantity'     => $item['quantity'],
                'max_quantity' => $item['max_quantity'] ?? 0, // Đảm bảo max_quantity có giá trị
                'toggle'       => $item['toggle'] ?? 1, // Đảm bảo toggle có giá trị mặc định là 1 (hoặc phù hợp)
            ];
        }
        // Tính tổng số lượng sản phẩm
        $_SESSION['cart_quantity'] = $this->cartModel->getTotalCartQuantity($account_id);
    }

    public function checkout()
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;

        if (!$account_id) {
            echo "Bạn cần đăng nhập để xem đơn hàng.";
            return;
        }

        // Check if "Mua Ngay" data exists in POST
        $product_id = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;

        if ($product_id && $quantity) {
            // Fetch product details for "Mua Ngay"
            $product = $this->productModel->getProductById($product_id);
            if (!$product) {
                echo "Sản phẩm không tồn tại.";
                return;
            }

            // Build temporary cart for single product purchase
            $cartItems = [
                [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'name' => $product->name,
                    'image' => $product->image
                ]
            ];
        } else {
            // Regular cart checkout
            $cartItems = $this->cartModel->getSelectedCartItems($account_id);

            if (empty($cartItems)) {
                echo "Giỏ hàng của bạn không có sản phẩm nào được chọn.";
                return;
            }
        }

        // Tính tổng tiền
        $totalAmount = array_reduce($cartItems, function ($sum, $item) {
            return $sum + ($item['quantity'] * $item['price']);
        }, 0);

        // Store cart items and total amount into session
        $_SESSION['cartItems'] = $cartItems;
        $_SESSION['totalAmount'] = $totalAmount;

        // Redirect to details preview page
        header('Location: /webbanhang/Order/detailsPreview');
        exit;
    }




    public function detailsPreview()
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            echo "Bạn cần đăng nhập.";
            return;
        }

        $cartItems = $_SESSION['cartItems'] ?? null;
        $totalAmount = $_SESSION['totalAmount'] ?? 0;

        // 1. Lấy thông tin khách hàng
        $customerInfo = $this->accountModel->getProfileByEmail($_SESSION['email']);

        // 2. Lấy danh sách khuyến mãi mà User này đang có
        // Giả sử bạn đã khởi tạo $this->accountPromotionModel trong __construct
        $userPromotions = $this->accountPromotionModel->getByAccount($account_id, 100, 0);

        // 3. Kiểm tra xem user có đang chọn mã nào không (qua URL ?promotion_id=...)
        $selected_promo_id = $_GET['promotion_id'] ?? null;
        $discount_amount = 0;
        $applied_promo = null;

        if ($selected_promo_id) {
            $applied_promo = $this->promotionModel->getById($selected_promo_id);

            // Logic tính giảm giá
            if ($applied_promo && $totalAmount >= $applied_promo->min_order_amount) {
                if ($applied_promo->promotion_type_id == 1) { // Giảm %
                    $discount_amount = $totalAmount * ($applied_promo->discount_value / 100);
                } elseif ($applied_promo->promotion_type_id == 2) { // Giảm tiền thẳng
                    $discount_amount = $applied_promo->discount_value;
                }
                // Loại 3 (Freeship) sẽ xử lý ở bước tính phí ship trong View
            }
        }

        // 4. Lưu vào Session để tí nữa file finalizeCheckout/Cash dùng
        $_SESSION['applied_promotion_id'] = $selected_promo_id;
        $_SESSION['discount_amount'] = $discount_amount;

        include 'app/views/order/detailsPreview.php';
    }

    function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        // Bỏ kiểm tra SSL nếu test
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "❌ CURL Error: " . curl_error($ch);
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return $result;
    }



    public function finalizeCheckout()
    {
        $this->ensureSessionStarted();

        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            echo "Bạn cần đăng nhập.";
            return;
        }

        $cartItems   = $_SESSION['cartItems'] ?? [];
        $totalAmount = $_SESSION['totalAmount'] ?? 0;

        if (empty($cartItems) || $totalAmount <= 0) {
            echo "Giỏ hàng trống hoặc không hợp lệ.";
            return;
        }

        // ===== 1. LẤY DỮ LIỆU POST =====
        $address      = $_POST['address'] ?? null;
        $phone_number = $_POST['phone_number'] ?? null;
        $promotion_id = (int)($_POST['promotion_id'] ?? 1); // 🔥 SỬA Ở ĐÂY

        if (!$address || !$phone_number) {
            echo "Vui lòng nhập đầy đủ địa chỉ và số điện thoại.";
            return;
        }

        // ===== 2. TÍNH PHÍ SHIP =====
        $shippingFee = ($totalAmount > 3000000) ? 0 : 150000;
        $discount_amount = 0;
        $appliedPromo = null;

        // ===== 3. KIỂM TRA KHUYẾN MÃI HỢP LỆ =====
        if ($promotion_id !== 1) {

            // 🔥 LẤY KHUYẾN MÃI THUỘC USER
            $appliedPromo = $this->promotionModel
                ->getUserPromotionById($account_id, $promotion_id);

            if (
                $appliedPromo &&
                (int)$appliedPromo->status === 1 &&
                strtotime($appliedPromo->end_date) >= time() &&
                $totalAmount >= $appliedPromo->min_order_amount
            ) {
                if ($appliedPromo->promotion_type_id == 1) {
                    $discount_amount = $totalAmount * ($appliedPromo->discount_value / 100);
                } elseif ($appliedPromo->promotion_type_id == 2) {
                    $discount_amount = $appliedPromo->discount_value;
                } elseif ($appliedPromo->promotion_type_id == 3) {
                    $discount_amount = $shippingFee;
                }
            } else {
                // ❌ Khuyến mãi không hợp lệ → reset
                $promotion_id = 1;
            }
        }

        // ===== 4. TỔNG CUỐI =====
        $finalTotal = max(0, $totalAmount + $shippingFee - $discount_amount);

        // ===== 5. LƯU SESSION =====
        $_SESSION['address']               = $address;
        $_SESSION['phone_number']          = $phone_number;
        $_SESSION['shipping_fee']          = $shippingFee;
        $_SESSION['discount_amount']       = $discount_amount;
        $_SESSION['final_total']           = $finalTotal;
        $_SESSION['applied_promotion_id']  = $promotion_id;

        // ===== 6. THANH TOÁN =====
        $selected_method = $_POST['payment_method'] ?? 'cash';
        $_SESSION['payment_method'] = $selected_method;

        switch ($selected_method) {
            case 'cash':
                include __DIR__ . '/Cash.php';
                break;
            case 'momo_qr':
                include __DIR__ . '/momoQR.php';
                break;
            case 'momo_atm':
                include __DIR__ . '/Momo.php';
                break;
            case 'vnpay':
                include __DIR__ . '/VnPay_NCB.php';
                break;
            default:
                echo "Phương thức thanh toán không hợp lệ.";
                return;
        }
    }


    private function createOrderAndRedirectThankYou()
    {
        $account_id = $_SESSION['account_id'];
        $cartItems = $_SESSION['cartItems'];
        $totalAmount = $_SESSION['totalAmount'];
        $payment_method = $_SESSION['payment_method'];

        // --- BẮT ĐẦU THAY ĐỔI ---
        // Lấy địa chỉ và SĐT từ session
        $address = $_SESSION['address'];
        $phone_number = $_SESSION['phone_number'];
        // --- KẾT THÚC THAY ĐỔI ---

        $transaction_id = uniqid(); // Gán mã giao dịch tự tạo

        // Gọi hàm createOrder với đủ tham số
        $orderId = $this->orderModel->createOrder($account_id, $totalAmount, $transaction_id, $payment_method, $address, $phone_number);

        if ($orderId) {
            foreach ($cartItems as $item) {
                $this->orderModel->createOrderDetail($orderId, $item['product_id'], $item['quantity'], $item['price']);
                $this->productModel->decreaseProductQuantity($item['product_id'], $item['quantity']);
            }

            // Xóa giỏ hàng và các thông tin liên quan trong session
            unset($_SESSION['cartItems']);
            unset($_SESSION['totalAmount']);
            unset($_SESSION['payment_method']);
            // --- BẮT ĐẦU THAY ĐỔI ---
            unset($_SESSION['address']);
            unset($_SESSION['phone_number']);
            // --- KẾT THÚC THAY ĐỔI ---

            // Chuyển về trang cảm ơn
            header("Location: /webbanhang/Order/thankYouSuccess");
            exit;
        } else {
            // Xử lý lỗi nếu không tạo được đơn hàng
            error_log("Không thể tạo đơn hàng từ createOrderAndRedirectThankYou");
            // Chuyển hướng về trang giỏ hàng hoặc trang lỗi
            header("Location: /webbanhang/cart/error");
            exit;
        }
    }


    public function thankYouSuccess()
    {
        $this->ensureSessionStarted();

        $orderId = $_SESSION['last_order_id'] ?? null;
        $account_id = $_SESSION['account_id'] ?? null;

        if (!$orderId || !$account_id) {
            echo "Không tìm thấy thông tin đơn hàng!";
            return;
        }

        $order = $this->orderModel->getOrderById($orderId);
        $account = $this->accountModel->getAccountById($account_id);
        var_dump($order);
        var_dump($account);
        exit;

        include 'app/views/order/thankYou.php';
    }


    public function thankYouMoMo()
    {
        $this->ensureSessionStarted();
        $_SESSION['payment_method'] = 'momo';
        $resultCode = $_GET['resultCode'] ?? null;
        $message = $_GET['message'] ?? '';
        $transaction_id = $_GET['transId'] ?? 'default_transaction_id';
        $orderInfo = $_GET['orderInfo'] ?? '';
        $amount = $_GET['amount'] ?? 0;

        if ($resultCode === null) {
            echo "Không nhận được kết quả thanh toán.";
            return;
        }

        if ($resultCode != 0) {
            include 'app/views/order/sorry.php';
            return;
        }

        // Thanh toán thành công, tiến hành tạo đơn hàng
        $account_id = $_SESSION['account_id'] ?? null;
        $cartItems = $_SESSION['cartItems'] ?? null;

        // --- BẮT ĐẦU CẬP NHẬT LOGIC KHUYẾN MÃI ---
        // Sử dụng final_total (số tiền sau khi đã trừ khuyến mãi) để lưu vào DB
        $totalAmount = $_SESSION['final_total'] ?? $_SESSION['totalAmount'];
        $address = $_SESSION['address'] ?? null;
        $phone_number = $_SESSION['phone_number'] ?? null;
        $shippingFee  = $_SESSION['shipping_fee'] ?? 0;

        // Lấy thông tin khuyến mãi từ session
        $promotion_id = $_SESSION['applied_promotion_id'] ?? null;
        $discount_amount = $_SESSION['discount_amount'] ?? 0;
        // --- KẾT THÚC CẬP NHẬT ---

        if (!$account_id || !$cartItems || !$totalAmount || !$address || !$phone_number) {
            error_log("Thiếu thông tin quan trọng (tài khoản, giỏ hàng, hoặc địa chỉ) trong session khi xử lý callback MoMo.");
            echo "Không tìm thấy thông tin đơn hàng đầy đủ.";
            return;
        }
        if ($promotion_id) {
            $promo = $this->promotionModel->getById($promotion_id);
            $promoName = $promo ? $promo->name : '';
        }
        $payment_method = $_SESSION['payment_method'] ?? 'momo';

        // --- CẬP NHẬT: Truyền đầy đủ 9 tham số vào hàm createOrder ---
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

        if ($orderId) {
            // --- CẬP NHẬT: Đánh dấu mã khuyến mãi đã được sử dụng ---
            if ($promotion_id) {
                $this->accountPromotionModel->usePromotion($account_id, $promotion_id);
            }

            // Tạo chi tiết đơn hàng và trừ tồn kho
            foreach ($cartItems as $item) {
                $this->orderModel->createOrderDetail($orderId, $item['product_id'], $item['quantity'], $item['price']);
                $this->productModel->decreaseProductQuantity($item['product_id'], $item['quantity']);
            }

            // Xử lý giỏ hàng
            $this->cartModel->clearSelectedCartItems($account_id);
            $this->updateCartSession($account_id);

            // Gửi email xác nhận
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

            // --- CẬP NHẬT: Xóa các session liên quan khuyến mãi và thanh toán ---
            unset(
                $_SESSION['cartItems'],
                $_SESSION['totalAmount'],
                $_SESSION['final_total'],
                $_SESSION['payment_method'],
                $_SESSION['address'],
                $_SESSION['phone_number'],
                $_SESSION['shipping_fee'],
                $_SESSION['applied_promotion_id'],
                $_SESSION['discount_amount']
            );

            // Hiển thị trang cảm ơn
            $order = $this->orderModel->getOrderById($orderId);
            include 'app/views/order/thankYou.php';
        } else {
            echo "Lỗi khi lưu đơn hàng vào hệ thống.";
        }
    }


    public function thankYouVnpay()
    {
        $this->ensureSessionStarted();

        $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? null;
        $vnp_TransactionStatus = $_GET['vnp_TransactionStatus'] ?? null;
        $transaction_id = $_GET['vnp_TransactionNo'] ?? 'default_transaction_id';
        $amount = ($_GET['vnp_Amount'] ?? 0) / 100; // Số tiền từ VNPAY (nếu cần đối soát)
        $orderInfo = $_GET['vnp_OrderInfo'] ?? '';

        if ($vnp_ResponseCode === null) {
            echo "Không nhận được kết quả thanh toán.";
            return;
        }

        // VNPAY trả về '00' là thành công
        if ($vnp_ResponseCode != '00' || $vnp_TransactionStatus != '00') {
            include 'app/views/order/sorry.php';
            return;
        }

        // --- BẮT ĐẦU LẤY THÔNG TIN TỪ SESSION ---
        $account_id = $_SESSION['account_id'] ?? null;
        $cartItems = $_SESSION['cartItems'] ?? null;

        // Ưu tiên lấy số tiền cuối cùng (đã trừ giảm giá) từ session
        $totalAmount = $_SESSION['final_total'] ?? $_SESSION['totalAmount'];

        $address = $_SESSION['address'] ?? null;
        $phone_number = $_SESSION['phone_number'] ?? null;
        $shippingFee  = $_SESSION['shipping_fee'] ?? 0;

        // Lấy thông tin khuyến mãi
        $promotion_id = $_SESSION['applied_promotion_id'] ?? null;
        $discount_amount = $_SESSION['discount_amount'] ?? 0;
        // --- KẾT THÚC ---

        // Kiểm tra dữ liệu (Lưu ý: shippingFee có thể là 0 nên dùng isset thay vì !)
        if (!$account_id || !$cartItems || !$totalAmount || !$address || !$phone_number || !isset($shippingFee)) {
            error_log("Thiếu thông tin quan trọng trong session khi xử lý callback VNPAY.");
            echo "Không tìm thấy thông tin đơn hàng đầy đủ.";
            return;
        }

        $payment_method = $_SESSION['payment_method'] ?? 'vnpay';

        // --- CẬP NHẬT: Truyền đầy đủ 9 tham số vào hàm createOrder ---
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

        if ($orderId) {
            // --- CẬP NHẬT: Đánh dấu mã khuyến mãi đã được sử dụng ---
            if ($promotion_id) {
                $this->accountPromotionModel->usePromotion($account_id, $promotion_id);
            }

            // Tạo chi tiết đơn hàng và giảm số lượng tồn kho sản phẩm
            foreach ($cartItems as $item) {
                $this->orderModel->createOrderDetail($orderId, $item['product_id'], $item['quantity'], $item['price']);
                $this->productModel->decreaseProductQuantity($item['product_id'], $item['quantity']);
            }

            // Xóa giỏ hàng trong DB và cập nhật lại session giỏ hàng nhỏ (nếu có)
            $this->cartModel->clearSelectedCartItems($account_id);
            $this->updateCartSession($account_id);

            // Lấy thông tin tài khoản để gửi mail xác nhận
            $account = $this->accountModel->getAccountById($account_id);
            if ($account && !empty($account->email)) {
                $helperPath = __DIR__ . '/../helpers/EmailHelper.php';
                if (file_exists($helperPath)) {
                    require_once $helperPath;
                    EmailHelper::sendOrderConfirmationEmail(
                        $account->email,
                        $orderId,
                        $totalAmount,
                        $cartItems,
                        $address,
                        $phone_number,
                        $account->full_name,
                        $shippingFee
                    );
                }
            }

            // --- CẬP NHẬT: Dọn dẹp sạch sẽ Session sau khi hoàn tất ---
            unset(
                $_SESSION['cartItems'],
                $_SESSION['totalAmount'],
                $_SESSION['final_total'],
                $_SESSION['payment_method'],
                $_SESSION['address'],
                $_SESSION['phone_number'],
                $_SESSION['shipping_fee'],
                $_SESSION['applied_promotion_id'],
                $_SESSION['discount_amount']
            );

            // Lấy lại thông tin đơn hàng và tài khoản để hiển thị trang cảm ơn
            $order = $this->orderModel->getOrderById($orderId);
            $account = $this->accountModel->getAccountById($account_id);

            include 'app/views/order/thankYou.php';
        } else {
            error_log("Lỗi tạo đơn hàng VNPAY cho Account ID: " . $account_id);
            echo "Có lỗi xảy ra trong quá trình tạo đơn hàng.";
        }
    }


    public function viewOrderDetails($orderId)
    {
        $this->ensureSessionStarted();

        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            echo "Bạn cần đăng nhập để xem chi tiết đơn hàng.";
            return;
        }

        // Lấy thông tin đơn hàng từ model
        $order = $this->orderModel->getOrderById($orderId);

        if (!$order) {
            echo "Không tìm thấy đơn hàng.";
            return;
        }

        // Lấy thông tin khách hàng dựa trên `account_id` của đơn hàng
        $customerInfo = $this->accountModel->getAccountById($order->account_id);

        if (!$customerInfo) {
            echo "Không tìm thấy thông tin khách hàng.";
            return;
        }

        // Lấy chi tiết đơn hàng
        $orderDetails = $this->orderDetailsModel->getOrderDetailsByOrderId($orderId);

        // Kiểm tra và truyền dữ liệu tới view
        include 'app/views/order/details.php';
    }



    // Xem chi tiết đơn hàng
    public function view($orderId)
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;

        if (!$account_id) {
            echo "Bạn cần đăng nhập để xem chi tiết đơn hàng.";
            return;
        }

        $order = $this->orderModel->getOrderById($orderId, $account_id);

        if (!$order) {
            echo "Đơn hàng không tồn tại.";
            return;
        }

        // Lấy thông tin chi tiết đơn hàng
        $orderDetails = $this->orderModel->getOrderDetails($orderId);

        // Lấy thông tin khách hàng từ AccountModel
        $customerInfo = $this->accountModel->getProfileByEmail($_SESSION['email']);

        // Hiển thị trang chi tiết đơn hàng
        include 'app/views/order/details.php';
    }

    public function filterForUser()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /webbanhang/Auth/login');
            exit;
        }

        $filters = [
            'status'          => is_array($_GET['status'] ?? null) ? $_GET['status'] : [],
            'min_amount'      => isset($_GET['min_amount']) ? (float)$_GET['min_amount'] : null,
            'max_amount'      => isset($_GET['max_amount']) ? (float)$_GET['max_amount'] : null,
            'start_date'      => $_GET['start_date'] ?? null,
            'end_date'        => $_GET['end_date'] ?? null,
            'payment_methods' => is_array($_GET['payment_methods'] ?? null) ? $_GET['payment_methods'] : [],

            // ✅ Thêm lọc theo mã đơn hàng
            'min_order_id'    => isset($_GET['min_order_id']) ? (int)$_GET['min_order_id'] : null,
            'max_order_id'    => isset($_GET['max_order_id']) ? (int)$_GET['max_order_id'] : null,
        ];

        // Bắt buộc chỉ lấy đơn hàng của user hiện tại
        $filters['account_id'] = $_SESSION['user_id'];

        $orders = $this->orderModel->filterOrdersForUser($filters);

        require_once __DIR__ . '/../views/user/order_filter.php';
    }

    // public function ajaxFilterForUser()
    // {
    //     if (session_status() === PHP_SESSION_NONE) session_start();
    //     if (!isset($_SESSION['user_id'])) {
    //         http_response_code(403);
    //         echo "Bạn chưa đăng nhập!";
    //         return;
    //     }

    //     $filters = [
    //         'status'          => is_array($_GET['status'] ?? null) ? $_GET['status'] : [],
    //         'min_amount'      => isset($_GET['min_amount']) ? (float)$_GET['min_amount'] : null,
    //         'max_amount'      => isset($_GET['max_amount']) ? (float)$_GET['max_amount'] : null,
    //         'start_date'      => $_GET['start_date'] ?? null,
    //         'end_date'        => $_GET['end_date'] ?? null,
    //         'payment_methods' => is_array($_GET['payment_methods'] ?? null) ? $_GET['payment_methods'] : [],
    //         'min_order_id'    => isset($_GET['min_order_id']) ? (int)$_GET['min_order_id'] : null,
    //         'max_order_id'    => isset($_GET['max_order_id']) ? (int)$_GET['max_order_id'] : null,
    //         'account_id'      => $_SESSION['user_id'],
    //     ];

    //     $orders = $this->orderModel->filterOrdersForUser($filters);

    //     if (empty($orders)) {
    //         echo '<div class="alert alert-warning text-center">Không tìm thấy đơn hàng phù hợp!</div>';
    //     } else {
    //         include __DIR__ . '/../views/order/listorder.php';
    //     }
    // }
    public function ajaxFilterForUser()
    {
        $filters = [];

        if (!empty($_POST['enable_order_id'])) {
            $minId = isset($_POST['min_order_id']) && $_POST['min_order_id'] !== '' ? (int)$_POST['min_order_id'] : null;
            $maxId = isset($_POST['max_order_id']) && $_POST['max_order_id'] !== '' ? (int)$_POST['max_order_id'] : null;
            if ($minId !== null || $maxId !== null) {
                $filters['min_order_id'] = $minId;
                $filters['max_order_id'] = $maxId;
                $filters['enable_order_id'] = true;
            }
        }

        if (!empty($_POST['enable_status']) && !empty($_POST['status']) && is_array($_POST['status'])) {
            $filters['status'] = $_POST['status'];
            $filters['enable_status'] = true;
        }

        if (!empty($_POST['enable_amount'])) {
            $minAmount = isset($_POST['min_amount']) && $_POST['min_amount'] !== '' ? (int)$_POST['min_amount'] : null;
            $maxAmount = isset($_POST['max_amount']) && $_POST['max_amount'] !== '' ? (int)$_POST['max_amount'] : null;
            if ($minAmount !== null || $maxAmount !== null) {
                $filters['min_amount'] = $minAmount;
                $filters['max_amount'] = $maxAmount;
                $filters['enable_amount'] = true;
            }
        }

        if (!empty($_POST['enable_date'])) {
            $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            if ($startDate !== null || $endDate !== null) {
                $filters['start_date'] = $startDate;
                $filters['end_date'] = $endDate;
                $filters['enable_date'] = true;
            }
        }

        if (!empty($_POST['enable_payment_method']) && !empty($_POST['payment_methods']) && is_array($_POST['payment_methods'])) {
            $filters['payment_methods'] = $_POST['payment_methods'];
            $filters['enable_payment_method'] = true;
        }

        // Giả sử có session hoặc xác định $account_id đúng ở đây
        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            // Xử lý trường hợp chưa đăng nhập hoặc không có account_id
            echo json_encode(['error' => 'User not logged in']);
            return;
        }

        $orders = $this->orderModel->filterOrdersByUser($account_id, $filters);

        // Trả về partial html hoặc json tuỳ bạn muốn
        include 'app/views/order/listorder2.php';
    }
}
