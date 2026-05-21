@echo off
setlocal enabledelayedexpansion

:: Change to the directory of this batch file
cd /d "%~dp0"

echo ===================================================
echo   ROS POS System - Automated Database Backup
echo ===================================================

:: 1. Retrieve configuration dynamically from Laravel environment
echo Fetching database credentials and backup settings...

for /f "usebackq delims=" %%i in (`php artisan tinker --execute^="echo env('DB_DATABASE')"`) do set DB_NAME=%%i
for /f "usebackq delims=" %%i in (`php artisan tinker --execute^="echo env('DB_USERNAME')"`) do set DB_USER=%%i
for /f "usebackq delims=" %%i in (`php artisan tinker --execute^="echo env('DB_PASSWORD')"`) do set DB_PASS=%%i
for /f "usebackq delims=" %%i in (`php artisan tinker --execute^="echo App\Helper\SystemHelper::getSetting('backup_disk_path', 'C:\ROS-Backups')"`) do set BACKUP_DIR=%%i

:: Fallbacks if values could not be fetched
if "%DB_NAME%"=="" set DB_NAME=re_o_s
if "%DB_USER%"=="" set DB_USER=root
if "%BACKUP_DIR%"=="" set BACKUP_DIR=C:\ROS-Backups

:: 2. Set up MySQL dump executable path
set MYSQLDUMP_PATH=C:\wamp64\bin\mysql\mysql8.4.7\bin\mysqldump.exe

if not exist "!MYSQLDUMP_PATH!" (
    echo ERROR: mysqldump.exe not found at "!MYSQLDUMP_PATH!"
    echo Please verify your WampServer MySQL path.
    pause
    exit /b 1
)

:: 3. Create backup directory if it doesn't exist
if not exist "!BACKUP_DIR!" (
    echo Creating backup directory "!BACKUP_DIR!"...
    mkdir "!BACKUP_DIR!"
)

:: 4. Generate timestamp (format: ddMMyyHHmm)
for /f %%i in ('powershell -command "Get-Date -format ddMMyyHHmm"') do set dt=%%i

:: 5. Construct backup file path
set BACKUP_FILE=!BACKUP_DIR!\!DB_NAME!_!dt!.sql

:: 6. Perform the dump
echo Backing up database "!DB_NAME!" to "!BACKUP_FILE!"...

:: Handle password if present or blank
if "!DB_PASS!"=="" (
    "!MYSQLDUMP_PATH!" -u !DB_USER! !DB_NAME! > "!BACKUP_FILE!"
) else (
    "!MYSQLDUMP_PATH!" -u !DB_USER! -p!DB_PASS! !DB_NAME! > "!BACKUP_FILE!"
)

:: 7. Check if the dump succeeded
if !ERRORLEVEL! equ 0 (
    echo [SUCCESS] Backup completed successfully!
    echo File: !BACKUP_FILE!
    echo Size:
    for %%F in ("!BACKUP_FILE!") do echo %%~zF bytes
) else (
    echo [ERROR] Backup failed. Please check database connectivity or permissions.
    exit /b 1
)

echo ===================================================
echo NOTE: This creates raw .sql files for quick recovery.
echo       The Laravel web dashboard shows Spatie .zip backups
echo       stored under: !BACKUP_DIR!\ROS\
echo ===================================================
