<?php include 'app/views/shares/header.php'; ?>

<style>
    body {
        background-color: #f0f2f5;
    }

    .section-otp {
        min-height: 85vh;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .card-otp {
        border-radius: 20px;
        border: none;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        background: #fff;
        width: 100%;
    }

    .banner-otp {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 40px;
        height: 100%;
        min-height: 400px;
    }

    .banner-otp i {
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .form-wrapper-otp {
        padding: 50px 40px;
    }

    .otp-input {
        letter-spacing: 5px;
        font-weight: bold;
        font-size: 1.5rem;
        text-align: center;
    }

    .btn-verify {
        background: linear-gradient(to right, #667eea, #764ba2);
        border: none;
        color: white;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        width: 100%;
    }

    .btn-verify:hover {
        opacity: 0.9;
        color: white;
        transform: translateY(-2px);
    }
</style>

<section class="section-otp">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                <div class="card card-otp">
                    <div class="row g-0">
                        <!-- Cột Trái -->
                        <div class="col-md-5 d-none d-md-block">
                            <div class="banner-otp">
                                <i class="bi bi-shield-check"></i>
                                <h3>Xác Thực OTP</h3>
                                <p>Chúng tôi đã gửi mã 6 số đến email:</p>
                                <p class="fw-bold"><?= isset($_SESSION['temp_email']) ? $_SESSION['temp_email'] : '...' ?></p>
                            </div>
                        </div>

                        <!-- Cột Phải -->
                        <div class="col-md-7">
                            <div class="form-wrapper-otp">
                                <h3 class="fw-bold text-center mb-4">Nhập mã xác thực</h3>

                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger text-center"><?= $_SESSION['error'];
                                                                                unset($_SESSION['error']); ?></div>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['success_message'])): ?>
                                    <div class="alert alert-success text-center"><?= $_SESSION['success_message'];
                                                                                    unset($_SESSION['success_message']); ?></div>
                                <?php endif; ?>

                                <form action="/webbanhang/account/verifyOtp" method="post">
                                    <div class="mb-4">
                                        <label class="form-label text-muted small">Nhập mã 6 số trong email</label>
                                        <input type="text" class="form-control form-control-lg otp-input" name="otp" maxlength="6" placeholder="------" required autofocus>
                                    </div>

                                    <button type="submit" class="btn btn-verify mb-3">Xác Thực Ngay</button>

                                    <div class="text-center">
                                        <p class="small text-muted">Chưa nhận được mã? <a href="#" class="text-primary fw-bold">Gửi lại</a></p>
                                        <a href="/webbanhang/account/register" class="text-decoration-none small text-secondary">Đăng ký lại</a>
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