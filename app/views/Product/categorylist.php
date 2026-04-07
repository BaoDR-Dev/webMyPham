<?php include __DIR__ . '/../shares/header.php'; ?>
<?php include __DIR__ . '/../shares/navbar.php'; ?>
<div class="can22">
    <div class="Nhieu">
        <div class=" row">
            <!-- Cột bên trái: Danh mục sản phẩm -->
            <div class="col-md-3">
                <h3 class="cate">Danh Mục Sản Phẩm</h3>
                <div class="categoryLala">
                    <a class="dropdown-item <?= empty($category_id) ? 'active-category' : '' ?>" href="<?= BASE_URL ?>/Product/showAll">
                        Tất cả
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <?php if ($category->id == 0) continue; ?>
                        <a class="dropdown-item <?= $category->id == $category_id ? 'active-category' : '' ?>"
                            href="<?= BASE_URL ?>/Product/categoryList/<?= htmlspecialchars($category->id) ?>">
                            <?= htmlspecialchars($category->name) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

            </div>

            <div class="col-md-8">
                <h1 class="Topic1"><?= $categoryName ?? 'Danh Mục Không Xác Định'; ?></h1>
                <div class="ahihi">
                    <div class="product-list">
                        <?php if (!empty($products) && is_array($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <?php if (isset($product->status) && $product->status == 1): ?>

                                    <div class="col-ms-2">
                                        <div class="card">
                                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                                                onerror="this.src='<?= BASE_URL ?>/public/images/no-image.png';this.onerror=null;"
                                                class="card-img-top" alt="Hình sản phẩm">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h5>
                                                <p class="product-price">Giá: <?= number_format($product->price, 0, ',', '.'); ?> VND</p>
                                            </div>
                                            <div class="product-actions">
                                                <?php if (SessionHelper::isAdmin()): ?>
                                                    <a href="<?= BASE_URL ?>/Product/edit/<?= $product->id; ?>" class="btn btn-warning">Sửa</a>
                                                    <a href="<?= BASE_URL ?>/Product/delete/<?= $product->id; ?>" class="btn btn-danger"
                                                        onclick="return confirm(' Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>/cart/add/<?= $product->id ?>" class="btn btn-primary">Thêm vào giỏ hàng</a>


                                                    <a href="<?= BASE_URL ?>/Product/view/<?php echo $product->id; ?>?category_id=<?= $category_id ?>" class="btn btn-primary">Chi tiết</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>


                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>


                        <?php else: ?>
                            <p class="text-center col-12">Không có sản phẩm nào trong danh mục này.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>



        </div>
        <?php if ($totalPages > 1): ?>
            <div class="text-end mt-4">
                <nav aria-label="Page navigation">
                    <?= renderPagination($totalPages, $page, isset($category_id) ? "<?= BASE_URL ?>/Product/categoryList/$category_id" : "<?= BASE_URL ?>/Product/showAll") ?>
                </nav>
            </div>
        <?php endif; ?>


    </div>

    <?php include __DIR__ . '/../shares/footer.php'; ?>