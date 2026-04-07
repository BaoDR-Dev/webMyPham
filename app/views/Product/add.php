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
        max-width: 1100px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .textadd {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 1.5rem;
        text-align: left;
    }

    /* Card Layout */
    .form-container-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Labels & Inputs */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    /* CKEditor Custom Styling */
    .limited-editor {
        border: 1px solid #e2e8f0;
        border-radius: 0 0 10px 10px;
        min-height: 180px;
        padding: 10px;
        background: #fff;
    }

    #toolbar-container-description,
    #toolbar-container-full-description {
        border: 1px solid #e2e8f0;
        border-bottom: none;
        border-radius: 10px 10px 0 0;
        background: #f8fafc;
    }

    /* Image Upload Section */
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        background: #f8fafc;
        transition: 0.3s;
        cursor: pointer;
    }

    .upload-zone:hover {
        border-color: var(--primary-color);
        background: #f1f5f9;
    }

    .product-image-preview {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-top: 10px;
    }

    /* Action Buttons */
    .btn-submit {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.8rem;
        border-radius: 12px;
        font-weight: 700;
        transition: 0.3s;
    }

    .btn-submit:hover {
        background: #4338ca;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3);
    }
</style>

<div class="khuon-add">
    <div class="d-flex align-items-center mb-4">
        <div class="icon-box bg-primary text-white rounded-3 p-2 me-3 shadow">
            <i class="bi bi-plus-circle-fill fs-4"></i>
        </div>
        <h1 class="textadd m-0">Thêm sản phẩm mới</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/webbanhang/Product/save" enctype="multipart/form-data" class="form-container-card">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="form-group">
                    <label for="name-add-product">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="name-add-product" name="name" required placeholder="Nhập tên sản phẩm...">
                </div>

                <div class="form-group">
                    <label>Mô tả ngắn</label>
                    <div id="toolbar-container-description"></div>
                    <div class="limited-editor" id="editor-description"></div>
                    <input type="hidden" id="description-input" name="description">
                </div>

                <div class="form-group">
                    <label>Mô tả chi tiết</label>
                    <div id="toolbar-container-full-description"></div>
                    <div class="limited-editor" id="editor-full-description"></div>
                    <input type="hidden" id="full-description-input" name="full_description">
                </div>
            </div>

            <div class="col-lg-4">
                <div class="p-4 border rounded-4 bg-light bg-opacity-50">
                    <div class="form-group">
                        <label for="price">Giá bán (VNĐ)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">₫</span>
                            <input type="number" id="price" name="price" class="form-control border-start-0 fw-bold text-primary" step="0.01" required placeholder="0.00">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Danh mục</label>
                        <select id="category_id" name="category_id" class="form-control" required>

                            <?php foreach ($categories as $category): ?>
                                <?php if ($category->id == 0) continue; ?>
                                <option value="<?= htmlspecialchars($category->id); ?>">
                                    <?= htmlspecialchars($category->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="quantity">Số lượng</label>
                                <input type="number" id="quantity" name="quantity" class="form-control" min="0" required placeholder="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="status">Trạng thái</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="1">Hoạt động</option>
                                    <option value="0">Tạm dừng</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hình ảnh sản phẩm</label>
                        <label for="image" class="upload-zone d-block">
                            <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                            <p class="small text-muted mt-2 mb-0">Click để tải ảnh hoặc kéo thả vào đây</p>
                            <input type="file" id="image" name="image" class="d-none" onchange="previewImage(event)">
                        </label>
                        <div id="image-preview" class="mt-3 text-center"></div>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-plus-lg me-2"></i>Tạo sản phẩm
                    </button>
                    <a href="/webbanhang/Admin/adminCategoryList/1" class="btn btn-light border py-2 text-muted fw-bold rounded-3">
                        Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Footer -->
<?php include __DIR__ . '/../shares/footer.php'; ?>

<!-- CKEditor 5 Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/decoupled-document/ckeditor.js"></script>

<script>
    let editorInstanceDescription, editorInstanceFullDescription;

    // CKEditor for description
    DecoupledEditor
        .create(document.querySelector('#editor-description'))
        .then(editor => {
            const toolbarContainer = document.querySelector('#toolbar-container-description');
            toolbarContainer.appendChild(editor.ui.view.toolbar.element);
            editorInstanceDescription = editor;
        })
        .catch(error => {
            console.error('Error initializing CKEditor for description:', error);
        });

    // CKEditor for full_description
    DecoupledEditor
        .create(document.querySelector('#editor-full-description'))
        .then(editor => {
            const toolbarContainer = document.querySelector('#toolbar-container-full-description');
            toolbarContainer.appendChild(editor.ui.view.toolbar.element);
            editorInstanceFullDescription = editor;
        })
        .catch(error => {
            console.error('Error initializing CKEditor for full_description:', error);
        });

    // Update hidden input fields with CKEditor data before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        document.querySelector('#description-input').value = editorInstanceDescription.getData();
        document.querySelector('#full-description-input').value = editorInstanceFullDescription.getData();
    });

    // Preview image function
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');
        preview.innerHTML = ''; // Clear previous image preview

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

    // Preview image với hiệu ứng mượt mà
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('product-image-preview', 'img-fluid');
                img.style.maxHeight = '200px';
                preview.appendChild(img);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>