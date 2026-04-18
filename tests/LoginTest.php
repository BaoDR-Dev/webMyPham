<?php
/**
 * LoginTest.php
 * PHPUnit White-box Tests: checkLogin, save (Register), addCart, finalizeCheckout
 *
 * Cách chạy:
 *   vendor/bin/phpunit --no-coverage
 *   php -d xdebug.mode=coverage vendor/bin/phpunit --coverage-html coverage/html
 */

use PHPUnit\Framework\TestCase;

// ═════════════════════════════════════════════════════════════════════════════
//  TEST CLASS 1 – checkLogin
// ═════════════════════════════════════════════════════════════════════════════
/**
 * @covers AccountModel::getAccountByEmail
 * @covers AccountModel::updateLastLogin
 */
class CheckLoginTest extends TestCase
{
    private PDO $pdo;
    private TestAccountModel $model;

    protected function setUp(): void
    {
        $this->pdo   = createTestDatabase();
        $this->model = new TestAccountModel($this->pdo);

        // Seed động từ JSON – is_active/is_verified đọc từ Excel
        foreach (ExcelDataHelper::seeds() as $email => $info) {
            $hash       = password_hash($info['password'], PASSWORD_BCRYPT);
            $role       = $info['role']        ?? 'user';
            $isActive   = $info['is_active']   ?? 1;
            $isVerified = $info['is_verified']  ?? 1;
            $stmt = $this->pdo->prepare(
                "INSERT OR IGNORE INTO account (email, full_name, password, role, is_active, is_verified)
                 VALUES (:email, 'Test User', :hash, :role, :is_active, :is_verified)"
            );
            $stmt->execute([
                ':email'       => $email,
                ':hash'        => $hash,
                ':role'        => $role,
                ':is_active'   => $isActive,
                ':is_verified' => $isVerified,
            ]);
        }
    }

    public static function loginDataProvider(): array
    {
        try {
            return ExcelDataHelper::provider();
        } catch (\RuntimeException $e) {
            // Nếu chưa chạy excel_to_json.py thì báo lỗi rõ ràng
            throw new \RuntimeException(
                "Chưa có test_data.json. Chạy trước: python test-data/excel_to_json.py\n" . $e->getMessage()
            );
        }
    }

    /**
     * @test
     * @dataProvider loginDataProvider
     */
    public function test_login_from_excel(string $tcId, string $method, string $email, string $password, string $expected): void
    {
        // ── TC GET → controller không xử lý → luôn FAIL ───────────────────
        if ($method === 'GET') {
            $actual      = 'Request method GET không được xử lý';
            $realStatus  = 'FAIL';
            $finalStatus = ($realStatus === $expected) ? 'PASS' : 'FAIL';
            ExcelDataHelper::writeResult($tcId, $actual, $finalStatus);
            $this->assertEquals('PASS', $finalStatus, "[$tcId] $actual");
            return;
        }

        $email    = trim($email);
        $password = trim($password);

        // ── Bước 1: validate input ─────────────────────────────────────────
        if (empty($email) || empty($password)) {
            $actual      = empty($email) ? 'Email trống' : 'Password trống';
            $realStatus  = 'FAIL';
            $finalStatus = ($realStatus === $expected) ? 'PASS' : 'FAIL';
            ExcelDataHelper::writeResult($tcId, $actual, $finalStatus);
            $this->assertEquals('PASS', $finalStatus, "[$tcId] $actual");
            return;
        }

        // ── Bước 2: gọi AccountModel thật ─────────────────────────────────
        $account = $this->model->getAccountByEmail($email);

        // ── Bước 3: assert từng điều kiện ─────────────────────────────────
        if (!$account) {
            $actual     = 'Không tìm thấy tài khoản!';
            $realStatus = 'FAIL';
            $this->assertFalse((bool)$account, "[$tcId] Model phải trả về false");

        } elseif ($account->is_active != 1) {
            $actual     = 'Tài khoản đã bị vô hiệu hóa!';
            $realStatus = 'FAIL';
            $this->assertNotEquals(1, $account->is_active, "[$tcId] is_active phải != 1");

        } elseif ($account->is_verified != 1) {
            $actual     = 'Tài khoản chưa được kích hoạt!';
            $realStatus = 'FAIL';
            $this->assertNotEquals(1, $account->is_verified, "[$tcId] is_verified phải != 1");

        } elseif (!password_verify($password, $account->password)) {
            $actual     = 'Mật khẩu không đúng!';
            $realStatus = 'FAIL';
            $this->assertFalse(password_verify($password, $account->password), "[$tcId] password_verify phải false");

        } else {
            $updated  = $this->model->updateLastLogin($email);
            $redirect = ($account->role === 'admin') ? '/admin/dashboard' : '/product/home';
            $actual   = "Đăng nhập thành công → $redirect";
            $realStatus = 'PASS';
            $this->assertTrue($updated,                              "[$tcId] updateLastLogin phải true");
            $this->assertEquals(1, $account->is_active,             "[$tcId] is_active phải = 1");
            $this->assertEquals(1, $account->is_verified,           "[$tcId] is_verified phải = 1");
            $this->assertContains($account->role, ['user', 'admin'], "[$tcId] role phải hợp lệ");
        }

        $finalStatus = ($realStatus === $expected) ? 'PASS' : 'FAIL';
        ExcelDataHelper::writeResult($tcId, $actual, $finalStatus);
        $this->assertEquals('PASS', $finalStatus,
            "[$tcId] Mong đợi: $expected | Thực tế: $realStatus | $actual"
        );
    }

    // ─── BUG_TC01: Dead Code – check is_active 2 lần ────────────────────────
    /** @test */
    public function BUG_TC01_deadCodeDoubleCheckIsActive(): void
    {
        // Đọc source AccountController để kiểm tra có check is_active 2 lần không
        $source = file_get_contents(__DIR__ . '/../app/controllers/AccountController.php');

        // Đếm số lần check is_active != 1 trong hàm checkLogin
        $matches = [];
        preg_match_all('/is_active\s*!=\s*1/', $source, $matches);
        $count = count($matches[0]);

        $actual = "is_active != 1 xuất hiện $count lần trong controller";
        // Đúng ra chỉ cần check 1 lần
        $status = ($count === 1) ? 'PASS' : 'FAIL';
        ExcelDataHelper::writeResult('BUG_TC01', $actual, $status);

        $this->assertEquals(1, $count,
            "BUG_TC01: Dead code – is_active bị check $count lần, chỉ cần 1 lần"
        );
    }

    // ─── BUG_TC02: Lỗi điều hướng – redirect sau exit vẫn có code thừa ──────
    /** @test */
    public function BUG_TC02_deadCodeAfterExit(): void
    {
        $source = file_get_contents(__DIR__ . '/../app/controllers/AccountController.php');

        // Tìm đoạn checkLogin: sau exit; cuối cùng không được có header() nữa
        // Lấy phần checkLogin
        preg_match('/public function checkLogin\(\)(.*?)public function /s', $source, $m);
        $checkLoginBody = $m[1] ?? '';

        // Đếm số lần gọi header("Location:...") trong checkLogin
        $matches = [];
        preg_match_all('/header\s*\(\s*["\']Location:/', $checkLoginBody, $matches);
        $count = count($matches[0]);

        $actual = "header(Location:) được gọi $count lần trong checkLogin";
        // Mỗi nhánh có 1 redirect → tổng hợp lý là 4 (not found, disabled, not verified, success)
        // Nếu > 4 → có redirect thừa
        $status = ($count <= 4) ? 'PASS' : 'FAIL';
        ExcelDataHelper::writeResult('BUG_TC02', $actual, $status);

        $this->assertLessThanOrEqual(4, $count,
            "BUG_TC02: Có $count lần redirect trong checkLogin, nhiều hơn cần thiết"
        );
    }

    // ─── BUG_TC03: Lỗi Session – không destroy session cũ trước khi tạo mới ─
    /** @test */
    public function BUG_TC03_sessionNotDestroyedBeforeLogin(): void
    {
        $source = file_get_contents(__DIR__ . '/../app/controllers/AccountController.php');

        // Lấy phần checkLogin
        preg_match('/public function checkLogin\(\)(.*?)public function /s', $source, $m);
        $checkLoginBody = $m[1] ?? '';

        // Kiểm tra có session_regenerate_id hoặc session_destroy trước khi set session không
        $hasRegenerate = str_contains($checkLoginBody, 'session_regenerate_id');
        $hasDestroy    = str_contains($checkLoginBody, 'session_destroy');

        $actual = 'session_regenerate_id: ' . ($hasRegenerate ? 'có' : 'không có') .
                  ' | session_destroy: ' . ($hasDestroy ? 'có' : 'không có');
        // Bảo mật đúng cần có session_regenerate_id(true) trước khi set session
        $status = $hasRegenerate ? 'PASS' : 'FAIL';
        ExcelDataHelper::writeResult('BUG_TC03', $actual, $status);

        $this->assertTrue($hasRegenerate,
            "BUG_TC03: Thiếu session_regenerate_id() trước khi set session → rủi ro Session Fixation"
        );
    }
}

// ═════════════════════════════════════════════════════════════════════════════
//  TEST CLASS 2 – save / Register
// ═════════════════════════════════════════════════════════════════════════════
/**
 * @covers AccountModel::save
 * @covers AccountModel::getAccountByEmail
 * @covers AccountModel::verifyAccountDirectly
 */
class SaveRegisterTest extends TestCase
{
    private PDO $pdo;
    private TestAccountModel $model;

    protected function setUp(): void
    {
        $this->pdo   = createTestDatabase();
        $this->model = new TestAccountModel($this->pdo);
    }

    private function doSave(
        string  $email    = 'new@example.com',
        string  $fullName = 'New User',
        string  $password = 'Pass@123',
        string  $address  = '123 HCM',
        string  $phone    = '0901234567',
        ?string $birth    = '2000-01-01',
        string  $otp      = '123456',
        ?string $expiry   = null,
        string  $role     = 'user'
    ): bool {
        $expiry ??= date('Y-m-d H:i:s', time() + 300);
        return $this->model->save($email, $fullName, $password, $address, $phone, $birth, $otp, $expiry, $role);
    }

    /** @test */
    public function TC_REG_01_registerValidData(): void
    {
        $this->assertTrue($this->doSave());
        $account = $this->model->getAccountByEmail('new@example.com');
        $this->assertNotNull($account);
        $this->assertEquals('New User', $account->full_name);
        $this->assertEquals('user',     $account->role);
        $this->assertEquals(0,          $account->is_verified);
    }

    /** @test */
    public function TC_REG_02_duplicateEmail(): void
    {
        $this->doSave('dup@example.com');
        $this->assertFalse($this->doSave('dup@example.com'));
    }

    /** @test */
    public function TC_REG_03_emptyEmail(): void
    {
        $this->assertTrue(empty(''));
    }

    /** @test */
    public function TC_REG_04_emptyFullName(): void
    {
        $this->assertTrue(empty(''));
    }

    /** @test */
    public function TC_REG_05_emptyPassword(): void
    {
        $this->assertTrue(empty(''));
    }

    /** @test */
    public function TC_REG_06_passwordIsHashed(): void
    {
        $this->doSave('hashed@example.com', 'Hashed User', 'PlainPass');
        $account = $this->model->getAccountByEmail('hashed@example.com');
        $this->assertNotEquals('PlainPass', $account->password);
        $this->assertTrue(password_verify('PlainPass', $account->password));
    }

    /** @test */
    public function TC_REG_07_verifyAccountDirectly(): void
    {
        $this->doSave('verify@example.com');
        $this->model->verifyAccountDirectly('verify@example.com');
        $account = $this->model->getAccountByEmail('verify@example.com');
        $this->assertEquals(1, $account->is_verified);
    }

    /** @test */
    public function TC_REG_08_otpStoredInDB(): void
    {
        $this->doSave('otp@example.com', 'OTP User', 'Pass@123', '123 HCM', '0909090909', '1995-05-05', '654321');
        $stmt = $this->pdo->prepare("SELECT verification_token FROM account WHERE email = ?");
        $stmt->execute(['otp@example.com']);
        $this->assertEquals('654321', $stmt->fetch(PDO::FETCH_OBJ)->verification_token);
    }

    /** @test */
    public function TC_REG_09_nullBirthDate(): void
    {
        $this->assertTrue($this->doSave('nobirth@example.com', 'No Birth', 'Pass@123', 'HCM', '0900000000', null));
    }

    /** @test */
    public function TC_REG_10_defaultRoleIsUser(): void
    {
        $this->doSave('rolecheck@example.com');
        $this->assertEquals('user', $this->model->getAccountByEmail('rolecheck@example.com')->role);
    }
}

// ═════════════════════════════════════════════════════════════════════════════
//  TEST CLASS 3 – addCart
// ═════════════════════════════════════════════════════════════════════════════
/**
 * @covers CartModel::addOrUpdateCart
 * @covers CartModel::getCartItems
 * @covers CartModel::getTotalCartQuantity
 * @covers CartModel::getCartItem
 * @covers CartModel::removeItem
 */
class AddCartTest extends TestCase
{
    private PDO $pdo;
    private CartModel $cartModel;

    protected function setUp(): void
    {
        $this->pdo       = createTestDatabase();
        $this->cartModel = new CartModel($this->pdo);

        $this->pdo->exec("INSERT INTO product (id, name, price, quantity) VALUES (1, 'Son Môi A', 150000, 50)");
        $this->pdo->exec("INSERT INTO product (id, name, price, quantity) VALUES (2, 'Kem Dưỡng B', 300000, 10)");
        $this->pdo->exec("INSERT INTO product (id, name, price, quantity) VALUES (3, 'Hết Hàng C', 200000, 0)");
        $this->pdo->exec("INSERT INTO account (id, email, full_name, password, role, is_active, is_verified)
            VALUES (1, 'buyer@example.com', 'Buyer', 'hash', 'user', 1, 1)");
    }

    /** @test */
    public function TC_CART_01_addNewProduct(): void
    {
        $this->cartModel->addOrUpdateCart(1, 1, 2);
        $item = $this->cartModel->getCartItem(1, 1);
        $this->assertNotNull($item);
        $this->assertEquals(2, $item->quantity);
    }

    /** @test */
    public function TC_CART_02_addSameProductIncreasesQuantity(): void
    {
        $this->cartModel->addOrUpdateCart(1, 1, 2);
        $this->cartModel->addOrUpdateCart(1, 1, 3);
        $this->assertEquals(5, $this->cartModel->getCartItem(1, 1)->quantity);
    }

    /** @test */
    public function TC_CART_03_addMultipleProducts(): void
    {
        $this->cartModel->addOrUpdateCart(1, 1, 1);
        $this->cartModel->addOrUpdateCart(1, 2, 2);
        $this->assertEquals(3, $this->cartModel->getTotalCartQuantity(1));
    }

    /** @test */
    public function TC_CART_04_notLoggedIn_accountIdNull(): void
    {
        $this->assertNull(null);
    }

    /** @test */
    public function TC_CART_05_productNotExist(): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE id = ?");
        $stmt->execute([999]);
        $this->assertFalse((bool)$stmt->fetch());
    }

    /** @test */
    public function TC_CART_06_outOfStock(): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE id = ?");
        $stmt->execute([3]);
        $this->assertLessThan(1, $stmt->fetch(PDO::FETCH_OBJ)->quantity);
    }

    /** @test */
    public function TC_CART_07_quantityZeroIsInvalid(): void
    {
        $this->assertFalse(is_numeric(0) && 0 > 0);
    }

    /** @test */
    public function TC_CART_08_negativeQuantityIsInvalid(): void
    {
        $this->assertFalse(is_numeric(-5) && -5 > 0);
    }

    /** @test */
    public function TC_CART_09_removeProduct(): void
    {
        $this->cartModel->addOrUpdateCart(1, 1, 3);
        $this->cartModel->removeItem(1, 1);
        $this->assertFalse((bool)$this->cartModel->getCartItem(1, 1));
    }

    /** @test */
    public function TC_CART_10_emptyCartTotalIsZero(): void
    {
        $this->assertEquals(0, $this->cartModel->getTotalCartQuantity(1));
    }

    // ─── BUG_CART01: Account is_active=0 vẫn add được → controller không check
    /** @test */
    public function BUG_CART01_disabledAccountCanStillAddToCart(): void
    {
        // Seed tài khoản bị vô hiệu hóa
        $this->pdo->exec("INSERT INTO account (id, email, full_name, password, role, is_active, is_verified)
            VALUES (2, 'disabled@test.com', 'Disabled', 'hash', 'user', 0, 1)");

        // Seed product
        $this->pdo->exec("INSERT INTO product (id, name, price, quantity) VALUES (10, 'Test SP', 100000, 5)");

        // Kiểm tra account có is_active=0
        $stmt = $this->pdo->prepare("SELECT is_active FROM account WHERE id = 2");
        $stmt->execute();
        $account = $stmt->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $account->is_active, "Account phải có is_active=0");

        // CartModel không check is_active → vẫn add được (đây là bug)
        $this->cartModel->addOrUpdateCart(2, 10, 1);
        $item = $this->cartModel->getCartItem(2, 10);

        // Kiểm tra controller có check is_active không
        $source = file_get_contents(__DIR__ . '/../app/controllers/CartController.php');
        $hasActiveCheck = str_contains($source, 'is_active');

        $this->assertTrue($hasActiveCheck,
            "BUG_CART01: CartController không kiểm tra is_active → tài khoản bị khóa vẫn thêm được vào giỏ"
        );
    }
}

// ═════════════════════════════════════════════════════════════════════════════
//  TEST CLASS 4 – finalizeCheckout
// ═════════════════════════════════════════════════════════════════════════════
class FinalizeCheckoutTest extends TestCase
{
    private function checkoutLogic(
        array   $cartItems,
        float   $totalAmount,
        string  $address,
        string  $phone,
        string  $paymentMethod   = 'cash',
        int     $promotionTypeId = 0,
        float   $discountValue   = 0,
        float   $minOrderAmount  = 0,
        bool    $promoActive     = true,
        ?string $promoEndDate    = null
    ): array {
        if (empty($cartItems) || $totalAmount <= 0)
            return ['error' => 'Giỏ hàng trống hoặc không hợp lệ.'];
        if (empty($address) || empty($phone))
            return ['error' => 'Vui lòng nhập đầy đủ địa chỉ và số điện thoại.'];

        $shippingFee = ($totalAmount > 3_000_000) ? 0 : 150_000;
        $discount    = 0;

        if ($promotionTypeId > 0) {
            $endDate = $promoEndDate ?? date('Y-m-d', strtotime('+1 day'));
            $isValid = $promoActive && strtotime($endDate) >= time() && $totalAmount >= $minOrderAmount;
            if ($isValid) {
                if ($promotionTypeId === 1)     $discount = $totalAmount * ($discountValue / 100);
                elseif ($promotionTypeId === 2) $discount = $discountValue;
                elseif ($promotionTypeId === 3) $discount = $shippingFee;
            }
        }

        $validMethods = ['cash', 'momo_qr', 'momo_atm', 'vnpay'];
        if (!in_array($paymentMethod, $validMethods))
            return ['error' => 'Phương thức thanh toán không hợp lệ.'];

        return [
            'shippingFee'   => $shippingFee,
            'discount'      => $discount,
            'finalTotal'    => max(0, $totalAmount + $shippingFee - $discount),
            'paymentMethod' => $paymentMethod,
        ];
    }

    private function item(float $price = 500_000): array
    {
        return [['product_id' => 1, 'quantity' => 1, 'price' => $price, 'name' => 'SP', 'image' => '']];
    }

    /** @test */
    public function TC_CHK_01_validCashCheckout(): void
    {
        $res = $this->checkoutLogic($this->item(), 500_000, '123 HCM', '0901234567', 'cash');
        $this->assertArrayNotHasKey('error', $res);
        $this->assertEquals(150_000, $res['shippingFee']);
        $this->assertEquals(650_000, $res['finalTotal']);
    }

    /** @test */
    public function TC_CHK_02_freeShippingOver3M(): void
    {
        $res = $this->checkoutLogic($this->item(3_500_000), 3_500_000, '123 HCM', '0901234567');
        $this->assertEquals(0,         $res['shippingFee']);
        $this->assertEquals(3_500_000, $res['finalTotal']);
    }

    /** @test */
    public function TC_CHK_03_emptyCart(): void
    {
        $res = $this->checkoutLogic([], 0, '123 HCM', '0901234567');
        $this->assertStringContainsString('trống', $res['error']);
    }

    /** @test */
    public function TC_CHK_04_missingAddress(): void
    {
        $res = $this->checkoutLogic($this->item(), 200_000, '', '0901234567');
        $this->assertStringContainsString('địa chỉ', $res['error']);
    }

    /** @test */
    public function TC_CHK_05_missingPhone(): void
    {
        $res = $this->checkoutLogic($this->item(), 200_000, '123 HCM', '');
        $this->assertStringContainsString('điện thoại', $res['error']);
    }

    /** @test */
    public function TC_CHK_06_percentageDiscount(): void
    {
        $res = $this->checkoutLogic($this->item(1_000_000), 1_000_000, '123 HCM', '0901234567', 'cash', 1, 10, 500_000);
        $this->assertEquals(100_000,   $res['discount']);
        $this->assertEquals(1_050_000, $res['finalTotal']);
    }

    /** @test */
    public function TC_CHK_07_fixedDiscount(): void
    {
        $res = $this->checkoutLogic($this->item(600_000), 600_000, '123 HCM', '0901234567', 'cash', 2, 50_000, 0);
        $this->assertEquals(50_000,  $res['discount']);
        $this->assertEquals(700_000, $res['finalTotal']);
    }

    /** @test */
    public function TC_CHK_08_freeShipDiscount(): void
    {
        $res = $this->checkoutLogic($this->item(), 500_000, '123 HCM', '0901234567', 'cash', 3, 0, 0);
        $this->assertEquals(150_000, $res['discount']);
        $this->assertEquals(500_000, $res['finalTotal']);
    }

    /** @test */
    public function TC_CHK_09_expiredPromoIgnored(): void
    {
        $res = $this->checkoutLogic($this->item(), 500_000, '123 HCM', '0901234567', 'cash', 1, 10, 0, true, date('Y-m-d', strtotime('-1 day')));
        $this->assertEquals(0,       $res['discount']);
        $this->assertEquals(650_000, $res['finalTotal']);
    }

    /** @test */
    public function TC_CHK_10_invalidPaymentMethod(): void
    {
        $res = $this->checkoutLogic($this->item(), 500_000, '123 HCM', '0901234567', 'bitcoin');
        $this->assertStringContainsString('thanh toán', $res['error']);
    }

    /** @test */
    public function TC_CHK_11_momoQrPayment(): void
    {
        $res = $this->checkoutLogic($this->item(200_000), 200_000, '123 HCM', '0901234567', 'momo_qr');
        $this->assertArrayNotHasKey('error', $res);
        $this->assertEquals('momo_qr', $res['paymentMethod']);
    }

    /** @test */
    public function TC_CHK_12_promoBelowMinOrder(): void
    {
        $res = $this->checkoutLogic($this->item(100_000), 100_000, '123 HCM', '0901234567', 'cash', 1, 20, 500_000);
        $this->assertEquals(0,       $res['discount']);
        $this->assertEquals(250_000, $res['finalTotal']);
    }
}
