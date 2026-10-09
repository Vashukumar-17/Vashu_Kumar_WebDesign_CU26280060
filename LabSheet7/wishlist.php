<?php
require 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['book_id'] ?? null, FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? 'add';
    if ($id) {
        if ($action === 'remove') {
            $s = $pdo->prepare("DELETE FROM wishlist WHERE user_id=? AND book_id=?");
            $s->execute([$_SESSION['user']['id'], $id]);
        } else {
            $s = $pdo->prepare("INSERT IGNORE INTO wishlist(user_id,book_id) VALUES(?,?)");
            $s->execute([$_SESSION['user']['id'], $id]);
        }
    }
    header('Location: wishlist.php');
    exit;
}
$s = $pdo->prepare("SELECT b.* FROM wishlist w JOIN books b ON b.id=w.book_id WHERE w.user_id=? ORDER BY w.created_at DESC");
$s->execute([$_SESSION['user']['id']]);
$books = $s->fetchAll();
$pageTitle = 'My wishlist';
require 'includes/header.php'; ?>
<h1>My wishlist</h1><?php if (!$books): ?>
    <div class="empty">No saved books yet. Browse books and add them to your wishlist.</div><?php else: ?>
    <div class="grid"><?php foreach ($books as $b): ?>
            <article class="book-card">
                <div class="cover">📚</div>
                <h3><?= e($b['title']) ?></h3>
                <p class="muted"><?= e($b['author']) ?></p>
                <p class="price"><?= money($b['price']) ?></p>
                <div class="actions"><a class="button" href="book.php?id=<?= $b['id'] ?>">Details</a>
                    <form method="post"><input type="hidden" name="book_id" value="<?= $b['id'] ?>"><input type="hidden"
                            name="action" value="remove"><button class="button danger">Remove</button></form>
                </div>
            </article><?php endforeach; ?>
    </div><?php endif; ?>
<?php require 'includes/footer.php'; ?>