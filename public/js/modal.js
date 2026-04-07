$(document).ready(function () {

    // ================================
    // 1. BIẾN LƯU ẢNH GỐC
    // ================================
    let originalAvatarSrc = '';

    // ================================
    // 2. KHI MỞ MODAL → LOAD PROFILE
    // ================================
    $("#profileModal").on("show.bs.modal", function () {
        $.ajax({
            url: "/webbanhang/account/profile",
            method: "GET",
            dataType: "json",
            success: function (data) {

                if (!data.email) {
                    alert("Không tải được thông tin tài khoản!");
                    return;
                }

                // Gán vào input
                $("#full_name").val(data.full_name);
                $("#address").val(data.address);
                $("#phone_number").val(data.phone_number);
                $("#birth_date").val(data.birth_date);
                $("#email_hidden").val(data.email);

                // Gán vào sidebar
                $("#profileFullNameDisplay").text(data.full_name || "Chưa có tên");
                $("#profileEmailDisplay").text(data.email);

                // Avatar
                let avatar = data.image ? "/webbanhang/" + data.image : "/webbanhang/public/uploads/avatar/default.png";
                $("#profileImagePreview").attr("src", avatar);
                originalAvatarSrc = avatar;

                // Reset input file
                $("#image").val("");
            },
            error: function (xhr) {
                console.error("Lỗi khi tải profile:", xhr.responseText);
                alert("Không thể tải thông tin tài khoản!");
            }
        });
    });

    // ================================
    // 3. XEM TRƯỚC ẢNH AVATAR
    // ================================
    $("#image").on("change", function (event) {
        const file = event.target.files[0];

        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $("#profileImagePreview").attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            $("#profileImagePreview").attr("src", originalAvatarSrc);
        }
    });

    // ================================
    // 4. CẬP NHẬT PROFILE
    // ================================
    $("#profileForm").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "/webbanhang/account/updateProfile",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {

                if (response.success) {
                    alert("Cập nhật thành công!");

                    // Ẩn modal
                    $("#profileModal").modal("hide");

                    // Cập nhật avatar navbar
                    if (response.new_image) {
                        $(".user-avatar").attr("src", "/webbanhang/" + response.new_image);
                    }

                } else {
                    alert("Lỗi: " + response.error);
                }
            },
            error: function (xhr) {
                alert("Lỗi khi cập nhật! Kiểm tra console.");
                console.error(xhr.responseText);
            }
        });
    });

    // ================================
    // 5. ĐỔI MẬT KHẨU
    // ================================
    $("#changePasswordForm").submit(function (event) {
        event.preventDefault();

        let currentPassword = $("#currentPassword").val().trim();
        let newPassword = $("#newPassword").val().trim();
        let confirmPassword = $("#confirmPassword").val().trim();
        let messageBox = $("#changePasswordMessage");

        messageBox.text("").removeClass("text-success text-danger").hide();

        $.ajax({
            url: "/webbanhang/account/changePassword",
            type: "POST",
            data: {
                currentPassword: currentPassword,
                newPassword: newPassword,
                confirmPassword: confirmPassword
            },
            dataType: "json",
            success: function (response) {

                messageBox
                    .text(response.message)
                    .addClass(response.status === "success" ? "text-success" : "text-danger")
                    .show();

                // Nếu đổi mật khẩu thành công → tự động logout
                if (response.status === "success") {
                    setTimeout(function () {
                        window.location.href = "/webbanhang/account/logout";
                    }, 2000);
                }
            },
            error: function (xhr) {
                messageBox.text(xhr.responseText || "Lỗi khi gửi yêu cầu!")
                    .addClass("text-danger")
                    .show();
            }
        });
    });

    // ================================
    // 6. FIX LỖI BACKDROP KHI ĐÓNG MODAL
    // ================================
    $('#profileModal').on('hidden.bs.modal', function () {
        $(".modal-backdrop").remove();
        $("body").removeClass("modal-open").css("padding-right", "");
    });

});
