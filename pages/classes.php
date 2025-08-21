<?php
require_once __DIR__ . '/../includes/header.php';

// Retrieve all upcoming classes with their activity type
$stmt = $db->prepare('SELECT c.id, c.title, c.start_at, c.end_at, c.capacity, c.trainer, a.name AS activity_name, a.description AS activity_description
                      FROM classes c
                      JOIN activity_types a ON c.activity_type_id = a.id
                      ORDER BY c.start_at ASC');
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_class_id'])) {
    $classId = (int)$_POST['book_class_id'];
    if (!isset($_SESSION['user_id'])) {
        header('Location: /smartgym/pages/login.php');
        exit;
    }
    // Check if reservation already exists for this user and class
    $check = $db->prepare('SELECT COUNT(*) FROM class_reservations WHERE class_id = :class_id AND user_id = :user_id');
    $check->execute([
        ':class_id' => $classId,
        ':user_id'  => $_SESSION['user_id']
    ]);
    if ((int)$check->fetchColumn() === 0) {
        $stmtRes = $db->prepare('INSERT INTO class_reservations (class_id, user_id, status) VALUES (:class_id, :user_id, "CONFIRMED")');
        $stmtRes->execute([
            ':class_id' => $classId,
            ':user_id'  => $_SESSION['user_id']
        ]);
        $bookedMsg = '¡Reserva realizada con éxito!';
    } else {
        $bookedMsg = 'Ya tienes una reserva para esta clase.';
    }
}
?>
<div class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold mb-8 text-center">Our Classes</h2>
    <?php if (!empty($bookedMsg)): ?>
        <div class="bg-green-100 text-green-800 p-3 rounded mb-6 text-sm">
            <?php echo $bookedMsg; ?>
        </div>
    <?php endif; ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($classes as $class): ?>
        <div class="bg-white rounded-lg overflow-hidden shadow-lg">
            <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80" alt="<?php echo htmlspecialchars($class['title']); ?>" class="w-full h-48 object-cover">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($class['title']); ?></h3>
                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded mb-2"><?php echo htmlspecialchars($class['activity_name']); ?></span>
                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($class['activity_description']); ?></p>
                <?php
                    $start = new DateTime($class['start_at']);
                    $end   = new DateTime($class['end_at']);
                    $durationMin = $start->diff($end)->i + ($start->diff($end)->h * 60);
                ?>
                <div class="flex justify-between text-sm text-gray-500 mb-4">
                    <span><i class="fas fa-clock mr-1"></i> <?php echo $durationMin; ?> min</span>
                    <span><i class="fas fa-user mr-1"></i> Capacidad <?php echo htmlspecialchars($class['capacity']); ?></span>
                </div>
                <form method="post" action="">
                    <input type="hidden" name="book_class_id" value="<?php echo $class['id']; ?>">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Reservar</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>