@echo off
REM Hospital System - Deployment Script for Windows
REM Creates deployment package for Hostinger shared hosting

echo ========================================
echo Hospital System - Deployment Package
echo ========================================
echo.

REM Check if required tools exist
where node >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Node.js is not installed or not in PATH
    pause
    exit /b 1
)

where npm >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: npm is not installed or not in PATH
    pause
    exit /b 1
)

REM Clean previous builds
echo [1/6] Cleaning previous builds...
if exist "frontend\dist" rmdir /s /q "frontend\dist"
if exist "hospital-deployment.zip" del /q "hospital-deployment.zip"

REM Install frontend dependencies
echo [2/6] Installing frontend dependencies...
cd frontend
call npm ci
if %errorlevel% neq 0 (
    echo ERROR: Failed to install frontend dependencies
    cd ..
    pause
    exit /b 1
)
cd ..

REM Build frontend for production
echo [3/6] Building frontend for production...
cd frontend
call npm run build
if %errorlevel% neq 0 (
    echo ERROR: Failed to build frontend
    cd ..
    pause
    exit /b 1
)
cd ..

REM Create temporary deployment directory
echo [4/6] Preparing deployment files...
if exist "hospital-deploy-temp" rmdir /s /q "hospital-deploy-temp"
mkdir "hospital-deploy-temp"

REM Copy backend files (excluding vendor, node_modules, .env, tests)
echo [5/6] Copying backend files...
xcopy /E /I /Q "backend" "hospital-deploy-temp\backend" /EXCLUDE:exclude-list.txt 2>nul

REM Manually exclude specific directories and files
if exist "hospital-deploy-temp\backend\vendor" rmdir /s /q "hospital-deploy-temp\backend\vendor"
if exist "hospital-deploy-temp\backend\node_modules" rmdir /s /q "hospital-deploy-temp\backend\node_modules"
if exist "hospital-deploy-temp\backend\.env" del /q "hospital-deploy-temp\backend\.env"
if exist "hospital-deploy-temp\backend\tests" rmdir /s /q "hospital-deploy-temp\backend\tests"

REM Copy frontend dist
echo Copying frontend build...
mkdir "hospital-deploy-temp\frontend"
xcopy /E /I /Q "frontend\dist" "hospital-deploy-temp\frontend\dist"

REM Copy deployment instructions
copy /Y "DEPLOYMENT.md" "hospital-deploy-temp\"

REM Create zip package
echo [6/6] Creating deployment zip...
powershell -Command "Compress-Archive -Path 'hospital-deploy-temp\*' -DestinationPath 'hospital-deployment.zip' -Force"

REM Clean up temporary directory
rmdir /s /q "hospital-deploy-temp"

echo.
echo ========================================
echo Deployment package created successfully!
echo ========================================
echo.
echo File: hospital-deployment.zip
echo.
echo Next steps:
echo 1. Check backend/.env.production and update credentials
echo 2. Upload hospital-deployment.zip to Hostinger
echo 3. Follow DEPLOYMENT.md instructions
echo.
pause
