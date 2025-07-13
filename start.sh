#!/bin/bash

# Wait for the database to be ready
sleep 5

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache views
php artisan view:clear
php artisan view:cache

# Start the server
php artisan serve --host=0.0.0.0 --port=$PORT 