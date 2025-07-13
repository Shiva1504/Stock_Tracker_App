# 🚀 Deploy to Railway - Simple Method

## 🎯 **Step-by-Step Deployment**

### Step 1: Deploy on Railway
1. Go to [railway.app](https://railway.app)
2. Click **"Start a New Project"**
3. **Sign up with GitHub**
4. Click **"Deploy from GitHub repo"**
5. **Select your Stock_Tracker_App repository**
6. **Click "Deploy Now"**

### Step 2: Add PostgreSQL Database
1. Click **"New"** in your project
2. Select **"Database"** → **"PostgreSQL"**
3. Railway will automatically connect it

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

### Step 4: Copy Database URL
1. Click on your **database service**
2. Copy the **"Connect"** URL
3. Add it to your app service variables as `DATABASE_URL`

## 🎉 **That's It!**

Railway will automatically:
- ✅ Detect it's a PHP app
- ✅ Install PHP 8.2
- ✅ Run `composer install`
- ✅ Run `npm install && npm run build`
- ✅ Start your app

## 📋 **Files Used**
- `Procfile` - Tells Railway how to start the app
- `composer.json` - PHP dependencies
- `package.json` - Node.js dependencies
- `.env.example` - Environment template

## 🚀 **Your App Will Be Live!**

After deployment, you'll get a URL like:
`https://your-app-name.railway.app`

**No complex configuration needed!** Railway will handle everything automatically. 