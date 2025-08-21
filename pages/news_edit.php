<?php
require_once __DIR__ . '/../includes/header.php';
if (!$loggedIn) {
    header('Location: /smartgym/pages/login.php');
    exit;
}
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM posts WHERE id = :id');
$stmt->execute([':id'=>$id]);
$post = $stmt->fetch();
if (!$post) {
    echo '<div class="container"><p>Post not found.</p></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
$title = $post['title'];
$body = $post['body'];
$image = $post['image_url'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $image = trim($_POST['image_url'] ?? '');
    if ($title === '') { $errors[] = 'Title is required'; }
    if ($body === '') { $errors[] = 'Body is required'; }
    if (!$errors) {
        $stmt = $db->prepare('UPDATE posts SET title=:title, body=:body, image_url=:img WHERE id=:id');
        $stmt->execute([':title'=>$title, ':body'=>$body, ':img'=>$image ?: null, ':id'=>$id]);
        header('Location: /smartgym/pages/news.php');
        exit;
    }
}
?>
<div class="container">
    <h2 class="mb-4">Edit News</h2>
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
        <button class="btn btn-primary">Update</button>
        <a href="/smartgym/pages/news.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
