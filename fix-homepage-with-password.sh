#!/bin/bash

# Script per configurare la homepage con password MySQL
# Uso: MYSQL_PASSWORD=tua_password ./fix-homepage-with-password.sh
#   oppure: ./fix-homepage-with-password.sh (chiederà la password)

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"

# Usa password da variabile d'ambiente o chiedila
if [ -z "$MYSQL_PASSWORD" ]; then
    echo "Inserisci la password MySQL per root:"
    read -s MYSQL_PASSWORD
    echo ""
fi

DB_PASS="$MYSQL_PASSWORD"

echo "=== Configurazione Homepage WordPress ==="
echo ""

# 1. Verifica configurazione attuale
echo "1. Configurazione attuale homepage:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('show_on_front', 'page_on_front', 'page_for_posts');" 2>&1 | grep -v "Warning"
echo ""

# 2. Cerca pagine che potrebbero essere la homepage
echo "2. Pagine candidate per homepage (con 'home' nel nome o slug):"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT ID, post_title, post_name, post_status FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish' AND (post_name LIKE '%home%' OR post_title LIKE '%Home%' OR post_title LIKE '%home%' OR post_title LIKE '%Homepage%') ORDER BY post_date DESC LIMIT 5;" 2>&1 | grep -v "Warning"
echo ""

# 3. Cerca la pagina con slug 'home' o ID più basso (spesso è la homepage)
HOME_ID=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT ID FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish' AND post_name = 'home' LIMIT 1;" 2>&1 | grep -v "Warning" | tail -1)

if [ -z "$HOME_ID" ] || [ "$HOME_ID" = "" ]; then
    # Prova a cercare per titolo
    HOME_ID=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT ID FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish' AND (post_title LIKE '%Home%' OR post_title LIKE '%home%') ORDER BY post_date ASC LIMIT 1;" 2>&1 | grep -v "Warning" | tail -1)
fi

if [ -z "$HOME_ID" ] || [ "$HOME_ID" = "" ]; then
    # Se non trova, usa la prima pagina pubblicata
    HOME_ID=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT ID FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_date ASC LIMIT 1;" 2>&1 | grep -v "Warning" | tail -1)
fi

if [ -n "$HOME_ID" ] && [ "$HOME_ID" != "" ]; then
    echo "3. Trovata pagina homepage (ID: $HOME_ID):"
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT ID, post_title, post_name FROM wp_posts WHERE ID = $HOME_ID;" 2>&1 | grep -v "Warning"
    echo ""
    
    echo "4. Configurazione homepage come pagina statica..."
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "UPDATE wp_options SET option_value = 'page' WHERE option_name = 'show_on_front'; UPDATE wp_options SET option_value = '$HOME_ID' WHERE option_name = 'page_on_front';" 2>&1 | grep -v "Warning"
    echo "✓ Homepage configurata"
    echo ""
    
    echo "5. Verifica configurazione aggiornata:"
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('show_on_front', 'page_on_front', 'page_for_posts');" 2>&1 | grep -v "Warning"
else
    echo "✗ Nessuna pagina trovata per la homepage!"
    echo ""
    echo "Pagine disponibili:"
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT ID, post_title, post_name FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 10;" 2>&1 | grep -v "Warning"
fi

echo ""
echo "=== Completato ==="
echo "Ricarica la pagina del sito per vedere la homepage."
