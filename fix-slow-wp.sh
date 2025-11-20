#!/bin/bash

# Script per fixare WordPress lento localmente

cd /Volumes/Data/dev/archivio

echo "=== Fix WordPress Lento ==="
echo ""

# 1. Diagnostica
echo "1. Eseguo diagnostica..."
php diagnose-slow.php

echo ""
echo "2. Ottimizzazioni automatiche..."
echo ""

# 2. Pulisci transients scaduti
echo "   - Pulisco transients scaduti..."
export PATH="$HOME/bin:$PATH"
wp transient delete --expired --path="$(pwd)" 2>/dev/null || echo "     (richiede wp-cli)"

# 3. Ottimizza database
echo "   - Ottimizzo database..."
wp db optimize --path="$(pwd)" 2>/dev/null || echo "     (richiede wp-cli)"

# 4. Limita revisioni
echo "   - Limito revisioni post..."
wp config set WP_POST_REVISIONS 5 --path="$(pwd)" 2>/dev/null || echo "     (richiede wp-cli)"

# 5. Disattiva debug se attivo
echo "   - Verifico debug mode..."
if grep -q "define.*WP_DEBUG.*true" wp-config.php 2>/dev/null; then
    echo "     ⚠️ WP_DEBUG è attivo - disattivalo per produzione"
fi

echo ""
echo "=== Fix Completato ==="
echo ""
echo "Prossimi passi:"
echo "1. Disattiva plugin non necessari"
echo "2. Attiva cache (WP Super Cache)"
echo "3. Limita revisioni post"
echo "4. Pulisci opzioni autoload eccessive"


