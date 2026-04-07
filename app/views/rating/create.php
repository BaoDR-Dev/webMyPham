<?php include __DIR__ . '/../shares/header.php'; ?>
<?php include __DIR__ . '/../shares/navbar.php'; ?>
<style>
    /* Google Fonts - bạn có thể thêm vào header.php nếu muốn */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap');

    :root {
        /* Màu chủ đạo mới, dựa trên #6366f1 */
        --brand-primary: #6366f1;
        --brand-primary-rgb: 99, 102, 241;
        /* RGB cho rgba() */
        --brand-primary-dark: #4f46e5;
        --brand-primary-light: #eef2ff;
        /* Màu nền rất nhạt cho card */
        --brand-accent-yellow: #facc15;
        --brand-accent-yellow-dark: #eab308;
        --text-color-dark: #1f2937;
        --text-color-muted: #6b7280;
        --border-color: #e5e7eb;

        /* Shadows for depth */
        --soft-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        --card-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        --hover-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
        --element-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        /* Cho các element nhỏ hơn */
    }

    body {
        font-family: 'Inter', sans-serif;
        color: var(--text-color-dark);
        line-height: 1.6;
        margin: 0;
    }

    html,
    body {
        height: 100%;
    }

    body {
        display: flex;
        flex-direction: column;
    }

    .rating-page-wrapper {
        flex-grow: 1;
        min-height: 100vh;
        /* Đảm bảo wrapper chiếm toàn bộ chiều cao */
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--brand-primary-light), #fff);
        padding: 20px;
        /* Giảm padding tổng thể */
    }

    .rating-card {
        background: var(--bs-white);
        border-radius: 20px;
        /* Bo góc nhỏ hơn */
        max-width: 420px;
        /* ĐÃ THU NHỎ: max-width */
        width: 100%;
        padding: 30px;
        /* ĐÃ THU NHỎ: padding */
        box-shadow: var(--soft-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        transform: scale(0.8);
        transform-origin: top center;
    }





    .rating-title {
        text-align: center;
        font-family: 'Poppins', sans-serif;
        /* Font riêng cho tiêu đề */
        font-weight: 700;
        font-size: 2.2rem;
        /* Cỡ chữ lớn hơn */
        margin-bottom: 30px;
        /* Tăng khoảng cách */
        color: var(--brand-primary);
        /* Màu chủ đạo */
        position: relative;
    }

    .rating-title::after {
        /* Đường gạch dưới tinh tế */
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: var(--brand-accent-yellow);
        margin: 15px auto 0;
        border-radius: 2px;
    }

    /* Product Section */
    .rating-product {
        display: flex;
        gap: 20px;
        /* Khoảng cách lớn hơn */
        align-items: center;
        margin-bottom: 30px;
        padding: 15px;
        background-color: var(--brand-primary-light);
        /* Nền nhẹ nhàng cho sản phẩm */
        border-radius: 16px;
        box-shadow: var(--element-shadow);
    }

    .rating-product img {
        width: 100px;
        /* Kích thước lớn hơn */
        height: 100px;
        object-fit: cover;
        border-radius: 16px;
        /* Bo góc nhiều hơn */
        border: 1px solid var(--border-color);
        /* Viền nhẹ */
        box-shadow: 0 4px 15px rgba(0, 0, 0, .1);
        /* Bóng đổ nhẹ hơn */
    }

    .rating-product-info h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
        /* Cỡ chữ lớn hơn */
        color: var(--text-color-dark);
    }

    .rating-product-info .price {
        color: #ef4444;
        /* Giữ màu đỏ cho giá */
        font-weight: 700;
        /* Đậm hơn */
        font-size: 1.1rem;
        margin-top: 8px;
        /* Khoảng cách lớn hơn */
    }

    /* Stars Rating - ĐÃ CẢI TIẾN */
    .rating-stars {
        /* Đổi tên class từ .stars-rating để phù hợp với HTML mới */
        display: flex;
        flex-direction: row-reverse;
        /* Đảo ngược thứ tự để CSS selector hoạt động tốt */
        justify-content: center;
        margin: 25px 0 30px;
        /* Tăng khoảng cách */
    }

    .rating-stars input {
        display: none;
        /* Ẩn radio buttons */
    }

    .rating-stars label {
        font-size: 44px;
        /* Ngôi sao lớn hơn */
        color: var(--border-color);
        /* Màu xám mặc định */
        cursor: pointer;
        transition: color 0.3s ease, transform 0.2s ease;
        padding: 0 5px;
        /* Khoảng cách giữa các sao */
    }

    .rating-stars label:hover {
        color: var(--brand-accent-yellow);
        transform: scale(1.1);
        /* Hiệu ứng phình to khi hover */
    }

    .rating-stars label:hover~label {
        color: var(--brand-accent-yellow);
        /* Các sao trước đó cũng vàng */
    }

    .rating-stars input:checked~label {
        color: var(--brand-accent-yellow);
        /* Sao đã chọn và các sao trước đó vàng */
    }

    /* Form Elements */
    .form-label {
        /* Sử dụng form-label của Bootstrap thay vì label-rating */
        display: block;
        font-weight: 600;
        font-size: 1rem;
        color: var(--text-color-dark);
        margin-bottom: 10px;
        /* Khoảng cách với input */
    }

    .rating-form textarea {
        /* Đổi tên class từ textarea-rating */
        width: 100%;
        min-height: 120px;
        /* Chiều cao tối thiểu */
        border-radius: 16px;
        /* Bo góc nhiều hơn */
        padding: 15px;
        /* Tăng padding */
        border: 1px solid var(--border-color);
        resize: vertical;
        /* Chỉ cho phép resize theo chiều dọc */
        outline: none;
        font-size: 1rem;
        color: var(--text-color-dark);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .rating-form textarea:focus {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 3px rgba(var(--brand-primary-rgb), 0.2);
        /* Shadow khi focus */
    }

    .rating-actions {
        display: flex;
        gap: 14px;
        margin-top: 24px;
    }

    /* Cả 2 nút đều chiếm 1/2 */
    .rating-actions>* {
        flex: 1;
    }

    /* Nút gửi */
    .btn-submit {
        display: flex;
        justify-content: center;
        align-items: center;

        background: linear-gradient(135deg, #6b67d2, #a8a7d0ff);
        border: none;
        padding: 12px 0;
        font-size: 15px;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        color: #fff;
        box-shadow: 0 6px 16px rgba(107, 103, 210, 0.35);
        transition: all 0.25s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
    }

    /* Nút hủy */
    .btn-cancel-rating {
        display: flex;
        justify-content: center;
        align-items: center;

        background: #f1f3f5;
        color: #555;
        text-decoration: none;
        padding: 12px 0;
        border-radius: 10px;
        font-weight: 500;
        border: 1px solid #e0e0e0;
        transition: all 0.25s ease;
    }

    .btn-cancel-rating:hover {
        background: #e9ecef;
        color: #333;
    }


    /* Reset body-rating, assuming header.php handles body styling */
    body.body-rating {
        background: none;
        /* Loại bỏ background nếu rating-page-wrapper đã xử lý */
        display: block;
        /* Đảm bảo body không phải flex container nếu rating-page-wrapper đã là */
    }
</style>

<body class="body-rating">
    <div class="rating-page-wrapper">
        <div class="rating-card">
            <h2 class="rating-title">Đánh Giá Sản Phẩm</h2>

            <div class="rating-product">
                <img src="/webbanhang/<?php echo htmlspecialchars($product->image ?? 'assets/img/no-image.png'); ?>"
                    alt="<?php echo htmlspecialchars($product->name ?? 'Sản phẩm'); ?>">
                <div class="rating-product-info">
                    <h4><?php echo htmlspecialchars($product->name ?? 'Không xác định'); ?></h4>
                    <p class="price"><?php echo number_format($product->price ?? 0, 0, ',', '.'); ?> VND</p>
                </div>
            </div>

            <form action="/webbanhang/Rating/store" method="POST" class="rating-form">
                <input type="hidden" name="product_id" value="<?php echo $product->id ?? ''; ?>">
                <input type="hidden" name="order_id" value="<?php echo $order_id ?? ''; ?>">

                <label for="review-rating" class="form-label">Đánh giá của bạn</label>
                <div class="rating-stars" id="review-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input
                            type="radio"
                            id="star<?php echo $i; ?>"
                            name="rating"
                            value="<?php echo $i; ?>"
                            required>
                        <label for="star<?php echo $i; ?>">★</label>
                    <?php endfor; ?>
                </div>

                <label for="review-text-area" class="form-label">Nhận xét</label>
                <textarea name="review_text" id="review-text-area" rows="4" placeholder="Chia sẻ trải nghiệm của bạn..." required></textarea>

                <div class="rating-actions">
                    <button type="submit" class="btn-submit">Gửi đánh giá</button>
                    <a href="/webbanhang/Order/viewOrderDetails/<?= htmlspecialchars($order_id) ?>"
                        class="btn-cancel-rating">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Script cho stars rating đã được loại bỏ, CSS giờ đây xử lý toàn bộ -->
</body>
<?php include __DIR__ . '/../shares/footer.php'; ?>