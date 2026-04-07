<?php include 'app/views/shares/header.php'; ?>

<style>
    /* Tải font chữ nếu cần hoặc dùng hệ thống */
    :root {
        --primary-color: #4361ee;
        --success-color: #2ec4b6;
        --danger-color: #e63946;
        --warning-color: #ff9f1c;
        --text-main: #2b2d42;
        --text-muted: #8d99ae;
        --bg-light: #f8f9fa;
        --border-color: #edf2f4;
    }

    .promotion-management {
        color: var(--text-main);
        font-family: 'Inter', sans-serif;
    }

    /* Header & Button Thêm mới */
    .page-title {
        font-weight: 700;
        color: var(--text-main);
    }

    .btn-add-new {
        background: var(--primary-color);
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
    }

    .btn-add-new:hover {
        background: #374ccf;
        color: #fff;
        transform: translateY(-2px);
    }

    /* Filter Section - Thay thế Card */
    .filter-wrapper {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .filter-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 8px;
        display: block;
    }

    .custom-input {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 12px;
        background-color: var(--bg-light);
    }

    .custom-input:focus {
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        border-color: var(--primary-color);
    }

    .btn-filter {
        background: #fff;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        padding: 10px;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-filter:hover {
        background: var(--primary-color);
        color: #fff;
    }

    /* Table Section - Thay thế Card */
    .table-container {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        /* Để bo góc table */
        border: 1px solid var(--border-color);
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table thead {
        background-color: var(--bg-light);
    }

    .custom-table th {
        padding: 15px 20px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        font-weight: 700;
        border-bottom: 2px solid var(--border-color);
    }

    .custom-table td {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .custom-table tr:hover {
        background-color: rgba(67, 97, 238, 0.02);
    }

    /* Nội dung trong bảng */
    .promo-name {
        font-weight: 600;
        color: var(--text-main);
    }

    .promo-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .badge-type {
        background: #e2e8f0;
        color: #475569;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .promo-value {
        font-weight: 700;
        color: var(--primary-color);
    }

    .promo-date {
        font-size: 0.85rem;
        color: #64748b;
    }

    /* Status Badges */
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .status-active {
        background-color: rgba(46, 196, 182, 0.15);
        color: #008f82;
    }

    .status-expired {
        background-color: rgba(230, 57, 70, 0.1);
        color: var(--danger-color);
    }

    /* Action Buttons */
    .action-btns a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        margin-left: 5px;
        transition: 0.3s;
        text-decoration: none;
    }

    .btn-edit {
        color: var(--warning-color);
        border: 1px solid var(--warning-color);
    }

    .btn-edit:hover {
        background: var(--warning-color);
        color: #fff;
    }

    .btn-delete {
        color: var(--danger-color);
        border: 1px solid var(--danger-color);
    }

    .btn-delete:hover {
        background: var(--danger-color);
        color: #fff;
    }

    .empty-state {
        text-align: center;
        padding: 50px !important;
        color: var(--text-muted);
    }

    .btn-filter {
        background: #fff;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        padding: 10px 15px;
        /* Chỉnh lại padding cho cân với nút Add */
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-filter:hover {
        background: var(--primary-color);
        color: #fff;
    }

    /* Đảm bảo Header luôn đẹp trên mobile */
    @media (max-width: 576px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 15px;
        }

        .header-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>

<link rel="stylesheet" href="/webbanhang/public/css/promotion-custom.css">

<div class="container-fluid mt-4 promotion-management">

    <!-- HEADER: Tiêu đề bên trái, Nhóm nút bên phải -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title m-0">
            <i class="bi bi-tags-fill me-2"></i>Quản lý khuyến mãi
        </h4>

        <div class="header-actions d-flex gap-2">
            <!-- Nút Bộ lọc đã được đưa vào đây -->
            <button class="btn btn-filter" data-bs-toggle="offcanvas" data-bs-target="#promotionFilter">
                <i class="bi bi-funnel"></i> Bộ lọc
            </button>

            <!-- Nút Thêm mới -->
            <a href="/webbanhang/Admin/addPromotion" class="btn-add-new">
                <i class="bi bi-plus-lg me-1"></i>Thêm khuyến mãi
            </a>
        </div>
    </div>

    <!-- OFFCANVAS BỘ LỌC (Giữ nguyên) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="promotionFilter">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">
                <i class="bi bi-funnel-fill me-2"></i>Bộ lọc khuyến mãi
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <form id="promotionFilterForm">
                <!-- KEYWORD -->
                <div class="mb-3">
                    <label class="filter-label">Tìm kiếm</label>
                    <input type="text" name="keyword" class="form-control custom-input"
                        placeholder="Tên / nội dung">
                </div>

                <!-- TYPE -->
                <div class="mb-3">
                    <label class="filter-label">Loại khuyến mãi</label>
                    <select name="promotion_type_id" class="form-select custom-input">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= $type->id ?>">
                                <?= htmlspecialchars($type->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- STATUS -->
                <div class="mb-3">
                    <label class="filter-label">Trạng thái</label>
                    <select name="is_active" class="form-select custom-input">
                        <option value="">-- Tất cả --</option>
                        <option value="1">Kích hoạt</option>
                        <option value="0">Tắt</option>
                    </select>
                </div>

                <!-- DATE -->
                <div class="mb-3">
                    <label class="filter-label">Thời gian</label>
                    <div class="d-flex gap-2">
                        <input type="date" name="start_date" class="form-control custom-input">
                        <input type="date" name="end_date" class="form-control custom-input">
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Áp dụng
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        Đặt lại
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- BẢNG DỮ LIỆU -->
    <div id="promotionTableWrapper">
        <?php include 'app/views/Admin/components/table_promotions.php'; ?>
    </div>

    <script>
        document.getElementById('promotionFilterForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const params = new URLSearchParams(new FormData(form)).toString();

            fetch('/webbanhang/Admin/ajaxFilterPromotions?' + params)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('promotionTableWrapper').innerHTML = html;

                    // Đóng offcanvas
                    const offcanvasElement = document.getElementById('promotionFilter');
                    const instance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (instance) instance.hide();
                });
        });
    </script>

</div>

<?php include 'app/views/shares/footer.php'; ?>