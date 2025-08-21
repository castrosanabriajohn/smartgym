<?php
require_once __DIR__ . '/../includes/header.php';

// Traer posts publicados (incluye image_url)
$stmt = $db->query(
    'SELECT id, user_id, title, body, published_at, created_at, image_url
     FROM posts
     WHERE published_at IS NOT NULL
     ORDER BY published_at DESC'
);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold mb-8 text-center">Latest News</h2>

    <?php if (empty($posts)): ?>
        <p class="text-center text-gray-600">No hay noticias publicadas aún.</p>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php foreach ($posts as $post): ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <?php if (!empty($post['image_url'])): ?>
                <img
                    src="<?php echo htmlspecialchars($post['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($post['title']); ?>"
                    class="w-full h-48 object-cover"
                >
            <?php endif; ?>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h3>
                <p class="text-gray-600 text-sm mb-4">
                    <?php
                    $fecha = $post['published_at'] ?: $post['created_at'];
                    echo date('F j, Y', strtotime($fecha));
                    ?>
                </p>
                <p class="text-gray-600 mb-4">
                    <?php echo htmlspecialchars(mb_strimwidth($post['body'], 0, 150, '...')); ?>
                </p>
                <a href="/smartgym/pages/news_detail.php?id=<?php echo (int)$post['id']; ?>"
                   class="text-blue-600 hover:underline">Leer más</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
