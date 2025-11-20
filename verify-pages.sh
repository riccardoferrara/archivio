#!/bin/bash

# Script per verificare le pagine importate
MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"

# Prova diverse password comuni
if [ -z "$MYSQL_PASSWORD" ]; then
    # Prova prima con password vuota (MAMP default a volte)
    if $MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -e "SELECT 1;" >/dev/null 2>&1; then
        DB_PASS=""
        echo "✓ Connesso senza password"
    # Prova con 'root'
    elif $MYSQL_CMD -u"$DB_USER" -proot -h"localhost" "$DB_NAME" -e "SELECT 1;" >/dev/null 2>&1; then
        DB_PASS="root"
        echo "✓ Connesso con password 'root'"
    else
        echo "Inserisci la password MySQL per root:"
        read -s MYSQL_PASSWORD
        DB_PASS="$MYSQL_PASSWORD"
    fi
else
    DB_PASS="$MYSQL_PASSWORD"
fi

echo ""
echo "=== Verifica Pagine Importate ==="
echo ""

# Conta totale pagine
TOTAL_PAGES=$($MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'page';" 2>&1 | grep -v "Warning" | tail -1)

# Conta pagine pubblicate
PUBLISHED_PAGES=$($MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish';" 2>&1 | grep -v "Warning" | tail -1)

# Conta per status
echo "Distribuzione per status:"
$MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -N -e "SELECT post_status, COUNT(*) as count FROM wp_posts WHERE post_type = 'page' GROUP BY post_status ORDER BY count DESC;" 2>&1 | grep -v "Warning"

echo ""
echo "Totale pagine: $TOTAL_PAGES"
echo "Pagine pubblicate: $PUBLISHED_PAGES"
echo ""

# Lista tutte le pagine con titolo
echo "=== Elenco Pagine (prime 30) ==="
$MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -e "SELECT ID, post_title, post_status, post_date FROM wp_posts WHERE post_type = 'page' ORDER BY ID LIMIT 30;" 2>&1 | grep -v "Warning"

echo ""
echo "=== Verifica nel file SQL originale ==="
# Analizza il file SQL per contare le pagine
echo "Analisi file SQL in corso..."
SQL_PAGES=$(python3 << 'PYEOF'
import re
count = 0
in_insert = False
with open('backups/db/Swp1868342-prod.sql.local', 'r', encoding='utf-8', errors='ignore') as f:
    for line in f:
        if 'INSERT INTO' in line and 'wp_posts' in line:
            in_insert = True
        if in_insert and re.search(r"'page'[,)]", line):
            count += 1
        if in_insert and line.strip().endswith(';'):
            in_insert = False
print(count)
PYEOF
)
echo "Pagine nel file SQL (stima): $SQL_PAGES"
echo ""
echo "=== Confronto ==="
if [ "$TOTAL_PAGES" -eq "$SQL_PAGES" ] || [ "$TOTAL_PAGES" -ge "$SQL_PAGES" ]; then
    echo "✓ Tutte le pagine sembrano essere state importate"
else
    echo "⚠️ Possibile discrepanza: $TOTAL_PAGES pagine importate vs ~$SQL_PAGES nel file SQL"
fi

