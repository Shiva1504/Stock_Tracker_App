# 🚀 Free Deployment to Render

## 📋 Prerequisites
- A GitHub account (free)
- Your Stock Tracker App code

## 🎯 Step-by-Step Deployment

### Step 1: Push Your Code to GitHub
```bash
# Initialize git (if not already done)
git init
git add .
git commit -m "Ready for Render deployment"

# Create a new repository on GitHub.com
# Then push your code:
git remote add origin https://github.com/your-username/Stock_Tracker_App.git
git branch -M main
git push -u origin main
```

### Step 2: Sign Up for Render
1. Go to [render.com](https://render.com)
2. Click "Get Started for Free"
3. Sign up with your GitHub account
4. Verify your email

### Step 3: Deploy Your App
1. **Click "New +"** in your Render dashboard
2. **Select "Web Service"**
3. **Connect your GitHub repository**
4. **Configure your service:**
   - **Name**: `stock-tracker-app`
   - **Environment**: `PHP`
   - **Region**: Choose closest to you
   - **Branch**: `main`
   - **Build Command**: 
     ```bash
     composer install --no-dev --optimize-autoloader
     npm ci --only=production
     npm run build
     php artisan key:generate
     php artisan storage:link
     ```
   - **Start Command**:
     ```bash
     php artisan migrate --force
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     vendor/bin/heroku-php-apache2 public/
     ```

### Step 4: Add Environment Variables
Click "Environment" tab and add these variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
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

### Step 5: Add PostgreSQL Database
1. **Click "New +"** again
2. **Select "PostgreSQL"**
3. **Name**: `stock-tracker-db`
4. **Plan**: Free
5. **Copy the database URL**
6. **Add to your web service environment variables:**
   - `DATABASE_URL`: (paste the URL from step 5)

### Step 6: Deploy!
1. Click "Create Web Service"
2. Wait for build to complete (5-10 minutes)
3. Your app will be live at: `https://your-app-name.onrender.com`

## 🎉 You're Done!

Your Stock Tracker App is now live and free! 

## 📊 Free Tier Limits
- **750 hours/month** (enough for 24/7 uptime)
- **512MB RAM**
- **Sleeps after 15 minutes** of inactivity (wakes up on first request)
- **PostgreSQL database** included

## 🔧 Troubleshooting

### If Build Fails:
1. Check the build logs in Render dashboard
2. Make sure all dependencies are in `composer.json`
3. Verify your `.htaccess` file is in the `public/` directory

### If App Doesn't Work:
1. Check the logs in Render dashboard
2. Verify all environment variables are set
3. Make sure database URL is correct

### Database Connection Issues:
1. Ensure `DATABASE_URL` is set correctly
2. Check if database is created and running
3. Verify migrations ran successfully

## 🚀 Next Steps
- Set up a custom domain (optional)
- Configure email settings for notifications
- Set up monitoring and alerts

## 💰 Cost
**$0/month** - Completely free!

---

**Need help?** Check Render's documentation or ask for support in their community. 