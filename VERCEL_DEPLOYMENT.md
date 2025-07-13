# Vercel Deployment Guide for Stock Tracker App

## Current Status
Your app is deployed at: https://stock-tracker-app-nine.vercel.app/

## Issue Identified
The PHP code is being served as plain text instead of being executed. This is a common issue with Laravel on Vercel.

## Solution

### 1. Update vercel.json Configuration
The current configuration has been updated to use the PHP runtime:

```json
{
  "version": 2,
  "framework": null,
  "builds": [
    {
      "src": "public/index.php",
      "use": "@vercel/php"
    }
  ],
  "routes": [
    {
      "src": "/(.*)",
      "dest": "/public/index.php"
    }
  ]
}
```

### 2. Required Environment Variables
You need to set these environment variables in your Vercel dashboard:

#### Essential Variables:
```
APP_NAME=Stock Tracker App
APP_ENV=production
APP_DEBUG=false
APP_URL=https://stock-tracker-app-nine.vercel.app
APP_KEY=base64:YOUR_32_CHARACTER_RANDOM_KEY
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

### 3. Generate Application Key
Run this command locally to generate a secure APP_KEY:

```bash
php artisan key:generate --show
```

Copy the output and use it as your APP_KEY in Vercel.

### 4. Database Setup
Since Vercel uses serverless functions, the database will be recreated on each request. For production, consider:

#### Option A: Use External Database
- Set up a free database on Railway, PlanetScale, or similar
- Update DB_CONNECTION to mysql or pgsql
- Add database credentials to environment variables

#### Option B: Use Vercel Storage
- Vercel offers KV storage that can be used for simple data
- Requires code modifications to use KV instead of SQLite

### 5. Deploy Steps

1. **Commit and push your changes:**
   ```bash
   git add .
   git commit -m "Fix Vercel configuration for Laravel"
   git push
   ```

2. **Set Environment Variables in Vercel Dashboard:**
   - Go to your project in Vercel dashboard
   - Navigate to Settings > Environment Variables
   - Add all the variables listed above

3. **Redeploy:**
   - Vercel will automatically redeploy when you push changes
   - Or manually trigger a redeploy from the dashboard

### 6. Post-Deployment Setup

After successful deployment, you'll need to run migrations:

```bash
# Connect to Vercel function and run migrations
vercel env pull .env.local
php artisan migrate --force
```

### 7. Troubleshooting

#### If PHP still shows as text:
1. Check that `@vercel/php` is properly configured
2. Ensure the build is successful in Vercel logs
3. Try removing and re-adding the build configuration

#### If database errors occur:
1. Ensure SQLite database path is writable (`/tmp/`)
2. Check that migrations have been run
3. Verify database connection settings

#### If environment variables aren't working:
1. Check Vercel dashboard for correct variable names
2. Ensure variables are set for production environment
3. Redeploy after adding new variables

### 8. Production Considerations

#### Performance:
- Vercel functions have cold starts
- Consider using external database for better performance
- Implement caching strategies

#### Storage:
- Vercel functions are stateless
- Use external storage for file uploads
- Consider CDN for static assets

#### Monitoring:
- Set up error tracking (Sentry, Bugsnag)
- Monitor function execution times
- Set up logging aggregation

### 9. Alternative Deployment Options

If Vercel continues to have issues, consider:

1. **Railway** - Better Laravel support
2. **Render** - Free tier with good PHP support
3. **Heroku** - Traditional but reliable
4. **DigitalOcean App Platform** - Good performance

## Next Steps

1. Update the vercel.json configuration
2. Set environment variables in Vercel dashboard
3. Generate and set APP_KEY
4. Redeploy the application
5. Test all functionality
6. Set up monitoring and logging

## Support

If you encounter issues:
1. Check Vercel function logs
2. Verify environment variables
3. Test locally with production settings
4. Consider alternative deployment platforms 