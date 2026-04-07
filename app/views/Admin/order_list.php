<?php include 'app/views/shares/header.php'; ?>
<style>
    .table-loading {
        opacity: 0.5;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .clear-search {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #adb5bd;
        font-size: 1.1rem;
        transition: color 0.2s ease;
        z-index: 10;
    }

    .clear-search:hover {
        color: #dc3545;
    }

    /* Wrapper ngoài */
    .search-wrapper {
        border: 1px solid var(--bs-border-color);
    }

    /* Input group */
    .search-group {
        border-radius: 0.75rem;
    }

    /* Input */
    .search-group input::placeholder {
        font-size: 0.95rem;
        color: #adb5bd;
    }

    /* Focus đẹp hơn */
    .search-group:focus-within {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
    }
</style>
<div class="order-management-wrapper container my-4">
    <?php require_once __DIR__ . '/components/order_filter.php'; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <!-- Tiêu đề -->
        <div class="d-flex align-items-start gap-2 border-start border-4 border-primary ps-3">
            <i class="bi bi-box-seam fs-4 text-primary mt-1"></i>
            <div>
                <h4 class="mb-1 fw-semibold" style="color: #6b67d2ff;">Điều hành đơn hàng</h4>
                <small class="text-muted">Theo dõi, lọc và xử lý các đơn mua hàng</small>
            </div>
        </div>

        <!-- Thời gian + Bộ lọc -->
        <div class="d-flex align-items-center gap-2">

            <!-- Thời gian -->
            <span class="badge bg-white text-dark border p-2 px-3 rounded-pill shadow-sm fw-semibold">
                <i class="bi bi-calendar3 me-2 text-primary"></i>
                Ngày <?= date('d/m/Y') ?>
            </span>

            <!-- Nút lọc -->
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                style="width: 100px;"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#filterOffcanvas"
                aria-controls="filterOffcanvas">
                <i class="bi bi-funnel-fill"></i>
                Bộ lọc
            </button>
        </div>

    </div>

    <!-- Thanh tìm kiếm nhanh -->
    <!-- Quick search -->
    <div class="input-group search-group overflow-hidden shadow-sm position-relative" style="max-width: 400px; margin-bottom: 20px;">
        <span class="input-group-text bg-white border-0">
            <i class="bi bi-search text-primary"></i>
        </span>

        <input type="text"
            id="quickSearchInput"
            class="form-control border-0 shadow-none pe-5"
            placeholder="Nhập tên khách hàng hoặc mã đơn hàng">

        <!-- Icon clear -->
        <span class="clear-search d-none" id="clearQuickSearch">
            <i class="bi bi-x-circle-fill"></i>
        </span>
    </div>





    <!-- Bảng đơn hàng -->
    <div id="orderTableContainer">
        <?php require_once __DIR__ . '/components/order_table.php'; ?>
    </div>

    <!-- Loader -->
    <div id="orderDataLoader" class="text-center my-3 d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
        </div>
        <p>Đang tải đơn hàng...</p>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {

        /* ========================
            KHAI BÁO BIẾN CHUNG
        ======================== */

        const orderTableContainer = document.getElementById("orderTableContainer");
        const orderDataLoader = document.getElementById("orderDataLoader");
        const resetButton = document.getElementById("resetFilter");

        const quickSearchInput = document.getElementById('quickSearchInput');
        const filterSearchInput = document.getElementById('filterSearchInput');
        const filterForm = document.getElementById('filterForm');
        const clearQuickSearch = document.getElementById('clearQuickSearch');

        let quickSearchTimeout = null;

        if (quickSearchInput && clearQuickSearch) {

            quickSearchInput.addEventListener('input', function() {
                const keyword = this.value.trim();

                // Hiện / ẩn icon X
                clearQuickSearch.classList.toggle('d-none', keyword.length === 0);

                // Debounce
                clearTimeout(quickSearchTimeout);
                quickSearchTimeout = setTimeout(() => {

                    // Nếu có keyword thì search nhanh
                    if (keyword.length > 0) {
                        loadOrderTable("?keyword=" + encodeURIComponent(keyword));
                    } else {
                        // Nếu xoá hết text → load lại toàn bộ
                        loadOrderTable();
                    }

                }, 400);
            });

            // Click icon X
            clearQuickSearch.addEventListener('click', function() {
                quickSearchInput.value = '';
                clearQuickSearch.classList.add('d-none');

                clearTimeout(quickSearchTimeout);
                loadOrderTable();
            });
        }




        /* ========================
            HÀM LOAD BẢNG ĐƠN HÀNG
        ======================== */
        let currentController = null;

        function loadOrderTable(queryString = "") {

            // Huỷ request cũ
            if (currentController) {
                currentController.abort();
            }

            currentController = new AbortController();

            orderTableContainer.classList.add("table-loading");

            fetch("/webbanhang/Admin/ajaxFilterOrder" + queryString, {
                    signal: currentController.signal,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.text())
                .then(html => {
                    orderTableContainer.innerHTML = html;
                })
                .catch(err => {
                    if (err.name !== "AbortError") {
                        console.error(err);
                    }
                })
                .finally(() => {
                    orderTableContainer.classList.remove("table-loading");
                });
        }

        // function loadOrderTable(queryString = "") {
        //     // orderTableContainer.classList.add("d-none");
        //     // orderDataLoader.classList.remove("d-none");

        //     fetch("/webbanhang/Admin/ajaxFilterOrder" + queryString, {
        //             method: "GET",
        //             headers: {
        //                 "X-Requested-With": "XMLHttpRequest"
        //             }
        //         })
        //         .then(res => res.text())
        //         .then(html => {
        //             orderTableContainer.innerHTML = html;
        //         })
        //         .catch(err => {
        //             console.error("Lỗi tải đơn hàng:", err);
        //         })
        //         .finally(() => {
        //             orderTableContainer.classList.remove("d-none");
        //             orderDataLoader.classList.add("d-none");
        //         });
        // }


        /* ========================
            BẬT / TẮT CONTROL FILTER
        ======================== */
        const filtersConfig = [{
                checkboxId: "chk_customer_name",
                controls: ["customer_name"]
            },
            {
                checkboxId: "chk_product_name",
                controls: ["product_name"]
            },
            {
                checkboxId: "chk_customer_id",
                controls: ["min_customer_id", "max_customer_id"]
            },
            {
                checkboxId: "chk_status",
                controls: ["status"]
            },
            {
                checkboxId: "chk_amount",
                controls: ["min_amount", "max_amount"]
            },
            {
                checkboxId: "chk_order_id",
                controls: ["min_order_id", "max_order_id"]
            },
            {
                checkboxId: "chk_date",
                controls: ["start_date", "end_date"]
            },
            {
                checkboxId: "chk_payment",
                controls: ["payment_methods"]
            }
        ];

        function toggleControls(controls, disabled) {
            controls.forEach(control => {
                if (!control) return;
                control.disabled = disabled;

                if (control.tagName === "SELECT" && control.multiple) {
                    Array.from(control.options).forEach(opt => opt.disabled = disabled);
                }
            });
        }

        filtersConfig.forEach(filter => {
            const checkbox = document.getElementById(filter.checkboxId);
            if (!checkbox) return;

            const controls = filter.controls.map(id => document.getElementById(id));
            toggleControls(controls, !checkbox.checked);

            checkbox.addEventListener("change", function() {
                toggleControls(controls, !this.checked);
            });
        });

        /* ========================
            SUBMIT FILTER
        ======================== */
        if (filterForm) {
            filterForm.addEventListener("submit", function(e) {
                e.preventDefault();

                // Khi filter thì clear tìm nhanh để tránh xung đột
                // if (quickSearchInput) quickSearchInput.value = "";

                const params = new URLSearchParams(new FormData(this));
                loadOrderTable("?" + params.toString());
            });
        }

        /* ========================
            RESET FILTER
        ======================== */
        if (resetButton) {
            resetButton.addEventListener("click", function(e) {
                e.preventDefault();

                // Bỏ chọn checkbox
                document.querySelectorAll("#filterOffcanvas .form-check-input").forEach(cb => {
                    cb.checked = false;
                    cb.dispatchEvent(new Event("change"));
                });

                // Reset input
                document.querySelectorAll("#filterOffcanvas .form-control").forEach(input => {
                    if (input.tagName === "SELECT") {
                        input.selectedIndex = 0;
                    } else {
                        input.value = "";
                    }
                });

                // Clear search
                // if (quickSearchInput) quickSearchInput.value = "";

                // Load lại danh sách không có tìm kiếm hay bộ lọc
                loadOrderTable();
            });
        }

    });
</script>
<?php include 'app/views/shares/footer.php'; ?>