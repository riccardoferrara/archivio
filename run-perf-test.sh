#!/bin/bash

cd /Volumes/Data/dev/archivio

URL=$(grep "^REMOTE_URL=" sync-wp-ftp.sh | cut -d'"' -f2 | sed 's|/$||')

echo "=== Test Performance per: $URL ===" > perf-output.txt
echo "" >> perf-output.txt

echo "Test 1:" >> perf-output.txt
curl -o /dev/null -s -w "  TTFB: %{time_starttransfer}s | Totale: %{time_total}s | Dimensione: %{size_download} bytes | HTTP: %{http_code}\n" "$URL" >> perf-output.txt 2>&1

echo "Test 2:" >> perf-output.txt
curl -o /dev/null -s -w "  TTFB: %{time_starttransfer}s | Totale: %{time_total}s | Dimensione: %{size_download} bytes | HTTP: %{http_code}\n" "$URL" >> perf-output.txt 2>&1

echo "Test 3:" >> perf-output.txt
curl -o /dev/null -s -w "  TTFB: %{time_starttransfer}s | Totale: %{time_total}s | Dimensione: %{size_download} bytes | HTTP: %{http_code}\n" "$URL" >> perf-output.txt 2>&1

echo "" >> perf-output.txt
echo "Header HTTP:" >> perf-output.txt
curl -s -I "$URL" >> perf-output.txt 2>&1

echo "" >> perf-output.txt
echo "Analisi HTML (prime 50 righe):" >> perf-output.txt
curl -s "$URL" | head -50 >> perf-output.txt 2>&1

cat perf-output.txt


