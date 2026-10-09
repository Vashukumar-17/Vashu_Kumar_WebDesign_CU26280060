# Q18 – Apache performance and log analysis

## Log locations
| OS | error.log | access.log |
|---|---|---|
| XAMPP (Windows) | `C:\xampp\apache\logs\error.log` | `C:\xampp\apache\logs\access.log` |
| Ubuntu/Debian | `/var/log/apache2/error.log` | `/var/log/apache2/access.log` |

## Steps
1. **Generate requests** – run `simulate_requests.sh` (Linux/macOS/Git Bash) which sends 200 normal
   requests, some 404s, and a concurrent load test with ApacheBench.
2. **Watch logs live**
   ```bash
   tail -f /var/log/apache2/access.log
   tail -f /var/log/apache2/error.log
   ```
3. **Analyse** (Linux/Git Bash)
   ```bash
   # Requests per status code
   awk '{print $9}' access.log | sort | uniq -c | sort -rn
   # Top requested URLs
   awk '{print $7}' access.log | sort | uniq -c | sort -rn | head
   # Top client IPs
   awk '{print $1}' access.log | sort | uniq -c | sort -rn | head
   # 404 errors only
   grep '" 404 ' access.log
   ```
4. **Read an access-log line**
   `127.0.0.1 - - [09/Oct/2026:15:10:01 +0530] "GET /missing.html HTTP/1.1" 404 196`
   → client IP, timestamp, request line, status code, bytes sent.
5. **Error log:** 404s appear as `File does not exist: .../missing.html`; PHP errors appear as `PHP Warning: ...`.

## ApacheBench output to note
`Requests per second`, `Time per request`, `Failed requests`, and the percentile table.
