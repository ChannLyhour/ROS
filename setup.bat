@echo off
REM Colors simulation (Windows doesn't support ANSI by default in older versions)
setlocal enabledelayedexpansion

echo.
echo === ROS POS - Docker Setup ===
echo.

REM Check if Docker is installed
docker --version >nul 2>&1
if errorlevel 1 (
    echo WARNING: Docker is not installed or not in PATH.
    echo Please install Docker Desktop first.
    echo Download from: https://www.docker.com/products/docker-desktop
    echo.
    pause
    exit /b 1
)

echo [OK] Docker is installed
echo.

REM Build images
echo Building Docker images...
docker-compose build
if errorlevel 1 goto error

REM Start containers
echo.
echo Starting containers...
docker-compose up -d
if errorlevel 1 goto error

REM Wait for database
echo.
echo Waiting for database to be ready...
timeout /t 10 /nobreak

REM Copy env file
if not exist .env (
    echo Setting up environment file...
    copy .env.docker .env
)

REM Install dependencies
echo.
echo Installing PHP dependencies...
docker-compose exec -T app composer install
if errorlevel 1 goto error

REM Generate app key
echo Generating application key...
docker-compose exec -T app php artisan key:generate
if errorlevel 1 goto error

REM Run migrations
echo Running database migrations...
docker-compose exec -T app php artisan migrate --force
if errorlevel 1 goto error

REM Seed database
echo Seeding database...
docker-compose exec -T app php artisan db:seed --force
if errorlevel 1 goto error

REM Install npm
echo Installing npm dependencies...
docker-compose exec -T app npm install
if errorlevel 1 goto error

REM Build assets
echo Building frontend assets...
docker-compose exec -T app npm run build
if errorlevel 1 goto error

echo.
echo [SUCCESS] Setup complete!
echo.
echo Your application is running at:
echo   http://localhost
echo.
echo Database credentials:
echo   Host: localhost
echo   Port: 3306
echo   User: ros_user
echo   Password: ros_password
echo.
echo Useful commands:
echo   make logs       - View container logs
echo   make bash       - Access app container
echo   make migrate    - Run migrations
echo   make seed       - Seed database
echo   make down       - Stop containers
echo   make help       - View all commands
echo.
pause
exit /b 0

:error
echo.
echo [ERROR] Setup failed. Please check the error message above.
pause
exit /b 1
