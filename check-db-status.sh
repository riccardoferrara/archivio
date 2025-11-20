#!/bin/bash

# Script per verificare lo stato del database

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="root"

echo "=== Verifica Database ==="
echo ""

echo "1. Data ultimo post:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT MAX(post_date) as 'Ultimo post' FROM wp_posts WHERE post_type IN ('post', 'page');" 2>&1 | grep -v "Warning"

echo ""
echo "2. Data ultima immagine:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT MAX(post_date) as 'Ultima immagine' FROM wp_posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image%';" 2>&1 | grep -v "Warning"

echo ""
echo "3. Totale immagini nel database:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT COUNT(*) as 'Totale' FROM wp_posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image%';" 2>&1 | grep -v "Warning"

echo ""
echo "4. Immagini 2025/02 nel database:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT COUNT(*) as 'Immagini 2025/02' FROM wp_posts WHERE post_type = 'attachment' AND guid LIKE '%2025/02%';" 2>&1 | grep -v "Warning"

echo ""
echo "5. Ultime 5 immagini caricate:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT post_id, post_title, post_date, guid FROM wp_posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image%' ORDER BY post_date DESC LIMIT 5;" 2>&1 | grep -v "Warning"

echo ""
echo "6. URL configurato:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT option_value FROM wp_options WHERE option_name = 'home';" 2>&1 | grep -v "Warning"

