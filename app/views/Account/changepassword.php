<!-- Modal Đổi mật khẩu -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$email = $_SESSION['email'] ?? '';
?>
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <!-- HEADER VỚI GRADIENT -->
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel"><i class="fas fa-shield-alt me-2"></i>Đổi Mật Khẩu An Toàn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="changePasswordForm">
                <div class="modal-body">
                    <p class="text-muted mb-4">Để bảo vệ tài khoản, vui lòng không chia sẻ mật khẩu cho người khác.</p>

                    <!-- Email -->
                    <div class="form-group mb-3">
                        <label for="email" class="form-label fw-bold">Tài khoản Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($email) ?>" autocomplete="username" disabled>
                            <input type="hidden" name="email" id="email_hidden" value="<?= htmlspecialchars($email) ?>">
                        </div>
                    </div>

                    <!-- Mật khẩu hiện tại -->
                    <div class="form-group mb-3">
                        <label for="currentPassword" class="form-label fw-bold">Mật khẩu hiện tại</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Nhập mật khẩu hiện tại" autocomplete="current-password" required>
                        </div>
                    </div>

                    <!-- Mật khẩu mới -->
                    <div class="form-group mb-3">
                        <label for="newPassword" class="form-label fw-bold">Mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Nhập mật khẩu mới" autocomplete="new-password" required>
                        </div>
                        <div class="password-hint">Sử dụng ít nhất 8 ký tự.</div>
                    </div>

                    <!-- Xác nhận mật khẩu mới -->
                    <div class="form-group mb-4">
                        <label for="confirmPassword" class="form-label fw-bold">Xác nhận mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Nhập lại mật khẩu mới" autocomplete="new-password" required>
                        </div>
                    </div>

                    <!-- Thông báo lỗi (dạng alert) -->
                    <div id="changePasswordMessage" class="alert alert-danger d-none" role="alert">
                        <!-- Nội dung lỗi sẽ được chèn vào đây bằng JavaScript -->
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-gradient-primary btn-center">
                        <i class="fas fa-check-circle me-2"></i>Cập nhật mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>