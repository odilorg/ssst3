# Deployment Guide - Balance Payment System

## Production Deployment Checklist

**System:** Phase 5 - Email-Based Balance Payment System
**Date:** 2025-11-06
**Version:** 1.0.0

---

## Pre-Deployment Checklist

### 1. Code & Dependencies ✅
- [ ] All code committed to version control
- [ ] All dependencies installed (`composer install --no-dev --optimize-autoloader`)
- [ ] Node modules built for production (`npm run build`)
- [ ] No debug code or console.log statements
- [ ] All TODO comments resolved or documented

### 2. Configuration ✅
- [ ] `.env` file configured for production
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Database credentials correct
- [ ] OCTO API credentials verified
- [ ] Mail configuration tested
- [ ] Queue configuration set to Redis

### 3. Security ✅
- [ ] `.env` file not in version control
- [ ] OCTO webhook secret configured
- [ ] Rate limiting enabled
- [ ] HTTPS configured
- [ ] CSRF protection enabled
- [ ] Security audit passed

### 4. Database ✅
- [ ] Migrations reviewed
- [ ] Backup strategy in place
- [ ] Database indexes optimized
- [ ] Foreign keys validated

### 5. Testing ✅
- [ ] Integration tests passed
- [ ] Manual testing completed
- [ ] Payment flow tested end-to-end
- [ ] Email delivery verified
- [ ] Webhook validation tested

---

## Server Requirements

### Minimum Specifications

**Web Server:**
- CPU: 2 cores
- RAM: 4GB
- Storage: 20GB SSD
- OS: Ubuntu 22.04 LTS or similar

**Database Server (if separate):**
- CPU: 2 cores
- RAM: 4GB
- Storage: 50GB SSD

**Redis Server:**
- RAM: 1GB minimum

### Software Requirements

- **PHP:** 8.2+
- **MySQL:** 8.0+
- **Redis:** 6.0+
- **Nginx:** 1.20+ or Apache 2.4+
- **Composer:** 2.5+
- **Node.js:** 18+ (for asset compilation)
- **Supervisor:** For queue worker management

---

## Step 1: Server Preparation

### 1.1 Update System Packages

```bash
sudo apt update
sudo apt upgrade -y
```

### 1.2 Install PHP and Extensions

```bash
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-redis \
    php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip \
    php8.2-gd php8.2-intl php8.2-cli php8.2-common
```

### 1.3 Install MySQL

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 1.4 Install Redis

```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

### 1.5 Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
```

### 1.6 Install Composer

```bash
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

### 1.7 Install Supervisor

```bash
sudo apt install -y supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

---

## Step 2: Application Deployment

### 2.1 Clone Repository

```bash
cd /var/www
sudo git clone https://github.com/yourusername/ssst3.git
sudo chown -R www-data:www-data ssst3
cd ssst3
```

### 2.2 Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 2.3 Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/ssst3
sudo chmod -R 755 /var/www/ssst3
sudo chmod -R 775 /var/www/ssst3/storage
sudo chmod -R 775 /var/www/ssst3/bootstrap/cache
```

### 2.4 Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file:

```env
APP_NAME="Jahongir Travel"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ssst3_production
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

OCTO_API_KEY=your_production_api_key
OCTO_MERCHANT_ID=your_merchant_id
OCTO_WEBHOOK_SECRET=your_webhook_secret
OCTO_BASE_URL=https://api.octo.uz
```

### 2.5 Database Setup

```bash
# Create database
mysql -u root -p
> CREATE DATABASE ssst3_production;
> CREATE USER 'ssst3_user'@'localhost' IDENTIFIED BY 'secure_password';
> GRANT ALL PRIVILEGES ON ssst3_production.* TO 'ssst3_user'@'localhost';
> FLUSH PRIVILEGES;
> EXIT;

# Run migrations
php artisan migrate --force

# Seed data if needed
# php artisan db:seed --force
```

### 2.6 Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
composer dump-autoload --optimize
```

---

## Step 3: Nginx Configuration

### 3.1 Create Nginx Config

Create `/etc/nginx/sites-available/ssst3`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/ssst3/public;

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
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Rate limiting for payment endpoints
    location ~ ^/balance-payment/ {
        limit_req zone=payment burst=5 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
}

# Rate limit zone (add to nginx.conf http block)
# limit_req_zone $binary_remote_addr zone=payment:10m rate=10r/m;
```

### 3.2 Enable Site

```bash
sudo ln -s /etc/nginx/sites-available/ssst3 /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 3.3 Install SSL Certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

---

## Step 4: Queue Worker Setup (Supervisor)

### 4.1 Create Supervisor Config

Create `/etc/supervisor/conf.d/ssst3-worker.conf`:

```ini
[program:ssst3-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ssst3/artisan queue:work redis --queue=urgent,default --sleep=3 --tries=3 --max-time=3600 --timeout=60
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ssst3/storage/logs/worker.log
stopwaitsecs=3600
```

### 4.2 Start Queue Worker

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ssst3-worker:*
```

### 4.3 Verify Queue Worker

```bash
sudo supervisorctl status ssst3-worker:*
```

---

## Step 5: Scheduler Setup

### 5.1 Add Cron Entry

```bash
sudo crontab -e -u www-data
```

Add this line:

```cron
* * * * * cd /var/www/ssst3 && php artisan schedule:run >> /dev/null 2>&1
```

### 5.2 Verify Scheduler

```bash
php artisan schedule:list
```

---

## Step 6: Monitoring & Logging

### 6.1 Log Rotation

Create `/etc/logrotate.d/ssst3`:

```
/var/www/ssst3/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    sharedscripts
}
```

### 6.2 Monitor Queue Jobs

```bash
# Check queue status
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### 6.3 Monitor Error Logs

```bash
tail -f /var/www/ssst3/storage/logs/laravel.log
tail -f /var/www/ssst3/storage/logs/worker.log
tail -f /var/log/nginx/error.log
```

---

## Step 7: Testing Production Deployment

### 7.1 Health Checks

```bash
# Test application
curl https://yourdomain.com

# Test payment page (with valid token)
curl https://yourdomain.com/balance-payment/test-token

# Test webhook endpoint
curl -X POST https://yourdomain.com/balance-payment/webhook \
     -H "Content-Type: application/json" \
     -d '{"test":"data"}'
```

### 7.2 Test Email Sending

```bash
php artisan tinker
>>> $booking = \App\Models\Booking::first();
>>> \App\Jobs\SendBalancePaymentReminder::dispatch($booking, 7);
```

Check logs:
```bash
tail -f storage/logs/laravel.log | grep -i "mail\|reminder"
```

### 7.3 Test Queue Processing

```bash
# Dispatch test job
php artisan tinker
>>> dispatch(function() { \Log::info('Test queue job'); });

# Check worker log
tail -f storage/logs/worker.log
```

### 7.4 Test Payment Flow

1. Create a test booking with deposit paid
2. Generate payment token manually in admin panel
3. Visit payment URL
4. Complete test payment (use OCTO test credentials)
5. Verify callback/webhook handling
6. Check booking was updated correctly

---

## Step 8: Backup Strategy

### 8.1 Database Backup Script

Create `/usr/local/bin/backup-ssst3-db.sh`:

```bash
#!/bin/bash
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/var/backups/ssst3/database"
DB_NAME="ssst3_production"
DB_USER="ssst3_user"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/backup_$TIMESTAMP.sql.gz

# Keep only last 7 days
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +7 -delete
```

Make executable:
```bash
sudo chmod +x /usr/local/bin/backup-ssst3-db.sh
```

### 8.2 Application Backup Script

Create `/usr/local/bin/backup-ssst3-app.sh`:

```bash
#!/bin/bash
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/var/backups/ssst3/application"
APP_DIR="/var/www/ssst3"

mkdir -p $BACKUP_DIR
tar -czf $BACKUP_DIR/app_$TIMESTAMP.tar.gz \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    $APP_DIR

# Keep only last 7 days
find $BACKUP_DIR -name "app_*.tar.gz" -mtime +7 -delete
```

Make executable:
```bash
sudo chmod +x /usr/local/bin/backup-ssst3-app.sh
```

### 8.3 Schedule Backups

```bash
sudo crontab -e
```

Add:
```cron
# Database backup daily at 2 AM
0 2 * * * /usr/local/bin/backup-ssst3-db.sh

# Application backup weekly on Sunday at 3 AM
0 3 * * 0 /usr/local/bin/backup-ssst3-app.sh
```

---

## Step 9: Security Hardening

### 9.1 Firewall Configuration

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 9.2 Fail2Ban Setup

```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

Create `/etc/fail2ban/jail.local`:

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[nginx-http-auth]
enabled = true

[nginx-noscript]
enabled = true

[nginx-badbots]
enabled = true

[nginx-noproxy]
enabled = true
```

### 9.3 Disable Unnecessary Services

```bash
sudo systemctl disable bluetooth
sudo systemctl disable cups
```

---

## Step 10: Post-Deployment Verification

### Checklist

- [ ] Website accessible via HTTPS
- [ ] SSL certificate valid and auto-renewing
- [ ] Queue worker running (check with `supervisorctl status`)
- [ ] Cron scheduler running (check crontab)
- [ ] Database accessible and backed up
- [ ] Email sending working
- [ ] Payment processing tested
- [ ] Webhook receiving and processing
- [ ] Admin panel accessible
- [ ] Logs rotating correctly
- [ ] Firewall configured
- [ ] Monitoring tools set up

### Testing Commands

```bash
# Check application status
php artisan about

# Check queue worker
sudo supervisorctl status ssst3-worker:*

# Check cron jobs
sudo crontab -l -u www-data

# Check nginx
sudo nginx -t
sudo systemctl status nginx

# Check PHP-FPM
sudo systemctl status php8.2-fpm

# Check MySQL
sudo systemctl status mysql

# Check Redis
redis-cli ping

# View application logs
tail -f /var/www/ssst3/storage/logs/laravel.log

# View nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

---

## Step 11: Monitoring Setup (Optional but Recommended)

### 11.1 Install Laravel Horizon (Optional)

If you prefer Horizon over Supervisor:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon:publish
```

Access at: `https://yourdomain.com/admin/horizon`

### 11.2 Application Performance Monitoring

Consider integrating:
- **Sentry** for error tracking
- **New Relic** for performance monitoring
- **LogRocket** for user session replay
- **Uptime Robot** for uptime monitoring

---

## Rollback Procedure

If something goes wrong:

### Quick Rollback

```bash
# Stop queue workers
sudo supervisorctl stop ssst3-worker:*

# Rollback to previous version
cd /var/www/ssst3
git reset --hard previous-commit-hash

# Reinstall dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Recache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl reload nginx
sudo supervisorctl start ssst3-worker:*
```

### Database Rollback

```bash
# Restore from backup
gunzip < /var/backups/ssst3/database/backup_TIMESTAMP.sql.gz | \
    mysql -u ssst3_user -p ssst3_production
```

---

## Maintenance Mode

### Enable Maintenance Mode

```bash
php artisan down --refresh=15 --secret="maintenance-bypass-token"
```

Access site during maintenance: `https://yourdomain.com/maintenance-bypass-token`

### Disable Maintenance Mode

```bash
php artisan up
```

---

## Troubleshooting Production Issues

### Common Issues

**Issue: Queue not processing**
```bash
# Check worker status
sudo supervisorctl status ssst3-worker:*

# Restart workers
sudo supervisorctl restart ssst3-worker:*

# Check logs
tail -f storage/logs/worker.log
```

**Issue: Emails not sending**
```bash
# Check queue
php artisan queue:work --once --verbose

# Test mail configuration
php artisan tinker
>>> Mail::raw('Test', fn($msg) => $msg->to('test@example.com'));

# Check mail logs
tail -f storage/logs/laravel.log | grep -i mail
```

**Issue: Payments not completing**
```bash
# Check webhook logs
tail -f storage/logs/laravel.log | grep -i webhook

# Verify OCTO credentials
php artisan tinker
>>> config('services.octo')

# Test webhook signature
# See BALANCE_PAYMENT_SYSTEM.md troubleshooting section
```

---

## Support Contacts

**Technical Support:** tech@yourdomain.com
**Emergency Contact:** +998 XX XXX XX XX
**OCTO Support:** support@octo.uz

---

## Version Control

**Deployment Date:** 2025-11-06
**Version:** 1.0.0
**Deployed By:** [Your Name]
**Git Commit:** [Commit Hash]

---

**Next Steps:**
1. Monitor application for 24 hours
2. Set up alerting for failed jobs
3. Schedule performance review in 1 week
4. Update documentation based on production learnings

---

## Checklist Complete ✅

- [x] Server prepared and secured
- [x] Application deployed
- [x] Database configured and backed up
- [x] Queue worker running with Supervisor
- [x] Scheduler configured with cron
- [x] Nginx configured with SSL
- [x] Monitoring and logging set up
- [x] Backups automated
- [x] Security hardened
- [x] Production testing completed

**Status: READY FOR PRODUCTION**

---

*Document Version: 1.0*
*Last Updated: 2025-11-06*
*Maintained By: Development Team*
