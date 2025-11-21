#!/bin/bash

# Script per sincronizzare WordPress tra locale e produzione usando FTP
# Uso: ./sync-wp-ftp.sh [pull|push] [--db-only|--files-only]
# 
# NOTA: Per il database, questo script richiede accesso a phpMyAdmin o 
# un metodo alternativo per esportare/importare il database

set -e

# ============================================
# CONFIGURAZIONE - MODIFICA QUESTI VALORI
# ============================================

# Configurazione FTP Aruba
# NOTA: Se ftp.archiviowebsite.com non funziona, prova:
# - archiviowebsite.com (senza ftp.)
# - ftp.aruba.it
# - L'IP del server (se fornito da Aruba)
REMOTE_FTP_HOST="ftp.archiviowebsite.com"  # o ftp.aruba.it
REMOTE_FTP_USER="9329510@aruba.it"
REMOTE_FTP_PASS="Archivio_2023"
REMOTE_FTP_PORT="21"
# Prova anche porta 22 per SFTP se FTP non funziona
# REMOTE_FTP_PORT="22"
REMOTE_FTP_PATH="/www.archiviowebsite.com"  # Percorso relativo dalla root FTP
# Se il percorso è diverso, verifica connettendoti con un client FTP grafico

# IMPORTANTE: Se ricevi errore "530 Errore critico: impossibile collegarsi al server"
# significa che il filtro accessi FTP è attivo. Vai su:
# https://admin.aruba.it → Gestione Hosting → Accesso FTP → Filtro Accessi
# e aggiungi il tuo IP pubblico o disattiva temporaneamente il filtro

# Configurazione locale
LOCAL_WP_PATH="$(pwd)"

# URL del sito
REMOTE_URL="https://www.archiviowebsite.com/"
LOCAL_URL="http://localhost"

# Directory da sincronizzare (relativo a wp-content)
SYNC_DIRS=("uploads" "themes" "plugins")

# Configurazione database (per esportazione/importazione manuale)
# Se hai accesso phpMyAdmin, usa queste informazioni
REMOTE_DB_HOST="localhost"
REMOTE_DB_NAME="nome-database"
REMOTE_DB_USER="username-db"
REMOTE_DB_PASS="password-db"

# ============================================
# FUNZIONI
# ============================================

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

log_note() {
    echo -e "${BLUE}[NOTE]${NC} $1"
}

# Verifica se lftp è installato
check_lftp() {
    if ! command -v lftp &> /dev/null; then
        log_error "lftp non trovato. Installa lftp:"
        log_info "  macOS: brew install lftp"
        log_info "  Linux: sudo apt-get install lftp"
        exit 1
    fi
    log_info "lftp trovato: $(lftp --version | head -1)"
}

# Verifica connessione FTP
check_ftp_connection() {
    log_info "Verifica connessione FTP a $REMOTE_FTP_HOST:$REMOTE_FTP_PORT..."
    
    # Prova connessione FTP normale
    if lftp -c "set ftp:list-options -a; set ssl:verify-certificate no; open -p $REMOTE_FTP_PORT -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS $REMOTE_FTP_HOST; ls" > /dev/null 2>&1; then
        log_info "Connessione FTP verificata!"
        return 0
    else
        log_warn "Tentativo connessione FTP fallito, provo SFTP..."
        
        # Prova SFTP se FTP non funziona
        if [ "$REMOTE_FTP_PORT" = "21" ]; then
            log_info "Tentativo con SFTP (porta 22)..."
            if lftp -c "set ssl:verify-certificate no; open -p 22 -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS sftp://$REMOTE_FTP_HOST; ls" > /dev/null 2>&1; then
                log_info "Connessione SFTP verificata! Aggiorna REMOTE_FTP_PORT a 22 nello script."
                return 0
            fi
        fi
        
        log_error "Impossibile connettersi via FTP/SFTP"
        log_warn ""
        log_warn "Possibili soluzioni:"
        log_warn "  1. Verifica credenziali FTP nel pannello Aruba (https://admin.aruba.it)"
        log_warn "  2. Prova hostname alternativi:"
        log_warn "     - archiviowebsite.com (senza ftp.)"
        log_warn "     - ftp.aruba.it"
        log_warn "     - L'IP del server (se fornito)"
        log_warn "  3. Verifica che il filtro accessi FTP sia configurato correttamente nel pannello Aruba"
        log_warn "  4. Prova a connetterti con un client FTP grafico (FileZilla, Cyberduck) per testare"
        log_warn "  5. Contatta il supporto Aruba se il problema persiste"
        log_warn ""
        log_warn "Per testare manualmente:"
        log_warn "  lftp -c \"open -p $REMOTE_FTP_PORT -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS $REMOTE_FTP_HOST; ls\""
        return 1
    fi
}

# PULL: Scarica da remoto a locale
sync_pull() {
    log_info "=== PULL: Sincronizzazione da produzione a locale (FTP) ==="
    
    if ! check_ftp_connection; then
        exit 1
    fi
    
    # 1. Database
    if [ "$SYNC_DB" = true ]; then
        log_warn "=== ATTENZIONE: Database ==="
        log_note "Con FTP non possiamo esportare il database automaticamente."
        log_note "Opzioni disponibili:"
        log_note "  1. Usa phpMyAdmin su Aruba per esportare il database"
        log_note "  2. Scarica manualmente il file .sql"
        log_note "  3. Usa un plugin WordPress come 'WP Migrate DB'"
        echo ""
        read -p "Hai già scaricato il database? (s/n): " db_ready
        
        if [ "$db_ready" = "s" ] || [ "$db_ready" = "S" ]; then
            read -p "Inserisci il percorso del file .sql: " db_file
            
            if [ -f "$db_file" ]; then
                log_info "Importazione database in locale..."
                wp db import "$db_file" --path="$LOCAL_WP_PATH" || {
                    log_error "Errore nell'importazione del database"
                    exit 1
                }
                
                # Sostituisce gli URL
                log_info "Sostituzione URL da $REMOTE_URL a $LOCAL_URL..."
                wp search-replace "$REMOTE_URL" "$LOCAL_URL" --path="$LOCAL_WP_PATH" --all-tables --skip-columns=guid || true
                log_info "Database importato!"
            else
                log_error "File non trovato: $db_file"
                exit 1
            fi
        else
            log_warn "Salta importazione database. Puoi farlo manualmente dopo."
        fi
    fi
    
    # 2. File
    if [ "$SYNC_FILES" = true ]; then
        log_info "Sincronizzazione file da remoto via FTP..."
        
        for dir in "${SYNC_DIRS[@]}"; do
            log_info "Sincronizzazione wp-content/$dir..."
            
            # Crea directory locale se non esiste
            mkdir -p "$LOCAL_WP_PATH/wp-content/$dir"
            
            # Usa lftp per scaricare i file
            # Ottimizzazioni: --parallel=3 (3 connessioni parallele), --only-newer (solo file nuovi/modificati)
            lftp -c "
                set ftp:list-options -a;
                set ssl:verify-certificate no;
                set net:max-retries 3;
                set net:timeout 30;
                set ftp:use-feat no;
                set ftp:use-mlsd no;
                open -p $REMOTE_FTP_PORT -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS $REMOTE_FTP_HOST;
                lcd $LOCAL_WP_PATH/wp-content/$dir;
                cd $REMOTE_FTP_PATH/wp-content/$dir;
                mirror --delete --verbose --only-newer --parallel=3 --exclude-glob .git* --exclude-glob .DS_Store --exclude-glob '*.log' --exclude-glob '*.tmp';
            " || {
                log_warn "Errore durante la sincronizzazione di wp-content/$dir"
                log_warn "Continuo con le altre directory..."
            }
        done
        
        log_info "File sincronizzati!"
    fi
    
    log_info "=== PULL completato ==="
}

# UPLOAD: Carica un singolo file da locale a remoto
upload_single_file() {
    local file_path="$1"
    
    if [ -z "$file_path" ]; then
        log_error "Percorso file non specificato"
        exit 1
    fi
    
    # Converti percorso relativo in assoluto
    if [ ! "${file_path:0:1}" = "/" ]; then
        file_path="$(pwd)/$file_path"
    fi
    
    # Verifica che il file esista
    if [ ! -f "$file_path" ]; then
        log_error "File non trovato: $file_path"
        exit 1
    fi
    
    # Calcola percorso relativo alla root di WordPress
    local wp_root="$LOCAL_WP_PATH"
    if [[ "$file_path" != "$wp_root"* ]]; then
        log_error "Il file deve essere all'interno della directory WordPress: $wp_root"
        exit 1
    fi
    
    # Percorso relativo dalla root WP
    local relative_path="${file_path#$wp_root/}"
    local remote_file_path="$REMOTE_FTP_PATH/$relative_path"
    local remote_dir=$(dirname "$remote_file_path")
    
    log_info "=== Upload singolo file (FTP) ==="
    log_info "File locale: $file_path"
    log_info "File remoto: $remote_file_path"
    
    if ! check_ftp_connection; then
        exit 1
    fi
    
    # Usa lftp per caricare il singolo file
    # lftp creerà automaticamente le directory necessarie
    lftp -c "
        set ftp:list-options -a;
        set ssl:verify-certificate no;
        set net:max-retries 3;
        set net:timeout 30;
        set ftp:use-feat no;
        set ftp:use-mlsd no;
        open -p $REMOTE_FTP_PORT -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS $REMOTE_FTP_HOST;
        cd $remote_dir 2>/dev/null || true;
        put -O $remote_dir $file_path;
        bye;
    " && log_info "✓ File caricato con successo!" || {
        log_error "Errore durante il caricamento del file"
        exit 1
    }
}

# PUSH: Carica da locale a remoto
sync_push() {
    log_info "=== PUSH: Sincronizzazione da locale a produzione (FTP) ==="
    log_warn "ATTENZIONE: Stai per sovrascrivere i dati in produzione!"
    read -p "Sei sicuro? (scrivi 'yes' per confermare): " confirm
    
    if [ "$confirm" != "yes" ]; then
        log_info "Operazione annullata."
        exit 0
    fi
    
    if ! check_ftp_connection; then
        exit 1
    fi
    
    # 1. Database
    if [ "$SYNC_DB" = true ]; then
        log_warn "=== ATTENZIONE: Database ==="
        log_note "Con FTP non possiamo importare il database automaticamente."
        log_note "Opzioni disponibili:"
        log_note "  1. Esporta il database locale:"
        log_note "     wp db export backup.sql --path=$LOCAL_WP_PATH"
        log_note "  2. Importa in phpMyAdmin su Aruba"
        log_note "  3. Usa un plugin WordPress come 'WP Migrate DB'"
        echo ""
        read -p "Vuoi esportare il database locale ora? (s/n): " export_db
        
        if [ "$export_db" = "s" ] || [ "$export_db" = "S" ]; then
            DB_FILE="backup_local_$(date +%Y%m%d_%H%M%S).sql"
            log_info "Esportazione database locale..."
            wp db export "$DB_FILE" --path="$LOCAL_WP_PATH" || {
                log_error "Errore nell'esportazione del database"
                exit 1
            }
            
            # Sostituisce gli URL
            log_info "Sostituzione URL da $LOCAL_URL a $REMOTE_URL..."
            sed -i.bak "s|$LOCAL_URL|$REMOTE_URL|g" "$DB_FILE"
            rm -f "$DB_FILE.bak"
            
            log_info "Database esportato: $DB_FILE"
            log_note "Ora importalo manualmente in phpMyAdmin su Aruba"
        fi
    fi
    
    # 2. File
    if [ "$SYNC_FILES" = true ]; then
        log_info "Sincronizzazione file verso remoto via FTP..."
        
        for dir in "${SYNC_DIRS[@]}"; do
            if [ ! -d "$LOCAL_WP_PATH/wp-content/$dir" ]; then
                log_warn "Directory locale non trovata: wp-content/$dir"
                continue
            fi
            
            log_info "Sincronizzazione wp-content/$dir..."
            
            # Usa lftp per caricare i file
            # Ottimizzazioni: --parallel=3 (3 connessioni parallele), --only-newer (solo file nuovi/modificati)
            lftp -c "
                set ftp:list-options -a;
                set ssl:verify-certificate no;
                set net:max-retries 3;
                set net:timeout 30;
                set ftp:use-feat no;
                set ftp:use-mlsd no;
                open -p $REMOTE_FTP_PORT -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS $REMOTE_FTP_HOST;
                lcd $LOCAL_WP_PATH/wp-content/$dir;
                cd $REMOTE_FTP_PATH/wp-content/$dir;
                mirror --reverse --delete --verbose --only-newer --parallel=3 --exclude-glob .git* --exclude-glob .DS_Store --exclude-glob '*.log' --exclude-glob '*.tmp';
            " || {
                log_warn "Errore durante la sincronizzazione di wp-content/$dir"
                log_warn "Continuo con le altre directory..."
            }
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
    echo "Uso: $0 [pull|push|upload-file] [--db-only|--files-only|FILE_PATH]"
    echo ""
    echo "Comandi:"
    echo "  pull              Scarica da produzione a locale (FTP)"
    echo "  push              Carica da locale a produzione (FTP)"
    echo "  upload-file FILE  Carica un singolo file su produzione (FTP)"
    echo ""
    echo "Opzioni:"
    echo "  --db-only         Sincronizza solo il database (richiede intervento manuale)"
    echo "  --files-only      Sincronizza solo i file"
    echo ""
    echo "Esempi:"
    echo "  $0 upload-file wp-content/themes/valeska-child-server/functions.php"
    echo "  $0 upload-file wp-content/plugins/my-plugin/my-plugin.php"
    echo ""
    echo "NOTA: Per il database, questo script richiede accesso a phpMyAdmin"
    echo "      o un metodo alternativo per esportare/importare il database."
    exit 1
fi

DIRECTION=$1

# Gestione upload singolo file
if [ "$DIRECTION" = "upload-file" ]; then
    if [ -z "$2" ]; then
        log_error "Specifica il percorso del file da caricare"
        echo "Esempio: $0 upload-file wp-content/themes/valeska-child-server/functions.php"
        exit 1
    fi
    check_lftp
    upload_single_file "$2"
    exit 0
fi

SYNC_DB=true
SYNC_FILES=true

# Gestione opzioni
if [ "$2" = "--db-only" ]; then
    SYNC_FILES=false
elif [ "$2" = "--files-only" ]; then
    SYNC_DB=false
fi

# Verifica lftp
check_lftp

# Mostra informazioni di configurazione
log_info "Configurazione:"
log_info "  Metodo connessione: FTP"
log_info "  Server FTP: $REMOTE_FTP_USER@$REMOTE_FTP_HOST:$REMOTE_FTP_PORT"
log_info "  Percorso remoto: $REMOTE_FTP_PATH"
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

