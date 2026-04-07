<?php include 'app/views/shares/header.php'; ?>

<!-- CSS RIÊNG CHO FORM MỚI (Đã đổi tên class để tránh lỗi) -->
<style>
    /* Nền trang */
    body {
        background-color: #f0f2f5;
    }

    .section-login-new {
        min-height: 85vh;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .card-login-new {
        border: none;
        border-radius: 20px;
        box-shadow: none !important;
        /* Đảm bảo không có box-shadow */
        overflow: hidden;
        background: #fff;
        width: 100% !important;
        max-width: 100% !important;
        transition: none !important;
        /* Loại bỏ mọi transition */
        transform: none !important;
        /* Loại bỏ mọi transform */
        outline: none !important;
        /* Loại bỏ mọi outline */
        filter: none !important;
        /* Rất quan trọng: loại bỏ mọi hiệu ứng filter, đặc biệt là blur */
        opacity: 1 !important;
        /* Đảm bảo độ mờ hoàn toàn (không trong suốt) */
    }

    /* Đảm bảo card không thay đổi khi có phần tử con được focus hoặc khi hover */
    .card-login-new:focus-within,
    .card-login-new:focus,
    .card-login-new:hover {
        outline: none !important;
        box-shadow: none !important;
        transform: none !important;
        filter: none !important;
        opacity: 1 !important;
        transition: none !important;
    }


    /* Cột hình ảnh bên trái */
    .banner-login-new {
        background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        text-align: center;
        padding: 40px;
        position: relative;
        height: 100%;
        min-height: 450px;
    }

    .banner-login-new::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('https://cdn-icons-png.flaticon.com/512/2966/2966327.png') center/cover;
        opacity: 0.1;
        mix-blend-mode: overlay;
    }

    .banner-login-new h3 {
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 10px;
        z-index: 1;
        color: #fff;
    }

    .banner-login-new p {
        font-size: 1rem;
        opacity: 0.9;
        z-index: 1;
        color: #fff;
    }

    /* Form bên phải */
    .form-wrapper-new {
        padding: 50px 40px;
        background: #fff;
    }

    .title-login-new {
        font-weight: 800;
        color: #333;
        margin-bottom: 5px;
    }

    .subtitle-login-new {
        color: #6c757d;
        margin-bottom: 30px;
        font-size: 0.95rem;
    }

    /* Input Floating Label */
    .form-floating>.form-control {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        padding-left: 20px;
    }

    .form-floating>.form-control:focus {
        border-color: #0d6efd;
        box-shadow: none !important;
        outline: none !important;
        filter: none !important;
        /* Loại bỏ filter cho input khi focus */
    }

    .form-floating>label {
        padding-left: 20px;
    }

    /* Nút đăng nhập */
    .btn-login-new {
        background: linear-gradient(to right, #0d6efd, #0099ff);
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        transition: background 0.3s ease !important;
        margin-top: 10px;
        color: white;
        box-shadow: none !important;
        transform: none !important;
        outline: none !important;
        filter: none !important;
        /* Loại bỏ filter cho nút */
        opacity: 1 !important;
    }

    .btn-login-new:hover {
        transform: none !important;
        box-shadow: none !important;
        background: linear-gradient(to right, #0b5ed7, #007bc4);
        color: white;
        filter: none !important;
        opacity: 1 !important;
    }

    .btn-login-new:focus {
        outline: none !important;
        box-shadow: none !important;
        filter: none !important;
        opacity: 1 !important;
    }

    .link-forgot-new {
        color: #6c757d;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .link-forgot-new:hover {
        color: #0d6efd;
    }

    .link-register-new {
        text-align: center;
        margin-top: 25px;
        font-size: 0.95rem;
    }

    .link-register-new a {
        font-weight: 700;
        text-decoration: none;
        color: #0d6efd;
    }
</style>

<section class="section-login-new">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Tăng độ rộng cột chứa -->
            <div class="col-xl-10 col-lg-11 col-md-12">

                <div class="card card-login-new">
                    <div class="row g-0">

                        <!-- CỘT TRÁI: BANNER -->
                        <div class="col-md-6 d-none d-md-block">
                            <div class="banner-login-new">
                                <img src="/webbanhang/public/images/Logo.png" alt="Logo" style="width: 80px; margin-bottom: 20px; z-index: 1;">
                                <h3>Y Tế 24/7</h3>
                                <p>Chăm sóc sức khỏe toàn diện<br>cho gia đình bạn.</p>
                            </div>
                        </div>

                        <!-- CỘT PHẢI: FORM -->
                        <div class="col-md-6">
                            <div class="form-wrapper-new">

                                <h2 class="title-login-new">Xin chào,</h2>
                                <p class="subtitle-login-new">Vui lòng đăng nhập để tiếp tục mua sắm.</p>

                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger d-flex align-items-center" role="alert" style="border-radius: 10px;">
                                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                                        <div><?= $_SESSION['error']; ?></div>
                                    </div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <form action="/webbanhang/account/checklogin" method="post">

                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                        <label for="email">Địa chỉ Email</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu" required>
                                        <label for="password">Mật khẩu</label>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="rememberMe">
                                            <label class="form-check-label text-secondary small" for="rememberMe">
                                                Ghi nhớ tôi
                                            </label>
                                        </div>
                                        <a href="/webbanhang/account/forgotPassword" class="link-forgot-new">Quên mật khẩu?</a>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 btn-login-new">
                                        Đăng Nhập <i class="bi bi-arrow-right-short"></i>
                                    </button>

                                    <div class="link-register-new">
                                        <p>Bạn chưa có tài khoản? <a href="/webbanhang/account/register">Đăng ký ngay</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Nút quay về trang chủ (mobile) -->
                <div class="text-center mt-3 d-md-none">
                    <a href="/webbanhang/" class="text-muted text-decoration-none small">
                        <i class="bi bi-arrow-left"></i> Quay về trang chủ
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include 'app/views/shares/footer.php'; ?>