#!/bin/bash

# Script per fixare i permalink e pulire la cache
# Uso: ./fix-permalinks.sh

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="${MYSQL_PASSWORD:-root}"

echo "=== Fix Permalink e Cache ==="
echo ""

# 1. Verifica permalink attuale
echo "1. Permalink structure attuale:"
PERMALINK=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT option_value FROM wp_options WHERE option_name = 'permalink_structure';" 2>&1 | grep -v "Warning" | tail -1)
echo "   $PERMALINK"
echo ""

# 2. Imposta permalink se vuoto (usa struttura postname)
if [ -z "$PERMALINK" ] || [ "$PERMALINK" = "NULL" ] || [ "$PERMALINK" = "" ]; then
    echo "2. Permalink vuoto, imposto struttura '/%postname%/'..."
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "UPDATE wp_options SET option_value = '/%postname%/' WHERE option_name = 'permalink_structure';" 2>&1 | grep -v "Warning"
    echo "✓ Permalink configurato"
else
    echo "2. Permalink già configurato"
fi
echo ""

# 3. Pulisci cache file
echo "3. Pulizia cache file..."
rm -rf wp-content/cache/wpfc/* 2>/dev/null
rm -rf wp-content/cache/autoptimize/* 2>/dev/null
rm -rf wp-content/cache/* 2>/dev/null
echo "✓ Cache file pulita"
echo ""

# 4. Rimuovi transients
echo "4. Rimozione transients..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "DELETE FROM wp_options WHERE option_name LIKE '_transient_%'; DELETE FROM wp_options WHERE option_name LIKE '_site_transient_%';" 2>&1 | grep -v "Warning"
echo "✓ Transients rimossi"
echo ""

# 5. Verifica homepage
echo "5. Verifica homepage:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" -h"localhost" "$DB_NAME" -e "SELECT ID, post_title, post_status, post_type FROM wp_posts WHERE ID = 9954;" 2>&1 | grep -v "Warning"
echo ""

echo "=== Completato ==="
echo "Ora:"
echo "1. Vai su http://localhost:8888/wp-admin"
echo "2. Impostazioni → Permalink"
echo "3. Clicca 'Salva modifiche' (anche senza cambiare nulla)"
echo "4. Ricarica la homepage con Cmd+Shift+R"
