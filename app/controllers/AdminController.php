<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/models/ProductModel.php');
require_once('app/models/OrderModel.php'); // Thêm OrderModel
require_once('app/models/CategoryModel.php');
require_once('app/models/PromotionModel.php');
require_once('app/models/PromotionTypeModel.php');
class AdminController
{
    private $accountModel;
    private $orderModel;
    private $productModel;
    private $categoryModel;
    private $promotionModel;
    private $promotionTypeModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        $this->orderModel = new OrderModel($this->db);
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
        $this->promotionModel = new PromotionModel($this->db);
        $this->promotionTypeModel = new PromotionTypeModel($this->db);
    }

    public function promotionList()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        // 👉 LẤY LOẠI KHUYẾN MÃI
        $types = $this->promotionTypeModel->getAll();

        // 👉 DANH SÁCH KHUYẾN MÃI
        $promotions = $this->promotionModel->getAll();

        require_once 'app/views/admin/promotion/index.php';
    }


    public function addPromotion()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $types = $this->promotionTypeModel->getAll();
        $categories = $this->categoryModel->getAll(); // 🔥 THÊM DÒNG NÀY

        require_once('app/views/admin/promotion/create.php');
    }

    public function storePromotion()
    {
        // Check quyền admin
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /webbanhang/Admin/promotionList');
            exit;
        }

        // ===== LẤY DATA =====
        $promotionTypeId = (int)$_POST['promotion_type_id'];

        // Xử lý discount theo loại KM
        $discountValue = null;

        // 1 = %, 2 = tiền, 3 = freeship
        if ($promotionTypeId === 1 || $promotionTypeId === 2) {
            $discountValue = !empty($_POST['discount_value'])
                ? (float)$_POST['discount_value']
                : 0;
        }

        $data = [
            'name' => trim($_POST['name']),
            'content' => trim($_POST['content']),
            'promotion_type_id' => $promotionTypeId,
            'discount_value' => $discountValue,
            'min_order_amount' => !empty($_POST['min_order_amount'])
                ? (float)$_POST['min_order_amount']
                : null,
            'category_id' => !empty($_POST['category_id'])
                ? (int)$_POST['category_id']
                : null,
            'apply_per_product' => isset($_POST['apply_per_product']) ? 1 : 0,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // ===== VALIDATE CƠ BẢN =====
        if (empty($data['name']) || empty($data['start_date']) || empty($data['end_date'])) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc';
            header('Location: /webbanhang/Admin/addPromotion');
            exit;
        }

        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            $_SESSION['error'] = 'Ngày bắt đầu không được lớn hơn ngày kết thúc';
            header('Location: /webbanhang/Admin/addPromotion');
            exit;
        }

        // ===== LƯU =====
        $this->promotionModel->create($data);

        $_SESSION['success'] = 'Thêm khuyến mãi thành công';
        header('Location: /webbanhang/Admin/promotionList');
        exit;
    }
    public function deletePromotion()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $id = $_GET['id'];
        $this->promotionModel->delete($id);

        header('Location: /webbanhang/Admin/promotionList');
        exit;
    }
    public function editPromotion()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        $id = $_GET['id'];
        $promotion = $this->promotionModel->getById($id);
        $types = $this->promotionTypeModel->getAll();

        require_once('app/views/admin/promotion/edit.php');
    }
    public function updatePromotion()
    {
        // Check quyền admin
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /webbanhang/Admin/promotionList');
            exit;
        }

        $id = (int)$_POST['id'];
        $promotionTypeId = (int)$_POST['promotion_type_id'];

        // Xử lý discount theo loại khuyến mãi
        $discountValue = null;
        if ($promotionTypeId === 1 || $promotionTypeId === 2) {
            $discountValue = !empty($_POST['discount_value'])
                ? (float)$_POST['discount_value']
                : 0;
        }

        $data = [
            'name' => trim($_POST['name']),
            'content' => trim($_POST['content']),
            'promotion_type_id' => $promotionTypeId,
            'discount_value' => $discountValue,
            'min_order_amount' => !empty($_POST['min_order_amount'])
                ? (float)$_POST['min_order_amount']
                : null,
            'category_id' => !empty($_POST['category_id'])
                ? (int)$_POST['category_id']
                : null,
            'apply_per_product' => isset($_POST['apply_per_product']) ? 1 : 0,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Validate ngày
        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            $_SESSION['error'] = 'Ngày bắt đầu không được lớn hơn ngày kết thúc';
            header('Location: /webbanhang/Admin/editPromotion/' . $id);
            exit;
        }

        // ===== UPDATE =====
        $this->promotionModel->update($id, $data);

        $_SESSION['success'] = 'Cập nhật khuyến mãi thành công';
        header('Location: /webbanhang/Admin/promotionList');
        exit;
    }

    public function ajaxFilterPromotions()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            exit;
        }

        $conditions = [];
        $params = [];

        if (!empty($_GET['keyword'])) {
            $conditions[] = '(p.name LIKE :kw OR p.content LIKE :kw)';
            $params['kw'] = '%' . $_GET['keyword'] . '%';
        }

        if (!empty($_GET['promotion_type_id'])) {
            $conditions[] = 'p.promotion_type_id = :type';
            $params['type'] = (int)$_GET['promotion_type_id'];
        }

        if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
            $conditions[] = 'p.is_active = :active';
            $params['active'] = (int)$_GET['is_active'];
        }

        if (!empty($_GET['start_date'])) {
            $conditions[] = 'p.start_date >= :start_date';
            $params['start_date'] = $_GET['start_date'];
        }

        if (!empty($_GET['end_date'])) {
            $conditions[] = 'p.end_date <= :end_date';
            $params['end_date'] = $_GET['end_date'];
        }

        $promotions = $this->promotionModel->filter($conditions, $params);

        require 'app/views/Admin/components/table_promotions.php';
    }


    public function updateUserStatus()
    {

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            $status = $_POST['status'] ?? null;

            if (!$user_id || $status === null) {
                echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
                return;
            }


            $result = $this->accountModel->updateStatus($user_id, $status);

            if ($result) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => "Không thể cập nhật"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Phương thức không hợp lệ"]);
        }
    }


    protected function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function manageUsers()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        // Lấy danh sách người dùng từ AccountModel
        $users = $this->accountModel->getAllUsersExceptAdmin();

        // Bao gồm view hiển thị danh sách người dùng
        include_once 'app/views/Admin/users_list.php';
    }
    public function userDetails()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $user_id = isset($_GET['user_id']) && is_numeric($_GET['user_id'])
            ? (int) $_GET['user_id']
            : null;

        if ($user_id === null) {
            echo "ID khách hàng không hợp lệ.";
            exit;
        }

        $customer = $this->accountModel->getAccountById($user_id);
        if (!$customer) {
            echo "Không tìm thấy thông tin khách hàng.";
            exit;
        }

        // ✅ GÁN ĐÚNG BIẾN CHO VIEW
        $customerInfo = $customer;

        $orders = $this->orderModel->getOrdersByAccountId($user_id);

        include 'app/views/admin/customer_details.php';
    }



    public function updateOrderStatus()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = $_POST['order_id'] ?? null;
            $status = $_POST['status'] ?? null;
            $user_id = $_POST['user_id'] ?? null;

            if (!$order_id || !$status || !$user_id) {
                echo "Thiếu thông tin cần thiết.";
                return;
            }

            // Gọi model để cập nhật trạng thái đơn hàng
            $orderModel = new OrderModel($this->db);
            $orderModel->updateOrderStatus($order_id, $status);

            // Redirect lại trang chi tiết khách hàng
            header("Location: /webbanhang/Admin/userDetails?user_id=" . $user_id);
            exit;
        } else {
            echo "Phương thức không hợp lệ.";
        }
    }

    public function updateOrderStatus2()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = $_POST['order_id'] ?? null;
            $status = $_POST['status'] ?? null;


            if (!$order_id || !$status) {
                echo "Thiếu thông tin cần thiết.";
                var_dump($_POST);

                return;
            }

            // Gọi model để cập nhật trạng thái đơn hàng

            $this->orderModel->updateOrderStatus($order_id, $status);

            // Redirect lại trang chi tiết khách hàng
            header("Location: /webbanhang/Admin/orderList");
            exit;
        } else {
            echo "Phương thức không hợp lệ.";
        }
    }
    public function updateOrderStatus3()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = $_POST['order_id'] ?? null;
            $status = $_POST['status'] ?? null;

            if (!$order_id || !$status) {
                echo "Thiếu thông tin cần thiết.";
                var_dump($_POST);
                return;
            }

            // Gọi model để cập nhật trạng thái đơn hàng
            $this->orderModel->updateOrderStatus($order_id, $status);

            // ✅ Redirect lại trang chi tiết khách hàng và truyền order_id
            header("Location: /webbanhang/Admin/customerInfo/$order_id");
            exit;
        } else {
            echo "Phương thức không hợp lệ.";
        }
    }

    public function dashboard()
    {
        $this->ensureSessionStarted();

        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        // ====== LỌC NGÀY ======
        $start_date_filter = ($_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days')));
        $end_date_filter   = ($_GET['end_date'] ?? date('Y-m-d'));

        // Để bao gồm TOÀN BỘ ngày bắt đầu và ngày kết thúc trong truy vấn SQL
        $start_datetime_sql = $start_date_filter . ' 00:00:00';
        $end_datetime_sql   = $end_date_filter . ' 23:59:59';


        // ====== THỐNG KÊ ĐƠN HÀNG THEO NGÀY ======
        $orderStatsRaw = $this->orderModel->getOrderStatisticsByDateRange($start_datetime_sql, $end_datetime_sql);

        $orderStats = [];
        foreach ($orderStatsRaw as $row) {
            $orderStats[$row->order_date] = [
                'orders'  => (int)$row->total_orders,
                'revenue' => (float)$row->total_revenue
            ];
        }

        $labels = [];
        $dataOrders = [];
        $dataRevenue = [];

        $current = strtotime($start_date_filter); // Dùng $start_date_filter để lặp qua ngày
        $end     = strtotime($end_date_filter);   // Dùng $end_date_filter để lặp qua ngày

        while ($current <= $end) {
            $date = date('Y-m-d', $current);
            $labels[]       = $date;
            $dataOrders[]   = $orderStats[$date]['orders']  ?? 0;
            $dataRevenue[]  = $orderStats[$date]['revenue'] ?? 0;
            $current = strtotime('+1 day', $current);
        }

        // ====== THỐNG KÊ KHÁCH HÀNG ======
        // (Giả định getCustomerStatisticsByDateRange trả về mảng các đối tượng/mảng có account_id, total_orders, total_revenue)
        $customerStatsRaw = $this->orderModel->getCustomerStatisticsByDateRange($start_datetime_sql, $end_datetime_sql);
        $customerStats = [];

        foreach ($customerStatsRaw as $cs) {

            // 🔴 SỬA -> thành ['key']
            $account = $this->accountModel->getAccountById($cs['account_id']);

            if ($account) {
                $customerStats[] = [
                    'full_name'     => $account->full_name . ' #' . $account->id,
                    'total_orders'  => (int)$cs['total_orders'],
                    'total_revenue' => (float)$cs['total_revenue'],
                ];
            } else {
                $customerStats[] = [
                    'full_name'     => 'Khách #' . $cs['account_id'],
                    'total_orders'  => (int)$cs['total_orders'],
                    'total_revenue' => (float)$cs['total_revenue'],
                ];
            }
        }



        // ====== THỐNG KÊ SẢN PHẨM ======
        // (Giả định getProductStatisticsByDateRange trả về mảng các đối tượng/mảng có product_name, total_quantity, total_revenue)
        $productStatsRaw = $this->productModel->getProductStatisticsByDateRange($start_datetime_sql, $end_datetime_sql);
        $productStats = [];
        foreach ($productStatsRaw as $ps) {
            $productStats[] = [
                'product_name' => $ps->product_name, // Đảm bảo key là 'product_name'
                'total_quantity' => (int)$ps->total_quantity,
                'total_revenue' => (float)$ps->total_revenue,
            ];
        }


        // ====== SẢN PHẨM -> KHÁCH MUA (Product Buyers) ======
        $productBuyers = [];
        $product_keyword = $_GET['product_keyword'] ?? '';
        if (!empty($product_keyword)) {
            // Hàm getBuyersByProduct của bạn đã trả về các đối tượng với full_name, total_quantity
            $buyersRaw = $this->orderModel->getBuyersByProduct(
                $product_keyword,
                $start_datetime_sql,
                $end_datetime_sql
            );
            foreach ($buyersRaw as $buyer) {
                $productBuyers[] = [
                    'customer_id' => $buyer->account_id,
                    'customer_name' => $buyer->full_name,    // Đảm bảo key này khớp trong PHP và HTML
                    'total_quantity' => (int)$buyer->total_quantity
                ];
            }
        }

        // ====== KHÁCH HÀNG -> SẢN PHẨM ĐÃ MUA (Customer Products) ======
        $customerProducts = [];
        $customer_id = $_GET['customer_id'] ?? '';
        if (!empty($customer_id)) {
            // Hàm getProductsByCustomer của bạn đã trả về các đối tượng với name, total_quantity
            $productsRaw = $this->orderModel->getProductsByCustomer(
                $customer_id,
                $start_datetime_sql,
                $end_datetime_sql
            );
            foreach ($productsRaw as $product) {
                $customerProducts[] = [
                    'product_name' => $product->name, // Đảm bảo key này khớp trong PHP và HTML
                    'total_quantity' => (int)$product->total_quantity
                ];
            }
        }

        include 'app/views/admin/dashboard.php';
    }

    public function adminCategoryList($category_id = null)
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        // Kiểm tra quyền admin
        if (!SessionHelper::isAdmin()) {
            header('Location: /webbanhang/error');
            exit;
        }

        if (is_null($category_id) || !is_numeric($category_id)) {
            header('Location: /webbanhang/error');
            exit;
        }

        $category_id = (int) $category_id;

        // Phân trang
        $limit = 12;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        // Lấy sản phẩm theo danh mục có phân trang
        $products = $this->productModel->getProductsByCategory2($category_id, $limit, $offset);

        // Đếm tổng số sản phẩm để tính tổng số trang
        $totalProducts = $this->productModel->countProductsByCategory($category_id);
        $totalPages = ceil($totalProducts / $limit);

        // Lấy thông tin danh mục
        $category = $this->categoryModel->getCategoryById($category_id);

        if ($category) {
            $categoryName = htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8');

            // Truyền dữ liệu qua view
            include 'app/views/admin/products_list.php';
        } else {
            include 'app/views/admin/product/emptycategory.php';
        }
    }

    public function showAll()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;


        $search = $_GET['search'] ?? '';
        $minPrice = $_GET['minPrice'] ?? null;
        $maxPrice = $_GET['maxPrice'] ?? null;

        $products = $this->productModel->getAllProducts2($search, $minPrice, $maxPrice);
        $totalProducts = $this->productModel->countAllProducts($search, $minPrice, $maxPrice);


        include 'app/views/admin/products_list.php';
    }
    public function searchSuggestions()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        if (!isset($_GET['keyword'])) {
            echo json_encode([]);
            return;
        }

        $keyword = trim($_GET['keyword']);
        $results = $this->productModel->searchProducts($keyword);

        header('Content-Type: application/json');
        echo json_encode($results);
    }

    public function searchProductSuggestions()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $keyword = $_GET['keyword'] ?? '';
        $results = [];

        if (!empty($keyword)) {
            $products = $this->productModel->searchProducts2($keyword);

            foreach ($products as $product) {
                $results[] = [
                    'id' => $product['id'],
                    'name' => $product['name']
                ];
            }
        }

        // Trả kết quả JSON
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }

    public function adminCategoryTable($category_id)
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $search = $_GET['search'] ?? null;

        if ($search) {
            // Tìm theo tên hoặc mã sản phẩm (id hoặc name)
            $products = $this->productModel->searchByKeyword2($search, $category_id);
        } else {
            // Nếu không có từ khóa thì lấy tất cả theo danh mục
            $products = $this->productModel->getProductsByCategory($category_id);
        }

        $categories = $this->categoryModel->getAll(); // để hiển thị danh mục bên trái

        require_once __DIR__ . '/../views/admin/products_list.php';
    }
    public function adminCategoryTable2($category_id)
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $search = $_GET['search'] ?? null;

        // Tập hợp các bộ lọc
        $filters = [
            'minPrice'     => $_GET['minPrice'] ?? null,
            'maxPrice'     => $_GET['maxPrice'] ?? null,
            'minQuantity'  => $_GET['minQuantity'] ?? null,
            'maxQuantity'  => $_GET['maxQuantity'] ?? null,
            'minId'        => $_GET['minId'] ?? null,
            'maxId'        => $_GET['maxId'] ?? null,
            'status'       => $_GET['status'] ?? null,
            'minRating'    => $_GET['minRating'] ?? null,
            'maxRating'    => $_GET['maxRating'] ?? null,
            'minReviews'   => $_GET['minReviews'] ?? null,
            'maxReviews'   => $_GET['maxReviews'] ?? null
        ];



        // Nếu có từ khóa tìm kiếm, ưu tiên lọc theo từ khóa trong danh mục
        if ($search) {
            $products = $this->productModel->searchByKeyword2($search, $category_id);
            // Sau khi tìm kiếm xong, tiếp tục áp dụng lọc nếu có
            $products = $this->productModel->applyFilters($products, $filters);
        } else {
            // Không có từ khóa thì lọc bình thường theo danh mục và bộ lọc
            $products = $this->productModel->filterProductsByCategory($category_id, $filters);
        }

        $categories = $this->categoryModel->getAll();
        $categoryName = $this->categoryModel->getNameById($category_id);

        require_once __DIR__ . '/../views/admin/products_list.php';
    }

    public function ajaxFilterProducts()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $category_id = $_GET['category_id'] ?? null;
        $search = $_GET['search'] ?? null;

        // ✅ Bổ sung đầy đủ các trường lọc
        $filters = [
            'minPrice'     => $_GET['minPrice'] ?? null,
            'maxPrice'     => $_GET['maxPrice'] ?? null,
            'minQuantity'  => $_GET['minQuantity'] ?? null,
            'maxQuantity'  => $_GET['maxQuantity'] ?? null,
            'minId'        => $_GET['minId'] ?? null,
            'maxId'        => $_GET['maxId'] ?? null,
            'status'       => $_GET['status'] ?? null,
            'minRating'    => $_GET['minRating'] ?? null,
            'maxRating'    => $_GET['maxRating'] ?? null,
            'minReviews'   => $_GET['minReviews'] ?? null,
            'maxReviews'   => $_GET['maxReviews'] ?? null
        ];

        if ($search) {
            $products = $this->productModel->searchByKeyword2($search, $category_id);
            $products = $this->productModel->applyFilters($products, $filters);
        } else {
            $products = $this->productModel->filterProductsByCategory($category_id, $filters);
        }

        require_once __DIR__ . '/../views/admin/components/products_table_partial.php';
    }


    public function orderList()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();

        // Kiểm tra quyền admin
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $orders = $this->orderModel->getAllOrdersWithUser();
        include 'app/views/admin/order_list.php';
    }

    public function filter()
    {

        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        // Lấy dữ liệu lọc từ GET
        // Đổi key 'statuses' thành 'status' cho khớp với form HTML
        $filters = [
            'customer_name'    => trim($_GET['customer_name'] ?? ''),
            'min_customer_id'  => isset($_GET['min_customer_id']) ? (int)$_GET['min_customer_id'] : null,
            'max_customer_id'  => isset($_GET['max_customer_id']) ? (int)$_GET['max_customer_id'] : null,
            'status'           => is_array($_GET['status'] ?? null) ? $_GET['status'] : [],
            'min_amount'       => isset($_GET['min_amount']) ? (float)$_GET['min_amount'] : null,
            'max_amount'       => isset($_GET['max_amount']) ? (float)$_GET['max_amount'] : null,
            'min_order_id'     => isset($_GET['min_order_id']) ? (int)$_GET['min_order_id'] : null,
            'max_order_id'     => isset($_GET['max_order_id']) ? (int)$_GET['max_order_id'] : null,
            'start_date'       => $_GET['start_date'] ?? null,
            'end_date'         => $_GET['end_date'] ?? null,
            'payment_methods'  => is_array($_GET['payment_methods'] ?? null) ? $_GET['payment_methods'] : [],
            'product_name'     => trim($_GET['product_name'] ?? ''),
            'product_id'       => isset($_GET['product_id']) ? (int)$_GET['product_id'] : null, // ✅ thêm dòng này
        ];

        $orders = $this->orderModel->filterOrders($filters);

        // Truyền dữ liệu và include view
        $filters = $_GET; // để dễ hiển thị lại trong form nếu cần
        require_once __DIR__ . '/../views/admin/components/order_table.php';
    }

    public function userDetails2()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SESSION['role'] !== 'admin') {
            http_response_code(403);
            exit('Forbidden');
        }

        $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
        if (!$user_id) {
            http_response_code(400);
            exit('ID khách hàng không hợp lệ');
        }

        // 🔒 FILTER
        $filters = [
            'user_id'         => $user_id,
            'status'          => $_GET['status'] ?? [],
            'payment_methods' => $_GET['payment_methods'] ?? [],
            'min_amount'      => $_GET['min_amount'] ?? null,
            'max_amount'      => $_GET['max_amount'] ?? null,
            'min_order_id'    => $_GET['min_order_id'] ?? null,
            'max_order_id'    => $_GET['max_order_id'] ?? null,
        ];

        $orders = $this->orderModel->filterOrdersByUser3($filters);

        // ⚠️ CHỈ RETURN TABLE
        include 'app/views/admin/components/user_table_order.php';
    }



    public function customerInfo($orderId)
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        // Lấy thông tin đơn hàng để truy ra account_id
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) {
            echo "Không tìm thấy đơn hàng.";
            exit;
        }

        // Lấy thông tin khách hàng từ account_id
        $customerInfo = $this->orderModel->getCustomerByOrderId($orderId);

        // Lấy toàn bộ đơn hàng của khách hàng đó
        $orders = $this->orderModel->getOrdersByAccountId2($order->account_id);

        include 'app/views/Admin/customer_info.php';
    }

    public function orderDetail($orderId)
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $order = $this->orderModel->getOrderById($orderId);
        $orderDetails = $this->orderModel->getOrderDetails($orderId);
        $customerInfo = $this->orderModel->getCustomerByOrderId($orderId);

        require_once 'app/views/admin/orderdetail.php';
    }
    public function orderDetail2($orderId)
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $order = $this->orderModel->getOrderById($orderId);
        $orderDetails = $this->orderModel->getOrderDetails($orderId);
        $customerInfo = $this->orderModel->getCustomerByOrderId($orderId);

        require_once 'app/views/admin/orderdetail2.php';
    }
    public function filterAccount()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        $filters = [
            'searchName'    => $_GET['searchName'] ?? '',
            'searchEmail'   => $_GET['searchEmail'] ?? '',
            'searchAddress' => $_GET['searchAddress'] ?? '',
            'searchPhone'   => $_GET['searchPhone'] ?? '',
            'birthDateFrom' => $_GET['birthDateFrom'] ?? '',
            'birthDateTo'   => $_GET['birthDateTo'] ?? '',
            'minId'         => $_GET['minId'] ?? '',
            'maxId'         => $_GET['maxId'] ?? '',
            'lastLoginDays' => $_GET['lastLoginDays'] ?? '',
            'status'        => $_GET['status'] ?? ''
        ];

        $users = $this->accountModel->filterUsers($filters);
        require_once __DIR__ . '/../views/admin/components/users_table.php';
    }

    public function ajaxFilterUsers()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }

        $filters = [
            'keyword'        => $_GET['keyword'] ?? null, // ✅ THÊM
            'searchName'    => $_GET['searchName'] ?? null,
            'searchEmail'   => $_GET['searchEmail'] ?? null,
            'searchAddress' => $_GET['searchAddress'] ?? null,
            'searchPhone'   => $_GET['searchPhone'] ?? null,
            'birthDateFrom' => $_GET['birthDateFrom'] ?? null,
            'birthDateTo'   => $_GET['birthDateTo'] ?? null,
            'minId'         => $_GET['minId'] ?? null,
            'maxId'         => $_GET['maxId'] ?? null,
            'lastLoginDays' => $_GET['lastLoginDays'] ?? null,
            'status'        => isset($_GET['status']) ? $_GET['status'] : null,
        ];

        $users = $this->accountModel->filterUsers($filters);
        require_once __DIR__ . '/../views/admin/components/users_table.php';
    }


    public function ajaxFilterOrder()
    {
        // Start session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check quyền admin
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            exit('Access denied');
        }

        // Lấy dữ liệu filter từ GET
        $filters = [
            // Search nhanh
            'keyword'          => trim($_GET['keyword'] ?? ''),

            // Customer
            'customer_name'    => trim($_GET['customer_name'] ?? ''),
            'min_customer_id'  => isset($_GET['min_customer_id']) && $_GET['min_customer_id'] !== ''
                ? (int)$_GET['min_customer_id']
                : null,
            'max_customer_id'  => isset($_GET['max_customer_id']) && $_GET['max_customer_id'] !== ''
                ? (int)$_GET['max_customer_id']
                : null,

            // Order
            'min_order_id'     => isset($_GET['min_order_id']) && $_GET['min_order_id'] !== ''
                ? (int)$_GET['min_order_id']
                : null,
            'max_order_id'     => isset($_GET['max_order_id']) && $_GET['max_order_id'] !== ''
                ? (int)$_GET['max_order_id']
                : null,

            // Amount
            'min_amount'       => isset($_GET['min_amount']) && $_GET['min_amount'] !== ''
                ? (float)$_GET['min_amount']
                : null,
            'max_amount'       => isset($_GET['max_amount']) && $_GET['max_amount'] !== ''
                ? (float)$_GET['max_amount']
                : null,

            // Status & payment
            'status'           => isset($_GET['status']) && is_array($_GET['status'])
                ? $_GET['status']
                : [],
            'payment_methods'  => isset($_GET['payment_methods']) && is_array($_GET['payment_methods'])
                ? $_GET['payment_methods']
                : [],

            // Date
            'start_date'       => $_GET['start_date'] ?? null,
            'end_date'         => $_GET['end_date'] ?? null,

            // Product
            'product_name'     => trim($_GET['product_name'] ?? ''),
        ];

        // Lấy dữ liệu từ model
        $orders = $this->orderModel->filterOrders($filters);

        // Render bảng (AJAX)
        require_once __DIR__ . '/../views/admin/components/order_table.php';
    }


    public function NotAdmin()
    {
        include 'app/views/Admin/NoAdmin.php'; // Hiển thị trang thông báo
        exit;
    }
}
