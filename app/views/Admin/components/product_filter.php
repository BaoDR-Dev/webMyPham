<!-- Offcanvas Filter -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="productFilterOffcanvas" aria-labelledby="productFilterLabel">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle"
                style="width:34px; height:34px;">
                <i class="bi bi-funnel-fill"></i>
            </div>
            <h5 class="offcanvas-title fw-bold m-0" id="productFilterLabel">Bộ lọc nâng cao</h5>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
    </div>

    <div class="offcanvas-body">

        <form action="/webbanhang/Admin/ajaxFilterProducts/<?= $category_id ?>" method="GET" id="filterForm">

            <!-- Search -->
            <div class="mb-4">
                <label class="small fw-semibold text-muted mb-2">Tìm theo từ khóa</label>
                <div class=" position-relative">
                    <input type="search" name="search" id="searchInput" class="form-control"
                        style="width: 360px; margin-left: -10px;"
                        placeholder="Nhập tên / mã / SKU..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" autocomplete="off">
                    <div id="suggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index:1000;"></div>
                </div>
            </div>

            <hr class="my-4">

            <!-- PHP Filters -->
            <?php
            $filters = [
                ['id' => 'filterProductId', 'label' => 'Khoảng Mã ID', 'icon' => 'bi-hash', 'inputs' => ['minId' => 'Từ mã', 'maxId' => 'Đến mã']],
                ['id' => 'filterPrice', 'label' => 'Khoảng Giá (₫)', 'icon' => 'bi-currency-dollar', 'inputs' => ['minPrice' => 'Giá tối thiểu', 'maxPrice' => 'Giá tối đa']],
                ['id' => 'filterQuantity', 'label' => 'Số lượng kho', 'icon' => 'bi-box-seam', 'inputs' => ['minQuantity' => 'Tối thiểu', 'maxQuantity' => 'Tối đa']],
            ];
            ?>

            <!-- Dynamic Filters -->
            <?php foreach ($filters as $filter): ?>
                <fieldset class="mb-4">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="<?= $filter['id'] ?>">
                        <label class="form-check-label fw-semibold" for="<?= $filter['id'] ?>">
                            <i class="<?= $filter['icon'] ?> me-2"></i><?= $filter['label'] ?>
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <?php foreach ($filter['inputs'] as $name => $placeholder): ?>
                            <input type="number" class="form-control filter-input" name="<?= $name ?>"
                                placeholder="<?= $placeholder ?>" min="0" disabled>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>

            <!-- Status Filter -->
            <fieldset class="mb-4">
                <div class="form-check form-switch mb-2">
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="filterStatus">
                    <label class="form-check-label fw-semibold" for="filterStatus">
                        <i class="bi bi-toggle-on me-2"></i>Trạng thái bán
                    </label>
                </div>

                <select class="form-select filter-input" name="status" disabled>
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="1">Đang hoạt động</option>
                    <option value="0">Tạm dừng</option>
                </select>
            </fieldset>

            <!-- Rating Stars -->
            <fieldset class="mb-4">
                <div class="form-check form-switch mb-2">
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="filterRatingStars">
                    <label class="form-check-label fw-semibold" for="filterRatingStars">
                        <i class="bi bi-star-fill text-warning me-2"></i>Số sao đánh giá
                    </label>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <input type="number" class="form-control filter-input" name="minRating" placeholder="0.0" min="0" max="5" step="0.1" disabled>
                    <span class="text-muted small">→</span>
                    <input type="number" class="form-control filter-input" name="maxRating" placeholder="5.0" min="0" max="5" step="0.1" disabled>
                </div>
            </fieldset>

            <!-- Buttons -->
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary shadow-sm fw-semibold py-2">
                    <i class="bi bi-check2-circle me-2"></i>Áp dụng bộ lọc
                </button>

                <button type="button" id="resetBtn" class="btn btn-outline-secondary fw-semibold py-2">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Đặt lại tất cả
                </button>
            </div>
        </form>
    </div>
</div>