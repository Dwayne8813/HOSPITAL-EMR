# Installation Guide

## Prerequisites

### Server Requirements

| Requirement | Minimum Version |
|-------------|-----------------|
| PHP | 8.2 or higher |
| MySQL | 8.0 or higher |
| PostgreSQL | 15 or higher |
| SQLite | 3.35.0 or higher |
| Composer | 2.0 or higher |
| Node.js | 18.0 or higher |
| npm | 9.0 or higher |

### PHP Extensions Required

```
bcmath
ctype
curl
dom
fileinfo
filter
gd
json
mbstring
mysqli
openssl
pdo
pdo_mysql
pdo_sqlite
xml
zip
```

### PHP Configuration

```ini
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 64M
post_max_size = 64M
max_file_uploads = 20
```

---

## Installation Methods

### Method 1: Web Installer (Recommended)

The easiest way to install the system is using the web installer.

#### Step 1: Upload Files

Upload all files to your web server's public directory (usually `public_html` or `www`).

#### Step 2: Set Permissions

```bash
# Linux/Unix
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/uploads storage/app/backups storage/logs
chown -R www-data:www-data storage bootstrap/cache

# Windows (via IIS)
# Ensure IIS_IUSRS has modify permissions on:
# - storage/
# - bootstrap/cache/
```

#### Step 3: Run Installer

Navigate to:
```
http://your-domain.com/install
```

Follow the installation wizard:

1. **Welcome Screen** - System requirements check
2. **Database Configuration** - Enter database credentials
3. **Admin Account** - Create administrator account
4. **Hospital Information** - Enter facility details
5. **Installation Complete** - Ready to use

#### Step 4: Delete Installer

After installation, delete the `installer` directory for security:

```bash
rm -rf installer/
```

---

### Method 2: Manual Installation

#### Step 1: Clone Repository

```bash
git clone <repository-url> hospital
cd hospital
```

#### Step 2: Backend Setup

```bash
cd backend

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Edit .env file with your database credentials
nano .env
```

#### Step 3: Database Setup

**Option A: SQLite (Development)**

```bash
# SQLite is configured by default
# Ensure database/database.sqlite exists
touch database/database.sqlite

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force
```

**Option B: MySQL**

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_emr
DB_USERNAME=root
DB_PASSWORD=your_password
```

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE hospital_emr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force
```

**Option C: PostgreSQL**

Edit `.env` file:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hospital_emr
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

```bash
# Create database
createdb hospital_emr

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force
```

#### Step 4: Frontend Setup

```bash
cd ../frontend

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Build for production
npm run build
```

#### Step 5: Configure Web Server

**Apache (.htaccess):**

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On
    RewriteBase /

    # Handle Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    # Handle API
    RewriteRule ^api/(.*)$ backend/public/api.php [L,QSA]

    # Handle static assets
    RewriteRule ^assets/(.*)$ frontend/dist/assets/$1 [L]
</IfModule>
```

**Nginx:**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/hospital;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realdoc_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Step 6: Set Permissions

```bash
# Linux/Unix
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/uploads storage/app/backups storage/logs
chown -R www-data:www-data storage bootstrap/cache
```

#### Step 7: Create Storage Links

```bash
php artisan storage:link
```

#### Step 8: Optimize (Production)

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

## Environment Configuration

### Backend `.env` File

```env
# Application
APP_NAME="Hospital EMR/EHR"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_emr
DB_USERNAME=your_username
DB_PASSWORD=your_password

# JWT Authentication
JWT_SECRET=your-jwt-secret
JWT_TTL=3600
JWT_REFRESH_TTL=10080

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# File Storage
FILESYSTEM_DISK=local
BACKUP_PATH=storage/app/backups

# Frontend URL (for CORS)
FRONTEND_URL=https://your-domain.com

# Installer Status
INSTALLER_COMPLETED=true
```

### Frontend `.env` File

```env
VITE_API_URL=https://your-domain.com/api
VITE_APP_NAME="Hospital EMR"
VITE_APP_VERSION=1.0.0
VITE_PWA_ENABLED=true
```

---

## Database Setup

### Create Database User (MySQL)

```sql
CREATE USER 'hospital_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON hospital_emr.* TO 'hospital_user'@'localhost';
FLUSH PRIVILEGES;
```

### Create Database User (PostgreSQL)

```sql
CREATE USER hospital_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE hospital_emr TO hospital_user;
GRANT ALL ON SCHEMA public TO hospital_user;
```

### Run Migrations

```bash
php artisan migrate --force
```

### Seed Default Data

```bash
# Run all seeders
php artisan db:seed --force

# Or run specific seeders
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=Cie10Seeder
php artisan db:seed --class=MedicationCatalogSeeder
php artisan db:seed --class=ServiceCatalogSeeder
```

---

## Default Data

### Default Roles

| Role | Description |
|------|-------------|
| admin | Full system access |
| doctor | Medical staff access |
| nurse | Nursing staff access |
| billing | Billing and payments |
| pharmacist | Pharmacy management |
| lab_tech | Laboratory operations |
| radiologist | Radiology operations |
| receptionist | Front desk operations |

### Default Admin Account

```json
{
    "email": "admin@hospital.com",
    "password": "password",
    "name": "System Administrator"
}
```

### Default Doctor Accounts

```json
{
    "email": "doctor@hospital.com",
    "password": "password",
    "name": "Dr. John Smith",
    "specialty": "Internal Medicine"
}
```

### Default Settings

```json
{
    "institution": {
        "name": "Hospital EMR System",
        "address": "123 Medical Center Drive",
        "phone": "+1-555-0100",
        "email": "admin@hospital.com"
    },
    "appointments": {
        "slot_duration_minutes": 30,
        "allow_booking_days_ahead": 30,
        "cancellation_hours_before": 24
    },
    "billing": {
        "currency": "USD",
        "tax_rate": 0,
        "invoice_prefix": "INV",
        "payment_prefix": "PAY"
    }
}
```

---

## Post-Installation Steps

### 1. Change Default Passwords

**Important:** Change all default passwords immediately after installation.

```bash
# Via artisan command (if available)
php artisan user:change-password admin@hospital.com new-password
```

Or use the API:
```http
POST /api/v1/auth/change-password
Authorization: Bearer <admin-token>
Content-Type: application/json

{
    "current_password": "password",
    "password": "your-new-secure-password",
    "password_confirmation": "your-new-secure-password"
}
```

### 2. Configure Email Settings

Update `.env` with your SMTP credentials and test:

```bash
php artisan tinker
>>> Mail::raw('Test email', function($message) { $message->to('test@example.com')->subject('Test'); });
```

### 3. Set Up Cron Jobs

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Set Up Queue Worker (Optional)

```bash
# Start queue worker
php artisan queue:work --sleep=3 --tries=3

# Create systemd service for queue worker
sudo nano /etc/systemd/system/hospital-worker.service
```

```ini
[Unit]
Description=Hospital EMR Queue Worker
After=network.target

[Service]
User=www-data
WorkingDirectory=/var/www/hospital/backend
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable hospital-worker
sudo systemctl start hospital-worker
```

### 5. Configure Backup Automation

```bash
# Add to crontab
0 2 * * * cd /var/www/hospital/backend && php artisan backup:run --only-db >> /dev/null 2>&1
```

### 6. Enable SSL Certificate

```bash
# Using Certbot (Let's Encrypt)
sudo certbot --apache -d your-domain.com
```

### 7. Set Up Monitoring

```bash
# Install Laravel Telescope (development)
composer require laravel/telescope --dev

# Install Laravel Horizon (production queue)
composer require laravel/horizon
```

---

## Troubleshooting

### Common Issues

#### 1. "Class not found" Error

```bash
composer dump-autoload
php artisan optimize
```

#### 2. Permission Denied Errors

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 3. Database Connection Error

Verify `.env` database credentials:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

#### 4. JWT Token Error

```bash
php artisan jwt:secret
php artisan config:clear
```

#### 5. CORS Errors

Verify `FRONTEND_URL` in `.env` matches your frontend URL.

#### 6. Memory Limit Error

Increase memory limit in `php.ini`:
```ini
memory_limit = 256M
```

#### 7. Upload Limit Error

Increase upload limits in `php.ini`:
```ini
upload_max_filesize = 64M
post_max_size = 64M
```

#### 8. Session Issues

Clear session data:
```bash
php artisan session:table
php artisan migrate
```

### Log Files

Check application logs:
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Apache logs
tail -f /var/log/apache2/error.log

# Nginx logs
tail -f /var/log/nginx/error.log

# PHP logs
tail -f /var/log/php/errors.log
```

### Debug Mode

**Never enable debug mode in production:**

```env
# Development
APP_DEBUG=true

# Production
APP_DEBUG=false
```

### Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

---

## System Requirements Check

Run this script to verify your server meets all requirements:

```bash
php -r "
echo 'PHP Version: ' . PHP_VERSION . PHP_EOL;
echo 'Required Extensions:' . PHP_EOL;
\$required = ['bcmath', 'ctype', 'curl', 'dom', 'fileinfo', 'filter', 'gd', 'json', 'mbstring', 'mysqli', 'openssl', 'pdo', 'pdo_mysql', 'pdo_sqlite', 'xml', 'zip'];
foreach (\$required as \$ext) {
    echo '  ' . \$ext . ': ' . (extension_loaded(\$ext) ? '✓' : '✗') . PHP_EOL;
}
"
```

---

## Support

If you encounter issues not covered here:

1. Check the [Troubleshooting](#troubleshooting) section
2. Review application logs in `storage/logs/`
3. Consult Laravel documentation: https://laravel.com/docs
4. Contact system administrator
