<?php include 'app/views/shares/header.php'; ?>

<!-- Nhúng CKEditor 5 Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/decoupled-document/ckeditor.js"></script>

<style>
    /* Style cho CKEditor giống trang Product */
    .limited-editor {
        min-height: 250px;
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
        margin-top: 5px;
    }

    .ck-editor__editable_inline {
        min-height: 250px;
    }
</style>

<div class="container-fluid mt-4">
    <div class="khuon-add">
        <div class="d-flex align-items-center mb-4">
            <div class="icon-box bg-primary text-white rounded-3 p-2 me-3 shadow">
                <i class="bi bi-gift-fill fs-4"></i>
            </div>
            <h1 class="textadd m-0">Thêm khuyến mãi mới</h1>
        </div>

        <form method="POST"
            action="/webbanhang/Admin/storePromotion"
            class="form-container-card"
            id="promotionForm">

            <div class="row g-4">

                <!-- CỘT TRÁI: THÔNG TIN CHÍNH -->
                <div class="col-lg-8">
                    <!-- TÊN KHUYẾN MÃI -->
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Tên khuyến mãi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ví dụ: Giảm giá Giáng Sinh" required>
                    </div>

                    <!-- NỘI DUNG (SỬ DỤNG CKEDITOR) -->
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Mô tả nội dung</label>
                        <div id="toolbar-container-content"></div>
                        <div class="limited-editor" id="editor-content"></div>
                        <!-- Input ẩn để lưu dữ liệu khi submit -->
                        <input type="hidden" id="content-input" name="content">
                    </div>

                    <!-- NGÀY THÁNG -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="fw-bold mb-1">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="startDate"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold mb-1">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="endDate"
                                class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI: CẤU HÌNH -->
                <div class="col-lg-4">
                    <div class="p-4 border rounded-4 bg-light shadow-sm">

                        <!-- LOẠI KHUYẾN MÃI -->
                        <div class="form-group mb-3">
                            <label class="fw-bold mb-1">Loại khuyến mãi <span class="text-danger">*</span></label>
                            <select name="promotion_type_id"
                                id="promotionType"
                                class="form-control"
                                required>
                                <option value="">-- Chọn loại --</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= $type->id ?>">
                                        <?= htmlspecialchars($type->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- GIÁ TRỊ GIẢM -->
                        <div class="form-group mb-3" id="discountBox">
                            <label class="fw-bold mb-1">Giá trị giảm</label>
                            <div class="input-group">
                                <input type="number" name="discount_value" class="form-control" step="0.01" min="0" placeholder="0.00">
                                <span class="input-group-text bg-white">VNĐ/%</span>
                            </div>
                        </div>

                        <!-- ĐƠN HÀNG TỐI THIỂU -->
                        <div class="form-group mb-3">
                            <label class="fw-bold mb-1">Đơn hàng tối thiểu</label>
                            <div class="input-group">
                                <input type="number" name="min_order_amount" class="form-control" min="0" placeholder="0">
                                <span class="input-group-text bg-white">₫</span>
                            </div>
                        </div>

                        <!-- DANH MỤC -->
                        <div class="form-group mb-3" id="categoryBox">
                            <label class="fw-bold mb-1">Áp dụng cho danh mục</label>
                            <select name="category_id" id="categorySelect" class="form-control">
                                <option value="">-- Toàn bộ sản phẩm --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat->id ?>">
                                        <?= htmlspecialchars($cat->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- CHECKBOX CẤU HÌNH -->
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="apply_per_product" id="applyPerProduct" value="1">
                            <label class="form-check-label fw-bold" for="applyPerProduct">Áp dụng từng sản phẩm</label>
                        </div>

                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                            <label class="form-check-label fw-bold" for="isActive">Kích hoạt ngay</label>
                        </div>
                    </div>

                    <!-- NÚT HÀNH ĐỘNG -->
                    <div class="mt-4 d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                            <i class="bi bi-save me-2"></i>Lưu khuyến mãi
                        </button>
                        <a href="/webbanhang/Admin/promotionList" class="btn btn-outline-secondary border-0">
                            Quay lại danh sách
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    let editorInstanceContent;

    // 1. Khởi tạo CKEditor
    DecoupledEditor
        .create(document.querySelector('#editor-content'))
        .then(editor => {
            const toolbarContainer = document.querySelector('#toolbar-container-content');
            toolbarContainer.appendChild(editor.ui.view.toolbar.element);
            editorInstanceContent = editor;
        })
        .catch(error => {
            console.error('Lỗi CKEditor:', error);
        });

    const form = document.getElementById('promotionForm');
    const typeSelect = document.getElementById('promotionType');
    const discountBox = document.getElementById('discountBox');
    const categoryBox = document.getElementById('categoryBox');

    // 2. Ẩn hiện các trường dựa trên loại khuyến mãi
    function toggleFields() {
        const type = parseInt(typeSelect.value);
        // Giả sử ID 3 là Miễn phí vận chuyển (Freeship)
        if (type === 3) {
            discountBox.style.display = 'none';
            categoryBox.style.display = 'none';
        } else {
            discountBox.style.display = 'block';
            categoryBox.style.display = 'block';
        }
    }

    typeSelect.addEventListener('change', toggleFields);

    // 3. Xử lý trước khi Submit
    form.addEventListener('submit', function(e) {
        // Gán dữ liệu từ CKEditor vào input ẩn
        if (editorInstanceContent) {
            document.getElementById('content-input').value = editorInstanceContent.getData();
        }

        // Kiểm tra ngày tháng
        const start = new Date(document.getElementById('startDate').value);
        const end = new Date(document.getElementById('endDate').value);

        if (start > end) {
            e.preventDefault();
            alert('❌ Ngày bắt đầu không thể lớn hơn ngày kết thúc!');
        }
    });
</script>

<?php include 'app/views/shares/footer.php'; ?>