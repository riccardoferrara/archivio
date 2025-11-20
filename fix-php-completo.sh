#!/bin/bash

# Script completo per fixare PHP e avviare server

set -e

cd /Volumes/Data/dev/archivio

echo "=== Fix PHP e Librerie ==="
echo ""

# 1. Verifica PHP di sistema
echo "1. Verifico PHP di sistema..."
if [ -x "/usr/bin/php" ]; then
    SYSTEM_PHP_VERSION=$(/usr/bin/php -v 2>&1 | head -1)
    echo "   ✓ PHP di sistema trovato: $SYSTEM_PHP_VERSION"
    USE_SYSTEM_PHP=true
else
    echo "   ⚠ PHP di sistema non trovato"
    USE_SYSTEM_PHP=false
fi

# 2. Verifica PHP Homebrew
echo ""
echo "2. Verifico PHP Homebrew..."
if command -v php >/dev/null 2>&1; then
    BREW_PHP=$(which php)
    echo "   ✓ PHP Homebrew trovato: $BREW_PHP"
    
    # Testa se funziona
    if php -v >/dev/null 2>&1; then
        echo "   ✓ PHP Homebrew funziona"
        USE_SYSTEM_PHP=false
    else
        echo "   ⚠ PHP Homebrew ha problemi, userò PHP di sistema"
        USE_SYSTEM_PHP=true
    fi
else
    echo "   ⚠ PHP Homebrew non trovato"
fi

# 3. Fix librerie se necessario
echo ""
echo "3. Verifico librerie..."
if [ "$USE_SYSTEM_PHP" = false ]; then
    # Prova a fixare libtiff
    if [ -d "/usr/local/Cellar/libtiff" ]; then
        LIBTIFF_VER=$(ls /usr/local/Cellar/libtiff/ | head -1)
        if [ -n "$LIBTIFF_VER" ]; then
            echo "   Trovato libtiff: $LIBTIFF_VER"
            if [ ! -L "/usr/local/opt/libtiff" ]; then
                echo "   Creo symlink..."
                ln -sf "/usr/local/Cellar/libtiff/$LIBTIFF_VER" /usr/local/opt/libtiff
            fi
        fi
    fi
fi

# 4. Scegli PHP da usare
echo ""
echo "4. Preparo server..."
if [ "$USE_SYSTEM_PHP" = true ]; then
    PHP_BIN="/usr/bin/php"
    echo "   Userò PHP di sistema: $PHP_BIN"
else
    PHP_BIN=$(which php)
    echo "   Userò PHP Homebrew: $PHP_BIN"
fi

# 5. Verifica wp-config.php
if [ ! -f "wp-config.php" ]; then
    echo ""
    echo "⚠ ATTENZIONE: wp-config.php non trovato"
    echo "   Configura WordPress prima di avviare il server"
    exit 1
fi

# 6. Avvia server
echo ""
echo "=== Avvio Server ==="
echo "Server: http://localhost:8000"
echo "Premi Ctrl+C per fermare"
echo ""

$PHP_BIN -S localhost:8000


