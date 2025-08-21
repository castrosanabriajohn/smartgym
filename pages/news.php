<?php
require_once __DIR__ . '/../includes/header.php';

// Fetch all published posts
$stmt = $db->query('SELECT id, user_id, title, body, published_at, created_at FROM posts WHERE published_at IS NOT NULL ORDER BY published_at DESC');
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold mb-8 text-center">Latest News</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php foreach ($posts as $post): ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <?php if ($post['image_url']): ?>
            <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-48 object-cover">
            <?php endif; ?>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($post['title']); ?></h3>
                <p class="text-gray-600 text-sm mb-4"><?php echo date('F j, Y', strtotime($post['published_at'] ?? $post['created_at'])); ?></p>
                <p class="text-gray-600 mb-4">
                    <?php echo htmlspecialchars(mb_strimwidth($post['body'], 0, 150, '...')); ?>
                </p>
                <a href="/smartgym/pages/news_detail.php?id=<?php echo $post['id']; ?>" class="text-blue-600 hover:underline">Leer más</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>