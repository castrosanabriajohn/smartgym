<?php
// header.php
// This file outputs the opening HTML tags, the navigation bar and
// includes common resources like TailwindCSS and Font Awesome. It
// should be included at the top of every page.
require_once __DIR__ . '/../config.php';

// Ensure the database is initialized only once per session. If the
// initialization has not been done, include init_db.php which creates
// tables and inserts sample data. This prevents repeatedly creating
// tables on every request.
if (empty($_SESSION['db_initialized'])) {
    require_once __DIR__ . '/../init_db.php';
}

// Determine if a user is logged in by checking the session. If so,
// try to get the user name either from the session or via a query.
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartGym</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom styles can be added in /css/custom.css -->
    <link rel="stylesheet" href="/smartgym/css/custom.css">
</head>
<body class="font-sans bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-gray-900 text-white sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <i class="fas fa-dumbbell text-blue-500 text-2xl mr-2"></i>
                    <span class="font-bold text-xl">SMART GYM</span>
                </div>
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="/smartgym/index.php" class="hover:text-blue-400 transition">Home</a>
                    <a href="/smartgym/index.php#about" class="hover:text-blue-400 transition">About</a>
                    <a href="/smartgym/index.php#classes" class="hover:text-blue-400 transition">Classes</a>
                    <a href="/smartgym/index.php#trainers" class="hover:text-blue-400 transition">Trainers</a>
                    <a href="/smartgym/index.php#membership" class="hover:text-blue-400 transition">Membership</a>
                    <a href="/smartgym/pages/news.php" class="hover:text-blue-400 transition">News</a>
                    <a href="/smartgym/pages/gallery.php" class="hover:text-blue-400 transition">Gallery</a>
                    <a href="/smartgym/pages/contact.php" class="hover:text-blue-400 transition">Contact</a>
                    <?php if ($loggedIn): ?>
                        <span class="text-gray-300">Hello, <?php echo htmlspecialchars($currentUser); ?></span>
                        <a href="/smartgym/pages/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">Logout</a>
                    <?php else: ?>
                        <a href="/smartgym/pages/login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Login</a>
                        <a href="/smartgym/pages/register.php" class="border border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-white px-4 py-2 rounded-lg transition">Sign Up</a>
                    <?php endif; ?>
                </div>
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="menu-btn" class="block focus:outline-none">
                        <span class="hamburger-top block w-6 h-0.5 bg-white mb-1.5"></span>
                        <span class="hamburger-middle block w-6 h-0.5 bg-white mb-1.5"></span>
                        <span class="hamburger-bottom block w-6 h-0.5 bg-white"></span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-gray-800 absolute w-full px-6 pb-4">
            <div class="flex flex-col space-y-3">
                <a href="/smartgym/index.php" class="block py-2 hover:text-blue-400 transition">Home</a>
                <a href="/smartgym/index.php#about" class="block py-2 hover:text-blue-400 transition">About</a>
                <a href="/smartgym/index.php#classes" class="block py-2 hover:text-blue-400 transition">Classes</a>
                <a href="/smartgym/index.php#trainers" class="block py-2 hover:text-blue-400 transition">Trainers</a>
                <a href="/smartgym/index.php#membership" class="block py-2 hover:text-blue-400 transition">Membership</a>
                <a href="/smartgym/pages/news.php" class="block py-2 hover:text-blue-400 transition">News</a>
                <a href="/smartgym/pages/gallery.php" class="block py-2 hover:text-blue-400 transition">Gallery</a>
                <a href="/smartgym/pages/contact.php" class="block py-2 hover:text-blue-400 transition">Contact</a>
                <?php if ($loggedIn): ?>
                    <span class="block py-2 text-gray-300">Hello, <?php echo htmlspecialchars($currentUser); ?></span>
                    <a href="/smartgym/pages/logout.php" class="block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">Logout</a>
                <?php else: ?>
                    <a href="/smartgym/pages/login.php" class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Login</a>
                    <a href="/smartgym/pages/register.php" class="block border border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-white px-4 py-2 rounded-lg transition">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <script>
    // Mobile menu toggle functionality
    $(function(){
        $('#menu-btn').on('click', function() {
            $('#mobile-menu').toggleClass('hidden');
            $(this).toggleClass('open');
        });
    });
    </script>

    <!-- Main Content Wrapper -->
    <main>