<?php
// Require SessionHelper and other necessary files
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/RatingModel.php');
require_once('app/models/AccountModel.php');
require_once('app/models/OrderModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/models/OrderDetailsModel.php'); // Import OrderDetailsModel

class RatingController
{
    private $db;
    private $productModel;
    private $categoryModel;
    private $orderModel;
    private $accountModel;
    private $orderDetailsModel;
    private $ratingModel;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();

        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
        $this->accountModel = new AccountModel($this->db);
        $this->orderModel = new OrderModel($this->db);
        $this->orderDetailsModel = new OrderDetailsModel($this->db);
        $this->ratingModel = new RatingModel($this->db);
    }

    public function edit($productId, $orderId)
    {
        $review = $this->ratingModel->getReviewByProductAndOrder($productId, $orderId);

        if (!$review) {
            die('Không tìm thấy đánh giá để chỉnh sửa.');
        }

        $product = $this->productModel->getProductById($productId);

        require 'app/views/rating/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /webbanhang');
            exit;
        }

        $ratingId   = $_POST['rating_id'] ?? null;
        $rating     = $_POST['rating'] ?? null;
        $reviewText = trim($_POST['review_text'] ?? '');
        $orderId    = $_POST['order_id'] ?? null;

        if (!$ratingId) {
            die('Thiếu thông tin cần thiết.');
        }

        if ($rating < 1 || $rating > 5) {
            die('Số sao không hợp lệ.');
        }

        $success = $this->ratingModel->updateReview(
            (int)$ratingId,
            (int)$rating,
            $reviewText
        );

        if ($success) {
            header("Location: /webbanhang/Order/viewOrderDetails/$orderId");
            exit;
        }

        die('Cập nhật đánh giá thất bại.');
    }



    public function create($productId, $orderId)
    {
        $product = $this->productModel->getProductById($productId);

        // Truyền đúng biến ra view
        $order_id = $orderId;

        require 'app/views/rating/create.php';
    }


    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? null;
            $orderId = $_POST['order_id'] ?? null;
            $rating = $_POST['rating'] ?? null;
            $reviewText = $_POST['review_text'] ?? '';

            // Kiểm tra dữ liệu đầu vào
            if (!$productId || !$orderId || !$rating) {
                echo "<pre>";
                echo "product_id: ";
                var_dump($productId);
                echo "order_id: ";
                var_dump($orderId);
                echo "rating: ";
                var_dump($rating);
                echo "</pre>";
                die('Thiếu thông tin để tạo đánh giá.');
            }

            // Tạo đánh giá
            $this->ratingModel->createReview($productId, $orderId, $rating, $reviewText);

            // Cập nhật trạng thái is_reviewed
            $this->orderModel->updateReviewStatus($orderId, $productId);
            $this->productModel->updateRatingStats($productId);
            // Chuyển hướng sau khi hoàn tất
            header("Location: /webbanhang/Order/viewOrderDetails/$orderId");
            exit;
        }
    }

    public function reviewFilter()
    {
        $productId = $_GET['product_id'] ?? null;
        $rating = $_GET['rating'] ?? null;

        if (!$productId) {
            http_response_code(400);
            echo 'Thiếu product_id';
            return;
        }

        $ratingModel = new RatingModel();
        $reviews = $ratingModel->getReviewsByProductId($productId, $rating);

        include 'app/views/product/reviews.php';
    }
}
