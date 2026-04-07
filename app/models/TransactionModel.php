<?php
class TransactionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createTransaction($data)
    {
        // Kiểm tra tính hợp lệ dữ liệu
        if (empty($data['orderId']) || empty($data['amount']) || !is_numeric($data['amount'])) {
            error_log("Dữ liệu không hợp lệ khi tạo giao dịch.");
            return false;
        }

        // Chuẩn bị câu lệnh SQL
        $query = "INSERT INTO transaction 
                  (orderId, partnerCode, requestId, amount, orderInfo, orderType, transId, resultCode, message, payType, responseTime, signature)
                  VALUES
                  (:orderId, :partnerCode, :requestId, :amount, :orderInfo, :orderType, :transId, :resultCode, :message, :payType, :responseTime, :signature)";

        $stmt = $this->db->prepare($query);

        // Ràng buộc tham số
        $stmt->bindParam(':orderId', $data['orderId'], PDO::PARAM_INT);
        $stmt->bindParam(':partnerCode', $data['partnerCode'], PDO::PARAM_STR);
        $stmt->bindParam(':requestId', $data['requestId'], PDO::PARAM_STR);
        $stmt->bindParam(':amount', $data['amount'], PDO::PARAM_STR);
        $stmt->bindParam(':orderInfo', $data['orderInfo'], PDO::PARAM_STR);
        $stmt->bindParam(':orderType', $data['orderType'], PDO::PARAM_STR);
        $stmt->bindParam(':transId', $data['transId'], PDO::PARAM_STR);
        $stmt->bindParam(':resultCode', $data['resultCode'], PDO::PARAM_INT);
        $stmt->bindParam(':message', $data['message'], PDO::PARAM_STR);
        $stmt->bindParam(':payType', $data['payType'], PDO::PARAM_STR);
        $stmt->bindParam(':responseTime', $data['responseTime'], PDO::PARAM_STR);
        $stmt->bindParam(':signature', $data['signature'], PDO::PARAM_STR);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            return $this->db->lastInsertId(); // Trả về ID giao dịch vừa tạo
        }

        // Ghi log lỗi nếu thất bại
        error_log("Lỗi khi thực hiện câu lệnh SQL (Transaction): " . implode(", ", $stmt->errorInfo()));
        return false;
    }



    public function getTransactionByOrderId($orderId)
    {
        $sql = "SELECT * FROM transaction WHERE orderId = :orderId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllTransactions()
    {
        $sql = "SELECT * FROM transaction ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
