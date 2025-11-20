#!/bin/bash

URL="https://www.archiviowebsite.com/"

echo "=== Analisi Performance Completa ==="
echo "URL: $URL"
echo ""

echo "1. Test URL Finale (seguendo redirect):"
echo "----------------------------------------"
for i in 1 2 3; do
  echo -n "Test $i: "
  curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s | Totale: %{time_total}s | Dimensione: %{size_download} bytes | HTTP: %{http_code}\n" -L "$URL"
done

echo ""
echo "2. Header HTTP Finale:"
echo "----------------------------------------"
curl -s -I -L "$URL" | grep -E "(HTTP|Server|Cache-Control|X-Cache|X-Powered-By|Content-Type|Location)" | head -10

echo ""
echo "3. Analisi Dimensioni Pagina:"
echo "----------------------------------------"
HTML_SIZE=$(curl -s -L "$URL" | wc -c)
echo "Dimensione HTML: $(numfmt --to=iec-i --suffix=B $HTML_SIZE 2>/dev/null || echo "${HTML_SIZE} bytes")"

echo ""
echo "4. Risorse Caricate (prime 20):"
echo "----------------------------------------"
HTML=$(curl -s -L "$URL")
JS_COUNT=$(echo "$HTML" | grep -o '<script[^>]*src="[^"]*"' | wc -l | tr -d ' ')
CSS_COUNT=$(echo "$HTML" | grep -o '<link[^>]*rel="stylesheet"[^>]*>' | wc -l | tr -d ' ')
IMG_COUNT=$(echo "$HTML" | grep -o '<img[^>]*src="[^"]*"' | wc -l | tr -d ' ')
echo "Script JavaScript: $JS_COUNT"
echo "Fogli di stile CSS: $CSS_COUNT"
echo "Immagini: $IMG_COUNT"

echo ""
echo "5. Plugin Rilevati:"
echo "----------------------------------------"
echo "$HTML" | grep -o 'wp-content/plugins/[^/]*' | sort -u | head -10

echo ""
echo "6. Verifica Ottimizzazioni:"
echo "----------------------------------------"
if echo "$HTML" | grep -q "\.min\.js\|\.min\.css"; then
  echo "✓ Alcuni file JS/CSS sono minificati"
else
  echo "⚠ Nessun file minificato rilevato"
fi

if echo "$HTML" | grep -qi "loading=\"lazy\""; then
  echo "✓ Lazy loading attivo"
else
  echo "⚠ Lazy loading non rilevato"
fi

if curl -s -I -L "$URL" | grep -qi "cache"; then
  echo "✓ Cache rilevata negli header"
else
  echo "⚠ Nessun header cache rilevato"
fi

echo ""
echo "=== Analisi Completata ==="


