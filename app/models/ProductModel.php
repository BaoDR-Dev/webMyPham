<?php
class ProductModel
{
    private $conn;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //Lấy danh sách sản phẩm theo danh mục
    public function getProductsByCategory($category_id)
    {
        $query = "SELECT * FROM product WHERE category_id = :category_id AND status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }



    // Lấy tất cả sản phẩm
    public function getProducts()
    {
        $query = "SELECT p.id, p.name, p.description, p.full_description, p.price, p.image, p.status, p.quantity, 
                         p.total_rating, p.review_count, 
                         c.name as category_name,
                         CASE WHEN p.review_count > 0 THEN (p.total_rating / p.review_count) ELSE 0 END AS average_rating
                  FROM product p
                  LEFT JOIN category c ON p.category_id = c.id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    // Lấy thông tin chi tiết của sản phẩm theo ID
    public function getProductById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getNewArrivals($limit = 10)
    {
        $sql = "
        SELECT 
            p.id, p.name, p.price, p.image,
            p.total_rating, p.review_count
        FROM product p
        ORDER BY p.id DESC
        LIMIT :limit
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Rating
    public function getRatingStatsByProductId($product_id)
    {
        $query = "SELECT total_rating, review_count FROM product WHERE id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ); // Trả về tổng điểm và số lượng đánh giá
    }

    // public function updateRatingStats($product_id, $new_rating)
    // {
    //     $query = "UPDATE product
    //           SET total_rating = total_rating + :new_rating, 
    //               review_count = review_count + 1
    //           WHERE id = :product_id";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(':new_rating', $new_rating); // Không cần PDO::PARAM_FLOAT
    //     $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

    //     return $stmt->execute();
    // }

    public function getAverageRating($product_id)
    {
        $query = "SELECT (total_rating / review_count) AS average_rating
              FROM product
              WHERE id = :product_id AND review_count > 0"; // Chỉ tính nếu có đánh giá
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->average_rating ?? 0; // Trả về 0 nếu chưa có đánh giá
    }


    // Thêm sản phẩm mới
    public function addProduct($name, $description, $full_description, $price, $category_id, $image, $status, $quantity)
    {
        $errors = [];
        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }
        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }
        if (!is_numeric($price) || $price < 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }
        if (!is_numeric($quantity) || $quantity < 0) {
            $errors['quantity'] = 'Số lượng sản phẩm không hợp lệ';
        }
        if (!in_array($status, [0, 1])) {
            $errors['status'] = 'Trạng thái không hợp lệ (0 hoặc 1)';
        }
        if (count($errors) > 0) {
            return $errors;
        }

        $query = "INSERT INTO " . $this->table_name . "(name, description, full_description, price, category_id, image, status, quantity) 
        VALUES (:name, :description, :full_description, :price, :category_id, :image, :status, :quantity)";

        $stmt = $this->conn->prepare($query);

        // Giữ lại HTML cho description và full_description
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars($description); // Không dùng strip_tags
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));
        $status = htmlspecialchars(strip_tags($status));
        $quantity = htmlspecialchars(strip_tags($quantity));
        $full_description = htmlspecialchars($full_description);

        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':full_description', $full_description);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

        return $stmt->execute();
    }


    // Cập nhật sản phẩm theo ID
    public function updateProduct($id, $name, $description, $full_description, $price, $category_id, $image, $status, $quantity)
    {
        $query = "UPDATE " . $this->table_name . " SET 
                  name = :name, 
                  description = :description, 
                  full_description = :full_description, 
                  price = :price, 
                  category_id = :category_id, 
                  image = :image, 
                  status = :status, 
                  quantity = :quantity 
              WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Xử lý các giá trị khác ngoài mô tả
        $name = htmlspecialchars(strip_tags($name)); // Mã hóa tên
        $price = htmlspecialchars(strip_tags($price)); // Mã hóa giá
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));
        $status = htmlspecialchars(strip_tags($status));
        $quantity = htmlspecialchars(strip_tags($quantity));

        // Không mã hóa HTML của description và full_description
        $stmt->bindParam(':description', $description); // Dữ liệu HTML
        $stmt->bindParam(':full_description', $full_description); // Dữ liệu HTML
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    // Xóa sản phẩm theo ID
    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function decreaseProductQuantity($product_id, $quantity)
    {
        $query = "UPDATE product 
              SET quantity = quantity - :quantity 
              WHERE id = :product_id AND quantity >= :quantity";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getProductStatistics()
    {
        $query = "SELECT p.id, p.name, SUM(od.quantity) AS total_quantity
              FROM order_details od 
              JOIN product p ON od.product_id = p.id
              GROUP BY p.id, p.name
              ORDER BY total_quantity DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getProductStatisticsByDateRange($start_date, $end_date)
    {
        $query = "SELECT p.name AS product_name, 
                         SUM(od.quantity) AS total_quantity, 
                         SUM(od.quantity * od.price) AS total_revenue
                  FROM order_details od
                  JOIN product p ON od.product_id = p.id
                  JOIN orders o ON od.order_id = o.id
                  WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
                  GROUP BY p.name
                  ORDER BY total_quantity DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getFeaturedProducts()
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, p.status, p.quantity, 
                         p.category_id, c.name as category_name 
                  FROM product p 
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.status = 1
                  ORDER BY p.id DESC
                  LIMIT 8";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    public function updateRatingStats($product_id)
    {
        // Lấy tất cả đánh giá của sản phẩm
        $query = "SELECT COUNT(*) AS review_count, AVG(rating) AS avg_rating 
                  FROM rating 
                  WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Cập nhật tổng điểm và số lượng đánh giá vào bảng product
            $updateQuery = "UPDATE product 
                            SET review_count = :review_count, total_rating = :avg_rating 
                            WHERE id = :product_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':review_count', $result['review_count'], PDO::PARAM_INT);
            $updateStmt->bindParam(':avg_rating', $result['avg_rating'], PDO::PARAM_STR);
            $updateStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            return $updateStmt->execute();
        }

        return false;
    }


    public function searchByKeyword($keyword)
    {
        $query = "SELECT name FROM product WHERE name LIKE :keyword LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function searchProducts($keyword)
    {
        $query = "SELECT id, name, price, image FROM " . $this->table_name . " 
              WHERE name LIKE :keyword AND status = 1 
              LIMIT 20";
        $stmt = $this->conn->prepare($query);
        $like = '%' . $keyword . '%';
        $stmt->bindParam(':keyword', $like, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function searchByKeyword2($keyword, $category_id)
    {
        $keyword = "%" . $keyword . "%";
        $sql = "SELECT * FROM product WHERE (name LIKE ? OR id LIKE ?) AND category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$keyword, $keyword, $category_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    public function searchProductsPaginated($keyword, $limit, $offset, $categoryId = null, $minPrice = null, $maxPrice = null)
    {
        // BẮT ĐẦU VỚI ĐIỀU KIỆN KEYWORD VÀ STATUS
        $sql = "SELECT * FROM {$this->table_name} WHERE name LIKE ? AND status = 1";
        $params = [];
        $types = [];

        // Tham số 1: KEYWORD
        $params[] = '%' . $keyword . '%';
        $types[] = PDO::PARAM_STR;

        // Tham số 2: Lọc theo categoryId
        if ($categoryId !== null) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
            $types[] = PDO::PARAM_INT;
        }

        // Tham số 3: Lọc theo giá tối thiểu
        if ($minPrice !== null) {
            $sql .= " AND price >= ?";
            $params[] = $minPrice;
            $types[] = PDO::PARAM_STR;
        }

        // Tham số 4: Lọc theo giá tối đa
        if ($maxPrice !== null) {
            $sql .= " AND price <= ?";
            $params[] = $maxPrice;
            $types[] = PDO::PARAM_STR;
        }

        // Tham số CUỐI: LIMIT và OFFSET
        $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";

        $params[] = $limit;
        $types[] = PDO::PARAM_INT;

        $params[] = $offset;
        $types[] = PDO::PARAM_INT;

        $stmt = $this->conn->prepare($sql);

        // Bind các tham số
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value, $types[$key]);
        }

        // THỰC THI
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // THAY THẾ HÀM countSearchResults CŨ BẰNG HÀM NÀY
    public function countSearchResults($keyword, $categoryId = null, $minPrice = null, $maxPrice = null)
    {
        // BẮT ĐẦU VỚI ĐIỀU KIỆN KEYWORD VÀ STATUS
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE name LIKE ? AND status = 1";
        $params = [];
        $types = [];

        // Tham số 1: KEYWORD
        $params[] = '%' . $keyword . '%';
        $types[] = PDO::PARAM_STR;

        // Tham số 2: Lọc theo categoryId
        if ($categoryId !== null) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
            $types[] = PDO::PARAM_INT;
        }

        // Tham số 3: Lọc theo giá tối thiểu
        if ($minPrice !== null) {
            $sql .= " AND price >= ?";
            $params[] = $minPrice;
            $types[] = PDO::PARAM_STR;
        }

        // Tham số 4: Lọc theo giá tối đa
        if ($maxPrice !== null) {
            $sql .= " AND price <= ?";
            $params[] = $maxPrice;
            $types[] = PDO::PARAM_STR;
        }

        $stmt = $this->conn->prepare($sql);

        // Bind các tham số
        foreach ($params as $key => $value) {
            // key + 1 vì bindValue bắt đầu từ chỉ số 1
            $stmt->bindValue($key + 1, $value, $types[$key]);
        }

        // THỰC THI
        $stmt->execute();
        return $stmt->fetchColumn();
    }


    public function getProductsByCategory2($category_id, $limit, $offset)
    {
        $sql = "SELECT * FROM product 
            WHERE category_id = :category_id
            LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getTopRatedProducts($limit = 10)
    {
        // Ưu tiên sản phẩm có nhiều đánh giá và điểm cao
        // composite_score = (total_rating * log10(review_count + 1))
        $sql = "SELECT *, 
                   (total_rating * LOG10(review_count + 1)) AS score
            FROM product 
            WHERE status = 1 AND review_count > 0
            ORDER BY score DESC 
            LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopSellingProducts($limit = 10)
    {
        $sql = "SELECT p.*, SUM(od.quantity) AS total_sold
            FROM product p
            INNER JOIN order_details od ON p.id = od.product_id
            GROUP BY p.id
            ORDER BY total_sold DESC
            LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function countProductsByCategory($category_id)
    {
        $sql = "SELECT COUNT(*) FROM product 
            WHERE category_id = :category_id AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }


    public function getAllProducts($limit, $offset)
    {
        $sql = "SELECT * FROM product 
            WHERE status = 1 
            LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAllProducts2()
    {
        $stmt = $this->conn->prepare("SELECT * FROM product");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function countAllProducts()
    {
        $sql = "SELECT COUNT(*) FROM product WHERE status = 1";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchColumn();
    }


    public function filterProductsByCategory($categoryId, $filters = [])
    {
        $sql = "SELECT * FROM product WHERE category_id = :category_id";
        $params = ['category_id' => $categoryId];

        // Lọc theo khoảng giá
        if (!empty($filters['minPrice']) && is_numeric($filters['minPrice'])) {
            $sql .= " AND price >= :minPrice";
            $params['minPrice'] = $filters['minPrice'];
        }
        if (!empty($filters['maxPrice']) && is_numeric($filters['maxPrice'])) {
            $sql .= " AND price <= :maxPrice";
            $params['maxPrice'] = $filters['maxPrice'];
        }

        // Lọc theo số lượng
        if (!empty($filters['minQuantity']) && is_numeric($filters['minQuantity'])) {
            $sql .= " AND quantity >= :minQuantity";
            $params['minQuantity'] = $filters['minQuantity'];
        }
        if (!empty($filters['maxQuantity']) && is_numeric($filters['maxQuantity'])) {
            $sql .= " AND quantity <= :maxQuantity";
            $params['maxQuantity'] = $filters['maxQuantity'];
        }

        // Lọc theo ID sản phẩm
        if (!empty($filters['minId']) && is_numeric($filters['minId'])) {
            $sql .= " AND id >= :minId";
            $params['minId'] = $filters['minId'];
        }
        if (!empty($filters['maxId']) && is_numeric($filters['maxId'])) {
            $sql .= " AND id <= :maxId";
            $params['maxId'] = $filters['maxId'];
        }
        // Lọc theo số sao trung bình
        if (!empty($filters['minRating']) && is_numeric($filters['minRating'])) {
            $sql .= " AND total_rating >= :minRating";
            $params['minRating'] = $filters['minRating'];
        }
        if (!empty($filters['maxRating']) && is_numeric($filters['maxRating'])) {
            $sql .= " AND total_rating <= :maxRating";
            $params['maxRating'] = $filters['maxRating'];
        }

        // Lọc theo số lượng đánh giá
        if (!empty($filters['minReviews']) && is_numeric($filters['minReviews'])) {
            $sql .= " AND review_count >= :minReviews";
            $params['minReviews'] = $filters['minReviews'];
        }
        if (!empty($filters['maxReviews']) && is_numeric($filters['maxReviews'])) {
            $sql .= " AND review_count <= :maxReviews";
            $params['maxReviews'] = $filters['maxReviews'];
        }

        // Lọc theo trạng thái
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function searchProducts2($keyword)
    {
        $sql = "SELECT id, name FROM product WHERE name LIKE ? OR id LIKE ? LIMIT 10";
        $param = ['%' . $keyword . '%', '%' . $keyword . '%'];
        return $this->conn->query($sql, $param);
    }

    public function applyFilters(array $products, array $filters): array
    {
        return array_filter($products, function ($product) use ($filters) {
            if (isset($filters['minPrice']) && $product->price < $filters['minPrice']) {
                return false;
            }
            if (isset($filters['maxPrice']) && $product->price > $filters['maxPrice']) {
                return false;
            }
            if ($filters['minQuantity'] !== null && $product->quantity < $filters['minQuantity']) {
                return false;
            }
            if ($filters['maxQuantity'] !== null && $product->quantity > $filters['maxQuantity']) {
                return false;
            }
            if ($filters['minId'] !== null && $product->id < $filters['minId']) {
                return false;
            }
            if ($filters['maxId'] !== null && $product->id > $filters['maxId']) {
                return false;
            }
            if ($filters['status'] !== null && $product->status != $filters['status']) {
                return false;
            }
            if (isset($filters['minRating']) && $filters['minRating'] !== null && $product->total_rating < $filters['minRating']) {
                return false;
            }
            if (isset($filters['maxRating']) && $filters['maxRating'] !== null && $product->total_rating > $filters['maxRating']) {
                return false;
            }
            if (isset($filters['minReviews']) && $filters['minReviews'] !== null && $product->review_count < $filters['minReviews']) {
                return false;
            }
            if (isset($filters['maxReviews']) && $filters['maxReviews'] !== null && $product->review_count > $filters['maxReviews']) {
                return false;
            }


            return true;
        });
    }
}
