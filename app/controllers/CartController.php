<?php
require_once('app/config/database.php');
require_once('app/models/CartModel.php');
require_once('app/models/ProductModel.php');

class CartController
{
    private $cartModel;
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->cartModel = new CartModel($this->db);
        $this->productModel = new ProductModel($this->db);
    }

    private function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
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



    public function index()
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            include_once 'app/views/account/login.php';
            exit;
        }
        $this->updateCartSession($account_id);
        include 'app/views/product/cart.php';
    }

    public function add($product_id, $quantity = 1)
    {
        $this->ensureSessionStarted();

        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            echo "Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.";
            return;
        }

        // Lấy thông tin sản phẩm
        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            echo "Sản phẩm không tồn tại.";
            return;
        }

        // Kiểm tra số lượng tồn kho
        if ($product->quantity < $quantity) {
            echo "Không đủ hàng trong kho. Chỉ còn {$product->quantity} sản phẩm.";
            return;
        }

        // Thêm vào CSDL
        $this->cartModel->addOrUpdateCart($account_id, $product_id, $quantity);

        // Cập nhật session giỏ hàng
        $this->updateCartSession($account_id);

        // Chuyển hướng sang trang giỏ hàng
        header('Location: /webbanhang/cart/index');
        exit;
    }

    public function addFromDetail()
    {
        $this->ensureSessionStarted();

        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            $this->sendJsonResponse(false, "Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.");
            return;
        }

        $product_id = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if ($product_id && is_numeric($quantity) && $quantity > 0) {
            $product = $this->productModel->getProductById($product_id);

            if ($product && $product->quantity >= $quantity) {
                // Cập nhật giỏ hàng trong database
                $this->cartModel->addOrUpdateCart($account_id, $product_id, $quantity);

                // Cập nhật giỏ hàng trong session
                $this->updateCartSession($account_id);

                // Tính lại tổng số lượng trong session
                $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

                // Nếu là AJAX thì trả về JSON
                if ($this->isAjaxRequest()) {
                    $this->sendJsonResponse(true, "Đã thêm vào giỏ hàng!", ['cartCount' => $cartCount]);
                    return;
                }

                // Nếu không phải AJAX thì chuyển trang
                header('Location: /webbanhang/Cart/index');
                exit;
            } else {
                $this->sendJsonResponse(false, "Sản phẩm không đủ hàng hoặc không tồn tại.");
            }
        } else {
            $this->sendJsonResponse(false, "Dữ liệu không hợp lệ.");
        }
    }

    private function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function sendJsonResponse($success, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(array_merge([
            'success' => $success,
            'message' => $message
        ], $data));
        exit;
    }


    public function addFromShow($product_id)
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;

        if (!$account_id) {
            echo "Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.";
            return;
        }

        // Lấy số lượng từ form
        $quantity = $_POST['quantity'] ?? 1;

        // Lấy thông tin sản phẩm
        $product = $this->productModel->getProductById($product_id);

        if ($product) {
            // Kiểm tra số lượng trong kho
            if ($product->quantity < $quantity) {
                echo "Không đủ hàng trong kho. Chỉ còn {$product->quantity} sản phẩm.";
                return;
            }

            // Thêm sản phẩm vào giỏ hàng
            $this->cartModel->addOrUpdateCart2($account_id, $product_id, $quantity);
            $this->updateCartSession($account_id);
            header('Location: /webbanhang/Cart/index');
            exit;
        } else {
            echo "Sản phẩm không tồn tại.";
        }
    }


    public function updateQuantity($product_id)
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            echo json_encode(['error' => 'Bạn cần đăng nhập để thực hiện thao tác này.']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quantity = (int)($_POST['quantity'] ?? 1);

            // Lấy thông tin sản phẩm từ kho
            $product = $this->productModel->getProductById($product_id);
            if ($product && $product->quantity < $quantity) {
                echo json_encode(['error' => "Không thể cập nhật. Số lượng sản phẩm tối đa trong kho là {$product->quantity}."]);
                return;
            }

            // Cập nhật giỏ hàng nếu số lượng hợp lệ
            $this->cartModel->updateCartQuantity($account_id, $product_id, $quantity);
            $this->updateCartSession($account_id);

            $updatedSubtotal = $_SESSION['cart'][$product_id]['price'] * $_SESSION['cart'][$product_id]['quantity'];
            $overallTotal = 0;
            foreach ($_SESSION['cart'] as $item) {
                if ($item['toggle']) {
                    $overallTotal += $item['price'] * $item['quantity'];
                }
            }

            echo json_encode([
                'updatedSubtotal' => $updatedSubtotal,
                'overallTotal'    => $overallTotal,
                'quantity'        => $quantity,
            ]);
            return;
        }

        header('Location: /webbanhang/Cart/index');
        exit;
    }

    public function toggle($product_id)
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;

        if (!$account_id) {
            echo json_encode(['error' => 'Bạn cần đăng nhập để thực hiện thao tác này.']);
            return;
        }

        $cartItem = $this->cartModel->getCartItem($account_id, $product_id);
        if ($cartItem) {
            $newToggle = $cartItem->toggle ? 0 : 1; // Thay đổi trạng thái toggle
            $this->cartModel->updateToggle($account_id, $product_id, $newToggle); // Cập nhật trạng thái trong database
            $this->updateCartSession($account_id); // Cập nhật lại session giỏ hàng

            $overallTotal = 0;
            foreach ($_SESSION['cart'] as $item) {
                if (isset($item['toggle']) && $item['toggle']) {
                    $overallTotal += $item['price'] * $item['quantity'];
                }
            }

            echo json_encode([
                'newToggle'    => $newToggle,
                'overallTotal' => $overallTotal,
            ]);
            return; // Không chuyển trang
        }

        echo json_encode(['error' => 'Sản phẩm không tồn tại trong giỏ hàng.']);
    }

    public function getCartQuantity()
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;

        if (!$account_id) {
            echo json_encode(['cartQuantity' => 0]);
            return;
        }

        $cartQuantity = $this->cartModel->getTotalCartQuantity($account_id);
        echo json_encode(['cartQuantity' => $cartQuantity]);
    }



    public function remove($product_id)
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            echo "Bạn cần đăng nhập để thực hiện thao tác này.";
            return;
        }

        $this->cartModel->removeItem($account_id, $product_id);
        $this->updateCartSession($account_id);
        header('Location: /webbanhang/Cart/index');
        exit;
    }

    public function clear()
    {
        $this->ensureSessionStarted();
        $account_id = $_SESSION['account_id'] ?? null;
        if (!$account_id) {
            echo "Bạn cần đăng nhập để thực hiện thao tác này.";
            return;
        }

        $this->cartModel->clearCart($account_id);
        $_SESSION['cart'] = [];
        $_SESSION['cart_quantity'] = 0;
        header('Location: /webbanhang/Cart/index');
        exit;
    }
    public function error()
    {
        $errorMessage = "Đã xảy ra lỗi khi tạo đơn hàng. Vui lòng thử lại.";
        include 'app/views/product/error.php';
    }
}
