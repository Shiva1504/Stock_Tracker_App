#!/bin/bash

# Stock Tracker App Production Setup Script
# This script helps set up the production environment

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Log functions
log() {
    echo -e "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

info() {
    log "${BLUE}INFO: $1${NC}"
}

success() {
    log "${GREEN}SUCCESS: $1${NC}"
}

warning() {
    log "${YELLOW}WARNING: $1${NC}"
}

error() {
    log "${RED}ERROR: $1${NC}"
    exit 1
}

# Generate random password
generate_password() {
    openssl rand -base64 32 | tr -d "=+/" | cut -c1-25
}

# Create production environment file
create_env_file() {
    info "Creating production environment file..."
    
    if [ -f ".env" ]; then
        warning ".env file already exists. Creating backup..."
        cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    fi
    
    # Generate secure passwords
    APP_KEY=$(php artisan key:generate --show)
    DB_PASSWORD=$(generate_password)
    DB_ROOT_PASSWORD=$(generate_password)
    REDIS_PASSWORD=$(generate_password)
    
    cat > .env << EOF
# Production Environment Configuration
# Generated on $(date)

# Application Settings
APP_NAME="Stock Tracker App"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock_tracker_prod
DB_USERNAME=stock_user
DB_PASSWORD=${DB_PASSWORD}

# Cache & Session Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=${REDIS_PASSWORD}
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="\${APP_NAME}"

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

# Maintenance Mode
APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

# Performance
CACHE_TTL=3600
SESSION_LIFETIME=120

# Docker-specific variables
DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
EOF

    success "Production environment file created"
    warning "Please update the following values in .env:"
    warning "- APP_URL: Set to your actual domain"
    warning "- MAIL_*: Configure your email settings"
}

# Create SSL directory
create_ssl_directory() {
    info "Creating SSL directory..."
    mkdir -p deployment/ssl
    success "SSL directory created at deployment/ssl/"
    warning "Please add your SSL certificates:"
    warning "- deployment/ssl/certificate.crt"
    warning "- deployment/ssl/private.key"
}

# Create deployment directories
create_deployment_dirs() {
    info "Creating deployment directories..."
    mkdir -p deployment/mysql
    mkdir -p backups
    success "Deployment directories created"
}

# Show next steps
show_next_steps() {
    echo ""
    echo -e "${BLUE}=========================================="
    echo "  Production Setup Complete!"
    echo "=========================================="
    echo -e "${NC}"
    echo ""
    echo "Next steps:"
    echo ""
    echo "1. Update .env file with your actual values:"
    echo "   - Set APP_URL to your domain"
    echo "   - Configure email settings"
    echo ""
    echo "2. Add SSL certificates:"
    echo "   - deployment/ssl/certificate.crt"
    echo "   - deployment/ssl/private.key"
    echo ""
    echo "3. Deploy the application:"
    echo "   ./deploy.sh"
    echo ""
    echo "4. Monitor the deployment:"
    echo "   docker-compose ps"
    echo "   docker-compose logs -f"
    echo ""
    echo "5. Access your application:"
    echo "   http://localhost (or your domain)"
    echo ""
    success "Production setup completed successfully!"
}

# Main setup function
main() {
    echo -e "${BLUE}"
    echo "=========================================="
    echo "  Stock Tracker App Production Setup"
    echo "=========================================="
    echo -e "${NC}"
    
    create_env_file
    create_ssl_directory
    create_deployment_dirs
    show_next_steps
}

# Run main function
main "$@" 