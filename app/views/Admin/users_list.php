<?php include __DIR__ . '/../shares/header.php'; ?>
<?php include __DIR__ . '/../shares/navbar.php'; ?>

<!-- Custom CSS for a clean and modern look -->
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

    /* Nút tuỳ chỉnh */
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

    /* Ensure table-responsive has enough height for scroll */
    .user-list-table-container {
        max-height: 600px;
        /* Or a suitable height */
        overflow-y: auto;
    }
</style>


<div class="account-management-wrapper container my-5"> <!-- Đổi tên class 'can' và thêm Bootstrap container/margin -->

    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">

            <!-- Tiêu đề -->
            <div class="d-flex align-items-start gap-2 border-start border-4 border-primary ps-3">
                <i class="bi bi-box-seam fs-4 text-primary mt-1"></i>
                <div>
                    <h4 class="mb-1 fw-semibold" style="color: #6b67d2ff;">Quản Lý Khách Hàng</h4>
                    <small class="text-muted">Theo dõi, lọc và quản lý khách hàng</small>
                </div>
            </div>
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
                    data-bs-target="#userFilterOffcanvas"
                    <i class="bi bi-funnel-fill"></i>
                    Bộ lọc
                </button>
            </div>

        </div>

    </div>
    <div class="input-group search-group overflow-hidden shadow-sm position-relative mb-3"
        style="max-width:400px;">

        <span class="input-group-text bg-white border-0">
            <i class="bi bi-search text-primary"></i>
        </span>

        <input type="text"
            id="quickUserSearch"
            class="form-control border-0 shadow-none pe-5"
            placeholder="Tìm tên, email hoặc SĐT khách hàng">

        <span class="clear-search d-none" id="clearUserSearch">
            <i class="bi bi-x-circle-fill"></i>
        </span>
    </div>

    <!-- Offcanvas bộ lọc -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="userFilterOffcanvas" aria-labelledby="userFilterLabel">
        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle"
                    style="width:34px; height:34px;">
                    <i class="bi bi-funnel-fill"></i>
                </div>
                <h5 class="offcanvas-title fw-bold m-0" id="userFilterLabel">Bộ lọc người dùng</h5>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
        </div>
        <div class="offcanvas-body">
            <form action="/webbanhang/Admin/filterAccount" method="GET" id="filterForm">
                <!-- Search Name -->
                <fieldset class="filter-group">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="filterSearchName" <?= !empty($_GET['searchName']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="filterSearchName"><i class="bi bi-person me-2 text-secondary"></i> Tìm theo tên</label>
                    </div>
                    <input type="text" name="searchName" class="form-control form-control-sm filter-input" placeholder="Tên khách hàng" value="<?= htmlspecialchars($_GET['searchName'] ?? '') ?>" <?= empty($_GET['searchName']) ? 'disabled' : '' ?>>
                </fieldset>

                <!-- Search Email -->
                <fieldset class="filter-group">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="filterSearchEmail" <?= !empty($_GET['searchEmail']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="filterSearchEmail"><i class="bi bi-envelope me-2 text-secondary"></i> Tìm theo Email</label>
                    </div>
                    <input type="email" name="searchEmail" class="form-control form-control-sm filter-input" placeholder="Email" value="<?= htmlspecialchars($_GET['searchEmail'] ?? '') ?>" <?= empty($_GET['searchEmail']) ? 'disabled' : '' ?>>
                </fieldset>

                <!-- Search Phone -->
                <fieldset class="filter-group">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="filterSearchPhone" <?= !empty($_GET['searchPhone']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="filterSearchPhone"><i class="bi bi-phone me-2 text-secondary"></i> Tìm theo Số điện thoại</label>
                    </div>
                    <input type="text" name="searchPhone" class="form-control form-control-sm filter-input" placeholder="Số điện thoại" value="<?= htmlspecialchars($_GET['searchPhone'] ?? '') ?>" <?= empty($_GET['searchPhone']) ? 'disabled' : '' ?>>
                </fieldset>

                <!-- Search Address -->
                <fieldset class="filter-group">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="filterSearchAddress" <?= !empty($_GET['searchAddress']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="filterSearchAddress"><i class="bi bi-geo-alt me-2 text-secondary"></i> Tìm theo Địa chỉ</label>
                    </div>
                    <input type="text" name="searchAddress" class="form-control form-control-sm filter-input" placeholder="Địa chỉ" value="<?= htmlspecialchars($_GET['searchAddress'] ?? '') ?>" <?= empty($_GET['searchAddress']) ? 'disabled' : '' ?>>
                </fieldset>

                <!-- Filter Birth Date -->
                <fieldset class="filter-group">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="filterBirthDate" <?= (!empty($_GET['birthDateFrom']) || !empty($_GET['birthDateTo'])) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="filterBirthDate"><i class="bi bi-calendar-date me-2 text-secondary"></i> Lọc ngày sinh (Từ - Đến)</label>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="date" name="birthDateFrom" class="form-control form-control-sm filter-input" value="<?= htmlspecialchars($_GET['birthDateFrom'] ?? '') ?>" <?= (empty($_GET['birthDateFrom']) && empty($_GET['birthDateTo'])) ? 'disabled' : '' ?>>
                        <input type="date" name="birthDateTo" class="form-control form-control-sm filter-input" value="<?= htmlspecialchars($_GET['birthDateTo'] ?? '') ?>" <?= (empty($_GET['birthDateFrom']) && empty($_GET['birthDateTo'])) ? 'disabled' : '' ?>>
                    </div>
                </fieldset>

                <!-- Filter Last Login -->
                <fieldset class="filter-group">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="filterLastLogin" <?= !empty($_GET['lastLoginDays']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="filterLastLogin"><i class="bi bi-clock-history me-2 text-secondary"></i> Lọc đăng nhập lần cuối (Số ngày gần nhất)</label>
                    </div>
                    <input type="number" name="lastLoginDays" id="lastLoginDays" class="form-control form-control-sm filter-input" min="1" placeholder="Nhập số ngày" value="<?= htmlspecialchars($_GET['lastLoginDays'] ?? '') ?>" <?= empty($_GET['lastLoginDays']) ? 'disabled' : '' ?>>
                </fieldset>

                <!-- Filter Status -->
                <fieldset class="filter-group">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input filter-toggle shadow-none" id="filterStatus" <?= isset($_GET['status']) && $_GET['status'] !== '' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="filterStatus"><i class="bi bi-toggle-on me-2 text-secondary"></i> Lọc theo trạng thái tài khoản</label>
                    </div>
                    <select class="form-select form-select-sm filter-input" name="status" <?= !isset($_GET['status']) || $_GET['status'] === '' ? 'disabled' : '' ?>>
                        <option value="">-- Tất cả --</option>
                        <option value="1" <?= (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : '' ?>>Không hoạt động</option>
                    </select>
                </fieldset>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn-custom">
                        <i class="bi bi-check2-circle me-2"></i>Áp dụng bộ lọc
                    </button>
                    <button type="button" class="btn-reset" id="resetFilter">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Đặt lại tất cả
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Phần danh sách người dùng -->
    <div class="shadow-lg rounded-3 border overflow-hidden"> <!-- Bọc bảng trong một khối có bóng đổ và bo góc -->
        <div class="card-header bg-light py-3 px-4">
            <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-primary"></i> Danh sách người dùng</h5>
        </div>
        <div class="card-body p-0"> <!-- p-0 để loại bỏ padding của card-body mặc định -->
            <div class="table-responsive user-list-table-container"> <!-- Thêm class user-list-table-container -->
                <?php if ($users && count($users) > 0): ?>
                    <?php require_once __DIR__ . '/components/users_table.php'; ?>
                <?php else: ?>
                    <p class="text-center text-muted fst-italic py-4 mb-0">
                        <i class="bi bi-info-circle me-2"></i> Không có người dùng nào phù hợp với bộ lọc.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        /* ========================
            GLOBAL VARIABLES
        ======================== */
        const filterForm = document.getElementById('filterForm');
        const userFilterOffcanvas = document.getElementById('userFilterOffcanvas');
        const userTableContainer = document.querySelector('.user-list-table-container'); // Corrected selector
        const resetFilterBtn = document.getElementById('resetFilter');
        const quickUserSearch = document.getElementById('quickUserSearch');
        const clearUserSearch = document.getElementById('clearUserSearch');
        let userSearchTimeout = null;

        if (quickUserSearch && clearUserSearch) {

            quickUserSearch.addEventListener('input', function() {
                const keyword = this.value.trim();

                clearUserSearch.classList.toggle('d-none', keyword.length === 0);

                clearTimeout(userSearchTimeout);
                userSearchTimeout = setTimeout(() => {

                    fetch('/webbanhang/Admin/ajaxFilterUsers?keyword=' + encodeURIComponent(keyword), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.text())
                        .then(html => {
                            document.querySelector('.user-list-table-container').innerHTML = html;
                        });

                }, 400);
            });

            clearUserSearch.addEventListener('click', function() {
                quickUserSearch.value = '';
                clearUserSearch.classList.add('d-none');

                fetch('/webbanhang/Admin/ajaxFilterUsers', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        document.querySelector('.user-list-table-container').innerHTML = html;
                    });
            });
        }

        /* ========================
            TOGGLE FILTER CONTROLS
        ======================== */
        document.querySelectorAll('.filter-toggle').forEach(function(checkbox) {
            const fieldset = checkbox.closest('fieldset');
            const inputs = fieldset ? fieldset.querySelectorAll('.filter-input') : [];

            // Initial state based on PHP pre-fill
            inputs.forEach(input => {
                // For date inputs with 'From - To', check if either has a value
                if (fieldset && (fieldset.id === 'filterBirthDate')) {
                    const birthDateFrom = fieldset.querySelector('[name="birthDateFrom"]');
                    const birthDateTo = fieldset.querySelector('[name="birthDateTo"]');
                    checkbox.checked = !!(birthDateFrom.value || birthDateTo.value);
                }
                // For status select, check if a specific option is selected (not '-- Tất cả --')
                else if (fieldset && fieldset.id === 'filterStatus') {
                    const statusSelect = fieldset.querySelector('select[name="status"]');
                    checkbox.checked = (statusSelect.value !== '' && statusSelect.value !== null);
                }
                // For other inputs, check if they have a value
                else {
                    checkbox.checked = !!input.value;
                }
                input.disabled = !checkbox.checked;
            });

            checkbox.addEventListener('change', function() {
                inputs.forEach(input => {
                    input.disabled = !this.checked;
                    if (!this.checked) {
                        if (input.tagName.toLowerCase() === 'select') {
                            input.selectedIndex = 0; // Reset select to first option
                        } else {
                            input.value = ''; // Clear input value
                        }
                    }
                });
            });
        });


        /* ========================
            AJAX FILTER SUBMISSION
        ======================== */
        if (filterForm) {
            filterForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                let formData = new URLSearchParams(new FormData(this));

                // Show a loading indicator (optional, but good for UX)
                if (userTableContainer) {
                    userTableContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div><p class="mt-2 text-muted">Đang tải danh sách người dùng...</p></div>';
                }

                fetch('/webbanhang/Admin/ajaxFilterUsers?' + formData.toString(), {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (userTableContainer) {
                            userTableContainer.innerHTML = data; // Update table content
                        }
                        // Optionally close the offcanvas after filter
                        const bsOffcanvas = bootstrap.Offcanvas.getInstance(userFilterOffcanvas);
                        if (bsOffcanvas) bsOffcanvas.hide();
                    })
                    .catch(error => console.error('Lỗi khi tải dữ liệu:', error));
            });
        }

        /* ========================
            RESET FILTER
        ======================== */
        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default action

                // Uncheck all filter toggles and dispatch change event to disable/clear inputs
                document.querySelectorAll('.filter-toggle').forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.dispatchEvent(new Event('change'));
                });

                // Manually clear specific inputs that might not be covered by toggle logic (e.g., date ranges, selects with default option)
                document.querySelectorAll('.filter-input').forEach(input => {
                    if (input.tagName.toLowerCase() === 'select') {
                        input.selectedIndex = 0;
                    } else {
                        input.value = '';
                    }
                });

                // Submit an empty form to get all users
                filterForm.submit(); // This will trigger the AJAX submit handler above
            });
        }


        /* ========================
            TOGGLE USER STATUS (from users_table.php)
        ======================== */
        document.addEventListener("change", function(event) {
            if (event.target.classList.contains("toggle-status")) {
                const userId = event.target.getAttribute("data-user-id");
                const isActive = event.target.checked ? 1 : 0;

                fetch("/webbanhang/Admin/updateUserStatus", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `user_id=${userId}&status=${isActive}`,
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Phản hồi từ server:", data);
                        if (!data.success) {
                            alert("Cập nhật trạng thái thất bại! Lỗi: " + (data.message || "Không rõ nguyên nhân"));
                            // Revert toggle state if update failed
                            event.target.checked = !isActive;
                        }
                    })
                    .catch(error => {
                        console.error("Lỗi khi gửi yêu cầu:", error);
                        alert("Có lỗi xảy ra khi gửi dữ liệu!");
                        // Revert toggle state if request failed
                        event.target.checked = !isActive;
                    });
            }
        });

        /* ========================
            TABLE ROW CLICK TO USER DETAILS
        ======================== */
        document.addEventListener("click", function(event) {
            const row = event.target.closest(".user-row");
            if (!row) return;

            // ❌ Không redirect nếu click vào toggle hoặc nút lịch sử
            if (
                event.target.classList.contains("toggle-status") ||
                event.target.closest(".toggle-status") ||
                event.target.closest(".view-history")
            ) {
                return;
            }

            const userId = row.dataset.userId;

            if (!userId || isNaN(userId)) {
                alert("ID khách hàng không hợp lệ");
                return;
            }

            window.location.href = `/webbanhang/Admin/userDetails?user_id=${userId}`;
        });



    });
</script>

<?php include __DIR__ . '/../shares/footer.php'; ?>