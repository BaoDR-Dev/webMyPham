<div class="offcanvas offcanvas-end" tabindex="-1" id="orderFilterOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold">
            <i class="bi bi-funnel-fill me-2 text-primary"></i>Bộ lọc đơn hàng
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <form id="orderFilterForm" method="get" action="/webbanhang/Admin/userDetails2">

            <!-- 🔒 khóa cứng user -->
            <input type="hidden" name="user_id" value="<?= (int)$customerInfo->id ?>">

            <!-- Lọc mã đơn A → B -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input filter-toggle" type="checkbox">
                    <label class="form-check-label fw-semibold">
                        <i class="bi bi-hash me-2"></i>Lọc theo mã đơn (A → B)
                    </label>
                </div>
                <div class="d-flex gap-2">
                    <input type="number" name="min_order_id"
                        class="form-control form-control-sm filter-input"
                        placeholder="Từ mã" disabled>
                    <input type="number" name="max_order_id"
                        class="form-control form-control-sm filter-input"
                        placeholder="Đến mã" disabled>
                </div>
            </fieldset>

            <!-- Trạng thái -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input filter-toggle" type="checkbox">
                    <label class="form-check-label fw-semibold">
                        <i class="bi bi-toggles me-2"></i>Trạng thái
                    </label>
                </div>
                <select name="status[]" class="form-select form-select-sm filter-input"
                    multiple disabled>
                    <option value="pending">Chờ xử lý</option>
                    <option value="processing">Đang xử lý</option>
                    <option value="completed">Hoàn thành</option>
                    <option value="cancelled">Đã hủy</option>
                </select>
            </fieldset>

            <!-- Thanh toán -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input filter-toggle" type="checkbox">
                    <label class="form-check-label fw-semibold">
                        <i class="bi bi-credit-card me-2"></i>Thanh toán
                    </label>
                </div>
                <select name="payment_methods[]" class="form-select form-select-sm filter-input"
                    multiple disabled>
                    <option value="cash">Tiền mặt</option>
                    <option value="momo">Momo</option>
                    <option value="vnpay">VNPay</option>
                </select>
            </fieldset>

            <!-- Khoảng tiền -->
            <fieldset class="filter-group">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input filter-toggle" type="checkbox">
                    <label class="form-check-label fw-semibold">
                        <i class="bi bi-currency-dollar me-2"></i>Khoảng tiền
                    </label>
                </div>
                <div class="d-flex gap-2">
                    <input type="number" name="min_amount"
                        class="form-control form-control-sm filter-input"
                        placeholder="Từ" disabled>
                    <input type="number" name="max_amount"
                        class="form-control form-control-sm filter-input"
                        placeholder="Đến" disabled>
                </div>
            </fieldset>

            <!-- Buttons -->
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Áp dụng
                </button>

                <button type="button" id="resetFilterBtn"
                    class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>

        </form>

    </div>
</div>