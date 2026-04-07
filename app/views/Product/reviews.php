<div id="review-list-container" class="review-container">
    <?php if (!empty($reviews)): ?>
        <div class="review-list">
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <span class="reviewer-avatar">👤</span>
                            <span class="reviewer-name"><?= htmlspecialchars($review['account_name'] ?? 'Người dùng') ?></span>
                        </div>

                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= $i <= $review['rating'] ? 'active' : '' ?>">★</span>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <p class="review-date"><?= htmlspecialchars($review['created_at']) ?></p>

                    <p class="review-text">
                        <?= htmlspecialchars($review['review_text'] ?? 'Không có nhận xét.') ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-review">Chưa có đánh giá nào cho sản phẩm này.</p>
    <?php endif; ?>
</div>