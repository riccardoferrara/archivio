#!/bin/bash

# Script per attivare il tema corretto
# Uso: ./fix-active-theme.sh

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="root"

echo "=== Attivazione Tema WordPress ==="
echo ""

# Verifica tema attuale
echo "1. Tema attuale nel database:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('template', 'stylesheet');" 2>&1 | grep -v "Warning"
echo ""

# Verifica se valeska-child-server esiste
if [ -d "wp-content/themes/valeska-child-server" ]; then
    echo "2. Trovato tema 'valeska-child-server', attivazione..."
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "UPDATE wp_options SET option_value = 'valeska-child-server' WHERE option_name = 'template'; UPDATE wp_options SET option_value = 'valeska-child-server' WHERE option_name = 'stylesheet';" 2>&1 | grep -v "Warning"
    echo "✓ Tema 'valeska-child-server' attivato"
elif [ -d "wp-content/themes/valeska" ]; then
    echo "2. Trovato tema 'valeska', attivazione..."
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "UPDATE wp_options SET option_value = 'valeska' WHERE option_name = 'template'; UPDATE wp_options SET option_value = 'valeska' WHERE option_name = 'stylesheet';" 2>&1 | grep -v "Warning"
    echo "✓ Tema 'valeska' attivato"
else
    echo "✗ Nessun tema valeska trovato!"
    exit 1
fi

echo ""
echo "3. Verifica tema aggiornato:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('template', 'stylesheet');" 2>&1 | grep -v "Warning"
echo ""
echo "=== Completato ==="
echo "Ricarica la pagina del sito per vedere il tema aggiornato."
