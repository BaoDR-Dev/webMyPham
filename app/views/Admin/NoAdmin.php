<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
        text-align: center;
    }

    .no-access-wrapper {
        margin-top: 100px;
    }

    .no-access-box {
        display: inline-block;
        background-color: #fff;
        padding: 40px 50px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .lock-icon {
        font-size: 50px;
        color: #e74c3c;
        margin-bottom: 20px;
    }

    .no-access-title {
        font-size: 28px;
        color: #e74c3c;
        margin-bottom: 20px;
    }

    .no-access-message {
        font-size: 18px;
        color: #333;
        margin-bottom: 30px;
    }

    .no-access-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .no-access-btn {
        text-decoration: none;
        background-color: #3498db;
        color: #fff;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .no-access-btn:hover {
        background-color: #2980b9;
    }
</style>

<body>
    <div class="no-access-wrapper">
        <div class="no-access-box">

            <img src="/webbanhang/public/images/Khoa.jpg" alt="Logo" height="200">
            <h1 class="no-access-title">Bạn không có quyền Admin</h1>
            <p class="no-access-message">Bạn cần có quyền quản trị để truy cập nội dung này.</p>
            <div class="no-access-buttons">
                <a href="/webbanhang/account/login" class="no-access-btn">Đăng nhập lại</a>
                <a href="/webbanhang/product/home" class="no-access-btn">Về trang chủ</a>
            </div>
        </div>
    </div>
</body>