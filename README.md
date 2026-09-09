# NewsHub — Laravel + MongoDB

A Laravel 12 rebuild of the legacy NewsBlogManagement PHP/MySQL application. It includes a public news site, search and category filters, article comments, contact and newsletter forms, authentication, author post management, and admin category/comment/user management.

## Requirements

- PHP 8.2+ with the MongoDB extension
- Composer 2
- MongoDB Atlas or MongoDB Community Server

## Local setup

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
# Set DB_URI and DB_DATABASE in .env
php artisan db:seed
php artisan serve
```

The supplied MariaDB content has been transformed into safe MongoDB seed data. Personal emails, contact messages, comments and password hashes were deliberately excluded from the repository. Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` before seeding.

## Vercel

The project includes a Vercel PHP community-runtime entry point. Configure `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `DB_CONNECTION=mongodb`, `DB_URI`, `DB_DATABASE=news_blog`, `SESSION_DRIVER=cookie`, `CACHE_STORE=array`, and `LOG_CHANNEL=stderr` in Vercel before deployment.
