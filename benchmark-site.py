#!/usr/bin/env python3
import time
import urllib.request

url = "http://localhost/nutterlyGood/"
for label in ["cold", "warm"]:
    t0 = time.perf_counter()
    try:
        with urllib.request.urlopen(url, timeout=180) as r:
            html = r.read()
        elapsed = time.perf_counter() - t0
        print(f"{label}: {elapsed:.2f}s, {len(html):,} bytes")
    except Exception as e:
        elapsed = time.perf_counter() - t0
        print(f"{label}: FAILED after {elapsed:.2f}s — {e}")