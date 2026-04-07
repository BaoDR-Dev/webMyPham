PS C:\laragon\www\WEBBANHANG> \push_all_branches.bat
\push_all_branches.bat : The term '\push_all_branches.bat' is not recognized as the 
name of a cmdlet, function, script file, or operable program. Check the spelling of   
the name, or if a path was included, verify that the path is correct and try again.   
At line:1 char:1
+ \push_all_branches.bat
+ ~~~~~~~~~~~~~~~~~~~~~~
    + CategoryInfo          : ObjectNotFound: (\push_all_branches.bat:String) [], Co  
   mmandNotFoundException
    + FullyQualifiedErrorId : CommandNotFoundException


Suggestion [3,General]: The command \push_all_branches.bat was not found, but does exist in the current location. Windows PowerShell does not load commands from the current location by default. If you trust this command, instead type: ".\\push_all_branches.bat". See "get-help about_Command_Precedence" for more details.
PS C:\laragon\www\WEBBANHANG>@echo off
echo === PUSH ALL BRANCHES ===

cd C:\laragon\www\WEBBANHANG

REM ===== DEVELOP =====
git checkout develop
git add .
git commit -m "develop: full project source"
git push origin develop

REM ===== BAO =====
git checkout feature/bao/frontend
git add app/views/shares/ public/css/home.css public/css/nav.css public/css/style.css
git commit -m "bao: homepage, header, navbar, footer UI"
git push origin feature/bao/frontend

REM ===== DANG =====
git checkout feature/dang/frontend
git add app/controllers/ProductController.php app/models/ProductModel.php app/views/Product/ public/css/product.css public/css/show.css public/css/card.css
git commit -m "dang: product pages, search, category UI"
git push origin feature/dang/frontend

REM ===== TIEN =====
git checkout feature/tien/backend
git add app/controllers/OrderController.php app/controllers/CartController.php app/models/OrderModel.php app/models/CartModel.php app/views/Order/ app/views/Product/cart.php app/views/Product/checkout.php public/css/order.css public/css/pay.css
git commit -m "tien: order and cart backend"
git push origin feature/tien/backend

REM ===== VU =====
git checkout feature/vu/database
git add app/config/database.php app/controllers/AccountController.php app/models/AccountModel.php app/views/Account/ app/controllers/AdminController.php app/views/Admin/
git commit -m "vu: database, account, admin"
git push origin feature/vu/database

REM ===== MERGE VE DEVELOP =====
git checkout develop
git merge feature/bao/frontend -m "merge: bao frontend"
git merge feature/dang/frontend -m "merge: dang frontend"
git merge feature/tien/backend -m "merge: tien backend"
git merge feature/vu/database -m "merge: vu database"
git push origin develop

REM ===== MERGE VE MAIN =====
git checkout main
git merge develop -m "merge: develop to main"
git push origin main

echo === DONE ===
pause
