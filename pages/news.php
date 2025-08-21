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
<div class="container py-5">
    <h2 class="text-center mb-4">Latest News</h2>
    <?php if ($loggedIn): ?>
        <a href="/smartgym/pages/news_create.php" class="btn btn-success mb-3">Add News</a>
    <?php endif; ?>
    <?php if (empty($posts)): ?>
        <p class="text-center text-muted">No hay noticias publicadas aún.</p>
    <?php else: ?>
    <div class="row">
        <?php foreach ($posts as $post): ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <?php if (!empty($post['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($post['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                    <p class="card-text"><small class="text-muted">
                        <?php
                        $fecha = $post['published_at'] ?: $post['created_at'];
                        echo date('F j, Y', strtotime($fecha));
                        ?>
                    </small></p>
                    <p class="card-text"><?php echo htmlspecialchars(mb_strimwidth($post['body'], 0, 150, '...')); ?></p>
                    <a href="/smartgym/pages/news_detail.php?id=<?php echo (int)$post['id']; ?>" class="btn btn-link p-0">Leer más</a>
                    <?php if ($loggedIn): ?>
                        <div class="mt-2">
                            <a class="btn btn-sm btn-outline-primary" href="/smartgym/pages/news_edit.php?id=<?php echo (int)$post['id']; ?>">Edit</a>
                            <a class="btn btn-sm btn-outline-danger" href="/smartgym/pages/news_delete.php?id=<?php echo (int)$post['id']; ?>">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
