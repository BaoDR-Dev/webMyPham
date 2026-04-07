@echo off
cd /d C:\laragon\www\WEBBANHANG

REM Commit file bat truoc
git add push_all_branches.bat
git commit -m "add push script"

echo === PUSH CODE LEN NHANH BAO ===
git checkout feature/bao/frontend
git merge develop --no-edit
git push origin feature/bao/frontend
echo [DONE] bao

echo === PUSH CODE LEN NHANH DANG ===
git checkout feature/dang/frontend
git merge develop --no-edit
git push origin feature/dang/frontend
echo [DONE] dang

echo === PUSH CODE LEN NHANH TIEN ===
git checkout feature/tien/backend
git merge develop --no-edit
git push origin feature/tien/backend
echo [DONE] tien

echo === PUSH CODE LEN NHANH VU ===
git checkout feature/vu/database
git merge develop --no-edit
git push origin feature/vu/database
echo [DONE] vu

git checkout develop
echo === DONE ===
pause
