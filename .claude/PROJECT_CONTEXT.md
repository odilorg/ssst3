# Project Context - Jahongir Travel Platform (Staging)

> **Essential project information for context recovery after auto-compact**
>
> Read this file during session initialization instead of full README.md

---

## 📊 Project Overview

**What:** Travel booking platform for Jahongir Travel company
**URL:** https://staging.jahongir-travel.uz
**Market:** Uzbekistan tourism (Russian/English/Uzbek trilingual)
**Users:** Tourists, travel agents, admin team
**Client:** Jahongir Travel company

**Key Features:**
- Tour catalog with detailed itineraries
- Online booking system with payments (OctoBank)
- Blog for travel content
- Admin panel (Filament 4) for content management
- Lead management CRM
- Multi-language support (RU/EN/UZ)
- AI-powered tour matching and email generation

---

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 12 (PHP 8.2+)
- **Admin Panel:** Filament 4.0
- **Database:** PostgreSQL / MySQL
- **ORM:** Eloquent
- **Payments:** OctoBank API integration
- **AI:** OpenAI PHP client

### Frontend
- **Templating:** Blade + Livewire
- **Styling:** Tailwind CSS
- **JS:** Alpine.js

### Infrastructure
- **Server:** VPS at /domains/staging.jahongir-travel.uz
- **Web Server:** Nginx
- **Queue:** Laravel Queue (database driver)

---

## 📁 Project Structure

```
/domains/staging.jahongir-travel.uz/
├── app/
│   ├── Filament/       # Admin panel resources
│   ├── Http/           # Controllers, Middleware
│   ├── Livewire/       # Livewire components
│   ├── Models/         # Eloquent models
│   ├── Services/       # Business logic
│   └── Mail/           # Email classes
├── resources/
│   └── views/          # Blade templates
├── routes/
│   └── web.php         # Web routes
├── public/             # Public assets
├── config/             # Laravel config
├── database/           # Migrations, seeders
└── lang/               # Translations (ru/en/uz)
```

---

## 🚀 Quick Commands

### Artisan
```bash
cd /domains/staging.jahongir-travel.uz
php artisan migrate           # Run migrations
php artisan cache:clear       # Clear cache
php artisan config:clear      # Clear config cache
php artisan queue:work        # Run queue worker
php artisan tinker            # Interactive REPL
```

### Composer
```bash
composer install              # Install dependencies
composer dump-autoload        # Regenerate autoload
```

### Git
```bash
git pull origin main          # Pull latest changes
git status                    # Check status
```

---

## ⚠️ Known Issues

1. **ImportLeads Wizard** - Filament 4 compatibility issue (disabled)
2. See `KNOWN_ISSUES.md` for full list

---

## 🔗 Related URLs

- **Staging:** https://staging.jahongir-travel.uz
- **Admin Panel:** https://staging.jahongir-travel.uz/admin
- **Dev Environment:** https://dev.jahongir-travel.uz (/var/www/jahongir-dev)

---

## 🔒 Safety Rules (CRITICAL!)

**ALLOWED:**
- Work ONLY in: /domains/staging.jahongir-travel.uz/**
- Git operations in project folder
- Composer/artisan commands in project folder
- Read files anywhere (for reference)

**FORBIDDEN:**
- DO NOT touch other /domains/* directories
- DO NOT touch /var/www/* (other projects)
- DO NOT modify /etc/nginx/*, /etc/systemd/*
- DO NOT run destructive commands on other sites

---

**Last Updated:** 2026-01-08
**Project Type:** Laravel 12 + Filament 4 Travel Platform
