<?php
// Q21 – Authenticate with password_verify() and transparently upgrade old hashes
$pdo = require __DIR__ . '/../config/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Re-hash if the algorithm/cost defaults have improved since the hash was created
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        }
        $message = '<p style="color:green">Login successful. Welcome, ' . htmlspecialchars($user['username']) . '!</p>';
    } else {
        $message = '<p style="color:red">Invalid username or password.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>

<body style="font-family:Arial,sans-serif;max-width:420px;margin:50px auto">
    <h2>Login</h2>
    <?= $message ?>
    <form method="post">
        <input name="username" placeholder="Username" required style="width:100%;padding:8px;margin:5px 0"><br>
        <input type="password" name="password" placeholder="Password" required
            style="width:100%;padding:8px;margin:5px 0"><br>
        <button style="width:100%;padding:8px">Log in</button>
    </form>
    <p><a href="register.php">Create an account</a></p>
</body>

</html>