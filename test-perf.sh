#!/bin/bash

URL="https://www.archiviowebsite.com/"

echo "=== Test Performance ===" > perf-result.txt
echo "URL: $URL" >> perf-result.txt
echo "" >> perf-result.txt

echo "Test 1:" >> perf-result.txt
curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s | Totale: %{time_total}s | Dimensione: %{size_download} bytes | HTTP: %{http_code}\n" "$URL" >> perf-result.txt 2>&1

echo "" >> perf-result.txt
echo "Test 2:" >> perf-result.txt
curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s | Totale: %{time_total}s | Dimensione: %{size_download} bytes | HTTP: %{http_code}\n" "$URL" >> perf-result.txt 2>&1

echo "" >> perf-result.txt
echo "Test 3:" >> perf-result.txt
curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s | Totale: %{time_total}s | Dimensione: %{size_download} bytes | HTTP: %{http_code}\n" "$URL" >> perf-result.txt 2>&1

echo "" >> perf-result.txt
echo "Header:" >> perf-result.txt
curl -s -I "$URL" | grep -E "(HTTP|Server|Cache-Control|X-Cache)" >> perf-result.txt 2>&1

cat perf-result.txt


