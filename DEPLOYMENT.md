# Hospital System - Hostinger Deployment Guide

## Prerequisites

- Hostinger Shared Hosting (Premium or Business plan recommended)
- PHP 8.3 or higher
- MySQL 8.0+
- Node.js 18+ (for local build)
- Composer (for local build)

---

## Step 1: Pre-Deployment Setup (Local Machine)

### 1.1 Generate Application Key

```bash
cd backend
php artisan key:generate --show
```

Copy the generated key. You'll need it for `.env.production`.

### 1.2 Generate JWT Secret

```bash
php artisan jwt:secret --show
```

Copy the generated JWT secret.

### 1.3 Update .env.production

Edit `backend/.env.production` and replace:
- `REPLACE_WITH_GENERATED_KEY` with your APP_KEY
- `REPLACE_WITH_GENERATED_JWT_SECRET` with your JWT_SECRET
- `your-domain.com` with your actual domain
- `your_database_name` with your Hostinger MySQL database name
- `your_database_username` with your Hostinger MySQL username
- `your_database_password` with your Hostinger MySQL password

---

## Step 2: Build Deployment Package

### Windows
```bash
deploy.bat
```

### Linux/Mac
```bash
chmod +x deploy.sh
./deploy.sh
```

This creates `hospital-deployment.zip` in the project root.

---

## Step 3: Upload to Hostinger

### 3.1 Access Hostinger File Manager

1. Log in to Hostinger hPanel
2. Go to **Files** → **File Manager**
3. Navigate to `public_html`

### 3.2 Upload and Extract

1. Upload `hospital-deployment.zip` to `public_html`
2. Right-click the zip file → **Extract**
3. Delete the zip file after extraction

### 3.3 Move Files to Correct Locations

**Via Hostinger Terminal (if available) or File Manager:**

```bash
# Move backend files to public_html
mv backend/* public_html/
mv backend/.* public_html/ 2>/dev/null

# Move frontend build to public_html/public
mkdir -p public_html/public
mv frontend/dist/* public_html/public/

# Clean up
rm -rf backend frontend DEPLOYMENT.md deploy.bat deploy.sh
```

**Alternative (File Manager only):**

1. Move ALL contents from `backend/` to `public_html/`
2. Move ALL contents from `frontend/dist/` to `public_html/public/`
3. Delete empty `backend/`, `frontend/`, and deployment files

---

## Step 4: Configure Hostinger

### 4.1 PHP Version

1. Go to **Advanced** → **PHP Configuration**
2. Select **PHP 8.3** (or latest available)

### 4.2 Node.js Version (if available)

1. Go to **Advanced** → **Node.js**
2. Select **Node.js 18** or higher

### 4.3 Create MySQL Database

1. Go to **Databases** → **MySQL Databases**
2. Create new database:
   - Database name: `your_database_name`
   - Username: `your_database_username`
   - Password: `your_database_password`
3. Note down the database credentials

### 4.4 Set Environment to Production

1. Go to **Advanced** → **SSH Access**
2. Enable SSH if available

Or via File Manager:
1. Edit `public_html/.env`
2. Ensure `APP_ENV=production` and `APP_DEBUG=false`

---

## Step 5: Run Database Migrations

### Via SSH (Recommended)

```bash
ssh your_username@your-domain.com
cd public_html
php artisan migrate --force
php artisan db:seed --force  # Optional: seed initial data
```

### Via Hostinger Terminal

If SSH is not available, create a temporary PHP script:

1. Create `public_html/migrate.php`:
```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->call('migrate', ['--force' => true]);
echo "Migration completed with status: $status";
```

2. Visit `https://your-domain.com/migrate.php` in browser
3. DELETE the file immediately after migration

---

## Step 6: Post-Deployment Configuration

### 6.1 Set File Permissions

Via File Manager or SSH:

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

### 6.2 Create Storage Symlink

```bash
php artisan storage:link
```

### 6.3 Configure SSL Certificate

1. Go to **Security** → **SSL/TLS**
2. Enable **Force HTTPS Redirect**

### 6.4 Set Up Domain

1. Go to **Hosting** → **Domain**
2. Ensure domain points to `public_html`

---

## Step 7: Verify Deployment

### 7.1 Check Backend API

Visit: `https://your-domain.com/api/v1/health`

Expected response:
```json
{"status": "healthy"}
```

### 7.2 Check Frontend

Visit: `https://your-domain.com`

The React app should load and connect to the API.

---

## Troubleshooting

### 500 Internal Server Error

1. Check `storage/logs/laravel.log` for errors
2. Ensure `.env` file exists and is properly configured
3. Verify database credentials
4. Check PHP version (must be 8.3+)

### API Not Working

1. Verify `.htaccess` exists in `public_html`
2. Check `APP_URL` in `.env` matches your domain
3. Ensure `SANCTUM_STATEFUL_DOMAINS` includes your domain

### Frontend Not Loading

1. Verify `frontend/dist/` contents are in `public_html/public/`
2. Check `VITE_API_URL` in frontend build matches API endpoint

### Database Connection Error

1. Verify MySQL credentials in `.env`
2. Check database exists in Hostinger MySQL panel
3. Ensure database user has proper privileges

---

## File Structure After Deployment

```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/           <- React build files
│   ├── index.html
│   ├── assets/
│   └── ...
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── .htaccess
├── artisan
└── index.php
```

---

## Rollback Procedure

If deployment fails:

1. Keep backup of previous deployment
2. Restore files from backup
3. Run `php artisan migrate:rollback` if needed
4. Clear cache: `php artisan cache:clear`

---

## Support

For issues specific to Hostinger:
- Hostinger Support: https://www.hostinger.com/support
- Hostinger Knowledge Base: https://www.hostinger.com/tutorials

For application issues:
- Check Laravel logs: `storage/logs/laravel.log`
- Check PHP error logs in Hostinger panel
