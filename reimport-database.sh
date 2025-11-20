#!/bin/bash

# Script per reimportare il database con logging dettagliato
# Uso: ./reimport-database.sh

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="${MYSQL_PASSWORD:-root}"
DB_FILE="backups/db/Swp1868342-prod.sql"
TEMP_FILE="${DB_FILE}.local"
LOG_FILE="backups/db/reimport_log_$(date +%Y%m%d_%H%M%S).txt"

echo "=== Reimport Database ==="
echo "Log salvato in: $LOG_FILE"
echo ""

# Verifica file
if [ ! -f "$DB_FILE" ]; then
    echo "✗ File non trovato: $DB_FILE"
    exit 1
fi

# Prepara file SQL (solo URL, niente conversioni con MySQL 8.0)
echo "1. Preparazione file SQL..."
sed "s|https://www.archiviowebsite.com|http://localhost:8888|g" "$DB_FILE" | \
sed "s|http://www.archiviowebsite.com|http://localhost:8888|g" | \
sed "s|http://archiviowebsite:8888|http://localhost:8888|g" | \
sed "/^CREATE DATABASE/d" | \
sed "/^USE /d" > "$TEMP_FILE" 2>&1 | tee -a "$LOG_FILE"

echo "✓ File preparato: $TEMP_FILE ($(du -h "$TEMP_FILE" | cut -f1))"
echo ""

# Svuota database (ATTENZIONE!)
echo "2. Svuotamento database..."
read -p "Vuoi svuotare il database prima di reimportare? (s/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo "Svuotamento in corso..."
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS=0; DROP TABLE IF EXISTS wp_posts, wp_postmeta, wp_options, wp_users, wp_usermeta;" 2>&1 | tee -a "$LOG_FILE"
    echo "✓ Database svuotato"
else
    echo "Saltato svuotamento"
fi
echo ""

# Importa con logging completo
echo "3. Importazione database (può richiedere diversi minuti)..."
echo "   Monitora il progresso in: $LOG_FILE"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" --force < "$TEMP_FILE" > "$LOG_FILE" 2>&1

# Conta errori
ERROR_COUNT=$(grep -c "ERROR" "$LOG_FILE" 2>/dev/null || echo "0")
WARNING_COUNT=$(grep -c "Warning" "$LOG_FILE" 2>/dev/null || echo "0")

echo ""
echo "4. Risultati importazione:"
echo "   Errori: $ERROR_COUNT"
echo "   Warning: $WARNING_COUNT"
echo ""

# Verifica importazione
echo "5. Verifica importazione:"
PAGES_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'page';" 2>&1 | grep -v "Warning" | tail -1)
POSTS_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'post';" 2>&1 | grep -v "Warning" | tail -1)
TABLES_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SHOW TABLES;" 2>&1 | grep -v "Warning" | wc -l | tr -d ' ')

echo "   Tabelle: $TABLES_COUNT"
echo "   Pagine: $PAGES_COUNT"
echo "   Post: $POSTS_COUNT"
echo ""

if [ "$PAGES_COUNT" -lt 10 ]; then
    echo "⚠️ ATTENZIONE: Poche pagine importate! Controlla il log: $LOG_FILE"
    echo "   Errori principali:"
    grep "ERROR" "$LOG_FILE" | head -10
else
    echo "✓ Importazione completata"
fi

echo ""
echo "=== Completato ==="
echo "Log completo: $LOG_FILE"
