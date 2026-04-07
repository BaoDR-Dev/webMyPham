<?php

class RatingModel
{
    private $conn;

    public function __construct()
    {
        require_once 'app/config/Database.php';
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy đánh giá theo product_id và order_id
    public function getReviewByProductAndOrder($productId, $orderId)
    {
        $sql = "SELECT * FROM rating WHERE product_id = :product_id AND order_id = :order_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Cập nhật đánh giá
    public function updateReview($ratingId, $rating, $reviewText)
    {
        $sql = "UPDATE rating 
                SET rating = :rating, review_text = :review_text, updated_at = NOW() 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':review_text', $reviewText);
        $stmt->bindParam(':id', $ratingId);
        return $stmt->execute();
    }

    // Tạo đánh giá mới
    public function createReview($productId, $orderId, $rating, $reviewText)
    {
        $sql = "INSERT INTO rating (product_id, order_id, rating, review_text, created_at, updated_at) 
            VALUES (:product_id, :order_id, :rating, :review_text, NOW(), NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':review_text', $reviewText);
        return $stmt->execute();
    }

    public function getReviewsByProductId($productId, $rating = null)
    {
        $query = "
        SELECT r.*, a.full_name AS account_name
        FROM rating r
        JOIN orders o ON r.order_id = o.id
        JOIN account a ON o.account_id = a.id
        WHERE r.product_id = :product_id
    ";

        if ($rating !== null) {
            $query .= " AND r.rating = :rating";
        }

        $query .= " ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);

        if ($rating !== null) {
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getReviewStats($productId)
    {
        $query = "SELECT COUNT(*) AS review_count, AVG(rating) AS average_rating 
              FROM rating 
              WHERE product_id = :product_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
