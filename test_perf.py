#!/usr/bin/env python3
import urllib.request
import time
import sys

URL = "https://www.archiviowebsite.com/"

print("=== Test Performance ===")
print(f"URL: {URL}")
print()

for i in range(1, 4):
    print(f"Test {i}:", end=" ")
    try:
        start = time.time()
        req = urllib.request.urlopen(URL, timeout=10)
        ttfb = time.time() - start
        content = req.read()
        total = time.time() - start
        size = len(content)
        code = req.getcode()
        print(f"TTFB: {ttfb:.3f}s | Totale: {total:.3f}s | Dimensione: {size} bytes | HTTP: {code}")
    except Exception as e:
        print(f"Errore: {e}")

print()
print("Header HTTP:")
try:
    req = urllib.request.urlopen(URL, timeout=10)
    headers = dict(req.headers)
    for key in ['Server', 'Cache-Control', 'X-Cache', 'X-Powered-By']:
        if key in headers:
            print(f"{key}: {headers[key]}")
except Exception as e:
    print(f"Errore: {e}")


