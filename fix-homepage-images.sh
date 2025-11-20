#!/bin/bash

# Script per verificare e fixare le immagini nella homepage

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="root"

echo "=== Verifica e Fix Immagini Homepage ==="
echo ""

# 1. Pulisce cache
echo "1. Pulizia cache..."
rm -rf wp-content/cache/* 2>/dev/null
echo "✓ Cache pulita"

# 2. Rimuove transients
echo "2. Rimozione transients..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "
DELETE FROM wp_options WHERE option_name LIKE '_transient_%';
DELETE FROM wp_options WHERE option_name LIKE '_site_transient_%';
" 2>&1 | grep -v "Warning" >/dev/null
echo "✓ Transients rimossi"

# 3. Verifica homepage
echo "3. Verifica homepage..."
HOME_ID=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -N -e "SELECT ID FROM wp_posts WHERE post_type = 'page' AND post_name = 'home' OR post_title LIKE '%Home%' LIMIT 1;" 2>&1 | grep -v "Warning" | tail -1)

if [ -n "$HOME_ID" ] && [ "$HOME_ID" != "" ]; then
    echo "   Homepage ID: $HOME_ID"
    echo "   Contenuto homepage (prime 500 caratteri):"
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT LEFT(post_content, 500) FROM wp_posts WHERE ID = $HOME_ID;" 2>&1 | grep -v "Warning" | tail -1 | head -c 200
    echo "..."
else
    echo "   Homepage non trovata con post_name='home'"
    echo "   Cercando pagina principale..."
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT ID, post_title, post_name FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY menu_order LIMIT 5;" 2>&1 | grep -v "Warning"
fi

echo ""
echo "4. Verifica ultime immagini caricate:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT post_id, post_title, post_date FROM wp_posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image%' ORDER BY post_date DESC LIMIT 5;" 2>&1 | grep -v "Warning"

echo ""
echo "=== Completato ==="
echo ""
echo "Ora:"
echo "1. Ricarica la pagina con Ctrl+F5 (o Cmd+Shift+R su Mac)"
echo "2. Se usi WP Fastest Cache, vai su WP Admin → WP Fastest Cache → Delete Cache"
echo "3. Verifica che le immagini siano aggiornate"

