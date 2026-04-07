@echo off
cd /d C:\laragon\www\WEBBANHANG

echo === BAO ===
git checkout feature/bao/frontend
git add app\views\shares\
git add public\css\home.css public\css\nav.css public\css\style.css public\css\footer.css public\css\chatbot.css public\css\contact.css
git add public\js\slideshow.js public\js\footer.js public\js\show.js public\js\chatbot.js
git commit -m "bao: homepage, header, navbar, footer"
git push origin feature/bao/frontend

echo === DANG ===
git checkout feature/dang/frontend
git add app\controllers\ProductController.php app\controllers\CategoryController.php
git add app\models\ProductModel.php app\models\CategoryModel.php
git add app\views\Product\
git add public\css\product.css public\css\show.css public\css\card.css public\css\result-search.css
git commit -m "dang: product, category, search"
git push origin feature/dang/frontend

echo === TIEN ===
git checkout feature/tien/backend
git add app\controllers\CartController.php app\controllers\OrderController.php
git add app\models\CartModel.php app\models\OrderModel.php app\models\OrderDetailsModel.php
git add app\views\Order\
git add app\views\Product\cart.php app\views\Product\checkout.php
git add public\css\order.css public\css\order_detail.css public\css\order_list.css public\css\pay.css
git commit -m "tien: cart, order, checkout"
git push origin feature/tien/backend

echo === VU ===
git checkout feature/vu/database
git add app\config\
git add app\controllers\AccountController.php app\controllers\AdminController.php
git add app\models\AccountModel.php app\models\TransactionModel.php
git add app\views\Account\ app\views\Admin\
git add public\css\login.css public\css\profile.css public\css\changepasswor.css public\css\productAdmin.css
git commit -m "vu: database, account, admin"
git push origin feature/vu/database

echo === DEVELOP (BAO - nhom truong) ===
git checkout develop
git add .
git commit -m "bao: promotion, rating, payment, api, helpers"
git push origin develop

echo === DONE ===
pause
