<?php
require_once('app/config/database.php');
require_once('app/models/PromotionModel.php');
require_once('app/models/OrderModel.php');
require_once('app/models/CartModel.php');
require_once('app/models/AccountModel.php'); // Thêm AccountModel
require_once('app/models/ProductModel.php');
require_once('app/models/OrderDetailsModel.php'); // Import OrderDetailsModel
require_once('app/models/AccountPromotionModel.php');
class PromotionController
{
    private $db;
    private $promotionModel;
    private $accountPromotionModel;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->promotionModel = new PromotionModel($this->db);
        $this->accountPromotionModel = new AccountPromotionModel($this->db);
    }

    // Danh sách khuyến mãi (admin)
    public function index()
    {
        $promotions = $this->promotionModel->getAll();
        require_once('app/views/promotion/index.php');
    }

    // Form tạo khuyến mãi
    public function create()
    {
        require_once('app/views/promotion/create.php');
    }

    public function receive($promotionId)
    {
        if (empty($_SESSION['account_id'])) {
            header('Location: /webbanhang/account/login');
            exit;
        }

        $this->accountPromotionModel->receivePromotion(
            $_SESSION['account_id'],
            $promotionId
        );

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }


    // Lưu khuyến mãi
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'content' => $_POST['content'],
                'promotion_type_id' => $_POST['promotion_type_id'],
                'discount_value' => $_POST['discount_value'] ?? null,
                'min_order_amount' => $_POST['min_order_amount'] ?? null,
                'category_id' => $_POST['category_id'] ?? null,
                'apply_per_product' => isset($_POST['apply_per_product']) ? 1 : 0,
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $this->promotionModel->create($data);
            header('Location: index.php?controller=promotion&action=index');
        }
    }

    // Form sửa
    public function edit()
    {
        $id = $_GET['id'];
        $promotion = $this->promotionModel->getById($id);
        require_once('app/views/promotion/edit.php');
    }

    // Update
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            $data = [
                'name' => $_POST['name'],
                'content' => $_POST['content'],
                'promotion_type_id' => $_POST['promotion_type_id'],
                'discount_value' => $_POST['discount_value'] ?? null,
                'min_order_amount' => $_POST['min_order_amount'] ?? null,
                'category_id' => $_POST['category_id'] ?? null,
                'apply_per_product' => isset($_POST['apply_per_product']) ? 1 : 0,
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $this->promotionModel->update($id, $data);
            header('Location: index.php?controller=promotion&action=index');
        }
    }

    // Xóa
    public function delete()
    {
        $id = $_GET['id'];
        $this->promotionModel->delete($id);
        header('Location: index.php?controller=promotion&action=index');
    }
}
