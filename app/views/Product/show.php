<?php include 'app/views/shares/header.php'; ?>
<?php include __DIR__ . '/../shares/navbar.php'; ?>

<div class="product-view">
    <div class="product-container-re">

        <!-- ============================================= -->
        <!-- KHỐI THÔNG TIN SẢN PHẨM CHÍNH (BỐ CỤC 2 CỘT) -->
        <!-- ============================================= -->
        <section class="product-core-info">

            <!-- CỘT BÊN TRÁI: HÌNH ẢNH SẢN PHẨM -->
            <div class="product-gallery">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>" class="main-product-image" onerror="this.src='<?= BASE_URL ?>/public/images/no-image.png';this.onerror=null;">
                <!-- (Tùy chọn) Thêm các ảnh thumbnail ở đây nếu có -->
            </div>

            <!-- CỘT BÊN PHẢI: THÔNG TIN VÀ HÀNH ĐỘNG (ĐÃ THIẾT KẾ LẠI) -->
            <div class="product-main-details">
                <!-- KHỐI THÔNG TIN CƠ BẢN -->
                <div class="product-info-header">
                    <h1 class="product-title-main"><?= htmlspecialchars($product->name) ?></h1>
                    <div class="reviews-summary-link">
                        <span class="stars-preview">
                            <?php
                            $rating = round($product->total_rating ?? 0);
                            for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= $i <= $rating ? 'filled' : '' ?>">★</span>
                            <?php endfor; ?>
                        </span>
                        <a href="#reviews-section" class="review-count-link">(Xem <?= htmlspecialchars($product->review_count ?? 0) ?> đánh giá)</a>
                    </div>
                    <p class="product-short-description">
                        <?= htmlspecialchars_decode($product->description) ?>
                    </p>
                </div>

                <!-- KHỐI MUA HÀNG NỔI BẬT -->
                <div class="product-purchase-panel">
                    <!-- Giá sản phẩm -->
                    <div class="panel-price-section">
                        <label class="price-label">Giá bán:</label>
                        <span class="final-price"><?= number_format($product->price, 0, ',', '.') ?> VND</span>
                    </div>

                    <!-- Chọn số lượng -->
                    <div class="panel-quantity-section">
                        <label class="quantity-label">Số lượng:</label>
                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn-re" id="decrease">-</button>
                            <input type="text" id="quantity" name="quantity" value="1" readonly>
                            <button type="button" class="quantity-btn-re" id="increase">+</button>
                        </div>
                    </div>

                    <!-- Các nút hành động -->
                    <div class="panel-buttons-section">
                        <form id="add-to-cart-form" method="POST" action="<?= BASE_URL ?>/Cart/addFromDetail">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product->id) ?>">
                            <input type="hidden" name="quantity" id="cart-quantity">
                            <button type="submit" class="btn-re btn-add-to-cart">Thêm vào giỏ hàng</button>
                        </form>
                        <form action="<?= BASE_URL ?>/Order/checkout" method="POST">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product->id) ?>">
                            <input type="hidden" name="quantity" id="buy-now-quantity">
                            <button type="submit" class="btn-re btn-buy-now">Mua Ngay</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================= -->
        <!--    KHỐI THÔNG TIN MỞ RỘNG (DẠNG TABS)        -->
        <!-- ============================================= -->
        <section class="product-extended-info">
            <!-- Thanh điều hướng tabs -->
            <nav class="tab-nav">
                <button class="tab-link active" data-tab="description-tab">Thông Tin Chi Tiết</button>
                <button class="tab-link" data-tab="reviews-tab">Đánh Giá Sản Phẩm</button>
                <button class="tab-link" data-tab="similar-tab">Sản Phẩm Tương Tự</button>
            </nav>

            <!-- Nội dung các tabs -->
            <div class="tab-content-wrapper">

                <!-- Tab 1: Mô tả chi tiết -->
                <div id="description-tab" class="tab-pane active">
                    <div class="description-content">
                        <?= !empty($product->full_description) ? htmlspecialchars_decode($product->full_description) : '<p>Không có mô tả chi tiết cho sản phẩm này.</p>' ?>
                    </div>
                </div>

                <!-- Tab 2: Đánh giá -->
                <div id="reviews-tab" class="tab-pane">
                    <div class="review-summary-filter">
                        <div class="review-summary">
                            <p><strong>Đánh Giá TB:</strong> <?= number_format($product->total_rating ?? 0, 1) ?>/5 <span class="saosao-re">★</span></p>
                            <p><strong>Tổng số:</strong> <?= htmlspecialchars($product->review_count ?? 0) ?> đánh giá</p>
                        </div>
                        <div class="filter-form">
                            <label for="rating-filter">Lọc theo:</label>
                            <select id="rating-filter">
                                <option value="">Tất cả</option>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?> sao</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div id="review-list-container" class="review-container">
                        <?php if (!empty($reviews)): ?>
                            <div class="review-list">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <span class="reviewer-avatar">👤</span>
                                                <span class="reviewer-name"><?= htmlspecialchars($review['account_name'] ?? 'Người dùng') ?></span>
                                            </div>
                                            <div class="review-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="star <?= $i <= $review['rating'] ? 'active' : '' ?>">★</span>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <p class="review-date"><?= htmlspecialchars($review['created_at']) ?></p>
                                        <p class="review-text"><?= htmlspecialchars($review['review_text'] ?? 'Không có nhận xét.') ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-review">Chưa có đánh giá nào cho sản phẩm này.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tab 3: Sản phẩm tương tự -->
                <div id="similar-tab" class="tab-pane">
                    <div class="related-products-list">
                        <?php foreach ($similarProducts as $similar): ?>
                            <div class="related-product-card">
                                <a href="<?= BASE_URL ?>/Product/view/<?= htmlspecialchars($similar->id) ?>?category_id=<?= htmlspecialchars($similar->category_id) ?>" class="product-link">
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($similar->image) ?>" class="related-product-img" alt="<?= htmlspecialchars($similar->name) ?>" onerror="this.src='<?= BASE_URL ?>/public/images/no-image.png';this.onerror=null;">
                                    <div class="related-product-body">
                                        <h5 class="related-product-title"><?= htmlspecialchars($similar->name, ENT_QUOTES, 'UTF-8'); ?></h5>
                                        <p class="related-product-price"><?= number_format($similar->price, 0, ',', '.'); ?> VND</p>
                                    </div>
                                </a>
                                <a href="<?= BASE_URL ?>/Cart/add/<?= htmlspecialchars($similar->id) ?>" class="btn-re btn-quick-add">Thêm vào giỏ</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($similarProducts)): ?>
                        <div class="see-all-container">
                            <a href="<?= BASE_URL ?>/Product/categoryList/<?= htmlspecialchars($similarProducts[0]->category_id) ?>" class="btn-re btn-see-all">
                                Xem tất cả sản phẩm cùng danh mục &rarr;
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>


<?php include 'app/views/shares/footer.php'; ?>

<script>
    document.getElementById('rating-filter').addEventListener('change', function() {
        const rating = this.value;
        const productId = <?= json_encode($product->id) ?>;

        fetch(`<?= BASE_URL ?>/Product/reviewFilter?product_id=${productId}&rating=${rating}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('review-list-container').innerHTML = html;
            })
            .catch(error => {
                console.error('Lỗi khi lọc đánh giá:', error);
            });
    });
    // Xử lý logic cho Tabs
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.product-view .tab-link');
        const tabPanes = document.querySelectorAll('.product-view .tab-pane');

        tabLinks.forEach(link => {
            link.addEventListener('click', () => {
                const tabId = link.getAttribute('data-tab');

                // Xóa active class khỏi tất cả các link và pane
                tabLinks.forEach(item => item.classList.remove('active'));
                tabPanes.forEach(item => item.classList.remove('active'));

                // Thêm active class cho link và pane được click
                link.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('#add-to-cart-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // ✅ Cập nhật số lượng ở biểu tượng giỏ hàng (nếu cần)
                            const badge = document.querySelector('.bi-cart + .badge, .bi-cart ~ .badge');
                            if (badge) {
                                badge.textContent = data.cartCount;
                            }

                            // ✅ Chuyển sang trang giỏ hàng
                            window.location.href = '<?= BASE_URL ?>/cart/index';
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(() => {
                        alert("Đã xảy ra lỗi khi thêm vào giỏ hàng.");
                    });
            });
        }
    });
</script>