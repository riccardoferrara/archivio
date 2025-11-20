#!/bin/bash

# Script per importare il database di produzione in locale
# Uso: ./import-db-production.sh [file.sql|--download|--manual]
#      MYSQL_PASSWORD=tuapassword ./import-db-production.sh file.sql

set -e

WP_PATH="$(pwd)"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="${MYSQL_PASSWORD:-root}"  # Usa MYSQL_PASSWORD se impostata, altrimenti "root"
DB_HOST="localhost"

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

# Verifica MySQL MAMP
MYSQL_CMD=""
if [ -f /Applications/MAMP/Library/bin/mysql ]; then
    MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
elif command -v mysql >/dev/null 2>&1; then
    MYSQL_CMD=$(which mysql)
else
    log_error "MySQL non trovato. Assicurati che MAMP sia installato."
    exit 1
fi

# Verifica che siamo nella directory WordPress
if [ ! -f "$WP_PATH/wp-config.php" ]; then
    log_error "wp-config.php non trovato. Esegui lo script dalla directory WordPress."
    exit 1
fi

# Crea backup database locale prima dell'importazione
create_backup() {
    log_info "Creazione backup database locale..."
    BACKUP_DIR="backups/db"
    mkdir -p "$BACKUP_DIR"
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_FILE="$BACKUP_DIR/backup_local_before_import_$TIMESTAMP.sql"
    
    if [ -f /Applications/MAMP/Library/bin/mysqldump ]; then
        MYSQLDUMP_CMD="/Applications/MAMP/Library/bin/mysqldump"
    elif command -v mysqldump >/dev/null 2>&1; then
        MYSQLDUMP_CMD=$(which mysqldump)
    else
        log_warn "mysqldump non trovato, salto backup (non consigliato)"
        return
    fi
    
    $MYSQLDUMP_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null || {
        log_warn "Errore durante backup, continuo comunque..."
        return
    }
    
    log_info "✓ Backup creato: $BACKUP_FILE"
}

# Importa database e sostituisce URL
import_database() {
    local DB_FILE="$1"
    
    if [ ! -f "$DB_FILE" ]; then
        log_error "File database non trovato: $DB_FILE"
        exit 1
    fi
    
    log_info "Importazione database da: $DB_FILE"
    
    # Verifica/Crea database se non esiste
    log_info "Verifica esistenza database '$DB_NAME'..."
    DB_CHECK=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" -e "SHOW DATABASES LIKE '$DB_NAME';" 2>&1 | grep -v "Warning" | grep -E "^$DB_NAME$" | wc -l | tr -d ' ')
    
    if [ -z "$DB_CHECK" ] || [ "$DB_CHECK" -eq 0 ]; then
        log_info "Database '$DB_NAME' non trovato, creazione in corso..."
        $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1 | grep -v "Warning" || true
        log_info "✓ Database '$DB_NAME' creato"
    else
        log_info "✓ Database '$DB_NAME' già esistente"
    fi
    
    # Crea backup prima
    create_backup
    
    # Verifica versione MySQL
    MYSQL_VERSION=$($MYSQL_CMD --version 2>&1 | grep -oE "Ver [0-9]+\.[0-9]+" | grep -oE "[0-9]+\.[0-9]+" | head -1)
    MYSQL_MAJOR_VERSION=$(echo "$MYSQL_VERSION" | cut -d. -f1)
    
    log_info "Versione MySQL rilevata: $MYSQL_VERSION"
    
    # Sostituisce URL produzione → locale nel file SQL
    log_info "Sostituzione URL da produzione a locale..."
    TEMP_FILE="${DB_FILE}.local"
    
    # Base: sempre sostituisci URL e rimuovi CREATE DATABASE/USE
    SED_CMD="sed \"s|https://www.archiviowebsite.com|http://localhost:8888|g\" \"$DB_FILE\" | \
sed \"s|http://www.archiviowebsite.com|http://localhost:8888|g\" | \
sed \"s|http://archiviowebsite:8888|http://localhost:8888|g\" | \
sed \"/^CREATE DATABASE/d\" | \
sed \"/^USE /d\""
    
    # Se MySQL < 8.0, aggiungi conversioni collation e tipi numerici
    if [ "$MYSQL_MAJOR_VERSION" -lt 8 ]; then
        log_info "MySQL < 8.0 rilevato: applico conversioni collation e sintassi..."
        SED_CMD="$SED_CMD | \
sed \"s|utf8mb4_0900_ai_ci|utf8mb4_unicode_ci|g\" | \
sed \"s|utf8mb4_0900_as_ci|utf8mb4_unicode_ci|g\" | \
sed \"s|utf8mb4_0900_as_cs|utf8mb4_unicode_ci|g\" | \
sed \"s|bigint UNSIGNED|bigint(20) UNSIGNED|g\" | \
sed \"s|int UNSIGNED|int(11) UNSIGNED|g\" | \
sed \"s|mediumint UNSIGNED|mediumint(9) UNSIGNED|g\" | \
sed \"s|smallint UNSIGNED|smallint(6) UNSIGNED|g\" | \
sed \"s|tinyint UNSIGNED|tinyint(4) UNSIGNED|g\""
    else
        log_info "MySQL 8.0+ rilevato: sostituzione URL e rimozione CREATE DATABASE/USE"
    fi
    
    # Esegui comando sed
    eval "$SED_CMD > \"$TEMP_FILE\""
    
    # Importa database (ignora errori di tabelle già esistenti)
    log_info "Importazione in corso (può richiedere alcuni minuti)..."
    log_info "File temporaneo: $TEMP_FILE ($(du -h "$TEMP_FILE" | cut -f1))"
    
    # Salva errori in un file temporaneo per analisi
    ERROR_LOG="${TEMP_FILE}.errors"
    
    # Se MySQL < 8.0, filtra anche errori di sintassi ALTER TABLE
    if [ "$MYSQL_MAJOR_VERSION" -lt 8 ]; then
        IMPORT_OUTPUT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" --force < "$TEMP_FILE" 2>&1)
        echo "$IMPORT_OUTPUT" > "$ERROR_LOG"
        echo "$IMPORT_OUTPUT" | grep -v "Warning: Using a password" | grep -v "ERROR 1050" | grep -v "ERROR 1064.*MODIFY.*AUTO_INCREMENT" | head -20 || true
    else
        IMPORT_OUTPUT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" --force < "$TEMP_FILE" 2>&1)
        echo "$IMPORT_OUTPUT" > "$ERROR_LOG"
        # Mostra solo errori critici (non warning o errori di tabelle già esistenti)
        ERRORS=$(echo "$IMPORT_OUTPUT" | grep -v "Warning: Using a password" | grep -v "ERROR 1050" | grep "ERROR" || true)
        if [ -n "$ERRORS" ]; then
            log_warn "Errori durante importazione (primi 10):"
            echo "$ERRORS" | head -10
        fi
    fi
    
    # Verifica che l'importazione sia avvenuta
    TABLE_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" -N -e "SHOW TABLES;" 2>&1 | grep -v "Warning" | wc -l | tr -d ' ')
    if [ "$TABLE_COUNT" -lt 10 ]; then
        log_error "Importazione fallita: solo $TABLE_COUNT tabelle importate"
        log_error "File errori salvato in: $ERROR_LOG"
        log_error "Controlla il file errori per dettagli:"
        tail -30 "$ERROR_LOG" | grep -E "ERROR|FATAL" | head -10 || tail -20 "$ERROR_LOG"
        rm -f "$TEMP_FILE"
        exit 1
    fi
    log_info "✓ Importate $TABLE_COUNT tabelle"
    
    rm -f "$TEMP_FILE" "$ERROR_LOG"
    log_info "✓ Database importato con successo!"
    
    # Verifica importazione
    log_info "Verifica importazione..."
    IMG_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image%';" 2>&1 | grep -v "Warning: Using a password" | tail -1)
    log_info "✓ Immagini nel database: $IMG_COUNT"
}

# Download database via FTP (se disponibile)
download_database() {
    log_warn "=== Download Database via FTP ==="
    log_warn "Questa funzione richiede che il database sia già esportato sul server"
    log_warn "e accessibile via FTP."
    
    # Leggi configurazione FTP da sync-wp-ftp.sh
    if [ ! -f "sync-wp-ftp.sh" ]; then
        log_error "File sync-wp-ftp.sh non trovato. Configura manualmente le credenziali FTP."
        exit 1
    fi
    
    REMOTE_FTP_HOST=$(grep "^REMOTE_FTP_HOST=" sync-wp-ftp.sh | cut -d'"' -f2)
    REMOTE_FTP_USER=$(grep "^REMOTE_FTP_USER=" sync-wp-ftp.sh | cut -d'"' -f2)
    REMOTE_FTP_PASS=$(grep "^REMOTE_FTP_PASS=" sync-wp-ftp.sh | cut -d'"' -f2)
    REMOTE_FTP_PATH=$(grep "^REMOTE_FTP_PATH=" sync-wp-ftp.sh | cut -d'"' -f2)
    
    log_info "Cercando file database sul server FTP..."
    log_info "Percorsi comuni:"
    log_info "  - $REMOTE_FTP_PATH/backup*.sql"
    log_info "  - $REMOTE_FTP_PATH/backup*.sql.gz"
    log_info "  - $REMOTE_FTP_PATH/*.sql"
    
    read -p "Inserisci il percorso del file database sul server (es: /backup/database.sql): " REMOTE_DB_PATH
    
    if [ -z "$REMOTE_DB_PATH" ]; then
        log_error "Percorso non specificato"
        exit 1
    fi
    
    DB_FILE="backups/db/production_$(date +%Y%m%d_%H%M%S).sql"
    mkdir -p "$(dirname "$DB_FILE")"
    
    log_info "Download database da FTP..."
    if lftp -c "
        set ftp:list-options -a;
        set ssl:verify-certificate no;
        open -p 21 -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS $REMOTE_FTP_HOST;
        get $REMOTE_DB_PATH -o $DB_FILE;
    " 2>&1 | grep -v "Warning"; then
        log_info "✓ Database scaricato: $DB_FILE"
        import_database "$DB_FILE"
    else
        log_error "Errore durante il download"
        exit 1
    fi
}

# Main
case "$1" in
    --download)
        download_database
        ;;
    --manual)
        log_info "=== Importazione Manuale ==="
        log_info ""
        log_info "Opzioni disponibili:"
        log_info "  1. Esporta database da phpMyAdmin su Aruba"
        log_info "  2. Salva il file .sql"
        log_info "  3. Esegui: ./import-db-production.sh /path/to/database.sql"
        log_info ""
        log_info "Oppure:"
        log_info "  ./import-db-production.sh --download  (scarica via FTP se disponibile)"
        ;;
    "")
        if [ -z "$1" ] && [ -n "$2" ]; then
            import_database "$2"
        else
            log_error "Uso: $0 [file.sql|--download|--manual]"
            echo ""
            echo "Esempi:"
            echo "  $0 database.sql              Importa file SQL specificato"
            echo "  $0 --download                Scarica database via FTP"
            echo "  $0 --manual                  Mostra istruzioni per importazione manuale"
            exit 1
        fi
        ;;
    *)
        if [ -f "$1" ]; then
            import_database "$1"
        else
            log_error "File non trovato: $1"
            log_error "Uso: $0 [file.sql|--download|--manual]"
            exit 1
        fi
        ;;
esac

log_info ""
log_info "=== Completato ==="
log_info "Ora puoi accedere al sito su: http://localhost:8888"
log_info ""
log_warn "NOTA: Se vedi ancora problemi, verifica che:"
log_warn "  - Gli URL nel database siano stati sostituiti correttamente"
log_warn "  - I permessi dei file siano corretti"
log_warn "  - Il file wp-config.php abbia WP_HOME e WP_SITEURL configurati"

