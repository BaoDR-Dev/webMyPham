<?php
class AccountModel
{
    private $conn;
    private $table_name = "account";

    public function __construct($db)
    {
        // Truyền trực tiếp đối tượng PDO
        $this->conn = $db;

        if (!$this->conn) {
            throw new Exception("Database connection failed.");
        }
    }


    public function getAccountByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            error_log("Lỗi SQL trong getAccountByEmail: " . json_encode($stmt->errorInfo()));
            return false;
        }

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        error_log("Kết quả trả về từ getAccountByEmail: " . json_encode($result));
        return $result ?: false;
    }



    public function updateLastLogin($email)
    {
        $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // AccountModel.php

    // Hàm đăng ký, thêm token xác thực và đặt is_verified = 0
    // app/models/AccountModel.php

    // 1. Sửa hàm save: Thêm tham số $expiry và lưu vào DB
    public function save($email, $fullName, $password, $address, $phoneNumber, $birthDate, $otp, $expiry, $role = 'user')
    {
        if ($this->getAccountByEmail($email)) {
            return false;
        }

        // Thêm cột token_expiry vào câu lệnh INSERT
        $query = "INSERT INTO " . $this->table_name . " 
          (email, full_name, password, address, phone_number, birth_date, role, is_active, is_verified, verification_token, token_expiry, created_at) 
          VALUES (:email, :full_name, :password, :address, :phone_number, :birth_date, :role, 1, 0, :otp, :expiry, NOW())";

        $stmt = $this->conn->prepare($query);

        // ... (Các phần sanitize dữ liệu giữ nguyên như cũ) ...
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":full_name", $fullName);
        $stmt->bindParam(":password", $passwordHash);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":phone_number", $phoneNumber);
        $stmt->bindParam(":birth_date", $birthDate);
        $stmt->bindParam(":role", $role);

        // Bind OTP và thời gian hết hạn
        $stmt->bindParam(":otp", $otp);
        $stmt->bindParam(":expiry", $expiry);

        return $stmt->execute();
    }

    // 2. Viết lại hàm Verify: Kiểm tra Email + OTP + Thời gian
    public function verifyAccountByOtp($email, $otpInput)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đảm bảo đúng múi giờ

        // 1. Chỉ tìm user khớp Email và OTP (Chưa check thời gian vội)
        $query = "SELECT id, token_expiry FROM " . $this->table_name . " 
              WHERE email = :email 
              AND verification_token = :token 
              AND is_verified = 0";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $otpInput);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // 2. Bây giờ mới check thời gian bằng PHP (Chính xác 100%)
            // So sánh: Thời gian hết hạn trong DB > Thời gian hiện tại
            if (strtotime($user['token_expiry']) > time()) {

                // OTP đúng và còn hạn -> Kích hoạt
                $updateQuery = "UPDATE " . $this->table_name . " 
                            SET is_verified = 1, verification_token = NULL, token_expiry = NULL 
                            WHERE id = :id";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':id', $user['id']);
                return $updateStmt->execute();
            }
        }

        // Nếu code chạy đến đây nghĩa là sai OTP hoặc OTP hết hạn
        return false;
    }

    // AccountModel.php (Thêm hàm mới)

    public function verifyAccountByToken($token)
    {
        // 1. Tìm user bằng token và trạng thái chưa verified
        $query = "SELECT id FROM " . $this->table_name . " 
              WHERE verification_token = :token AND is_verified = 0 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // 2. Cập nhật trạng thái thành verified và xóa token
            $updateQuery = "UPDATE " . $this->table_name . " 
                        SET is_verified = 1, verification_token = NULL 
                        WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            return $updateStmt->execute();
        }
        return false;
    }

    // AccountModel.php (Thêm các hàm mới)

    // Lưu token đặt lại và thời gian hết hạn
    public function setPasswordResetToken($email, $token, $expiryTime)
    {
        $query = "UPDATE " . $this->table_name . " 
              SET reset_token = :token, token_expiry = :expiry 
              WHERE email = :email AND is_verified = 1"; // Chỉ cho phép đặt lại khi đã verified
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':expiry', $expiryTime, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Tìm user bằng reset token và kiểm tra hết hạn
    public function findUserByResetToken($token)
    {
        // 1. SET MÚI GIỜ VIỆT NAM (Để khớp với lúc tạo)
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        // Lấy thông tin user và thời gian hết hạn
        $query = "SELECT id, email, token_expiry FROM " . $this->table_name . " 
                  WHERE reset_token = :token LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user) {
            // 2. SO SÁNH THỜI GIAN
            // Chuyển string trong DB thành số giây (Timestamp)
            $expiryTimestamp = strtotime($user->token_expiry);
            $currentTimestamp = time(); // Thời gian hiện tại ở VN

            // Debug (Nếu vẫn lỗi thì bỏ comment 2 dòng dưới để xem nó in ra gì)
            // echo "Hết hạn lúc: " . date('Y-m-d H:i:s', $expiryTimestamp) . "<br>";
            // echo "Hiện tại là: " . date('Y-m-d H:i:s', $currentTimestamp) . "<br>"; die();

            if ($expiryTimestamp > $currentTimestamp) {
                return $user; // Token còn hạn -> OK
            }
        }
        return false; // Không tìm thấy hoặc hết hạn
    }

    // Cập nhật mật khẩu và xóa token reset
    public function updatePasswordAndClearResetToken($userId, $newHashedPassword)
    {
        $query = "UPDATE " . $this->table_name . " 
              SET password = :password, reset_token = NULL, token_expiry = NULL 
              WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $newHashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Kiểm tra xem OTP của email này đã hết hạn chưa
    public function isOtpExpired($email)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đảm bảo đúng giờ VN

        $query = "SELECT token_expiry FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['token_expiry']) {
            // Nếu thời gian hết hạn < thời gian hiện tại => Đã hết hạn
            return strtotime($user['token_expiry']) < time();
        }
        return false;
    }

    // Xóa tài khoản chưa kích hoạt (Dùng khi OTP hết hạn)
    public function deleteUnverifiedAccount($email)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE email = :email AND is_verified = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    public function getAllUsersExceptAdmin()
    {
        $query = "SELECT id, full_name, email, birth_date, address, phone_number, is_active, last_login
              FROM " . $this->table_name . " 
              WHERE role != 'admin'";
        $stmt = $this->conn->prepare($query);

        if (!$stmt->execute()) {
            error_log("Lỗi SQL: " . json_encode($stmt->errorInfo()));
            return false;
        }

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateStatus($userId, $isActive)
    {
        $query = "UPDATE " . $this->table_name . " SET is_active = :is_active WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':is_active', $isActive, PDO::PARAM_INT);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }




    public function getProfileByEmail($email)
    {
        $query = "SELECT email, full_name, address, phone_number, birth_date, image 
              FROM " . $this->table_name . " 
              WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            error_log("Lỗi SQL: " . json_encode($stmt->errorInfo()));
            return false;
        }

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function updateProfile($email, $fullName, $address, $phoneNumber, $birthDate, $imagePath = null)
    {
        // Bắt đầu câu lệnh UPDATE
        $query = "UPDATE " . $this->table_name . " 
              SET full_name = :full_name, 
                  address = :address, 
                  phone_number = :phone_number, 
                  birth_date = :birth_date";

        // Chỉ thêm phần cập nhật ảnh nếu $imagePath không phải là null
        if ($imagePath !== null) {
            $query .= ", image = :image";
        }

        $query .= " WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        // Bind các tham số cơ bản
        $stmt->bindParam(":full_name", $fullName);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":phone_number", $phoneNumber);
        $stmt->bindParam(":birth_date", $birthDate);
        $stmt->bindParam(":email", $email);

        // Chỉ bind tham số ảnh nếu có
        if ($imagePath !== null) {
            $stmt->bindParam(":image", $imagePath);
        }

        if (!$stmt->execute()) {
            error_log("SQL Error on profile update: " . json_encode($stmt->errorInfo()));
            return false;
        }

        // Trả về true nếu có ít nhất 1 hàng được cập nhật
        return $stmt->rowCount() > 0;
    }


    public function updateUserStatus($userId, $isActive)
    {
        $query = "UPDATE " . $this->table_name . " SET is_active = :is_active WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':is_active', $isActive, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updatePassword($email, $hashedPassword)
    {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            error_log("Lỗi SQL khi cập nhật mật khẩu: " . json_encode($stmt->errorInfo()));
            return false;
        }

        if ($stmt->rowCount() === 0) {
            error_log("Không có dòng nào bị ảnh hưởng. Email có tồn tại không?");
            return false;
        } 

        error_log("Mật khẩu đã được cập nhật thành công cho email: " . $email);
        return true;
    }
    public function getAccountById($account_id)
    {
        $query = "SELECT * FROM account WHERE id = :account_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ); // Trả về đối tượng chứa thông tin khách hàng
    }

    public function filterUsers($filters)
    {
        $sql = "SELECT * FROM account WHERE 1=1 AND role != 'admin'";
        $params = [];
        /* ======================
        QUICK SEARCH (KEYWORD)
    ====================== */
        if (!empty($filters['keyword'])) {
            $sql .= " AND (
            full_name LIKE :kw
            OR email LIKE :kw
            OR phone_number LIKE :kw
            OR address LIKE :kw
            OR birth_date LIKE :kw
        )";
            $params[':kw'] = '%' . $filters['keyword'] . '%';
        }

        /* ======================
        FILTER CHI TIẾT
    ====================== */
        if (!empty($filters['searchName'])) {
            $sql .= " AND full_name LIKE :name";
            $params[':name'] = '%' . $filters['searchName'] . '%';
        }

        if (!empty($filters['searchEmail'])) {
            $sql .= " AND email LIKE :email";
            $params[':email'] = '%' . $filters['searchEmail'] . '%';
        }

        if (!empty($filters['searchAddress'])) {
            $sql .= " AND address LIKE :address";
            $params[':address'] = '%' . $filters['searchAddress'] . '%';
        }

        if (!empty($filters['searchPhone'])) {
            $sql .= " AND phone_number LIKE :phone";
            $params[':phone'] = '%' . $filters['searchPhone'] . '%';
        }

        if (!empty($filters['birthDateFrom'])) {
            $sql .= " AND birth_date >= :birthDateFrom";
            $params[':birthDateFrom'] = $filters['birthDateFrom'];
        }

        if (!empty($filters['birthDateTo'])) {
            $sql .= " AND birth_date <= :birthDateTo";
            $params[':birthDateTo'] = $filters['birthDateTo'];
        }

        if (!empty($filters['minId'])) {
            $sql .= " AND id >= :minId";
            $params[':minId'] = $filters['minId'];
        }

        if (!empty($filters['maxId'])) {
            $sql .= " AND id <= :maxId";
            $params[':maxId'] = $filters['maxId'];
        }

        if (!empty($filters['lastLoginDays'])) {
            $sql .= " AND last_login >= DATE_SUB(NOW(), INTERVAL :days DAY)";
            $params[':days'] = (int)$filters['lastLoginDays'];
        }

        if ($filters['status'] !== null && $filters['status'] !== '') {
            $sql .= " AND is_active = :status";
            $params[':status'] = $filters['status'];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }





    public function searchByKeyworduser($keyword)
    {
        $keyword = "%$keyword%";
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ?");
        $stmt->execute([$keyword, $keyword]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function applyFiltersuser($users, $filters)
    {
        return array_filter($users, function ($user) use ($filters) {
            if (!empty($filters['minId']) && $user['id'] < $filters['minId']) return false;
            if (!empty($filters['maxId']) && $user['id'] > $filters['maxId']) return false;
            if (!empty($filters['role']) && $user['role'] !== $filters['role']) return false;
            if (!empty($filters['status']) && $user['status'] !== $filters['status']) return false;
            return true;
        });
    }
}
