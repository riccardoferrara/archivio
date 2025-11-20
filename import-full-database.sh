#!/bin/bash

# Script per importare l'intero database
# Uso: MYSQL_PASSWORD=tua_password ./import-full-database.sh

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="${MYSQL_PASSWORD:-root}"
DB_FILE="backups/db/Swp1868342-prod.sql.local"

echo "=== Import Database Completo ==="
echo ""

# Verifica file
if [ ! -f "$DB_FILE" ]; then
    echo "✗ File non trovato: $DB_FILE"
    exit 1
fi

echo "File: $DB_FILE ($(du -h "$DB_FILE" | cut -f1))"
echo ""

# Importa con max_allowed_packet aumentato e timeout aumentato
echo "Importazione in corso (può richiedere 5-10 minuti)..."
echo ""

# Aumenta max_allowed_packet temporaneamente
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SET GLOBAL max_allowed_packet=1073741824;" 2>&1 | grep -v "Warning" || true

# Importa
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" \
    --max_allowed_packet=1G \
    --net_buffer_length=16384 \
    --force < "$DB_FILE" 2>&1 | tee "backups/db/import_full_$(date +%Y%m%d_%H%M%S).log" | \
    grep -v "Warning" | grep -E "ERROR|wp_posts|INSERT" | head -50

echo ""
echo "Verifica importazione:"
POSTS_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts;" 2>&1 | grep -v "Warning" | tail -1)
PAGES_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'page';" 2>&1 | grep -v "Warning" | tail -1)
TABLES_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SHOW TABLES;" 2>&1 | grep -v "Warning" | wc -l | tr -d ' ')

echo "   Tabelle: $TABLES_COUNT"
echo "   Totale righe wp_posts: $POSTS_COUNT"
echo "   Pagine: $PAGES_COUNT"
echo ""

if [ "$PAGES_COUNT" -lt 10 ]; then
    echo "⚠️ ATTENZIONE: Poche pagine importate!"
    echo "   Controlla il log per errori"
else
    echo "✓ Importazione completata"
fi

echo ""
echo "=== Completato ==="
