# Vercel Deployment Guide for Stock Tracker App

## Current Status
Your app is deployed at: https://stock-tracker-app-nine.vercel.app/

## Issue Identified
The `@vercel/php` package is deprecated and no longer available on npm registry.

## Solution

### 1. Remove vercel.json Configuration
The `vercel.json` file has been removed to let Vercel auto-detect the Laravel application. This is the recommended approach for Laravel apps on Vercel.

### 2. Required Environment Variables
You need to set these environment variables in your Vercel dashboard:

#### Essential Variables:
```
APP_NAME=Stock Tracker App
APP_ENV=production
APP_DEBUG=false
APP_URL=https://stock-tracker-app-nine.vercel.app
APP_KEY=base64:C6nnb5FezItE3O8+SEXV6566lLB9GVSLt5qlEkdy4mI=
```

#### Database Configuration (SQLite):
```
DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite
```

#### Cache and Session:
```
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

#### Mail Configuration:
```
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Stock Tracker App"
```

#### Logging:
```
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 3. Deploy Steps

1. **Commit and push your changes:**
   ```bash
   git add .
   git commit -m "Remove vercel.json for auto-detection"
   git push
   ```

2. **Set Environment Variables in Vercel Dashboard:**
   - Go to your project in Vercel dashboard
   - Navigate to Settings > Environment Variables
   - Add all the variables listed above

3. **Redeploy:**
   - Vercel will automatically redeploy when you push changes
   - Or manually trigger a redeploy from the dashboard

### 4. Alternative: Use Local Development
Since Vercel has compatibility issues with Laravel, you can run the app locally:

```bash
# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

Then visit: http://localhost:8000

### 5. Recommended Alternative Platforms

For production deployment, consider these Laravel-friendly platforms:

#### Option A: Railway (Recommended)
- Excellent Laravel support
- Free tier available
- Easy deployment from GitHub
- Built-in database support

#### Option B: Render
- Good PHP support
- Free tier available
- Automatic deployments
- Built-in SSL

#### Option C: Heroku
- Traditional but reliable
- Good Laravel support
- Free tier (with limitations)
- Extensive documentation

### 6. Railway Deployment (Recommended Alternative)

If you want to switch to Railway:

1. **Sign up at railway.app**
2. **Connect your GitHub repository**
3. **Railway will auto-detect Laravel**
4. **Add environment variables**
5. **Deploy automatically**

Railway provides:
- ✅ Better Laravel support
- ✅ Built-in database
- ✅ Automatic HTTPS
- ✅ Free tier
- ✅ No configuration needed

### 7. Local Development Setup

For now, you can continue development locally:

```bash
# Clone your repository
git clone https://github.com/Shiva1504/Stock_Tracker_App.git
cd Stock_Tracker_App

# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations and seeders
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

### 8. Next Steps

1. **For immediate use**: Run `php artisan serve` locally
2. **For production**: Consider Railway or Render
3. **For learning**: Continue with local development
4. **For sharing**: Use ngrok or similar for temporary public access

## Support

If you need help with:
- Local development: The app works perfectly with `php artisan serve`
- Alternative deployment: Railway or Render are recommended
- Configuration: Check the Laravel documentation

## Current Status

✅ **Local Development**: Working perfectly with `php artisan serve`  
❌ **Vercel Deployment**: Has compatibility issues with Laravel  
🔄 **Alternative Platforms**: Railway/Render recommended for production 