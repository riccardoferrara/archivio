#!/bin/bash

# Script per importare solo wp_posts dal file SQL
# Uso: MYSQL_PASSWORD=tua_password ./import-posts-only.sh

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="${MYSQL_PASSWORD:-root}"
DB_FILE="backups/db/Swp1868342-prod.sql.local"

echo "=== Import Solo wp_posts ==="
echo ""

# Estrai solo gli INSERT di wp_posts
echo "1. Estrazione INSERT wp_posts..."
TEMP_POSTS="backups/db/wp_posts_only.sql"

# Trova tutte le righe INSERT INTO wp_posts e le righe successive fino alla prossima INSERT o fine
grep -n "INSERT INTO.*wp_posts" "$DB_FILE" | while read line; do
    LINE_NUM=$(echo "$line" | cut -d: -f1)
    # Estrai dalla riga INSERT fino alla prossima INSERT o 1000 righe dopo
    tail -n +$LINE_NUM "$DB_FILE" | head -1000 | grep -v "^INSERT INTO" | head -1
done

# Metodo più semplice: estrai tutte le righe che contengono INSERT INTO wp_posts e le successive fino a una riga vuota o nuova INSERT
awk '/INSERT INTO.*wp_posts/,/^$/{print}' "$DB_FILE" | head -50000 > "$TEMP_POSTS" 2>/dev/null

# Alternativa: usa sed per estrarre tutto tra INSERT INTO wp_posts
sed -n '/INSERT INTO.*`wp_posts`/,/^);/p' "$DB_FILE" > "$TEMP_POSTS" 2>/dev/null

if [ ! -s "$TEMP_POSTS" ]; then
    echo "   ✗ Impossibile estrarre INSERT wp_posts"
    echo "   Provo metodo alternativo..."
    # Estrai manualmente le righe INSERT
    grep -A 10000 "INSERT INTO.*wp_posts" "$DB_FILE" | head -50000 > "$TEMP_POSTS"
fi

if [ -s "$TEMP_POSTS" ]; then
    echo "   ✓ File estratto: $TEMP_POSTS ($(wc -l < "$TEMP_POSTS" | tr -d ' ') righe, $(du -h "$TEMP_POSTS" | cut -f1))"
else
    echo "   ✗ File vuoto!"
    exit 1
fi

echo ""
echo "2. Import wp_posts..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" --force < "$TEMP_POSTS" 2>&1 | grep -v "Warning" | head -50

echo ""
echo "3. Verifica importazione:"
POSTS_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts;" 2>&1 | grep -v "Warning" | tail -1)
PAGES_COUNT=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'page';" 2>&1 | grep -v "Warning" | tail -1)
echo "   Totale righe wp_posts: $POSTS_COUNT"
echo "   Pagine: $PAGES_COUNT"
echo ""

echo "=== Completato ==="
