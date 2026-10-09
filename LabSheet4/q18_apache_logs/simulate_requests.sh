#!/usr/bin/env bash
# Q18 – Generate traffic so there is something to see in access.log / error.log
# Usage: bash simulate_requests.sh [base_url]
BASE="${1:-http://localhost}"

echo "== 50 valid requests =="
for i in $(seq 1 50); do
  curl -s -o /dev/null -w "%{http_code} " "$BASE/"
done
echo

echo "== 10 requests to missing pages (will log 404s) =="
for i in $(seq 1 10); do
  curl -s -o /dev/null -w "%{http_code} " "$BASE/missing_$i.html"
done
echo

echo "== Load test: 500 requests, 20 concurrent (ApacheBench) =="
if command -v ab >/dev/null 2>&1; then
  ab -n 500 -c 20 "$BASE/"
else
  echo "ab not found. Install: sudo apt install apache2-utils"
fi

echo "== Last lines of the logs (Linux paths) =="
tail -n 5 /var/log/apache2/access.log 2>/dev/null
tail -n 5 /var/log/apache2/error.log  2>/dev/null
