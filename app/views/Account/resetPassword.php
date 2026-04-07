<?php include 'app/views/shares/header.php'; ?>

<style>
    body {
        background-color: #f0f2f5;
    }

    .section-reset {
        min-height: 85vh;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .card-reset {
        border: none;
        border-radius: 20px;
        /* Đã xóa box-shadow để loại bỏ hiệu ứng "nổi" hoặc "mờ" ban đầu */
        box-shadow: none !important;
        background: #fff;
        width: 100% !important;
        overflow: hidden;
        /* Đảm bảo không có bất kỳ hiệu ứng chuyển đổi nào trên card */
        transition: none !important;
        transform: none !important;
        outline: none !important;
        filter: none !important;
        opacity: 1 !important;
    }

    /* Đảm bảo card không thay đổi khi có phần tử con được focus hoặc khi hover */
    .card-reset:focus-within,
    .card-reset:focus,
    .card-reset:hover {
        box-shadow: none !important;
        transform: none !important;
        outline: none !important;
        filter: none !important;
        opacity: 1 !important;
        transition: none !important;
    }

    /* Banner trái: Màu xanh lá */
    .banner-reset {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        text-align: center;
        padding: 40px;
        height: 100%;
        min-height: 450px;
    }

    .banner-reset i {
        font-size: 4rem;
        margin-bottom: 15px;
    }

    .form-wrapper-reset {
        padding: 50px 40px;
    }

    .form-floating>.form-control {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        /* Thêm border để nhất quán với các form khác */
    }

    .form-floating>.form-control:focus {
        border-color: #11998e;
        /* Thay đổi màu border khi focus cho phù hợp với màu banner */
        box-shadow: none !important;
        /* Loại bỏ box-shadow khi input focus */
        outline: none !important;
        /* Loại bỏ outline khi input focus */
        filter: none !important;
    }

    .btn-reset {
        background: linear-gradient(to right, #11998e, #118a80);
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        color: white;
        /* Chỉ giữ transition cho background để đổi màu mượt mà */
        transition: background 0.3s ease !important;
        /* Loại bỏ hiệu ứng zoom, box-shadow, outline */
        transform: none !important;
        box-shadow: none !important;
        outline: none !important;
        filter: none !important;
        opacity: 1 !important;
    }

    .btn-reset:hover {
        /* Đảm bảo không có hiệu ứng zoom, box-shadow khi hover */
        transform: none !important;
        box-shadow: none !important;
        background: linear-gradient(to right, #0e867b, #0e7a70);
        /* Điều chỉnh màu hover cho phù hợp */
        color: white;
        filter: none !important;
        opacity: 1 !important;
    }

    .btn-reset:focus {
        outline: none !important;
        box-shadow: none !important;
        filter: none !important;
        opacity: 1 !important;
    }
</style>

<section class="section-reset">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11 col-md-12">
                <div class="card card-reset">
                    <div class="row g-0">

                        <!-- CỘT TRÁI -->
                        <div class="col-md-6 d-none d-md-block">
                            <div class="banner-reset">
                                <i class="bi bi-key-fill"></i>
                                <h3>Đặt Lại Mật Khẩu</h3>
                                <p>Tạo mật khẩu mới mạnh hơn để bảo vệ tài khoản của bạn.</p>
                            </div>
                        </div>

                        <!-- CỘT PHẢI -->
                        <div class="col-md-6">
                            <div class="form-wrapper-reset">
                                <h2 class="fw-bold mb-2 text-dark">Mật khẩu mới</h2>
                                <p class="text-muted mb-4">Vui lòng nhập mật khẩu mới.</p>

                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger" style="border-radius: 10px;">
                                        <i class="bi bi-exclamation-circle me-1"></i> <?= $_SESSION['error']; ?>
                                    </div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <!-- QUAN TRỌNG: Action phải truyền Token qua URL -->
                                <form action="/webbanhang/account/resetPassword?token=<?= htmlspecialchars($token ?? ''); ?>" method="post">

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu mới" required>
                                        <label for="password"><i class="bi bi-lock me-1"></i> Mật khẩu mới</label>
                                    </div>

                                    <div class="form-floating mb-4">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                                        <label for="confirm_password"><i class="bi bi-shield-check me-1"></i> Nhập lại mật khẩu</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 btn-reset">
                                        Xác Nhận Đổi Mật Khẩu
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/shares/footer.php'; ?>