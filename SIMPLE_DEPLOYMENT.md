# 🚀 Simple Deployment Guide (No Docker)

## 🎯 **Railway Deployment (Recommended)**

### Step 1: Deploy to Railway
1. Go to [railway.app](https://railway.app)
2. Click **"Start a New Project"**
3. **Sign up with GitHub**
4. Click **"Deploy from GitHub repo"**
5. **Select your Stock_Tracker_App repository**
6. Railway will automatically detect it's a PHP app

### Step 2: Add PostgreSQL Database
1. Click **"New"** in your project
2. Select **"Database"** → **"PostgreSQL"**
3. Railway will automatically connect it to your app

### Step 3: Environment Variables
Click on your app service and go to "Variables" tab. Add:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app
DB_CONNECTION=pgsql
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stack
LOG_LEVEL=error
FILESYSTEM_DISK=local
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_HTTP_ONLY=true
```

### Step 4: Deploy!
Railway will automatically:
- Install PHP 8.2
- Run `composer install`
- Run `npm install && npm run build`
- Start your app

## 🎯 **Render Deployment (Alternative)**

### Step 1: Deploy to Render
1. Go to [render.com](https://render.com)
2. Click **"Get Started for Free"**
3. **Sign up with GitHub**
4. Click **"New +"** → **"Web Service"**
5. **Connect your GitHub repository**

### Step 2: Configure Service
- **Name**: `stock-tracker-app`
- **Environment**: `PHP`
- **Region**: Choose closest to you
- **Branch**: `main`

### Step 3: Build & Start Commands
**Build Command:**
```bash
composer install --no-dev --optimize-autoloader
npm ci --only=production
npm run build
php artisan key:generate
php artisan storage:link
```

**Start Command:**
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
vendor/bin/heroku-php-apache2 public/
```

### Step 4: Add PostgreSQL Database
1. Click **"New +"** again
2. Select **"PostgreSQL"**
3. Name: `stock-tracker-db`
4. Plan: **Free**

### Step 5: Environment Variables
Add these variables:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
DB_CONNECTION=pgsql
DATABASE_URL=(paste the URL from step 4)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stack
LOG_LEVEL=error
FILESYSTEM_DISK=local
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_HTTP_ONLY=true
```

## 🎉 **Why This Works Better**

✅ **No Docker complexity**  
✅ **Native PHP buildpacks**  
✅ **Automatic PHP 8.2 detection**  
✅ **Simpler deployment process**  
✅ **Fewer points of failure**  

## 📋 **Files Used**
- `Procfile` - Tells Railway/Render how to start the app
- `railway.json` - Railway-specific configuration
- `public/.htaccess` - Apache configuration
- `composer.json` - PHP dependencies
- `package.json` - Node.js dependencies

## 🚀 **Ready to Deploy!**

**Try Railway first** - it's the simplest and most reliable option!

Your app will be live in 5-10 minutes with no Docker build issues. 