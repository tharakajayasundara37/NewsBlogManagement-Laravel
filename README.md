# News Blog Management — Laravel + MongoDB

A Laravel 12 and MongoDB rebuild of the original PHP/MySQL News Blog Management project. The public website, author workflow and administrative control room are responsive and ready for local use or Vercel deployment.

## Included features

- Professional homepage with featured, latest and popular stories
- News archive with keyword and category search
- Article pages with view counts, related stories, sharing and moderated comments
- Contact messages, newsletter subscriptions, About/team page and FAQ
- Admin, editor and author roles
- Role-aware dashboard statistics and post access
- Post CRUD with filters, date sorting and bulk publish/draft/delete actions
- Vercel-safe image uploads stored with posts in MongoDB (JPG, PNG, GIF, WEBP up to 4 MB)
- Category create/edit/delete with post-count protection
- Comment search, approval, rejection and permanent deletion
- User create/edit/delete, password reset and role management
- MongoDB-backed authentication and content

## Requirements

- PHP 8.2+ with the MongoDB extension
- Composer 2
- Node.js and npm
- MongoDB Atlas or MongoDB Community Server

## Local setup with XAMPP

```powershell
composer install
npm install
npm run build
Copy-Item .env.example .env
& "C:\xampp\php\php.exe" artisan key:generate
# Configure DB_URI, DB_DATABASE, ADMIN_EMAIL and ADMIN_PASSWORD in .env
& "C:\xampp\php\php.exe" artisan db:seed
& "C:\xampp\php\php.exe" artisan serve
```

Open `http://127.0.0.1:8000` and sign in through `/login`.

The converted legacy content is stored in `database/seed-data/legacy-content.json`. Personal contact submissions and credentials are not committed to Git.

## MongoDB database

This project is configured to use the `news_blog_laravel` database. Keep the real Atlas URI only in your local `.env` and Vercel environment variables. Never commit `.env` to a public repository.

## Vercel deployment

The repository includes `vercel.json` and a serverless PHP entry point. Configure these environment variables in Vercel:

- `APP_KEY`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL`
- `DB_CONNECTION=mongodb`
- `DB_URI`
- `DB_DATABASE=news_blog_laravel`
- `SESSION_DRIVER=cookie`
- `CACHE_STORE=array`
- `LOG_CHANNEL=stderr`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`

Deploy only after all environment variables are saved. Uploaded post images are embedded in MongoDB documents, so they remain available across serverless deployments.

## Verification

```powershell
& "C:\xampp\php\php.exe" artisan test
npm run build
```
