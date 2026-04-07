<style>
    /* Wrapper */
    .table-responsive {
        background: #fff;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    /* Table */
    .product-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    /* Header */
    .product-table thead th {
        background: #f8f9fa;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6b7280;
        font-weight: 700;
        padding: 14px 16px;
        border: none;
    }

    /* Row */
    .product-table tbody tr {
        background: #fff;
        transition: all .2s ease;
    }

    /* Cell */
    .product-table tbody td {
        padding: 16px;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #495057;
        border-top: 1px solid #e9ecef;
        border-bottom: 1px solid #e9ecef;
    }

    /* Bo góc card */
    .product-table tbody td:first-child {
        border-left: 1px solid #e9ecef;
        border-radius: 10px 0 0 10px;
        font-weight: 600;
        color: var(--bs-primary);
    }

    .product-table tbody td:last-child {
        border-right: 1px solid #e9ecef;
        border-radius: 0 10px 10px 0;
    }

    /* Hover giống bảng user */
    .product-table tbody tr:hover td {
        background: #f8f9fa;
    }

    .product-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    /* Product info */
    .product-image-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .img-product-modern {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        object-fit: cover;
        background: #f1f3f5;
    }

    .product-name {
        font-weight: 600;
        color: #212529;
    }

    /* Price */
    .price-text {
        font-weight: 700;
        color: #1f2937;
    }

    /* Stock */
    .stock-badge {
        background: #f1f3f5;
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* Rating */
    .product-rating {
        background: #fff7dd;
        padding: 6px 10px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #f59e0b;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Actions */
    .action-buttons-group {
        display: flex;
        gap: 10px;
    }

    .btn-action-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: .2s;
    }

    .btn-action-icon:hover {
        background: #e9ecef;
        transform: scale(1.1);
    }
</style>
<!-- Main Product Table Section -->
<div>
    <div id="product-table-container" class="table-responsive">
        <?php if (!empty($products)): ?>
            <!-- Nội dung bảng sản phẩm (từ products_table_partial.php hoặc nhúng trực tiếp) -->
            <table class="table align-middle product-table">
                <thead>
                    <tr>
                        <th>MÃ SẢN PHẨM</th>
                        <th>THÔNG TIN</th>
                        <th>GIÁ BÁN</th>
                        <th>KHO HÀNG</th>
                        <th>ĐÁNH GIÁ</th>
                        <th>THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr data-quantity="<?= $product->quantity ?? 0 ?>">
                            <td>#<?= htmlspecialchars($product->id ?? '0') ?></td>
                            <td>
                                <div class="product-image-info">
                                    <img src="/webbanhang/<?= htmlspecialchars($product->image ?? 'public/images/default.jpg') ?>"
                                        class="img-product-modern">
                                    <div>
                                        <div class="product-name"><?= htmlspecialchars($product->name ?? 'Không rõ') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="price-text">
                                    <?= isset($product->price) ? number_format($product->price, 0, ',', '.') : '0' ?> <small>₫</small>
                                </span>
                            </td>
                            <td>
                                <span class="stock-badge">
                                    <i class="bi bi-layers-half"></i> <?= $product->quantity ?? 0 ?>
                                </span>
                            </td>
                            <td>
                                <?php if (isset($product->total_rating) && $product->review_count > 0): ?>
                                    <div class="product-rating">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="rating-score"><?= number_format($product->total_rating, 1) ?></span>
                                        <span class="review-count">(<?= $product->review_count ?>)</span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">Chưa có</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons-group">
                                    <?php if (SessionHelper::isAdmin()): ?>
                                        <a href="/webbanhang/Product/edit/<?= $product->id ?>" class="btn-action-icon text-primary" title="Chỉnh sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn-action-icon text-danger btn-delete-product" data-id="<?= $product->id ?>" title="Xóa">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    <?php else: ?>
                                        <a href="/webbanhang/Product/view/<?= $product->id ?>" class="btn btn-white rounded-pill me-2">Chi tiết</a>
                                        <a href="/webbanhang/cart/add/<?= $product->id ?>" class="btn btn-primary rounded-pill">Mua</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" style="width: 60px; opacity: 0.2;" class="mb-3">
                <h5 class="text-muted fw-light">Danh sách không tồn tại sản phẩm</h5>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    // Hàm này sẽ được gọi để gắn sự kiện xóa cho các nút
    function attachDeleteEvents() {
        document.querySelectorAll('.btn-delete-product').forEach(function(button) {
            // Loại bỏ các listener cũ để tránh trùng lặp nếu hàm được gọi nhiều lần
            button.removeEventListener('click', handleDeleteProduct);
            // Gắn listener mới
            button.addEventListener('click', handleDeleteProduct);
        });
    }

    // Hàm xử lý sự kiện click nút xóa
    function handleDeleteProduct() {
        const productId = this.dataset.id;
        if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            fetch(`/webbanhang/Product/deleteAjax/${productId}`, { // Sửa lỗi cú pháp URL
                    method: 'DELETE',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('tr').remove();
                        // Nếu cần, bạn có thể thêm logic để cập nhật lại các chỉ số thống kê
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

    document.addEventListener('DOMContentLoaded', function() {
        attachDeleteEvents(); // Gắn sự kiện khi trang tải lần đầu

        // Nếu bạn tải lại bảng qua AJAX, hãy đảm bảo gọi attachDeleteEvents() sau khi tải xong.
        // Ví dụ, trong hàm fetch của bộ lọc:
        // .then(html => {
        //     productTableContainer.innerHTML = html;
        //     productTableContainer.style.opacity = '1';
        //     if (typeof attachDeleteEvents === "function") attachDeleteEvents(); // Gọi ở đây
        // })
    });
</script>