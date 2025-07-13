# 🚀 Deploy to Vercel

## 📋 **Prerequisites**
- A GitHub account (free)
- Your Stock Tracker App code

## 🎯 **Step-by-Step Vercel Deployment**

### Step 1: Sign Up for Vercel
1. Go to [vercel.com](https://vercel.com)
2. Click **"Sign Up"**
3. **Sign up with your GitHub account**
4. Verify your email

### Step 2: Deploy Your App
1. **Click "New Project"**
2. **Import your GitHub repository** (Stock_Tracker_App)
3. **Vercel will automatically detect it's a PHP app**
4. Click **"Deploy"**

### Step 3: Configure Environment Variables
After deployment, go to your project settings and add these environment variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.vercel.app
APP_KEY=base64:your-generated-key-here
DB_CONNECTION=sqlite
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

### Step 4: Generate Application Key
1. Go to your project **"Settings"** → **"Environment Variables"**
2. Add `APP_KEY` with a value like: `base64:your-generated-key-here`
3. You can generate one by running locally: `php artisan key:generate --show`

### Step 5: Database Setup
For Vercel, we'll use **SQLite** (included) or you can connect an external database:

**Option A: SQLite (Recommended for Vercel)**
- No additional setup needed
- Database file will be created automatically

**Option B: External Database**
- Use a service like PlanetScale, Supabase, or Railway
- Add the database URL to environment variables

## 🎉 **Your App Will Be Live!**

After deployment, you'll get a URL like:
`https://your-app-name.vercel.app`

## 📊 **Vercel Free Tier Benefits**
- ✅ **Unlimited deployments**
- ✅ **Automatic HTTPS**
- ✅ **Global CDN**
- ✅ **Automatic scaling**
- ✅ **No credit card required**

## 🔧 **Vercel-Specific Optimizations**

### File Structure
- ✅ `vercel.json` - Vercel configuration
- ✅ `public/index.php` - Entry point
- ✅ `composer.json` - PHP dependencies
- ✅ `package.json` - Node.js dependencies

### Build Process
Vercel will automatically:
1. ✅ Install PHP 8.2
2. ✅ Run `composer install`
3. ✅ Build your app
4. ✅ Deploy to global CDN

## 🚀 **Next Steps After Deployment**

1. **Test your app** - Visit the provided URL
2. **Set up custom domain** (optional)
3. **Configure email settings** for notifications
4. **Set up monitoring** and analytics

## 💰 **Cost**
**$0/month** - Completely free!

---

**Ready to deploy?** Follow the steps above and your Stock Tracker App will be live on Vercel! 🚀 