#!/bin/bash

# Script per aggiornare WordPress core usando PHP MAMP
# Uso: ./update-wordpress-core.sh

set -e

WP_PATH="$(pwd)"
PHP_BIN="/Applications/MAMP/bin/php/php7.4.21/bin/php"

# Colori
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verifica che siamo nella directory WordPress
if [ ! -f "$WP_PATH/wp-config.php" ]; then
    log_error "wp-config.php non trovato. Esegui lo script dalla directory WordPress."
    exit 1
fi

# Verifica PHP MAMP
if [ ! -f "$PHP_BIN" ]; then
    log_error "PHP MAMP non trovato: $PHP_BIN"
    exit 1
fi

log_info "Aggiornamento WordPress core..."
log_info "Directory: $WP_PATH"
log_info "PHP: $PHP_BIN"

# Crea backup
BACKUP_DIR="backups/wp-core-$(date +%Y%m%d_%H%M%S)"
log_info "Creazione backup in $BACKUP_DIR..."
mkdir -p "$BACKUP_DIR"
cp -r wp-admin "$BACKUP_DIR/" 2>/dev/null || true
cp -r wp-includes "$BACKUP_DIR/" 2>/dev/null || true
cp -r wp-*.php "$BACKUP_DIR/" 2>/dev/null || true
cp -r xmlrpc.php "$BACKUP_DIR/" 2>/dev/null || true
log_info "✓ Backup creato"

# Usa WP-CLI se disponibile, altrimenti download manuale
if command -v wp >/dev/null 2>&1; then
    log_info "Usando WP-CLI per aggiornare WordPress..."
    $PHP_BIN $(which wp) core update --path="$WP_PATH" --allow-root || {
        log_warn "WP-CLI fallito, provo download manuale..."
        update_manual
    }
else
    log_warn "WP-CLI non trovato, uso metodo manuale..."
    update_manual
fi

log_info "✓ WordPress aggiornato!"
log_info "Verifica la versione: $PHP_BIN -r \"require '$WP_PATH/wp-load.php'; echo get_bloginfo('version');\""


