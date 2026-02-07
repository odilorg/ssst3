# Project Context - Jahongir Travel (VPS Staging)

> **Essential project information for context recovery after auto-compact**

---

## ⚠️ CRITICAL: VPS-ONLY DEVELOPMENT

**This is a VPS staging environment. You are running DIRECTLY on the server!**

**ALWAYS:**
- ✅ Work ONLY in: /domains/staging.jahongir-travel.uz/
- ✅ Use php artisan commands for Laravel
- ✅ Use composer for dependencies
- ✅ Test on https://staging.jahongir-travel.uz

**DO NOT:**
- ❌ Suggest local development commands
- ❌ Reference localhost or local paths
- ❌ Touch other /domains/* directories
- ❌ Touch /var/www/* directories
- ❌ Modify nginx/systemd configs
- ❌ Run destructive commands (rm -rf /, drop database, etc.)

---

## 📊 Project Overview

**What:** Travel booking platform for Jahongir Travel
**URL:** https://staging.jahongir-travel.uz
**Admin:** https://staging.jahongir-travel.uz/admin
**Tech:** Laravel 12 + Filament 4 + PostgreSQL
**Market:** Uzbekistan tourism (RU/EN/UZ trilingual)

---

## 🛠️ Tech Stack

- **Framework:** Laravel 12 (PHP 8.2+)
- **Admin Panel:** Filament 4.0
- **Database:** PostgreSQL
- **Payments:** OctoBank API
- **AI:** OpenAI PHP client
- **Frontend:** Blade + Livewire + Tailwind CSS

---

## 📁 Project Structure

```
/domains/staging.jahongir-travel.uz/
├── app/
│   ├── Filament/       # Admin panel resources
│   ├── Http/           # Controllers, Middleware
│   ├── Livewire/       # Livewire components
│   ├── Models/         # Eloquent models
│   └── Services/       # Business logic
├── resources/views/    # Blade templates
├── routes/web.php      # Web routes
├── public/             # Public assets
├── config/             # Laravel config
├── database/           # Migrations, seeders
└── lang/               # Translations (ru/en/uz)
```

---

## 🚀 Quick Commands

```bash
# Navigate to project
cd /domains/staging.jahongir-travel.uz

# Artisan commands
php artisan migrate
php artisan cache:clear
php artisan config:clear
php artisan queue:work
php artisan tinker

# Composer
composer install
composer dump-autoload

# Git
git pull origin main
git status
```

---

## 🔒 Safety Rules

**ALLOWED:**
- Work in /domains/staging.jahongir-travel.uz/**
- Git, composer, artisan commands
- Read files anywhere (for reference)

**FORBIDDEN:**
- DO NOT touch other /domains/* or /var/www/*
- DO NOT modify /etc/nginx/*, /etc/systemd/*
- DO NOT run: rm -rf /, pm2 delete all, systemctl stop nginx
- DO NOT drop/modify other databases

---

**Last Updated:** 2026-01-08
**Environment:** VPS STAGING ONLY
**Tech Stack:** Laravel 12 + Filament 4
