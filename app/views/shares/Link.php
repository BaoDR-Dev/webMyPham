<?php
$currentUrl = $_SERVER['REQUEST_URI'];

$breadcrumbs = [
    [
        'name' => 'Trang chủ',
        'url' => '/webbanhang/product/home'
    ]
];

// Mapping các nav link
$navItems = [
    '/news' => 'Tin tức',
    '/cart/index' => 'Giỏ hàng',
    '/shares/contact' => 'Liên hệ',
    '/account/login' => 'Đăng nhập',
    '/order/index' => 'Đơn hàng',
];

// Kiểm tra nav page
foreach ($navItems as $path => $title) {
    if (strpos(strtolower($currentUrl), strtolower($path)) !== false) {
        $breadcrumbs[] = [
            'name' => $title,
            'url' => '/webbanhang' . $path
        ];
        break;
    }
}

// Nếu là trang chi tiết sản phẩm
if (preg_match('#/product/view/(\d+)#i', $currentUrl, $matches)) {
    $productId = $matches[1];
    $categoryId = $_GET['category_id'] ?? null;

    // Nếu có category_id từ URL thì thêm danh mục
    if ($categoryId) {
        foreach ($categories as $cat) {
            if ($cat->id == $categoryId) {
                $breadcrumbs[] = [
                    'name' => htmlspecialchars($cat->name),
                    'url' => '/webbanhang/Product/categoryList/' . $cat->id
                ];
                break;
            }
        }
    }

    // Thêm tên sản phẩm cuối cùng
    foreach ($allproducts as $prod) {
        if ($prod->id == $productId) {
            $breadcrumbs[] = [
                'name' => htmlspecialchars($prod->name),
                'url' => '' // Trang hiện tại
            ];
            break;
        }
    }
}

// Nếu là trang danh mục sản phẩm
elseif (preg_match('#/product/categorylist/(\d+)#i', $currentUrl, $matches)) {
    $categoryId = $matches[1];
    foreach ($categories as $cat) {
        if ($cat->id == $categoryId) {
            $breadcrumbs[] = [
                'name' => htmlspecialchars($cat->name),
                'url' => '' // trang hiện tại
            ];
            break;
        }
    }
}
?>

<nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '>'; margin-top: 10px;">
    <ol class="breadcrumb bg-light p-2 rounded">
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <?php if ($index === array_key_last($breadcrumbs)): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= $crumb['name'] ?></li>
            <?php else: ?>
                <li class="breadcrumb-item">
                    <a href="<?= $crumb['url'] ?>"><?= $crumb['name'] ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>