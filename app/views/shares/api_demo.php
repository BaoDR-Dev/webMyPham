<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Demo — DevTools Testing</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh}
        .wrap{max-width:960px;margin:0 auto;padding:2rem}
        h1{color:#38bdf8;font-size:1.5rem;margin-bottom:.3rem}
        .sub{color:#94a3b8;font-size:.85rem;margin-bottom:1.5rem}
        .tip{background:#1c1917;border-left:3px solid #f59e0b;padding:.7rem 1rem;border-radius:0 6px 6px 0;font-size:.82rem;color:#fcd34d;margin-bottom:1.5rem}
        .section-title{color:#7dd3fc;font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin:1.5rem 0 .5rem;border-bottom:1px solid #1e3a5f;padding-bottom:.3rem}
        .card{background:#1e293b;border:1px solid #334155;border-radius:8px;padding:1.2rem;margin-bottom:1rem}
        .card-head{display:flex;align-items:center;gap:.5rem;margin-bottom:.8rem;font-size:.9rem;color:#e2e8f0}
        .badge{font-size:.68rem;padding:2px 7px;border-radius:4px;font-weight:700;flex-shrink:0}
        .get{background:#166534;color:#86efac}.post{background:#1e3a5f;color:#93c5fd}.del{background:#7f1d1d;color:#fca5a5}
        .url{font-size:.75rem;color:#64748b;font-family:monospace;margin-bottom:.6rem}
        .row{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.6rem;align-items:center}
        input,select{background:#0f172a;border:1px solid #475569;color:#e2e8f0;padding:5px 9px;border-radius:5px;font-size:.82rem;width:130px}
        input[type=text]{width:160px}
        button{background:#0284c7;color:#fff;border:none;padding:6px 14px;border-radius:5px;cursor:pointer;font-size:.82rem}
        button:hover{background:#0369a1}
        button.danger{background:#b91c1c}button.danger:hover{background:#991b1b}
        .status{display:inline-block;font-size:.72rem;padding:2px 7px;border-radius:4px;margin-bottom:.4rem}
        .ok{background:#14532d;color:#86efac}.err{background:#7f1d1d;color:#fca5a5}
        .res{background:#0f172a;border:1px solid #1e3a5f;border-radius:5px;padding:.8rem;font-family:monospace;font-size:.75rem;max-height:220px;overflow-y:auto;white-space:pre-wrap;color:#a5f3fc}
    </style>
</head>
<body>
<div class="wrap">
    <h1>🔧 API Demo — Chrome DevTools</h1>
    <p class="sub">Mở F12 → tab <strong>Network</strong> → lọc <strong>Fetch/XHR</strong> → bấm các nút để thấy request/response JSON</p>
    <div class="tip">💡 Mỗi nút gọi một API endpoint riêng. Xem Headers, Payload, Preview, Response trong DevTools để demo Bài 2.</div>

    <!-- ===== ACCOUNT ===== -->
    <div class="section-title">Account</div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Đăng nhập</div>
        <div class="url"><?= BASE_URL ?>/Api/login</div>
        <div class="row">
            <input type="text" id="login-email" placeholder="email" value="test@example.com">
            <input type="password" id="login-pass" placeholder="password" value="123456">
            <button onclick="call('login','POST',{email:v('login-email'),password:v('login-pass')})">Đăng nhập</button>
        </div>
        <div id="s-login"></div><div class="res" id="r-login">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Đăng xuất</div>
        <div class="url"><?= BASE_URL ?>/Api/logout</div>
        <div class="row"><button onclick="call('logout','POST',{})">Đăng xuất</button></div>
        <div id="s-logout"></div><div class="res" id="r-logout">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Xem profile</div>
        <div class="url"><?= BASE_URL ?>/Api/profile</div>
        <div class="row"><button onclick="call('profile','GET')">Gọi API</button></div>
        <div id="s-profile"></div><div class="res" id="r-profile">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Đổi mật khẩu</div>
        <div class="url"><?= BASE_URL ?>/Api/changePassword</div>
        <div class="row">
            <input type="password" id="cp-cur" placeholder="mật khẩu cũ">
            <input type="password" id="cp-new" placeholder="mật khẩu mới">
            <input type="password" id="cp-con" placeholder="xác nhận">
            <button onclick="call('changePassword','POST',{currentPassword:v('cp-cur'),newPassword:v('cp-new'),confirmPassword:v('cp-con')})">Đổi</button>
        </div>
        <div id="s-changePassword"></div><div class="res" id="r-changePassword">// chờ...</div>
    </div>

    <!-- ===== PRODUCT ===== -->
    <div class="section-title">Product</div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Danh sách sản phẩm</div>
        <div class="url" id="u-products"><?= BASE_URL ?>/Api/products?page=1&limit=6</div>
        <div class="row">
            <input type="number" id="p-page" value="1" placeholder="page" min="1">
            <input type="number" id="p-limit" value="6" placeholder="limit" min="1">
            <button onclick="getProducts()">Gọi API</button>
        </div>
        <div id="s-products"></div><div class="res" id="r-products">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Chi tiết sản phẩm</div>
        <div class="url" id="u-product"><?= BASE_URL ?>/Api/product/{id}</div>
        <div class="row">
            <input type="number" id="p-id" value="1" placeholder="product_id">
            <button onclick="getProduct()">Gọi API</button>
        </div>
        <div id="s-product"></div><div class="res" id="r-product">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Tìm kiếm sản phẩm</div>
        <div class="url" id="u-search"><?= BASE_URL ?>/Api/search?keyword=...</div>
        <div class="row">
            <input type="text" id="s-kw" value="áo" placeholder="keyword">
            <input type="number" id="s-min" placeholder="min_price">
            <input type="number" id="s-max" placeholder="max_price">
            <button onclick="searchProducts()">Tìm</button>
        </div>
        <div id="s-search"></div><div class="res" id="r-search">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Gợi ý tìm kiếm (autocomplete)</div>
        <div class="url" id="u-autocomplete"><?= BASE_URL ?>/Api/autocomplete?keyword=...</div>
        <div class="row">
            <input type="text" id="ac-kw" value="áo" placeholder="keyword">
            <button onclick="autocomplete()">Gọi API</button>
        </div>
        <div id="s-autocomplete"></div><div class="res" id="r-autocomplete">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Danh mục</div>
        <div class="url"><?= BASE_URL ?>/Api/categories</div>
        <div class="row"><button onclick="call('categories','GET')">Gọi API</button></div>
        <div id="s-categories"></div><div class="res" id="r-categories">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Sản phẩm theo danh mục</div>
        <div class="url" id="u-categoryProducts"><?= BASE_URL ?>/Api/categoryProducts/{id}</div>
        <div class="row">
            <input type="number" id="cat-id" value="1" placeholder="category_id">
            <button onclick="getCategoryProducts()">Gọi API</button>
        </div>
        <div id="s-categoryProducts"></div><div class="res" id="r-categoryProducts">// chờ...</div>
    </div>

    <!-- ===== CART ===== -->
    <div class="section-title">Cart (cần đăng nhập)</div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Xem giỏ hàng</div>
        <div class="url"><?= BASE_URL ?>/Api/cart</div>
        <div class="row"><button onclick="call('cart','GET')">Gọi API</button></div>
        <div id="s-cart"></div><div class="res" id="r-cart">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Thêm vào giỏ</div>
        <div class="url"><?= BASE_URL ?>/Api/cartAdd — Body: { product_id, quantity }</div>
        <div class="row">
            <input type="number" id="ca-pid" value="1" placeholder="product_id">
            <input type="number" id="ca-qty" value="1" placeholder="quantity">
            <button onclick="call('cartAdd','POST',{product_id:+v('ca-pid'),quantity:+v('ca-qty')})">Thêm</button>
        </div>
        <div id="s-cartAdd"></div><div class="res" id="r-cartAdd">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Cập nhật số lượng</div>
        <div class="url"><?= BASE_URL ?>/Api/cartUpdate — Body: { product_id, quantity }</div>
        <div class="row">
            <input type="number" id="cu-pid" value="1" placeholder="product_id">
            <input type="number" id="cu-qty" value="2" placeholder="quantity">
            <button onclick="call('cartUpdate','POST',{product_id:+v('cu-pid'),quantity:+v('cu-qty')})">Cập nhật</button>
        </div>
        <div id="s-cartUpdate"></div><div class="res" id="r-cartUpdate">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Toggle chọn sản phẩm</div>
        <div class="url"><?= BASE_URL ?>/Api/cartToggle/{product_id}</div>
        <div class="row">
            <input type="number" id="ct-pid" value="1" placeholder="product_id">
            <button onclick="callParam('cartToggle',v('ct-pid'),'POST')">Toggle</button>
        </div>
        <div id="s-cartToggle"></div><div class="res" id="r-cartToggle">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Xóa khỏi giỏ</div>
        <div class="url"><?= BASE_URL ?>/Api/cartRemove/{product_id}</div>
        <div class="row">
            <input type="number" id="cr-pid" value="1" placeholder="product_id">
            <button class="danger" onclick="callParam('cartRemove',v('cr-pid'),'POST')">Xóa</button>
        </div>
        <div id="s-cartRemove"></div><div class="res" id="r-cartRemove">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Xóa toàn bộ giỏ</div>
        <div class="url"><?= BASE_URL ?>/Api/cartClear</div>
        <div class="row"><button class="danger" onclick="call('cartClear','POST',{})">Xóa tất cả</button></div>
        <div id="s-cartClear"></div><div class="res" id="r-cartClear">// chờ...</div>
    </div>

    <!-- ===== ORDER ===== -->
    <div class="section-title">Order (cần đăng nhập)</div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Danh sách đơn hàng</div>
        <div class="url"><?= BASE_URL ?>/Api/orders</div>
        <div class="row"><button onclick="call('orders','GET')">Gọi API</button></div>
        <div id="s-orders"></div><div class="res" id="r-orders">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Chi tiết đơn hàng</div>
        <div class="url" id="u-order"><?= BASE_URL ?>/Api/order/{id}</div>
        <div class="row">
            <input type="number" id="o-id" value="1" placeholder="order_id">
            <button onclick="getOrder()">Gọi API</button>
        </div>
        <div id="s-order"></div><div class="res" id="r-order">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Hủy đơn hàng</div>
        <div class="url"><?= BASE_URL ?>/Api/orderCancel/{id}</div>
        <div class="row">
            <input type="number" id="oc-id" value="1" placeholder="order_id">
            <button class="danger" onclick="callParam('orderCancel',v('oc-id'),'POST')">Hủy đơn</button>
        </div>
        <div id="s-orderCancel"></div><div class="res" id="r-orderCancel">// chờ...</div>
    </div>

    <!-- ===== RATING ===== -->
    <div class="section-title">Rating (cần đăng nhập)</div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Tạo đánh giá</div>
        <div class="url"><?= BASE_URL ?>/Api/ratingStore — Body: { product_id, order_id, rating, review_text }</div>
        <div class="row">
            <input type="number" id="rs-pid" value="1" placeholder="product_id">
            <input type="number" id="rs-oid" value="1" placeholder="order_id">
            <input type="number" id="rs-rat" value="5" placeholder="rating 1-5" min="1" max="5">
            <input type="text" id="rs-txt" value="Sản phẩm tốt!" placeholder="review_text">
            <button onclick="call('ratingStore','POST',{product_id:+v('rs-pid'),order_id:+v('rs-oid'),rating:+v('rs-rat'),review_text:v('rs-txt')})">Gửi</button>
        </div>
        <div id="s-ratingStore"></div><div class="res" id="r-ratingStore">// chờ...</div>
    </div>

    <!-- ===== PROMOTION ===== -->
    <div class="section-title">Promotion</div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Danh sách khuyến mãi</div>
        <div class="url"><?= BASE_URL ?>/Api/promotions</div>
        <div class="row"><button onclick="call('promotions','GET')">Gọi API</button></div>
        <div id="s-promotions"></div><div class="res" id="r-promotions">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Khuyến mãi của tôi</div>
        <div class="url"><?= BASE_URL ?>/Api/myPromotions</div>
        <div class="row"><button onclick="call('myPromotions','GET')">Gọi API</button></div>
        <div id="s-myPromotions"></div><div class="res" id="r-myPromotions">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Nhận khuyến mãi</div>
        <div class="url"><?= BASE_URL ?>/Api/promotionReceive/{id}</div>
        <div class="row">
            <input type="number" id="pr-id" value="1" placeholder="promotion_id">
            <button onclick="callParam('promotionReceive',v('pr-id'),'POST')">Nhận</button>
        </div>
        <div id="s-promotionReceive"></div><div class="res" id="r-promotionReceive">// chờ...</div>
    </div>

    <!-- ===== ADMIN ===== -->
    <div class="section-title">Admin (cần quyền admin)</div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Danh sách user</div>
        <div class="url"><?= BASE_URL ?>/Api/adminUsers</div>
        <div class="row"><button onclick="call('adminUsers','GET')">Gọi API</button></div>
        <div id="s-adminUsers"></div><div class="res" id="r-adminUsers">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Bật/tắt user</div>
        <div class="url"><?= BASE_URL ?>/Api/adminUserStatus — Body: { user_id, status }</div>
        <div class="row">
            <input type="number" id="aus-uid" value="2" placeholder="user_id">
            <select id="aus-status"><option value="1">Kích hoạt</option><option value="0">Vô hiệu</option></select>
            <button onclick="call('adminUserStatus','POST',{user_id:+v('aus-uid'),status:v('aus-status')})">Cập nhật</button>
        </div>
        <div id="s-adminUserStatus"></div><div class="res" id="r-adminUserStatus">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Tất cả đơn hàng (admin)</div>
        <div class="url"><?= BASE_URL ?>/Api/adminOrders</div>
        <div class="row"><button onclick="call('adminOrders','GET')">Gọi API</button></div>
        <div id="s-adminOrders"></div><div class="res" id="r-adminOrders">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge post">POST</span> Cập nhật trạng thái đơn (admin)</div>
        <div class="url"><?= BASE_URL ?>/Api/adminOrderStatus — Body: { order_id, status }</div>
        <div class="row">
            <input type="number" id="aos-oid" value="1" placeholder="order_id">
            <select id="aos-status">
                <option value="pending">pending</option>
                <option value="processing">processing</option>
                <option value="completed">completed</option>
                <option value="cancelled">cancelled</option>
            </select>
            <button onclick="call('adminOrderStatus','POST',{order_id:+v('aos-oid'),status:v('aos-status')})">Cập nhật</button>
        </div>
        <div id="s-adminOrderStatus"></div><div class="res" id="r-adminOrderStatus">// chờ...</div>
    </div>

    <div class="card">
        <div class="card-head"><span class="badge get">GET</span> Dashboard thống kê</div>
        <div class="url" id="u-adminDashboard"><?= BASE_URL ?>/Api/adminDashboard</div>
        <div class="row">
            <input type="date" id="db-start">
            <input type="date" id="db-end">
            <button onclick="getDashboard()">Gọi API</button>
        </div>
        <div id="s-adminDashboard"></div><div class="res" id="r-adminDashboard">// chờ...</div>
    </div>
</div>

<script>
const BASE = '<?= BASE_URL ?>/Api';
const v = id => document.getElementById(id).value;

function render(key, status, data) {
    const ok = status >= 200 && status < 300;
    document.getElementById('s-' + key).innerHTML =
        `<span class="status ${ok ? 'ok' : 'err'}">HTTP ${status} ${ok ? '✓' : '✗'}</span>`;
    document.getElementById('r-' + key).textContent = JSON.stringify(data, null, 2);
}

async function call(endpoint, method = 'GET', body = null) {
    const opts = { method, headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } };
    if (body && method !== 'GET') opts.body = JSON.stringify(body);
    const res  = await fetch(`${BASE}/${endpoint}`, opts);
    const data = await res.json();
    render(endpoint, res.status, data);
}

async function callParam(endpoint, param, method = 'GET') {
    const res  = await fetch(`${BASE}/${endpoint}/${param}`, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    render(endpoint, res.status, data);
}

async function getProducts() {
    const url = `${BASE}/products?page=${v('p-page')}&limit=${v('p-limit')}`;
    document.getElementById('u-products').textContent = url.replace(BASE, '<?= BASE_URL ?>/Api');
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    render('products', res.status, await res.json());
}

async function getProduct() {
    const id = v('p-id');
    document.getElementById('u-product').textContent = `<?= BASE_URL ?>/Api/product/${id}`;
    const res = await fetch(`${BASE}/product/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    render('product', res.status, await res.json());
}

async function searchProducts() {
    let url = `${BASE}/search?keyword=${encodeURIComponent(v('s-kw'))}`;
    if (v('s-min')) url += `&min_price=${v('s-min')}`;
    if (v('s-max')) url += `&max_price=${v('s-max')}`;
    document.getElementById('u-search').textContent = url.replace(BASE, '<?= BASE_URL ?>/Api');
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    render('search', res.status, await res.json());
}

async function autocomplete() {
    const url = `${BASE}/autocomplete?keyword=${encodeURIComponent(v('ac-kw'))}`;
    document.getElementById('u-autocomplete').textContent = url.replace(BASE, '<?= BASE_URL ?>/Api');
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    render('autocomplete', res.status, await res.json());
}

async function getCategoryProducts() {
    const id = v('cat-id');
    document.getElementById('u-categoryProducts').textContent = `<?= BASE_URL ?>/Api/categoryProducts/${id}`;
    const res = await fetch(`${BASE}/categoryProducts/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    render('categoryProducts', res.status, await res.json());
}

async function getOrder() {
    const id = v('o-id');
    document.getElementById('u-order').textContent = `<?= BASE_URL ?>/Api/order/${id}`;
    const res = await fetch(`${BASE}/order/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    render('order', res.status, await res.json());
}

async function getDashboard() {
    let url = `${BASE}/adminDashboard`;
    if (v('db-start')) url += `?start_date=${v('db-start')}`;
    if (v('db-end'))   url += `${v('db-start') ? '&' : '?'}end_date=${v('db-end')}`;
    document.getElementById('u-adminDashboard').textContent = url.replace(BASE, '<?= BASE_URL ?>/Api');
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    render('adminDashboard', res.status, await res.json());
}
</script>
</body>
</html>
