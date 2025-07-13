#!/bin/bash

# Stock Tracker App Production Deployment Script
# This script handles the complete deployment process

set -e

# Configuration
APP_NAME="Stock Tracker App"
DEPLOYMENT_ENV="production"
BACKUP_BEFORE_DEPLOY=true

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

# Check if running as root
check_root() {
    if [[ $EUID -eq 0 ]]; then
        error "This script should not be run as root"
    fi
}

# Check prerequisites
check_prerequisites() {
    info "Checking prerequisites..."
    
    # Check if Docker is installed
    if ! command -v docker &> /dev/null; then
        error "Docker is not installed"
    fi
    
    # Check if Docker Compose is installed
    if ! command -v docker-compose &> /dev/null; then
        error "Docker Compose is not installed"
    fi
    
    # Check if .env file exists
    if [ ! -f ".env" ]; then
        error ".env file not found. Please create it from .env.example"
    fi
    
    success "Prerequisites check passed"
}

# Create backup
create_backup() {
    if [ "$BACKUP_BEFORE_DEPLOY" = true ]; then
        info "Creating backup before deployment..."
        
        # Create backup directory
        mkdir -p backups
        
        # Database backup
        if command -v mysqldump &> /dev/null; then
            source .env
            mysqldump -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" > "backups/pre_deploy_backup_$(date +%Y%m%d_%H%M%S).sql"
            success "Database backup created"
        else
            warning "mysqldump not available, skipping database backup"
        fi
        
        # Application files backup
        tar -czf "backups/pre_deploy_files_$(date +%Y%m%d_%H%M%S).tar.gz" \
            --exclude='node_modules' \
            --exclude='vendor' \
            --exclude='storage/logs' \
            --exclude='storage/framework/cache' \
            --exclude='storage/framework/sessions' \
            --exclude='storage/framework/views' \
            --exclude='.git' \
            .
        success "Application files backup created"
    fi
}

# Stop existing services
stop_services() {
    info "Stopping existing services..."
    docker-compose down --remove-orphans
    success "Services stopped"
}

# Build and start services
deploy_services() {
    info "Building and starting services..."
    
    # Pull latest images
    docker-compose pull
    
    # Build application
    docker-compose build --no-cache app
    
    # Start services
    docker-compose up -d
    
    success "Services deployed"
}

# Wait for services to be healthy
wait_for_health() {
    info "Waiting for services to be healthy..."
    
    # Wait for MySQL
    timeout=60
    while [ $timeout -gt 0 ]; do
        if docker-compose exec -T mysql mysqladmin ping -h localhost --silent; then
            success "MySQL is healthy"
            break
        fi
        sleep 2
        timeout=$((timeout - 2))
    done
    
    if [ $timeout -le 0 ]; then
        error "MySQL failed to become healthy"
    fi
    
    # Wait for Redis
    timeout=30
    while [ $timeout -gt 0 ]; do
        if docker-compose exec -T redis redis-cli ping &> /dev/null; then
            success "Redis is healthy"
            break
        fi
        sleep 2
        timeout=$((timeout - 2))
    done
    
    if [ $timeout -le 0 ]; then
        error "Redis failed to become healthy"
    fi
    
    # Wait for application
    timeout=60
    while [ $timeout -gt 0 ]; do
        if curl -f http://localhost/health &> /dev/null; then
            success "Application is healthy"
            break
        fi
        sleep 2
        timeout=$((timeout - 2))
    done
    
    if [ $timeout -le 0 ]; then
        error "Application failed to become healthy"
    fi
}

# Run database migrations
run_migrations() {
    info "Running database migrations..."
    docker-compose exec -T app php artisan migrate --force
    success "Database migrations completed"
}

# Optimize application
optimize_application() {
    info "Optimizing application..."
    
    # Clear caches
    docker-compose exec -T app php artisan config:clear
    docker-compose exec -T app php artisan route:clear
    docker-compose exec -T app php artisan view:clear
    docker-compose exec -T app php artisan cache:clear
    
    # Cache configurations
    docker-compose exec -T app php artisan config:cache
    docker-compose exec -T app php artisan route:cache
    docker-compose exec -T app php artisan view:cache
    
    success "Application optimized"
}

# Set up cron jobs
setup_cron() {
    info "Setting up cron jobs..."
    
    # Add cron job for price alert checking
    (crontab -l 2>/dev/null; echo "*/15 * * * * docker-compose exec -T app php artisan check:price-alerts") | crontab -
    
    # Add cron job for backups
    (crontab -l 2>/dev/null; echo "0 2 * * * docker-compose run --rm backup") | crontab -
    
    success "Cron jobs configured"
}

# Show deployment status
show_status() {
    info "Deployment Status:"
    echo ""
    docker-compose ps
    echo ""
    info "Application URL: http://localhost"
    info "Health Check: http://localhost/health"
    echo ""
    success "Deployment completed successfully!"
}

# Main deployment function
main() {
    echo -e "${BLUE}"
    echo "=========================================="
    echo "  $APP_NAME Production Deployment"
    echo "=========================================="
    echo -e "${NC}"
    
    check_root
    check_prerequisites
    create_backup
    stop_services
    deploy_services
    wait_for_health
    run_migrations
    optimize_application
    setup_cron
    show_status
}

# Handle script arguments
case "${1:-}" in
    --help|-h)
        echo "Usage: $0 [OPTIONS]"
        echo ""
        echo "Options:"
        echo "  --help, -h     Show this help message"
        echo "  --no-backup    Skip backup creation"
        echo ""
        echo "This script deploys the Stock Tracker App to production."
        exit 0
        ;;
    --no-backup)
        BACKUP_BEFORE_DEPLOY=false
        ;;
esac

# Run main function
main "$@" 