# Q20 – Configure PHP extensions in php.ini

1. Find php.ini (`php --ini` or `phpinfo()` → *Loaded Configuration File*).
2. Remove the leading `;` to enable each extension:

**Windows / XAMPP**
```ini
extension=mysqli
extension=pdo_mysql
extension=mbstring
extension=curl
extension=openssl
extension=fileinfo
```
(Ensure `extension_dir = "ext"` points to the `php\ext` folder.)

**Ubuntu/Debian** (no php.ini editing needed)
```bash
sudo apt install php-mysql php-mbstring php-curl
sudo phpenmod mysqli pdo_mysql mbstring curl
```

3. Restart Apache.
4. Verify:
   - Browser: open `check_extensions.php` – each row should say **Loaded**.
   - CLI: `php -m | grep -Ei "mysqli|pdo_mysql|mbstring|curl"`
   - `phpinfo()` – search for the extension's section heading.

**If an extension will not load:** check `error.log` for "unable to load dynamic library", confirm the
`.dll`/`.so` exists in the extension dir, and make sure you edited the php.ini that Apache actually loads
(CLI and Apache can use different files).
