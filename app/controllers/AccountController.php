<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/AccountModel.php');
require_once(__DIR__ . '/../models/CartModel.php');
// AccountController.php (Bạn cần đặt dòng này ở đầu file hoặc trong hàm cần dùng)
require_once(__DIR__ . '/../helpers/EmailHelper.php');

class AccountController
{
    private $accountModel;
    private $cartModel;
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection(); // Lấy PDO từ Database
        $this->accountModel = new AccountModel($this->db); // Truyền PDO vào AccountModel
        $this->cartModel = new CartModel($this->db); // Truyền PDO vào CartModel
    }
    public function register()
    {
        include_once 'app/views/account/register.php';
    }

    public function login()
    {
        include_once 'app/views/account/login.php';
    }

    // AccountController.php (Thay thế hàm save() cũ)

    public function save()
    {
        // 1. Đặt múi giờ Việt Nam để không bị lệch giờ
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            $fullName = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmpassword'] ?? '';
            $address = $_POST['address'] ?? '';
            $phoneNumber = $_POST['phone_number'] ?? '';
            $birthDate = $_POST['birth_date'] ?? null;
            $role = 'user';

            // Validate cơ bản
            if (empty($email) || empty($password) || empty($fullName)) {
                $_SESSION['error'] = "Vui lòng nhập đủ thông tin.";
                include_once 'app/views/account/register.php';
                return;
            }

            // Check trùng email
            if ($this->accountModel->getAccountByEmail($email)) {
                $_SESSION['error'] = "Email này đã tồn tại.";
                include_once 'app/views/account/register.php';
                return;
            }

            // --- TẠO OTP ---
            $otp = (string)rand(100000, 999999); // Ép kiểu về chuỗi cho chắc chắn

            // Thời gian hết hạn: Hiện tại + 5 phút
            // Lưu ý: Dùng format Y-m-d H:i:s chuẩn
            $otpExpiry = date("Y-m-d H:i:s", time() + 300);

            // Lưu vào DB
            $result = $this->accountModel->save(
                $email,
                $fullName,
                $password,
                $address,
                $phoneNumber,
                $birthDate,
                $otp,
                $otpExpiry,
                $role
            );

            if ($result) {
                // Gửi mail
                require_once(__DIR__ . '/../helpers/EmailHelper.php');
                EmailHelper::sendVerificationEmail($email, $otp);

                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['temp_email'] = $email;
                $_SESSION['success_message'] = "Mã OTP đã gửi tới email. Nhập ngay!";

                header('Location: /webbanhang/account/verifyOtp');
                exit;
            } else {
                $_SESSION['error'] = "Lỗi hệ thống, không thể tạo tài khoản.";
                include_once 'app/views/account/register.php';
            }
        }
    }

    public function verifyOtp()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Nếu không có email trong session (truy cập trái phép) thì đá về login
        if (!isset($_SESSION['temp_email'])) {
            header('Location: /webbanhang/account/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $otpInput = $_POST['otp'] ?? '';
            $email = $_SESSION['temp_email'];

            // 1. Thử xác thực OTP
            if ($this->accountModel->verifyAccountByOtp($email, $otpInput)) {
                // --- THÀNH CÔNG ---
                unset($_SESSION['temp_email']);
                $_SESSION['success_message'] = "Kích hoạt thành công! Đăng nhập ngay.";
                header('Location: /webbanhang/account/login');
                exit;
            } else {
                // --- THẤT BẠI (Có thể do Sai OTP hoặc OTP hết hạn) ---

                // 2. Kiểm tra xem có phải do Hết hạn không?
                if ($this->accountModel->isOtpExpired($email)) {

                    // Nếu đã hết hạn -> Xóa tài khoản khỏi DB
                    $this->accountModel->deleteUnverifiedAccount($email);

                    // Xóa session email tạm
                    unset($_SESSION['temp_email']);

                    $_SESSION['error'] = "Mã OTP đã hết hiệu lực. Tài khoản đăng ký đã bị hủy. Vui lòng đăng ký lại.";
                    header('Location: /webbanhang/account/register'); // Chuyển hướng về trang đăng ký
                    exit;
                } else {
                    // Nếu chưa hết hạn -> Chỉ là nhập sai số -> Cho nhập lại
                    $_SESSION['error'] = "Mã OTP không đúng. Vui lòng kiểm tra lại email.";
                }
            }
        }

        include_once 'app/views/account/verify_otp.php';
    }

    public function verify()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = $_GET['token'] ?? null;

        if (empty($token)) {
            $_SESSION['error'] = 'Mã xác thực không hợp lệ.';
            header('Location: /webbanhang/account/login');
            return;
        }

        if ($this->accountModel->verifyAccountByToken($token)) {
            $_SESSION['success_message'] = 'Xác thực tài khoản thành công! Bạn có thể đăng nhập.';
        } else {
            $_SESSION['error'] = 'Mã xác thực không tồn tại, đã hết hạn, hoặc tài khoản đã được kích hoạt.';
        }
        header('Location: /webbanhang/account/login');
        exit;
    }

    // Trong file AccountController.php (hoặc nơi chứa hàm checkLogin)
    public function checkLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Lấy thông tin tài khoản theo email
            $account = $this->accountModel->getAccountByEmail($email);

            // Không tìm thấy tài khoản
            if (!$account) {
                $_SESSION['error'] = "Không tìm thấy tài khoản!";
                header("Location: /webbanhang/account/login");
                exit;
            }

            // Tài khoản bị vô hiệu hóa
            if ($account->is_active != 1) {
                $_SESSION['error'] = "Tài khoản của bạn đã bị vô hiệu hóa!";
                header("Location: /webbanhang/account/login");
                exit;
            }
            if ($account->is_verified != 1) {
                $_SESSION['error'] = "Tài khoản của bạn chưa được kích hoạt. Vui lòng kiểm tra email xác thực!";
                header("Location: /webbanhang/account/login");
                exit;
            }

            // Tài khoản bị vô hiệu hóa (is_active, vẫn giữ kiểm tra này)
            if ($account->is_active != 1) {
                $_SESSION['error'] = "Tài khoản của bạn đã bị vô hiệu hóa!";
                header("Location: /webbanhang/account/login");
                exit;
            }
            // Mật khẩu không đúng
            if (!password_verify($password, $account->password)) {
                $_SESSION['error'] = "Mật khẩu không đúng!";
                header("Location: /webbanhang/account/login");
                exit;
            }

            // Đăng nhập thành công - lưu thông tin vào session
            $_SESSION['account_id'] = $account->id;
            $_SESSION['email'] = $account->email;
            $_SESSION['role'] = $account->role;
            $_SESSION['full_name'] = $account->full_name;

            // =================================================================
            // DÒNG NÀY LÀ MẤU CHỐT - THÊM VÀO ĐỂ LƯU ẢNH VÀO SESSION
            $_SESSION['image'] = $account->image;
            // =================================================================


            // Cập nhật thời gian đăng nhập cuối cùng
            $this->accountModel->updateLastLogin($email);

            // Lấy tổng số lượng sản phẩm trong giỏ hàng (nếu không phải admin)
            if ($account->role !== 'admin') {
                $cartQuantity = $this->cartModel->getTotalCartQuantity($account->id);
                $_SESSION['cart_quantity'] = $cartQuantity;
            }

            // Điều hướng theo vai trò
            if ($account->role === 'admin') {

                header("Location: /webbanhang/admin/dashboard");
            } else {

                header("Location: /webbanhang/product/home");
                $this->updateCartSession($account->id);
            }
            exit;
        }
    }

    // AccountController.php (Thêm hàm mới)

    public function forgotPassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. SET MÚI GIỜ VIỆT NAM (Thêm dòng này)
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $account = $this->accountModel->getAccountByEmail($email);

            if ($account && $account->is_verified == 1) {
                $resetToken = bin2hex(random_bytes(32));

                // 2. TẠO THỜI GIAN HẾT HẠN (Giờ VN + 1 tiếng)
                $expiryTime = date("Y-m-d H:i:s", time() + 3600);

                // Lưu token vào DB
                $this->accountModel->setPasswordResetToken($email, $resetToken, $expiryTime);

                // Gửi Email
                require_once(__DIR__ . '/../helpers/EmailHelper.php');
                EmailHelper::sendPasswordResetEmail($email, $resetToken);

                $_SESSION['info'] = 'Link đặt lại mật khẩu đã được gửi đến email của bạn.';
            } else {
                $_SESSION['info'] = 'Nếu email tồn tại, chúng tôi đã gửi link đặt lại mật khẩu.';
            }
            header('Location: /webbanhang/account/login');
            exit;
        }
        // Load view form quên mật khẩu
        include_once 'app/views/account/forgotPassword.php';
    }

    public function resetPassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_GET['token'] ?? null;
        $user = null;

        if (!empty($token)) {
            $user = $this->accountModel->findUserByResetToken($token);
        }

        if (!$user) {
            $_SESSION['error'] = 'Mã đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.';
            header('Location: /webbanhang/account/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newPassword = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');

            if (empty($newPassword) || $newPassword !== $confirmPassword || strlen($newPassword) < 6) {
                $_SESSION['error'] = 'Mật khẩu không hợp lệ hoặc không khớp.';
                // Tải lại view resetPassword với token
                include_once 'app/views/account/resetPassword.php';
                return;
            }

            $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Cập nhật mật khẩu và xóa token
            if ($this->accountModel->updatePasswordAndClearResetToken($user->id, $newHashedPassword)) {
                $_SESSION['success_message'] = 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập.';
                header('Location: /webbanhang/account/login');
                exit;
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật mật khẩu.';
                include_once 'app/views/account/resetPassword.php';
            }
            if (!$user) {
                // Tạm thời comment dòng chuyển hướng lại
                // header('Location: /webbanhang/account/login');
                // exit;

                // Thêm dòng này để in lỗi ra màn hình
                echo "<h1>Lỗi xác thực Token!</h1>";
                echo "Token trên URL: " . htmlspecialchars($token) . "<br>";
                echo "Vui lòng kiểm tra lại Database xem cột token_expiry là mấy giờ?<br>";
                die(); // Dừng code tại đây
            }
        } else {
            // Load view form đặt lại mật khẩu
            include_once 'app/views/account/resetPassword.php';
        }
    }

    private function updateCartSession($account_id)
    {
        $cartItems = $this->cartModel->getCartItems($account_id);
        $_SESSION['cart'] = [];
        foreach ($cartItems as $item) {
            $_SESSION['cart'][$item['product_id']] = [
                'name'         => $item['name'],
                'price'        => $item['price'],
                'image'        => $item['image'],
                'quantity'     => $item['quantity'],
                'max_quantity' => $item['max_quantity'] ?? 0, // Đảm bảo max_quantity có giá trị
                'toggle'       => $item['toggle'] ?? 1, // Đảm bảo toggle có giá trị mặc định là 1 (hoặc phù hợp)
            ];
        }
        // Tính tổng số lượng sản phẩm
        $_SESSION['cart_quantity'] = $this->cartModel->getTotalCartQuantity($account_id);
    }

    public function profile()
    {
        // if (session_status() === PHP_SESSION_NONE) {
        //     session_start();
        // }

        // In session để kiểm tra
        error_log("Session Email: " . ($_SESSION['email'] ?? 'Không có'));

        if (!isset($_SESSION['email'])) {
            http_response_code(403);
            echo json_encode(["error" => "Bạn chưa đăng nhập!"]);
            exit;
        }

        $email = $_SESSION['email'];
        $userProfile = $this->accountModel->getProfileByEmail($email);

        if (!$userProfile) {
            http_response_code(404);
            echo json_encode(["error" => "Không tìm thấy tài khoản!"]);
            exit;
        }

        // In dữ liệu để debug
        error_log(json_encode($userProfile));

        header('Content-Type: application/json');
        echo json_encode($userProfile);
        exit;
    }


    // Trong file: app/controllers/AccountController.php

    public function updateProfile()
    {
        // 1. Kiểm tra phương thức và session
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Phương thức không hợp lệ."]);
            return;
        }

        if (!isset($_SESSION['email'])) {
            http_response_code(403);
            echo json_encode(["success" => false, "error" => "Bạn chưa đăng nhập!"]);
            return;
        }

        header('Content-Type: application/json');

        $email = $_SESSION['email'];
        $fullName = $_POST['full_name'] ?? null;
        $address = $_POST['address'] ?? null;
        $phoneNumber = $_POST['phone_number'] ?? null;
        $birthDate = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;

        // 2. Lấy đường dẫn ảnh cũ TRƯỚC KHI cập nhật
        $oldProfile = $this->accountModel->getProfileByEmail($email);
        $oldImagePath = $oldProfile->image ?? null;

        $newImagePath = null;

        // 3. Xử lý upload ảnh mới (nếu có)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = "public/uploads/account/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (!is_writable($uploadDir)) {
                echo json_encode(["success" => false, "error" => "Lỗi server: Thư mục upload không có quyền ghi!"]);
                return;
            }

            $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $newImagePath = $targetPath;
            } else {
                echo json_encode(["success" => false, "error" => "Lỗi server: Không thể di chuyển file đã upload."]);
                return;
            }
        }

        // 4. Gọi Model để cập nhật vào Database
        $result = $this->accountModel->updateProfile($email, $fullName, $address, $phoneNumber, $birthDate, $newImagePath);

        // 5. Xử lý kết quả
        if ($result) {
            // XÓA ẢNH CŨ nếu việc cập nhật DB thành công VÀ có ảnh mới được upload
            if ($newImagePath !== null && $oldImagePath !== null && file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            // Lấy lại thông tin mới nhất để trả về client
            $finalProfile = $this->accountModel->getProfileByEmail($email);
            echo json_encode([
                "success"   => true,
                "message"   => "Cập nhật thành công!",
                "new_image" => $finalProfile->image ?? null
            ]);
        } else {
            // Nếu cập nhật DB thất bại, xóa file mới upload để tránh rác
            if ($newImagePath !== null && file_exists($newImagePath)) {
                unlink($newImagePath);
            }
            echo json_encode(["success" => false, "error" => "Không có thông tin nào thay đổi."]);
        }
    }



    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['email'])) {
                http_response_code(403);
                echo "Bạn chưa đăng nhập!";
                exit;
            }

            $email = $_SESSION['email'];
            $currentPassword = trim($_POST['currentPassword'] ?? '');
            $newPassword = trim($_POST['newPassword'] ?? '');
            $confirmPassword = trim($_POST['confirmPassword'] ?? '');

            // Kiểm tra tài khoản
            $account = $this->accountModel->getAccountByEmail($email);
            if (!$account || !password_verify($currentPassword, $account->password)) {
                http_response_code(400);
                echo "Mật khẩu hiện tại không chính xác!";
                exit;
            }

            // Kiểm tra mật khẩu mới
            if (strlen($newPassword) < 6) {
                http_response_code(400);
                echo "Mật khẩu mới phải có ít nhất 6 ký tự!";
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                http_response_code(400);
                echo "Mật khẩu xác nhận không khớp!";
                exit;
            }

            // Cập nhật mật khẩu
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $updateSuccess = $this->accountModel->updatePassword($email, $hashedPassword);

            if ($updateSuccess) {
                session_unset();
                session_destroy();

                header('Content-Type: application/json');
                echo json_encode([
                    "status" => "success",
                    "message" => "Đổi mật khẩu thành công!"
                ]);
                exit;
            } else {
                http_response_code(500);
                echo "Có lỗi khi cập nhật mật khẩu.";
            }
        }
    }







    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();
        header('Location: /webbanhang/account/login'); // Chuyển về trang đăng nhập
        exit;
    }
}
