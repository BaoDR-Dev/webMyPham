<?php
// Require SessionHelper and other necessary files
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/models/RatingModel.php');
require_once('app/models/PromotionModel.php');
require_once('app/models/AccountPromotionModel.php');


class ProductController
{
    private $productModel;
    private $categoryModel; // Thêm thuộc tính CategoryModel
    private $ratingModel;
    private $promotionModel;
    private $accountPromotionModel;
    private $db;

    public function __construct()
    {
        // Kết nối cơ sở dữ liệu
        $this->db = (new Database())->getConnection();

        // Khởi tạo các model
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
        $this->ratingModel = new RatingModel($this->db);
        $this->promotionModel = new PromotionModel($this->db);
        $this->accountPromotionModel = new AccountPromotionModel($this->db);
    }

    public function home()
    {
        $accountId = $_SESSION['account_id'] ?? null;

        $promotions = $this->promotionModel->getLatestWithReceiveStatus($accountId, 6);
        // Category có ảnh đại diện từ product
        $featuredCategories = $this->categoryModel->getCategoriesWithImage(6);

        $topSellingProducts = $this->productModel->getTopSellingProducts(10);
        $topProducts        = $this->productModel->getTopRatedProducts(10);
        $newArrivals        = $this->productModel->getNewArrivals(10);
        $this->accountPromotionModel->updateExpiredAccountPromotions();

        include 'app/views/shares/home.php';
    }



    // Hiển thị thông tin chi tiết sản phẩm
    public function view($product_id)
    {
        $ratingFilter = isset($_GET['rating']) ? (int) $_GET['rating'] : null;

        // Lấy thông tin chi tiết sản phẩm từ ID
        $product = $this->productModel->getProductById($product_id);
        $categoryId = $_GET['category_id'] ?? null;

        if (!$product) {
            header("Location: /webbanhang/error");
            exit;
        }

        // Lấy danh sách sản phẩm tương tự
        $similarProducts = $this->productModel->getProductsByCategory($product->category_id);
        $similarProducts = array_filter($similarProducts, function ($p) use ($product_id) {
            return $p->id !== $product_id;
        });
        $similarProducts = array_slice($similarProducts, 0, 5);

        // ✅ Lấy danh sách đánh giá trước khi lọc
        $reviews = $this->ratingModel->getReviewsByProductId($product_id);

        // ✅ Lọc theo số sao nếu có
        if ($ratingFilter && $ratingFilter >= 1 && $ratingFilter <= 5) {
            $reviews = array_filter($reviews, function ($r) use ($ratingFilter) {
                return $r->rating == $ratingFilter;
            });
        }

        // Hiển thị giao diện chi tiết
        include 'app/views/product/show.php';
    }

    public function reviewFilter()
    {
        $productId = $_GET['product_id'] ?? null;
        $rating = $_GET['rating'] ?? null;

        if (!$productId) {
            http_response_code(400);
            echo "Thiếu thông tin sản phẩm.";
            return;
        }

        $ratingModel = new RatingModel();
        $reviews = $ratingModel->getReviewsByProductId($productId, $rating);

        include 'app/views/product/reviews.php';
    }




    // Trang thêm sản phẩm 
    public function add()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        $categories = (new CategoryModel($this->db))->getCategories();
        include_once 'app/views/product/add.php';
    }

    // Lưu sản phẩm mới
    // Thêm sản phẩm mới
    public function save()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $full_description = $_POST['full_description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $status = $_POST['status'] ?? 1;
            $quantity = $_POST['quantity'] ?? 0;

            // Xử lý ảnh
            $image = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->uploadImage($_FILES['image']);
            }

            $result = $this->productModel->addProduct(
                $name,
                $description,
                $full_description,
                $price,
                $category_id,
                $image,
                $status,
                $quantity
            );

            if (is_array($result)) {
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
            } else {
                header('Location: /webbanhang/Admin/adminCategoryList/' . urlencode($category_id));
                exit;
            }
        }
    }

    // Chỉnh sửa sản phẩm
    public function edit($id)
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();

        if (!$product) {
            header('Location: /webbanhang/Product');
            exit();
        }

        include 'app/views/product/edit.php';
    }

    // Lưu sản phẩm sau khi chỉnh sửa
    public function update()
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description']; // Giữ nguyên HTML
            $full_description = $_POST['full_description'] ?? ''; // Giữ nguyên HTML
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            $status = $_POST['status'] ?? 1;
            $quantity = $_POST['quantity'] ?? 0;

            // Xử lý ảnh
            $image = isset($_FILES['image']) && $_FILES['image']['error'] == 0 ?
                $this->uploadImage($_FILES['image']) : $_POST['existing_image'];

            $edit = $this->productModel->updateProduct(
                $id,
                $name,
                $description,
                $full_description,
                $price,
                $category_id,
                $image,
                $status,
                $quantity
            );

            if ($edit) {
                header('Location: /webbanhang/Admin/adminCategoryList/' . urlencode($category_id));
                exit;
            } else {
                echo "Đã xảy ra lỗi khi lưu sản phẩm.";
            }
        }
    }



    // Xóa sản phẩm
    public function delete($id)
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        if ($this->productModel->deleteProduct($id)) {
            // header('Location: /webbanhang/Admin/adminCategoryList/');
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm.";
        }
    }
    public function deleteAjax($id)
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Không có quyền xóa']);
            return;
        }

        if ($this->productModel->deleteProduct($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Xóa thất bại']);
        }
    }


    // Xử lý upload hình ảnh
    private function uploadImage($file)
    {
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /webbanhang/Admin/NotAdmin');
            exit;
        }
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }

        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG và GIF.");
        }

        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }

        return $target_file;
    }

    public function categoryList($category_id = null)
    {
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

            // Truyền thêm biến phân trang qua view
            include 'app/views/product/categorylist.php';
        } else {
            include 'app/views/product/emptycategory.php';
        }
    }


    // THAY THẾ HÀM search() CŨ BẰNG HÀM NÀY
    public function search()
    {
        // Lấy các tham số từ URL
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // --- XỬ LÝ LỌC ĐÃ SỬA ĐỔI (Đảm bảo giá trị null) ---

        // 1. Lọc theo Category ID:
        // Nếu có giá trị category và là số, kiểm tra xem nó có lớn hơn 0 không. 
        // Nếu là '0' hoặc rỗng (''), nó sẽ là null.
        $filter_category_id = null;
        if (isset($_GET['category']) && is_numeric($_GET['category'])) {
            $categoryId = (int)$_GET['category'];
            if ($categoryId > 0) {
                $filter_category_id = $categoryId;
            }
        }
        // Lọc theo Khoảng Giá:
        // Lấy giá trị nếu có, là số và >= 0. Nếu rỗng/không hợp lệ, nó sẽ là null.
        $filter_min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) && (float)$_GET['min_price'] >= 0
            ? (float)$_GET['min_price'] : null;
        $filter_max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) && (float)$_GET['max_price'] >= 0
            ? (float)$_GET['max_price'] : null;

        // --- KẾT THÚC XỬ LÝ LỌC ---

        $limit = 6;
        $offset = ($page - 1) * $limit;

        if (empty($keyword)) {
            header("Location: /webbanhang");
            exit;
        }

        $categories = $this->categoryModel->getAllCategories();

        // Gọi các hàm model
        $totalResults = $this->productModel->countSearchResults($keyword, $filter_category_id, $filter_min_price, $filter_max_price);
        $totalPages = ceil($totalResults / $limit);
        $results = $this->productModel->searchProductsPaginated($keyword, $limit, $offset, $filter_category_id, $filter_min_price, $filter_max_price);

        $queryParams = $_GET;

        include 'app/views/product/search_results.php';
    }

    public function autocomplete()
    {
        header('Content-Type: text/html; charset=utf-8');

        $keyword = $_GET['keyword'] ?? '';
        if (strlen($keyword) < 2) {
            echo ''; // Tránh gợi ý linh tinh
            return;
        }

        // Tìm sản phẩm (status = 1 AND toggle = 1 đã lọc trong model)
        $products = $this->productModel->searchProducts($keyword);

        // Tìm danh mục
        $categories = $this->categoryModel->searchCategories($keyword);

        if (empty($products) && empty($categories)) {
            echo ''; // Không có gì -> ẩn box
            return;
        }

        foreach ($products as $product) {
            echo '<div class="suggestion-item" data-type="product" data-id="' . $product->id . '">' . htmlspecialchars($product->name) . '</div>';
        }

        foreach ($categories as $category) {
            echo '<div class="suggestion-item" data-type="category" data-id="' . $category->id . '"><i class="bi bi-folder"></i> ' . htmlspecialchars($category->name) . '</div>';
        }
    }


    public function showAll()
    {
        $limit = 12;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getAllProducts($limit, $offset);
        $totalProducts = $this->productModel->countAllProducts();
        $totalPages = ceil($totalProducts / $limit);

        $categories = $this->categoryModel->getAllCategories();
        $category_id = null;
        $categoryName = 'Tất cả sản phẩm';

        include 'app/views/product/categorylist.php';
    }
}
