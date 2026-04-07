<?php include __DIR__ . '/../shares/header.php'; ?>
<?php include __DIR__ . '/../shares/navbar.php'; ?>


<div class="container mt-4">
    <h1>Danh sách sản phẩm</h1>
    <div class="product-list row">

        <?php if (!empty($products) && is_array($products)): ?>
            <?php foreach ($products as $product): ?>
                <?php if (isset($product->status) && $product->status == 1): // Chỉ hiển thị sản phẩm active 
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card product-card">
                            <!-- Hình ảnh sản phẩm -->
                            <img src="/webbanhang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                                onerror="this.src='/webbanhang/public/images/no-image.png';this.onerror=null;"
                                class="card-img-top"
                                alt="Hình sản phẩm">

                            <!-- Nội dung sản phẩm -->
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h5>
                                <p class="product-price">Giá: <?php echo number_format($product->price, 0, ',', '.'); ?> VND</p>
                                <p class="product-quantity">Số lượng: <?php echo $product->quantity; ?> sản phẩm</p>
                            </div>

                            <!-- Nút hành động -->
                            <div class="product-actions">
                                <?php if (SessionHelper::isAdmin()): ?>
                                    <a href="/webbanhang/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning">Sửa</a>
                                    <a href="/webbanhang/Product/delete/<?php echo $product->id; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
                                    <a href="/webbanhang/Product/view/<?php echo $product->id; ?>" class="btn btn-primary">Chi tiết</a>
                                <?php else: ?>
                                    <a href="/webbanhang/Cart/add/<?php echo $product->id; ?>" class="btn btn-primary">Thêm vào giỏ hàng</a>
                                    <a href="/webbanhang/Product/view/<?php echo $product->id; ?>" class="btn btn-info">Chi tiết</a>
                                <?php endif; ?>
                            </div>
                        </div>



                    </div>


                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Không có sản phẩm nào.</p>
        <?php endif; ?>
    </div>


    <?php include __DIR__ . '/../shares/footer.php'; ?>