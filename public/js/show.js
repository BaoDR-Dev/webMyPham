
document.addEventListener("DOMContentLoaded", function () {
    const quantityInput = document.getElementById('quantity');
    const increaseBtn = document.getElementById('increase');
    const decreaseBtn = document.getElementById('decrease');

    const buyNowQty = document.getElementById('buy-now-quantity');
    const cartQty = document.getElementById('cart-quantity');

    // Chỉ chạy logic số lượng khi các element tồn tại (trang chi tiết sản phẩm)
    if (quantityInput && increaseBtn && decreaseBtn) {
        function updateHiddenQuantities() {
            const qty = quantityInput.value;
            if (buyNowQty) buyNowQty.value = qty;
            if (cartQty) cartQty.value = qty;
        }

        increaseBtn.addEventListener('click', () => {
            let qty = parseInt(quantityInput.value);
            quantityInput.value = qty + 1;
            updateHiddenQuantities();
        });

        decreaseBtn.addEventListener('click', () => {
            let qty = parseInt(quantityInput.value);
            if (qty > 1) quantityInput.value = qty - 1;
            updateHiddenQuantities();
        });

        // Khởi tạo giá trị khi trang vừa load
        updateHiddenQuantities();
    }

    // Xử lý "Xem thêm / Thu gọn" mô tả
    const fullDescription = document.getElementById("full-description");
    const toggleButton = document.getElementById("toggle-description");

    if (fullDescription && toggleButton) {
        if (fullDescription.scrollHeight <= 200) {
            toggleButton.style.display = "none";
        }

        toggleButton.addEventListener("click", function () {
            if (fullDescription.classList.contains("collapsed")) {
                fullDescription.classList.remove("collapsed");
                fullDescription.classList.add("expanded");
                toggleButton.textContent = "Thu gọn";
            } else {
                fullDescription.classList.remove("expanded");
                fullDescription.classList.add("collapsed");
                toggleButton.textContent = "Xem thêm";
            }
        });
    }
});

