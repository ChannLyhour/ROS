@echo off
REM Quick WiFi Server Setup for ROS POS

echo.
echo ===================================
echo   ROS POS - Local WiFi Hosting
echo ===================================
echo.

REM Get local IP address
for /f "tokens=2 delims=:" %%A in ('ipconfig ^| findstr /R "IPv4 Address.*192"') do (
    set "LOCAL_IP=%%A"
    set "LOCAL_IP=!LOCAL_IP: =!"
)

if not defined LOCAL_IP (
    echo Getting WiFi IP address...
    for /f "tokens=2 delims=:" %%A in ('ipconfig ^| findstr "IPv4"') do (
        set "LOCAL_IP=%%A"
        set "LOCAL_IP=!LOCAL_IP: =!"
        goto found_ip
    )
)

:found_ip
echo [INFO] Local IP Address: %LOCAL_IP%
echo.

REM Check if Composer dependencies are installed
if not exist "vendor" (
    echo [INFO] Installing Composer dependencies...
    call composer install
)

REM Check if .env exists
if not exist ".env" (
    echo [INFO] Setting up .env file...
    copy .env.example .env 2>nul
    if not exist ".env" (
        echo [WARNING] .env file not found. Please create it manually.
    )
)

REM Generate app key if needed
echo [INFO] Generating application key...
php artisan key:generate

REM Run migrations
echo [INFO] Running database migrations...
php artisan migrate --seed

REM Start the server
echo.
echo [SUCCESS] Starting server!
echo.
echo Access your application:
echo   - Local:  http://localhost:8000
echo   - WiFi:   http://%LOCAL_IP%:8000
echo.
echo Press CTRL+C to stop the server
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause
