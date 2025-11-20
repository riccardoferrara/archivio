#!/bin/bash

# Script per configurare la homepage con ID specifico
# Uso: ./set-homepage-id.sh [ID_PAGINA]

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="${MYSQL_PASSWORD:-root}"  # Usa MYSQL_PASSWORD se impostata, altrimenti "root"

HOME_ID="${1:-9954}"  # Usa ID 9954 di default (dalla produzione)

echo "=== Configurazione Homepage (ID: $HOME_ID) ==="
echo ""

# Verifica se la pagina esiste
PAGE_EXISTS=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE ID = $HOME_ID AND post_type = 'page' AND post_status = 'publish';" 2>&1 | grep -v "Warning" | tail -1)

if [ "$PAGE_EXISTS" -eq 0 ]; then
    echo "✗ Pagina con ID $HOME_ID non trovata o non pubblicata!"
    echo ""
    echo "Cercando pagine simili..."
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT ID, post_title, post_name, post_status FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY ID LIMIT 10;" 2>&1 | grep -v "Warning"
    echo ""
    echo "Usa: ./set-homepage-id.sh [ID_PAGINA] per configurare una pagina specifica"
    exit 1
fi

echo "1. Pagina trovata:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT ID, post_title, post_name, post_status FROM wp_posts WHERE ID = $HOME_ID;" 2>&1 | grep -v "Warning"
echo ""

echo "2. Configurazione homepage come pagina statica..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "UPDATE wp_options SET option_value = 'page' WHERE option_name = 'show_on_front'; UPDATE wp_options SET option_value = '$HOME_ID' WHERE option_name = 'page_on_front';" 2>&1 | grep -v "Warning"
echo "✓ Homepage configurata"
echo ""

echo "3. Verifica configurazione:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('show_on_front', 'page_on_front', 'page_for_posts');" 2>&1 | grep -v "Warning"
echo ""

echo "=== Completato ==="
echo "Ricarica la pagina http://localhost:8888 per vedere la homepage."
