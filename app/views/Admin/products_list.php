<?php include __DIR__ . '/../shares/header.php'; ?>

<style>
    body {
        background: #f3f6f9;
        font-family: 'Inter', sans-serif;
    }

    /* ---- Card Layout ---- */
    .stat-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 22px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
        transition: 0.25s ease;
        border: 1px solid rgba(230, 232, 236, 0.8);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 26px rgba(0, 0, 0, 0.06);
    }

    .icon-box {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.6rem;
    }

    /* ---- Toolbar ---- */
    .toolbar-card {
        background: #ffffff;
        padding: 20px 24px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(230, 232, 236, 0.8);
    }

    .search-group {
        border-radius: 40px;
        background: #f8f9fc;
        border: 1px solid #e1e2e6;
        transition: 0.2s;
    }

    .search-group:focus-within {
        box-shadow: 0 0 0 3px rgba(50, 132, 255, 0.3);
    }

    .search-group input {
        background: none;
        border: none;
        height: 45px;
    }

    /* ---- Product Table ---- */
    .modern-table-container {
        background: #ffffff;
        border-radius: 22px;
        padding: 0;
        box-shadow: 0 6px 26px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(230, 232, 236, 0.7);
        overflow: hidden;
    }

    .product-table thead tr {
        background: #f2f4f7;
        border-bottom: 1px solid #e1e4e8;
    }

    .product-table th {
        padding: 16px 14px;
        font-size: 13px;
        font-weight: 700;
        color: #6b7280;
        letter-spacing: 0.5px;
    }

    .product-table tbody tr {
        transition: 0.2s;
    }

    .product-table tbody tr:hover {
        background: #f8fafc;
    }

    /* ---- Product row ---- */
    .img-product-modern {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        object-fit: cover;
        margin-right: 12px;
        background: #eef0f3;
    }

    .product-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 15px;
    }

    .product-sku {
        color: #9ca3af;
        font-size: 12px;
    }

    /* ---- Stock Badge ---- */
    .stock-badge {
        padding: 6px 14px;
        border-radius: 14px;
        font-weight: 600;
        font-size: 14px;
    }

    tr[data-quantity="0"] .stock-badge {
        background: #ffeaea;
        color: #d9534f;
    }

    tr[data-quantity="1"] .stock-badge,
    tr[data-quantity="2"] .stock-badge,
    tr[data-quantity="3"] .stock-badge {
        background: #fff6da;
        color: #cfa400;
    }

    tr[data-quantity]:not([data-quantity="0"]):not([data-quantity="1"]):not([data-quantity="2"]):not([data-quantity="3"]) .stock-badge {
        background: #e7f8ef;
        color: #2f9d55;
    }

    /* ---- Rating ---- */
    .product-rating {
        background: #fff7dd;
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 14px;
        color: #f5a300;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }

    .product-rating i {
        color: #ffb400;
    }

    /* ---- Action Buttons ---- */
    .action-buttons-group {
        display: flex;
        gap: 10px;
    }

    .btn-action-icon {
        width: 38px;
        height: 38px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 12px;
        background: #f4f5f7;
        transition: transform .15s ease, background .2s;
        font-size: 1.2rem;
    }

    .btn-action-icon:hover {
        transform: scale(1.15);
        background: #ececec;
    }

    .btn {
        border-radius: 50px !important;
        padding: 8px 20px;
        font-weight: 600;
    }

    .btn-primary {
        background: #3b82f6;
        border: none;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-clear-search {
        cursor: pointer;
        color: #6c757d;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .btn-clear-search:hover {
        background-color: #f1f3f5;
        color: #212529;
    }
</style>



<?php
// PHP calculations for stats
$totalProducts = count($products);
$totalStock = 0;
$outOfStock = 0;
foreach ($products as $p) {
    $qty = $p->quantity ?? 0;
    $totalStock += $qty;
    if ($qty == 0) $outOfStock++;
}
?>

<div class="container-fluid p-4 main-admin-wrapper">
    <!-- Header Title Section -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <div class="d-flex align-items-start gap-2 border-start border-4 border-primary ps-3">
                <i class="bi bi-box-seam fs-4 text-primary mt-1"></i>
                <div>
                    <h4 class="mb-1 fw-semibold" style="color: #6b67d2ff;">Quản Lí Kho hàng</h4>
                    <small class=" text-muted">Quản lý và cập nhật trạng thái sản phẩm</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <div class="d-inline-flex align-items-center px-3 py-2 bg-white border rounded-pill shadow-sm">
                        <i class="bi bi-folder2-open text-primary me-2"></i>
                        <span class="fw-semibold text-dark" style="font-size: 20px;">
                            Danh mục: <?= htmlspecialchars($category->name ?? "Tất cả") ?>
                        </span>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <span class="badge bg-white text-dark border p-2 px-3 rounded-pill shadow-sm fw-semibold">
                <i class="bi bi-calendar3 me-2 text-primary"></i>
                Ngày <?= date('d/m/Y') ?>
            </span>
        </div>


    </div>

    <!-- Stat Cards Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card d-flex align-items-center">
                <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="text-muted">TỔNG SẢN PHẨM</div>
                    <h3 class="mb-0 fw-bold" id="totalProductsCount"><?= number_format($totalProducts) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card d-flex align-items-center">
                <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                    <i class="bi bi-stack"></i>
                </div>
                <div>
                    <div class="text-muted">TỒN KHO</div>
                    <h3 class="mb-0 fw-bold" id="totalStockCount"><?= number_format($totalStock) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card d-flex align-items-center border-start">
                <div class="icon-box bg-danger bg-opacity-10 me-3">
                    <i class="bi bi-exclamation-octagon"></i>
                </div>
                <div>
                    <div class="text-muted">HẾT HÀNG</div>
                    <h3 class="mb-0 fw-bold" id="outOfStockCount"><?= number_format($outOfStock) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar Section -->
    <div class="toolbar-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div style="flex: 0 0 40%; min-width: 300px;">
                <div style="flex: 0 0 40%; min-width: 300px;">
                    <div class="input-group search-group overflow-hidden shadow-none">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="text" id="quickSearchInput" class="form-control"
                            placeholder="Tìm kiếm sản phẩm nhanh...">

                        <!-- NÚT CLOSE -->
                        <span class="input-group-text btn-clear-search d-none" id="clearQuickSearch">
                            <i class="bi bi-x-lg"></i>
                        </span>
                    </div>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-white rounded-pill fw-bold d-flex align-items-center " style=" width: 200px;" data-bs-toggle="offcanvas" data-bs-target="#productFilterOffcanvas">
                    <i class="bi bi-funnel me-2"></i><span>Bộ lọc</span>
                </button>

                <a href="/webbanhang/Product/add"
                    class="btn btn-primary rounded-pill fw-bold shadow d-flex align-items-center justify-content-center">
                    <i class="bi bi-plus-lg me-1"></i>
                    <span>Thêm mới</span>
                </a>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/components/products_table_partial.php'; ?>
</div>

<?php require_once __DIR__ . '/components/product_filter.php'; ?>

<!-- JavaScript Section -->
<script>
    // Helper function to format numbers for display
    function formatNumber(num) {
        return num.toLocaleString('vi-VN');
    }

    // Function to update stat card counts
    function updateStatCounts(totalProductsChange, totalStockChange, outOfStockChange) {
        const totalProductsElem = document.getElementById('totalProductsCount');
        const totalStockElem = document.getElementById('totalStockCount');
        const outOfStockElem = document.getElementById('outOfStockCount');

        let currentTotalProducts = parseInt(totalProductsElem.textContent.replace(/[^0-9]/g, ''));
        let currentTotalStock = parseInt(totalStockElem.textContent.replace(/[^0-9]/g, ''));
        let currentOutOfStock = parseInt(outOfStockElem.textContent.replace(/[^0-9]/g, ''));

        totalProductsElem.textContent = formatNumber(currentTotalProducts + totalProductsChange);
        totalStockElem.textContent = formatNumber(currentTotalStock + totalStockChange);
        outOfStockElem.textContent = formatNumber(currentOutOfStock + outOfStockChange);
    }

    // Function to attach delete event listeners
    function attachDeleteEvents() {
        document.querySelectorAll('.btn-delete-product').forEach(function(button) {
            // Remove existing listener to prevent duplicates
            button.removeEventListener('click', handleDeleteProduct);
            button.addEventListener('click', handleDeleteProduct);
        });
    }

    // Handle product deletion
    function handleDeleteProduct() {
        const button = this;
        const productId = button.dataset.id;
        const row = button.closest('tr');
        const productQuantity = parseInt(row.dataset.quantity || '0');

        if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            fetch(`/webbanhang/Product/deleteAjax/${productId}`, {
                    method: 'DELETE',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        row.remove(); // Remove the row from the table

                        // Update stat counts
                        let outOfStockChange = 0;
                        if (productQuantity === 0) {
                            outOfStockChange = -1; // Decrement out of stock if the deleted product had 0 quantity
                        }
                        updateStatCounts(-1, -productQuantity, outOfStockChange);

                    } else {
                        alert('Xóa thất bại: ' + (data.message || 'Đã có lỗi xảy ra.'));
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi gửi yêu cầu xóa:', error);
                    alert('Không thể kết nối đến máy chủ.');
                });
        }
    }
    document.querySelectorAll('.filter-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const fieldset = this.closest('fieldset');
            const inputs = fieldset.querySelectorAll('.filter-input');
            inputs.forEach(input => {
                input.disabled = !this.checked;
                if (!this.checked) input.value = ''; // Xóa giá trị khi tắt
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const quickSearchInput = document.getElementById('quickSearchInput');
        const filterSearchInput = document.getElementById('searchInput'); // Assuming this exists in product_filter.php
        const filterForm = document.getElementById('filterForm'); // Assuming this exists in product_filter.php
        const productTableContainer = document.getElementById('product-table-container');
        const filterToggles = document.querySelectorAll('.filter-toggle'); // Assuming this exists in product_filter.php
        const resetBtn = document.getElementById('resetBtn'); // Assuming this exists in product_filter.php
        const categoryId = <?= json_encode($category_id) ?>; // Pass categoryId from PHP
        const clearQuickSearchBtn = document.getElementById('clearQuickSearch');

        // Sync quick search input with filter offcanvas search input

        // QUICK SEARCH LOGIC (GÕ → TÌM)
        if (quickSearchInput && filterSearchInput && filterForm) {
            quickSearchInput.addEventListener('input', function() {
                filterSearchInput.value = this.value;

                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    filterForm.dispatchEvent(new Event('submit'));
                }, 400); // debounce 400ms
            });

            // Sync ngược (khi gõ trong offcanvas filter)
            filterSearchInput.addEventListener('input', function() {
                quickSearchInput.value = this.value;

                if (this.value.trim() !== '') {
                    clearQuickSearchBtn.classList.remove('d-none');
                } else {
                    clearQuickSearchBtn.classList.add('d-none');
                }
            });
        }


        // Filter form submission logic
        if (filterForm && productTableContainer) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // productTableContainer.style.opacity = '0.5'; // Optional: fade effect
                const formData = new FormData(filterForm);
                const params = new URLSearchParams();
                for (const [key, value] of formData.entries()) {
                    if (value.trim() !== '') params.append(key, value.trim());
                }

                fetch(`/webbanhang/Admin/ajaxFilterProducts?category_id=${categoryId}&${params.toString()}`)
                    .then(res => res.text())
                    .then(html => {
                        productTableContainer.innerHTML = html;
                        // productTableContainer.style.opacity = '1'; // Optional: fade effect
                        attachDeleteEvents(); // Re-attach events for newly loaded content
                    })
                    .catch(err => {
                        alert('Lỗi kết nối.');
                        // productTableContainer.style.opacity = '1';
                    });
            });
        }

        // Reset filter button logic
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                filterForm.reset();
                filterToggles.forEach(cb => {
                    cb.checked = false;
                    // Assuming toggleFilterInputs makes inputs disabled if checkbox is unchecked
                    // This function should be defined if it's external or copied here
                    const fieldset = cb.closest('fieldset');
                    const inputs = fieldset.querySelectorAll('.filter-input');
                    inputs.forEach(input => input.disabled = true);
                });
                filterForm.dispatchEvent(new Event('submit')); // Trigger a new search
            });
        }

        // Initial attachment of delete events on page load
        attachDeleteEvents();
        // The toggleFilterInputs function needs to be available if resetBtn is used.
        // It's defined in the original script block, so it should be accessible.
    });
</script>

<?php include __DIR__ . '/../shares/footer.php'; ?>