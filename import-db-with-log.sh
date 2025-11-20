#!/bin/bash

# Script per importare il database con logging
set -e

WP_PATH="$(pwd)"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="${MYSQL_PASSWORD:-root}"  # Usa MYSQL_PASSWORD se impostata, altrimenti "root"
DB_HOST="localhost"
DB_FILE="$1"
LOG_FILE="backups/db/import_log_$(date +%Y%m%d_%H%M%S).txt"

mkdir -p backups/db

exec > >(tee -a "$LOG_FILE")
exec 2>&1

echo "=== INIZIO IMPORT DATABASE ==="
echo "Data: $(date)"
echo "File: $DB_FILE"
echo ""

# Verifica MySQL MAMP
MYSQL_CMD=""
if [ -f /Applications/MAMP/Library/bin/mysql ]; then
    MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
elif command -v mysql >/dev/null 2>&1; then
    MYSQL_CMD=$(which mysql)
else
    echo "ERROR: MySQL non trovato"
    exit 1
fi

echo "MySQL trovato: $MYSQL_CMD"
echo ""

# Verifica file
if [ ! -f "$DB_FILE" ]; then
    echo "ERROR: File non trovato: $DB_FILE"
    exit 1
fi

FILE_SIZE=$(du -h "$DB_FILE" | cut -f1)
echo "Dimensione file: $FILE_SIZE"
echo ""

# Conta tabelle prima
echo "=== TABELLE PRIMA DELL'IMPORT ==="
TABLE_COUNT_BEFORE=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME';" 2>&1 | grep -v "Warning" | tail -1)
echo "Tabelle esistenti: $TABLE_COUNT_BEFORE"
echo ""

# Crea backup
echo "=== CREAZIONE BACKUP ==="
BACKUP_DIR="backups/db"
mkdir -p "$BACKUP_DIR"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/backup_local_before_import_$TIMESTAMP.sql"

if [ -f /Applications/MAMP/Library/bin/mysqldump ]; then
    MYSQLDUMP_CMD="/Applications/MAMP/Library/bin/mysqldump"
elif command -v mysqldump >/dev/null 2>&1; then
    MYSQLDUMP_CMD=$(which mysqldump)
fi

if [ -n "$MYSQLDUMP_CMD" ]; then
    echo "Creazione backup locale..."
    $MYSQLDUMP_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" > "$BACKUP_FILE" 2>&1 || echo "WARN: Backup fallito, continuo..."
    echo "Backup creato: $BACKUP_FILE"
else
    echo "WARN: mysqldump non trovato, salto backup"
fi
echo ""

# Verifica versione MySQL
echo "=== VERIFICA VERSIONE MYSQL ==="
MYSQL_VERSION=$($MYSQL_CMD --version 2>&1 | grep -oE "Ver [0-9]+\.[0-9]+" | grep -oE "[0-9]+\.[0-9]+" | head -1)
MYSQL_MAJOR_VERSION=$(echo "$MYSQL_VERSION" | cut -d. -f1)
echo "Versione MySQL: $MYSQL_VERSION"
echo ""

# Prepara file SQL
echo "=== PREPARAZIONE FILE SQL ==="
TEMP_FILE="${DB_FILE}.local"

# Base: sempre sostituisci URL
SED_CMD="sed \"s|https://www.archiviowebsite.com|http://localhost:8888|g\" \"$DB_FILE\" | \
sed \"s|http://www.archiviowebsite.com|http://localhost:8888|g\" | \
sed \"s|http://archiviowebsite:8888|http://localhost:8888|g\""

# Se MySQL < 8.0, aggiungi conversioni
if [ "$MYSQL_MAJOR_VERSION" -lt 8 ]; then
    echo "MySQL < 8.0: applico conversioni collation e sintassi..."
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
    echo "MySQL 8.0+: nessuna conversione necessaria (solo URL)"
fi

eval "$SED_CMD > \"$TEMP_FILE\""

TEMP_SIZE=$(du -h "$TEMP_FILE" | cut -f1)
echo "File temporaneo creato: $TEMP_FILE ($TEMP_SIZE)"
echo ""

# Importa database
echo "=== IMPORT DATABASE ==="
echo "Questo può richiedere diversi minuti..."
echo "Inizio: $(date)"

# Se MySQL < 8.0, filtra anche errori di sintassi ALTER TABLE
if [ "$MYSQL_MAJOR_VERSION" -lt 8 ]; then
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" --force < "$TEMP_FILE" 2>&1 | grep -v "Warning: Using a password" | grep -v "ERROR 1050" | grep -v "ERROR 1064.*MODIFY.*AUTO_INCREMENT" || true
else
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" --force < "$TEMP_FILE" 2>&1 | grep -v "Warning: Using a password" | grep -v "ERROR 1050" || true
fi

echo "Fine: $(date)"
echo ""

# Verifica importazione
echo "=== VERIFICA IMPORT ==="
TABLE_COUNT_AFTER=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME';" 2>&1 | grep -v "Warning" | tail -1)
echo "Tabelle dopo import: $TABLE_COUNT_AFTER"

if [ "$TABLE_COUNT_AFTER" -lt 10 ]; then
    echo "ERROR: Importazione fallita: solo $TABLE_COUNT_AFTER tabelle importate"
    rm -f "$TEMP_FILE"
    exit 1
fi

# Conta immagini
IMG_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"$DB_HOST" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image%';" 2>&1 | grep -v "Warning" | tail -1)
echo "Immagini nel database: $IMG_COUNT"
echo ""

# Pulisci file temporaneo
rm -f "$TEMP_FILE"
echo "File temporaneo rimosso"
echo ""

echo "=== IMPORT COMPLETATO ==="
echo "Log salvato in: $LOG_FILE"
echo ""

