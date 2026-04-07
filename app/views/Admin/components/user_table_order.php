<?php if (!empty($orders)): ?>
    <div class="table-responsive">
        <table class="table align-middle mb-0 order-table-custom">
            <thead>
                <tr>
                    <th style="width: 80px;">Mã ĐH</th>
                    <th style="width: 120px;">Ngày Đặt</th>
                    <th class="text-center" style="width: 160px;">Trạng thái</th>
                    <th class="text-center" style="width: 120px;">Thanh toán</th>
                    <th class="text-center" style="min-width: 140px;">Tổng tiền</th>
                    <th class="text-center" style="width: 100px;">Chi tiết đơn hàng</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="fw-semibold text-primary">#<?= htmlspecialchars($order->id) ?></td>
                        <td class="text-muted"><?= date('d/m/Y', strtotime($order->created_at)) ?></td>
                        <td class="text-center">
                            <?php
                            $statusClass = match ($order->status ?? '') {
                                'pending' => 'secondary',
                                'processing' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'dark'
                            };
                            ?>
                            <form method="post" action="/webbanhang/Admin/updateOrderStatus2" class="d-inline-block">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->id ?? '') ?>">
                                <select name="status"
                                    class="order-status-select bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?>"
                                    onchange="this.form.submit()">
                                    <option value="pending" <?= ($order->status ?? '') == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                    <option value="processing" <?= ($order->status ?? '') == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                    <option value="completed" <?= ($order->status ?? '') == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                    <option value="cancelled" <?= ($order->status ?? '') == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-center">
                            <?php
                            echo match ($order->payment_method ?? '') {
                                'momo' => '<span class="badge bg-danger-subtle text-danger payment-badge"><i class="bi bi-wallet-fill"></i> MoMo</span>',
                                'vnpay' => '<span class="badge bg-primary-subtle text-primary payment-badge"><i class="bi bi-credit-card-2-front-fill"></i> VnPay</span>',
                                'cash' => '<span class="badge bg-success-subtle text-success payment-badge"><i class="bi bi-cash-stack"></i> Tiền mặt</span>',
                                default => '<span class="badge bg-secondary-subtle text-secondary payment-badge">Khác</span>'
                            };
                            ?>
                        </td>
                        <td class="fw-bold text-end text-success"><?= number_format($order->total_amount, 0, ',', '.') ?> VND</td>
                        <td class="text-center">
                            <a href="/webbanhang/Admin/orderDetail2/<?= htmlspecialchars($order->id) ?>" class="btn btn-sm btn-outline-primary btn-detail-order-custom">
                                <i class="bi bi-eye me-1"></i>Xem chi tiết
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="empty-state-message">
        <i class="bi bi-box-seam fs-4 d-block mb-3 text-muted"></i>
        <p class="mb-0">Khách hàng này chưa có đơn hàng nào.</p>
    </div>
<?php endif; ?>