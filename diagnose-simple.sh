#!/bin/bash

# Diagnostica semplice senza caricare WordPress

cd /Volumes/Data/dev/archivio

echo "=== Diagnostica WordPress Lento (Semplice) ==="
echo ""

# 1. Verifica wp-config.php
echo "1. Configurazione:"
if [ -f wp-config.php ]; then
    echo "   ✓ wp-config.php trovato"
    
    # Verifica debug
    if grep -q "define.*WP_DEBUG.*true" wp-config.php; then
        echo "   ⚠️ WP_DEBUG è ATTIVO (rallenta!)"
    else
        echo "   ✓ WP_DEBUG disattivo"
    fi
    
    # Verifica cache
    if grep -q "define.*WP_CACHE.*true" wp-config.php; then
        echo "   ✓ Cache attiva"
    else
        echo "   ⚠️ Cache NON attiva"
    fi
else
    echo "   ✗ wp-config.php non trovato"
fi

echo ""

# 2. Verifica database (se wp-cli disponibile)
echo "2. Database:"
if command -v wp >/dev/null 2>&1; then
    export PATH="$HOME/bin:$PATH"
    
    # Transients scaduti
    EXPIRED=$(wp db query "SELECT COUNT(*) FROM wp_options WHERE option_name LIKE '_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()" --path="$(pwd)" --skip-column-names 2>/dev/null)
    if [ -n "$EXPIRED" ] && [ "$EXPIRED" -gt 0 ]; then
        echo "   ⚠️ Transients scaduti: $EXPIRED (rallentano!)"
    else
        echo "   ✓ Transients OK"
    fi
    
    # Opzioni autoload
    AUTOLOAD=$(wp db query "SELECT COUNT(*) FROM wp_options WHERE autoload = 'yes'" --path="$(pwd)" --skip-column-names 2>/dev/null)
    if [ -n "$AUTOLOAD" ] && [ "$AUTOLOAD" -gt 500 ]; then
        echo "   ⚠️ Opzioni autoload: $AUTOLOAD (troppe!)"
    else
        echo "   ✓ Opzioni autoload: $AUTOLOAD"
    fi
    
    # Revisioni
    REVISIONS=$(wp db query "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'revision'" --path="$(pwd)" --skip-column-names 2>/dev/null)
    if [ -n "$REVISIONS" ] && [ "$REVISIONS" -gt 1000 ]; then
        echo "   ⚠️ Revisioni post: $REVISIONS (troppe!)"
    else
        echo "   ✓ Revisioni: $REVISIONS"
    fi
else
    echo "   ⚠️ wp-cli non disponibile (installa per diagnostica completa)"
fi

echo ""

# 3. Plugin
echo "3. Plugin:"
if [ -f wp-content/plugins ]; then
    PLUGIN_COUNT=$(ls -1 wp-content/plugins 2>/dev/null | wc -l | tr -d ' ')
    echo "   Plugin installati: $PLUGIN_COUNT"
    if [ "$PLUGIN_COUNT" -gt 30 ]; then
        echo "   ⚠️ Troppi plugin installati"
    fi
fi

echo ""

# 4. Raccomandazioni
echo "=== RACCOMANDAZIONI ==="
echo ""
echo "1. Disattiva WP_DEBUG in wp-config.php:"
echo "   define('WP_DEBUG', false);"
echo ""
echo "2. Pulisci transients scaduti:"
echo "   wp transient delete --expired --path=\"\$(pwd)\""
echo ""
echo "3. Ottimizza database:"
echo "   make db-optimize"
echo ""
echo "4. Attiva cache (WP Super Cache)"
echo ""
echo "=== Fine Diagnostica ==="


