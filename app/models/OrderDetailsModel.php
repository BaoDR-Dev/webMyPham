<?php
class OrderDetailsModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Tạo chi tiết đơn hàng mới
    public function createOrderDetail($order_id, $product_id, $quantity, $price)
    {
        $query = "INSERT INTO order_details (order_id, product_id, quantity, price)
                  VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Lấy danh sách chi tiết đơn hàng theo ID đơn hàng
    public function getOrderDetailsByOrderId($order_id)
    {
        $query = "SELECT od.*, p.name AS product_name, p.image AS product_image
                  FROM order_details od
                  JOIN product p ON od.product_id = p.id
                  WHERE od.order_id = :order_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về danh sách chi tiết đơn hàng
    }

    // Xóa tất cả chi tiết của một đơn hàng
    public function deleteOrderDetailsByOrderId($order_id)
    {
        $query = "DELETE FROM order_details WHERE order_id = :order_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
