<?php
require_once __DIR__ . '/../includes/header.php';

// Validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
if ($id) {
    $stmt = $db->prepare('SELECT id, user_id, title, body, published_at, created_at FROM posts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!$post) {
    echo '<div class="container mx-auto px-6 py-16"><p class="text-center">Publicación no encontrada.</p></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
?>
<div class="container mx-auto px-6 py-16">
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- No image field in posts table -->
        <div class="p-6">
            <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>
            <p class="text-gray-600 text-sm mb-6"><?php echo date('F j, Y', strtotime($post['published_at'] ?? $post['created_at'])); ?></p>
            <p class="text-gray-700 leading-relaxed mb-6">
                <?php echo nl2br(htmlspecialchars($post['body'])); ?>
            </p>
            <a href="/smartgym/pages/news.php" class="text-blue-600 hover:underline">← Volver</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>