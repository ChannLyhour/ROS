# Local Hosting with WiFi - Quick Setup Guide

## Option 1: Using PHP Built-in Server (Easiest)

### Step 1: Get Your Computer's Local IP Address

**Windows (PowerShell):**
```powershell
ipconfig
```
Look for "IPv4 Address" under your WiFi connection (usually `192.168.x.x`)

**Or simpler:**
```powershell
(Get-NetIPAddress -AddressFamily IPv4 | Where-Object {$_.InterfaceAlias -like "*WiFi*"}).IPAddress
```

### Step 2: Start the Application

Navigate to your project folder and run:

```bash
# Install dependencies (if not done)
composer install

# Generate app key (if not done)
php artisan key:generate

# Run migrations and seed
php artisan migrate --seed

# Start the server on all interfaces
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 3: Access from Other Devices

On any device connected to the same WiFi:
- Open browser
- Go to: `http://YOUR_LOCAL_IP:8000`
- Example: `http://192.168.1.100:8000`

---

## Option 2: Using Existing Local Server (XAMPP/WAMP)

If you already have XAMPP or WAMP running:

### Step 1: Place Project in Web Root
- **XAMPP**: `C:\xampp\htdocs\ros`
- **WAMP**: `C:\wamp\www\ros`

### Step 2: Get Your Local IP
```powershell
ipconfig
```

### Step 3: Configure .env
Edit `.env` file and make sure database settings are correct:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ros_db
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Run Migrations
```bash
php artisan migrate --seed
```

### Step 5: Access from Network
- Go to: `http://YOUR_LOCAL_IP/ros/public`
- Example: `http://192.168.1.100/ros/public`

---

## Option 3: Using Node Development Server

If you're using Vite for frontend assets:

```bash
# Terminal 1: Start PHP
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2: Start Vite
npm run dev -- --host 0.0.0.0
```

Access at: `http://YOUR_LOCAL_IP:8000`

---

## Finding Your Local IP Address - All Methods

### Method 1: Command Prompt
```cmd
ipconfig
```
Look for IPv4 Address (e.g., 192.168.1.100)

### Method 2: PowerShell
```powershell
(Get-NetIPAddress -AddressFamily IPv4).IPAddress | Where-Object {$_ -notlike "127*"}
```

### Method 3: Network Settings
1. Settings → Network & Internet → Wi-Fi → Advanced
2. Look for "IPv4 address"

### Method 4: Quick Check
```cmd
hostname -I
```

---

## Firewall Configuration

### Windows Firewall - Allow PHP/Apache

**For PHP Built-in Server:**
```
Control Panel → Windows Defender Firewall → Allow an app through firewall
→ Click "Allow another app"
→ Browse to: C:\xampp\php\php.exe (or your PHP location)
→ Add it to allowed apps
```

**Or allow port 8000:**
```powershell
# Run as Administrator
New-NetFirewallRule -DisplayName "Laravel Port 8000" -Direction Inbound -Action Allow -Protocol TCP -LocalPort 8000
```

**For XAMPP Apache:**
```powershell
# Run as Administrator
New-NetFirewallRule -DisplayName "Apache" -Direction Inbound -Action Allow -Program "C:\xampp\apache\bin\apache.exe"
```

---

## Accessing from Other Devices

### From Same WiFi Network
1. Find host computer's IP: `192.168.1.100` (example)
2. On other device, go to: `http://192.168.1.100:8000`

### Test Connectivity
```bash
# From another device, ping your computer
ping 192.168.1.100

# Or try accessing
curl http://192.168.1.100:8000
```

---

## Database Access from Network

### Option 1: PhpMyAdmin (if XAMPP/WAMP)
- URL: `http://YOUR_LOCAL_IP/phpmyadmin`
- Username: `root`
- Password: (leave blank or your password)

### Option 2: MySQL Remote Access
Edit `my.cnf` or `my.ini`:
```ini
[mysqld]
bind-address = 0.0.0.0
```

Then connect from another device:
```bash
mysql -h 192.168.1.100 -u ros_user -p ros_database
```

---

## Quick Commands

```bash
# Start everything
php artisan serve --host=0.0.0.0 --port=8000

# Check if port is in use
netstat -ano | findstr :8000

# Kill process on port 8000
taskkill /PID <PID> /F

# Test server is running
curl http://localhost:8000
```

---

## Troubleshooting

### Cannot Access from Other Device
1. ✓ Both devices on same WiFi
2. ✓ Check Windows Firewall (allow PHP/Apache)
3. ✓ Check router isn't blocking connections
4. ✓ Ping the IP address: `ping 192.168.1.100`
5. ✓ Use `0.0.0.0` when starting server

### Database Connection Error
```bash
# Check MySQL is running
# Make sure DB_HOST in .env is correct (127.0.0.1 or localhost)

# Or allow remote MySQL
# Edit my.ini: bind-address = 0.0.0.0
```

### Port Already in Use
```powershell
# Find what's using port 8000
netstat -ano | findstr :8000

# Kill it
taskkill /PID <PID_NUMBER> /F
```

---

## Security Notes ⚠️

⚠️ **For Development Only** - Not for production!

- Disable debugging in production: `APP_DEBUG=false`
- Use strong database passwords
- WiFi should be private/password-protected
- Don't expose sensitive data
- Consider using VPN for external access

---

## Demo Accounts (from Seeder)

```
Email: admin@ros.com
Password: password

Email: admin2@ros.com
Password: password
```

---

## Default Ports

- **Laravel**: 8000 (configurable)
- **MySQL**: 3306
- **XAMPP Apache**: 80
- **XAMPP MySQL**: 3306
- **Node/Vite**: 5173

---

## Next Steps

1. ✅ Get your local IP address
2. ✅ Start the Laravel server
3. ✅ Configure firewall (if needed)
4. ✅ Test from another device on WiFi
5. ✅ Access your app!
