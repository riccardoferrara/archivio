#!/bin/bash

# Script per risolvere problemi con librerie PHP su macOS

echo "=== Fix Librerie PHP ==="
echo ""

# Verifica se libtiff è installato
if ! brew list libtiff &>/dev/null; then
    echo "Installo libtiff..."
    brew install libtiff
fi

# Trova la versione installata di libtiff
LIBTIFF_VERSION=$(brew list --versions libtiff | awk '{print $2}' | head -1)
LIBTIFF_PATH="/usr/local/Cellar/libtiff/${LIBTIFF_VERSION}/lib"

if [ -d "$LIBTIFF_PATH" ]; then
    echo "Trovato libtiff versione: $LIBTIFF_VERSION"
    echo "Percorso: $LIBTIFF_PATH"
    
    # Crea symlink se non esiste
    if [ ! -L "/usr/local/opt/libtiff" ]; then
        echo "Creo symlink per libtiff..."
        ln -s "/usr/local/Cellar/libtiff/${LIBTIFF_VERSION}" /usr/local/opt/libtiff
    fi
    
    # Verifica se libtiff.5.dylib esiste
    if [ -f "${LIBTIFF_PATH}/libtiff.5.dylib" ]; then
        echo "✓ libtiff.5.dylib trovato"
    else
        # Cerca altre versioni
        DYLIB_FILE=$(find "$LIBTIFF_PATH" -name "libtiff*.dylib" | head -1)
        if [ -n "$DYLIB_FILE" ]; then
            echo "Trovato: $DYLIB_FILE"
            echo "Creo symlink libtiff.5.dylib..."
            ln -sf "$(basename $DYLIB_FILE)" "${LIBTIFF_PATH}/libtiff.5.dylib"
        fi
    fi
else
    echo "⚠ Percorso libtiff non trovato"
fi

echo ""
echo "Reinstallo PHP e dipendenze..."
brew reinstall libtiff gd php

echo ""
echo "=== Fix Completato ==="
echo "Prova ora: make server"


