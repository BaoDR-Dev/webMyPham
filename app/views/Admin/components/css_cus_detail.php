<style>
    /* Google Fonts - bạn có thể thêm vào header.php nếu muốn */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap');

    :root {
        /* Định nghĩa lại một số biến màu nếu muốn tùy chỉnh sâu hơn Bootstrap, ví dụ: */
        --bs-primary: #007bff;
        /* Bootstrap primary */
        --bs-primary-rgb: 0, 123, 255;
        --bs-primary-dark: #0056b3;
        /* A darker shade of primary */
        --bs-primary-light: #e6f2ff;
        /* Very light primary for backgrounds */

        --bs-secondary: #6c757d;
        /* Bootstrap secondary */
        --bs-secondary-rgb: 108, 117, 125;
        --bs-secondary-light: #f1f2f3;
        /* Light secondary for backgrounds */


        --bs-success: #28a745;
        --bs-success-rgb: 40, 167, 69;
        --bs-success-bg-subtle: #d4edda;
        /* Using Bootstrap's actual subtle vars if available, or custom */
        --bs-success-text-emphasis: #155724;

        --bs-info: #17a2b8;
        /* Bootstrap info */
        --bs-info-rgb: 23, 162, 184;
        --bs-info-bg-subtle: #d1ecf1;
        --bs-info-text-emphasis: #0c5460;


        --bs-warning: #ffc107;
        /* Bootstrap warning */
        --bs-warning-rgb: 255, 193, 7;
        --bs-warning-bg-subtle: #fff3cd;
        --bs-warning-text-emphasis: #856404;


        --bs-danger: #dc3545;
        /* Bootstrap danger */
        --bs-danger-rgb: 220, 53, 69;
        --bs-danger-bg-subtle: #f8d7da;
        --bs-danger-text-emphasis: #721c24;


        --bs-dark: #343a40;
        /* Bootstrap dark */
        --bs-dark-rgb: 52, 58, 64;
        --bs-dark-bg-subtle: #d6d8d9;
        --bs-dark-text-emphasis: #1d2124;


        --bs-white: #fff;
        --bs-light: #f8f9fa;
        --bs-gray-100: #f8f9fa;
        --bs-gray-200: #e9ecef;
        --bs-gray-300: #dee2e6;
        --bs-gray-400: #ced4da;
        --bs-border-color-subtle: #e9ecef;
        /* Softer border */
        --bs-border-color-translucent: rgba(0, 0, 0, 0.08);
        /* Even softer for dashed lines */

        /* Shadows for depth */
        --soft-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        --hover-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bs-gray-100);
        color: var(--bs-body-color);
        line-height: 1.6;
    }

    /* Tổng thể container */
    .detail-page-wrapper {
        padding-top: 3.5rem;
        padding-bottom: 4rem;
    }

    /* Nút Quay lại */
    /* Đã tinh chỉnh để trông nhẹ nhàng hơn và căn chỉnh bằng flexbox */
    .btn-back-custom {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
        color: var(--bs-secondary);
        background-color: var(--bs-white);
        border: 1px solid var(--bs-border-color-subtle);
        border-radius: 0.5rem;
        transition: all 0.25s ease;
        text-decoration: none;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        /* Bóng đổ rất nhẹ */
    }

    .btn-back-custom:hover {
        background-color: var(--bs-gray-100);
        color: var(--bs-primary);
        border-color: var(--bs-gray-300);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    /* Header của trang */
    .page-main-header {
        /* margin-bottom đã được di chuyển lên wrapper cha */
        border-left: 6px solid var(--bs-primary);
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        background-color: var(--bs-white);
        border-radius: 0.85rem;
        box-shadow: var(--soft-shadow);
        flex-grow: 1;
        /* Cho phép header mở rộng trong flex container */
    }

    .page-main-header .title-text {
        font-size: 1.9rem;
        font-weight: 700;
        /* Đã đổi màu tiêu đề sang primary để rực rỡ hơn */
        color: var(--bs-primary);
        /* CHANGED: Brighter title color */
        margin-bottom: 0.35rem;
        font-family: 'Poppins', sans-serif;
    }

    .page-main-header .subtitle-text {
        font-size: 1rem;
        color: var(--bs-secondary);
        display: block;
    }

    .page-main-header i {
        font-size: 2.5rem !important;
        color: var(--bs-primary) !important;
    }

    /* Khối thông tin chung (Card nhẹ nhàng) */
    .simple-card {
        background-color: var(--bs-white);
        border: 1px solid var(--bs-border-color-subtle);
        border-radius: 0.85rem;
        box-shadow: var(--card-shadow);
        height: 100%;
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }

    .simple-card:hover {
        box-shadow: var(--soft-shadow);
    }

    .simple-card .card-header-light {
        padding: 1.1rem 1.75rem;
        border-bottom: 1px solid var(--bs-border-color-subtle);
        /* Thêm một chút màu nền để nổi bật */
        background-color: var(--bs-primary-light);
        /* CHANGED: Added subtle primary background */
        font-weight: 600;
        /* Đổi màu chữ cho phù hợp với nền mới */
        color: var(--bs-primary-dark);
        /* CHANGED: Darker primary text */
        font-size: 1.1rem;
        font-family: 'Poppins', sans-serif;
    }

    .simple-card .card-body-default {
        padding-right: 1.2rem;
        padding-left: 1.2rem;
    }

    /* Thông tin khách hàng */
    .customer-detail-list .list-group-item {
        background-color: transparent;
        border: none;
        padding: 0.9rem 0;
        font-size: 0.98rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        /* CHANGED: Solid border for clarity, still subtle */
        border-bottom: 1px solid var(--bs-border-color-subtle);
    }

    .customer-detail-list .list-group-item:last-of-type {
        border-bottom: none !important;
    }

    .customer-detail-list .list-group-item strong {
        /* CHANGED: Labels are now primary color */
        color: var(--bs-primary);
        font-weight: 600;
        min-width: 140px;
        flex-shrink: 0;
    }

    .customer-detail-list .list-group-item span,
    .customer-detail-list .list-group-item .value-text {
        color: var(--bs-body-color);
        text-align: right;
        flex-grow: 1;
        margin-left: 1.5rem;
        word-break: break-word;
    }

    .customer-detail-list .list-group-item .value-text.address-text {
        color: var(--bs-secondary);
        font-size: 0.9rem;
        text-align: left;
        margin-left: 0;
        padding-top: 0.3rem;
    }

    .customer-detail-list .list-group-item.flex-column.align-items-start strong {
        margin-bottom: 0.6rem;
    }


    /* Tổng quan đơn hàng */
    .overview-card-body {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        height: 100%;
        align-content: center;
    }

    @media (min-width: 768px) {
        .overview-card-body {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 992px) {
        .overview-card-body {
            grid-template-columns: 1fr;
        }
    }

    @media (min-width: 1200px) {
        .overview-card-body {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .overview-stat-item {
        /* Nền nhẹ nhàng cho từng stat, đã có màu sắc theo trạng thái */
        padding: 1rem 1.25rem;
        border-radius: 0.6rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-weight: 600;
        border: 1px solid;
        /* Border sẽ lấy màu từ class --bg-*-subtle */
        /* Màu chữ và nền được định nghĩa bởi các class bg-*-subtle */
    }

    /* Specific colors for each stat item (already defined by Bootstrap's subtle classes) */
    .overview-stat-item.total-orders-stat {
        background-color: var(--bs-success-bg-subtle);
        color: var(--bs-success-text-emphasis);
        border-color: var(--bs-success-border-subtle);
    }

    .overview-stat-item.total-spent-stat {
        background-color: var(--bs-info-bg-subtle);
        color: var(--bs-info-text-emphasis);
        border-color: var(--bs-info-border-subtle);
    }

    .overview-stat-item.last-order-stat {
        background-color: var(--bs-warning-bg-subtle);
        color: var(--bs-warning-text-emphasis);
        border-color: var(--bs-warning-border-subtle);
    }

    .overview-stat-item.completed-orders-stat {
        /* Đã đổi tên class cho phù hợp */
        background-color: var(--bs-secondary-bg-subtle);
        color: var(--bs-secondary-text-emphasis);
        border-color: var(--bs-secondary-border-subtle);
    }

    .overview-stat-item .stat-icon {
        font-size: 2rem;
        /* Slightly larger */
        opacity: 1;
        /* CHANGED: Full color icon */
        /* Màu icon sẽ tự động kế thừa từ cha (.overview-stat-item) */
    }

    .overview-stat-item .stat-value {
        font-size: 1rem;
        /* Kích thước số liệu lớn hơn một chút */
        font-weight: 700;
        margin-bottom: 0.2rem;
        line-height: 1;
    }

    .overview-stat-item .stat-label {
        font-size: 0.9rem;
        /* Lớn hơn một chút */
        font-weight: 500;
        opacity: 0.9;
        margin-bottom: 0;
    }


    /* Bảng đơn hàng */
    .order-table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
        --bs-table-bg: var(--bs-white);
        --bs-table-hover-bg: var(--bs-gray-50);
        border-radius: 0.85rem;
        overflow: hidden;
    }

    .order-table-custom thead th {
        background-color: var(--bs-dark);
        color: var(--bs-white);
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-weight: 700;
        padding: 1.1rem 1.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        border-top: none;
    }

    .order-table-custom tbody tr {
        transition: background-color 0.2s ease;
    }

    .order-table-custom tbody tr:hover {
        background-color: var(--bs-table-hover-bg);
    }

    .order-table-custom td {
        background-color: var(--bs-white);
        padding: 1.3rem 1.75rem;
        vertical-align: middle;
        font-size: 0.9rem;
        color: var(--bs-body-color);
        border-bottom: 1px solid var(--bs-border-color-subtle);
    }

    .order-table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    /* Trạng thái đơn hàng (Select) - Đã tinh chỉnh để trông như tag */
    .order-status-select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: none;
        border: none;
        background-color: transparent;
        font-weight: 600;
        padding: 0.45rem 0.9rem;
        border-radius: 0.5rem;
        text-align-last: center;
        -moz-text-align-last: center;
        transition: all 0.2s ease;
        line-height: 1.2;
        cursor: pointer;
        outline: none;
        min-width: 120px;
        display: inline-block;
    }

    .order-status-select:hover {
        opacity: 0.85;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .order-status-select:focus {
        box-shadow: 0 0 0 0.15rem rgba(var(--bs-primary-rgb), .25);
    }

    /* Màu sắc động cho select theo trạng thái */
    .order-status-select.bg-secondary-subtle {
        background-color: var(--bs-secondary-bg-subtle) !important;
        color: var(--bs-secondary-text-emphasis) !important;
    }

    .order-status-select.bg-info-subtle {
        background-color: var(--bs-info-bg-subtle) !important;
        color: var(--bs-info-text-emphasis) !important;
    }

    .order-status-select.bg-success-subtle {
        background-color: var(--bs-success-bg-subtle) !important;
        color: var(--bs-success-text-emphasis) !important;
    }

    .order-status-select.bg-danger-subtle {
        background-color: var(--bs-danger-bg-subtle) !important;
        color: var(--bs-danger-text-emphasis) !important;
    }

    .order-status-select.bg-dark-subtle {
        background-color: var(--bs-dark-bg-subtle) !important;
        color: var(--bs-dark-text-emphasis) !important;
    }

    /* Đảm bảo màu text option trong select vẫn rõ ràng */
    .order-status-select option {
        color: var(--bs-body-color);
        background-color: var(--bs-white);
    }


    /* Phương thức thanh toán (Badges) */
    .payment-badge {
        font-weight: 500;
        padding: 0.45em 0.8em;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        line-height: 1;
    }

    /* Nút chi tiết */
    .btn-detail-order-custom {
        font-size: 0.85rem;
        padding: 0.5rem 1.1rem;
        border-radius: 0.55rem;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .btn-detail-order-custom:hover {
        box-shadow: 0 2px 10px rgba(var(--bs-primary-rgb), 0.15);
        transform: translateY(-1px);
    }

    /* Thông báo rỗng / lỗi */
    .empty-state-message {
        padding: 3.5rem 2rem;
        text-align: center;
        color: var(--bs-secondary);
        font-size: 1.05rem;
        background-color: var(--bs-white);
        border-radius: 0.85rem;
        box-shadow: var(--card-shadow);
        margin-top: 2rem;
    }

    .empty-state-message i {
        font-size: 3.2rem !important;
        margin-bottom: 1.8rem !important;
        color: var(--bs-gray-400) !important;
    }


    .alert-custom {
        border-radius: 0.85rem;
        padding: 1.75rem 2.25rem;
        font-size: 1.05rem;
        border: none;
        box-shadow: var(--soft-shadow);
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .alert-danger-custom {
        background-color: var(--bs-danger-bg-subtle);
        color: var(--bs-danger-text-emphasis);
    }

    .alert-danger-custom i {
        font-size: 1.6rem;
        color: var(--bs-danger);
    }
</style>