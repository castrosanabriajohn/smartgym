<?php
require_once __DIR__ . '/../includes/header.php';
if (!$loggedIn) {
    header('Location: /smartgym/pages/login.php');
    exit;
}
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT title FROM posts WHERE id=:id');
$stmt->execute([':id'=>$id]);
$post = $stmt->fetch();
if (!$post) {
    echo '<div class="container"><p>Post not found.</p></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare('DELETE FROM posts WHERE id=:id');
    $stmt->execute([':id'=>$id]);
    header('Location: /smartgym/pages/news.php');
    exit;
}
?>
<div class="container">
    <h2 class="mb-4">Delete News</h2>
    <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($post['title']); ?></strong>?</p>
    <form method="post">
        <button class="btn btn-danger">Delete</button>
        <a href="/smartgym/pages/news.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
