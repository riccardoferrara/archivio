#!/bin/bash

# Script per verificare i dati Elementor della homepage

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="root"
HOME_ID=9954

echo "=== Verifica Dati Elementor Homepage ==="
echo ""

echo "1. Dimensione _elementor_data:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT LENGTH(meta_value) as 'Size bytes' FROM wp_postmeta WHERE post_id = $HOME_ID AND meta_key = '_elementor_data';" 2>&1 | grep -v "Warning"

echo ""
echo "2. Immagini 2025/02 nei dati Elementor:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT COUNT(*) as 'Trovate' FROM wp_postmeta WHERE post_id = $HOME_ID AND meta_key = '_elementor_data' AND meta_value LIKE '%2025/02%';" 2>&1 | grep -v "Warning"

echo ""
echo "3. URL localhost nei dati Elementor:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT (LENGTH(meta_value) - LENGTH(REPLACE(meta_value, 'localhost:8888', ''))) / LENGTH('localhost:8888') as 'Occorrenze localhost' FROM wp_postmeta WHERE post_id = $HOME_ID AND meta_key = '_elementor_data';" 2>&1 | grep -v "Warning"

echo ""
echo "4. URL archiviowebsite nei dati Elementor:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT COUNT(*) as 'Trovati' FROM wp_postmeta WHERE post_id = $HOME_ID AND meta_key = '_elementor_data' AND meta_value LIKE '%archiviowebsite%';" 2>&1 | grep -v "Warning"

echo ""
echo "5. Esempi di immagini nei dati Elementor:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT meta_value FROM wp_postmeta WHERE post_id = $HOME_ID AND meta_key = '_elementor_data' LIMIT 1\G" 2>&1 | grep -oE "(1479540_M-copia|1422220_M|1422058_M|2025/02)" | head -5

echo ""
echo "6. Data ultima modifica homepage:"
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "SELECT post_modified FROM wp_posts WHERE ID = $HOME_ID;" 2>&1 | grep -v "Warning"

echo ""
echo "=== Fine Verifica ==="

