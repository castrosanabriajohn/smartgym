<?php
require_once __DIR__ . '/../includes/header.php';
if (!$loggedIn) {
    header('Location: /smartgym/pages/login.php');
    exit;
}
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM membership_plans WHERE id = :id');
$stmt->execute([':id'=>$id]);
$plan = $stmt->fetch();
if (!$plan) {
    echo '<div class="container"><p>Plan not found.</p></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare('DELETE FROM membership_plans WHERE id=:id');
    $stmt->execute([':id'=>$id]);
    header('Location: /smartgym/index.php#membership');
    exit;
}
?>
<div class="container">
    <h2 class="mb-4">Delete Membership Plan</h2>
    <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($plan['name']); ?></strong>?</p>
    <form method="post">
        <button class="btn btn-danger">Delete</button>
        <a href="/smartgym/index.php#membership" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
