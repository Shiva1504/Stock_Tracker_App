#!/bin/sh

# Stock Tracker App Backup Script
# This script creates automated backups of the database and application files

set -e

# Configuration
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)
DB_BACKUP_FILE="db_backup_${DATE}.sql"
FILES_BACKUP_FILE="files_backup_${DATE}.tar.gz"
RETENTION_DAYS=30

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Log function
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Error function
error() {
    log "${RED}ERROR: $1${NC}"
    exit 1
}

# Success function
success() {
    log "${GREEN}SUCCESS: $1${NC}"
}

# Warning function
warning() {
    log "${YELLOW}WARNING: $1${NC}"
}

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

log "Starting backup process..."

# Database backup
log "Creating database backup..."
if mysqldump -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" > "$BACKUP_DIR/$DB_BACKUP_FILE"; then
    success "Database backup created: $DB_BACKUP_FILE"
    
    # Compress database backup
    gzip "$BACKUP_DIR/$DB_BACKUP_FILE"
    success "Database backup compressed: $DB_BACKUP_FILE.gz"
else
    error "Database backup failed"
fi

# Application files backup (if mounted)
if [ -d "/var/www/html" ]; then
    log "Creating application files backup..."
    if tar -czf "$BACKUP_DIR/$FILES_BACKUP_FILE" \
        --exclude='/var/www/html/node_modules' \
        --exclude='/var/www/html/vendor' \
        --exclude='/var/www/html/storage/logs' \
        --exclude='/var/www/html/storage/framework/cache' \
        --exclude='/var/www/html/storage/framework/sessions' \
        --exclude='/var/www/html/storage/framework/views' \
        --exclude='/var/www/html/.git' \
        -C /var/www/html .; then
        success "Application files backup created: $FILES_BACKUP_FILE"
    else
        warning "Application files backup failed"
    fi
fi

# Clean up old backups
log "Cleaning up old backups (older than $RETENTION_DAYS days)..."
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +$RETENTION_DAYS -delete
success "Old backups cleaned up"

# Show backup summary
log "Backup summary:"
ls -lh "$BACKUP_DIR" | grep "$DATE"

success "Backup process completed successfully!" 