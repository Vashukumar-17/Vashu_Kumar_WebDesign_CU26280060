<?php
// Q21 – Register a user: store only a password hash
$pdo = require __DIR__ . '/../config/db.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        $message = '<p style="color:red">Username: 3–50 letters, digits or underscore.</p>';
    } elseif (strlen($password) < 8) {
        $message = '<p style="color:red">Password must be at least 8 characters.</p>';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT); // bcrypt/argon2 with automatic salt
        try {
            $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)')->execute([$username, $hash]);
            $message = '<p style="color:green">Registered! Stored hash: <code>' . htmlspecialchars($hash) . '</code></p>';
        } catch (PDOException $e) {
            $message = '<p style="color:red">Username already taken.</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>

<body style="font-family:Arial,sans-serif;max-width:420px;margin:50px auto">
    <h2>Register</h2>
    <?= $message ?>
    <form method="post">
        <input name="username" placeholder="Username" required style="width:100%;padding:8px;margin:5px 0"><br>
        <input type="password" name="password" placeholder="Password (min 8 chars)" required
            style="width:100%;padding:8px;margin:5px 0"><br>
        <button style="width:100%;padding:8px">Register</button>
    </form>
    <p><a href="login.php">Go to login</a></p>
</body>

</html>