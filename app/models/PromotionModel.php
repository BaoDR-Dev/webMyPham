<?php
class PromotionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getLatestWithReceiveStatus($accountId, $limit = 6)
    {
        $sql = "
        SELECT 
            p.*,
            ap.id     AS received_id,
            ap.status AS ap_status
        FROM promotion p
        LEFT JOIN account_promotions ap 
            ON ap.promotion_id = p.id 
            AND ap.account_id = :account_id
        WHERE p.is_active = 1
        ORDER BY p.created_at DESC
        LIMIT :limit
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':account_id', $accountId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả khuyến mãi
    public function getAll()
    {
        $sql = "SELECT p.*, pt.name AS promotion_type_name
                FROM promotion p
                JOIN promotion_type pt ON p.promotion_type_id = pt.id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy khuyến mãi đang active
    public function getActivePromotions()
    {
        $sql = "SELECT * FROM promotion
                WHERE is_active = 1
                  AND start_date <= NOW()
                  AND end_date >= NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy khuyến mãi theo ID
    public function getById($id)
    {
        $sql = "SELECT * FROM promotion WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }


    // Thêm khuyến mãi
    public function create($data)
    {
        $sql = "INSERT INTO promotion
                (name, content, promotion_type_id, discount_value,
                 min_order_amount, category_id, apply_per_product,
                 start_date, end_date, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['content'],
            $data['promotion_type_id'],
            $data['discount_value'],
            $data['min_order_amount'],
            $data['category_id'],
            $data['apply_per_product'],
            $data['start_date'],
            $data['end_date'],
            $data['is_active']
        ]);
    }

    // Cập nhật khuyến mãi
    public function update($id, $data)
    {
        $sql = "UPDATE promotion SET
                    name = ?,
                    content = ?,
                    promotion_type_id = ?,
                    discount_value = ?,
                    min_order_amount = ?,
                    category_id = ?,
                    apply_per_product = ?,
                    start_date = ?,
                    end_date = ?,
                    is_active = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['content'],
            $data['promotion_type_id'],
            $data['discount_value'],
            $data['min_order_amount'],
            $data['category_id'],
            $data['apply_per_product'],
            $data['start_date'],
            $data['end_date'],
            $data['is_active'],
            $id
        ]);
    }

    // Xóa khuyến mãi
    public function delete($id)
    {
        $sql = "DELETE FROM promotion WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Kiểm tra khuyến mãi hợp lệ cho đơn hàng
    public function validatePromotion($promotionId, $orderTotal)
    {
        $sql = "SELECT * FROM promotion
                WHERE id = ?
                  AND is_active = 1
                  AND start_date <= NOW()
                  AND end_date >= NOW()
                  AND (min_order_amount IS NULL OR min_order_amount <= ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$promotionId, $orderTotal]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function filter($conditions = [], $params = [])
    {
        $sql = "
        SELECT p.*, pt.name AS type_name
        FROM promotion p
        LEFT JOIN promotion_type pt ON pt.id = p.promotion_type_id
    ";

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY p.created_at DESC';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM promotion WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function getUserPromotionById($account_id, $promotion_id)
    {
        if (!$account_id || !$promotion_id) {
            return null;
        }

        $sql = "
        SELECT 
            p.id,
            p.name,
            p.promotion_type_id,
            p.discount_value,
            p.min_order_amount,
            p.end_date,
            ap.status
        FROM account_promotions ap
        INNER JOIN promotion p ON p.id = ap.promotion_id
        WHERE 
            ap.account_id = :account_id
            AND ap.promotion_id = :promotion_id
        LIMIT 1
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindValue(':promotion_id', $promotion_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    // public function getLatestWithReceiveStatus($accountId = null, $limit = 10)
    // {
    //     $sql = "
    //     SELECT 
    //         p.*,
    //         ap.id AS received_id
    //     FROM promotion p
    //     LEFT JOIN account_promotions ap 
    //         ON ap.promotion_id = p.id
    //         AND ap.account_id = :account_id
    //     WHERE p.is_active = 1
    //     ORDER BY p.created_at DESC
    //     LIMIT :limit
    // ";

    //     $stmt = $this->db->prepare($sql);
    //     $stmt->bindValue(':account_id', $accountId ?? 0, PDO::PARAM_INT);
    //     $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    //     $stmt->execute();

    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    public function getPromotionById($id)
    {
        $sql = "SELECT id, name FROM promotions WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
