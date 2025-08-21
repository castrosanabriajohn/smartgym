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
$name = $plan['name'];
$duration = $plan['duration_days'];
$price = $plan['price'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    if ($name === '') { $errors[] = 'Name is required'; }
    if ($duration <= 0) { $errors[] = 'Duration must be positive'; }
    if ($price <= 0) { $errors[] = 'Price must be positive'; }
    if (!$errors) {
        $stmt = $db->prepare('UPDATE membership_plans SET name=:name, duration_days=:duration, price=:price WHERE id=:id');
        $stmt->execute([':name'=>$name, ':duration'=>$duration, ':price'=>$price, ':id'=>$id]);
        header('Location: /smartgym/index.php#membership');
        exit;
    }
}
?>
<div class="container">
    <h2 class="mb-4">Edit Membership Plan</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Duration (days)</label>
            <input type="number" name="duration" class="form-control" value="<?php echo htmlspecialchars($duration); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" required>
        </div>
        <button class="btn btn-primary">Update</button>
        <a href="/smartgym/index.php#membership" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
