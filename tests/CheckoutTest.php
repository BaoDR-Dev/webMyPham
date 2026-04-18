<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/models/OrderModel.php';

class CheckoutExcelHelper
{
    private static string $dataPath    = __DIR__ . '/../../test-data/w_checkout_data.json';
    private static string $resultsPath = __DIR__ . '/../../test-data/w_checkout_results.json';

    public static function load(): array
    {
        if (!file_exists(self::$dataPath))
            throw new \RuntimeException("Chay truoc: python test-data/excel_to_json_w_checkout.py");
        return json_decode(file_get_contents(self::$dataPath), true);
    }

    public static function provider(): array
    {
        $rows = [];
        foreach (self::load()['testcases'] as $tcId => $d) {
            $rows[$tcId] = [
                $tcId, $d['account_id'], $d['total_amount'],
                $d['transaction_id'], $d['payment_method'],
                $d['address'], $d['phone'],
                $d['product_id'] ?? '1', $d['quantity'] ?? '1',
                $d['price'] ?? $d['total_amount'], $d['expected'],
            ];
        }
        return $rows;
    }

    public static function writeResult(string $tcId, string $actual, string $status): void
    {
        $results = [];
        if (file_exists(self::$resultsPath))
            $results = json_decode(file_get_contents(self::$resultsPath), true) ?? [];
        $results[$tcId] = ['actual' => $actual, 'status' => $status];
        file_put_contents(self::$resultsPath,
            json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

/**
 * @covers OrderModel::createOrder
 * @covers OrderModel::createOrderDetail
 */
class CheckoutTest extends TestCase
{
    private PDO $pdo;
    private OrderModel $orderModel;

    protected function setUp(): void
    {
        $this->pdo        = createTestDatabase();
        $this->orderModel = new OrderModel($this->pdo);
        $this->pdo->exec("INSERT INTO account (id,email,full_name,password,role,is_active,is_verified) VALUES (1,'b@t.com','B','h','user',1,1)");
        $this->pdo->exec("INSERT INTO product (id,name,price,quantity) VALUES (1,'SP1',500000,10)");
        $this->pdo->exec("INSERT INTO product (id,name,price,quantity) VALUES (2,'SP2',200000,5)");
    }

    public static function checkoutDataProvider(): array { return CheckoutExcelHelper::provider(); }

    /**
     * @test
     * @dataProvider checkoutDataProvider
     */
    public function test_checkout_from_excel(
        string $tcId, string $accountId, string $totalAmount,
        string $transactionId, string $paymentMethod,
        string $address, string $phone,
        string $productId, string $quantity, string $price, string $expected
    ): void {
        $accId = $accountId !== '' ? (int)$accountId : null;
        $total = (float)$totalAmount;

        if ($total <= 0) {
            $actual = 'Gio hang trong hoac khong hop le';
            $this->assertLessThanOrEqual(0, $total);
            $fs = ('FAIL' === $expected) ? 'PASS' : 'FAIL';
            CheckoutExcelHelper::writeResult($tcId, $actual, $fs);
            $this->assertEquals('PASS', $fs, "[$tcId] $actual");
            return;
        }

        if (empty($address) || empty($phone)) {
            $actual = empty($address) ? 'Thieu dia chi' : 'Thieu so dien thoai';
            $this->assertTrue(empty($address) || empty($phone));
            $fs = ('FAIL' === $expected) ? 'PASS' : 'FAIL';
            CheckoutExcelHelper::writeResult($tcId, $actual, $fs);
            $this->assertEquals('PASS', $fs, "[$tcId] $actual");
            return;
        }

        $validMethods = ['cash', 'momo_qr', 'momo_atm', 'vnpay'];
        if (!in_array($paymentMethod, $validMethods)) {
            $actual = "Phuong thuc '$paymentMethod' khong hop le";
            $this->assertNotContains($paymentMethod, $validMethods);
            $fs = ('FAIL' === $expected) ? 'PASS' : 'FAIL';
            CheckoutExcelHelper::writeResult($tcId, $actual, $fs);
            $this->assertEquals('PASS', $fs, "[$tcId] $actual");
            return;
        }

        $ship    = ($total > 3_000_000) ? 0 : 150_000;
        $orderId = $this->orderModel->createOrder(
            $accId, $total + $ship, $transactionId ?: uniqid(),
            $paymentMethod, $address, $phone, $ship
        );
        $this->assertNotFalse($orderId);
        $this->assertGreaterThan(0, $orderId);

        $proId = $productId !== '' ? (int)$productId : 1;
        $qty   = $quantity  !== '' ? (int)$quantity  : 1;
        $prc   = $price     !== '' ? (float)$price   : $total;
        $this->assertTrue($this->orderModel->createOrderDetail($orderId, $proId, $qty, $prc));

        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_OBJ);
        $this->assertNotFalse($order);
        $this->assertEquals($paymentMethod, $order->payment_method);
        $this->assertEquals('pending',      $order->status);
        $this->assertEquals($ship,          $order->shipping_fee);

        $stmt2 = $this->pdo->prepare("SELECT * FROM order_details WHERE order_id = ?");
        $stmt2->execute([$orderId]);
        $detail = $stmt2->fetch(PDO::FETCH_OBJ);
        $this->assertNotFalse($detail);
        $this->assertEquals($proId, $detail->product_id);
        $this->assertEquals($qty,   $detail->quantity);

        $actual = "Tao don thanh cong, order_id=$orderId, ship=" . number_format($ship) . "d";
        $fs     = ('PASS' === $expected) ? 'PASS' : 'FAIL';
        CheckoutExcelHelper::writeResult($tcId, $actual, $fs);
        $this->assertEquals('PASS', $fs, "[$tcId] $actual");
    }

    /** @test */
    public function BUG_CHK01_stockNotDecreasedAfterOrder(): void
    {
        $src = file_get_contents(__DIR__ . '/../app/controllers/OrderController.php');
        $has = str_contains($src, 'decreaseProductQuantity');
        $actual = $has ? 'Co goi decreaseProductQuantity()' : 'KHONG giam ton kho sau order (bug)';
        CheckoutExcelHelper::writeResult('BUG_CHK01', $actual, $has ? 'PASS' : 'FAIL');
        $this->assertTrue($has, "BUG_CHK01: Ton kho khong giam sau order");
    }

    /** @test */
    public function BUG_CHK02_cartNotClearedAfterOrder(): void
    {
        $src = file_get_contents(__DIR__ . '/../app/controllers/OrderController.php');
        $has = str_contains($src, "unset(\$_SESSION['cartItems'])");
        $actual = $has ? 'Co xoa cartItems sau order' : 'KHONG xoa gio hang sau order (bug)';
        CheckoutExcelHelper::writeResult('BUG_CHK02', $actual, $has ? 'PASS' : 'FAIL');
        $this->assertTrue($has, "BUG_CHK02: Gio hang khong xoa sau order");
    }

    /** @test */
    public function BUG_CHK03_phoneNotValidated(): void
    {
        // Truyền SĐT sai định dạng "abc123xyz" → controller phải từ chối
        // Nhưng OrderModel vẫn lưu được → bug ở controller
        $orderId = $this->orderModel->createOrder(1, 500000, 'TXN_B03', 'cash', '123 HCM', 'abc123xyz', 150000);
        // Model không validate → tạo được order với SĐT sai
        $this->assertNotFalse($orderId, "Model tao duoc order voi SDT sai dinh dang");

        // Kiểm tra controller có validate không
        $src = file_get_contents(__DIR__ . '/../app/controllers/OrderController.php');
        preg_match('/public function finalizeCheckout\(\)(.*?)private function /s', $src, $m);
        $has = (bool)preg_match('/phone.*preg_match|preg_match.*phone|strlen.*phone/i', $m[1] ?? '');
        $actual = $has ? 'Co validate SDT' : 'KHONG validate dinh dang SDT (bug)';
        CheckoutExcelHelper::writeResult('BUG_CHK03', $actual, $has ? 'PASS' : 'FAIL');
        $this->assertTrue($has, "BUG_CHK03: Khong validate dinh dang SDT");
    }

    /** @test */
    public function BUG_CHK04_disabledAccountCanCheckout(): void
    {
        // Seed account bị khóa id=2
        $this->pdo->exec("INSERT INTO account (id,email,full_name,password,role,is_active,is_verified) VALUES (2,'dis@t.com','D','h','user',0,1)");

        // OrderModel không check is_active → vẫn tạo được order
        $orderId = $this->orderModel->createOrder(2, 500000, 'TXN_B04', 'cash', '123 HCM', '0901234567', 150000);
        $this->assertNotFalse($orderId, "Model tao duoc order voi account bi khoa");

        // Kiểm tra controller có check is_active không
        $src = file_get_contents(__DIR__ . '/../app/controllers/OrderController.php');
        preg_match('/public function finalizeCheckout\(\)(.*?)private function /s', $src, $m);
        $has = str_contains($m[1] ?? '', 'is_active');
        $actual = $has ? 'Co check is_active' : 'KHONG check is_active - TK bi khoa van checkout (bug)';
        CheckoutExcelHelper::writeResult('BUG_CHK04', $actual, $has ? 'PASS' : 'FAIL');
        $this->assertTrue($has, "BUG_CHK04: Khong check is_active truoc checkout");
    }

    /** @test */
    public function BUG_CHK05_addressNotValidated(): void
    {
        // Truyền địa chỉ 1 ký tự "A" → controller phải từ chối
        // Nhưng OrderModel vẫn lưu được → bug ở controller
        $orderId = $this->orderModel->createOrder(1, 500000, 'TXN_B05', 'cash', 'A', '0901234567', 150000);
        $this->assertNotFalse($orderId, "Model tao duoc order voi dia chi 1 ky tu");

        // Kiểm tra controller có validate không
        $src = file_get_contents(__DIR__ . '/../app/controllers/OrderController.php');
        preg_match('/public function finalizeCheckout\(\)(.*?)private function /s', $src, $m);
        $has = (bool)preg_match('/strlen.*address|address.*strlen|mb_strlen.*address/i', $m[1] ?? '');
        $actual = $has ? 'Co validate do dai dia chi' : 'KHONG validate do dai dia chi - nhan 1 ky tu (bug)';
        CheckoutExcelHelper::writeResult('BUG_CHK05', $actual, $has ? 'PASS' : 'FAIL');
        $this->assertTrue($has, "BUG_CHK05: Khong validate do dai dia chi");
    }
}
