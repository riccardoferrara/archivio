#!/bin/bash

# Fix specifico per problema libtiff con PHP Homebrew

echo "=== Fix Librerie PHP (libtiff) ==="
echo ""

# Trova versione libtiff installata
LIBTIFF_PATH=$(find /usr/local/Cellar/libtiff -name "libtiff*.dylib" 2>/dev/null | head -1 | xargs dirname)

if [ -z "$LIBTIFF_PATH" ]; then
    echo "⚠ libtiff non trovato, installo..."
    brew install libtiff
    LIBTIFF_PATH=$(find /usr/local/Cellar/libtiff -name "libtiff*.dylib" 2>/dev/null | head -1 | xargs dirname)
fi

if [ -n "$LIBTIFF_PATH" ]; then
    echo "✓ Trovato libtiff in: $LIBTIFF_PATH"
    
    # Crea directory opt se non esiste
    mkdir -p /usr/local/opt/libtiff/lib
    
    # Trova file dylib
    DYLIB_FILE=$(find "$LIBTIFF_PATH" -name "libtiff*.dylib" | head -1)
    
    if [ -n "$DYLIB_FILE" ]; then
        echo "✓ Trovato: $DYLIB_FILE"
        
        # Crea symlink
        if [ ! -L "/usr/local/opt/libtiff/lib/libtiff.5.dylib" ]; then
            echo "Creo symlink..."
            ln -sf "$DYLIB_FILE" /usr/local/opt/libtiff/lib/libtiff.5.dylib
            echo "✓ Symlink creato"
        else
            echo "✓ Symlink già esistente"
        fi
    fi
fi

echo ""
echo "Test PHP..."
if php -v >/dev/null 2>&1; then
    echo "✓ PHP funziona!"
    php -v | head -1
else
    echo "⚠ PHP ancora non funziona"
    echo ""
    echo "Provo reinstallazione completa..."
    brew reinstall libtiff gd php
fi

echo ""
echo "=== Fix Completato ==="


