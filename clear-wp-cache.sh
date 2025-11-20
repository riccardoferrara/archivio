#!/bin/bash

# Script per pulire la cache di WordPress

WP_PATH="$(pwd)"

echo "=== Pulizia Cache WordPress ==="
echo ""

# Rimuove cache WP Fastest Cache
if [ -d "wp-content/cache/wpfc" ]; then
    echo "Rimozione cache WP Fastest Cache..."
    rm -rf wp-content/cache/wpfc/*
    echo "✓ Cache WP Fastest Cache pulita"
fi

# Rimuove cache Autoptimize
if [ -d "wp-content/cache/autoptimize" ]; then
    echo "Rimozione cache Autoptimize..."
    rm -rf wp-content/cache/autoptimize/*
    echo "✓ Cache Autoptimize pulita"
fi

# Rimuove transients dal database
echo "Rimozione transients dal database..."
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "
DELETE FROM wp_options WHERE option_name LIKE '_transient_%';
DELETE FROM wp_options WHERE option_name LIKE '_site_transient_%';
" 2>&1 | grep -v "Warning" || echo "Transients rimossi"

echo ""
echo "✓ Cache pulita!"
echo ""
echo "Ricarica la pagina con Ctrl+F5 per vedere le modifiche"

