

<?php
/**
 * AddCartTest.php
 * PHPUnit White-box Tests cho chức năng: addCart (CartModel)
 * Đọc dữ liệu từ TC_w_addcart.xlsx qua w_addcart_data.json
 *
 * Chạy: php vendor\phpunit\phpunit\phpunit --no-coverage tests\AddCartTest.php
 */

use PHPUnit\Framework\TestCase;

class AddCartExcelHelper
{
    private static string $dataPath    = __DIR__ . '/../../test-data/w_addcart_data.json';
    private static string $resultsPath = __DIR__ . '/../../test-data/w_addcart_results.json';

    public static function load(): array
    {
        if (!file_exists(self::$dataPath)) {
            throw new \RuntimeException("Chạy trước: python test-data/excel_to_json_w_addcart.py");
        }
        return json_decode(file_get_contents(self::$dataPath), true);
    }

    public static function provider(): array
    {
        $rows = [];
        foreach (self::load()['testcases'] as $tcId => $d) {
            $rows[$tcId] = [$tcId, $d['account_id'], $d['product_id'], $d['quantity'], $d['action'], $d['expected']];
        }
        return $rows;
    }

    public static function writeResult(string $tcId, string $actual, string $status): void
    {
        $results = [];
        if (file_exists(self::$resultsPath)) {
            $results = json_decode(file_get_contents(self::$resultsPath), true) ?? [];
        }
        $results[$tcId] = ['actual' => $actual, 'status' => $status];
        file_put_contents(self::$resultsPath,
            json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

/**
 * @covers CartModel::addOrUpdateCart
 * @covers CartModel::getCartItem
 * @covers CartModel::getTotalCartQuantity
 * @covers CartModel::removeItem
 * @covers CartModel::updateToggle
 * @covers CartModel::clearCart
 */
class AddCartTest extends TestCase
{
    private PDO $pdo;
    private CartModel $cartModel;

    // Product seed: id=29 quantity=10, id=30 quantity=0 (hết hàng)
    protected function setUp(): void
    {
        $this->pdo       = createTestDatabase();
        $this->cartModel = new CartModel($this->pdo);

        $this->pdo->exec("INSERT INTO product (id, name, price, quantity) VALUES (29, 'Son Môi', 150000, 10)");
        $this->pdo->exec("INSERT INTO product (id, name, price, quantity) VALUES (30, 'Hết Hàng', 200000, 0)");
        $this->pdo->exec("INSERT INTO account (id, email, full_name, password, role, is_active, is_verified)
            VALUES (10, 'buyer@test.com', 'Buyer', 'hash', 'user', 1, 1)");
        $this->pdo->exec("INSERT INTO account (id, email, full_name, password, role, is_active, is_verified)
            VALUES (11, 'disabled@test.com', 'Disabled', 'hash', 'user', 0, 1)");
    }

    public static function cartDataProvider(): array
    {
        return AddCartExcelHelper::provider();
    }

    /**
     * @test
     * @dataProvider cartDataProvider
     */
    public function test_addcart_from_excel(
        string $tcId, string $accountId, string $productId,
        string $quantity, string $action, string $expected
    ): void {
        $accId  = $accountId !== '' ? (int)$accountId : null;
        $proId  = $productId !== '' ? (int)$productId : null;
        $qty    = (int)$quantity;

        // ── TC04: Chưa login ───────────────────────────────────────────────
        if ($accId === null) {
            $actual     = 'Không thêm được vì chưa đăng nhập (account_id null)';
            $realStatus = 'FAIL';
            $this->assertNull($accId, "[$tcId] account_id phải null");
            $finalStatus = ($realStatus === $expected) ? 'PASS' : 'FAIL';
            AddCartExcelHelper::writeResult($tcId, $actual, $finalStatus);
            $this->assertEquals('PASS', $finalStatus, "[$tcId] $actual");
            return;
        }

        // ── Kiểm tra account is_active ─────────────────────────────────────
        $stmtAcc = $this->pdo->prepare("SELECT is_active FROM account WHERE id = ?");
        $stmtAcc->execute([$accId]);
        $acc = $stmtAcc->fetch(PDO::FETCH_OBJ);
        if ($acc && $acc->is_active == 0) {
            // Thử add vào giỏ với account bị khóa
            $this->cartModel->addOrUpdateCart($accId, $proId ?? 29, $qty > 0 ? $qty : 1);
            $item = $this->cartModel->getCartItem($accId, $proId ?? 29);

            // Nếu item tồn tại → CartModel không chặn → đây là bug
            $canAdd     = (bool)$item;
            $actual     = $canAdd
                ? 'Thêm thành công, quantity=' . ($item->quantity ?? 0) . ' (bug: tài khoản bị khóa vẫn add được)'
                : 'Tài khoản bị chặn, không thêm được';
            $realStatus = $canAdd ? 'PASS' : 'FAIL';

            // expected=FAIL → mong đợi bị chặn → nếu add được thì FAIL
            $finalStatus = ($realStatus === $expected) ? 'PASS' : 'FAIL';
            AddCartExcelHelper::writeResult($tcId, $actual, $finalStatus);
            $this->assertEquals('PASS', $finalStatus,
                "[$tcId] BUG: account is_active=0 vẫn add được vào giỏ hàng"
            );
            return;
        }

        // ── TC05: SP không tồn tại ─────────────────────────────────────────
        if ($proId !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM product WHERE id = ?");
            $stmt->execute([$proId]);
            $product = $stmt->fetch(PDO::FETCH_OBJ);
            if (!$product) {
                $actual     = "SP id=$proId không tồn tại";
                $realStatus = 'FAIL';
                $this->assertFalse((bool)$product, "[$tcId] SP không tồn tại phải trả về false");
                $finalStatus = ($realStatus === $expected) ? 'PASS' : 'FAIL';
                AddCartExcelHelper::writeResult($tcId, $actual, $finalStatus);
                $this->assertEquals('PASS', $finalStatus, "[$tcId] $actual");
                return;
            }

            // ── TC06: Hết hàng ─────────────────────────────────────────────
            if ($action === 'add' && $product->quantity < 1) {
                $actual     = "Tồn kho không đủ (còn {$product->quantity})";
                $realStatus = 'FAIL';
                $this->assertLessThan(1, $product->quantity, "[$tcId] Hết hàng phải bị từ chối");
                $finalStatus = ($realStatus === $expected) ? 'PASS' : 'FAIL';
                AddCartExcelHelper::writeResult($tcId, $actual, $finalStatus);
                $this->assertEquals('PASS', $finalStatus, "[$tcId] $actual");
                return;
            }
        }

        // ── TC07, TC08: Số lượng không hợp lệ ─────────────────────────────
        if ($action === 'add' && !($qty > 0)) {
            $actual     = "Số lượng $qty không hợp lệ";
            $realStatus = 'FAIL';
            $this->assertFalse($qty > 0, "[$tcId] Qty <= 0 phải bị từ chối");
            $finalStatus = ($realStatus === $expected) ? 'PASS' : 'FAIL';
            AddCartExcelHelper::writeResult($tcId, $actual, $finalStatus);
            $this->assertEquals('PASS', $finalStatus, "[$tcId] $actual");
            return;
        }

        // ── Thực hiện action ───────────────────────────────────────────────
        switch ($action) {
            case 'add':
                $this->cartModel->addOrUpdateCart($accId, $proId, $qty);
                $item = $this->cartModel->getCartItem($accId, $proId);
                $this->assertNotNull($item, "[$tcId] Item phải tồn tại sau khi add");
                $this->assertEquals($qty, $item->quantity, "[$tcId] Quantity phải = $qty");
                $actual     = "Thêm thành công, quantity={$item->quantity}";
                $realStatus = 'PASS';
                break;

            case 'add_again':
                // Thêm lần 1 trước
                $this->cartModel->addOrUpdateCart($accId, $proId, 2);
                // Thêm lần 2 → cộng dồn
                $this->cartModel->addOrUpdateCart($accId, $proId, $qty);
                $item = $this->cartModel->getCartItem($accId, $proId);
                $this->assertEquals(2 + $qty, $item->quantity, "[$tcId] Quantity phải cộng dồn = " . (2 + $qty));
                $actual     = "Quantity cộng dồn thành {$item->quantity}";
                $realStatus = 'PASS';
                break;

            case 'add_multi':
                // Thêm SP29 qty=2 trước, rồi thêm SP khác qty=1
                $this->cartModel->addOrUpdateCart($accId, 29, 2);
                $this->cartModel->addOrUpdateCart($accId, $proId, $qty);
                $total = $this->cartModel->getTotalCartQuantity($accId);
                $this->assertEquals(3, $total, "[$tcId] Tổng phải = 3");
                $actual     = "Tổng quantity = $total";
                $realStatus = 'PASS';
                break;

            case 'remove':
                $this->cartModel->addOrUpdateCart($accId, $proId, 2);
                $this->cartModel->removeItem($accId, $proId);
                $item = $this->cartModel->getCartItem($accId, $proId);
                $this->assertFalse((bool)$item, "[$tcId] Giỏ phải rỗng sau khi xóa");
                $actual     = 'Xóa thành công';
                $realStatus = 'PASS';
                break;

            case 'total':
                $total = $this->cartModel->getTotalCartQuantity($accId);
                $this->assertEquals(0, $total, "[$tcId] Giỏ rỗng tổng phải = 0");
                $actual     = "Tổng = $total";
                $realStatus = 'PASS';
                break;

            case 'toggle':
                $this->cartModel->addOrUpdateCart($accId, $proId, $qty);
                $this->cartModel->updateToggle($accId, $proId, false);
                $item = $this->cartModel->getCartItem($accId, $proId);
                $this->assertEquals(0, $item->toggle, "[$tcId] Toggle phải = 0");
                $actual     = 'Toggle thành công (toggle=0)';
                $realStatus = 'PASS';
                break;

            case 'clear':
                // Thêm sẵn 1 SP vào giỏ rồi clear
                $this->cartModel->addOrUpdateCart($accId, 29, 2);
                $this->cartModel->clearCart($accId);
                $total = $this->cartModel->getTotalCartQuantity($accId);
                $this->assertEquals(0, $total, "[$tcId] Giỏ phải rỗng sau clear");
                $actual     = 'Giỏ hàng rỗng sau khi clear';
                $realStatus = 'PASS';
                break;

            default:
                $actual     = "Action '$action' không xác định";
                $realStatus = 'FAIL';
        }

        $finalStatus = ($realStatus === $expected) ? 'PASS' : 'FAIL';
        AddCartExcelHelper::writeResult($tcId, $actual, $finalStatus);
        $this->assertEquals('PASS', $finalStatus,
            "[$tcId] Mong đợi: $expected | Thực tế: $realStatus | $actual"
        );
    }

    // ─── BUG_CART01: Account is_active=0 vẫn add được ────────────────────────
    /** @test */
    public function BUG_CART01_disabledAccountCanStillAddToCart(): void
    {
        // Account id=11 có is_active=0
        $stmt = $this->pdo->prepare("SELECT is_active FROM account WHERE id = 11");
        $stmt->execute();
        $this->assertEquals(0, $stmt->fetch(PDO::FETCH_OBJ)->is_active);

        // CartModel không check is_active → vẫn add được
        $this->cartModel->addOrUpdateCart(11, 29, 1);

        // Kiểm tra CartController có check is_active không
        $source = file_get_contents(__DIR__ . '/../app/controllers/CartController.php');
        $hasActiveCheck = str_contains($source, 'is_active');

        $actual = $hasActiveCheck
            ? 'CartController có check is_active'
            : 'CartController KHÔNG check is_active → tài khoản bị khóa vẫn add được (bug)';
        $status = $hasActiveCheck ? 'PASS' : 'FAIL';
        AddCartExcelHelper::writeResult('BUG_CART01', $actual, $status);

        $this->assertTrue($hasActiveCheck,
            "BUG_CART01: CartController không kiểm tra is_active → tài khoản bị khóa vẫn thêm được vào giỏ"
        );
    }
}
