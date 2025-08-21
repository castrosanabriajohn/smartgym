<?php
require_once __DIR__ . '/../includes/header.php';
if (!$loggedIn) {
    header('Location: /smartgym/pages/login.php');
    exit;
}
$title = '';
$body = '';
$image = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $image = trim($_POST['image_url'] ?? '');
    if ($title === '') { $errors[] = 'Title is required'; }
    if ($body === '') { $errors[] = 'Body is required'; }
    if (!$errors) {
        $stmt = $db->prepare('INSERT INTO posts (user_id, title, body, published_at, image_url) VALUES (:uid,:title,:body,NOW(),:img)');
        $stmt->execute([':uid'=>$_SESSION['user_id'], ':title'=>$title, ':body'=>$body, ':img'=>$image ?: null]);
        header('Location: /smartgym/pages/news.php');
        exit;
    }
}
?>
<div class="container">
    <h2 class="mb-4">Create News</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Body</label>
            <textarea name="body" class="form-control" rows="6" required><?php echo htmlspecialchars($body); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Image URL (optional)</label>
            <input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($image); ?>">
        </div>
        <button class="btn btn-primary">Publish</button>
        <a href="/smartgym/pages/news.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
