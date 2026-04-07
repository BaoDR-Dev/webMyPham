<?php
class CartModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getCartItems($account_id)
    {
        // Bổ sung max_quantity (số lượng tồn kho) từ bảng product
        $query = "SELECT c.product_id, c.quantity, c.toggle, p.quantity AS max_quantity, p.name, p.price, p.image 
        FROM cart c 
        JOIN product p ON c.product_id = p.id 
        WHERE c.account_id = :account_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về dữ liệu dạng mảng liên kết
    }
    public function getCartByAccountId($account_id)
    {
        // Bổ sung max_quantity (số lượng tồn kho) từ bảng product
        $query = "SELECT c.product_id, c.quantity, c.toggle, p.quantity AS max_quantity, p.name, p.price, p.image 
        FROM cart c 
        JOIN product p ON c.product_id = p.id 
        WHERE c.account_id = :account_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về dữ liệu dạng mảng liên kết
    }

    public function updateProductQuantity2($userId, $productId, $quantity)
    {
        $sql = "UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $userId, $productId]);
    }
    public function getCategoryById($category_id)
    {
        $query = "SELECT name FROM categories WHERE id = :category_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ); // Trả về đối tượng chứa thông tin danh mục
    }

    public function getSelectedCartItems($account_id)
    {
        $query = "SELECT c.product_id, c.quantity, c.toggle, p.quantity AS max_quantity, p.name, p.price, p.image 
                  FROM cart c 
                  JOIN product p ON c.product_id = p.id 
                  WHERE c.account_id = :account_id AND c.toggle = 1"; // Lọc theo toggle = 1
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm lấy tổng số lượng sản phẩm trong giỏ hàng của người dùng
    public function getTotalCartQuantity($accountId)
    {
        $query = "SELECT SUM(quantity) AS total_quantity FROM cart WHERE account_id = :account_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $accountId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_quantity'] ?? 0;
    }



    public function getCartItem($account_id, $product_id)
    {
        $query = "SELECT * FROM cart 
                  WHERE account_id = :account_id AND product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function addOrUpdateCart($account_id, $product_id, $quantity)
    {
        $cartItem = $this->getCartItem($account_id, $product_id);
        if ($cartItem) {
            $query = "UPDATE cart 
                      SET quantity = quantity + :quantity 
                      WHERE account_id = :account_id AND product_id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        } else {
            $query = "INSERT INTO cart (account_id, product_id, quantity, toggle) 
                      VALUES (:account_id, :product_id, :quantity, 1)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        }
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function addOrUpdateCart2($account_id, $product_id, $quantity)
    {
        $query = "INSERT INTO cart (account_id, product_id, quantity)
                  VALUES (:account_id, :product_id, :quantity)
                  ON DUPLICATE KEY UPDATE quantity = quantity + :quantity";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        return $stmt->execute();
    }


    public function updateProductQuantity($product_id, $quantity)
    {
        $query = "UPDATE product SET quantity = quantity - :quantity WHERE id = :product_id AND quantity >= :quantity";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            error_log("Lỗi cập nhật số lượng sản phẩm: " . json_encode($stmt->errorInfo()));
        }
    }

    public function updateCartQuantity($account_id, $product_id, $quantity)
    {
        // Lấy max_quantity từ bảng product
        $query = "SELECT quantity FROM product WHERE id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $quantity > 0 && $quantity <= $product['quantity']) {
            // Tiến hành cập nhật số lượng giỏ hàng
            $query = "UPDATE cart 
                      SET quantity = :quantity 
                      WHERE account_id = :account_id AND product_id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            error_log("Số lượng không hợp lệ hoặc vượt tồn kho.");
        }
    }

    public function addOrUpdateCartItem($accountId, $productId, $quantity, $toggle = true)
    {
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $sqlCheck = "SELECT * FROM cart WHERE account_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([$accountId, $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Nếu đã có thì cập nhật số lượng (có thể giới hạn tối đa theo kho ở đây)
            $sqlUpdate = "UPDATE cart SET quantity = quantity + ?, toggle = ? WHERE account_id = ? AND product_id = ?";
            $stmt = $this->db->prepare($sqlUpdate);
            $stmt->execute([$quantity, $toggle, $accountId, $productId]);
        } else {
            // Nếu chưa có thì thêm mới
            $sqlInsert = "INSERT INTO cart (account_id, product_id, quantity, toggle) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sqlInsert);
            $stmt->execute([$accountId, $productId, $quantity, $toggle]);
        }
    }

    public function updateToggle($account_id, $product_id, $toggle)
    {
        $query = "UPDATE cart SET toggle = :toggle 
                  WHERE account_id = :account_id AND product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':toggle', $toggle ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function removeItem($account_id, $product_id)
    {
        $query = "DELETE FROM cart 
                  WHERE account_id = :account_id AND product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function clearSelectedCartItems($account_id)
    {
        // Delete only products with toggle = 1
        $query = "DELETE FROM cart WHERE account_id = :account_id AND toggle = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function clearCart($account_id)
    {
        $query = "DELETE FROM cart WHERE account_id = :account_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function updateQuantity($product_id, $quantity)
    {
        $user_id = $_SESSION['user']['id'] ?? null;
        if (!$user_id) return;

        $sql = "UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':quantity' => $quantity,
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);
    }

    public function updateCartItem($accountId, $productId, $quantity, $toggle)
    {
        $sql = "UPDATE cart 
            SET quantity = ?, toggle = ? 
            WHERE account_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $toggle, $accountId, $productId]);
    }
}
