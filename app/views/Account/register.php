<?php include 'app/views/shares/header.php'; ?>

<!-- CSS RIÊNG CHO TRANG ĐĂNG KÝ (STYLE NEW) -->
<style>
    body {
        background-color: #f0f2f5;
    }

    .section-register-new {
        min-height: 90vh;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .card-register-new {
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
    .card-register-new:focus-within,
    .card-register-new:focus,
    .card-register-new:hover {
        box-shadow: none !important;
        transform: none !important;
        outline: none !important;
        filter: none !important;
        opacity: 1 !important;
        transition: none !important;
    }

    /* Banner bên trái */
    .banner-register-new {
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
        min-height: 500px;
    }

    .banner-register-new::before {
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

    .banner-register-new h3 {
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 15px;
        z-index: 1;
    }

    .banner-register-new p {
        font-size: 1.1rem;
        opacity: 0.9;
        z-index: 1;
        margin-bottom: 30px;
    }

    .btn-outline-light-custom {
        border: 2px solid rgba(255, 255, 255, 0.8);
        color: white;
        padding: 10px 30px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        z-index: 1;
        /* Đảm bảo không có hiệu ứng zoom/mờ */
        transform: none !important;
        box-shadow: none !important;
        outline: none !important;
        filter: none !important;
        opacity: 1 !important;
    }

    .btn-outline-light-custom:hover {
        background: white;
        color: #0d6efd;
        /* Đảm bảo không có hiệu ứng zoom/mờ khi hover */
        transform: none !important;
        box-shadow: none !important;
        outline: none !important;
        filter: none !important;
        opacity: 1 !important;
    }

    .btn-outline-light-custom:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    /* Form bên phải */
    .form-wrapper-register {
        padding: 50px 40px;
    }

    .title-register-new {
        font-weight: 800;
        color: #333;
        margin-bottom: 10px;
    }

    .form-floating>.form-control {
        border-radius: 10px;
        border: 1px solid #dee2e6;
    }

    .form-floating>.form-control:focus {
        border-color: #0d6efd;
        /* Loại bỏ box-shadow và outline khi input được focus */
        box-shadow: none !important;
        outline: none !important;
        filter: none !important;
    }

    .btn-register-new {
        background: linear-gradient(to right, #0d6efd, #0099ff);
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
        color: white;
        /* Chỉ giữ transition cho background để đổi màu mượt mà */
        transition: background 0.3s ease !important;
        margin-top: 10px;
        /* Loại bỏ hiệu ứng zoom, box-shadow, outline */
        transform: none !important;
        box-shadow: none !important;
        outline: none !important;
        filter: none !important;
        opacity: 1 !important;
    }

    .btn-register-new:hover {
        /* Đảm bảo không có hiệu ứng zoom, box-shadow khi hover */
        transform: none !important;
        box-shadow: none !important;
        background: linear-gradient(to right, #0b5ed7, #007bc4);
        color: white;
        filter: none !important;
        opacity: 1 !important;
    }

    .btn-register-new:focus {
        outline: none !important;
        box-shadow: none !important;
        filter: none !important;
        opacity: 1 !important;
    }
</style>

<section class="section-register-new">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-lg-12">
                <div class="card card-register-new">
                    <div class="row g-0">

                        <!-- CỘT TRÁI: BANNER -->
                        <div class="col-lg-5 d-none d-lg-block">
                            <div class="banner-register-new">
                                <img src="/webbanhang/public/images/Logo.png" alt="Logo" style="width: 80px; margin-bottom: 20px; z-index: 1;">
                                <h3>Tham gia cùng<br>Y Tế 24/7</h3>
                                <p>Tạo tài khoản để nhận ưu đãi hấp dẫn và theo dõi đơn hàng dễ dàng.</p>
                                <a href="/webbanhang/account/login" class="btn-outline-light-custom">Đã có tài khoản?</a>
                            </div>
                        </div>

                        <!-- CỘT PHẢI: FORM ĐĂNG KÝ -->
                        <div class="col-lg-7">
                            <div class="form-wrapper-register">
                                <h2 class="title-register-new text-center text-lg-start">Tạo Tài Khoản Mới</h2>
                                <p class="text-muted mb-4 text-center text-lg-start">Vui lòng điền đầy đủ thông tin bên dưới.</p>

                                <?php if (isset($errors) && !empty($errors)): ?>
                                    <div class="alert alert-danger" style="border-radius: 10px;">
                                        <ul class="mb-0 ps-3">
                                            <?php foreach ($errors as $err): ?>
                                                <li><?= htmlspecialchars($err) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <form action="/webbanhang/account/save" method="post">

                                    <!-- Họ tên & Email -->
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Họ và tên" required>
                                        <label for="fullname"><i class="bi bi-person me-1"></i> Họ và Tên</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                        <label for="email"><i class="bi bi-envelope me-1"></i> Địa chỉ Email</label>
                                    </div>

                                    <!-- Mật khẩu (Chia 2 cột) -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu" required>
                                                <label for="password"><i class="bi bi-lock me-1"></i> Mật khẩu</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Xác nhận" required>
                                                <label for="confirmpassword"><i class="bi bi-shield-check me-1"></i> Xác nhận MK</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Số điện thoại & Ngày sinh (Chia 2 cột) -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="SĐT">
                                                <label for="phone_number"><i class="bi bi-phone me-1"></i> Số điện thoại</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                                                <label for="birth_date"><i class="bi bi-calendar me-1"></i> Ngày sinh</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Địa chỉ -->
                                    <div class="form-floating mb-4">
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Địa chỉ">
                                        <label for="address"><i class="bi bi-geo-alt me-1"></i> Địa chỉ giao hàng</label>
                                    </div>

                                    <!-- Button Submit -->
                                    <button type="submit" class="btn btn-primary w-100 btn-register-new">
                                        Đăng Ký Tài Khoản
                                    </button>

                                    <!-- Link Mobile Only -->
                                    <div class="text-center mt-4 d-lg-none">
                                        <p>Đã có tài khoản? <a href="/webbanhang/account/login" class="text-primary fw-bold text-decoration-none">Đăng nhập ngay</a></p>
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