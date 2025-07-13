# GitHub Deployment Guide for Stock Tracker App

## 🚀 **GitHub-Based Deployment Options**

Since GitHub Pages only supports static sites, we'll use GitHub Actions to deploy your Laravel app to free hosting platforms.

## 📋 **Option 1: Railway (Recommended)**

### **Step 1: Set up Railway**
1. Go to [railway.app](https://railway.app)
2. Sign up with your GitHub account
3. Click "New Project" → "Deploy from GitHub repo"
4. Select your `Stock_Tracker_App` repository
5. Railway will auto-detect Laravel and deploy

### **Step 2: Configure GitHub Secrets**
1. Go to your GitHub repository
2. Navigate to **Settings** → **Secrets and variables** → **Actions**
3. Add these secrets:
   - `RAILWAY_TOKEN`: Your Railway API token
   - `RAILWAY_SERVICE`: Your Railway service ID

### **Step 3: Environment Variables in Railway**
Add these environment variables in Railway dashboard:
```
APP_NAME=Stock Tracker App
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app
APP_KEY=base64:C6nnb5FezItE3O8+SEXV6566lLB9GVSLt5qlEkdy4mI=
DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## 📋 **Option 2: Render**

### **Step 1: Set up Render**
1. Go to [render.com](https://render.com)
2. Sign up with your GitHub account
3. Click "New" → "Web Service"
4. Connect your GitHub repository
5. Configure as PHP service

### **Step 2: Configure GitHub Secrets**
Add these secrets to your GitHub repository:
- `RENDER_API_KEY`: Your Render API key
- `RENDER_SERVICE_ID`: Your Render service ID

### **Step 3: Environment Variables in Render**
Add the same environment variables as listed above.

## 📋 **Option 3: Heroku**

### **Step 1: Set up Heroku**
1. Go to [heroku.com](https://heroku.com)
2. Sign up for a free account
3. Install Heroku CLI
4. Create a new app

### **Step 2: Configure GitHub Actions**
The workflow will automatically deploy to Heroku when you push to main.

## 🔧 **GitHub Actions Workflows**

### **Railway Workflow** (`.github/workflows/deploy.yml`)
```yaml
name: Deploy to Railway

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install Composer dependencies
      run: composer install --prefer-dist --no-interaction
        
    - name: Install Node.js dependencies
      run: npm ci
        
    - name: Build assets
      run: npm run build
        
    - name: Deploy to Railway
      uses: railway/deploy@v1
      with:
        service: ${{ secrets.RAILWAY_SERVICE }}
        token: ${{ secrets.RAILWAY_TOKEN }}
```

### **Render Workflow** (`.github/workflows/deploy-render.yml`)
```yaml
name: Deploy to Render

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install Composer dependencies
      run: composer install --prefer-dist --no-interaction
        
    - name: Install Node.js dependencies
      run: npm ci
        
    - name: Build assets
      run: npm run build
        
    - name: Deploy to Render
      run: |
        curl -X POST "https://api.render.com/deploy/srv-${{ secrets.RENDER_SERVICE_ID }}?key=${{ secrets.RENDER_API_KEY }}"
```

## 🎯 **Recommended Setup: Railway**

### **Why Railway?**
- ✅ **Excellent Laravel support**
- ✅ **Built-in database**
- ✅ **Automatic HTTPS**
- ✅ **Free tier**
- ✅ **Easy GitHub integration**
- ✅ **No complex configuration**

### **Quick Setup Steps:**
1. **Sign up at railway.app**
2. **Connect your GitHub repo**
3. **Railway auto-deploys**
4. **Add environment variables**
5. **Done!**

## 📊 **Deployment Process**

### **Automatic Deployment:**
1. **Push code to main branch**
2. **GitHub Actions triggers**
3. **Build and test code**
4. **Deploy to hosting platform**
5. **App goes live automatically**

### **Manual Deployment:**
1. **Go to hosting platform dashboard**
2. **Trigger manual deployment**
3. **Monitor deployment logs**
4. **Verify app is working**

## 🔍 **Monitoring and Debugging**

### **GitHub Actions Logs:**
- Go to **Actions** tab in your repository
- Click on the latest workflow run
- Check for any errors or warnings

### **Platform Logs:**
- Railway: Dashboard → Logs
- Render: Dashboard → Logs
- Heroku: `heroku logs --tail`

## 🚨 **Troubleshooting**

### **Common Issues:**
1. **Build failures**: Check PHP version and extensions
2. **Environment variables**: Ensure all required vars are set
3. **Database issues**: Check database connection settings
4. **Asset build errors**: Verify Node.js dependencies

### **Solutions:**
1. **Check GitHub Actions logs**
2. **Verify environment variables**
3. **Test locally first**
4. **Check platform documentation**

## 🎉 **Success Indicators**

Your deployment is successful when:
- ✅ **GitHub Actions pass**
- ✅ **App loads without errors**
- ✅ **All features work**
- ✅ **Database operations succeed**
- ✅ **Assets load properly**

## 📞 **Support**

If you encounter issues:
1. **Check GitHub Actions logs**
2. **Verify environment variables**
3. **Test locally with production settings**
4. **Check platform-specific documentation**

## 🚀 **Next Steps**

1. **Choose a platform** (Railway recommended)
2. **Set up the platform account**
3. **Configure GitHub secrets**
4. **Add environment variables**
5. **Push code to trigger deployment**
6. **Monitor and test**

Your Stock Tracker App will be live and automatically updated whenever you push to the main branch! 🎉 