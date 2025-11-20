#!/bin/bash

# Script per importare database grande bypassando i limiti di PHP
# Questo script usa MySQL direttamente dalla riga di comando

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_FILE="backups/db/Swp1868342-prod.sql.local"

echo "=== Import Database Grande (bypass limiti PHP) ==="
echo ""

# Verifica file
if [ ! -f "$DB_FILE" ]; then
    echo "✗ File non trovato: $DB_FILE"
    exit 1
fi

FILE_SIZE=$(du -h "$DB_FILE" | cut -f1)
echo "File: $DB_FILE ($FILE_SIZE)"
echo ""

# Prova a trovare la password corretta
echo "Tentativo connessione al database..."
DB_PASS=""

# Prova password vuota
if $MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -e "SELECT 1;" >/dev/null 2>&1; then
    DB_PASS=""
    echo "✓ Connesso senza password"
# Prova password 'root'
elif $MYSQL_CMD -u"$DB_USER" -proot -h"localhost" "$DB_NAME" -e "SELECT 1;" >/dev/null 2>&1; then
    DB_PASS="root"
    echo "✓ Connesso con password 'root'"
else
    echo "Inserisci la password MySQL per root:"
    read -s MYSQL_PASSWORD
    DB_PASS="$MYSQL_PASSWORD"
    if ! $MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT 1;" >/dev/null 2>&1; then
        echo "✗ Password errata!"
        exit 1
    fi
    echo "✓ Connesso"
fi

echo ""
echo "⚠️ ATTENZIONE: Questo sovrascriverà tutti i dati nel database $DB_NAME"
echo "Vuoi continuare? (s/n)"
read -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo "Operazione annullata"
    exit 0
fi

# Crea backup del database attuale (se non vuoto)
echo ""
echo "Creazione backup database attuale..."
BACKUP_FILE="backups/db/backup_before_reimport_$(date +%Y%m%d_%H%M%S).sql"
if [ -z "$DB_PASS" ]; then
    /Applications/MAMP/Library/bin/mysqldump -u"$DB_USER" -h"localhost" "$DB_NAME" > "$BACKUP_FILE" 2>&1
else
    /Applications/MAMP/Library/bin/mysqldump -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" > "$BACKUP_FILE" 2>&1
fi
if [ -s "$BACKUP_FILE" ]; then
    echo "✓ Backup salvato: $BACKUP_FILE"
else
    echo "ℹ️ Database vuoto, nessun backup necessario"
    rm -f "$BACKUP_FILE"
fi

echo ""
echo "Svuotamento database..."
if [ -z "$DB_PASS" ]; then
    $MYSQL_CMD -u"$DB_USER" -h"localhost" -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1 | grep -v "Warning" || true
else
    $MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1 | grep -v "Warning" || true
fi
echo "✓ Database svuotato e ricreato"

echo ""
echo "Importazione in corso..."
echo "⚠️ Questo può richiedere 5-15 minuti per un file di 223MB"
echo "   Non chiudere il terminale durante l'importazione!"
echo ""

LOG_FILE="backups/db/import_large_$(date +%Y%m%d_%H%M%S).log"

if [ -z "$DB_PASS" ]; then
    $MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" \
        --max_allowed_packet=1G \
        --net_buffer_length=16384 \
        --force < "$DB_FILE" > "$LOG_FILE" 2>&1
else
    $MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" \
        --max_allowed_packet=1G \
        --net_buffer_length=16384 \
        --force < "$DB_FILE" > "$LOG_FILE" 2>&1
fi

# Verifica errori
ERROR_COUNT=$(grep -c "ERROR" "$LOG_FILE" 2>/dev/null || echo "0")
WARNING_COUNT=$(grep -c "Warning" "$LOG_FILE" 2>/dev/null || echo "0")

echo ""
echo "=== Verifica Importazione ==="
if [ -z "$DB_PASS" ]; then
    POSTS_COUNT=$($MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts;" 2>&1 | grep -v "Warning" | tail -1)
    PAGES_COUNT=$($MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'page';" 2>&1 | grep -v "Warning" | tail -1)
    TABLES_COUNT=$($MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -N -e "SHOW TABLES;" 2>&1 | grep -v "Warning" | wc -l | tr -d ' ')
else
    POSTS_COUNT=$($MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts;" 2>&1 | grep -v "Warning" | tail -1)
    PAGES_COUNT=$($MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'page';" 2>&1 | grep -v "Warning" | tail -1)
    TABLES_COUNT=$($MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SHOW TABLES;" 2>&1 | grep -v "Warning" | wc -l | tr -d ' ')
fi

echo "   Tabelle: $TABLES_COUNT"
echo "   Totale righe wp_posts: $POSTS_COUNT"
echo "   Pagine: $PAGES_COUNT"
echo "   Errori nel log: $ERROR_COUNT"
echo "   Warning nel log: $WARNING_COUNT"
echo ""

if [ "$ERROR_COUNT" -gt 0 ]; then
    echo "⚠️ ATTENZIONE: Trovati errori durante l'importazione!"
    echo "   Controlla il log: $LOG_FILE"
    echo "   (Gli errori minori sono normali, verifica che le pagine siano state importate)"
fi

if [ "$PAGES_COUNT" -gt 100 ]; then
    echo "✓ Importazione completata con successo!"
    echo ""
    echo "Ora esegui:"
    echo "  php verify-and-fix-pages.php"
else
    echo "⚠️ ATTENZIONE: Poche pagine importate!"
    echo "   Controlla il log: $LOG_FILE"
    echo "   Verifica che la password MySQL sia corretta"
fi

echo ""
echo "=== Completato ==="

