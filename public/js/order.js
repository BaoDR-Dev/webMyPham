document.addEventListener('DOMContentLoaded', function () {
    /**
     * Hàm cập nhật số lượng sản phẩm trong giỏ hàng hiển thị trên header
     */
    function updateCartCount() {
        fetch('/webbanhang/Cart/getCartQuantity', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            let cartCountEl = document.getElementById('cart-count');
            if (cartCountEl) {
                cartCountEl.textContent = data.cartQuantity; // Cập nhật hiển thị số lượng
            }
        })
        .catch(error => {
            console.error('Lỗi khi cập nhật số lượng giỏ hàng:', error);
        });
    }

    // Gọi hàm cập nhật số lượng giỏ hàng khi trang tải
    updateCartCount();
});
