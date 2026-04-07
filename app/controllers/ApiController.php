<?php
/**
 * ApiController - Tất cả REST API endpoints trả về JSON thuần
 * Dùng để demo Chrome DevTools (Network tab, Console tab, Sources tab)
 *
 * ===================== ACCOUNT =====================
 *   POST   /webbanhang/Api/login                    - Đăng nhập
 *   POST   /webbanhang/Api/register                 - Đăng ký
 *   POST   /webbanhang/Api/logout                   - Đăng xuất
 *   GET    /webbanhang/Api/profile                  - Xem profile
 *   POST   /webbanhang/Api/updateProfile            - Cập nhật profile
 *   POST   /webbanhang/Api/changePassword           - Đổi mật khẩu
 *
 * ===================== PRODUCT =====================
 *   GET    /webbanhang/Api/products                 - Danh sách sản phẩm
 *   GET    /webbanhang/Api/product/{id}             - Chi tiết sản phẩm
 *   GET    /webbanhang/Api/search?keyword=...       - Tìm kiếm
 *   GET    /webbanhang/Api/autocomplete?keyword=... - Gợi ý tìm kiếm
 *   GET    /webbanhang/Api/categories               - Danh sách danh mục
 *   GET    /webbanhang/Api/categoryProducts/{id}    - Sản phẩm theo danh mục
 *
 * ===================== CART ========================
 *   GET    /webbanhang/Api/cart                     - Xem giỏ hàng
 *   POST   /webbanhang/Api/cartAdd                  - Thêm vào giỏ
 *   POST   /webbanhang/Api/cartUpdate               - Cập nhật số lượng
 *   POST   /webbanhang/Api/cartRemove/{id}          - Xóa khỏi giỏ
 *   POST   /webbanhang/Api/cartToggle/{id}          - Toggle chọn/bỏ chọn
 *   POST   /webbanhang/Api/cartClear                - Xóa toàn bộ giỏ
 *
 * ===================== ORDER =======================
 *   GET    /webbanhang/Api/orders                   - Danh sách đơn hàng
 *   GET    /webbanhang/Api/order/{id}               - Chi tiết đơn hàng
 *   POST   /webbanhang/Api/orderCancel/{id}         - Hủy đơn hàng
 *
 * ===================== RATING ======================
 *   POST   /webbanhang/Api/ratingStore              - Tạo đánh giá
 *   POST   /webbanhang/Api/ratingUpdate             - Cập nhật đánh giá
 *
 * ===================== PROMOTION ===================
 *   GET    /webbanhang/Api/promotions               - Danh sách khuyến mãi
 *   GET    /webbanhang/Api/myPromotions             - Khuyến mãi của tôi
 *   POST   /webbanhang/Api/promotionReceive/{id}    - Nhận khuyến mãi
 *
 * ===================== ADMIN =======================
 *   GET    /webbanhang/Api/adminUsers               - Danh sách user (admin)
 *   POST   /webbanhang/Api/adminUserStatus          - Bật/tắt user (admin)
 *   GET    /webbanhang/Api/adminOrders              - Tất cả đơn hàng (admin)
 *   POST   /webbanhang/Api/adminOrderStatus         - Cập nhật trạng thái đơn (admin)
 *   GET    /webbanhang/Api/adminDashboard           - Thống kê dashboard (admin)
 */

require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/models/CartModel.php');
require_once('app/models/OrderModel.php');
require_once('app/models/OrderDetailsModel.php');
require_once('app/models/RatingModel.php');
require_once('app/models/PromotionModel.php');
require_once('app/models/AccountPromotionModel.php');

class ApiController
{
    private $db;
    private $accountModel;
    private $productModel;
    private $categoryModel;
    private $cartModel;
    private $orderModel;
    private $orderDetailsModel;
    private $ratingModel;
    private $promotionModel;
    private $accountPromotionModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->db                    = (new Database())->getConnection();
        $this->accountModel          = new AccountModel($this->db);
        $this->productModel          = new ProductModel($this->db);
        $this->categoryModel         = new CategoryModel($this->db);
        $this->cartModel             = new CartModel($this->db);
        $this->orderModel            = new OrderModel($this->db);
        $this->orderDetailsModel     = new OrderDetailsModel($this->db);
        $this->ratingModel           = new RatingModel($this->db);
        $this->promotionModel        = new PromotionModel($this->db);
        $this->accountPromotionModel = new AccountPromotionModel($this->db);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    private function json($data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    private function ok($data = [], string $msg = 'OK'): void
    {
        $this->json(['success' => true, 'message' => $msg, 'data' => $data]);
    }

    private function fail(string $msg, int $code = 400): void
    {
        $this->json(['success' => false, 'message' => $msg, 'data' => null], $code);
    }

    private function requireAuth(): void
    {
        if (empty($_SESSION['account_id'])) {
            $this->fail('Bạn cần đăng nhập.', 401);
        }
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->fail('Không có quyền truy cập.', 403);
        }
    }

    /** Đọc body JSON hoặc fallback về $_POST */
    private function body(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? $_POST;
    }

    // ================================================================
    // ACCOUNT
    // ================================================================

    /** POST /Api/login — Body: { email, password } */
    public function login(): void
    {
        $b        = $this->body();
        $email    = trim($b['email'] ?? '');
        $password = trim($b['password'] ?? '');

        if (!$email || !$password) {
            $this->fail('Vui lòng nhập email và mật khẩu.');
        }

        $account = $this->accountModel->getAccountByEmail($email);

        if (!$account)                    $this->fail('Không tìm thấy tài khoản.', 404);
        if ($account->is_active != 1)     $this->fail('Tài khoản đã bị vô hiệu hóa.', 403);
        if ($account->is_verified != 1)   $this->fail('Tài khoản chưa được kích hoạt.', 403);
        if (!password_verify($password, $account->password)) $this->fail('Mật khẩu không đúng.', 401);

        $_SESSION['account_id'] = $account->id;
        $_SESSION['email']      = $account->email;
        $_SESSION['role']       = $account->role;
        $_SESSION['full_name']  = $account->full_name;
        $_SESSION['image']      = $account->image;

        $this->accountModel->updateLastLogin($email);

        $this->ok([
            'account_id' => $account->id,
            'email'      => $account->email,
            'full_name'  => $account->full_name,
            'role'       => $account->role,
            'image'      => $account->image,
        ], 'Đăng nhập thành công.');
    }

    /** POST /Api/logout */
    public function logout(): void
    {
        session_unset();
        session_destroy();
        $this->ok([], 'Đã đăng xuất.');
    }

    /** GET /Api/profile */
    public function profile(): void
    {
        $this->requireAuth();
        $profile = $this->accountModel->getProfileByEmail($_SESSION['email']);
        if (!$profile) $this->fail('Không tìm thấy tài khoản.', 404);
        $this->ok(['profile' => $profile]);
    }

    /** POST /Api/updateProfile — multipart/form-data hoặc JSON */
    public function updateProfile(): void
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->fail('Dùng POST.', 405);

        $email       = $_SESSION['email'];
        $fullName    = $_POST['full_name']    ?? null;
        $address     = $_POST['address']      ?? null;
        $phoneNumber = $_POST['phone_number'] ?? null;
        $birthDate   = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
        $newImagePath = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $dir = 'public/uploads/account/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fileName)) {
                $newImagePath = $dir . $fileName;
            }
        }

        $result = $this->accountModel->updateProfile($email, $fullName, $address, $phoneNumber, $birthDate, $newImagePath);
        if ($result) {
            $updated = $this->accountModel->getProfileByEmail($email);
            $this->ok(['profile' => $updated], 'Cập nhật thành công.');
        } else {
            $this->fail('Không có thông tin nào thay đổi.');
        }
    }

    /** POST /Api/changePassword — Body: { currentPassword, newPassword, confirmPassword } */
    public function changePassword(): void
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->fail('Dùng POST.', 405);

        $b               = $this->body();
        $currentPassword = trim($b['currentPassword'] ?? '');
        $newPassword     = trim($b['newPassword']     ?? '');
        $confirmPassword = trim($b['confirmPassword'] ?? '');

        $account = $this->accountModel->getAccountByEmail($_SESSION['email']);
        if (!$account || !password_verify($currentPassword, $account->password)) {
            $this->fail('Mật khẩu hiện tại không đúng.', 401);
        }
        if (strlen($newPassword) < 6)          $this->fail('Mật khẩu mới phải ít nhất 6 ký tự.');
        if ($newPassword !== $confirmPassword)  $this->fail('Mật khẩu xác nhận không khớp.');

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->accountModel->updatePassword($_SESSION['email'], $hashed);
        $this->ok([], 'Đổi mật khẩu thành công.');
    }

    // ================================================================
    // PRODUCT
    // ================================================================

    /** GET /Api/products?page=1&limit=12 */
    public function products(): void
    {
        $limit  = max(1, min(50, (int)($_GET['limit'] ?? 12)));
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $items = $this->productModel->getAllProducts($limit, $offset);
        $total = $this->productModel->countAllProducts();

        $this->ok([
            'products'    => $items,
            'total'       => (int)$total,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => (int)ceil($total / $limit),
        ]);
    }

    /** GET /Api/product/{id} */
    public function product(int $id): void
    {
        $product = $this->productModel->getProductById($id);
        if (!$product) $this->fail('Sản phẩm không tồn tại.', 404);

        $reviews = $this->ratingModel->getReviewsByProductId($id);
        $similar = $this->productModel->getProductsByCategory($product->category_id);
        $similar = array_filter($similar, fn($p) => $p->id !== $id);
        $similar = array_slice(array_values($similar), 0, 5);

        $this->ok([
            'product'          => $product,
            'reviews'          => $reviews,
            'similar_products' => $similar,
        ]);
    }

    /** GET /Api/search?keyword=...&category=&min_price=&max_price=&page=1 */
    public function search(): void
    {
        $keyword = trim($_GET['keyword'] ?? '');
        if (!$keyword) $this->fail('Vui lòng nhập từ khóa.');

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 12;
        $offset = ($page - 1) * $limit;

        $catId    = isset($_GET['category']) && (int)$_GET['category'] > 0 ? (int)$_GET['category'] : null;
        $minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null;

        $total   = $this->productModel->countSearchResults($keyword, $catId, $minPrice, $maxPrice);
        $results = $this->productModel->searchProductsPaginated($keyword, $limit, $offset, $catId, $minPrice, $maxPrice);

        $this->ok([
            'keyword'     => $keyword,
            'results'     => $results,
            'total'       => (int)$total,
            'page'        => $page,
            'total_pages' => (int)ceil($total / $limit),
        ]);
    }

    /** GET /Api/autocomplete?keyword=... */
    public function autocomplete(): void
    {
        $keyword = trim($_GET['keyword'] ?? '');
        if (strlen($keyword) < 2) $this->ok(['products' => [], 'categories' => []]);

        $products   = $this->productModel->searchProducts($keyword);
        $categories = $this->categoryModel->searchCategories($keyword);

        $this->ok([
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    /** GET /Api/categories */
    public function categories(): void
    {
        $this->ok(['categories' => $this->categoryModel->getAllCategories()]);
    }

    /** GET /Api/categoryProducts/{id}?page=1 */
    public function categoryProducts(int $id): void
    {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) $this->fail('Danh mục không tồn tại.', 404);

        $limit  = 12;
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getProductsByCategory2($id, $limit, $offset);
        $total    = $this->productModel->countProductsByCategory($id);

        $this->ok([
            'category'    => $category,
            'products'    => $products,
            'total'       => (int)$total,
            'page'        => $page,
            'total_pages' => (int)ceil($total / $limit),
        ]);
    }

    // ================================================================
    // CART
    // ================================================================

    /** GET /Api/cart */
    public function cart(): void
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'];

        $items    = $this->cartModel->getCartItems($accountId);
        $quantity = $this->cartModel->getTotalCartQuantity($accountId);
        $total    = array_reduce($items, fn($s, $i) => $s + ($i['toggle'] ? $i['price'] * $i['quantity'] : 0), 0);

        $this->ok([
            'items'          => $items,
            'total_quantity' => (int)$quantity,
            'total_price'    => $total,
        ]);
    }

    /** POST /Api/cartAdd — Body: { product_id, quantity } */
    public function cartAdd(): void
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->fail('Dùng POST.', 405);

        $b         = $this->body();
        $productId = (int)($b['product_id'] ?? 0);
        $quantity  = (int)($b['quantity']   ?? 1);

        if ($productId <= 0 || $quantity <= 0) $this->fail('product_id và quantity phải là số dương.');

        $product = $this->productModel->getProductById($productId);
        if (!$product)                          $this->fail('Sản phẩm không tồn tại.', 404);
        if ($product->quantity < $quantity)     $this->fail("Kho còn {$product->quantity} sản phẩm.");

        $this->cartModel->addOrUpdateCart($_SESSION['account_id'], $productId, $quantity);
        $newQty = $this->cartModel->getTotalCartQuantity($_SESSION['account_id']);

        $this->ok(['cart_total_quantity' => (int)$newQty], 'Đã thêm vào giỏ hàng.');
    }

    /** POST /Api/cartUpdate — Body: { product_id, quantity } */
    public function cartUpdate(): void
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->fail('Dùng POST.', 405);

        $b         = $this->body();
        $productId = (int)($b['product_id'] ?? 0);
        $quantity  = (int)($b['quantity']   ?? 0);

        if ($productId <= 0 || $quantity <= 0) $this->fail('product_id và quantity phải là số dương.');

        $product = $this->productModel->getProductById($productId);
        if ($product && $product->quantity < $quantity) $this->fail("Tồn kho chỉ còn {$product->quantity}.");

        $accountId = $_SESSION['account_id'];
        $this->cartModel->updateCartQuantity($accountId, $productId, $quantity);

        $items = $this->cartModel->getCartItems($accountId);
        $total = array_reduce($items, fn($s, $i) => $s + ($i['toggle'] ? $i['price'] * $i['quantity'] : 0), 0);

        $this->ok(['product_id' => $productId, 'quantity' => $quantity, 'total_price' => $total], 'Đã cập nhật.');
    }

    /** POST /Api/cartRemove/{product_id} */
    public function cartRemove(int $productId): void
    {
        $this->requireAuth();
        $this->cartModel->removeItem($_SESSION['account_id'], $productId);
        $newQty = $this->cartModel->getTotalCartQuantity($_SESSION['account_id']);
        $this->ok(['cart_total_quantity' => (int)$newQty], 'Đã xóa khỏi giỏ hàng.');
    }

    /** POST /Api/cartToggle/{product_id} */
    public function cartToggle(int $productId): void
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'];
        $item = $this->cartModel->getCartItem($accountId, $productId);
        if (!$item) $this->fail('Sản phẩm không có trong giỏ.', 404);

        $newToggle = $item->toggle ? 0 : 1;
        $this->cartModel->updateToggle($accountId, $productId, $newToggle);

        $items = $this->cartModel->getCartItems($accountId);
        $total = array_reduce($items, fn($s, $i) => $s + ($i['toggle'] ? $i['price'] * $i['quantity'] : 0), 0);

        $this->ok(['new_toggle' => $newToggle, 'total_price' => $total]);
    }

    /** POST /Api/cartClear */
    public function cartClear(): void
    {
        $this->requireAuth();
        $this->cartModel->clearCart($_SESSION['account_id']);
        $this->ok([], 'Đã xóa toàn bộ giỏ hàng.');
    }

    // ================================================================
    // ORDER
    // ================================================================

    /** GET /Api/orders */
    public function orders(): void
    {
        $this->requireAuth();
        $orders = $this->orderModel->getOrdersByAccountId($_SESSION['account_id']);
        $this->ok(['orders' => $orders]);
    }

    /** GET /Api/order/{id} */
    public function order(int $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->getOrderById($id);
        if (!$order) $this->fail('Không tìm thấy đơn hàng.', 404);
        if ($order->account_id != $_SESSION['account_id'] && ($_SESSION['role'] ?? '') !== 'admin') {
            $this->fail('Không có quyền xem đơn hàng này.', 403);
        }

        $details  = $this->orderDetailsModel->getOrderDetailsByOrderId($id);
        $customer = $this->accountModel->getAccountById($order->account_id);

        $this->ok([
            'order'    => $order,
            'details'  => $details,
            'customer' => $customer,
        ]);
    }

    /** POST /Api/orderCancel/{id} */
    public function orderCancel(int $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->getOrderById($id);
        if (!$order)                                    $this->fail('Không tìm thấy đơn hàng.', 404);
        if ($order->account_id != $_SESSION['account_id']) $this->fail('Không có quyền.', 403);
        if ($order->status !== 'pending')               $this->fail('Chỉ đơn hàng chưa xử lý mới có thể hủy.');

        $this->orderModel->updateOrderStatus($id, 'cancelled');
        $this->ok(['order_id' => $id, 'status' => 'cancelled'], 'Đã hủy đơn hàng.');
    }

    // ================================================================
    // RATING
    // ================================================================

    /** POST /Api/ratingStore — Body: { product_id, order_id, rating, review_text } */
    public function ratingStore(): void
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->fail('Dùng POST.', 405);

        $b          = $this->body();
        $productId  = (int)($b['product_id']  ?? 0);
        $orderId    = (int)($b['order_id']    ?? 0);
        $rating     = (int)($b['rating']      ?? 0);
        $reviewText = trim($b['review_text']  ?? '');

        if (!$productId || !$orderId || $rating < 1 || $rating > 5) {
            $this->fail('Thiếu hoặc sai thông tin đánh giá.');
        }

        $this->ratingModel->createReview($productId, $orderId, $rating, $reviewText);
        $this->orderModel->updateReviewStatus($orderId, $productId);
        $this->productModel->updateRatingStats($productId);

        $this->ok([], 'Đánh giá thành công.');
    }

    /** POST /Api/ratingUpdate — Body: { rating_id, rating, review_text, order_id } */
    public function ratingUpdate(): void
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->fail('Dùng POST.', 405);

        $b          = $this->body();
        $ratingId   = (int)($b['rating_id']   ?? 0);
        $rating     = (int)($b['rating']      ?? 0);
        $reviewText = trim($b['review_text']  ?? '');

        if (!$ratingId || $rating < 1 || $rating > 5) $this->fail('Thiếu hoặc sai thông tin.');

        $this->ratingModel->updateReview($ratingId, $rating, $reviewText);
        $this->ok([], 'Cập nhật đánh giá thành công.');
    }

    // ================================================================
    // PROMOTION
    // ================================================================

    /** GET /Api/promotions */
    public function promotions(): void
    {
        $accountId  = $_SESSION['account_id'] ?? null;
        $promotions = $this->promotionModel->getLatestWithReceiveStatus($accountId, 20);
        $this->ok(['promotions' => $promotions]);
    }

    /** GET /Api/myPromotions */
    public function myPromotions(): void
    {
        $this->requireAuth();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 9;
        $offset = ($page - 1) * $limit;

        $promotions = $this->accountPromotionModel->getByAccount($_SESSION['account_id'], $limit, $offset);
        $total      = $this->accountPromotionModel->countByAccount($_SESSION['account_id']);

        $this->ok([
            'promotions'  => $promotions,
            'total'       => (int)$total,
            'page'        => $page,
            'total_pages' => (int)ceil($total / $limit),
        ]);
    }

    /** POST /Api/promotionReceive/{id} */
    public function promotionReceive(int $id): void
    {
        $this->requireAuth();
        $promotion = $this->promotionModel->getById($id);
        if (!$promotion) $this->fail('Khuyến mãi không tồn tại.', 404);

        $this->accountPromotionModel->receivePromotion($_SESSION['account_id'], $id);
        $this->ok([], 'Nhận khuyến mãi thành công.');
    }

    // ================================================================
    // ADMIN
    // ================================================================

    /** GET /Api/adminUsers */
    public function adminUsers(): void
    {
        $this->requireAdmin();
        $users = $this->accountModel->getAllUsersExceptAdmin();
        $this->ok(['users' => $users]);
    }

    /** POST /Api/adminUserStatus — Body: { user_id, status } */
    public function adminUserStatus(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->fail('Dùng POST.', 405);

        $b      = $this->body();
        $userId = (int)($b['user_id'] ?? 0);
        $status = $b['status'] ?? null;

        if (!$userId || $status === null) $this->fail('Thiếu user_id hoặc status.');

        $this->accountModel->updateStatus($userId, $status);
        $this->ok(['user_id' => $userId, 'status' => $status], 'Cập nhật trạng thái thành công.');
    }

    /** GET /Api/adminOrders */
    public function adminOrders(): void
    {
        $this->requireAdmin();
        $orders = $this->orderModel->getAllOrdersWithUser();
        $this->ok(['orders' => $orders]);
    }

    /** POST /Api/adminOrderStatus — Body: { order_id, status } */
    public function adminOrderStatus(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->fail('Dùng POST.', 405);

        $b       = $this->body();
        $orderId = (int)($b['order_id'] ?? 0);
        $status  = trim($b['status']   ?? '');

        if (!$orderId || !$status) $this->fail('Thiếu order_id hoặc status.');

        $this->orderModel->updateOrderStatus($orderId, $status);
        $this->ok(['order_id' => $orderId, 'status' => $status], 'Cập nhật trạng thái đơn hàng thành công.');
    }

    /** GET /Api/adminDashboard?start_date=&end_date= */
    public function adminDashboard(): void
    {
        $this->requireAdmin();

        $startDate = ($_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'))) . ' 00:00:00';
        $endDate   = ($_GET['end_date']   ?? date('Y-m-d')) . ' 23:59:59';

        $orderStats   = $this->orderModel->getOrderStatisticsByDateRange($startDate, $endDate);
        $productStats = $this->productModel->getProductStatisticsByDateRange($startDate, $endDate);
        $customerStats = $this->orderModel->getCustomerStatisticsByDateRange($startDate, $endDate);

        $this->ok([
            'order_stats'    => $orderStats,
            'product_stats'  => $productStats,
            'customer_stats' => $customerStats,
        ]);
    }
}
