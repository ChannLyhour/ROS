# Hosting & Deployment Guide

## Quick Start with Docker (Local Hosting)

### Prerequisites
- **Docker Desktop** - [Download](https://www.docker.com/products/docker-desktop)
- **Docker Compose** - Included with Docker Desktop

### Step 1: Start Docker Containers

```bash
# Build and start all services
docker-compose up -d

# Or using Makefile (recommended)
make build
make up
```

This will start:
- **PHP-FPM** (Laravel app) on internal port 9000
- **Nginx** (Web server) on http://localhost:80
- **MySQL** (Database) on localhost:3306
- **Redis** (Cache) on localhost:6379

### Step 2: Setup Laravel Application

```bash
# Copy environment file
copy .env.docker .env

# Or using Makefile
make bash
```

Inside the container:
```bash
# Generate app key
php artisan key:generate

# Install dependencies
composer install

# Run migrations
php artisan migrate --seed
```

Or use quick commands:
```bash
make migrate
make seed
```

### Step 3: Access Your Application

- **Web Application**: http://localhost
- **Database Admin** (Optional): Use a GUI like MySQL Workbench
  - Host: localhost
  - Port: 3306
  - User: ros_user
  - Password: ros_password
  - Database: ros_db

---

## Common Docker Commands

### Using Makefile (Recommended)

```bash
# View all available commands
make help

# Build images
make build

# Start containers
make up

# Stop containers
make down

# Restart containers
make restart

# View logs
make logs

# Run migrations
make migrate

# Seed database
make seed

# Fresh migration + seed
make fresh

# Access Laravel Tinker
make tinker

# Access container bash
make bash

# Access MySQL CLI
make mysql

# Install npm dependencies
make npm-install

# Run npm dev server
make npm-dev

# Build npm assets
make npm-build

# Run tests
make test
```

### Manual Docker Commands

```bash
# View running containers
docker-compose ps

# View container logs
docker-compose logs -f app

# Execute command in container
docker-compose exec app php artisan <command>

# Stop all containers
docker-compose down

# Remove all containers, volumes, networks
docker-compose down -v

# Rebuild specific service
docker-compose build --no-cache app
```

---

## Database Setup

### Initial Setup
```bash
make fresh
```

This will run migrations with seeders automatically.

### Manual Migration
```bash
make migrate
```

### Seed Database
```bash
make seed
```

### Access Database
```bash
# Via MySQL CLI
make mysql

# Via GUI (MySQL Workbench)
# Host: 127.0.0.1
# Port: 3306
# User: ros_user
# Password: ros_password
```

---

## Frontend Development

### Watch for Changes
```bash
make npm-dev
```

### Build for Production
```bash
make npm-build
```

### Install New Packages
```bash
docker-compose exec app npm install package-name
```

---

## Production Deployment

### Option 1: Cloud Platforms

#### DigitalOcean App Platform
1. Create a DigitalOcean account
2. Create a new App
3. Connect your GitHub repository
4. Select Docker as build type
5. Set environment variables
6. Deploy

#### AWS (EC2 + RDS)
1. Launch EC2 instance (Ubuntu 22.04)
2. Install Docker and Docker Compose
3. Clone your repository
4. Configure environment variables
5. Run: `docker-compose -f docker-compose.prod.yml up -d`
6. Set up RDS MySQL database
7. Run migrations: `docker-compose exec app php artisan migrate`

#### Heroku (Alternative)
1. Install Heroku CLI
2. Create Procfile:
```
web: vendor/bin/heroku-php-apache2 public/
worker: php artisan queue:work
```
3. Deploy: `git push heroku main`

### Option 2: VPS (DigitalOcean, Linode, Vultr)

```bash
# On VPS, SSH in and run:
ssh root@your_server_ip

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Clone repository
git clone your-repo-url
cd your-repo

# Setup environment
cp .env.example .env
# Edit .env with production values

# Start containers
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force
```

### Option 3: Shared Hosting (cPanel)

**Note**: Most shared hosting doesn't support Docker. Use traditional deployment:

1. Upload files via FTP
2. Set up MySQL database
3. Configure `.env` file
4. Run migrations via SSH
5. Set up cron jobs for queue workers

---

## Environment Variables

### Development (.env.docker)
```env
APP_ENV=local
APP_DEBUG=true
DB_HOST=db
CACHE_DRIVER=redis
```

### Production (.env)
```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=your-rds-endpoint.amazonaws.com
CACHE_DRIVER=redis
SESSION_DRIVER=cookie
```

---

## Security Best Practices

1. **Change Default Passwords**
   - MySQL: Update `MYSQL_PASSWORD` in docker-compose.yml
   - Redis: Add password if exposed externally

2. **Use HTTPS**
   - Generate SSL certificate (Let's Encrypt)
   - Update nginx configuration

3. **Environment Variables**
   - Never commit `.env` file
   - Use secrets management (AWS Secrets Manager, etc.)

4. **Database Backups**
   ```bash
   docker-compose exec db mysqldump -u ros_user -pros_password ros_db > backup.sql
   ```

5. **Update Dependencies**
   ```bash
   docker-compose exec app composer update
   docker-compose exec app npm update
   ```

---

## Troubleshooting

### Port Already in Use
```bash
# Find service using port 80
netstat -ano | findstr :80

# Kill process or change port in docker-compose.yml
```

### Database Connection Error
```bash
# Check if db service is running
docker-compose ps

# View db logs
docker-compose logs db

# Restart db
docker-compose restart db
```

### Permission Denied Errors
```bash
# Fix permissions
docker-compose exec app chown -R www-data:www-data /var/www/html
```

### Out of Memory
```bash
# Increase Docker memory limit in Docker Desktop settings
# Then restart: make restart
```

---

## Next Steps

1. ✅ Set up Docker
2. ✅ Configure environment variables
3. ✅ Run migrations and seeds
4. ✅ Test locally
5. Choose hosting platform (DigitalOcean, AWS, etc.)
6. Deploy production environment
7. Set up monitoring and backups

---

## Support Resources

- [Docker Documentation](https://docs.docker.com)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [DigitalOcean Tutorials](https://www.digitalocean.com/community/tutorials)
- [AWS Lambda for Laravel](https://serverless-laravel.com/)
