<?php
class OrderModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createOrder(
        $account_id,
        $total_amount,
        $transaction_id,
        $payment_method,
        $address,
        $phone_number,
        $shipping_fee = 0,
        $promotion_id = null,    // Thêm mới
        $discount_amount = 0     // Thêm mới
    ) {
        // Thêm promotion_id và discount_amount vào câu Query
        $query = "
        INSERT INTO orders 
        (account_id, total_amount, transaction_id, payment_method, status, address, phone_number, shipping_fee, promotion_id, discount_amount)
        VALUES 
        (:account_id, :total_amount, :transaction_id, :payment_method, :status, :address, :phone_number, :shipping_fee, :promotion_id, :discount_amount)
    ";

        $stmt = $this->db->prepare($query);
        $status = 'pending';

        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindParam(':total_amount', $total_amount, PDO::PARAM_STR);
        $stmt->bindParam(':transaction_id', $transaction_id, PDO::PARAM_STR);
        $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
        $stmt->bindParam(':shipping_fee', $shipping_fee, PDO::PARAM_STR);
        $stmt->bindParam(':promotion_id', $promotion_id, PDO::PARAM_INT); // Thêm bind
        $stmt->bindParam(':discount_amount', $discount_amount, PDO::PARAM_STR); // Thêm bind

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }



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

    public function getOrdersByAccountId($account_id)
    {
        if (empty($account_id) || !is_numeric($account_id)) {
            throw new InvalidArgumentException('account_id không hợp lệ');
        }

        $sql = "
            SELECT 
                o.id,
                o.account_id,
                o.total_amount,
                o.discount_amount,
                o.shipping_fee,
                o.status,
                o.payment_method,
                o.transaction_id,
                o.created_at,
                o.promotion_id,
                p.name   AS promotion_name,
                p.discount_value,
                p.promotion_type_id
            FROM orders o
            LEFT JOIN promotion p 
                ON o.promotion_id = p.id
            WHERE o.account_id = :account_id
            ORDER BY o.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ) ?: [];
    }

    public function getOrdersByCustomerId($customerId)
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE account_id = ?");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    public function getOrderById($orderId)
    {
        $sql = "
        SELECT 
            o.*,
            p.name AS promotion_name,
            p.discount_value,
            p.promotion_type_id
        FROM orders o
        LEFT JOIN promotion p ON o.promotion_id = p.id
        WHERE o.id = :id
        LIMIT 1
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function getOrderDetails($order_id)
    {
        $query = "SELECT od.*, p.name AS product_name
    FROM order_details od
    JOIN product p ON od.product_id = p.id
    WHERE od.order_id = :order_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateOrderStatus($orderId, $status)
    {
        $sql = "UPDATE orders SET status = :status WHERE id = :orderId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':orderId', $orderId);
        return $stmt->execute();
    }

    public function getOrderStatisticsByDateRange($start_date, $end_date)
    {
        $query = "SELECT 
                DATE(created_at) AS order_date,
                COUNT(*) AS total_orders,
                SUM(total_amount) AS total_revenue
              FROM orders
              WHERE created_at BETWEEN :start_date AND :end_date
              GROUP BY DATE(created_at)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ); // rất quan trọng
    }





    public function getCustomerStatisticsByDateRange($start_date, $end_date)
    {
        $query = "
        SELECT 
            a.id AS account_id,
            a.full_name,
            COUNT(o.id) AS total_orders,
            SUM(o.total_amount) AS total_revenue
        FROM orders o
        JOIN account a ON a.id = o.account_id
        WHERE o.created_at BETWEEN :start_date AND :end_date
        GROUP BY a.id, a.full_name
    ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBuyersByProduct($keyword, $start_date, $end_date)
    {
        $sql = "
        SELECT a.id AS account_id, a.full_name,
               SUM(od.quantity) AS total_quantity,
               SUM(od.quantity * od.price) AS total_money
        FROM orders o
        JOIN order_details od ON o.id = od.order_id
        JOIN product p ON od.product_id = p.id
        JOIN account a ON o.account_id = a.id
        WHERE o.created_at BETWEEN :start AND :end
          AND (p.id = :id OR p.name LIKE :name)
        GROUP BY a.id, a.full_name
        ORDER BY total_quantity DESC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start' => $start_date,
            ':end'   => $end_date,
            ':id'    => is_numeric($keyword) ? $keyword : 0,
            ':name'  => '%' . $keyword . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getProductsByCustomer($account_id, $start, $end)
    {
        $sql = "
        SELECT p.id, p.name,
               SUM(od.quantity) AS total_quantity,
               SUM(od.quantity * od.price) AS total_money
        FROM orders o
        JOIN order_details od ON o.id = od.order_id
        JOIN product p ON od.product_id = p.id
        WHERE o.account_id = :id
          AND o.created_at BETWEEN :start AND :end
        GROUP BY p.id, p.name
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $account_id,
            ':start' => $start,
            ':end' => $end
        ]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    public function getCustomerStatistics()
    {
        $query = "SELECT account_id, COUNT(*) AS total_orders, SUM(total_amount) AS total_revenue 
              FROM orders 
              GROUP BY account_id
              ORDER BY total_orders DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function isProductInCompletedOrder($account_id, $product_id)
    {
        $query = "SELECT COUNT(*) 
                  FROM order_details od
                  INNER JOIN orders o ON od.order_id = o.id
                  WHERE o.account_id = :account_id AND od.product_id = :product_id AND o.status = 'completed'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function isValidOrder($account_id, $order_id, $product_id)
    {
        $query = "SELECT COUNT(*) 
                  FROM orders o
                  INNER JOIN order_details od ON o.id = od.order_id
                  WHERE o.account_id = :account_id 
                  AND o.id = :order_id 
                  AND od.product_id = :product_id 
                  AND o.status = 'completed'";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0; // Trả về true nếu hợp lệ
    }

    public function updateReviewStatus($order_id, $product_id)
    {
        $query = "UPDATE order_details 
              SET is_reviewed = 1 
              WHERE order_id = :order_id AND product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getFilteredOrders($sql, $params)
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAllOrdersWithUser()
    {
        $query = "
        SELECT 
            o.id,
            a.full_name,
            o.total_amount,
            o.payment_method,
            o.status,
            o.created_at
        FROM orders o
        INNER JOIN account a ON o.account_id = a.id
        ORDER BY o.created_at DESC
    ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        // Trả về mảng kết hợp thay vì object
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function filterOrders($filters)
    {
        $sql = "
        SELECT DISTINCT o.*, a.full_name 
        FROM orders o
        JOIN account a ON o.account_id = a.id
        LEFT JOIN order_details od ON o.id = od.order_id
        LEFT JOIN product p ON od.product_id = p.id
        WHERE 1=1
    ";
        $params = [];

        // Lọc theo tên khách hàng
        if (!empty($filters['customer_name'])) {
            $sql .= " AND a.full_name LIKE ?";
            $params[] = '%' . $filters['customer_name'] . '%';
        }

        // Lọc theo account_id
        if (!empty($filters['min_customer_id'])) {
            $sql .= " AND o.account_id >= ?";
            $params[] = $filters['min_customer_id'];
        }
        if (!empty($filters['max_customer_id'])) {
            $sql .= " AND o.account_id <= ?";
            $params[] = $filters['max_customer_id'];
        }

        // Lọc theo trạng thái
        if (!empty($filters['status']) && is_array($filters['status'])) {
            $placeholders = implode(',', array_fill(0, count($filters['status']), '?'));
            $sql .= " AND o.status IN ($placeholders)";
            $params = array_merge($params, $filters['status']);
        }

        // Lọc theo tổng tiền
        if (!empty($filters['min_amount'])) {
            $sql .= " AND o.total_amount >= ?";
            $params[] = $filters['min_amount'];
        }
        if (!empty($filters['max_amount'])) {
            $sql .= " AND o.total_amount <= ?";
            $params[] = $filters['max_amount'];
        }

        // Lọc theo ID đơn hàng
        if (!empty($filters['min_order_id'])) {
            $sql .= " AND o.id >= ?";
            $params[] = $filters['min_order_id'];
        }
        if (!empty($filters['max_order_id'])) {
            $sql .= " AND o.id <= ?";
            $params[] = $filters['max_order_id'];
        }

        // Lọc theo ngày tạo
        if (!empty($filters['start_date'])) {
            $sql .= " AND o.created_at >= ?";
            $params[] = $filters['start_date'] . ' 00:00:00';
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND o.created_at <= ?";
            $params[] = $filters['end_date'] . ' 23:59:59';
        }

        // Lọc theo phương thức thanh toán
        if (!empty($filters['payment_methods']) && is_array($filters['payment_methods'])) {
            $placeholders = implode(',', array_fill(0, count($filters['payment_methods']), '?'));
            $sql .= " AND o.payment_method IN ($placeholders)";
            $params = array_merge($params, $filters['payment_methods']);
        }

        // ✅ Lọc theo tên sản phẩm (nếu có)
        if (!empty($filters['product_name'])) {
            $sql .= " AND p.name LIKE ?";
            $params[] = '%' . $filters['product_name'] . '%';
        }

        // ✅ Lọc theo ID sản phẩm (nếu có)
        if (!empty($filters['product_id'])) {
            $sql .= " AND p.id = ?";
            $params[] = $filters['product_id'];
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND (
            a.full_name LIKE ?
            OR o.id = ?)";

            $params[] = '%' . $filters['keyword'] . '%';
            $params[] = is_numeric($filters['keyword'])
                ? (int)$filters['keyword']
                : 0;
        }



        $sql .= " ORDER BY created_at DESC";
        // Thực thi truy vấn
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerByOrderId($orderId)
    {
        $sql = "SELECT a.* FROM orders o
            JOIN account a ON o.account_id = a.id
            WHERE o.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getOrdersByAccountId2($accountId)
    {
        $sql = "SELECT * FROM orders WHERE account_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$accountId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filterOrdersForUser($filters)
    {
        $sql = "
        SELECT o.*, a.full_name 
        FROM orders o
        JOIN account a ON o.account_id = a.id
        WHERE o.account_id = ?
    ";
        $params = [$filters['account_id']];

        if (!empty($filters['status']) && is_array($filters['status'])) {
            $placeholders = implode(',', array_fill(0, count($filters['status']), '?'));
            $sql .= " AND o.status IN ($placeholders)";
            $params = array_merge($params, $filters['status']);
        }

        if (!empty($filters['min_amount'])) {
            $sql .= " AND o.total_amount >= ?";
            $params[] = $filters['min_amount'];
        }
        if (!empty($filters['max_amount'])) {
            $sql .= " AND o.total_amount <= ?";
            $params[] = $filters['max_amount'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND o.created_at >= ?";
            $params[] = $filters['start_date'] . ' 00:00:00';
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND o.created_at <= ?";
            $params[] = $filters['end_date'] . ' 23:59:59';
        }

        if (!empty($filters['payment_methods']) && is_array($filters['payment_methods'])) {
            $placeholders = implode(',', array_fill(0, count($filters['payment_methods']), '?'));
            $sql .= " AND o.payment_method IN ($placeholders)";
            $params = array_merge($params, $filters['payment_methods']);
        }

        if (!empty($filters['account_id'])) {
            $sql .= " AND o.account_id = ?";
            $params[] = $filters['account_id'];
        }

        if (!empty($filters['min_order_id'])) {
            $sql .= " AND o.id >= ?";
            $params[] = $filters['min_order_id'];
        }

        if (!empty($filters['max_order_id'])) {
            $sql .= " AND o.id <= ?";
            $params[] = $filters['max_order_id'];
        }


        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Lọc đơn hàng theo user và điều kiện filter
     * @param int $userId
     * @param array $filters
     * @return array
     */
    public function filterOrdersByUser($account_id, $filters)
    {
        $sql = "SELECT * FROM orders WHERE account_id = ?";
        $params = [$account_id];

        if (!empty($filters['enable_status']) && !empty($filters['status']) && is_array($filters['status'])) {
            $placeholders = implode(',', array_fill(0, count($filters['status']), '?'));
            $sql .= " AND status IN ($placeholders)";
            $params = array_merge($params, $filters['status']);
        }

        if (!empty($filters['enable_order_id'])) {
            if (!empty($filters['min_order_id'])) {
                $sql .= " AND id >= ?";
                $params[] = $filters['min_order_id'];
            }
            if (!empty($filters['max_order_id'])) {
                $sql .= " AND id <= ?";
                $params[] = $filters['max_order_id'];
            }
        }

        if (!empty($filters['enable_amount'])) {
            if (!empty($filters['min_amount'])) {
                $sql .= " AND total_amount >= ?";
                $params[] = $filters['min_amount'];
            }
            if (!empty($filters['max_amount'])) {
                $sql .= " AND total_amount <= ?";
                $params[] = $filters['max_amount'];
            }
        }

        if (!empty($filters['enable_date'])) {
            if (!empty($filters['start_date'])) {
                $sql .= " AND created_at >= ?";
                $params[] = $filters['start_date'] . " 00:00:00";
            }
            if (!empty($filters['end_date'])) {
                $sql .= " AND created_at <= ?";
                $params[] = $filters['end_date'] . " 23:59:59";
            }
        }

        if (!empty($filters['enable_payment_method']) && !empty($filters['payment_methods']) && is_array($filters['payment_methods'])) {
            $placeholders = implode(',', array_fill(0, count($filters['payment_methods']), '?'));
            $sql .= " AND payment_method IN ($placeholders)";
            $params = array_merge($params, $filters['payment_methods']);
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function filterOrdersByUser3($filters)
    {
        $sql = "SELECT * FROM orders WHERE account_id = :account_id";
        $params = [
            'account_id' => $filters['user_id']
        ];

        // Mã đơn A → B
        if (!empty($filters['min_order_id'])) {
            $sql .= " AND id >= :min_order_id";
            $params['min_order_id'] = $filters['min_order_id'];
        }
        if (!empty($filters['max_order_id'])) {
            $sql .= " AND id <= :max_order_id";
            $params['max_order_id'] = $filters['max_order_id'];
        }

        // Trạng thái
        if (!empty($filters['status'])) {
            $in = implode(',', array_map(fn($k) => ":st_$k", array_keys($filters['status'])));
            $sql .= " AND status IN ($in)";
            foreach ($filters['status'] as $k => $v) {
                $params["st_$k"] = $v;
            }
        }

        // Thanh toán
        if (!empty($filters['payment_methods'])) {
            $in = implode(',', array_map(fn($k) => ":pm_$k", array_keys($filters['payment_methods'])));
            $sql .= " AND payment_method IN ($in)";
            foreach ($filters['payment_methods'] as $k => $v) {
                $params["pm_$k"] = $v;
            }
        }

        // Khoảng tiền
        if ($filters['min_amount'] !== null) {
            $sql .= " AND total_amount >= :min_amount";
            $params['min_amount'] = $filters['min_amount'];
        }
        if ($filters['max_amount'] !== null) {
            $sql .= " AND total_amount <= :max_amount";
            $params['max_amount'] = $filters['max_amount'];
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
