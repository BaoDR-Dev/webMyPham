@echo off
echo === PUSH CODE LEN NHANH THANH VIEN ===

cd C:\laragon\www\WEBBANHANG

REM ===== BAO - homepage, header, shares =====
git checkout feature/bao/frontend
git add app/views/shares/
git add public/css/home.css public/css/nav.css public/css/style.css public/css/footer.css
git add public/js/slideshow.js public/js/footer.js
git commit -m "bao: homepage, header, navbar, footer, chatbot"
git push origin feature/bao/frontend
echo [DONE] bao pushed

REM ===== DANG - product =====
git checkout feature/dang/frontend
git add app/controllers/ProductController.php
git add app/controllers/CategoryController.php
git add app/models/ProductModel.php
git add app/models/CategoryModel.php
git add app/views/Product/
git add public/css/product.css public/css/show.css public/css/card.css public/css/result-search.css
git commit -m "dang: product list, detail, search, category"
git push origin feature/dang/frontend
echo [DONE] dang pushed

REM ===== TIEN - order, cart =====
git checkout feature/tien/backend
git add app/controllers/CartController.php
git add app/controllers/OrderController.php
git add app/models/CartModel.php
git add app/models/OrderModel.php
git add app/models/OrderDetailsModel.php
git add app/views/Order/
git add app/views/Product/cart.php app/views/Product/checkout.php
git add public/css/order.css public/css/order_detail.css public/css/order_list.css public/css/pay.css
git commit -m "tien: cart, order, checkout, payment"
git push origin feature/tien/backend
echo [DONE] tien pushed

REM ===== VU - account, admin, database =====
git checkout feature/vu/database
git add app/config/
git add app/controllers/AccountController.php
git add app/controllers/AdminController.php
git add app/controllers/ChangePasswordController.php
git add app/models/AccountModel.php
git add app/models/TransactionModel.php
git add app/views/Account/
git add app/views/Admin/
git add public/css/login.css public/css/profile.css public/css/changepasswor.css
git commit -m "vu: database config, account, admin panel"
git push origin feature/vu/database
echo [DONE] vu pushed

REM ===== DEVELOP - phan cua nhom truong =====
git checkout develop
git add app/controllers/PromotionController.php
git add app/controllers/RatingController.php
git add app/controllers/AccountPromotionController.php
git add app/controllers/ApiController.php
git add app/controllers/DefaultController.php
git add app/controllers/Cash.php
git add app/controllers/Momo.php
git add app/controllers/momoQR.php
git add app/controllers/VnPay_NCB.php
git add app/models/PromotionModel.php
git add app/models/PromotionTypeModel.php
git add app/models/RatingModel.php
git add app/models/AccountPromotionModel.php
git add app/views/promotion/
git add app/views/rating/
git add app/views/OnlinePay/
git add app/helpers/
git add public/css/rating.css public/css/yes_no.css public/css/contact.css public/css/chatbot.css
git add public/js/
git add index.php .htaccess composer.json
git add push_all_branches.bat
git commit -m "bao: promotion, rating, payment, api, helpers"
git push origin develop
echo [DONE] develop pushed

echo.
echo === TAT CA DA PUSH XONG ===
echo Gio tung thanh vien tu merge nhanh cua minh vao develop tren GitHub
pause
