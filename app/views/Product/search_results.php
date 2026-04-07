<?php include __DIR__ . '/../shares/header.php'; ?>
<?php include __DIR__ . '/../shares/navbar.php'; ?>

<!-- Lấy tên danh mục hiện tại để hiển thị lên nút -->
<?php
$currentCategoryName = "Tất cả danh mục";
foreach ($categories as $cat) {
    if ($filter_category_id == $cat->id) {
        $currentCategoryName = $cat->name;
        break;
    }
}
?>

<style>
    /* === CẤU HÌNH CHUNG === */
    body {
        background-color: #f4f7f6;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Fix lỗi hiển thị text cắt dòng */
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Editor báo vàng kệ nó, chạy tốt */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 3rem;
        line-height: 1.5rem;
    }

    /* === SIDEBAR BỘ LỌC === */
    .filter-sidebar {
        background: #fff;
        padding: 24px;
        border-radius: 16px;
        position: sticky;
        top: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        z-index: 100;
        /* Để dropdown đè lên nội dung bên dưới */
    }

    .filter-title {
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 20px;
        color: #111;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-group label {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #999;
        margin-bottom: 8px;
        display: block;
    }

    /* === CUSTOM DROPDOWN (THAY THẾ SELECT MẶC ĐỊNH) === */
    .custom-dropdown-btn {
        width: 100%;
        text-align: left;
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 15px;
        font-weight: 600;
        color: #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
    }

    .custom-dropdown-btn:hover,
    .custom-dropdown-btn[aria-expanded="true"] {
        border-color: #0d6efd;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .custom-dropdown-menu {
        width: 100%;
        /* Rộng bằng nút cha */
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        padding: 8px;
        margin-top: 5px;
        max-height: 300px;
        /* Chiều cao tối đa */
        overflow-y: auto;
        /* Tự hiện thanh cuộn nếu dài */
    }

    /* Style thanh cuộn cho dropdown */
    .custom-dropdown-menu::-webkit-scrollbar {
        width: 6px;
    }

    .custom-dropdown-menu::-webkit-scrollbar-thumb {
        background-color: #cbd5e0;
        border-radius: 4px;
    }

    .custom-dropdown-item {
        padding: 10px 15px;
        border-radius: 8px;
        font-weight: 500;
        color: #555;
        cursor: pointer;
        transition: all 0.2s;
        /* Để hiển thị hết nội dung dài */
        white-space: normal;
        line-height: 1.4;
    }

    .custom-dropdown-item:hover {
        background-color: #f0f7ff;
        color: #0d6efd;
    }

    .custom-dropdown-item.active {
        background-color: #0d6efd;
        color: white;
    }


    /* === INPUT GIÁ === */
    .stylish-input {
        background-color: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.9rem;
    }

    .stylish-input:focus {
        background-color: #fff;
        border-color: #0d6efd;
        outline: none;
    }

    /* === PRODUCT CARD === */
    .product-card-clean {
        background: #fff;
        border-radius: 12px;
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 12px;
        border: 1px solid #f0f0f0;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .product-card-clean:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        border-color: transparent;
    }

    .card-img-wrapper {
        position: relative;
        padding-top: 100%;
        overflow: hidden;
        border-radius: 8px;
        background: #f8f9fa;
        margin-bottom: 12px;
    }

    .card-img-wrapper img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
        transition: transform 0.5s;
    }

    .product-card-clean:hover .card-img-wrapper img {
        transform: scale(1.08);
    }

    .card-info {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
        text-decoration: none;
    }

    .product-title:hover {
        color: #0d6efd;
    }

    .product-price {
        color: #dc3545;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }

    .btn-add-cart {
        margin-top: auto;
        border: 1px solid #0d6efd;
        background: white;
        color: #0d6efd;
        padding: 8px;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-add-cart:hover {
        background: #0d6efd;
        color: white;
    }

    /* === PHÂN TRANG === */
    .pagination .page-link {
        border-radius: 8px;
        margin-left: 6px;
        border: none;
        color: #555;
        font-weight: 600;
        min-width: 38px;
        text-align: center;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        color: white;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
    }
</style>

<div class="container py-5">
    <div class="row g-4">

        <!-- SIDEBAR -->
        <div class="col-lg-3">
            <div class="filter-sidebar">
                <div class="filter-title">
                    <i class="bi bi-funnel"></i> Bộ Lọc
                </div>

                <form id="filterForm" action="/webbanhang/product/search" method="GET">
                    <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">

                    <!-- INPUT ẨN ĐỂ LƯU GIÁ TRỊ CATEGORY KHI CHỌN TỪ DROPDOWN -->
                    <input type="hidden" name="category" id="hidden_category_input" value="<?= $filter_category_id ?>">

                    <!-- CUSTOM DROPDOWN LIST -->
                    <div class="filter-group mb-4">
                        <label>Danh mục sản phẩm</label>
                        <div class="dropdown">
                            <!-- Nút hiển thị -->
                            <button class="custom-dropdown-btn" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="text-truncate me-2" id="dropdownLabel"><?= htmlspecialchars($currentCategoryName) ?></span>
                                <i class="bi bi-chevron-down small"></i>
                            </button>

                            <!-- Danh sách xổ xuống (Làm đẹp thoải mái ở đây) -->
                            <ul class="dropdown-menu custom-dropdown-menu" aria-labelledby="categoryDropdown">
                                <li>
                                    <div class="custom-dropdown-item <?= empty($filter_category_id) ? 'active' : '' ?>"
                                        onclick="selectCategory('', 'Tất cả danh mục')">
                                        <i class="bi bi-grid-fill me-2 text-muted"></i> Tất cả danh mục
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <div class="custom-dropdown-item <?= ($filter_category_id == $category->id) ? 'active' : '' ?>"
                                            onclick="selectCategory('<?= $category->id ?>', '<?= htmlspecialchars($category->name) ?>')">
                                            <?= htmlspecialchars($category->name) ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="filter-group mb-4">
                        <label>Khoảng giá</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" name="min_price" class="form-control stylish-input" placeholder="Min" value="<?= $filter_min_price ?? '' ?>">
                            <span class="text-muted">-</span>
                            <input type="number" name="max_price" class="form-control stylish-input" placeholder="Max" value="<?= $filter_max_price ?? '' ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-3 shadow-sm">
                        Áp dụng
                    </button>

                    <?php if (!empty($filter_category_id) || !empty($filter_min_price) || !empty($filter_max_price)): ?>
                        <div class="text-center mt-3">
                            <a href="/webbanhang/product/search?keyword=<?= htmlspecialchars($keyword) ?>" class="text-danger small text-decoration-none">
                                <i class="bi bi-x-circle"></i> Xóa bộ lọc
                            </a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="mb-0 text-dark">Kết quả: "<?= htmlspecialchars($keyword) ?>"</h4>
                <span class="badge bg-light text-dark border"><?= $totalResults ?> sản phẩm</span>
            </div>

            <?php if (!empty($results)): ?>
                <!-- Grid 3 cột x 2 dòng = 6 sản phẩm -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                    <?php foreach ($results as $product): ?>
                        <div class="col">
                            <div class="product-card-clean">
                                <div class="card-img-wrapper">
                                    <a href="/webbanhang/Product/view/<?= $product->id ?>">
                                        <img src="/webbanhang/<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>" onerror="this.src='/webbanhang/public/images/no-image.png';this.onerror=null;">
                                    </a>
                                </div>
                                <div class="card-info">
                                    <a href="/webbanhang/Product/view/<?= $product->id ?>" class="product-title text-truncate-2">
                                        <?= htmlspecialchars($product->name) ?>
                                    </a>
                                    <div class="product-price"><?= number_format($product->price, 0, ',', '.') ?>đ</div>
                                    <a href="/webbanhang/cart/add/<?= $product->id ?>" class="btn-add-cart">Thêm vào giỏ</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-end">
                            <?php
                            unset($queryParams['page']);
                            $queryString = http_build_query($queryParams);
                            ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= $queryString ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-light text-center shadow-sm py-5">
                    <i class="bi bi-search display-6 text-muted"></i>
                    <p class="mt-3 mb-0">Không tìm thấy sản phẩm nào.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JAVASCRIPT ĐỂ XỬ LÝ DROPDOWN -->
<script>
    function selectCategory(id, name) {
        // 1. Cập nhật giá trị vào Input ẩn
        document.getElementById('hidden_category_input').value = id;

        // 2. Cập nhật text hiển thị trên nút
        document.getElementById('dropdownLabel').textContent = name;

        // 3. Xử lý UI active (tùy chọn)
        const items = document.querySelectorAll('.custom-dropdown-item');
        items.forEach(item => item.classList.remove('active'));
        event.target.classList.add('active'); // Lưu ý: event target có thể cần điều chỉnh nếu icon được click
    }
</script>

<?php include __DIR__ . '/../shares/footer.php'; ?>