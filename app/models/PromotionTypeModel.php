<?php
class PromotionTypeModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Lấy tất cả loại khuyến mãi
     * (PERCENT / FIXED / FREESHIP)
     */
    public function getAll()
    {
        $sql = "SELECT * FROM promotion_type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ); // ✅ array object
    }

    /**
     * Lấy loại khuyến mãi theo ID
     * (dùng khi edit promotion)
     */
    public function getById($id)
    {
        $sql = "SELECT id, code, name, description
                FROM promotion_type
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy loại khuyến mãi theo code
     * VD: PERCENT, FIXED, FREESHIP
     */
    public function getByCode($code)
    {
        $sql = "SELECT id, code, name
                FROM promotion_type
                WHERE code = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
