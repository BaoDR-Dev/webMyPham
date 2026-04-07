<?php include __DIR__ . '/../shares/header.php'; ?>
<style>
    /* Google Fonts - bạn có thể thêm vào header.php nếu muốn */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap');

    :root {
        /* Màu chủ đạo mới, dựa trên #6366f1 */
        --brand-primary: #6366f1;
        --brand-primary-dark: #4f46e5;
        --brand-primary-light: #eef2ff;
        /* Màu nền rất nhạt cho card */
        --brand-accent-yellow: #facc15;
        --brand-accent-yellow-dark: #eab308;
        --text-color-dark: #1f2937;
        --text-color-muted: #6b7280;
        --border-color: #e5e7eb;

        /* Shadows for depth */
        --soft-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        /* Nhẹ hơn một chút */
        --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        /* Nhẹ hơn một chút */
        --hover-shadow: 0 14px 35px rgba(0, 0, 0, 0.18);
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
        font-weight: 700;
        font-size: 1.8rem;
        /* ĐÃ THU NHỎ: Cỡ chữ */
        margin-bottom: 20px;
        /* ĐÃ THU NHỎ: margin-bottom */
        color: var(--brand-primary);
        position: relative;
    }

    .rating-title::after {
        content: '';
        display: block;
        width: 50px;
        /* Chiều rộng gạch dưới nhỏ hơn */
        height: 3px;
        /* Chiều cao gạch dưới nhỏ hơn */
        background: var(--brand-accent-yellow);
        margin: 10px auto 0;
        /* ĐÃ THU NHỎ: margin */
        border-radius: 2px;
    }

    /* Product Section */
    .rating-product {
        display: flex;
        gap: 15px;
        /* ĐÃ THU NHỎ: gap */
        align-items: center;
        margin-bottom: 25px;
        /* ĐÃ THU NHỎ: margin-bottom */
        padding: 12px;
        /* ĐÃ THU NHỎ: padding */
        background-color: var(--brand-primary-light);
        border-radius: 12px;
        /* Bo góc nhỏ hơn */
        box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
        /* Bóng đổ nhẹ hơn */
    }

    .rating-product img {
        width: 80px;
        /* ĐÃ THU NHỎ: Kích thước ảnh */
        height: 80px;
        object-fit: cover;
        border-radius: 12px;
        /* Bo góc nhỏ hơn */
        border: 1px solid var(--border-color);
        box-shadow: 0 3px 12px rgba(0, 0, 0, .08);
    }

    .rating-product-info h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
        /* ĐÃ THU NHỎ: Cỡ chữ */
        color: var(--text-color-dark);
    }

    .rating-product-info .price {
        color: #ef4444;
        font-weight: 700;
        font-size: 1rem;
        /* ĐÃ THU NHỎ: Cỡ chữ */
        margin-top: 5px;
        /* ĐÃ THU NHỎ: margin-top */
    }

    /* Stars Rating */
    .stars-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        margin: 20px 0 25px;
        /* ĐÃ THU NHỎ: margin */
    }

    .stars-rating input {
        display: none;
    }

    .stars-rating label {
        font-size: 38px;
        /* ĐÃ THU NHỎ: Kích thước sao */
        color: var(--border-color);
        cursor: pointer;
        transition: color 0.3s ease, transform 0.2s ease;
        padding: 0 4px;
        /* ĐÃ THU NHỎ: padding */
    }

    .stars-rating label:hover {
        color: var(--brand-accent-yellow);
        transform: scale(1.08);
        /* Hiệu ứng nhẹ hơn */
    }

    .stars-rating label:hover~label {
        color: var(--brand-accent-yellow);
    }

    .stars-rating input:checked~label {
        color: var(--brand-accent-yellow);
    }

    /* Form Elements */
    .label-rating {
        display: block;
        font-weight: 600;
        font-size: 0.95rem;
        /* ĐÃ THU NHỎ: Cỡ chữ */
        color: var(--text-color-dark);
        margin-bottom: 8px;
        /* ĐÃ THU NHỎ: margin-bottom */
    }

    .textarea-rating {
        width: 100%;
        min-height: 100px;
        /* ĐÃ THU NHỎ: Chiều cao tối thiểu */
        border-radius: 12px;
        /* Bo góc nhỏ hơn */
        padding: 12px;
        /* ĐÃ THU NHỎ: padding */
        border: 1px solid var(--border-color);
        resize: vertical;
        outline: none;
        font-size: 0.95rem;
        /* ĐÃ THU NHỎ: Cỡ chữ */
        color: var(--text-color-dark);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .textarea-rating:focus {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 2px rgba(var(--brand-primary-rgb), 0.2);
        /* Shadow nhỏ hơn */
    }

    /* Buttons */
    .button-group-rating {
        display: flex;
        gap: 12px;
        /* ĐÃ THU NHỎ: gap */
        margin-top: 25px;
        /* ĐÃ THU NHỎ: margin-top */
    }

    .btn-submit-rating,
    .btn-cancel-rating {
        flex: 1;
        padding: 12px;
        /* ĐÃ THU NHỎ: padding */
        border-radius: 12px;
        /* Bo góc nhỏ hơn */
        font-weight: 600;
        font-size: 1rem;
        /* ĐÃ THU NHỎ: Cỡ chữ */
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-submit-rating {
        background: var(--brand-primary);
        color: var(--bs-white);
        box-shadow: 0 3px 10px rgba(var(--brand-primary-rgb), 0.15);
        /* Bóng đổ nhẹ hơn */
    }

    .btn-submit-rating:hover {
        background: var(--brand-primary-dark);
        box-shadow: 0 5px 15px rgba(var(--brand-primary-rgb), 0.2);
        /* Bóng đổ nhẹ hơn */
        transform: translateY(-1px);
        /* Hiệu ứng nhẹ hơn */
    }

    .btn-cancel-rating {
        background: var(--border-color);
        color: var(--text-color-dark);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        /* Bóng đổ nhẹ hơn */
    }

    .btn-cancel-rating:hover {
        background: #dadde2;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    body.body-rating {
        background: none;
        display: block;
    }
</style>

<body class="body-rating">
    <div class="rating-page-wrapper">
        <div class="rating-card">
            <h2 class="rating-title">Chỉnh Sửa Đánh Giá</h2>

            <div class="rating-product">
                <img src="/webbanhang/<?php echo htmlspecialchars($product->image ?? 'assets/img/no-image.png'); ?>"
                    alt="<?php echo htmlspecialchars($product->name ?? 'Sản phẩm'); ?>">
                <div class="rating-product-info">
                    <h4><?php echo htmlspecialchars($product->name ?? 'Không xác định'); ?></h4>
                    <p class="price"><?php echo number_format($product->price ?? 0, 0, ',', '.'); ?> VND</p>
                </div>
            </div>

            <form action="/webbanhang/Rating/update" method="POST" class="form-rating">

                <input type="hidden" name="rating_id" value="<?= htmlspecialchars($review->id) ?>">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($review->product_id) ?>">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($review->order_id) ?>">

                <label for="review-rating-stars" class="label-rating">Đánh giá:</label>
                <div class="stars-rating" id="review-rating-stars">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input
                            type="radio"
                            id="star-<?= $i ?>"
                            name="rating"
                            value="<?= $i ?>"
                            <?= ($review->rating == $i) ? 'checked' : '' ?>>
                        <label for="star-<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>

                <label for="review-text" class="label-rating">Nhận xét:</label>
                <textarea name="review_text" id="review-text" class="textarea-rating" required><?= htmlspecialchars(trim($review->review_text)) ?></textarea>

                <div class="button-group-rating">
                    <button type="submit" class="btn-submit-rating">Cập Nhật</button>
                    <a href="/webbanhang/Order/viewOrderDetails/<?= htmlspecialchars($review->order_id) ?>"
                        class="btn-cancel-rating">Hủy</a>
                </div>

            </form>

        </div>
    </div>
    <?php include __DIR__ . '/../shares/footer.php'; ?>
</body>