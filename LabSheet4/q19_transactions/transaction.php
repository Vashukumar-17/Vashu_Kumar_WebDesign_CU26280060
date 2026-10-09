<?php
// Q19 – PDO transaction: transfer money between accounts and log it (multi-table, all-or-nothing)
$pdo = require __DIR__ . '/../config/db.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY, owner VARCHAR(100) NOT NULL, balance DECIMAL(10,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB");
$pdo->exec("CREATE TABLE IF NOT EXISTS transfer_log (
    id INT AUTO_INCREMENT PRIMARY KEY, from_account INT NOT NULL, to_account INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");
$pdo->exec("INSERT IGNORE INTO accounts (id, owner, balance) VALUES (1,'Alice',1000),(2,'Bob',500)");

function transfer(PDO $pdo, int $from, int $to, float $amount): string
{
    try {
        $pdo->beginTransaction();

        // Lock the sender's row to avoid race conditions
        $stmt = $pdo->prepare('SELECT balance FROM accounts WHERE id = ? FOR UPDATE');
        $stmt->execute([$from]);
        $balance = $stmt->fetchColumn();

        if ($balance === false) {
            throw new Exception('Sender account not found.');
        }
        if ($balance < $amount) {
            throw new Exception('Insufficient funds.');
        }

        $pdo->prepare('UPDATE accounts SET balance = balance - ? WHERE id = ?')->execute([$amount, $from]);
        $pdo->prepare('UPDATE accounts SET balance = balance + ? WHERE id = ?')->execute([$amount, $to]);
        $pdo->prepare('INSERT INTO transfer_log (from_account, to_account, amount) VALUES (?,?,?)')
            ->execute([$from, $to, $amount]);

        $pdo->commit();
        return "✔ COMMIT: transferred $amount from #$from to #$to.";
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return '✘ ROLLBACK: ' . $e->getMessage();
    }
}

function showBalances(PDO $pdo): void
{
    echo '<table border="1" cellpadding="6" cellspacing="0"><tr><th>ID</th><th>Owner</th><th>Balance</th></tr>';
    foreach ($pdo->query('SELECT * FROM accounts ORDER BY id') as $a) {
        printf('<tr><td>%d</td><td>%s</td><td>%.2f</td></tr>', $a['id'], htmlspecialchars($a['owner']), $a['balance']);
    }
    echo '</table>';
}

echo '<h2>Transaction demo</h2><h4>Before</h4>';
showBalances($pdo);

echo '<h4>Transfer 200 from Alice (#1) to Bob (#2)</h4><p>' . transfer($pdo, 1, 2, 200) . '</p>';
echo '<h4>Transfer 99999 from Bob (#2) to Alice (#1) – should fail</h4><p>' . transfer($pdo, 2, 1, 99999) . '</p>';

echo '<h4>After</h4>';
showBalances($pdo);
