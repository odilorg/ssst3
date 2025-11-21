# WebP Image Conversion - Production Setup Guide

This guide covers the complete production setup for automatic WebP image conversion in the application.

## Overview

The WebP conversion system automatically converts uploaded images (JPG/PNG) to WebP format with multiple responsive sizes, providing 25-35% better compression.

**Supported Models:**
- **BlogPost**: `featured_image` field
- **Tour**: `hero_image` field  
- **City**: `hero_image` field

## Production Setup Steps

### 1. Supervisor Configuration (Persistent Queue Worker)

Supervisor ensures the queue worker runs continuously and restarts automatically if it crashes.

**Installation:**
```bash
# Ubuntu/Debian
sudo apt-get install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

**Configuration:**

1. Copy the supervisor config to the system directory:
```bash
sudo cp deployment/supervisor-queue-worker.conf /etc/supervisor/conf.d/laravel-queue-worker.conf
```

2. Update paths in the config file:
```bash
sudo nano /etc/supervisor/conf.d/laravel-queue-worker.conf
```

Replace `/path/to/your/project` with your actual project path (e.g., `/var/www/html/ssst3`)

Replace `www-data` with your web server user if different

3. Update supervisor and start the worker:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-queue-worker:*
```

4. Check status:
```bash
sudo supervisorctl status laravel-queue-worker:*
```

**Managing the Queue Worker:**
```bash
# Stop workers
sudo supervisorctl stop laravel-queue-worker:*

# Start workers
sudo supervisorctl start laravel-queue-worker:*

# Restart workers (e.g., after code deployment)
sudo supervisorctl restart laravel-queue-worker:*

# View logs
tail -f storage/logs/worker.log
```

### 2. Environment Configuration

Ensure these variables are set in your `.env` file:

```env
# Queue Configuration
QUEUE_CONNECTION=database

# Image Conversion Settings
IMAGE_CONVERSION_ENABLED=true
IMAGE_WEBP_QUALITY=85
IMAGE_DRIVER=imagick
IMAGE_KEEP_ORIGINAL=false
```

**Important:** The `imagick` driver provides better performance. Ensure it's installed:

```bash
# Check if imagick is installed
php -m | grep imagick

# If not installed (Ubuntu/Debian):
sudo apt-get install php-imagick
sudo systemctl restart php-fpm  # or apache2
```

### 3. Batch Convert Existing Images

After deploying, convert all existing images to WebP:

**Dry Run (Preview):**
```bash
php artisan images:convert-to-webp --dry-run
```

**Convert All Models:**
```bash
php artisan images:convert-to-webp
```

**Convert Specific Model:**
```bash
# Tours only
php artisan images:convert-to-webp --model=tour

# Blog posts only
php artisan images:convert-to-webp --model=blog

# Cities only
php artisan images:convert-to-webp --model=city
```

**Test with Limited Records:**
```bash
php artisan images:convert-to-webp --model=tour --limit=5
```

**Verbose Output:**
```bash
php artisan images:convert-to-webp -v
```

### 4. Monitoring

#### Check Queue Status

```bash
# Check pending jobs count
php artisan queue:monitor image-processing

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

#### View Logs

```bash
# Queue worker logs
tail -f storage/logs/worker.log

# Laravel application logs
tail -f storage/logs/laravel.log

# Filter for image conversion logs
grep "ConvertImageToWebP" storage/logs/laravel.log
```

#### Database Monitoring Queries

```sql
-- Count pending jobs
SELECT COUNT(*) FROM jobs WHERE queue = 'image-processing';

-- Count failed jobs
SELECT COUNT(*) FROM failed_jobs WHERE queue = 'image-processing';

-- Check recent conversions
SELECT id, title, image_processing_status, updated_at 
FROM blog_posts 
WHERE image_processing_status = 'completed' 
ORDER BY updated_at DESC 
LIMIT 10;
```

### 5. Troubleshooting

#### Jobs Not Processing

1. Check if queue worker is running:
```bash
sudo supervisorctl status laravel-queue-worker:*
```

2. Check worker logs for errors:
```bash
tail -50 storage/logs/worker.log
```

3. Manually run a worker to see errors:
```bash
php artisan queue:work --queue=image-processing --tries=1
```

#### Failed Conversions

1. View failed jobs:
```bash
php artisan queue:failed
```

2. Check if imagick is installed:
```bash
php -m | grep imagick
```

3. Check file permissions:
```bash
ls -la storage/app/public/images/webp/
```

4. Retry a specific failed job:
```bash
php artisan queue:retry <job-id>
```

#### High Memory Usage

If image conversion uses too much memory:

1. Reduce the number of worker processes in supervisor config (change `numprocs=2` to `numprocs=1`)

2. Increase PHP memory limit in `php.ini`:
```ini
memory_limit = 512M
```

3. Restart supervisor:
```bash
sudo supervisorctl restart laravel-queue-worker:*
```

### 6. Deployment Checklist

When deploying to production:

- [ ] Install supervisor
- [ ] Configure supervisor with correct paths and user
- [ ] Start queue workers via supervisor
- [ ] Verify imagick extension is installed
- [ ] Set environment variables in `.env`
- [ ] Run batch conversion command
- [ ] Monitor first few conversions for errors
- [ ] Check frontend serves WebP images correctly
- [ ] Verify responsive sizes are generated
- [ ] Set up log rotation for worker logs

### 7. Performance Tuning

**Optimize for Large Images:**

Adjust timeout in supervisor config for large image processing:
```ini
command=php /path/to/project/artisan queue:work --queue=image-processing --tries=3 --timeout=600
stopwaitsecs=3600
```

**Increase Worker Count:**

For high-traffic sites, increase workers in supervisor config:
```ini
numprocs=4
```

**Queue Priority:**

If you have multiple queues, prioritize image processing:
```bash
php artisan queue:work --queue=high-priority,image-processing,default
```

### 8. Maintenance

**Weekly Tasks:**
- Check failed jobs count
- Review worker logs for errors
- Monitor storage usage in `storage/app/public/images/webp/`

**Monthly Tasks:**
- Analyze conversion success rate
- Review and clean old failed jobs
- Check database queue table size

**After Code Deployment:**
```bash
# Restart queue workers to load new code
sudo supervisorctl restart laravel-queue-worker:*
```

## Verification

After setup, verify everything works:

1. Upload a test image via Filament admin (Blog Post or Tour)
2. Check the queue processed it:
   ```bash
   tail -f storage/logs/laravel.log | grep ConvertImageToWebP
   ```
3. Verify WebP files exist:
   ```bash
   ls -lh storage/app/public/images/webp/
   ```
4. Check frontend serves WebP:
   - Visit a blog post or tour page
   - Open browser DevTools → Network tab
   - Find image request → verify it's a `.webp` file
5. Verify responsive sizes:
   - Check HTML source for `<picture>` element with `srcset`

## Support

If issues persist:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check worker logs: `storage/logs/worker.log`
3. Review failed jobs: `php artisan queue:failed`
4. Test manually: `php artisan images:convert-to-webp --model=blog --limit=1 -v`
