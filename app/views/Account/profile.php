<!-- ==================================================== -->
<!--         MODAL THÔNG TIN CÁ NHÂN (PHIÊN BẢN MỚI)       -->
<!-- ==================================================== -->
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-profile">
            <div class="modal-header">
                <h5 class="modal-title">Hồ Sơ Cá Nhân</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="profileForm" enctype="multipart/form-data">
                    <div class="profile-body">

                        <!-- CỘT TRÁI: AVATAR & THÔNG TIN CƠ BẢN -->
                        <div class="profile-sidebar">
                            <div class="avatar-upload-container">
                                <img id="profileImagePreview" src="/webbanhang/public/uploads/avatar/default.png" class="profile-avatar" alt="Avatar">

                                <!-- Nút upload ảnh đã được style lại -->
                                <label for="image" class="btn-upload-avatar">Thay đổi ảnh</label>
                                <input type="file" id="image" name="image" accept="image/*" class="d-none">
                            </div>

                            <h4 id="profileFullNameDisplay" class="profile-name"></h4>
                            <p id="profileEmailDisplay" class="profile-email"></p>
                        </div>

                        <!-- CỘT PHẢI: CÁC TRƯỜNG FORM -->
                        <div class="profile-form-fields">
                            <!-- Họ và tên -->
                            <div class="form-group">
                                <label for="full_name">Họ và tên</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nhập họ và tên của bạn">
                            </div>

                            <!-- Địa chỉ -->
                            <div class="form-group">
                                <label for="address">Địa chỉ</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Ví dụ: 123 Đường ABC, Phường XYZ...">
                            </div>

                            <!-- Số điện thoại -->
                            <div class="form-group">
                                <label for="phone_number">Số điện thoại</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Nhập số điện thoại">
                            </div>

                            <!-- Ngày sinh -->
                            <div class="form-group">
                                <label for="birth_date">Ngày sinh</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                            </div>

                            <!-- Email (Ẩn đi để không cho sửa) -->
                            <input type="hidden" name="email" id="email_hidden">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button> -->
                        <button type="submit" class="btn btn-primary btn-update-profile">Lưu Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>