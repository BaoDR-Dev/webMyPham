<?php

require_once __DIR__ . '/../models/AccountPromotionModel.php';
require_once __DIR__ . '/../models/PromotionModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../config/database.php';

class AccountPromotionController
{
    private $accountPromotionModel;
    private $promotionModel;
    private $categoryModel;
    public function __construct()
    {
        $db = (new Database())->getConnection();
        $this->accountPromotionModel = new AccountPromotionModel($db);
        $this->promotionModel = new PromotionModel($db);
        $this->categoryModel = new CategoryModel($db);
    }

    /* ======================================
       1. Kho khuyến mãi của user
       URL: /accountPromotion/myPromotions
       ====================================== */
    public function myPromotions()
    {
        if (!isset($_SESSION['account_id'])) {
            header('Location: /webbanhang/account/login');
            exit;
        }

        $accountId = $_SESSION['account_id'];

        // 1. Phân trang
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 9;
        $offset = ($page - 1) * $limit;

        // 2. Lấy danh sách khuyến mãi (đã bao gồm category_name từ bước 1)
        $promotions = $this->accountPromotionModel
            ->getByAccount($accountId, $limit, $offset);

        // 3. (Theo yêu cầu của bạn) Lấy thêm dữ liệu từ CategoryModel nếu cần
        // Giả sử bạn đã khởi tạo $this->categoryModel trong __construct
        $categories = $this->categoryModel->getAll();

        $total = $this->accountPromotionModel->countByAccount($accountId);
        $totalPages = ceil($total / $limit);

        require 'app/views/promotion/my_promotions.php';
    }



    /* ======================================
       2. Nhận khuyến mãi
       URL: /accountPromotion/receive/{id}
       ====================================== */
    public function receive($promotionId)
    {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /webbanhang/account/login');
            exit;
        }

        $accountId = $_SESSION['user_id'];

        // kiểm tra khuyến mãi còn hiệu lực
        $promotion = $this->promotionModel->findById($promotionId);
        if (!$promotion || strtotime($promotion->end_date) < time()) {
            $_SESSION['error'] = 'Khuyến mãi không hợp lệ hoặc đã hết hạn';
            header('Location: /webbanhang/promotions');
            exit;
        }

        $this->accountPromotionModel->receivePromotion($accountId, $promotionId);

        $_SESSION['success'] = 'Nhận khuyến mãi thành công';
        header('Location: /webbanhang/promotions');
        exit;
    }

    /* ======================================
       3. Áp dụng khuyến mãi (AJAX - Checkout)
       URL: /accountPromotion/apply
       ====================================== */
    public function apply()
    {
        if (!SessionHelper::isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }

        $accountId   = $_SESSION['user_id'];
        $promotionId = $_POST['promotion_id'] ?? null;
        $orderTotal  = $_POST['order_total'] ?? 0;

        if (!$promotionId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu khuyến mãi']);
            return;
        }

        // kiểm tra trong kho user
        $ap = $this->accountPromotionModel->canUse($accountId, $promotionId);
        if (!$ap) {
            echo json_encode(['success' => false, 'message' => 'Khuyến mãi không dùng được']);
            return;
        }

        // lấy thông tin khuyến mãi
        $promotion = $this->promotionModel->findById($promotionId);

        // kiểm tra giá trị đơn hàng tối thiểu
        if ($promotion->min_order_amount > 0 && $orderTotal < $promotion->min_order_amount) {
            echo json_encode([
                'success' => false,
                'message' => 'Đơn hàng chưa đủ điều kiện'
            ]);
            return;
        }

        // tính tiền giảm
        $discount = 0;
        if ($promotion->discount_type == 'percent') {
            $discount = $orderTotal * ($promotion->discount_value / 100);
        } else {
            $discount = $promotion->discount_value;
        }

        $_SESSION['applied_promotion'] = [
            'promotion_id' => $promotionId,
            'discount' => $discount
        ];

        echo json_encode([
            'success' => true,
            'discount' => $discount
        ]);
    }

    /* ======================================
       4. Ghi nhận dùng khuyến mãi (sau order)
       ====================================== */
    public function confirmUse()
    {
        if (!isset($_SESSION['applied_promotion'])) {
            return;
        }

        $accountId   = $_SESSION['user_id'];
        $promotionId = $_SESSION['applied_promotion']['promotion_id'];

        $this->accountPromotionModel->usePromotion($accountId, $promotionId);

        unset($_SESSION['applied_promotion']);
    }

    /* ======================================
       5. Bỏ khuyến mãi đã chọn
       ====================================== */
    public function removeApplied()
    {
        unset($_SESSION['applied_promotion']);
        header('Location: /webbanhang/cart/checkout');
        exit;
    }
}
