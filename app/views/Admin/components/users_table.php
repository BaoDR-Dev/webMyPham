<style>
    /* Tổng thể container */
    .user-management-wrapper {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }

    /* Header của trang */
    .page-header {
        margin-bottom: 2.5rem;
        /* Khoảng cách lớn hơn giữa tiêu đề và bảng */
    }

    .page-header h4 {
        color: #343a40;
        /* Màu chữ đậm hơn cho tiêu đề chính */
        font-weight: 700;
        /* Rất đậm */
    }

    /* Wrapper cho bảng, tạo hiệu ứng card */
    .table-card {
        background-color: #fff;
        border-radius: 0.75rem;
        /* Bo góc nhẹ */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        /* Bóng đổ nhẹ nhàng */
        overflow: hidden;
        /* Đảm bảo nội dung không tràn ra khỏi góc bo tròn */
    }

    /* Bảng */
    .user-table {
        width: 100%;
        border-collapse: separate;
        /* Quan trọng để border-spacing hoạt động */
        border-spacing: 0 0.5rem;
        /* Tạo khoảng cách 8px giữa các hàng */
        margin-bottom: 0;
        /* Loại bỏ margin-bottom mặc định của table */
        --bs-table-bg: transparent;
        /* Đặt nền bảng trong suốt để thấy nền của table-card */
    }

    /* Tiêu đề bảng */
    .user-table thead {
        background-color: #f8f9fa;
        /* Nền xám nhạt cho tiêu đề */
        border-bottom: 1px solid #e9ecef;
        /* Đường viền dưới cho thead */
    }

    .user-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6b7280;
        /* Màu chữ xám mờ */
        font-weight: 700;
        /* Nửa đậm */
        padding: 1rem 1.25rem;
        /* Khoảng cách rộng rãi */
        border-top: none;
        /* Loại bỏ border-top mặc định */
        border-bottom: none;
        /* Loại bỏ border-bottom mặc định của Bootstrap */
    }

    /* Các ô dữ liệu */
    .user-table td {
        background-color: #fff;
        /* Nền trắng cho mỗi ô */
        padding: 1rem 1.25rem;
        /* Khoảng cách nhất quán */
        vertical-align: middle;
        font-size: 0.875rem;
        /* Cỡ chữ dễ đọc */
        color: #495057;
        /* Màu chữ hơi đậm */
        border-top: 1px solid #e9ecef;
        /* Đường phân cách trên cho mỗi hàng */
        border-bottom: 1px solid #e9ecef;
        /* Đường phân cách dưới cho mỗi hàng */
        transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    /* Bo góc cho hàng đầu tiên và cuối cùng của tbody */
    .user-table tbody tr:first-child td {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    .user-table tbody tr:last-child td {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    /* Xóa border-top cho hàng đầu tiên để tránh đường viền kép */
    .user-table tbody tr:first-child td {
        border-top: none;
    }

    /* Hiệu ứng hover cho hàng */
    .user-row {
        cursor: pointer;
        outline: none;
        /* Loại bỏ outline khi focus */
    }

    .user-row:hover {
        background-color: #f8f9fa;
        /* Nền sáng hơn khi hover */
        transform: translateY(-2px);
        /* Nhấc nhẹ hàng lên */
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        /* Bóng đổ mạnh hơn khi hover */
        z-index: 1;
        /* Đảm bảo bóng đổ hiển thị trên các hàng khác */
        position: relative;
        /* Cần thiết cho z-index hoạt động */
    }

    /* ID */
    .user-table .id-col {
        font-weight: 600;
        color: var(--bs-primary);
        /* Màu xanh chính của Bootstrap */
    }

    /* Tên khách hàng */
    .user-table .name-col {
        font-weight: 600;
        color: #212529;
        /* Màu chữ rất đậm */
    }

    /* Địa chỉ */
    .user-table .address-col {
        white-space: normal;
        /* Cho phép xuống dòng */
        min-width: 150px;
        /* Đảm bảo đủ rộng */
    }

    /* Phone */
    .user-table .phone-col {
        font-weight: 600;
        color: #495057;
    }

    /* Switch đẹp hơn */
    .form-switch .form-check-input {
        cursor: pointer;
        width: 2.25em;
        /* Chiều rộng chuẩn của Bootstrap 5 switch */
        height: 1.25em;
        /* Chiều cao chuẩn */
        margin-left: -1.75em;
        /* Căn chỉnh vị trí */
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
        border-color: rgba(0, 0, 0, .25);
    }

    .form-switch .form-check-input:checked {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        background-color: var(--bs-success);
        /* Màu xanh lá khi bật */
        border-color: var(--bs-success);
    }

    .form-switch .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-success-rgb), .25);
        /* Bóng khi focus */
    }

    /* Nút lịch sử mua */
    .view-history {
        color: var(--bs-primary);
        /* Màu chữ xanh chính */
        border-color: var(--bs-primary);
        /* Viền màu xanh chính */
        background-color: transparent;
        /* Nền trong suốt */
        transition: all 0.2s ease;
        padding: 0.5rem 0.75rem;
        /* Kích thước nút */
        border-radius: 0.5rem;
        /* Bo góc nút */
    }

    .view-history:hover {
        background-color: var(--bs-primary);
        /* Nền xanh khi hover */
        color: #fff;
        /* Chữ trắng khi hover */
        box-shadow: 0 2px 8px rgba(var(--bs-primary-rgb), 0.2);
        /* Bóng nhẹ khi hover */
    }
</style>

<div class="user-management-wrapper container">


    <!-- Wrapper cho bảng -->
    <div class="table-card">
        <table class="table user-table align-middle">
            <thead>
                <tr>
                    <th style="width:70px;">ID</th>
                    <th>Khách hàng</th>
                    <th>Email</th>
                    <th>Địa chỉ</th>
                    <th>SĐT</th>
                    <th>Đăng nhập cuối</th>
                    <th class="text-center" style="width:120px;">Trạng thái</th>
                    <th class="text-center" style="width:140px;">Lịch sử mua</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="user-row" data-user-id="<?= $user->id ?>">
                        <td class="id-col">#<?= $user->id ?></td>

                        <td class="name-col">
                            <?= htmlspecialchars($user->full_name ?? 'Chưa cung cấp') ?>
                        </td>

                        <td class="text-muted text-break">
                            <?= htmlspecialchars($user->email ?? '---') ?>
                        </td>

                        <td class="address-col text-muted">
                            <?= htmlspecialchars($user->address ?? '---') ?>
                        </td>

                        <td class="phone-col">
                            <?= htmlspecialchars($user->phone_number ?? '---') ?>
                        </td>

                        <td class="text-muted small">
                            <?= $user->last_login
                                ? date('d/m/Y H:i', strtotime($user->last_login))
                                : '---' ?>
                        </td>

                        <td class="text-center">
                            <div class="form-check form-switch d-inline-flex justify-content-center">
                                <input class="form-check-input toggle-status"
                                    type="checkbox"
                                    role="switch"
                                    data-user-id="<?= $user->id ?>"
                                    <?= $user->is_active ? 'checked' : '' ?>>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="/webbanhang/Admin/userDetails?user_id=<?= $user->id ?>"
                                class="btn btn-sm btn-outline-primary view-history"
                                title="Xem lịch sử mua hàng">
                                <i class="bi bi-clock-history"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div> <!-- End table-card -->
</div> <!-- End container -->