#!/bin/bash

# Script per analizzare le performance del sito WordPress in produzione
# Uso: ./check-performance.sh

set -e

URL="https://www.archiviowebsite.com/"
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  Analisi Performance Sito WordPress                        ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

# 1. Test tempi di risposta
echo -e "${GREEN}1. Test Tempi di Risposta${NC}"
echo "----------------------------------------"
for i in {1..3}; do
    echo -n "Test $i: "
    curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s | Totale: %{time_total}s | Dimensione: %{size_download} bytes\n" \
        "$URL" || echo "Errore di connessione"
done
echo ""

# 2. Analisi header HTTP
echo -e "${GREEN}2. Header HTTP e Cache${NC}"
echo "----------------------------------------"
HEADERS=$(curl -s -I "$URL")
echo "$HEADERS" | grep -E "(HTTP|Server|X-Powered-By|Cache-Control|X-Cache|Vary|Expires)" || echo "Nessun header di cache trovato"
echo ""

# 3. Analisi dimensioni pagina
echo -e "${GREEN}3. Analisi Dimensioni Pagina${NC}"
echo "----------------------------------------"
HTML_SIZE=$(curl -s "$URL" | wc -c)
echo "Dimensione HTML: $(numfmt --to=iec-i --suffix=B $HTML_SIZE)"
echo ""

# 4. Conta risorse esterne
echo -e "${GREEN}4. Risorse Caricate${NC}"
echo "----------------------------------------"
HTML=$(curl -s "$URL")
JS_COUNT=$(echo "$HTML" | grep -o '<script[^>]*src="[^"]*"' | wc -l | tr -d ' ')
CSS_COUNT=$(echo "$HTML" | grep -o '<link[^>]*rel="stylesheet"[^>]*>' | wc -l | tr -d ' ')
IMG_COUNT=$(echo "$HTML" | grep -o '<img[^>]*src="[^"]*"' | wc -l | tr -d ' ')
echo "Script JavaScript: $JS_COUNT"
echo "Fogli di stile CSS: $CSS_COUNT"
echo "Immagini: $IMG_COUNT"
echo ""

# 5. Verifica plugin comuni che rallentano
echo -e "${GREEN}5. Plugin Potenzialmente Pesanti${NC}"
echo "----------------------------------------"
PLUGINS=$(echo "$HTML" | grep -o 'wp-content/plugins/[^/]*' | sort -u)
if [ -n "$PLUGINS" ]; then
    echo "$PLUGINS" | while read plugin; do
        echo "  - $plugin"
    done
else
    echo "Nessun plugin rilevato nell'HTML"
fi
echo ""

# 6. Verifica ottimizzazioni
echo -e "${GREEN}6. Verifica Ottimizzazioni${NC}"
echo "----------------------------------------"

# Minificazione JS/CSS
if echo "$HTML" | grep -q "\.min\.js\|\.min\.css"; then
    echo -e "${GREEN}✓${NC} Alcuni file JS/CSS sono minificati"
else
    echo -e "${YELLOW}⚠${NC} Nessun file minificato rilevato"
fi

# Lazy loading immagini
if echo "$HTML" | grep -qi "loading=\"lazy\""; then
    echo -e "${GREEN}✓${NC} Lazy loading attivo per alcune immagini"
else
    echo -e "${YELLOW}⚠${NC} Lazy loading non rilevato"
fi

# CDN
if echo "$HTML" | grep -q "cdn\|cloudfront\|cloudflare"; then
    echo -e "${GREEN}✓${NC} Possibile uso di CDN"
else
    echo -e "${YELLOW}⚠${NC} Nessun CDN rilevato"
fi

echo ""

# 7. Test connessione database (se wp-cli disponibile)
echo -e "${GREEN}7. Verifica Database (locale)${NC}"
echo "----------------------------------------"
if command -v wp &> /dev/null; then
    if [ -f "wp-config.php" ]; then
        echo "Controllo dimensioni database..."
        wp db size --path="$(pwd)" --human-readable 2>/dev/null || echo "Impossibile verificare dimensioni DB"
        
        echo ""
        echo "Tabelle più grandi:"
        wp db query "SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)' FROM information_schema.TABLES WHERE table_schema = DATABASE() ORDER BY (data_length + index_length) DESC LIMIT 5;" --path="$(pwd)" 2>/dev/null || echo "Impossibile eseguire query"
    else
        echo "wp-config.php non trovato (sito non configurato localmente)"
    fi
else
    echo "wp-cli non disponibile"
fi
echo ""

# 8. Raccomandazioni
echo -e "${GREEN}8. Raccomandazioni${NC}"
echo "----------------------------------------"
echo -e "${YELLOW}Per migliorare le performance:${NC}"
echo "1. Attiva cache (WP Super Cache, W3 Total Cache, o cache del server)"
echo "2. Ottimizza immagini (compressione, formati moderni come WebP)"
echo "3. Minifica JS e CSS"
echo "4. Usa CDN per risorse statiche"
echo "5. Disattiva plugin non necessari"
echo "6. Verifica query database lente"
echo "7. Considera hosting più performante o VPS"
echo ""

echo -e "${BLUE}Analisi completata!${NC}"


