document.addEventListener('DOMContentLoaded', function () {
    const BASE_URL = window.BASE_URL || '';

    function updateCartCount() {
        fetch(BASE_URL + '/Cart/getCartQuantity', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            let cartCountEl = document.getElementById('cart-count');
            if (cartCountEl) {
                cartCountEl.textContent = data.cartQuantity;
            }
        })
        .catch(error => {
            console.error('Lỗi khi cập nhật số lượng giỏ hàng:', error);
        });
    }

    updateCartCount();
});
