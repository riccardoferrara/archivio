#!/bin/bash

# Script completo per sostituire TUTTI gli URL di Elementor

MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_PASS="root"

echo "=== Sostituzione Completa URL Elementor ==="
echo ""

# Sostituisce tutte le varianti possibili
echo "1. Sostituzione URL con protocollo..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" << 'SQL'
UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'https://www.archiviowebsite.com', 'http://localhost:8888') 
WHERE meta_key LIKE '_elementor%';

UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'http://www.archiviowebsite.com', 'http://localhost:8888') 
WHERE meta_key LIKE '_elementor%';

UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'https://archiviowebsite.com', 'http://localhost:8888') 
WHERE meta_key LIKE '_elementor%';

UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'http://archiviowebsite.com', 'http://localhost:8888') 
WHERE meta_key LIKE '_elementor%';
SQL

echo "2. Sostituzione URL senza protocollo (con escape)..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" << 'SQL'
UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'archiviowebsite.com\/', 'localhost:8888\/') 
WHERE meta_key LIKE '_elementor%';

UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'www.archiviowebsite.com\/', 'localhost:8888\/') 
WHERE meta_key LIKE '_elementor%';
SQL

echo "3. Sostituzione URL senza protocollo (senza escape)..."
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" << 'SQL'
UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'archiviowebsite.com/', 'localhost:8888/') 
WHERE meta_key LIKE '_elementor%';

UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'www.archiviowebsite.com/', 'localhost:8888/') 
WHERE meta_key LIKE '_elementor%';
SQL

echo ""
echo "4. Verifica rimanenti..."
REMAINING=$($MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_postmeta WHERE meta_key LIKE '_elementor%' AND meta_value LIKE '%archiviowebsite%';" 2>&1 | grep -v "Warning" | tail -1)
echo "   Postmeta Elementor con URL vecchi rimasti: $REMAINING"

echo ""
echo "5. Pulizia cache..."
rm -rf wp-content/cache/* 2>/dev/null
$MYSQL_CMD -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" -e "DELETE FROM wp_options WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%';" 2>&1 | grep -v "Warning" >/dev/null

echo ""
echo "=== Completato ==="
echo ""
echo "Ora:"
echo "1. Vai su WP Admin → WP Fastest Cache → Delete Cache"
echo "2. Ricarica la pagina con Cmd+Shift+R"
echo ""

