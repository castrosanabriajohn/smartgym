<?php
require_once __DIR__ . '/../includes/header.php';

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $msg     = trim($_POST['message'] ?? '');
    if ($name && $email && $subject && $msg) {
        // Save to DB
        $stmt = $db->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)');
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':subject' => $subject,
            ':message' => $msg
        ]);
        $message = 'Your message has been received. Thank you for contacting us!';
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<div class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold mb-6 text-center">Contact Us</h2>
    <p class="text-center text-gray-600 mb-8">Have questions or feedback? We’d love to hear from you.</p>
    <?php if ($message): ?>
        <div class="bg-green-100 text-green-800 p-3 rounded mb-6 text-sm max-w-md mx-auto">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-800 p-3 rounded mb-6 text-sm max-w-md mx-auto">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="" class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-700" for="name">Name</label>
            <input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded-md" required>
        </div>
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-700" for="email">Email</label>
            <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-md" required>
        </div>
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-700" for="subject">Subject</label>
            <input type="text" id="subject" name="subject" class="w-full px-3 py-2 border rounded-md" required>
        </div>
        <div class="mb-6">
            <label class="block mb-2 text-sm font-medium text-gray-700" for="message">Message</label>
            <textarea id="message" name="message" rows="5" class="w-full px-3 py-2 border rounded-md" required></textarea>
        </div>
        <div class="text-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded">Send Message</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>