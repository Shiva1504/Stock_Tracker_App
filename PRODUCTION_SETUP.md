# 🚀 Production Deployment Guide

## 📋 Pre-Deployment Checklist

### 1. Environment Configuration
Create a `.env` file with the following production settings:

```env
# Application Settings
APP_NAME="Stock Tracker App"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock_tracker_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Cache & Session Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Security Settings
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
SESSION_HTTP_ONLY=true

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# File Storage
FILESYSTEM_DISK=local

# Queue Configuration
QUEUE_FAILED_DRIVER=database-uuids

# Broadcasting
BROADCAST_DRIVER=log

# Timezone & Locale
APP_TIMEZONE=Asia/Kolkata
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

### 2. Security Checklist
- [ ] Generate new APP_KEY
- [ ] Set APP_DEBUG=false
- [ ] Use HTTPS URLs
- [ ] Configure secure database credentials
- [ ] Set up Redis for caching
- [ ] Configure secure mail settings
- [ ] Set up proper file permissions

### 3. Performance Optimizations
- [ ] Enable Redis caching
- [ ] Configure queue workers
- [ ] Set up database indexing
- [ ] Optimize asset compilation
- [ ] Configure CDN (optional)

## 🔧 Production Setup Commands

### 1. Generate Application Key
```bash
php artisan key:generate
```

### 2. Clear and Cache Configuration
```bash
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache
```

### 3. Run Database Migrations
```bash
php artisan migrate --force
```

### 4. Set Up Queue Workers
```bash
# Start queue worker
php artisan queue:work --daemon

# Or use supervisor (recommended)
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start stock-tracker-worker:*
```

### 5. Optimize Assets
```bash
npm run build
```

### 6. Set File Permissions
```bash
sudo chown -R www-data:www-data /path/to/your/app
sudo chmod -R 755 /path/to/your/app
sudo chmod -R 775 /path/to/your/app/storage
sudo chmod -R 775 /path/to/your/app/bootstrap/cache
```

## 🛡️ Security Hardening

### 1. Server Security
- [ ] Install and configure firewall (UFW)
- [ ] Set up SSL/TLS certificates
- [ ] Configure secure SSH access
- [ ] Regular security updates

### 2. Application Security
- [ ] Enable HTTPS redirects
- [ ] Set secure headers
- [ ] Configure rate limiting
- [ ] Set up monitoring and logging

### 3. Database Security
- [ ] Use strong passwords
- [ ] Limit database access
- [ ] Regular backups
- [ ] Enable query logging

## 📊 Monitoring & Maintenance

### 1. Logging Configuration
```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'error'),
        'days' => 14,
    ],
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Stock Tracker Logger',
        'emoji' => ':boom:',
        'level' => env('LOG_LEVEL', 'critical'),
    ],
],
```

### 2. Health Checks
```bash
# Check application status
php artisan about

# Check queue status
php artisan queue:monitor

# Check cache status
php artisan cache:table
```

### 3. Backup Strategy
```bash
# Database backup
php artisan backup:run

# File backup
tar -czf backup-$(date +%Y%m%d).tar.gz /path/to/your/app
```

## 🚀 Deployment Platforms

### 1. VPS/Server Deployment
- [ ] Set up Nginx/Apache
- [ ] Configure PHP-FPM
- [ ] Set up SSL certificates
- [ ] Configure monitoring

### 2. Cloud Platform Deployment
- [ ] AWS, DigitalOcean, or similar
- [ ] Container deployment (Docker)
- [ ] Load balancer configuration
- [ ] Auto-scaling setup

### 3. Platform-as-a-Service
- [ ] Heroku, Railway, or similar
- [ ] Environment variable configuration
- [ ] Add-on services setup
- [ ] Domain configuration

## 🔄 Maintenance Tasks

### Daily
- [ ] Check application logs
- [ ] Monitor queue workers
- [ ] Verify backup completion

### Weekly
- [ ] Review performance metrics
- [ ] Update dependencies
- [ ] Check security advisories

### Monthly
- [ ] Full system backup
- [ ] Performance optimization
- [ ] Security audit

## 📞 Support & Troubleshooting

### Common Issues
1. **500 Errors**: Check logs in `storage/logs/`
2. **Queue Issues**: Restart queue workers
3. **Cache Issues**: Clear application cache
4. **Database Issues**: Check connection and permissions

### Emergency Procedures
1. **Application Down**: Check server status and logs
2. **Database Issues**: Restore from backup
3. **Security Breach**: Isolate and investigate

## 📈 Performance Optimization

### 1. Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_stock_product_id ON stock(product_id);
CREATE INDEX idx_stock_retailer_id ON stock(retailer_id);
CREATE INDEX idx_stock_in_stock ON stock(in_stock);
CREATE INDEX idx_price_alerts_product_id ON price_alerts(product_id);
CREATE INDEX idx_price_alerts_is_active ON price_alerts(is_active);
```

### 2. Caching Strategy
- [ ] Route caching
- [ ] Config caching
- [ ] View caching
- [ ] Database query caching
- [ ] API response caching

### 3. Asset Optimization
- [ ] Minify CSS/JS
- [ ] Compress images
- [ ] Enable gzip compression
- [ ] Use CDN for static assets

## 🔐 SSL Configuration

### Nginx SSL Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # Security headers
    add_header Strict-Transport-Security "max-age=63072000" always;
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    
    root /path/to/your/app/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 📊 Monitoring Setup

### 1. Application Monitoring
- [ ] Set up Laravel Telescope (development)
- [ ] Configure error tracking (Sentry)
- [ ] Set up uptime monitoring
- [ ] Performance monitoring

### 2. Server Monitoring
- [ ] CPU and memory usage
- [ ] Disk space monitoring
- [ ] Network traffic monitoring
- [ ] Log monitoring

### 3. Database Monitoring
- [ ] Query performance monitoring
- [ ] Connection pool monitoring
- [ ] Slow query logging
- [ ] Database size monitoring

---

**Remember**: Always test your deployment process in a staging environment first! 