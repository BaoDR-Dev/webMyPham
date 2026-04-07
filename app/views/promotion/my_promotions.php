<?php include 'app/views/shares/header.php'; ?>

<style>
    #modalPromoContent p {
        margin-bottom: 5px;
        /* Giảm khoảng cách giữa các dòng <p> */
    }

    #modalPromoContent {
        line-height: 1.5;
        font-size: 0.95rem;
    }

    /* CSS tùy chỉnh cho danh sách khuyến mãi */
    .promo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .promo-item {
        display: flex;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s;
        position: relative;
        cursor: pointer;
    }

    .promo-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .promo-left {
        width: 150px;
        min-width: 150px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 2px dashed #eee;
        padding: 10px;
    }

    .promo-left img {
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }

    .promo-right {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .promo-name {
        font-weight: bold;
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 5px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .promo-discount {
        color: #e74c3c;
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 8px;
    }

    .promo-dates {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .promo-disabled {
        filter: grayscale(1);
        opacity: 0.7;
    }

    @media (max-width: 576px) {
        .promo-grid {
            grid-template-columns: 1fr;
        }

        .promo-left {
            width: 100px;
            min-width: 100px;
        }
    }
</style>

<div class="container my-5">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-gift-fill text-danger me-2"></i>
        Kho khuyến mãi của tôi
    </h3>

    <?php if (empty($promotions)): ?>
        <div class="alert alert-info">Bạn chưa nhận khuyến mãi nào.</div>
    <?php else: ?>
        <div class="promo-grid">
            <?php
            usort($promotions, function ($a, $b) {
                $remainA   = $a->received_quantity - $a->used_quantity;
                $expiredA = !empty($a->end_date) && strtotime($a->end_date) < time();
                $activeA  = !$expiredA && $remainA > 0 && $a->status;

                $remainB   = $b->received_quantity - $b->used_quantity;
                $expiredB = !empty($b->end_date) && strtotime($b->end_date) < time();
                $activeB  = !$expiredB && $remainB > 0 && $b->status;

                // active lên trên
                return $activeB <=> $activeA;
            });
            ?>
            <?php foreach ($promotions as $promo): ?>
                <?php
                $remain  = $promo->received_quantity - $promo->used_quantity;
                $expired = !empty($promo->end_date) && strtotime($promo->end_date) < time();
                $is_active = !$expired && $remain > 0 && $promo->status;

                // Xử lý hình ảnh
                $img_name = "free2.png";
                if ($promo->promotion_type_id == 1) $img_name = "phantram.png";
                elseif ($promo->promotion_type_id == 2) $img_name = "-$2.png";

                $img_path = "/webbanhang/uploads/Promotions/" . $img_name;
                ?>

                <div class="promo-item <?= !$is_active ? 'promo-disabled' : '' ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#promotionDetailModal"
                    data-name="<?= htmlspecialchars($promo->promotion_name) ?>"
                    data-content="<?= htmlspecialchars($promo->content ?? '') ?>"
                    data-discount="<?= $promo->discount_value ?>"
                    data-type="<?= $promo->promotion_type_id ?>"
                    data-start="<?= $promo->start_date ?>"
                    data-end="<?= $promo->end_date ?>"
                    data-min="<?= $promo->min_order_amount ?>"
                    data-category="<?= htmlspecialchars($promo->category_name ?? 'Tất cả sản phẩm') ?>"
                    data-remain="<?= $remain ?>"
                    data-status="<?= $is_active ? '1' : '0' ?>"
                    data-use-url="/webbanhang/cart/index">

                    <div class="promo-left">
                        <img src="<?= $img_path ?>" alt="Promo">
                    </div>

                    <div class="promo-right">
                        <div class="promo-name"><?= htmlspecialchars($promo->promotion_name) ?></div>
                        <div class="promo-discount">
                            <?php if ($promo->promotion_type_id == 1): ?>
                                Giảm <?= (float)$promo->discount_value ?>%
                            <?php elseif ($promo->promotion_type_id == 2): ?>
                                Giảm <?= number_format($promo->discount_value) ?>đ
                            <?php else: ?>
                                Miễn phí vận chuyển
                            <?php endif; ?>
                        </div>
                        <div class="promo-dates">
                            <i class="bi bi-clock me-1"></i>
                            Hạn dùng: <?= !empty($promo->end_date) ? date('d/m/Y', strtotime($promo->end_date)) : 'Vô thời hạn' ?>
                        </div>
                        <?php if (!$is_active): ?>
                            <span class="text-danger small fw-bold mt-1">Không khả dụng</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Phân trang -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav class="mt-4 d-flex justify-content-end">
            <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- MODAL CHI TIẾT -->
<div class="modal fade" id="promotionDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Chi tiết khuyến mãi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 id="modalPromoName" class="fw-bold text-primary mb-2"></h4>
                <div id="modalPromoContent" class="text-muted mb-4"></div>

                <div class="p-3 bg-light rounded-3 mb-3">
                    <p class="mb-2"><strong>🎁 Ưu đãi:</strong> <span id="modalPromoDiscount" class="text-danger fw-bold"></span></p>
                    <p class="mb-2"><strong>📌 Điều kiện:</strong></p>
                    <ul class="small text-muted mb-2" id="modalPromoConditions"></ul>
                    <p class="mb-2"><strong>⏰ Thời gian:</strong></p>
                    <div class="small text-muted ms-3">
                        Bắt đầu: <span id="modalPromoStart"></span><br>
                        Kết thúc: <span id="modalPromoEnd"></span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span>Số lượt còn lại: <strong id="modalPromoRemain"></strong></span>
                    <span id="modalPromoStatus" class="badge"></span>
                </div>
            </div>
            <div class="modal-footer border-0">
                <a id="modalUseBtn" href="#" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">
                    Dùng ngay
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('promotionDetailModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function(event) {
            // Biến 'button' là thẻ .promo-item vừa được click
            const button = event.relatedTarget;

            // 1. Trích xuất thông tin từ thuộc tính data-*
            const name = button.getAttribute('data-name');
            const content = button.getAttribute('data-content');
            const type = button.getAttribute('data-type');
            const discount = button.getAttribute('data-discount');
            const start = button.getAttribute('data-start');
            const end = button.getAttribute('data-end');
            const minOrder = button.getAttribute('data-min');
            const categoryName = button.getAttribute('data-category'); // Đây là tên danh mục từ DB
            const remain = button.getAttribute('data-remain');
            const status = button.getAttribute('data-status');
            const useUrl = button.getAttribute('data-use-url');

            // 2. Cập nhật nội dung cơ bản cho Modal
            document.getElementById('modalPromoContent').innerHTML = content || 'Không có mô tả chi tiết.';
            document.getElementById('modalPromoContent').innerHTML = content || 'Không có mô tả chi tiết.';
            // 3. Hiển thị mức giảm giá
            let discountText = '';
            if (type == '1') {
                discountText = `Giảm ${parseFloat(discount)}%`;
            } else if (type == '2') {
                discountText = `Giảm ${Number(discount).toLocaleString('vi-VN')}đ`;
            } else {
                discountText = 'Miễn phí vận chuyển';
            }
            document.getElementById('modalPromoDiscount').textContent = discountText;

            // 4. Hiển thị ngày tháng
            const formatDate = (dateStr) => {
                if (!dateStr || dateStr === '0000-00-00 00:00:00') return '—';
                return new Date(dateStr).toLocaleDateString('vi-VN');
            };
            document.getElementById('modalPromoStart').textContent = formatDate(start);
            document.getElementById('modalPromoEnd').textContent = formatDate(end);

            // 5. Hiển thị điều kiện (SỬA LỖI TẠI ĐÂY)
            const conditions = document.getElementById('modalPromoConditions');
            conditions.innerHTML = ''; // Xóa trắng danh sách cũ

            // Điều kiện đơn hàng tối thiểu
            if (minOrder && parseFloat(minOrder) > 0) {
                conditions.innerHTML += `<li>Đơn tối thiểu: <strong>${Number(minOrder).toLocaleString('vi-VN')}đ</strong></li>`;
            }

            // Điều kiện danh mục (Kiểm tra nếu có tên danh mục và không phải giá trị trống)
            if (categoryName && categoryName.trim() !== '' && categoryName !== '0' && categoryName !== 'null') {
                conditions.innerHTML += `<li>Chỉ áp dụng cho danh mục: <strong>${categoryName}</strong></li>`;
            } else {
                conditions.innerHTML += `<li>Áp dụng cho <strong>tất cả sản phẩm</strong></li>`;
            }

            // 6. Trạng thái và lượt dùng còn lại
            document.getElementById('modalPromoRemain').textContent = remain;
            const statusBadge = document.getElementById('modalPromoStatus');
            const useBtn = document.getElementById('modalUseBtn');

            if (status === "1") {
                statusBadge.className = 'badge bg-success';
                statusBadge.textContent = 'Có thể sử dụng';
                useBtn.style.display = 'block';
                useBtn.href = useUrl;
            } else {
                statusBadge.className = 'badge bg-danger';
                statusBadge.textContent = 'Không khả dụng';
                useBtn.style.display = 'none';
            }
        });
    });
</script>

<?php include 'app/views/shares/footer.php'; ?>