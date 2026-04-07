<?php
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartCount = isset($_SESSION['cart']) && is_array($_SESSION['cart'])
    ? array_sum(array_map(fn($item) => $item['quantity'] ?? 0, $_SESSION['cart']))
    : 0;
?>

<?php include __DIR__ . '/../shares/header.php'; ?>

<main class="container mt-3"> <!-- Loại bỏ max-width: 80% ở đây -->

    <!-- Hero Section -->
    <section class="hero-section">
        <!-- Container cho slideshow, JavaScript sẽ tự động thêm ảnh vào đây -->
        <div class="slideshow-container"></div>

        <div class="services-container">
            <div class="service">
                <img src="/webbanhang/public/images/A1.png" alt="Icon thanh toán đa dạng">
                <div class="text-container">
                    <h3>Thanh Toán Linh Hoạt</h3>
                    <p>Hỗ trợ nhiều phương thức thanh toán an toàn và tiện lợi.</p>
                </div>
            </div>
            <div class="service">
                <img src="/webbanhang/public/images/A2.png" alt="Icon cam kết chính hãng">
                <div class="text-container">
                    <h3>Cam Kết Chính Hãng</h3>
                    <p>100% sản phẩm chính hãng, đảm bảo chất lượng và nguồn gốc.</p>
                </div>
            </div>
            <div class="service">
                <img src="/webbanhang/public/images/A3.png" alt="Icon giao hàng nhanh">
                <div class="text-container">
                    <h3>Giao Hàng Siêu Tốc 2H</h3>
                    <p>Dịch vụ giao nhanh trong nội thành TP. Hồ Chí Minh.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="intro text-center my-5">
        <p class="lead">Chúng tôi chuyên cung cấp sản phẩm Mỹ Phẩm chính hãng từ các thương hiệu hàng đầu thế giới. <br> Đội ngũ tư vấn tận tâm – Giao hàng nhanh – Bảo hành chính hãng.</p>
    </section>

    <!-- Featured Categories Section -->
    <section class="category-section mt-5">
        <?php if (!empty($featuredCategories)): ?>
            <section class="category-section mt-5">
                <!-- Sử dụng class mới section-heading-with-icon -->
                <div class="section-heading-with-icon">
                    <i class="bi bi-box-seam"></i>
                    <div>
                        <h2>Danh Mục Nổi Bật</h2>
                    </div>
                </div>
                <div class=" category-grid">
                    <?php foreach ($featuredCategories as $category): ?>
                        <?php if (!empty($category) && $category['id'] == 0) continue; ?>
                        <a href="/webbanhang/Product/CategoryList/<?= $category['id'] ?>" class="category-card">
                            <img
                                src="<?= !empty($category['image']) ? '/webbanhang/' . htmlspecialchars($category['image']) : '/webbanhang/public/images/A1.png' ?>"
                                class="category-image"
                                alt="<?= htmlspecialchars($category['name']) ?>"
                                onerror="this.src='/webbanhang/public/images/A1.png'">
                            <h3 class="category-name">
                                <?= htmlspecialchars($category['name']) ?>
                            </h3>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </section>
    <section class="product-section mt-5">
        <div class="section-heading-with-icon">
            <i class="bi bi-gift-fill text-danger"></i>
            <h2>Khuyến Mãi Mới Nhất</h2>
        </div>

        <div class="promo-grid-horizontal mt-3">
            <?php foreach ($promotions as $promo): ?>

                <?php
                if ((int)$promo['id'] === 1) continue;

                $hasRecord = !empty($promo['received_id']); // đã từng nhận
                $status    = (int)($promo['ap_status'] ?? 0);

                $isActive  = $hasRecord && $status === 1;
                $isUsed    = $hasRecord && $status === 0;


                // icon theo loại
                $img = 'free2.png';
                if ($promo['promotion_type_id'] == 1) $img = 'phantram.png';
                elseif ($promo['promotion_type_id'] == 2) $img = '-$2.png';
                ?>

                <div class="promo-card-simple <?= $hasRecord ? 'disabled' : '' ?>">


                    <div class="promo-icon">
                        <img src="/webbanhang/uploads/Promotions/<?= $img ?>">
                    </div>

                    <div class="promo-info">
                        <div class="promo-title"><?= htmlspecialchars($promo['name']) ?></div>

                        <div class="promo-discount">
                            <?php if ($promo['promotion_type_id'] == 1): ?>
                                Giảm <?= (int)$promo['discount_value'] ?>%
                            <?php elseif ($promo['promotion_type_id'] == 2): ?>
                                Giảm <?= number_format($promo['discount_value']) ?>đ
                            <?php else: ?>
                                Free Ship
                            <?php endif; ?>
                        </div>

                        <div class="promo-expire">
                            <i class="bi bi-clock"></i>
                            Hạn dùng: <?= date('d/m/Y', strtotime($promo['end_date'])) ?>
                        </div>

                        <?php if (!$hasRecord): ?>

                            <!-- CHƯA NHẬN -->
                            <a href="/webbanhang/promotion/receive/<?= (int)$promo['id'] ?>"
                                class="btn btn-sm btn-primary mt-2">
                                Nhận ngay
                            </a>

                        <?php elseif ($isActive): ?>

                            <!-- ĐÃ NHẬN – CÒN DÙNG -->
                            <span class="badge bg-secondary mt-2">Đã nhận</span>

                        <?php else: ?>

                            <!-- ĐÃ NHẬN – HẾT HẠN / ĐÃ DÙNG -->
                            <span class="badge bg-danger mt-2">Không khả dụng</span>

                        <?php endif; ?>

                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </section>


    <!-- Top Rated Products Section -->
    <section class="product-section mt-5">
        <!-- Sử dụng class mới section-heading-with-icon -->
        <div class="section-heading-with-icon">
            <i class="bi bi-box-seam"></i>
            <div>
                <h2>Sản Phẩm Được Đánh Giá Cao Nhất</h2>
            </div>
        </div>
        <div class="product-carousel-wrapper">
            <button class="carousel-btn prev" aria-label="Previous Products">&#10094;</button>
            <?php if (!empty($topProducts) && is_array($topProducts)): ?>
                <div class="product-grid">
                    <?php foreach ($topProducts as $product): ?>
                        <div class="product-card">
                            <a href="/webbanhang/Product/view/<?= htmlspecialchars($product['id']); ?>" class="product-image-link">
                                <img src="/webbanhang/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image" onerror="this.src='/webbanhang/public/images/no-image.png';this.onerror=null;">
                            </a>
                            <div class="product-info">
                                <h3 class="product-name"><?= htmlspecialchars($product['name']); ?></h3>
                                <div class="product-meta">
                                    <p class="product-price"><?= number_format($product['price'], 0, ',', '.'); ?> VND</p>
                                    <span class="product-rating">⭐ <?= number_format($product['total_rating'], 1); ?> (<?= $product['review_count']; ?>)</span>
                                </div>
                            </div>
                            <div class="product-actions">
                                <a href="/webbanhang/cart/add/<?= htmlspecialchars($product['id']); ?>" class="btn btn-primary">Thêm vào giỏ</a>
                                <a href="/webbanhang/Product/view/<?= htmlspecialchars($product['id']); ?>" class="btn btn-secondary">Chi tiết</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center">Không có sản phẩm nào để hiển thị.</p>
            <?php endif; ?>
            <button class="carousel-btn next" aria-label="Next Products">&#10095;</button>
        </div>
    </section>

    <!-- Mid-page Promotional Banner -->
    <section class="promo-banner my-5" style="background-image: url('/webbanhang/uploads/baner2.jpg');">
        <!-- Nếu có nội dung, hãy bỏ comment phần này -->
        <!-- <div class="promo-content">
            <h2>Ưu Đãi Đặc Biệt Tháng Này!</h2>
            <p>Giảm giá lên đến 30% cho tất cả sản phẩm chăm sóc sức khỏe.</p>
            <a href="/webbanhang/promotion" class="btn btn-primary promo-btn">Xem Chi Tiết</a>
        </div> -->
    </section>

    <!-- Top Selling Products Section -->
    <section class="product-section mt-5">
        <!-- Sử dụng class mới section-heading-with-icon -->
        <div class="section-heading-with-icon">
            <i class="bi bi-box-seam"></i>
            <div>
                <h2>Top 10 Sản Phẩm Bán Chạy Nhất</h2>
            </div>
        </div>
        <div class="product-carousel-wrapper">
            <button class="carousel-btn prev" aria-label="Previous Products">&#10094;</button>
            <?php if (!empty($topSellingProducts) && is_array($topSellingProducts)): ?>
                <div class="product-grid">
                    <?php foreach ($topSellingProducts as $product): ?>
                        <div class="product-card">
                            <a href="/webbanhang/Product/view/<?= htmlspecialchars($product['id']); ?>" class="product-image-link">
                                <img src="/webbanhang/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image" onerror="this.src='/webbanhang/public/images/no-image.png';this.onerror=null;">
                            </a>
                            <div class="product-info">
                                <h3 class="product-name"><?= htmlspecialchars($product['name']); ?></h3>
                                <div class="product-meta">
                                    <p class="product-price"><?= number_format($product['price'], 0, ',', '.'); ?> VND</p>
                                    <span class="product-sold">Đã bán: <?= $product['total_sold']; ?></span>
                                </div>
                            </div>
                            <div class="product-actions">
                                <a href="/webbanhang/cart/add/<?= $product['id']; ?>" class="btn btn-primary">Thêm vào giỏ</a>
                                <a href="/webbanhang/Product/view/<?= $product['id']; ?>" class="btn btn-secondary">Chi tiết</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center">Không có sản phẩm nào để hiển thị.</p>
            <?php endif; ?>
            <button class="carousel-btn next" aria-label="Next Products">&#10095;</button>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="product-section mt-5">

        <!-- Title -->
        <!-- Sử dụng class mới section-heading-with-icon -->
        <div class="section-heading-with-icon mb-3">
            <i class="bi bi-box-seam"></i>
            <div>
                <h2>Sản Phẩm Mới Về</h2>
            </div>
        </div>

        <div class="product-carousel-wrapper">
            <button class="carousel-btn prev" aria-label="Previous Products">&#10094;</button>

            <?php if (!empty($newArrivals) && is_array($newArrivals)): ?>
                <div class="product-grid">
                    <?php foreach ($newArrivals as $product): ?>
                        <div class="product-card">
                            <a href="/webbanhang/Product/view/<?= htmlspecialchars($product['id']) ?>" class="product-image-link">
                                <img
                                    src="/webbanhang/<?= htmlspecialchars($product['image']) ?>"
                                    alt="<?= htmlspecialchars($product['name']) ?>"
                                    class="product-image"
                                    onerror="this.src='/webbanhang/public/images/no-image.png';this.onerror=null;">
                            </a>

                            <div class="product-info">
                                <h3 class="product-name">
                                    <?= htmlspecialchars($product['name']) ?>
                                </h3>
                                <div class="product-meta">
                                    <p class="product-price">
                                        <?= number_format($product['price'], 0, ',', '.') ?> VND
                                    </p>
                                    <span class="product-rating">
                                        ⭐ <?= number_format($product['total_rating'] ?? 0, 1) ?>
                                        (<?= $product['review_count'] ?? 0 ?>)
                                    </span>
                                </div>
                            </div>

                            <div class="product-actions">
                                <a href="/webbanhang/cart/add/<?= $product['id'] ?>" class="btn btn-primary">
                                    Thêm vào giỏ
                                </a>
                                <a href="/webbanhang/Product/view/<?= $product['id'] ?>" class="btn btn-secondary">
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center">Không có sản phẩm mới.</p>
            <?php endif; ?>

            <button class="carousel-btn next" aria-label="Next Products">&#10095;</button>
        </div>
    </section>


</main>

<?php include __DIR__ . '/../shares/chatbot.php'; ?>
<?php include __DIR__ . '/../shares/footer.php'; ?>

<!-- === SCRIPT SECTION === -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // ===================================
        // SLIDESHOW LOGIC
        // ===================================
        const slideshowContainer = document.querySelector(".slideshow-container");
        if (slideshowContainer) {
            const images = [
                "/webbanhang/public/images/banner.jpg"
            ];
            let currentIndex = 0;
            let slideInterval;

            // Tạo các thẻ img và thêm vào container
            images.forEach((src, index) => {
                const img = document.createElement("img");
                img.src = src;
                img.alt = `Slide ${index + 1}`;
                if (index === 0) {
                    img.classList.add("active");
                }
                slideshowContainer.appendChild(img);
            });

            const imageElements = slideshowContainer.querySelectorAll('img');

            const showNextSlide = () => {
                imageElements[currentIndex].classList.remove("active");
                currentIndex = (currentIndex + 1) % images.length;
                imageElements[currentIndex].classList.add("active");
            };

            const startSlider = () => {
                slideInterval = setInterval(showNextSlide, 5000); // Đổi ảnh sau 5 giây
            };

            const stopSlider = () => {
                clearInterval(slideInterval);
            };

            slideshowContainer.addEventListener("mouseenter", stopSlider);
            slideshowContainer.addEventListener("mouseleave", startSlider);

            startSlider(); // Bắt đầu chạy slider
        }
        // ===================================
        // BASE CONTAINER SLIDESHOW LOGIC
        const baseContainer = document.querySelector(".base-container");
        if (baseContainer) {
            const images2 = [

                "/webbanhang/public/images/aq1.jpg",
                "/webbanhang/public/images/aq2.jpg",
                "/webbanhang/public/images/aq3.jpg"
            ];
            let currentIndex2 = 0;
            let slideInterval2;

            // Tạo các thẻ img và thêm vào container
            images2.forEach((src, index) => {
                const img2 = document.createElement("img");
                img2.src = src;
                img2.alt = `Slide ${index + 1}`;
                if (index === 0) {
                    img2.classList.add("active");
                }
                baseContainer.appendChild(img2);
            });

            const imageElements2 = baseContainer.querySelectorAll('img');

            const showNextSlide2 = () => {
                imageElements2[currentIndex2].classList.remove("active");
                currentIndex2 = (currentIndex2 + 1) % images2.length;
                imageElements2[currentIndex2].classList.add("active");
            };

            const startSlider2 = () => {
                slideInterval2 = setInterval(showNextSlide2, 5000); // Đổi ảnh sau 5 giây
            };

            const stopSlider2 = () => {
                clearInterval(slideInterval2);
            };

            baseContainer.addEventListener("mouseenter", stopSlider2);
            baseContainer.addEventListener("mouseleave", startSlider2);
            startSlider2(); // Bắt đầu chạy slider
        }

        // ===================================
        // PRODUCT CAROUSEL LOGIC
        // ===================================
        const carousels = document.querySelectorAll('.product-carousel-wrapper');
        carousels.forEach(wrapper => {
            const prevBtn = wrapper.querySelector('.carousel-btn.prev');
            const nextBtn = wrapper.querySelector('.carousel-btn.next');
            const grid = wrapper.querySelector('.product-grid');

            if (!grid || !prevBtn || !nextBtn) return;

            const updateButtons = () => {
                const scrollLeft = grid.scrollLeft;
                const maxScrollLeft = grid.scrollWidth - grid.clientWidth;

                // Dùng một khoảng nhỏ (1px) để xử lý sai số làm tròn của trình duyệt
                const isAtStart = scrollLeft < 1;
                const isAtEnd = maxScrollLeft - scrollLeft < 1;

                // Sử dụng classList.toggle để thêm/xóa class 'hidden'
                // Cú pháp: element.classList.toggle('className', booleanCondition);
                // Nếu điều kiện là true -> thêm class, nếu false -> xóa class.
                prevBtn.classList.toggle('hidden', isAtStart);
                nextBtn.classList.toggle('hidden', isAtEnd);
            };

            nextBtn.addEventListener('click', () => {
                // Cuộn một khoảng bằng 80% chiều rộng của grid
                const scrollAmount = grid.clientWidth * 0.8;
                grid.scrollLeft += scrollAmount;
            });

            prevBtn.addEventListener('click', () => {
                const scrollAmount = grid.clientWidth * 0.8;
                grid.scrollLeft -= scrollAmount;
            });

            // Cập nhật trạng thái nút khi cuộn
            grid.addEventListener('scroll', updateButtons);
            // Cập nhật lần đầu khi tải trang
            updateButtons();
        });


        // ===================================
        // SEARCH AUTOCOMPLETE LOGIC (jQuery)
        // ===================================
        if (window.jQuery) {
            let debounceTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(debounceTimeout);
                let keyword = $(this).val().trim();
                if (keyword.length > 1) {
                    debounceTimeout = setTimeout(() => {
                        $.ajax({
                            url: '/webbanhang/Product/autocomplete',
                            method: 'GET',
                            data: {
                                keyword
                            },
                            success: function(response) {
                                if (response.trim() !== '') {
                                    $('#suggestionBox').html(response).show();
                                } else {
                                    $('#suggestionBox').hide();
                                }
                            },
                            error: function() {
                                $('#suggestionBox').hide();
                            }
                        });
                    }, 300);
                } else {
                    $('#suggestionBox').hide();
                }
            });

            $(document).on('click', '.suggestion-item', function() {
                $('#searchInput').val($(this).text());
                $('#suggestionBox').hide();
                $('#searchForm').submit();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#searchForm').length) {
                    $('#suggestionBox').hide();
                }
            });
        }

    });
</script>