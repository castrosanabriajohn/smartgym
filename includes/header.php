<?php
// header.php
// Outputs opening HTML, navigation bar, and includes common resources like Bootstrap and Font Awesome.
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['db_initialized'])) {
    require_once __DIR__ . '/../init_db.php';
}

$loggedIn = isset($_SESSION['user_id']);
$currentUser = null;
if ($loggedIn) {
    if (!empty($_SESSION['user_name'])) {
        $currentUser = $_SESSION['user_name'];
    } else {
        $stmt = $db->prepare('SELECT name FROM users WHERE id = :id');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $currentUser = $stmt->fetchColumn();
        $_SESSION['user_name'] = $currentUser;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartGym</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/smartgym/css/custom.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="/smartgym/index.php"><i class="fas fa-dumbbell text-primary"></i> SMART GYM</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/smartgym/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/smartgym/index.php#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="/smartgym/index.php#classes">Classes</a></li>
        <li class="nav-item"><a class="nav-link" href="/smartgym/index.php#trainers">Trainers</a></li>
        <li class="nav-item"><a class="nav-link" href="/smartgym/index.php#membership">Membership</a></li>
        <li class="nav-item"><a class="nav-link" href="/smartgym/pages/news.php">News</a></li>
        <li class="nav-item"><a class="nav-link" href="/smartgym/pages/gallery.php">Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="/smartgym/pages/contact.php">Contact</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if ($loggedIn): ?>
            <li class="nav-item"><span class="navbar-text me-2">Hello, <?php echo htmlspecialchars($currentUser); ?></span></li>
            <li class="nav-item"><a class="btn btn-danger" href="/smartgym/pages/logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="btn btn-primary me-2" href="/smartgym/pages/login.php">Login</a></li>
            <li class="nav-item"><a class="btn btn-outline-light" href="/smartgym/pages/register.php">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="py-4">
