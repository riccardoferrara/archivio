#!/bin/bash

# Script per preparare il push rimuovendo le righe WP_HOME e WP_SITEURL da wp-config.php
# Uso: ./prepare-push.sh [--restore] [--check]

set -e

WP_CONFIG="wp-config.php"
BACKUP_FILE="wp-config.php.backup"
TEMP_FILE="wp-config.php.tmp"

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

# Verifica che wp-config.php esista
if [ ! -f "$WP_CONFIG" ]; then
    log_error "File $WP_CONFIG non trovato!"
    exit 1
fi

# Funzione per rimuovere le righe WP_HOME e WP_SITEURL
remove_local_urls() {
    log_info "Rimozione righe WP_HOME e WP_SITEURL da $WP_CONFIG..."
    
    # Crea backup
    cp "$WP_CONFIG" "$BACKUP_FILE"
    log_info "Backup creato: $BACKUP_FILE"
    
    # Rimuovi le righe che contengono WP_HOME o WP_SITEURL (e i commenti associati)
    # Rimuove anche il commento sopra se inizia con "Forza URL locali"
    awk '
        /\/\*.*Forza URL locali/ { 
            skip=1 
            next 
        }
        skip && /define\(.*WP_HOME/ { 
            skip=0
            next 
        }
        skip && /define\(.*WP_SITEURL/ { 
            skip=0
            next 
        }
        skip && /^\s*\*\// { 
            skip=0
            next 
        }
        skip && /^\s*$/ && prev_was_comment { 
            skip=0
            next 
        }
        /define\(.*WP_HOME/ { 
            next 
        }
        /define\(.*WP_SITEURL/ { 
            next 
        }
        { 
            print
            prev_was_comment=0
        }
        /\/\*/ { prev_was_comment=1 }
    ' "$WP_CONFIG" > "$TEMP_FILE"
    
    # Verifica che il file non sia vuoto
    if [ ! -s "$TEMP_FILE" ]; then
        log_error "Errore: il file risultante è vuoto!"
        mv "$BACKUP_FILE" "$WP_CONFIG"
        rm -f "$TEMP_FILE"
        exit 1
    fi
    
    mv "$TEMP_FILE" "$WP_CONFIG"
    log_info "✓ Righe WP_HOME e WP_SITEURL rimosse"
}

# Funzione per ripristinare le righe (per sviluppo)
restore_local_urls() {
    if [ ! -f "$BACKUP_FILE" ]; then
        log_error "File backup non trovato: $BACKUP_FILE"
        log_warn "Non posso ripristinare. Le righe potrebbero essere già state rimosse."
        exit 1
    fi
    
    log_info "Ripristino righe WP_HOME e WP_SITEURL da backup..."
    mv "$BACKUP_FILE" "$WP_CONFIG"
    log_info "✓ Ripristinato da backup"
}

# Funzione per verificare lo stato
check_status() {
    if grep -q "define('WP_HOME'" "$WP_CONFIG" 2>/dev/null || grep -q 'define("WP_HOME"' "$WP_CONFIG" 2>/dev/null; then
        log_warn "⚠️  Righe WP_HOME/WP_SITEURL TROVATE in wp-config.php"
        log_warn "   Rimuovile prima del push online!"
        return 1
    else
        log_info "✓ Nessuna riga WP_HOME/WP_SITEURL trovata - OK per push"
        return 0
    fi
}

# Main
case "$1" in
    --restore)
        restore_local_urls
        ;;
    --check)
        check_status
        exit $?
        ;;
    "")
        remove_local_urls
        log_info ""
        log_warn "⚠️  IMPORTANTE: Prima di fare push, verifica che le righe siano state rimosse:"
        log_info "   ./prepare-push.sh --check"
        log_info ""
        log_info "Per ripristinare dopo il push (per sviluppo locale):"
        log_info "   ./prepare-push.sh --restore"
        ;;
    *)
        echo "Uso: $0 [--restore|--check]"
        echo ""
        echo "  (nessun argomento)  Rimuove WP_HOME/WP_SITEURL da wp-config.php"
        echo "  --restore            Ripristina le righe dal backup"
        echo "  --check              Verifica se le righe sono presenti"
        exit 1
        ;;
esac


