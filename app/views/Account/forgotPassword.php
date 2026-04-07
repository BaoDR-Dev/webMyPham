<?php include 'app/views/shares/header.php'; ?>

<style>
    body {
        background-color: #f0f2f5;
    }

    .section-forgot {
        min-height: 85vh;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .card-forgot {
        border: none;
        border-radius: 20px;
        /* Đã xóa box-shadow để loại bỏ hiệu ứng "nổi" hoặc "mờ" ban đầu */
        box-shadow: none !important;
        overflow: hidden;
        background: #fff;
        width: 100% !important;
        /* Đảm bảo không có bất kỳ hiệu ứng chuyển đổi nào trên card */
        transition: none !important;
        transform: none !important;
        outline: none !important;
        filter: none !important;
        opacity: 1 !important;
    }

    /* Đảm bảo card không thay đổi khi có phần tử con được focus hoặc khi hover */
    .card-forgot:focus-within,
    .card-forgot:focus,
    .card-forgot:hover {
        box-shadow: none !important;
        transform: none !important;
        outline: none !important;
        filter: none !important;
        opacity: 1 !important;
        transition: none !important;
    }

    /* Banner trái: Màu Cam/Hồng để khác biệt với màu Xanh của Login */
    .banner-forgot {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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

    .banner-forgot i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.9;
    }

    .form-wrapper-forgot {
        padding: 50px 40px;
    }

    .form-floating>.form-control {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        /* Thêm border để nhất quán với các form khác */
    }

    .form-floating>.form-control:focus {
        border-color: #f5576c;
        /* Thay đổi màu border khi focus cho phù hợp với màu banner */
        box-shadow: none !important;
        /* Loại bỏ box-shadow khi input focus */
        outline: none !important;
        /* Loại bỏ outline khi input focus */
        filter: none !important;
    }

    .btn-forgot {
        background: linear-gradient(to right, #f5576c, #e03e56);
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

    .btn-forgot:hover {
        /* Đảm bảo không có hiệu ứng zoom, box-shadow khi hover */
        transform: none !important;
        box-shadow: none !important;
        background: linear-gradient(to right, #d44d62, #c7364b);
        /* Điều chỉnh màu hover cho phù hợp */
        color: white;
        filter: none !important;
        opacity: 1 !important;
    }

    .btn-forgot:focus {
        outline: none !important;
        box-shadow: none !important;
        filter: none !important;
        opacity: 1 !important;
    }

    /* Đảm bảo link quay lại đăng nhập không có hiệu ứng mặc định */
    .text-center a {
        transition: color 0.3s ease;
        text-decoration: none;
    }

    .text-center a:hover {
        color: #f5576c !important;
        /* Thay đổi màu hover cho link quay lại */
    }
</style>

<section class="section-forgot">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11 col-md-12">
                <div class="card card-forgot">
                    <div class="row g-0">

                        <!-- CỘT TRÁI -->
                        <div class="col-md-6 d-none d-md-block">
                            <div class="banner-forgot">
                                <i class="bi bi-shield-lock"></i>
                                <h3>Quên Mật Khẩu?</h3>
                                <p>Đừng lo lắng! Nhập email của bạn và chúng tôi sẽ gửi hướng dẫn khôi phục.</p>
                            </div>
                        </div>

                        <!-- CỘT PHẢI -->
                        <div class="col-md-6">
                            <div class="form-wrapper-forgot">
                                <h2 class="fw-bold mb-3 text-dark">Khôi phục tài khoản</h2>

                                <!-- Thông báo thành công -->
                                <?php if (isset($_SESSION['info'])): ?>
                                    <div class="alert alert-success d-flex align-items-center" role="alert" style="border-radius: 10px;">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <div><?= $_SESSION['info']; ?></div>
                                    </div>
                                    <?php unset($_SESSION['info']); ?>
                                <?php endif; ?>

                                <!-- Thông báo lỗi -->
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger d-flex align-items-center" role="alert" style="border-radius: 10px;">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <div><?= $_SESSION['error']; ?></div>
                                    </div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <form action="/webbanhang/account/forgotPassword" method="post">
                                    <div class="form-floating mb-4">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                        <label for="email"><i class="bi bi-envelope me-1"></i> Email đăng ký</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 btn-forgot">
                                        Gửi Link Khôi Phục <i class="bi bi-send ms-2"></i>
                                    </button>

                                    <div class="text-center mt-4">
                                        <a href="/webbanhang/account/login" class="text-decoration-none text-secondary">
                                            <i class="bi bi-arrow-left"></i> Quay lại Đăng nhập
                                        </a>
                                    </div>
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