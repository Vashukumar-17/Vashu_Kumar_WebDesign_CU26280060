<?php
// Q20 – Verify that required PHP extensions are active
$required = ['mysqli', 'pdo', 'pdo_mysql', 'mbstring', 'curl', 'openssl', 'fileinfo', 'json', 'session'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>PHP Extensions</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px
    }

    table {
      border-collapse: collapse;
      min-width: 420px
    }

    th,
    td {
      border: 1px solid #ccc;
      padding: 8px 14px;
      text-align: left
    }

    th {
      background: #37474f;
      color: #fff
    }

    .ok {
      color: #2e7d32;
      font-weight: bold
    }

    .bad {
      color: #c62828;
      font-weight: bold
    }
  </style>
</head>

<body>
  <h2>PHP <?= PHP_VERSION ?> – Extension Status</h2>
  <p>php.ini in use: <code><?= htmlspecialchars((string) php_ini_loaded_file()) ?></code></p>
  <table>
    <tr>
      <th>Extension</th>
      <th>Status</th>
      <th>Version</th>
    </tr>
    <?php foreach ($required as $ext):
      $loaded = extension_loaded($ext); ?>
      <tr>
        <td><?= $ext ?></td>
        <td class="<?= $loaded ? 'ok' : 'bad' ?>"><?= $loaded ? 'Loaded' : 'MISSING' ?></td>
        <td><?= $loaded ? htmlspecialchars((string) phpversion($ext)) : '-' ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>

</html>