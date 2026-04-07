<?php include __DIR__ . '/../shares/header.php'; ?>
<?php include __DIR__ . '/../shares/navbar.php'; ?>

<!-- Custom CSS for a modern, sophisticated, and economical look -->
<style>
    /* General Body & Typography */
    body {
        background-color: #f8f9fa;
        /* Rất nhẹ, gần như trắng, tạo cảm giác sạch sẽ */
        font-family: 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        /* Font hiện đại, dễ đọc */
        color: #343a40;
        /* Màu chữ đen xám đậm */
        line-height: 1.6;
    }

    h2.text-center {
        color: #212529;
        /* Màu tiêu đề chính */
        font-weight: 700;
        /* Tiêu đề đậm rõ ràng */
        margin-bottom: 3rem !important;
        /* Thêm khoảng cách dưới tiêu đề */
        letter-spacing: -0.02em;
        /* Giảm khoảng cách chữ một chút */
    }



    /* Card Styling */
    .carddb {
        border: none;
        /* Bỏ đường viền mặc định */
        border-radius: 12px;
        /* Góc bo tròn lớn hơn, mềm mại hơn */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        /* Đổ bóng nhẹ nhàng, có chiều sâu */
        transition: transform 0.2s ease, box-shadow 0.2s ease;

        /* Hiệu ứng khi hover */

    }

    .carddb:hover {
        transform: translateY(-2px);
        /* Nhấc nhẹ card lên khi hover */
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        /* Đổ bóng đậm hơn khi hover */
    }

    .carddb-header {
        background-color: #ffffff;
        /* Nền header trắng */
        border-bottom: 1px solid #e9ecef;
        /* Đường kẻ phân tách nhẹ */
        padding: 1.25rem 1.5rem;
        /* Padding thoải mái */
        font-weight: 600;
        /* Chữ đậm hơn */
        color: #495057;
        /* Màu chữ header */
        border-top-left-radius: 12px;
        /* Đồng bộ góc bo tròn */
        border-top-right-radius: 12px;
        /* Đồng bộ góc bo tròn */
    }

    .carddb-header h4,
    .carddb-header h5 {
        margin-bottom: 0;
        /* Loại bỏ margin dưới tiêu đề trong header */
        font-size: 1.15rem;
        /* Kích thước chữ tiêu đề header */
        color: #212529;
    }

    /* Để căn chỉnh nút Excel */
    .carddb-header.d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }


    .carddb-body {
        padding: 1.5rem;
        /* Padding thống nhất */
    }

    /* Sidebar Navigation */
    .list-group-flush .list-group-item {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        /* Đường phân cách mờ giữa các mục */
    }

    .list-group-flush .list-group-item:last-child {
        border-bottom: none;
        /* Bỏ đường viền dưới cùng */
    }

    .list-group-item {
        color: #495057;
        /* Màu chữ mặc định */
        font-weight: 500;
        padding: 1rem 1.5rem;
        /* Padding thoải mái */
        transition: background-color 0.2s ease, color 0.2s ease, border-left-color 0.2s ease;
        border-left: 4px solid transparent;
        /* Tạo không gian cho đường active */
    }

    .list-group-item:hover {
        background-color: #e9f2ff;
        /* Màu nền xanh nhạt khi hover */
        color: #0056b3;
        /* Màu chữ xanh đậm hơn */
        cursor: pointer;
    }

    .list-group-item.active {
        background-color: #007bff;
        /* Màu nền chính cho mục active */
        color: #fff;
        /* Chữ trắng */
        font-weight: 600;
        border-left-color: #0056b3;
        /* Đường viền bên trái nổi bật */
        border-radius: 0;
        /* Đảm bảo đường viền trái hiển thị rõ */
    }

    /* Để đảm bảo active state trên mobile vẫn đẹp */
    @media (max-width: 767.98px) {
        .list-group-item.active {
            border-left: none;
            /* Trên mobile không dùng border-left */
            border-radius: 0.75rem;
            /* Bo tròn các góc trên mobile */
        }
    }


    /* Form Styling */
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
        /* Khoảng cách dưới label */
    }

    .form-control {
        border-radius: 8px;
        /* Bo tròn input */
        border: 1px solid #ced4da;
        /* Viền nhẹ */
        padding: 0.75rem 1rem;
        /* Padding lớn hơn */
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);
        /* Bóng nhẹ bên trong */
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        /* Bóng xanh khi focus */
    }

    .btn-primary {
        background-color: #007bff;
        /* Màu xanh chính */
        border-color: #007bff;
        border-radius: 8px;
        /* Bo tròn button */
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        /* Padding lớn hơn */
        transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        /* Màu xanh đậm hơn khi hover */
        border-color: #004d9c;
    }

    .btn-primary:focus {
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.5);
    }

    /* Chart Specifics */
    canvas {
        max-width: 100%;
        height: auto !important;
        /* Đảm bảo Chart.js có thể tự điều chỉnh chiều cao */
    }

    /* Spacing adjustments */
    .container.mt-5 {
        margin-top: 3.5rem !important;
        /* Điều chỉnh lại khoảng cách trên cùng */
        margin-bottom: 3.5rem !important;
        /* Thêm khoảng cách dưới cùng */
    }
</style>


<div class="d-flex align-items-start gap-2 border-start border-4 border-primary ps-3" style="margin-top:30px; margin-bottom:20px; margin-left:15px;">
    <i class="bi bi-box-seam fs-4 text-primary mt-1"></i>
    <div>
        <h4 class="mb-1 fw-semibold" style="color: #6b67d2ff;">Thống Kê</h4>
        <small class="text-muted">Xem báo cáo, biểu đồ của đơn hàng, khách hàng, sản phẩm.</small>
    </div>
</div>

<div class="row g-4">
    <!-- Sidebar -->
    <div class="col-md-3">
        <div class="zoom">
            <div class="cardd  b mb-4">
                <div class="carddb-header">
                    <h5 class="mb-0">📊 Danh mục thống kê</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action" data-target="order-quantity" onclick="showSection(this, 'order-quantity')">📦 Đơn hàng - Số lượng</a>
                    <a href="#" class="list-group-item list-group-item-action" data-target="order-revenue" onclick="showSection(this, 'order-revenue')">💰 Đơn hàng - Doanh thu</a>
                    <a href="#" class="list-group-item list-group-item-action" data-target="customer-quantity" onclick="showSection(this, 'customer-quantity')">👤 Khách hàng - Số lượng</a>
                    <a href="#" class="list-group-item list-group-item-action" data-target="customer-revenue" onclick="showSection(this, 'customer-revenue')">👤 Khách hàng - Doanh thu</a>
                    <a href="#" class="list-group-item list-group-item-action" data-target="product-quantity" onclick="showSection(this, 'product-quantity')">🛍️ Sản phẩm - Số lượng</a>
                    <a href="#" class="list-group-item list-group-item-action" data-target="product-revenue" onclick="showSection(this, 'product-revenue')">🛍️ Sản phẩm - Doanh thu</a>
                    <a href="#" class="list-group-item list-group-item-action" data-target="product-buyer" onclick="showSection(this, 'product-buyer')">🔍 Sản phẩm → Khách mua</a>
                    <a href="#" class="list-group-item list-group-item-action" data-target="customer-product" onclick="showSection(this, 'customer-product')">🔍 Khách hàng → Sản phẩm mua</a>
                </div>
            </div>

            <!-- Form lọc thời gian (giữ nguyên) -->

            <div class="carddb mb-4">
                <div class="carddb-header">
                    <h5 class="mb-0">📅 Lọc theo thời gian</h5>
                </div>
                <div class="carddb-body">
                    <form method="GET">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Từ ngày:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="<?= htmlspecialchars($_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'))) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Đến ngày:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="<?= htmlspecialchars($_GET['end_date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Lọc dữ liệu</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Nội dung thống kê -->
    <div class="col-md-9">
        <!-- Các phần biểu đồ đã có sẵn (giữ nguyên) -->
        <div id="order-quantity" class="carddb mb-4 stat-section">
            <div class="carddb-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">📦 Số đơn hàng theo thời gian</h4>
                <button class="btn btn-sm btn-outline-success" onclick="exportChartData('orderBarChart', 'Số đơn hàng theo thời gian')">Xuất Excel</button>
            </div>
            <div class="carddb-body">
                <canvas id="orderBarChart" height="400"></canvas>
            </div>
        </div>

        <div id="order-revenue" class="carddb mb-4 stat-section" style="display: none;">
            <div class="carddb-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">💰 Doanh thu theo thời gian</h4>
                <button class="btn btn-sm btn-outline-success" onclick="exportChartData('orderLineChart', 'Doanh thu theo thời gian')">Xuất Excel</button>
            </div>
            <div class="carddb-body">
                <canvas id="orderLineChart" height="200"></canvas>
            </div>
        </div>

        <div id="customer-quantity" class="carddb mb-4 stat-section" style="display: none;">
            <div class="carddb-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">👤 Khách hàng theo số lượng đơn hàng</h4>
                <button class="btn btn-sm btn-outline-success" onclick="exportChartData('customerQuantityChart', 'Khách hàng theo số lượng đơn hàng')">Xuất Excel</button>
            </div>
            <div class="carddb-body">
                <canvas id="customerQuantityChart" height="100"></canvas>
            </div>
        </div>

        <div id="customer-revenue" class="carddb mb-4 stat-section" style="display: none;">
            <div class="carddb-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">👤 Khách hàng theo doanh thu</h4>
                <button class="btn btn-sm btn-outline-success" onclick="exportChartData('customerRevenueChart', 'Khách hàng theo doanh thu')">Xuất Excel</button>
            </div>
            <div class="carddb-body">
                <canvas id="customerRevenueChart" height="100"></canvas>
            </div>
        </div>

        <div id="product-quantity" class="carddb mb-4 stat-section" style="display: none;">
            <div class="carddb-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">🛍️ Sản phẩm theo số lượng bán</h4>
                <button class="btn btn-sm btn-outline-success" onclick="exportChartData('productQuantityChart', 'Sản phẩm theo số lượng bán')">Xuất Excel</button>
            </div>
            <div class="carddb-body">
                <canvas id="productQuantityChart" height="300"></canvas>
            </div>
        </div>

        <div id="product-revenue" class="carddb mb-4 stat-section" style="display: none;">
            <div class="carddb-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">🛍️ Sản phẩm theo doanh thu</h4>
                <button class="btn btn-sm btn-outline-success" onclick="exportChartData('productRevenueChart', 'Sản phẩm theo doanh thu')">Xuất Excel</button>
            </div>
            <div class="carddb-body">
                <canvas id="productRevenueChart" height="300"></canvas>
            </div>
        </div>

        <!-- PHẦN SẢN PHẨM -> KHÁCH MUA (Product Buyers) -->
        <div id="product-buyer" class="carddb mb-4 stat-section" style="display:none">
            <div class="carddb-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">🔍 Sản phẩm → Khách mua</h4>
                <button class="btn btn-sm btn-outline-success" onclick="exportTableData('product-buyer-table', 'Sản phẩm theo khách mua')">Xuất Excel</button>
            </div>
            <div class="carddb-body">
                <form method="GET" class="row g-2 mb-3">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">

                    <div class="col-md-8">
                        <input type="text" name="product_keyword" class="form-control"
                            placeholder="Nhập mã hoặc tên sản phẩm"
                            value="<?= htmlspecialchars($_GET['product_keyword'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Thống kê</button>
                    </div>
                </form>

                <?php if (!empty($productBuyers)): ?>
                    <table class="table table-bordered" id="product-buyer-table"> <!-- THÊM ID CHO BẢNG -->
                        <thead>
                            <tr>
                                <th>Mã khách hàng</th>
                                <th>Khách hàng</th>
                                <th>Số lượng mua</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productBuyers as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['customer_id']) ?></td>
                                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                    <td><?= (int)$row['total_quantity'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif (!empty($_GET['product_keyword'])): ?>
                    <p class="text-muted">Không có dữ liệu cho sản phẩm này trong khoảng thời gian đã chọn.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- PHẦN KHÁCH HÀNG -> SẢN PHẨM ĐÃ MUA (Customer Products) -->
        <div id="customer-product" class="carddb mb-4 stat-section" style="display:none">
            <div class="carddb-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">🔍 Khách hàng → Sản phẩm đã mua</h4>
                <button class="btn btn-sm btn-outline-success" onclick="exportTableData('customer-product-table', 'Khách hàng theo sản phẩm mua')">Xuất Excel</button>
            </div>

            <div class="carddb-body">
                <form method="GET" class="row g-2 mb-3">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">

                    <div class="col-md-8">
                        <input type="number" name="customer_id" class="form-control"
                            placeholder="Nhập mã khách hàng"
                            value="<?= htmlspecialchars($_GET['customer_id'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Thống kê</button>
                    </div>
                </form>

                <?php if (!empty($customerProducts)): ?>
                    <table class="table table-bordered" id="customer-product-table"> <!-- THÊM ID CHO BẢNG -->
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng mua</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customerProducts as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= (int)$row['total_quantity'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif (!empty($_GET['customer_id'])): ?>
                    <p class="text-muted">Không có dữ liệu cho khách hàng này trong khoảng thời gian đã chọn.</p>
                <?php endif; ?>
            </div>
        </div>
    </div> <!-- Kết thúc col-md-9 -->
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ==========================================================
    // CÁC BIẾN DỮ LIỆU ĐƯỢC PHP TRUYỀN XUỐNG JAVASCRIPT
    // ==========================================================
    const labels = <?= json_encode($labels); ?>;
    const orderData = <?= json_encode($dataOrders); ?>;
    const revenueData = <?= json_encode($dataRevenue); ?>;

    const customerLabels = <?= json_encode(array_column($customerStats, 'full_name')); ?>;
    const customerOrders = <?= json_encode(array_column($customerStats, 'total_orders')); ?>;
    const customerMoney = <?= json_encode(array_column($customerStats, 'total_revenue')); ?>;

    const productLabels = <?= json_encode(array_column($productStats, 'product_name')); ?>;
    const productQuantities = <?= json_encode(array_column($productStats, 'total_quantity')); ?>;
    const productMoney = <?= json_encode(array_column($productStats, 'total_revenue')); ?>;

    const charts = {}; // Object để lưu trữ các instance của Chart.js

    // ==========================================================
    // CÁC HÀM TIỆN ÍCH
    // ==========================================================

    // Hàm tiện ích để định dạng ngày tháng sang dd/mm/yyyy
    function formatDateToDDMMYYYY(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) { // Kiểm tra ngày không hợp lệ
            return '';
        }
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Tháng bắt đầu từ 0
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    // ==========================================================
    // HÀM TẠO BIỂU ĐỒ CHART.JS
    // ==========================================================
    function createChart(id, type, labels, data, opts = {}) {
        const ctx = document.getElementById(id);
        if (!ctx) return;

        if (charts[id]) charts[id].destroy(); // Hủy biểu đồ hiện có nếu tồn tại

        charts[id] = new Chart(ctx, {
            type,
            data: {
                labels,
                datasets: [{
                    label: opts.label || '',
                    data,
                    backgroundColor: opts.bg || 'rgba(75,192,192,0.7)',
                    borderColor: opts.border || 'rgba(75,192,192,1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: opts.axis || 'x', // 'x' cho biểu đồ cột dọc, 'y' cho biểu đồ cột ngang
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true, // Hiển thị legend cho biểu đồ
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label ? context.dataset.label + ': ' : '';
                                let value = 0;

                                // Xác định giá trị theo trục (x cho biểu đồ ngang, y cho biểu đồ dọc)
                                if (context.chart.options.indexAxis === 'y') {
                                    value = context.parsed.x;
                                } else {
                                    value = context.parsed.y;
                                }

                                if (value !== null) {
                                    // Định dạng tiền tệ cho doanh thu hoặc thêm đơn vị cho số lượng
                                    if (id.includes('Revenue') || id.includes('Money')) {
                                        return label + new Intl.NumberFormat('vi-VN', {
                                            style: 'currency',
                                            currency: 'VND'
                                        }).format(value);
                                    }
                                    return label + value.toLocaleString('vi-VN');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // ==========================================================
    // HÀM XUẤT DỮ LIỆU BIỂU ĐỒ RA FILE EXCEL (CSV)
    // ==========================================================
    function exportChartData(chartId, baseTitle) {
        const chartInstance = charts[chartId];
        if (!chartInstance) {
            alert('Không tìm thấy biểu đồ để xuất.');
            console.error('Chart instance not found for ID:', chartId); // Gỡ lỗi
            return;
        }

        const labels = chartInstance.data.labels;
        const dataset = chartInstance.data.datasets[0];
        const data = dataset.data;
        const dataLabel = dataset.label;

        // Xác định tiêu đề cho cột đầu tiên một cách linh hoạt hơn
        let firstColumnHeader = "Mục"; // Mặc định
        if (chartId.includes('order')) {
            firstColumnHeader = "Thời gian";
        } else if (chartId.includes('customer')) {
            firstColumnHeader = "Tên khách hàng";
        } else if (chartId.includes('product')) {
            firstColumnHeader = "Tên sản phẩm";
        }

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF"; // Thêm BOM cho tiếng Việt

        // Lấy ngày tháng từ form lọc để đưa vào tiêu đề
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const startDate = startDateInput ? formatDateToDDMMYYYY(startDateInput.value) : '';
        const endDate = endDateInput ? formatDateToDDMMYYYY(endDateInput.value) : '';

        let reportTitle = baseTitle;
        if (startDate && endDate) {
            reportTitle = `${baseTitle} từ ngày ${startDate} đến ngày ${endDate}`;
        } else if (startDate) {
            reportTitle = `${baseTitle} từ ngày ${startDate}`;
        } else if (endDate) {
            reportTitle = `${baseTitle} đến ngày ${endDate}`;
        }

        // Thêm tiêu đề báo cáo vào CSV
        csvContent += `"${reportTitle}"\n`;
        csvContent += `"${firstColumnHeader}","${dataLabel}"\n`;

        // Dữ liệu
        labels.forEach((label, index) => {
            // Bao quanh label bằng dấu nháy kép và thoát các dấu nháy kép bên trong
            const escapedLabel = String(label).replace(/"/g, '""');
            csvContent += `"${escapedLabel}",${data[index]}\n`;
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);

        // Tên file cũng dùng tiêu đề báo cáo
        link.setAttribute("download", `${reportTitle.replace(/ /g, '_').replace(/\//g, '-')}.csv`);
        document.body.appendChild(link); // Required for Firefox
        link.click();
        document.body.removeChild(link); // Clean up

        console.log('Exporting chart data for:', chartId, baseTitle); // Gỡ lỗi
        console.log('CSV Content (first 500 chars):', csvContent.substring(0, 500)); // Gỡ lỗi
    }


    // ==========================================================
    // HÀM XUẤT DỮ LIỆU TỪ BẢNG HTML RA FILE EXCEL (CSV)
    // ==========================================================
    function exportTableData(tableId, baseTitle) {
        const table = document.getElementById(tableId);
        if (!table) {
            alert('Không tìm thấy bảng để xuất.');
            console.error('Table not found for ID:', tableId);
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF"; // BOM cho tiếng Việt

        // Lấy ngày tháng từ form lọc để đưa vào tiêu đề
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const startDate = startDateInput ? formatDateToDDMMYYYY(startDateInput.value) : '';
        const endDate = endDateInput ? formatDateToDDMMYYYY(endDateInput.value) : '';

        let reportTitle = baseTitle;
        if (startDate && endDate) {
            reportTitle = `${baseTitle} từ ngày ${startDate} đến ngày ${endDate}`;
        } else if (startDate) {
            reportTitle = `${baseTitle} từ ngày ${startDate}`;
        } else if (endDate) {
            reportTitle = `${baseTitle} đến ngày ${endDate}`;
        }

        csvContent += `"${reportTitle}"\n`;

        // Lấy header của bảng
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => {
            return `"${th.textContent.trim().replace(/"/g, '""')}"`;
        }).join(',');
        csvContent += headers + '\n';

        // Lấy dữ liệu từ tbody
        table.querySelectorAll('tbody tr').forEach(row => {
            const rowData = Array.from(row.querySelectorAll('td')).map(td => {
                // Bao quanh dữ liệu bằng dấu nháy kép và thoát các dấu nháy kép bên trong
                return `"${td.textContent.trim().replace(/"/g, '""')}"`;
            }).join(',');
            csvContent += rowData + '\n';
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `${reportTitle.replace(/ /g, '_').replace(/\//g, '-')}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        console.log('Exporting table data for:', tableId, baseTitle);
        console.log('CSV Content (first 500 chars):', csvContent.substring(0, 500));
    }

    // ==========================================================
    // HÀM HIỂN THỊ/ẨN CÁC PHẦN THỐNG KÊ (SIDEBAR NAVIGATION)
    // ==========================================================
    function showSection(element, targetId) {
        document.querySelectorAll('.stat-section').forEach(section => {
            section.style.display = 'none';
        });

        const targetSection = document.getElementById(targetId);
        if (targetSection) {
            targetSection.style.display = 'block';
        }

        document.querySelectorAll('.list-group-item-action').forEach(item => {
            item.classList.remove('active');
        });
        element.classList.add('active');

        // Note: Biểu đồ sẽ được tạo/cập nhật một lần trong DOMContentLoaded
        // Nếu dữ liệu biểu đồ thay đổi khi chọn mục, bạn sẽ cần thêm logic update() ở đây
        // Ví dụ: charts[chartId].update();
    }


    // ==========================================================
    // CHẠY KHI TÀI LIỆU HTML ĐÃ ĐƯỢC TẢI XONG
    // ==========================================================

    document.addEventListener('DOMContentLoaded', () => {

        createChart('orderBarChart', 'bar', labels, orderData, {
            label: 'Số đơn hàng'
        });

        createChart('orderLineChart', 'line', labels, revenueData, {
            label: 'Doanh thu (VND)'
        });

        createChart('customerQuantityChart', 'bar', customerLabels, customerOrders, {
            label: 'Số đơn hàng',
            axis: 'y'
        });

        createChart('customerRevenueChart', 'bar', customerLabels, customerMoney, {
            label: 'Doanh thu',
            axis: 'y'
        });

        createChart('productQuantityChart', 'bar', productLabels, productQuantities, {
            label: 'Số lượng bán',
            axis: 'y'
        });

        createChart('productRevenueChart', 'bar', productLabels, productMoney, {
            label: 'Doanh thu',
            axis: 'y'
        });

        // ==============================
        // ⭐ TỰ ĐỘNG HIỂN THỊ ĐÚNG SECTION
        // ==============================
        <?php if (!empty($_GET['product_keyword'])): ?>
            showSection(
                document.querySelector('[data-target="product-buyer"]'),
                'product-buyer'
            );
        <?php elseif (!empty($_GET['customer_id'])): ?>
            showSection(
                document.querySelector('[data-target="customer-product"]'),
                'customer-product'
            );
        <?php else: ?>
            showSection(
                document.querySelector('[data-target="order-quantity"]'),
                'order-quantity'
            );
        <?php endif; ?>
    });
</script>

<?php include __DIR__ . '/../shares/footer.php'; ?>