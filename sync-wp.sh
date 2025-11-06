#!/bin/bash

# Script per sincronizzare WordPress tra locale e produzione
# Uso: ./sync-wp.sh [pull|push] [--db-only|--files-only]

set -e

# ============================================
# CONFIGURAZIONE - MODIFICA QUESTI VALORI
# ============================================

# Configurazione server remoto (produzione)
# Per Aruba: solitamente il percorso è /home/username/public_html o /home/username/sito.com
REMOTE_HOST="tuo-server.com"
REMOTE_USER="tuo-utente"
REMOTE_WP_PATH="/home/tuo-utente/public_html"  # Percorso assoluto sul server remoto
REMOTE_SSH_KEY=""  # Lascia vuoto se usi password, altrimenti percorso alla chiave SSH
REMOTE_SSH_PORT="22"  # Porta SSH (Aruba usa solitamente 22, ma potrebbe essere diversa)

# Metodo di connessione: "ssh" (consigliato) o "ftp" (se SSH non disponibile)
CONNECTION_METHOD="ssh"

# Configurazione FTP (solo se CONNECTION_METHOD="ftp")
REMOTE_FTP_HOST="ftp.tuo-sito.com"
REMOTE_FTP_USER="tuo-utente"
REMOTE_FTP_PASS="tua-password"
REMOTE_FTP_PORT="21"

# Configurazione locale
LOCAL_WP_PATH="$(pwd)"
LOCAL_DB_NAME="local"
LOCAL_DB_USER="root"
LOCAL_DB_PASS="root"
LOCAL_DB_HOST="localhost"

# URL del sito
REMOTE_URL="https://tuo-sito.com"
LOCAL_URL="http://localhost"

# Directory da sincronizzare (relativo a wp-content)
SYNC_DIRS=("uploads" "themes" "plugins")

# ============================================
# NOTE PER ARUBA
# ============================================
# 1. Verifica che il tuo piano Aruba includa accesso SSH
# 2. Il percorso WordPress su Aruba è solitamente:
#    - /home/username/public_html (per dominio principale)
#    - /home/username/sito.com (per domini aggiuntivi)
# 3. Se wp-cli non è installato su Aruba, installalo con:
#    curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
#    chmod +x wp-cli.phar
#    mv wp-cli.phar ~/bin/wp
# 4. Se SSH non è disponibile, usa CONNECTION_METHOD="ftp"

# ============================================
# FUNZIONI
# ============================================

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verifica se wp-cli è installato
check_wp_cli() {
    if ! command -v wp &> /dev/null; then
        log_error "wp-cli non trovato. Installa wp-cli prima di continuare."
        exit 1
    fi
    log_info "wp-cli trovato: $(wp --version)"
}

# Costruisce il comando SSH
get_ssh_cmd() {
    SSH_OPTS="-p $REMOTE_SSH_PORT"
    if [ -n "$REMOTE_SSH_KEY" ]; then
        echo "ssh $SSH_OPTS -i $REMOTE_SSH_KEY $REMOTE_USER@$REMOTE_HOST"
    else
        echo "ssh $SSH_OPTS $REMOTE_USER@$REMOTE_HOST"
    fi
}

# Verifica connessione SSH
check_ssh_connection() {
    SSH_CMD=$(get_ssh_cmd)
    log_info "Verifica connessione SSH a $REMOTE_USER@$REMOTE_HOST..."
    
    if $SSH_CMD "echo 'SSH OK'" > /dev/null 2>&1; then
        log_info "Connessione SSH verificata!"
        return 0
    else
        log_error "Impossibile connettersi via SSH"
        log_warn "Verifica:"
        log_warn "  1. Che SSH sia abilitato sul tuo piano Aruba"
        log_warn "  2. Credenziali e hostname corretti"
        log_warn "  3. Che la porta SSH ($REMOTE_SSH_PORT) sia aperta"
        return 1
    fi
}

# Verifica wp-cli remoto
check_remote_wp_cli() {
    SSH_CMD=$(get_ssh_cmd)
    REMOTE_WP="cd $REMOTE_WP_PATH && wp"
    
    log_info "Verifica wp-cli sul server remoto..."
    
    if $SSH_CMD "$REMOTE_WP --info" > /dev/null 2>&1; then
        REMOTE_VERSION=$($SSH_CMD "$REMOTE_WP --version" 2>/dev/null || echo "versione sconosciuta")
        log_info "wp-cli remoto trovato: $REMOTE_VERSION"
        return 0
    else
        log_error "wp-cli non trovato sul server remoto"
        log_warn "Installa wp-cli su Aruba con:"
        log_warn "  curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar"
        log_warn "  chmod +x wp-cli.phar"
        log_warn "  mv wp-cli.phar ~/bin/wp"
        return 1
    fi
}

# Costruisce il comando wp-cli remoto
get_remote_wp() {
    SSH_CMD=$(get_ssh_cmd)
    echo "$SSH_CMD \"cd $REMOTE_WP_PATH && wp\""
}

# PULL: Scarica da remoto a locale
sync_pull() {
    log_info "=== PULL: Sincronizzazione da produzione a locale ==="
    
    if [ "$CONNECTION_METHOD" = "ssh" ]; then
        # Verifica connessione
        if ! check_ssh_connection; then
            exit 1
        fi
        
        if ! check_remote_wp_cli; then
            exit 1
        fi
    fi
    
    SSH_CMD=$(get_ssh_cmd)
    REMOTE_WP="cd $REMOTE_WP_PATH && wp"
    
    # Backup database locale
    log_info "Creazione backup database locale..."
    BACKUP_FILE="backup_local_$(date +%Y%m%d_%H%M%S).sql"
    wp db export "$BACKUP_FILE" --path="$LOCAL_WP_PATH" 2>/dev/null || true
    log_info "Backup salvato: $BACKUP_FILE"
    
    # 1. Database
    if [ "$SYNC_DB" = true ]; then
        log_info "Scaricamento database da remoto..."
        REMOTE_DB_FILE="backup_remote_$(date +%Y%m%d_%H%M%S).sql"
        
        # Esporta database remoto
        $SSH_CMD "$REMOTE_WP db export $REMOTE_DB_FILE" || {
            log_error "Errore nell'esportazione del database remoto"
            exit 1
        }
        
        # Scarica il file
        log_info "Download del database..."
        SCP_OPTS="-P $REMOTE_SSH_PORT"
        if [ -n "$REMOTE_SSH_KEY" ]; then
            SCP_OPTS="$SCP_OPTS -i $REMOTE_SSH_KEY"
        fi
        scp $SCP_OPTS \
            "$REMOTE_USER@$REMOTE_HOST:$REMOTE_WP_PATH/$REMOTE_DB_FILE" \
            "$REMOTE_DB_FILE"
        
        # Importa nel database locale
        log_info "Importazione database in locale..."
        wp db import "$REMOTE_DB_FILE" --path="$LOCAL_WP_PATH" || {
            log_error "Errore nell'importazione del database"
            exit 1
        }
        
        # Sostituisce gli URL
        log_info "Sostituzione URL da $REMOTE_URL a $LOCAL_URL..."
        wp search-replace "$REMOTE_URL" "$LOCAL_URL" --path="$LOCAL_WP_PATH" --all-tables --skip-columns=guid || true
        
        # Rimuove il file temporaneo remoto
        $SSH_CMD "rm $REMOTE_WP_PATH/$REMOTE_DB_FILE" || true
        
        log_info "Database sincronizzato!"
    fi
    
    # 2. File
    if [ "$SYNC_FILES" = true ]; then
        log_info "Sincronizzazione file da remoto..."
        
        for dir in "${SYNC_DIRS[@]}"; do
            log_info "Sincronizzazione wp-content/$dir..."
            RSYNC_SSH_OPTS="-e 'ssh -p $REMOTE_SSH_PORT"
            if [ -n "$REMOTE_SSH_KEY" ]; then
                RSYNC_SSH_OPTS="$RSYNC_SSH_OPTS -i $REMOTE_SSH_KEY"
            fi
            RSYNC_SSH_OPTS="$RSYNC_SSH_OPTS'"
            
            rsync -avz --delete \
                $(eval echo $RSYNC_SSH_OPTS) \
                "$REMOTE_USER@$REMOTE_HOST:$REMOTE_WP_PATH/wp-content/$dir/" \
                "$LOCAL_WP_PATH/wp-content/$dir/"
        done
        
        log_info "File sincronizzati!"
    fi
    
    log_info "=== PULL completato ==="
}

# PUSH: Carica da locale a remoto
sync_push() {
    log_info "=== PUSH: Sincronizzazione da locale a produzione ==="
    log_warn "ATTENZIONE: Stai per sovrascrivere i dati in produzione!"
    read -p "Sei sicuro? (scrivi 'yes' per confermare): " confirm
    
    if [ "$confirm" != "yes" ]; then
        log_info "Operazione annullata."
        exit 0
    fi
    
    if [ "$CONNECTION_METHOD" = "ssh" ]; then
        # Verifica connessione
        if ! check_ssh_connection; then
            exit 1
        fi
        
        if ! check_remote_wp_cli; then
            exit 1
        fi
    fi
    
    SSH_CMD=$(get_ssh_cmd)
    REMOTE_WP="cd $REMOTE_WP_PATH && wp"
    
    # Backup database remoto
    log_info "Creazione backup database remoto..."
    REMOTE_BACKUP="backup_remote_$(date +%Y%m%d_%H%M%S).sql"
    $SSH_CMD "$REMOTE_WP db export $REMOTE_BACKUP" || {
        log_error "Errore nel backup del database remoto"
        exit 1
    }
    log_info "Backup remoto salvato: $REMOTE_BACKUP"
    
    # 1. Database
    if [ "$SYNC_DB" = true ]; then
        log_info "Esportazione database locale..."
        LOCAL_DB_FILE="backup_local_$(date +%Y%m%d_%H%M%S).sql"
        wp db export "$LOCAL_DB_FILE" --path="$LOCAL_WP_PATH" || {
            log_error "Errore nell'esportazione del database locale"
            exit 1
        }
        
        # Sostituisce gli URL prima di caricare
        log_info "Sostituzione URL da $LOCAL_URL a $REMOTE_URL..."
        sed -i.bak "s|$LOCAL_URL|$REMOTE_URL|g" "$LOCAL_DB_FILE"
        rm -f "$LOCAL_DB_FILE.bak"
        
        # Carica il file
        log_info "Upload del database..."
        SCP_OPTS="-P $REMOTE_SSH_PORT"
        if [ -n "$REMOTE_SSH_KEY" ]; then
            SCP_OPTS="$SCP_OPTS -i $REMOTE_SSH_KEY"
        fi
        scp $SCP_OPTS \
            "$LOCAL_DB_FILE" \
            "$REMOTE_USER@$REMOTE_HOST:$REMOTE_WP_PATH/$LOCAL_DB_FILE"
        
        # Importa nel database remoto
        log_info "Importazione database in remoto..."
        $SSH_CMD "$REMOTE_WP db import $LOCAL_DB_FILE" || {
            log_error "Errore nell'importazione del database remoto"
            exit 1
        }
        
        # Rimuove il file temporaneo remoto
        $SSH_CMD "rm $REMOTE_WP_PATH/$LOCAL_DB_FILE" || true
        rm -f "$LOCAL_DB_FILE"
        
        log_info "Database sincronizzato!"
    fi
    
    # 2. File
    if [ "$SYNC_FILES" = true ]; then
        log_info "Sincronizzazione file verso remoto..."
        
        for dir in "${SYNC_DIRS[@]}"; do
            log_info "Sincronizzazione wp-content/$dir..."
            RSYNC_SSH_OPTS="-e 'ssh -p $REMOTE_SSH_PORT"
            if [ -n "$REMOTE_SSH_KEY" ]; then
                RSYNC_SSH_OPTS="$RSYNC_SSH_OPTS -i $REMOTE_SSH_KEY"
            fi
            RSYNC_SSH_OPTS="$RSYNC_SSH_OPTS'"
            
            rsync -avz --delete \
                $(eval echo $RSYNC_SSH_OPTS) \
                "$LOCAL_WP_PATH/wp-content/$dir/" \
                "$REMOTE_USER@$REMOTE_HOST:$REMOTE_WP_PATH/wp-content/$dir/"
        done
        
        log_info "File sincronizzati!"
    fi
    
    log_info "=== PUSH completato ==="
}

# ============================================
# MAIN
# ============================================

# Verifica parametri
if [ $# -eq 0 ]; then
    echo "Uso: $0 [pull|push] [--db-only|--files-only]"
    echo ""
    echo "Comandi:"
    echo "  pull        Scarica da produzione a locale"
    echo "  push        Carica da locale a produzione"
    echo ""
    echo "Opzioni:"
    echo "  --db-only   Sincronizza solo il database"
    echo "  --files-only Sincronizza solo i file"
    exit 1
fi

DIRECTION=$1
SYNC_DB=true
SYNC_FILES=true

# Gestione opzioni
if [ "$2" = "--db-only" ]; then
    SYNC_FILES=false
elif [ "$2" = "--files-only" ]; then
    SYNC_DB=false
fi

# Verifica wp-cli locale
check_wp_cli

# Mostra informazioni di configurazione
log_info "Configurazione:"
log_info "  Metodo connessione: $CONNECTION_METHOD"
log_info "  Server remoto: $REMOTE_USER@$REMOTE_HOST:$REMOTE_SSH_PORT"
log_info "  Percorso remoto: $REMOTE_WP_PATH"
log_info "  Percorso locale: $LOCAL_WP_PATH"

# Esegue la sincronizzazione
case $DIRECTION in
    pull)
        sync_pull
        ;;
    push)
        sync_push
        ;;
    *)
        log_error "Direzione non valida: $DIRECTION"
        echo "Usa 'pull' o 'push'"
        exit 1
        ;;
esac

