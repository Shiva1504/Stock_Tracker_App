#!/bin/bash

# Railway build script for Stock Tracker App
set -e

echo "🚀 Starting Railway build..."

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm ci --only=production

# Build assets
echo "🎨 Building assets..."
npm run build

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate --force

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Cache configurations
echo "⚡ Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully!" 