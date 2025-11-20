#!/bin/bash

# Script completo per verificare e fixare il database

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="root"

echo "=========================================="
echo "  VERIFICA E FIX DATABASE WORDPRESS"
echo "=========================================="
echo ""

# 1. Pulisce cache file
echo "[1/5] Pulizia cache file..."
rm -rf wp-content/cache/wpfc/* 2>/dev/null
rm -rf wp-content/cache/autoptimize/* 2>/dev/null
rm -rf wp-content/cache/* 2>/dev/null
echo "✓ Cache file pulita"
echo ""

# 2. Rimuove transients
echo "[2/5] Rimozione transients dal database..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" << 'SQL'
DELETE FROM wp_options WHERE option_name LIKE '_transient_%';
DELETE FROM wp_options WHERE option_name LIKE '_site_transient_%';
SELECT 'Transients eliminati' as Status;
SQL
echo "✓ Transients rimossi"
echo ""

# 3. Verifica database
echo "[3/5] Verifica database..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" << 'SQL'
SELECT 
    COUNT(*) as 'Immagini totali',
    MAX(post_date) as 'Data ultima immagine',
    COUNT(CASE WHEN guid LIKE '%2025%' THEN 1 END) as 'Immagini 2025'
FROM wp_posts 
WHERE post_type = 'attachment' 
AND post_mime_type LIKE 'image%';
SQL
echo ""

# 4. Verifica URL
echo "[4/5] Verifica URL configurati..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" << 'SQL'
SELECT 
    option_name,
    option_value 
FROM wp_options 
WHERE option_name IN ('home', 'siteurl')
LIMIT 2;
SQL
echo ""

# 5. Verifica homepage
echo "[5/5] Verifica homepage..."
HOME_ID=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -N -e "SELECT ID FROM wp_posts WHERE post_type = 'page' AND (post_name = 'home' OR post_title LIKE '%Home%') AND post_status = 'publish' LIMIT 1;" 2>&1 | grep -v "Warning" | tail -1)

if [ -n "$HOME_ID" ] && [ "$HOME_ID" != "" ]; then
    echo "   Homepage trovata (ID: $HOME_ID)"
    echo "   Verifica immagini nella homepage..."
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "
    SELECT meta_key, LEFT(meta_value, 100) as 'Valore'
    FROM wp_postmeta 
    WHERE post_id = $HOME_ID 
    AND (meta_key LIKE '%image%' OR meta_key LIKE '%img%' OR meta_key LIKE '%photo%')
    LIMIT 5;
    " 2>&1 | grep -v "Warning"
else
    echo "   Homepage non trovata con post_name='home'"
    echo "   Cercando pagina principale..."
    $MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "
    SELECT ID, post_title, post_name, post_date 
    FROM wp_posts 
    WHERE post_type = 'page' 
    AND post_status = 'publish' 
    ORDER BY menu_order, post_date DESC 
    LIMIT 3;
    " 2>&1 | grep -v "Warning"
fi

echo ""
echo "=========================================="
echo "  COMPLETATO"
echo "=========================================="
echo ""
echo "Ora:"
echo "1. Ricarica la pagina con Cmd+Shift+R (Mac) o Ctrl+F5"
echo "2. Se usi WP Fastest Cache, vai su WP Admin → WP Fastest Cache → Delete Cache"
echo "3. Verifica che le immagini siano aggiornate"
echo ""

