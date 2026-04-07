<?php include 'app/views/shares/header.php'; ?>
<?php include 'app/views/shares/navbar.php'; ?>

<style>
    /* ----- GOOGLE FONT & BIẾN MÀU ----- */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    :root {
        --primary-color: #007bff;
        --primary-hover: #0056b3;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --light-gray: #f8f9fa;
        --border-color: #dee2e6;
        --text-color: #343a40;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--light-gray);
    }

    /* ----- LAYOUT CHÍNH ----- */
    .cart-container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 15px;
    }

    .cart-title {
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 30px;
    }

    .cart-table {
        width: 100%;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .cart-table thead {
        background-color: var(--primary-color);
        color: #fff;
        font-weight: 600;
    }

    .cart-table th,
    .cart-table td {
        padding: 15px;
        vertical-align: middle;
        text-align: center;
    }

    .cart-table tbody tr {
        border-bottom: 1px solid var(--border-color);
    }

    .cart-table tbody tr:last-child {
        border-bottom: none;
    }

    /* ----- THÔNG TIN SẢN PHẨM ----- */
    .product-info {
        display: flex;
        align-items: center;
        text-align: left;
    }

    .product-info img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 15px;
    }

    .product-name {
        font-weight: 600;
        color: var(--text-color);
    }

    /* ----- NÚT TOGGLE CHECKBOX ----- */
    .toggle-checkbox {
        appearance: none;
        width: 22px;
        height: 22px;
        border: 2px solid var(--success-color);
        border-radius: 4px;
        cursor: pointer;
        position: relative;
        transition: background-color 0.2s;
    }

    .toggle-checkbox:checked {
        background-color: var(--success-color);
    }

    .toggle-checkbox:checked::after {
        content: '✓';
        color: white;
        font-size: 16px;
        font-weight: bold;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* ----- NÚT TĂNG GIẢM SỐ LƯỢNG ----- */
    .quantity-controls {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-btn {
        width: 30px;
        height: 30px;
        border: 1px solid var(--border-color);
        background-color: #fff;
        cursor: pointer;
        font-weight: bold;
    }

    .quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity-display {
        width: 50px;
        height: 30px;
        text-align: center;
        border: 1px solid var(--border-color);
        border-left: none;
        border-right: none;
        font-weight: 500;
    }

    /* ----- NÚT XÓA ----- */
    .remove-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
    }

    .remove-btn svg {
        width: 20px;
        height: 20px;
        fill: var(--danger-color);
        transition: fill 0.2s;
    }

    .remove-btn:hover svg {
        fill: #a71d2a;
    }

    /* ----- PHẦN TỔNG KẾT ----- */
    .cart-summary {
        margin-top: 30px;
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        text-align: right;
    }

    .total-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .summary-actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }

    /* ----- GIỎ HÀNG TRỐNG ----- */
    .cart-empty {
        text-align: center;
        padding: 50px;
        background: #fff;
        border-radius: 8px;
    }

    .cart-empty h3 {
        margin-bottom: 20px;
    }

    /* ----- RESPONSIVE CHO DI ĐỘNG ----- */
    @media (max-width: 768px) {
        .cart-table thead {
            display: none;
            /* Ẩn header của bảng */
        }

        .cart-table,
        .cart-table tbody,
        .cart-table tr,
        .cart-table td {
            display: block;
            /* Chuyển thành dạng block */
            width: 100%;
        }

        .cart-table tr {
            padding: 15px 0;
        }

        .cart-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border: none;
            border-bottom: 1px solid var(--border-color);
        }

        .cart-table td::before {
            content: attr(data-label);
            /* Lấy nội dung từ data-label */
            font-weight: 600;
            color: var(--text-color);
        }

        .cart-table .product-info-cell {
            /* Hiển thị sản phẩm ở đầu */
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
        }

        .cart-table .product-info {
            width: 100%;
        }

        .summary-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .summary-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="cart-container">
    <h1 class="text-center cart-title">🛒 Giỏ hàng của bạn</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="cart-empty">
            <h3>Giỏ hàng của bạn đang trống!</h3>
            <p>Hãy khám phá thêm các sản phẩm tuyệt vời của chúng tôi.</p>
            <a href="/webbanhang/product/home" class=" btn btn-primary btn-lg">Bắt đầu mua sắm</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Chọn</th>
                        <th style="width: 35%; text-align: left;">Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $id => $item):
                        $subtotal = $item['price'] * $item['quantity'];
                        if ($item['toggle']) {
                            $total += $subtotal;
                        }
                    ?>
                        <tr data-product-id="<?php echo $id; ?>">
                            <td data-label="Chọn">
                                <input type="checkbox" class="toggle-checkbox"
                                    data-url="/webbanhang/Cart/toggle/<?php echo $id; ?>"
                                    <?php echo $item['toggle'] ? 'checked' : ''; ?>
                                    title="<?php echo $item['toggle'] ? 'Đã chọn' : 'Chọn sản phẩm này'; ?>">
                            </td>
                            <td class="product-info-cell" data-label="Sản phẩm">
                                <div class="product-info">
                                    <img src="/webbanhang/<?php echo htmlspecialchars($item['image']); ?>" alt="Ảnh sản phẩm" onerror="this.src='/webbanhang/public/images/no-image.png';this.onerror=null;">
                                    <div>
                                        <div class="product-name"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Giá"><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</td>
                            <td data-label="Số lượng">
                                <div class="quantity-controls">
                                    <button class="quantity-btn quantity-decrease" data-product-id="<?php echo $id; ?>">-</button>
                                    <input type="text" class="quantity-display"
                                        value="<?php echo $item['quantity']; ?>"
                                        data-max="<?php echo isset($item['max_quantity']) ? $item['max_quantity'] : 0; ?>"
                                        readonly>
                                    <button class="quantity-btn quantity-increase" data-product-id="<?php echo $id; ?>">+</button>
                                </div>
                            </td>
                            <td data-label="Thành tiền" class="subtotal-cell">
                                <strong><?php echo number_format($subtotal, 0, ',', '.'); ?> VND</strong>
                            </td>
                            <td data-label="Hành động">
                                <a href="/webbanhang/Cart/remove/<?php echo $id; ?>" class="remove-btn" title="Xóa sản phẩm">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="cart-summary">
            <h4><strong>Tổng thanh toán: <span id="overallTotal" class="total-price"><?php echo number_format($total, 0, ',', '.'); ?> VND</span></strong></h4>
            <div class="summary-actions">
                <a href="/webbanhang/product/home" class="btn btn-secondary btn-lg">Tiếp tục mua sắm</a>
                <a href="/webbanhang/Order/checkout" class="btn btn-success btn-lg">Thanh toán ngay 💳</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- CÁC HÀM CẬP NHẬT GIAO DIỆN ---
        function updateRowUI(productId, data) {
            let row = document.querySelector(`tr[data-product-id="${productId}"]`);
            if (!row) return;

            let input = row.querySelector('.quantity-display');
            input.value = data.newQuantity !== undefined ? data.newQuantity : input.value;

            if (data.updatedSubtotal !== undefined) {
                row.querySelector('.subtotal-cell').innerHTML = '<strong>' + data.updatedSubtotal.toLocaleString('vi-VN') + ' VND</strong>';
            }

            if (data.overallTotal !== undefined) {
                document.getElementById('overallTotal').textContent = data.overallTotal.toLocaleString('vi-VN') + ' VND';
            }

            updateQuantityButtonsState(row);
            updateCartCount();
        }

        function updateQuantityButtonsState(row) {
            let input = row.querySelector('.quantity-display');
            let decreaseButton = row.querySelector('.quantity-decrease');
            let increaseButton = row.querySelector('.quantity-increase');
            let quantity = parseInt(input.value, 10);
            let maxQuantity = parseInt(input.dataset.max, 10);

            decreaseButton.disabled = (quantity <= 1);
            increaseButton.disabled = (quantity >= maxQuantity);
        }

        function updateCartCount() {
            fetch('/webbanhang/Cart/getCartQuantity', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    let cartCountEl = document.getElementById('cart-count');
                    if (cartCountEl) cartCountEl.textContent = data.cartQuantity;
                })
                .catch(error => console.error('Lỗi khi lấy số lượng giỏ hàng:', error));
        }

        // --- XỬ LÝ SỰ KIỆN ---
        document.querySelectorAll('.quantity-decrease, .quantity-increase').forEach(button => {
            button.addEventListener('click', function() {
                let productId = this.dataset.productId;
                let row = document.querySelector(`tr[data-product-id="${productId}"]`);
                let input = row.querySelector('.quantity-display');
                let currentQuantity = parseInt(input.value, 10);
                let maxQuantity = parseInt(input.dataset.max, 10);
                let newQuantity = currentQuantity;

                if (this.classList.contains('quantity-increase')) {
                    if (currentQuantity < maxQuantity) newQuantity++;
                    else alert(`Số lượng tối đa cho sản phẩm này là ${maxQuantity}`);
                } else {
                    if (currentQuantity > 1) newQuantity--;
                }

                if (newQuantity !== currentQuantity) {
                    let formData = new FormData();
                    formData.append('quantity', newQuantity);

                    fetch(`/webbanhang/Cart/updateQuantity/${productId}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.error) alert(data.error);
                            else updateRowUI(productId, {
                                ...data,
                                newQuantity: newQuantity
                            });
                        })
                        .catch(err => console.error('Lỗi khi cập nhật số lượng:', err));
                }
            });
        });

        // Cập nhật sự kiện cho checkbox toggle
        document.querySelectorAll('.toggle-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                let url = this.dataset.url;
                fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            // Hoàn lại trạng thái checkbox nếu có lỗi
                            this.checked = !this.checked;
                            return;
                        }
                        this.title = this.checked ? 'Đã chọn' : 'Chọn sản phẩm này';
                        document.getElementById('overallTotal').textContent = data.overallTotal.toLocaleString('vi-VN') + ' VND';
                    })
                    .catch(err => {
                        console.error('Lỗi khi toggle:', err);
                        this.checked = !this.checked;
                    });
            });
        });

        // Cập nhật trạng thái các nút +/- khi tải trang
        document.querySelectorAll('tr[data-product-id]').forEach(updateQuantityButtonsState);
    });
</script>

<?php include 'app/views/shares/footer.php'; ?>