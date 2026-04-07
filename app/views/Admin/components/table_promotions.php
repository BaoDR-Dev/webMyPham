<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>Tên khuyến mãi</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th class="text-center">Trạng thái</th>
                <th class="text-end">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($promotions)): ?>
                <?php foreach ($promotions as $index => $p): ?>
                    <?php if ($p['id'] == 1) continue; ?>
                    <?php
                    // Chuyển đổi về mảng nếu dữ liệu là Object để tránh lỗi
                    $p = (array)$p;

                    $current_time = time();
                    $start_timestamp = strtotime($p['start_date']);
                    $end_timestamp = strtotime($p['end_date']);

                    $isExpired = $end_timestamp < $current_time;
                    $isNotStarted = $start_timestamp > $current_time;
                    $isActive = ($p['is_active'] == 1) && !$isExpired && !$isNotStarted;

                    // Lấy tên loại (nếu type_name trống thì thử dùng cột name của bảng type nếu có)
                    $typeName = $p['type_name'] ?? ($p['promotion_type_name'] ?? 'Chưa xác định');
                    ?>
                    <tr>
                        <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                        <td>
                            <div class="promo-name"><?= htmlspecialchars($p['name']) ?></div>
                        </td>

                        <!-- CỘT LOẠI ĐÃ SỬA -->
                        <td><span class="badge-type"><?= htmlspecialchars($typeName) ?></span></td>

                        <td>
                            <span class="promo-value">
                                <?php if ($p['promotion_type_id'] == 1): ?>
                                    <?= (float)$p['discount_value'] ?>%
                                <?php elseif ($p['promotion_type_id'] == 2): ?>
                                    <?= number_format($p['discount_value']) ?>đ
                                <?php else: ?>
                                    Freeship
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <div class="promo-date"><?= date('d/m/Y', $start_timestamp) ?></div>
                        </td>
                        <td>
                            <div class="promo-date"><?= date('d/m/Y', $end_timestamp) ?></div>
                        </td>
                        <td class="text-center">
                            <?php if (!$p['is_active']): ?>
                                <span class="status-badge bg-secondary text-white" style="opacity: 0.6;">Đã tắt</span>
                            <?php elseif ($isExpired): ?>
                                <span class="status-badge status-expired">Hết hạn</span>
                            <?php elseif ($isNotStarted): ?>
                                <span class="status-badge bg-info text-white">Sắp diễn ra</span>
                            <?php else: ?>
                                <span class="status-badge status-active">Đang chạy</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="action-btns">
                                <a href="/webbanhang/Admin/editPromotion?id=<?= $p['id'] ?>" class="btn-edit"><i class="bi bi-pencil-square"></i></a>
                                <a href="/webbanhang/Admin/deletePromotion?id=<?= $p['id'] ?>" onclick="return confirm('Xoá?')" class="btn-delete"><i class="bi bi-trash3"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="empty-state">Không có dữ liệu.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>