<?php include __DIR__ . '/../shares/header.php'; ?>
<style>
    :root {
        --primary-color: #4f46e5;
        --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    body {
        background: var(--bg-gradient);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    .khuon-add {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .textadd {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    /* Card thiết kế trắng tinh tế */
    .form-container-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Group form gọn gàng */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
        background-color: #fcfdfe;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        background-color: #fff;
    }

    /* Tối ưu trình soạn thảo CKEditor */
    .limited-editor {
        border: 1px solid #e2e8f0;
        border-radius: 0 0 10px 10px;
        min-height: 150px;
        padding: 10px;
    }

    #toolbar-container-description,
    #toolbar-container-full-description {
        border: 1px solid #e2e8f0;
        border-bottom: none;
        border-radius: 10px 10px 0 0;
        background: #f8fafc;
    }

    /* Preview hình ảnh */
    .product-image-preview {
        border-radius: 12px;
        border: 2px dashed #e2e8f0;
        padding: 5px;
        max-height: 250px;
        object-fit: contain;
    }

    /* Nút bấm hiện đại */
    .btn-save {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.8rem;
        border-radius: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.3s;
    }

    .btn-save:hover {
        background: #4338ca;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3);
    }

    .btn-back {
        background: transparent;
        color: #64748b;
        border: 1px solid #e2e8f0;
        padding: 0.8rem;
        border-radius: 12px;
        transition: all 0.2s;
    }

    .btn-back:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
</style>
<div class="khuon-add">
    <h1 class="textadd">Chỉnh sửa sản phẩm</h1>

    <form method="POST" action="/webbanhang/Product/update" enctype="multipart/form-data" class="form-container-card">
        <input type="hidden" name="id" value="<?= htmlspecialchars($product->id); ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="form-group">
                    <label for="name">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($product->name); ?>" required placeholder="Ví dụ: iPhone 15 Pro Max">
                </div>

                <div class="form-group">
                    <label>Mô tả ngắn</label>
                    <div id="toolbar-container-description"></div>
                    <div class="limited-editor" id="editor-description"><?= htmlspecialchars_decode($product->description); ?></div>
                    <input type="hidden" name="description" id="description-input">
                </div>

                <div class="form-group">
                    <label>Mô tả chi tiết</label>
                    <div id="toolbar-container-full-description"></div>
                    <div class="limited-editor" id="editor-full-description"><?= htmlspecialchars_decode($product->full_description); ?></div>
                    <input type="hidden" name="full_description" id="full-description-input">
                </div>
            </div>

            <div class="col-lg-4">
                <div class="p-3 border rounded-4 bg-light bg-opacity-50">
                    <div class="form-group">
                        <label for="price">Giá bán (VNĐ)</label>
                        <input type="number" id="price" name="price" class="form-control fw-bold text-primary" value="<?= htmlspecialchars($product->price); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Danh mục sản phẩm</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <?php foreach ($categories as $category): ?>
                                <?php if ($category->id == 0) continue; ?>
                                <option value="<?= $category->id; ?>" <?= $category->id == $product->category_id ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($category->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="quantity">Số lượng</label>
                                <input type="number" id="quantity" name="quantity" class="form-control" value="<?= (int)$product->quantity ?>" required min="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="status">Trạng thái</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="1" <?= $product->status == 1 ? 'selected' : ''; ?>>Mở bán</option>
                                    <option value="0" <?= $product->status == 0 ? 'selected' : ''; ?>>Tạm ẩn</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hình ảnh sản phẩm</label>
                        <div class="mb-2 text-center" id="image-preview">
                            <?php if (!empty($product->image)): ?>
                                <img src="/webbanhang/<?= $product->image; ?>" class="product-image-preview img-fluid">
                            <?php else: ?>
                                <div class="py-4 text-muted small border rounded">Chưa có ảnh</div>
                            <?php endif; ?>
                        </div>
                        <input type="file" id="image" name="image" class="form-control form-control-sm" onchange="previewImage(event)">
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($product->image); ?>">
                    </div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    <button type="submit" class="btn btn-save">
                        <i class="bi bi-check-circle me-2"></i>Cập nhật ngay
                    </button>
                    <a href="/webbanhang/Admin/adminCategoryList/<?= $product->category_id; ?>" class="btn btn-back">
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../shares/footer.php'; ?>

<!-- CKEditor Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/decoupled-document/ckeditor.js"></script>
<script>
    let editorInstanceDescription, editorInstanceFullDescription;

    DecoupledEditor
        .create(document.querySelector('#editor-description'))
        .then(editor => {
            document.querySelector('#toolbar-container-description').appendChild(editor.ui.view.toolbar.element);
            editorInstanceDescription = editor;
        });

    DecoupledEditor
        .create(document.querySelector('#editor-full-description'))
        .then(editor => {
            document.querySelector('#toolbar-container-full-description').appendChild(editor.ui.view.toolbar.element);
            editorInstanceFullDescription = editor;
        });

    document.querySelector('form').addEventListener('submit', function() {
        document.querySelector('#description-input').value = editorInstanceDescription.getData();
        document.querySelector('#full-description-input').value = editorInstanceFullDescription.getData();
    });

    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('product-image-preview');
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                preview.appendChild(img);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>