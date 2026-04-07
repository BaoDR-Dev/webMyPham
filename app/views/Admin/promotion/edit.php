<?php include 'app/views/shares/header.php'; ?>

<!-- Nhúng CKEditor 5 Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/decoupled-document/ckeditor.js"></script>

<style>
    /* CSS để làm cho editor trông giống thẻ input */
    .limited-editor {
        min-height: 200px;
        border: 1px solid #ced4da;
        padding: 15px;
        background-color: #fff;
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    #toolbar-container-content {
        border: 1px solid #ced4da;
        border-bottom: none;
        background-color: #f8f9fa;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    .ck-editor__editable_inline {
        min-height: 200px;
    }
</style>

<div class="container-fluid mt-4">
    <div class="d-flex align-items-center mb-4">
        <div class="icon-box bg-warning text-white rounded-3 p-2 me-3 shadow">
            <i class="bi bi-pencil-square fs-4"></i>
        </div>
        <h1 class="textadd m-0">Chỉnh sửa khuyến mãi</h1>
    </div>

    <form method="POST"
        action="/webbanhang/Admin/updatePromotion"
        class="form-container-card"
        id="promotionForm">

        <input type="hidden" name="id" value="<?= (int)$promotion->id ?>">

        <div class="row g-4">

            <!-- LEFT -->
            <div class="col-lg-8">
                <!-- NAME -->
                <div class="form-group">
                    <label>Tên khuyến mãi <span class="text-danger">*</span></label>
                    <input type="text"
                        name="name"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($promotion->name ?? '') ?>">
                </div>

                <!-- CONTENT (CKEDITOR) -->
                <div class="form-group">
                    <label>Nội dung khuyến mãi</label>
                    <!-- Container cho toolbar -->
                    <div id="toolbar-container-content"></div>
                    <!-- Vùng chỉnh sửa -->
                    <div class="limited-editor" id="editor-content">
                        <?= $promotion->content ?? '' // Đổ nội dung HTML trực tiếp vào đây 
                        ?>
                    </div>
                    <!-- Input ẩn để gửi dữ liệu lên Server -->
                    <input type="hidden" id="content-input" name="content">
                </div>

                <!-- DATE -->
                <div class="row">
                    <div class="col-md-6">
                        <label>Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date"
                            name="start_date"
                            id="start_date"
                            class="form-control"
                            required
                            value="<?= $promotion->start_date ? date('Y-m-d', strtotime($promotion->start_date)) : '' ?>">
                    </div>

                    <div class="col-md-6">
                        <label>Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date"
                            name="end_date"
                            id="end_date"
                            class="form-control"
                            required
                            value="<?= $promotion->end_date ? date('Y-m-d', strtotime($promotion->end_date)) : '' ?>">
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-lg-4">
                <div class="p-4 border rounded-4 bg-light">
                    <!-- PROMOTION TYPE -->
                    <div class="form-group">
                        <label>Loại khuyến mãi <span class="text-danger">*</span></label>
                        <select name="promotion_type_id"
                            id="promotionType"
                            class="form-control"
                            required>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= $type->id ?>"
                                    <?= $promotion->promotion_type_id == $type->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- DISCOUNT -->
                    <div class="form-group" id="discountBox">
                        <label>Giá trị giảm</label>
                        <input type="number"
                            name="discount_value"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="<?= htmlspecialchars($promotion->discount_value ?? '') ?>">
                    </div>

                    <!-- CATEGORY -->
                    <div class="form-group">
                        <label>Danh mục áp dụng</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- Toàn bộ sản phẩm --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>"
                                    <?= $promotion->category_id == $cat->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mt-3">
                        <label class="fw-bold">
                            <input type="checkbox"
                                name="apply_per_product"
                                value="1"
                                <?= !empty($promotion->apply_per_product) ? 'checked' : '' ?>>
                            Áp dụng cho từng sản phẩm
                        </label>
                    </div>

                    <!-- ACTIVE -->
                    <div class="form-group mt-3">
                        <label class="fw-bold">
                            <input type="checkbox"
                                name="is_active"
                                value="1"
                                <?= !empty($promotion->is_active) ? 'checked' : '' ?>>
                            Kích hoạt khuyến mãi
                        </label>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    <button type="submit" class="btn btn-submit btn-warning text-white fw-bold">
                        <i class="bi bi-save me-2"></i>Cập nhật khuyến mãi
                    </button>
                    <a href="/webbanhang/Admin/promotionList"
                        class="btn btn-light border text-muted">
                        Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let editorInstanceContent;

    // Khởi tạo CKEditor cho phần nội dung
    DecoupledEditor
        .create(document.querySelector('#editor-content'))
        .then(editor => {
            const toolbarContainer = document.querySelector('#toolbar-container-content');
            toolbarContainer.appendChild(editor.ui.view.toolbar.element);
            editorInstanceContent = editor;
        })
        .catch(error => {
            console.error('Lỗi khi khởi tạo CKEditor:', error);
        });

    const promotionType = document.getElementById('promotionType');
    const discountBox = document.getElementById('discountBox');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const form = document.getElementById('promotionForm');

    function toggleDiscount() {
        // Ví dụ: ID 3 là Miễn phí vận chuyển
        discountBox.style.display = (promotionType.value == 3) ? 'none' : 'block';
    }

    promotionType.addEventListener('change', toggleDiscount);
    toggleDiscount();

    // Xử lý trước khi Submit Form
    form.addEventListener('submit', function(e) {
        // 1. Chuyển dữ liệu từ CKEditor vào input ẩn
        if (editorInstanceContent) {
            document.querySelector('#content-input').value = editorInstanceContent.getData();
        }

        // 2. Kiểm tra ngày tháng
        if (startDate.value && endDate.value && startDate.value > endDate.value) {
            e.preventDefault();
            alert('❌ Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc');
        }
    });
</script>

<?php include 'app/views/shares/footer.php'; ?>