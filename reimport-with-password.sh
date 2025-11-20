#!/bin/bash

# Script per reimportare il database provando diverse password
MYSQL_CMD="/Applications/MAMP/Library/bin/mysql"
DB_NAME="archivio_local"
DB_USER="root"
DB_FILE="backups/db/Swp1868342-prod.sql.local"

echo "=== Reimport Database con Password Corretta ==="
echo ""

# Prova diverse password
PASSWORDS=("" "root" "password" "admin")

DB_PASS=""
for pwd in "${PASSWORDS[@]}"; do
    if [ -z "$pwd" ]; then
        if $MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -e "SELECT 1;" >/dev/null 2>&1; then
            DB_PASS=""
            echo "✓ Password trovata: (vuota)"
            break
        fi
    else
        if $MYSQL_CMD -u"$DB_USER" -p"$pwd" -h"localhost" "$DB_NAME" -e "SELECT 1;" >/dev/null 2>&1; then
            DB_PASS="$pwd"
            echo "✓ Password trovata: $pwd"
            break
        fi
    fi
done

if [ -z "$DB_PASS" ] && [ "$DB_PASS" != "0" ]; then
    echo "Inserisci la password MySQL per root:"
    read -s MYSQL_PASSWORD
    DB_PASS="$MYSQL_PASSWORD"
fi

echo ""
echo "File: $DB_FILE ($(du -h "$DB_FILE" | cut -f1))"
echo ""

# Svuota database prima (opzionale)
echo "Vuoi svuotare il database prima di reimportare? (s/n)"
read -n 1 -r
echo ""
if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo "Svuotamento database..."
    if [ -z "$DB_PASS" ]; then
        $MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS=0;" 2>&1 | grep -v "Warning" || true
        $MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1 | grep -v "Warning" || true
    else
        $MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS=0;" 2>&1 | grep -v "Warning" || true
        $MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1 | grep -v "Warning" || true
    fi
    echo "✓ Database svuotato"
fi

echo ""
echo "Importazione in corso (può richiedere 5-10 minuti)..."
echo ""

# Importa
if [ -z "$DB_PASS" ]; then
    $MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" \
        --max_allowed_packet=1G \
        --net_buffer_length=16384 \
        --force < "$DB_FILE" 2>&1 | tee "backups/db/reimport_$(date +%Y%m%d_%H%M%S).log" | \
        grep -v "Warning" | grep -E "ERROR|wp_posts|INSERT" | head -50
else
    $MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" \
        --max_allowed_packet=1G \
        --net_buffer_length=16384 \
        --force < "$DB_FILE" 2>&1 | tee "backups/db/reimport_$(date +%Y%m%d_%H%M%S).log" | \
        grep -v "Warning" | grep -E "ERROR|wp_posts|INSERT" | head -50
fi

echo ""
echo "Verifica importazione:"
if [ -z "$DB_PASS" ]; then
    POSTS_COUNT=$($MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts;" 2>&1 | grep -v "Warning" | tail -1)
    PAGES_COUNT=$($MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'page';" 2>&1 | grep -v "Warning" | tail -1)
    TABLES_COUNT=$($MYSQL_CMD -u"$DB_USER" -h"localhost" "$DB_NAME" -N -e "SHOW TABLES;" 2>&1 | grep -v "Warning" | wc -l | tr -d ' ')
else
    POSTS_COUNT=$($MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts;" 2>&1 | grep -v "Warning" | tail -1)
    PAGES_COUNT=$($MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'page';" 2>&1 | grep -v "Warning" | tail -1)
    TABLES_COUNT=$($MYSQL_CMD -u"$DB_USER" -p"$DB_PASS" -h"localhost" "$DB_NAME" -N -e "SHOW TABLES;" 2>&1 | grep -v "Warning" | wc -l | tr -d ' ')
fi

echo "   Tabelle: $TABLES_COUNT"
echo "   Totale righe wp_posts: $POSTS_COUNT"
echo "   Pagine: $PAGES_COUNT"
echo ""

if [ "$PAGES_COUNT" -gt 100 ]; then
    echo "✓ Importazione completata con successo!"
    echo ""
    echo "Ora esegui: php verify-and-fix-pages.php"
else
    echo "⚠️ ATTENZIONE: Poche pagine importate!"
    echo "   Controlla il log per errori"
fi

echo ""
echo "=== Completato ==="

