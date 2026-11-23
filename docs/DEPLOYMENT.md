# Deployment Guide

## Hostinger Shared Hosting Deployment

### Step 1: Prepare Your Files

#### Local Build

```bash
# Build frontend for production
cd frontend
npm run build

# Install backend dependencies
cd ../backend
composer install --optimize-autoloader --no-dev
```

#### Create Deployment Archive

```bash
# From project root
tar -czf hospital-deploy.tar.gz \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    backend/ frontend/dist/
```

### Step 2: Upload via Hostinger File Manager

1. Log in to Hostinger Control Panel
2. Navigate to **Files** → **File Manager**
3. Go to `public_html` directory
4. Upload `hospital-deploy.tar.gz`
5. Extract the archive in `public_html`

### Step 3: Directory Structure on Hostinger

```
public_html/
├── backend/           # Laravel application
├── frontend/          # Built React app
├── .htaccess          # Apache configuration
├── index.php          # Entry point
└── api.php            # API entry point
```

### Step 4: Configure .htaccess

Create `.htaccess` in `public_html` root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Redirect to https
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle API requests
    RewriteCond %{REQUEST_URI} ^/api
    RewriteRule ^api/(.*)$ backend/public/api.php [L,QSA]

    # Handle frontend static assets
    RewriteCond %{REQUEST_URI} ^/assets
    RewriteRule ^assets/(.*)$ frontend/dist/assets/$1 [L]

    # Handle SPA routing
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Prevent access to hidden files
<FilesMatch "^\.">
    Require all denied
</FilesMatch>
```

### Step 5: Backend .htaccess

Create `backend/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /backend/

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Step 6: Create public entry points

**public_html/index.php:**

```php
<?php
// Redirect to frontend
header('Location: frontend/dist/index.html');
exit;
```

**public_html/api.php:**

```php
<?php
// Redirect API calls to backend
require __DIR__ . '/backend/public/index.php';
```

### Step 7: Create Symlinks

In File Manager or via SSH:

```bash
cd public_html/backend
ln -s ../storage/storage storage
```

### Step 8: Database Setup

1. Go to **Databases** → **MySQL Databases** in Hostinger panel
2. Create database: `u1234567_hospital`
3. Create user with full privileges
4. Note the credentials for `.env` configuration

### Step 9: Configure Environment

Edit `backend/.env`:

```env
APP_NAME="Hospital EMR"
APP_ENV=production
APP_KEY=base64:generate-with-php-artisan
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u1234567_hospital
DB_USERNAME=u1234567_username
DB_PASSWORD=your_password

JWT_SECRET=generate-with-php-artisan
JWT_TTL=3600
JWT_REFRESH_TTL=10080

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your-email@your-domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

FILESYSTEM_DISK=local
FRONTEND_URL=https://your-domain.com
INSTALLER_COMPLETED=true
```

### Step 10: Run Migrations

Via Hostinger SSH Terminal or PHP:

```bash
cd backend
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
```

---

## AWS EC2 (Ubuntu): Elastic IP + SSH

An **Elastic IP (EIP)** is a static public IPv4 address that survives instance
stop/start or replacement. Associate it with the Ubuntu instance first, then use
that address for every SSH command below.

### Step 1: Allocate and associate the Elastic IP

AWS Console: **EC2 → Network & Security → Elastic IPs → Allocate Elastic IP address**,
then **Actions → Associate address** and select the Ubuntu instance.

Or with the AWS CLI:

```bash
aws ec2 allocate-address --domain vpc --region us-east-1
aws ec2 associate-address \
  --instance-id i-0123456789abcdef0 \
  --allocation-id eipalloc-0123456789abcdef0 \
  --region us-east-1
```

Copy the `PublicIp` from the output — that is `<ELASTIC_IP>` below. An Elastic IP
is free while associated with a running instance, but is charged when unassociated.

### Step 2: Allow SSH in the security group

Attach a security group to the instance with these inbound rules:

| Type | Protocol | Port | Source |
|------|----------|------|--------|
| SSH | TCP | 22 | `YOUR_PUBLIC_IP/32` |
| HTTP | TCP | 80 | `0.0.0.0/0` (optional) |
| HTTPS | TCP | 443 | `0.0.0.0/0` (optional) |

Restrict port 22 to your own IP. CLI example:

```bash
aws ec2 authorize-security-group-ingress \
  --group-id sg-0123456789abcdef0 \
  --protocol tcp --port 22 --cidr YOUR_PUBLIC_IP/32
```

If you expose the Docker ports directly, also allow `8000` (API) and `3000` (web).
Prefer nginx + TLS in front of them instead.

### Step 3: Enable SSH on Ubuntu

Ubuntu EC2 AMIs normally include OpenSSH. Verify/start it:

```bash
ssh ubuntu@<ELASTIC_IP>   # first connection asks to accept the host key

sudo apt update
sudo apt install -y openssh-server
sudo systemctl enable --now ssh
sudo systemctl status ssh --no-pager
```

If UFW is enabled:

```bash
sudo ufw allow OpenSSH
sudo ufw status
```

For stricter rules, use `sudo ufw allow from YOUR_PUBLIC_IP/32 to any port 22 proto tcp`.

### Step 4: Use key-based authentication

On your local machine (skip if reusing an existing key):

```bash
# macOS / Linux / Git Bash
ssh-keygen -t ed25519 -f ~/.ssh/hospital_emr -C "hospital-emr"
ssh-copy-id -i ~/.ssh/hospital_emr.pub ubuntu@<ELASTIC_IP>
```

PowerShell (Windows OpenSSH):

```powershell
ssh-keygen -t ed25519 -f "$env:USERPROFILE\.ssh\hospital_emr" -C "hospital-emr"
type "$env:USERPROFILE\.ssh\hospital_emr.pub" | ssh ubuntu@<ELASTIC_IP> "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"
```

Connect with the key:

```bash
ssh -i ~/.ssh/hospital_emr ubuntu@<ELASTIC_IP>
```

Optional `~/.ssh/config` alias (works in PowerShell too):

```sshconfig
Host hospital-emr
    HostName <ELASTIC_IP>
    User ubuntu
    IdentityFile ~/.ssh/hospital_emr
    IdentitiesOnly yes
```

Then simply:

```bash
ssh hospital-emr
```

### Step 5: Harden sshd (after key login works)

Use a drop-in file so package upgrades do not overwrite your settings:

```bash
sudo tee /etc/ssh/sshd_config.d/99-hospital-emr.conf >/dev/null <<'EOF'
PasswordAuthentication no
PermitRootLogin no
PubkeyAuthentication yes
EOF

sudo sshd -t && sudo systemctl reload ssh
```

Keep one SSH session open while testing a new one so you do not lock yourself out.

### Step 6: Point the app at the Elastic IP

On the instance, use the stable address in `docker-compose.yml` and `backend/config/cors.php`:

```yaml
# docker-compose.yml
api:
  environment:
    APP_URL: http://<ELASTIC_IP>:8000
    FRONTEND_URL: http://<ELASTIC_IP>:3000
web:
  environment:
    VITE_API_URL: http://<ELASTIC_IP>:8000/api/v1
```

```php
// backend/config/cors.php
'allowed_origins' => [
    'http://<ELASTIC_IP>:3000',
],
'allowed_hosts' => [
    '<ELASTIC_IP>',
],
```

Restart with `docker compose up -d --build`. Verify the IP stays the same after an
instance stop/start — that is the point of the Elastic IP.

---

## FTP Upload Instructions

### Using FileZilla

1. Open FileZilla
2. Enter Hostinger FTP credentials:
   - Host: `your-domain.com` or server IP
   - Username: FTP username
   - Password: FTP password
   - Port: 21 (FTP) or 22 (SFTP)
3. Connect and navigate to `public_html`
4. Upload all project files
5. Ensure permissions are set correctly

### Using cPanel File Manager

1. Log in to Hostinger/cPanel
2. Open File Manager
3. Navigate to `public_html`
4. Use Upload button to upload files
5. Extract archives as needed

### Setting Permissions via FTP

```bash
# Via SSH terminal
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
chmod 644 .htaccess
```

---

## SSL Setup

### Using Hostinger SSL

1. Go to **Security** → **SSL/TLS** in Hostinger panel
2. Select **Free SSL Certificate** (Let's Encrypt)
3. Select your domain
4. Click **Install**
5. Enable **Force HTTPS**

### Manual SSL (Let's Encrypt via SSH)

```bash
# Connect via SSH
ssh username@your-domain.com

# Install Certbot
sudo apt install certbot python3-certbot-apache

# Get certificate
sudo certbot --apache -d your-domain.com -d www.your-domain.com

# Auto-renew
sudo crontab -e
# Add: 0 0 1 * * certbot renew --quiet
```

### SSL Configuration in .htaccess

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

## Cron Jobs Configuration

### Hostinger Cron Jobs

1. Go to **Advanced** → **Cron Jobs** in Hostinger panel
2. Add the following cron jobs:

#### Laravel Scheduler

```
* * * * * cd /home/u1234567/public_html/backend && php artisan schedule:run >> /dev/null 2>&1
```

#### Daily Backup

```
0 2 * * * cd /home/u1234567/public_html/backend && php artisan backup:run --only-db >> /dev/null 2>&1
```

#### Weekly Full Backup

```
0 3 * * 0 cd /home/u1234567/public_html/backend && php artisan backup:run >> /dev/null 2>&1
```

#### Clear Old Logs

```
0 4 * * * find /home/u1234567/public_html/backend/storage/logs -name "*.log" -mtime +30 -delete
```

### Cron Job Commands

| Schedule | Command | Description |
|----------|---------|-------------|
| Every minute | `php artisan schedule:run` | Run Laravel scheduler |
| Daily 2 AM | `php artisan backup:run --only-db` | Database backup |
| Weekly Sunday 3 AM | `php artisan backup:run` | Full backup |
| Daily 4 AM | Clear old logs | Remove logs > 30 days |

---

## Backup Configuration

### Automatic Backups

Add to `.env`:

```env
BACKUP_PATH=storage/app/backups
BACKUP_DISKS=local
```

### Manual Backup via SSH

```bash
cd backend

# Database backup
php artisan backup:run --only-db

# Full backup
php artisan backup:run

# List backups
php artisan backup:list

# Download backup
php artisan backup:download <backup-id>
```

### Backup Storage

Backups are stored in:
```
storage/app/backups/
```

### Configure External Backup (Optional)

Edit `config/backup.php` to add cloud storage:

```php
'backup' => [
    'destination' => [
        'disks' => [
            'local',
            // 's3', // Add S3 support
        ],
    ],
],
```

---

## Performance Optimization

### Enable Gzip Compression

Add to `.htaccess`:

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE application/html
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
    AddOutputFilterByType DEFLATE application/ld+json
    AddOutputFilterByType DEFLATE image/svg+xml
</IfModule>
```

### Browser Caching

Add to `.htaccess`:

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    ExpiresByType application/json "access plus 0 seconds"
    ExpiresByType text/html "access plus 0 seconds"
</IfModule>
```

### Laravel Optimization

```bash
cd backend

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Frontend Optimization

Already handled by Vite build process:
- Code splitting
- Tree shaking
- Minification
- Asset hashing

---

## Security Hardening

### File Permissions

```bash
# Directories
find . -type d -exec chmod 755 {} \;

# Files
find . -type f -exec chmod 644 {} \;

# Specific files
chmod 600 .env
chmod 644 .htaccess
chmod 755 artisan
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Disable Directory Listing

Add to `.htaccess`:

```apache
Options -Indexes
```

### Protect Sensitive Files

```apache
# Protect .env
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

# Protect .git
<Directory ".git">
    Order allow,deny
    Deny from all
</Directory>

# Protect hidden files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### Security Headers

```apache
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self';"
    Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header set Permissions-Policy "camera=(), microphone=(), geolocation=()"
</IfModule>
```

### Disable PHP Info

```apache
<Files "phpinfo.php">
    Order allow,deny
    Deny from all
</Files>
```

### Rate Limiting

Configure in `routes/api.php` or via middleware:

```php
Route::middleware('throttle:60,1')->group(function () {
    // API routes
});
```

---

## Environment-Specific Configurations

### Production .env

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Development .env

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

---

## Domain Configuration

### Primary Domain

1. Log in to Hostinger panel
2. Go to **Domains** → **Domain Settings**
3. Ensure domain points to `public_html`

### Subdomain Setup

1. Go to **Domains** → **Subdomains**
2. Create subdomain: `emr.your-domain.com`
3. Set document root to `public_html`
4. Follow standard deployment steps

### Addon Domain

1. Go to **Domains** → **Addon Domains**
2. Add domain with document root `public_html`
3. Follow standard deployment steps

---

## Monitoring and Maintenance

### Health Check Endpoint

Add to `routes/api.php`:

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});
```

### Log Monitoring

```bash
# Via SSH
tail -f storage/logs/laravel.log

# Error monitoring
grep -i "error" storage/logs/laravel.log | tail -50
```

### Database Monitoring

```bash
# Check database size
mysql -u username -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema = 'hospital_emr';"
```

### Disk Space Monitoring

```bash
# Via SSH
df -h
du -sh storage/
```

---

## Troubleshooting Deployment

### 500 Internal Server Error

1. Check `storage/logs/laravel.log`
2. Verify file permissions
3. Ensure `.env` is configured correctly
4. Check PHP version (8.2+)

### 404 Not Found

1. Verify `.htaccess` is in place
2. Check `RewriteBase` in `.htaccess`
3. Ensure mod_rewrite is enabled

### Database Connection Failed

1. Verify database credentials in `.env`
2. Check database exists in Hostinger panel
3. Verify user has correct privileges

### CORS Errors

1. Update `FRONTEND_URL` in `.env`
2. Check `config/cors.php` settings
3. Verify headers are not being stripped

### Permission Denied

1. Recursively set permissions:
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```
2. Ensure web server user owns files

### Memory Limit Exceeded

Increase in `php.ini` or Hostinger panel:
```ini
memory_limit = 256M
```

---

## Rollback Procedure

If deployment fails:

1. **Restore from backup:**
   ```bash
   php artisan backup:restore <backup-id>
   ```

2. **Revert code:**
   ```bash
   git checkout HEAD~1
   ```

3. **Clear caches:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```

4. **Restart services:**
   ```bash
   php artisan queue:restart
   ```

---

## Post-Deployment Checklist

- [ ] SSL certificate installed and working
- [ ] HTTPS redirect configured
- [ ] Database migrations run
- [ ] Default data seeded
- [ ] Admin password changed
- [ ] Email configuration tested
- [ ] Cron jobs configured
- [ ] Backup automation working
- [ ] Error logging configured
- [ ] Performance optimized
- [ ] Security headers in place
- [ ] Directory listing disabled
- [ ] Sensitive files protected
- [ ] CORS configured correctly
- [ ] API endpoints tested
- [ ] Frontend loading correctly
- [ ] Health check endpoint working
