<?php
// 1. KHỞI TẠO SESSION & LOGIC CƠ BẢN
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartCount = isset($_SESSION['cart']) && is_array($_SESSION['cart'])
    ? array_sum(array_map(fn($item) => $item['quantity'] ?? 0, $_SESSION['cart']))
    : 0;

// 2. IMPORT MODEL & HELPER
require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../../models/CategoryModel.php');
require_once(__DIR__ . '/../../models/ProductModel.php');
require_once(__DIR__ . '/../../helpers/SessionHelper.php');

$database = new Database();
$db = $database->getConnection();

$categoryModel = new CategoryModel($db);
$categories = $categoryModel->getCategories();

$productModel = new ProductModel($db);
$allproducts = $productModel->getProducts();

// Biến kiểm tra Admin để dùng cho logic hiển thị bên dưới
$isAdmin = SessionHelper::isAdmin();

function isActive0($path)
{
    $currentUrl = $_SERVER['REQUEST_URI'];
    return (strpos($currentUrl, $path) !== false) ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng Mỹ Phẩm The Boys</title>
    <link rel="icon" href="<?= BASE_URL ?>/public/images/Logo.png" type="image/png">

    <!-- 3. CSS LIBS (Giữ nguyên các file CSS của bạn) -->
    <!-- 1. CSS CỦA WEBSITE -->
    <link href="<?= BASE_URL ?>/public/css/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/footer.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/pay.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/login.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/home.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/show.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/product.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/rating.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/order.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/result-search.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/card.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/productAdmin.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/order_list.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/chatbot.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/contact.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/yes_no.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/profile.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/order_detail.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/changepasswor.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/nav.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- 2. BOOTSTRAP 5 & BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- 3. THÊM MỚI: GOOGLE MATERIAL SYMBOLS (Cho Chatbot) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!-- 4. THÊM MỚI: FONTAWESOME (Cho Modal đổi mật khẩu) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- 5. JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>/public/js/modal.js"></script>
    <style>
        /* LOGIC CSS RIÊNG BIỆT CHO 2 GIAO DIỆN */
        <?php if ($isAdmin): ?>

        /* --- GIAO DIỆN ADMIN --- */
        body {
            padding-left: 250px;
            /* Đẩy nội dung sang phải */
            padding-top: 60px;
            /* Tránh header che */
            background-color: #f4f6f9;
        }

        /* Sidebar dọc */
        .admin-sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #212529;
            /* Màu đen */
            color: #fff;
            overflow-y: auto;
            z-index: 1000;
            padding-top: 20px;
        }

        .admin-sidebar .nav-link {
            color: #adb5bd;
            padding: 12px 20px;
            display: flex;
            align-items: center;
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }

        /* Topbar Admin (Màu trắng) */
        .navbar-admin-top {
            height: 60px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1020;
        }

        /* Mobile Admin */
        @media (max-width: 991px) {
            body {
                padding-left: 0;
            }

            .admin-sidebar {
                left: -250px;
                transition: 0.3s;
            }

            .admin-sidebar.show {
                left: 0;
            }
        }

        <?php else: ?>

        /* --- GIAO DIỆN USER (Giữ nguyên) --- */
        body {
            padding-top: 80px;
            /* Chiều cao gradient navbar */
        }

        <?php endif; ?>
    </style>
</head>

<body>
    <?php
    $avatarPath = BASE_URL . '/public/uploads/avatar/default.png';
    if (isset($_SESSION['image']) && !empty($_SESSION['image'])) {
        $avatarPath = BASE_URL . '/' . htmlspecialchars($_SESSION['image']);
    }
    ?>

    <!-- ========================================================= -->
    <!-- PHẦN 1: GIAO DIỆN ADMIN (THANH DỌC)                       -->
    <!-- ========================================================= -->
    <?php if ($isAdmin): ?>

        <!-- A. Topbar Admin (Chỉ chứa Logo, Toggle và Avatar) -->
        <nav class="navbar navbar-expand fixed-top navbar-admin-top px-3">
            <div class="d-flex align-items-center w-100">
                <!-- Nút mở menu trên mobile -->
                <button class="btn border-0 me-2 d-lg-none" onclick="$('.admin-sidebar').toggleClass('show')">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Logo -->
                <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="<?= BASE_URL ?>/Admin/dashboard">
                    <img src="<?= BASE_URL ?>/public/images/Logo.png" alt="Logo" height="35" class="me-2">
                    24/7 Store
                </a>

                <!-- User Dropdown (Góc phải) -->
                <div class="ms-auto dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="admDrp" data-bs-toggle="dropdown">
                        <img src="<?= $avatarPath ?>" width="32" height="32" class="rounded-circle me-2 border object-fit-cover" onerror="this.src='<?= BASE_URL ?>/public/uploads/avatar/default.png'">
                        <span class="d-none d-sm-inline fw-bold small"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Đổi mật khẩu</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/account/logout">Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- B. Sidebar Admin (Thanh dọc bên trái) -->
        <div class="admin-sidebar">
            <div class="px-3 mb-3 text-uppercase text-white-50 small fw-bold">Quản lý cửa hàng</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= isActive0('/Admin/dashboard') ?>" href="<?= BASE_URL ?>/Admin/dashboard">
                        <i class="bi bi-speedometer2 me-3 fs-5"></i> Thống kê
                    </a>
                </li>

                <!-- Dropdown Sản phẩm -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#prodMenu" role="button" aria-expanded="false">
                        <i class="bi bi-box-seam me-3 fs-5"></i> Sản phẩm
                        <i class="bi bi-chevron-down ms-auto small"></i>
                    </a>
                    <div class="collapse <?= (strpos($_SERVER['REQUEST_URI'], 'Category') !== false || strpos($_SERVER['REQUEST_URI'], 'addProduct') !== false) ? 'show' : '' ?>" id="prodMenu">
                        <ul class="nav flex-column ms-3 ps-2 border-start border-secondary">
                            <!-- <li class="nav-item">
                                <a class="nav-link py-1 small" href="<?= BASE_URL ?>/Admin/addProduct">Thêm mới</a>
                            </li> -->
                            <?php foreach ($categories as $category): ?>
                                <?php if ($category->id != 0): ?>
                                    <li class="nav-item">
                                        <a class="nav-link py-1 small" href="<?= BASE_URL ?>/Admin/adminCategoryList/<?= $category->id ?>">
                                            <?= $category->name ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= isActive0('/Admin/orderList') ?>" href="<?= BASE_URL ?>/Admin/orderList">
                        <i class="bi bi-receipt me-3 fs-5"></i> Đơn hàng
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= isActive0('/Admin/manageUsers') ?>" href="<?= BASE_URL ?>/Admin/manageUsers">
                        <i class="bi bi-people me-3 fs-5"></i> Tài khoản
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActive0('/Admin/promotion') ?>" href="<?= BASE_URL ?>/Admin/promotionList">
                        <i class="bi bi-tags me-3 fs-5"></i> khuyến mãi
                    </a>
                </li>

            </ul>
        </div>


        <!-- ========================================================= -->
        <!-- PHẦN 2: GIAO DIỆN USER (THANH NGANG CŨ)                   -->
        <!-- ========================================================= -->
    <?php else: ?>

        <nav class="navbar navbar-expand-lg fixed-top gradient-navbar">
            <div class="container-fluid px-4">
                <!-- Logo -->
                <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/product/home">
                    <img src="<?= BASE_URL ?>/public/images/Logo.png" alt="Logo" height="45">
                    <span class="brand-name">The Boys</span>
                </a>

                <!-- Toggle Mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbar">
                    <i class="bi bi-list text-white"></i>
                </button>

                <!-- Menu User -->
                <div class="collapse navbar-collapse" id="userNavbar">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link <?= isActive0('/product/home') ?>" href="<?= BASE_URL ?>/product/home">Trang chủ</a></li>
                        <li class="nav-item"><a class="nav-link <?= isActive0('/Product/') ?>" href="<?= BASE_URL ?>/Product/showAll">Sản phẩm</a></li>
                        <li class="nav-item"><a class="nav-link <?= isActive0('/shop/contact') ?>" href="<?= BASE_URL ?>/shop/contact">Liên hệ</a></li>
                    </ul>

                    <!-- Hành động bên phải -->
                    <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">

                        <!-- Search Form -->
                        <form class="search-form-gradient" action="<?= BASE_URL ?>/Product/search" method="GET">
                            <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
                            <input class="form-control" type="search" name="keyword" placeholder="Tìm kiếm...">
                        </form>

                        <?php if (isset($_SESSION['email'])): ?>
                            <!-- Cart -->
                            <a class="nav-icon position-relative text-white" href="<?= BASE_URL ?>/cart/index">
                                <i class="bi bi-bag fs-5"></i>
                                <?php if ($cartCount > 0): ?>
                                    <span class="badge-count"><?= $cartCount ?></span>
                                <?php endif; ?>
                            </a>

                            <!-- User Dropdown -->
                            <div class="dropdown d-flex align-items-center gap-2">
                                <span class="welcome-text d-none d-lg-block text-white">Chào, <?= htmlspecialchars($_SESSION['full_name']) ?></span>
                                <a href="#" class="p-0" data-bs-toggle="dropdown">
                                    <img src="<?= $avatarPath ?>" class="user-avatar border" alt="User Avatar" onerror="this.src='<?= BASE_URL ?>/public/uploads/avatar/default.png'">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                                            <i class="bi bi-person-circle me-2 text-primary"></i> Tài khoản của tôi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= BASE_URL ?>/order/index">
                                            <i class="bi bi-box-seam me-2 text-success"></i> Quản lý đơn hàng
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= BASE_URL ?>/AccountPromotion/myPromotions">
                                            <i class="bi bi-ticket-perforated me-2 text-danger"></i> Kho khuyến mãi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                            <i class="bi bi-key me-2 text-warning"></i> Đổi mật khẩu
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/account/logout">
                                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a class="btn btn-primary-gradient" href="<?= BASE_URL ?>/account/login">Đăng nhập</a>
                            <a class="btn btn-primary-gradient" href="<?= BASE_URL ?>/account/register">Đăng ký</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>

    <?php endif; ?>
    <!-- KẾT THÚC HEADER -->