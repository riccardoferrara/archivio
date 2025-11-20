#!/bin/bash

# Fix immediato per WordPress lento (22 secondi)

cd /Volumes/Data/dev/archivio

echo "=== Fix Immediato WordPress Lento ==="
echo ""

export PATH="$HOME/bin:$PATH"

# 1. Pulisci transients scaduti
echo "1. Pulisco transients scaduti..."
wp transient delete --expired --path="$(pwd)" 2>/dev/null && echo "   ✓ Transients puliti" || echo "   ⚠️ Impossibile pulire (wp-cli non disponibile)"

# 2. Ottimizza database
echo ""
echo "2. Ottimizzo database..."
wp db optimize --path="$(pwd)" 2>/dev/null && echo "   ✓ Database ottimizzato" || echo "   ⚠️ Impossibile ottimizzare"

# 3. Limita revisioni
echo ""
echo "3. Limito revisioni post..."
if ! grep -q "WP_POST_REVISIONS" wp-config.php 2>/dev/null; then
    # Aggiungi dopo DB_COLLATE
    sed -i.bak '/DB_COLLATE/a\
\
/* Limita revisioni post per performance */\
define( '\''WP_POST_REVISIONS'\'', 5 );' wp-config.php
    echo "   ✓ Revisioni limitate a 5"
else
    echo "   ✓ Revisioni già limitate"
fi

# 4. Attiva cache object (veloce, senza plugin)
echo ""
echo "4. Attivo cache object..."
if ! grep -q "WP_CACHE" wp-config.php 2>/dev/null; then
    sed -i.bak2 '/DB_COLLATE/a\
\
/* Attiva cache object */\
define( '\''WP_CACHE'\'', true );' wp-config.php
    echo "   ✓ Cache object attivata"
else
    echo "   ✓ Cache già configurata"
fi

# 5. Disattiva autosave (locale)
echo ""
echo "5. Disattivo autosave (locale)..."
if ! grep -q "AUTOSAVE_INTERVAL" wp-config.php 2>/dev/null; then
    sed -i.bak3 '/DB_COLLATE/a\
\
/* Disattiva autosave per performance locale */\
define( '\''AUTOSAVE_INTERVAL'\'', 300 );' wp-config.php
    echo "   ✓ Autosave disattivato"
else
    echo "   ✓ Autosave già configurato"
fi

# 6. Pulisci opzioni autoload eccessive
echo ""
echo "6. Verifico opzioni autoload..."
AUTOLOAD_COUNT=$(wp db query "SELECT COUNT(*) FROM wp_options WHERE autoload = 'yes'" --path="$(pwd)" --skip-column-names 2>/dev/null)
if [ -n "$AUTOLOAD_COUNT" ] && [ "$AUTOLOAD_COUNT" -gt 500 ]; then
    echo "   ⚠️ Troppe opzioni autoload: $AUTOLOAD_COUNT"
    echo "   Pulisco transients da autoload..."
    wp db query "UPDATE wp_options SET autoload = 'no' WHERE option_name LIKE '_transient_%' AND autoload = 'yes'" --path="$(pwd)" 2>/dev/null && echo "   ✓ Transients rimossi da autoload"
else
    echo "   ✓ Opzioni autoload: $AUTOLOAD_COUNT (OK)"
fi

echo ""
echo "=== Fix Completato ==="
echo ""
echo "Prossimi passi:"
echo "1. Riavvia server: make server-stop && make server"
echo "2. Testa velocità: time curl -o /dev/null -s http://localhost:8000"
echo "3. Se ancora lento, installa plugin cache: WP Super Cache"
echo ""


