#!/bin/bash

# Script per testare l'importazione di una porzione del file SQL
# Uso: ./test-import-chunk.sh

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="${MYSQL_PASSWORD:-root}"
DB_FILE="backups/db/Swp1868342-prod.sql.local"

echo "=== Test Import Chunk ==="
echo ""

# Estrai solo i primi INSERT di wp_posts
echo "1. Estrazione primi INSERT wp_posts..."
TEMP_CHUNK="backups/db/test_chunk.sql"

# Trova la prima riga INSERT INTO wp_posts
FIRST_INSERT=$(grep -n "INSERT INTO.*wp_posts" "$DB_FILE" | head -1 | cut -d: -f1)
echo "   Prima INSERT trovata alla riga: $FIRST_INSERT"

# Estrai le prime 10 INSERT (circa 100-200 righe)
if [ -n "$FIRST_INSERT" ]; then
    tail -n +$FIRST_INSERT "$DB_FILE" | head -200 > "$TEMP_CHUNK"
    echo "   ✓ Chunk estratto: $TEMP_CHUNK ($(wc -l < "$TEMP_CHUNK" | tr -d ' ') righe)"
else
    echo "   ✗ Nessuna INSERT trovata!"
    exit 1
fi

echo ""
echo "2. Test import chunk..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" --force < "$TEMP_CHUNK" 2>&1 | head -30

echo ""
echo "3. Verifica righe importate:"
BEFORE=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts;" 2>&1 | grep -v "Warning" | tail -1)
echo "   Righe wp_posts: $BEFORE"
echo ""

echo "=== Completato ==="
echo "Se vedi errori sopra, controlla il formato degli INSERT"
