<?php
class CategoryModel
{
    private $conn;
    private $table_name = "category";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCategories()
    {
        $query = "SELECT id, name, description FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function getCategoryById($category_id)
    {
        $query = "SELECT name FROM " . $this->table_name . " WHERE id = :category_id";
        $stmt = $this->conn->prepare($query); // Đã sửa lỗi cú pháp ở đây
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ); // Trả về đối tượng chứa thông tin danh mục
    }
    public function getAllCategories()
    {
        $query = "SELECT id, name, description 
              FROM category"; // Thay 'category' bằng tên bảng của bạn nếu khác
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ); // Trả về dưới dạng danh sách đối tượng
    }
    public function getCategoriesWithImage($limit = 6)
    {
        $sql = "
        SELECT 
            c.id,
            c.name,
            c.description,
            (
                SELECT p.image
                FROM product p
                WHERE p.category_id = c.id
                ORDER BY p.id DESC
                LIMIT 1
            ) AS image
        FROM category c
        LIMIT :limit
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function searchCategories($keyword)
    {
        $query = "SELECT id, name FROM " . $this->table_name . " WHERE name LIKE :keyword LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $like = '%' . $keyword . '%';
        $stmt->bindParam(':keyword', $like, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    // Lấy tên danh mục theo ID
    public function getCategoryNameById($category_id)
    {
        $sql = "SELECT name FROM categories WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$category_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['name'] : null;
    }

    // Lấy tất cả danh mục
    public function getAllCategories2()
    {
        $sql = "SELECT * FROM categories";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAll()
    {
        $sql = "SELECT * FROM category";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getNameById($id)
    {
        $sql = "SELECT name FROM category WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['name'] : null;
    }
}
