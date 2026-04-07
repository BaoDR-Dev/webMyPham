<?php

class AccountPromotionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function updateExpiredAccountPromotions()
    {
        $sql = "
        UPDATE account_promotions ap
        JOIN promotion p ON ap.promotion_id = p.id
        SET ap.status = 0
        WHERE p.end_date < NOW()
          AND ap.status = 1
        
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }


    /* ===============================
       1. Kho khuyến mãi của user
       =============================== */
    public function getByAccount($accountId, $limit = 6, $offset = 0)
    {
        $sql = "
        SELECT 
            ap.*,
            p.id               AS promotion_id,
            p.name             AS promotion_name,
            p.content,
            p.promotion_type_id,
            p.discount_value,
            p.min_order_amount,
            p.category_id,
            p.start_date,
            p.end_date,
            c.name             AS category_name

        FROM account_promotions ap
        JOIN promotion p ON ap.promotion_id = p.id
        LEFT JOIN category c ON p.category_id = c.id

        WHERE ap.account_id = :account_id

        ORDER BY
            CASE
                WHEN ap.status = 1
                 AND (ap.received_quantity - ap.used_quantity) > 0
                 AND (p.end_date IS NULL OR p.end_date >= NOW())
                THEN 0
                ELSE 1
            END,
            p.end_date ASC

        LIMIT :limit OFFSET :offset
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':account_id', $accountId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }





    // Lấy danh sách có phân trang
    public function getByAccountPaginate($accountId, $limit, $offset)
    {
        $sql = "
        SELECT 
            ap.*,
            p.name AS promotion_name,
            p.promotion_type_id,
            p.discount_value,
            p.min_order_amount,
            p.start_date,
            p.end_date
        FROM account_promotions ap
        JOIN promotion p ON ap.promotion_id = p.id
        WHERE ap.account_id = ?
        ORDER BY ap.created_at DESC
        LIMIT ? OFFSET ?
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $accountId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Đếm tổng số
    public function countByAccount($accountId)
    {
        $sql = "SELECT COUNT(*) FROM account_promotions WHERE account_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$accountId]);
        return (int) $stmt->fetchColumn();
    }


    /* ===============================
       2. Nhận khuyến mãi
       =============================== */
    public function receivePromotion($accountId, $promotionId, $quantity = 1)
    {
        $sql = "
            INSERT INTO account_promotions (account_id, promotion_id, received_quantity)
            VALUES (:account_id, :promotion_id, :qty)
            ON DUPLICATE KEY UPDATE
                received_quantity = received_quantity + :qty,
                status = 1
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'account_id'   => $accountId,
            'promotion_id' => $promotionId,
            'qty'          => $quantity
        ]);
    }

    /* ===============================
       3. Kiểm tra còn dùng được không
       =============================== */
    public function canUse($accountId, $promotionId)
    {
        $sql = "
            SELECT ap.*
            FROM account_promotions ap
            JOIN promotion p ON ap.promotion_id = p.id
            WHERE ap.account_id = ?
              AND ap.promotion_id = ?
              AND ap.used_quantity < ap.received_quantity
              AND ap.status = 1
              AND p.end_date >= CURDATE()
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$accountId, $promotionId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /* ===============================
       4. Ghi nhận đã dùng
       =============================== */
    public function usePromotion($accountId, $promotionId)
    {
        try {
            $this->db->beginTransaction();

            $sql = "
                UPDATE account_promotions
                SET used_quantity = used_quantity + 1
                WHERE account_id = ?
                  AND promotion_id = ?
                  AND used_quantity < received_quantity
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$accountId, $promotionId]);

            $sql2 = "
                UPDATE account_promotions
                SET status = 0
                WHERE account_id = ?
                  AND promotion_id = ?
                  AND used_quantity >= received_quantity
            ";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$accountId, $promotionId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /* ===============================
       5. Xóa khỏi kho
       =============================== */
    public function remove($accountId, $promotionId)
    {
        $sql = "DELETE FROM account_promotions WHERE account_id = ? AND promotion_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$accountId, $promotionId]);
    }
}
