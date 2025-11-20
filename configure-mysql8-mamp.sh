#!/bin/bash

# Script per configurare MySQL 8.0 in MAMP
# Uso: ./configure-mysql8-mamp.sh

set -e

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

MAMP_BIN="/Applications/MAMP/Library/bin"
MAMP_BACKUP="$MAMP_BIN/mysql_backup_5.7_$(date +%Y%m%d_%H%M%S)"

log_info "=== Configurazione MySQL 8.0 per MAMP ==="
echo ""

# 1. Trova MySQL 8.0
log_info "Cercando MySQL 8.0 installato..."

MYSQL8_PATH=""

# Prova Homebrew (Apple Silicon)
if [ -d "/opt/homebrew/opt/mysql@8.0" ]; then
    MYSQL8_PATH="/opt/homebrew/opt/mysql@8.0/bin"
    log_info "✓ Trovato MySQL 8.0 in: $MYSQL8_PATH"
elif [ -d "/opt/homebrew/opt/mysql@8.1" ]; then
    MYSQL8_PATH="/opt/homebrew/opt/mysql@8.1/bin"
    log_info "✓ Trovato MySQL 8.1 in: $MYSQL8_PATH"
elif [ -d "/opt/homebrew/opt/mysql@8.2" ]; then
    MYSQL8_PATH="/opt/homebrew/opt/mysql@8.2/bin"
    log_info "✓ Trovato MySQL 8.2 in: $MYSQL8_PATH"
# Prova Homebrew (Intel)
elif [ -d "/usr/local/opt/mysql@8.0" ]; then
    MYSQL8_PATH="/usr/local/opt/mysql@8.0/bin"
    log_info "✓ Trovato MySQL 8.0 in: $MYSQL8_PATH"
elif [ -d "/usr/local/opt/mysql@8.1" ]; then
    MYSQL8_PATH="/usr/local/opt/mysql@8.1/bin"
    log_info "✓ Trovato MySQL 8.1 in: $MYSQL8_PATH"
# Prova installazione manuale
elif [ -d "/usr/local/mysql/bin" ]; then
    MYSQL8_PATH="/usr/local/mysql/bin"
    log_info "✓ Trovato MySQL in: $MYSQL8_PATH"
# Prova nel PATH
elif command -v mysql >/dev/null 2>&1; then
    MYSQL_PATH=$(which mysql)
    MYSQL_VERSION=$(mysql --version 2>&1 | grep -o "8\.[0-9]" | head -1)
    if [ -n "$MYSQL_VERSION" ]; then
        MYSQL8_PATH=$(dirname "$MYSQL_PATH")
        log_info "✓ Trovato MySQL 8.0 nel PATH: $MYSQL8_PATH"
    fi
fi

if [ -z "$MYSQL8_PATH" ]; then
    log_error "MySQL 8.0 non trovato!"
    echo ""
    log_warn "Opzioni:"
    log_warn "  1. Installa MySQL 8.0 via Homebrew:"
    log_warn "     brew install mysql@8.0"
    log_warn ""
    log_warn "  2. Oppure scarica da: https://dev.mysql.com/downloads/mysql/"
    exit 1
fi

# Verifica che mysql esista nel percorso
if [ ! -f "$MYSQL8_PATH/mysql" ]; then
    log_error "File mysql non trovato in: $MYSQL8_PATH"
    exit 1
fi

# Verifica versione
MYSQL_VERSION=$("$MYSQL8_PATH/mysql" --version 2>&1 | head -1)
log_info "Versione MySQL: $MYSQL_VERSION"
echo ""

# 2. Backup binari MAMP esistenti
log_info "Creazione backup binari MySQL MAMP..."
if [ -f "$MAMP_BIN/mysql" ]; then
    mkdir -p "$MAMP_BACKUP"
    cp "$MAMP_BIN"/mysql* "$MAMP_BACKUP/" 2>/dev/null || true
    log_info "✓ Backup creato in: $MAMP_BACKUP"
else
    log_warn "Nessun MySQL MAMP trovato da fare backup"
fi
echo ""

# 3. Crea symlink
log_info "Creazione symlink a MySQL 8.0..."

# Rimuovi symlink esistenti
rm -f "$MAMP_BIN/mysql" "$MAMP_BIN/mysqldump" "$MAMP_BIN/mysqladmin" "$MAMP_BIN/mysqld" 2>/dev/null || true

# Crea nuovi symlink
ln -sf "$MYSQL8_PATH/mysql" "$MAMP_BIN/mysql"
ln -sf "$MYSQL8_PATH/mysqldump" "$MAMP_BIN/mysqldump"
ln -sf "$MYSQL8_PATH/mysqladmin" "$MAMP_BIN/mysqladmin"
if [ -f "$MYSQL8_PATH/mysqld" ]; then
    ln -sf "$MYSQL8_PATH/mysqld" "$MAMP_BIN/mysqld"
fi

log_info "✓ Symlink creati"
echo ""

# 4. Verifica
log_info "Verifica configurazione..."
MAMP_MYSQL_VERSION=$("$MAMP_BIN/mysql" --version 2>&1 | head -1)
log_info "Versione MySQL MAMP: $MAMP_MYSQL_VERSION"

if echo "$MAMP_MYSQL_VERSION" | grep -q "8\.[0-9]"; then
    log_info "✓ MySQL 8.0 configurato correttamente!"
else
    log_warn "⚠ Versione MySQL non sembra essere 8.0"
    log_warn "Verifica manualmente: $MAMP_BIN/mysql --version"
fi
echo ""

log_info "=== Configurazione Completata ==="
log_warn ""
log_warn "IMPORTANTE:"
log_warn "  1. FERMA i server MAMP (Stop Servers)"
log_warn "  2. RIAVVIA i server MAMP (Start Servers)"
log_warn "  3. Verifica: $MAMP_BIN/mysql --version"
log_warn ""
log_info "Poi puoi importare il database:"
log_info "  ./import-db-production.sh backups/db/Swp1868342-prod.sql"

