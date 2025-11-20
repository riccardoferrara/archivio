#!/bin/bash

# Script per diagnosticare perché le pagine non si vedono
MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"

# Prova password
if [ -z "$MYSQL_PASSWORD" ]; then
    if $MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -e "SELECT 1;" >/dev/null 2>&1; then
        DB_PASS=""
    elif $MYSQL_CMD -u"$DB_USER" -proot -h"localhost" "$DB_NAME" -e "SELECT 1;" >/dev/null 2>&1; then
        DB_PASS="root"
    else
        echo "Inserisci la password MySQL:"
        read -s MYSQL_PASSWORD
        DB_PASS="$MYSQL_PASSWORD"
    fi
else
    DB_PASS="$MYSQL_PASSWORD"
fi

echo "=== DIAGNOSI PAGINE WORDPRESS ==="
echo ""

# 1. Conta pagine per status
echo "1. STATO PAGINE:"
$MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -e "
SELECT 
    post_status,
    COUNT(*) as count
FROM wp_posts 
WHERE post_type = 'page' 
GROUP BY post_status 
ORDER BY count DESC;
" 2>&1 | grep -v "Warning"
echo ""

# 2. Verifica homepage configurata
echo "2. CONFIGURAZIONE HOMEPAGE:"
$MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -e "
SELECT option_name, option_value 
FROM wp_options 
WHERE option_name IN ('show_on_front', 'page_on_front', 'page_for_posts', 'home', 'siteurl');
" 2>&1 | grep -v "Warning"
echo ""

# 3. Lista prime 20 pagine pubblicate
echo "3. PRIME 20 PAGINE PUBBLICATE:"
$MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -e "
SELECT 
    ID,
    post_title,
    post_name,
    post_status,
    post_parent,
    menu_order,
    post_date
FROM wp_posts 
WHERE post_type = 'page' 
AND post_status = 'publish'
ORDER BY menu_order, ID
LIMIT 20;
" 2>&1 | grep -v "Warning"
echo ""

# 4. Verifica permalink structure
echo "4. STRUTTURA PERMALINK:"
$MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -e "
SELECT option_value 
FROM wp_options 
WHERE option_name = 'permalink_structure';
" 2>&1 | grep -v "Warning"
echo ""

# 5. Verifica URL nel database
echo "5. URL CONFIGURATI:"
$MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -e "
SELECT 
    post_id,
    meta_key,
    meta_value
FROM wp_postmeta 
WHERE meta_key IN ('_wp_page_template', '_wp_attachment_url')
LIMIT 10;
" 2>&1 | grep -v "Warning"
echo ""

# 6. Controlla se ci sono pagine con URL di produzione
echo "6. VERIFICA URL PRODUZIONE NEL DATABASE:"
PROD_URLS=$($MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -N -e "
SELECT COUNT(*) 
FROM wp_posts 
WHERE post_content LIKE '%archiviowebsite.com%' 
OR guid LIKE '%archiviowebsite.com%';
" 2>&1 | grep -v "Warning" | tail -1)
echo "Righe con URL produzione: $PROD_URLS"
echo ""

# 7. Verifica rewrite rules
echo "7. REWRITE RULES (primi 3):"
$MYSQL_CMD -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h"localhost" "$DB_NAME" -e "
SELECT option_value 
FROM wp_options 
WHERE option_name = 'rewrite_rules'
LIMIT 1;
" 2>&1 | grep -v "Warning" | head -5
echo ""

echo "=== FINE DIAGNOSI ==="

