<style>
    /* CSS từ mẫu productFilterOffcanvas, đã được điều chỉnh và gộp */

    /* Header của Offcanvas */
    .offcanvas-header {
        background-color: var(--bs-offcanvas-bg);
        /* Hoặc một màu sáng tùy chỉnh */
    }

    .offcanvas-header .offcanvas-title {
        color: var(--bs-body-color);
        /* Màu chữ tiêu đề */
    }

    .offcanvas-header .btn-close {
        opacity: 0.7;
        /* Làm nút đóng mờ hơn */
    }

    .offcanvas-header .btn-close:hover {
        opacity: 1;
    }

    /* Nhóm filter */
    .filter-group {
        padding-bottom: 1rem;
        margin-bottom: 1.2rem;
        border-bottom: 1px solid var(--bs-border-color-translucent);
        /* Viền phân cách nhẹ nhàng */
    }

    /* Loại bỏ viền dưới cho nhóm filter cuối cùng */
    .offcanvas-body form .filter-group:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    /* Label đẹp hơn */
    .filter-group label.form-check-label {
        font-weight: 600;
        /* Đậm hơn một chút */
        color: var(--bs-body-color);
        /* Màu chữ chính */
        transition: color 0.2s ease;
        /* Thêm hiệu ứng chuyển đổi màu */
    }

    .filter-group label.form-check-label .bi {
        color: var(--bs-secondary);
        /* Icon màu xám */
    }

    /* Toggles */
    .form-check-input.filter-toggle:checked+label.form-check-label {
        color: var(--bs-primary);
        /* Màu chữ label sáng hơn khi bật toggle */
    }

    .form-check-input.filter-toggle:checked+label.form-check-label .bi {
        color: var(--bs-primary);
        /* Icon cũng sáng hơn */
    }

    /* Input và Select nhỏ gọn */
    .form-control.filter-input,
    .form-select.filter-input {
        border-radius: var(--bs-border-radius-sm);
        /* Bo góc nhỏ hơn */
        font-size: var(--bs-body-font-size-sm);
        /* Chữ nhỏ hơn */
    }

    .form-control.filter-input:disabled,
    .form-select.filter-input:disabled {
        background-color: var(--bs-tertiary-bg);
        /* Nền nhẹ nhàng hơn khi disabled */
        opacity: 0.7;
    }

    /* Buttons */
    .btn-custom {
        background-color: var(--bs-primary);
        /* Màu xanh dương chính */
        color: var(--bs-white);
        border: none;
        padding: var(--bs-btn-padding-y) var(--bs-btn-padding-x);
        font-size: var(--bs-btn-font-size);
        border-radius: var(--bs-btn-border-radius);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        /* Căn giữa nội dung nút */
        gap: 0.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease;
        width: 100%;
        /* Đảm bảo nút full width trong d-grid */
    }

    .btn-custom:hover {
        background-color: var(--bs-primary-hover);
        /* Màu hover đậm hơn */
        color: var(--bs-white);
    }

    .btn-custom:active {
        background-color: var(--bs-primary-active);
        border-color: var(--bs-primary-active);
    }

    .btn-reset {
        background-color: var(--bs-light);
        /* Màu nền sáng */
        color: var(--bs-body-color);
        /* Màu chữ tối */
        border: 1px solid var(--bs-border-color);
        padding: var(--bs-btn-padding-y) var(--bs-btn-padding-x);
        font-size: var(--bs-btn-font-size);
        border-radius: var(--bs-btn-border-radius);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        /* Căn giữa nội dung nút */
        gap: 0.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease;
        width: 100%;
        /* Đảm bảo nút full width trong d-grid */
    }

    .btn-reset:hover {
        background-color: var(--bs-secondary-bg);
        /* Nền xám nhạt hơn khi hover */
        color: var(--bs-body-color);
        border-color: var(--bs-border-color);
    }

    .btn-reset:active {
        background-color: var(--bs-tertiary-bg);
        border-color: var(--bs-tertiary-bg);
    }

    /* Adjust offcanvas-body padding for non-sticky buttons */
    .offcanvas-body {
        padding-bottom: var(--bs-offcanvas-padding-y);
        /* Reset về padding mặc định */
    }

    /* Variables for Bootstrap color shades - adapt if needed based on your Bootstrap version */
    :root {
        --bs-primary-hover: #0b5ed7;
        /* Màu hover cho primary */
        --bs-primary-active: #0a58ca;
        /* Màu active cho primary */
        --bs-secondary: #6c757d;
        /* Màu secondary mặc định */
        --bs-tertiary-bg: #e9ecef;
        /* Màu nền xám nhẹ */
    }
</style>
<!-- Offcanvas Filter -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
    <div class="offcanvas-header border-bottom py-3"> <!-- Header đơn giản với viền dưới -->
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle"
                style="width:34px; height:34px;">
                <i class="bi bi-funnel-fill"></i> <!-- Icon chính cho bộ lọc -->
            </div>
            <h5 class="offcanvas-title fw-bold m-0" id="filterOffcanvasLabel">Bộ lọc đơn hàng</h5>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
    </div>

    <div class="offcanvas-body">
        <form method="get" action="/webbanhang/Admin/filter" id="filterForm">
            <input type="hidden" name="keyword" id="filterSearchInput">

            <!-- Tên khách hàng -->
            <fieldset class="filter-group"> <!-- Sử dụng filter-group -->
                <div class="form-check form-switch mb-2"> <!-- Dùng form-switch để có toggle đẹp hơn -->
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="chk_customer_name">
                    <label class="form-check-label fw-semibold" for="chk_customer_name">
                        <i class="bi bi-person-circle me-2 text-secondary"></i> Lọc theo tên khách hàng
                    </label>
                </div>
                <input type="text" name="customer_name" id="customer_name" class="form-control form-control-sm filter-input" placeholder="Nhập tên khách hàng" disabled>
            </fieldset>

            <!-- Tên sản phẩm -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="chk_product_name">
                    <label class="form-check-label fw-semibold" for="chk_product_name">
                        <i class="bi bi-box-seam me-2 text-secondary"></i> Lọc theo tên sản phẩm
                    </label>
                </div>
                <input type="text" name="product_name" id="product_name" class="form-control form-control-sm filter-input" placeholder="Nhập tên sản phẩm" disabled>
            </fieldset>

            <!-- Mã khách hàng -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="chk_customer_id">
                    <label class="form-check-label fw-semibold" for="chk_customer_id">
                        <i class="bi bi-people me-2 text-secondary"></i> Lọc theo mã khách hàng (Từ - Đến)
                    </label>
                </div>
                <div class="d-flex gap-2">
                    <input type="number" name="min_customer_id" id="min_customer_id" class="form-control form-control-sm filter-input" placeholder="Từ mã" min="1" disabled>
                    <input type="number" name="max_customer_id" id="max_customer_id" class="form-control form-control-sm filter-input" placeholder="Đến mã" min="1" disabled>
                </div>
            </fieldset>

            <!-- Trạng thái -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="chk_status">
                    <label class="form-check-label fw-semibold" for="chk_status">
                        <i class="bi bi-toggles me-2 text-secondary"></i> Lọc theo trạng thái
                    </label>
                </div>
                <select class="form-select form-select-sm filter-input" name="status[]" id="status" multiple disabled>
                    <option value="pending">Chờ xử lý</option>
                    <option value="processing">Đang xử lý</option>
                    <option value="completed">Hoàn tất</option>
                    <option value="cancelled">Đã hủy</option>
                </select>
            </fieldset>

            <!-- Khoảng tiền -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="chk_amount">
                    <label class="form-check-label fw-semibold" for="chk_amount">
                        <i class="bi bi-currency-dollar me-2 text-secondary"></i> Lọc theo khoảng tiền (VND)
                    </label>
                </div>
                <div class="d-flex gap-2">
                    <input type="number" name="min_amount" id="min_amount" class="form-control form-control-sm filter-input" placeholder="Từ" min="0" disabled>
                    <input type="number" name="max_amount" id="max_amount" class="form-control form-control-sm filter-input" placeholder="Đến" min="0" disabled>
                </div>
            </fieldset>

            <!-- Mã đơn hàng -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="chk_order_id">
                    <label class="form-check-label fw-semibold" for="chk_order_id">
                        <i class="bi bi-hash me-2 text-secondary"></i> Lọc theo mã đơn hàng (Từ - Đến)
                    </label>
                </div>
                <div class="d-flex gap-2">
                    <input type="number" name="min_order_id" id="min_order_id" class="form-control form-control-sm filter-input" placeholder="Từ mã" min="1" disabled>
                    <input type="number" name="max_order_id" id="max_order_id" class="form-control form-control-sm filter-input" placeholder="Đến mã" min="1" disabled>
                </div>
            </fieldset>

            <!-- Ngày đặt hàng -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="chk_date">
                    <label class="form-check-label fw-semibold" for="chk_date">
                        <i class="bi bi-calendar me-2 text-secondary"></i> Lọc theo ngày đặt hàng
                    </label>
                </div>
                <div class="d-flex gap-2">
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm filter-input" disabled>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm filter-input" disabled>
                </div>
            </fieldset>

            <!-- Phương thức thanh toán -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="chk_payment">
                    <label class="form-check-label fw-semibold" for="chk_payment">
                        <i class="bi bi-credit-card me-2 text-secondary"></i> Lọc theo phương thức thanh toán
                    </label>
                </div>
                <select class="form-select form-select-sm filter-input" name="payment_methods[]" id="payment_methods" multiple disabled>
                    <option value="momo">Momo</option>
                    <option value="vnpay">VNPay</option>
                    <option value="cash">Tiền mặt</option>
                </select>
            </fieldset>

            <!-- Buttons (non-sticky footer) -->
            <div class="d-grid gap-2 mt-4"> <!-- d-grid để các nút full width và có khoảng cách -->
                <button type="submit" class="btn-custom">
                    <i class="bi bi-funnel-fill me-2"></i>Áp dụng bộ lọc
                </button>
                <button type="button" class="btn-reset" id="resetFilter">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Đặt lại tất cả
                </button>
            </div>
            </input>
        </form>
    </div>
</div>