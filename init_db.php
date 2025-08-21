<?php
// init_db.php
// Performs lightweight initialization tasks for the MySQL database. It
// creates auxiliary tables (uploads and contact_messages) if they do
// not already exist and seeds a default admin user. The primary
// schema (gyms, users, classes, etc.) should already be present.
require_once __DIR__ . '/config.php';

// Helper: check if a table exists in the current database
function tableExists(PDO $db, string $tableName): bool {
    $stmt = $db->prepare('SHOW TABLES LIKE :name');
    $stmt->execute([':name' => $tableName]);
    return $stmt->fetch() !== false;
}

// Create uploads table if it does not exist
if (!tableExists($db, 'uploads')) {
    $db->exec("CREATE TABLE uploads (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NULL,
        filename VARCHAR(255) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Create contact_messages table if it does not exist
if (!tableExists($db, 'contact_messages')) {
    $db->exec("CREATE TABLE contact_messages (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(160) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Create trainers table if it does not exist
if (!tableExists($db, 'trainers')) {
    $db->exec("CREATE TABLE trainers (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(120) NOT NULL,
        specialty VARCHAR(120),
        description TEXT,
        image_url VARCHAR(255),
        facebook VARCHAR(255),
        twitter VARCHAR(255),
        instagram VARCHAR(255)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Seed trainers if none exist
$stmt = $db->query('SELECT COUNT(*) FROM trainers');
if ((int)$stmt->fetchColumn() === 0) {
    $stmtTrainer = $db->prepare('INSERT INTO trainers (name, specialty, description, image_url, facebook, twitter, instagram) VALUES (:name,:specialty,:description,:image_url,:facebook,:twitter,:instagram)');
    $trainers = [
        [
            'name' => 'John Smith',
            'specialty' => 'Strength Coach',
            'description' => 'Con 12 años de experiencia, especializado en entrenamiento de fuerza y powerlifting.',
            'image_url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80',
            'facebook' => '#',
            'twitter' => '#',
            'instagram' => '#'
        ],
        [
            'name' => 'Sarah Johnson',
            'specialty' => 'Yoga Instructor',
            'description' => 'Instructora certificada con 8 años de experiencia en Hatha, Vinyasa y Yin yoga.',
            'image_url' => 'https://images.unsplash.com/photo-1545205597-3d9d02c29597?auto=format&fit=crop&w=1470&q=80',
            'facebook' => '#',
            'twitter' => '#',
            'instagram' => '#'
        ],
        [
            'name' => 'Mike Davis',
            'specialty' => 'CrossFit Coach',
            'description' => 'Atleta competitivo ahora entrenador de CrossFit con enfoque en técnica.',
            'image_url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80',
            'facebook' => '#',
            'twitter' => '#',
            'instagram' => '#'
        ],
        [
            'name' => 'Lisa Wong',
            'specialty' => 'Nutrition Specialist',
            'description' => 'Dietista registrada que crea planes de nutrición personalizados.',
            'image_url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80',
            'facebook' => '#',
            'twitter' => '#',
            'instagram' => '#'
        ],
    ];
    foreach ($trainers as $t) {
        $stmtTrainer->execute($t);
    }
}

// Seed sample classes if none exist
$stmt = $db->query('SELECT COUNT(*) FROM classes');
if ((int)$stmt->fetchColumn() === 0) {
    // Determine a gym_id and activity_type_ids
    $gymId = (int)$db->query('SELECT id FROM gyms ORDER BY id ASC LIMIT 1')->fetchColumn();
    // Fetch activity types by name
    $typesStmt = $db->query('SELECT id, name FROM activity_types');
    $types = $typesStmt->fetchAll(PDO::FETCH_KEY_PAIR); // id=>name
    // Helper to get id by name
    function getTypeId($types, $name) {
        foreach ($types as $id => $n) {
            if (strcasecmp($n, $name) === 0) return $id;
        }
        return null;
    }
    $now = new DateTime();
    $classesSeed = [];
    // Create a CrossFit class
    $cfId = getTypeId($types, 'CrossFit');
    if ($cfId) {
        $start = (clone $now)->modify('+1 day')->setTime(9, 0);
        $end   = (clone $now)->modify('+1 day')->setTime(10, 0);
        $classesSeed[] = [
            'title' => 'CrossFit',
            'activity_type_id' => $cfId,
            'start_at' => $start->format('Y-m-d H:i:s'),
            'end_at'   => $end->format('Y-m-d H:i:s'),
            'capacity' => 15,
            'trainer'  => 'John Smith'
        ];
    }
    // Yoga class
    $yogaId = getTypeId($types, 'Yoga');
    if ($yogaId) {
        $start = (clone $now)->modify('+1 day')->setTime(11, 0);
        $end   = (clone $now)->modify('+1 day')->setTime(11, 45);
        $classesSeed[] = [
            'title' => 'Yoga',
            'activity_type_id' => $yogaId,
            'start_at' => $start->format('Y-m-d H:i:s'),
            'end_at'   => $end->format('Y-m-d H:i:s'),
            'capacity' => 20,
            'trainer'  => 'Sarah Johnson'
        ];
    }
    // Ballet class as example
    $balletId = getTypeId($types, 'Ballet');
    if ($balletId) {
        $start = (clone $now)->modify('+2 days')->setTime(14, 0);
        $end   = (clone $now)->modify('+2 days')->setTime(15, 0);
        $classesSeed[] = [
            'title' => 'Ballet',
            'activity_type_id' => $balletId,
            'start_at' => $start->format('Y-m-d H:i:s'),
            'end_at'   => $end->format('Y-m-d H:i:s'),
            'capacity' => 10,
            'trainer'  => 'Lisa Wong'
        ];
    }
    $stmtClass = $db->prepare('INSERT INTO classes (gym_id, activity_type_id, title, start_at, end_at, capacity, trainer) VALUES (:gym_id, :activity_type_id, :title, :start_at, :end_at, :capacity, :trainer)');
    foreach ($classesSeed as $class) {
        $stmtClass->execute([
            ':gym_id' => $gymId,
            ':activity_type_id' => $class['activity_type_id'],
            ':title' => $class['title'],
            ':start_at' => $class['start_at'],
            ':end_at' => $class['end_at'],
            ':capacity' => $class['capacity'],
            ':trainer' => $class['trainer']
        ]);
    }
}

// Seed a default gym if none exists
$stmt = $db->query('SELECT COUNT(*) FROM gyms');
$gymCount = (int)$stmt->fetchColumn();
if ($gymCount === 0) {
    $db->exec("INSERT INTO gyms (name, address, phone) VALUES ('Smart Gym Central','San José','+506 0000-0000')");
}

// Seed a default admin user if none exists. We assume gym_id=1 from the seed.
$stmt = $db->prepare('SELECT id FROM users WHERE email = :email');
$stmt->execute([':email' => 'admin@smartgym.local']);
if (!$stmt->fetch()) {
    // Determine gym_id; fallback to 1
    $stmtGym = $db->query('SELECT id FROM gyms ORDER BY id ASC LIMIT 1');
    $gymId = (int)($stmtGym->fetchColumn() ?: 1);
    $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmtInsert = $db->prepare('INSERT INTO users (gym_id, name, email, age, password_hash) VALUES (:gym_id,:name,:email,:age,:password_hash)');
    $stmtInsert->execute([
        ':gym_id' => $gymId,
        ':name' => 'Admin',
        ':email' => 'admin@smartgym.local',
        ':age' => 30,
        ':password_hash' => $passwordHash
    ]);
}

// Seed membership plans if none exist
$stmt = $db->query('SELECT COUNT(*) FROM membership_plans');
if ((int)$stmt->fetchColumn() === 0) {
    $db->exec("INSERT INTO membership_plans (name, duration_days, price) VALUES
        ('Basic',30,29.00),
        ('Pro',30,59.00),
        ('Elite',30,99.00)");
}

// Seed posts if none exist
$stmt = $db->query('SELECT COUNT(*) FROM posts');
if ((int)$stmt->fetchColumn() === 0) {
    // Insert sample posts. Author is optional (NULL) in posts table
    $stmtPost = $db->prepare('INSERT INTO posts (user_id, title, body, published_at) VALUES (NULL, :title, :body, NOW())');
    $stmtPost->execute([
        ':title' => 'Bienvenido a Smart Gym',
        ':body' => 'Estamos emocionados de darles la bienvenida a nuestra nueva plataforma Smart Gym. Mantente al día con las últimas noticias y actualizaciones aquí.'
    ]);
    $stmtPost->execute([
        ':title' => 'Nueva clase de Yoga',
        ':body' => 'Hemos añadido una nueva clase de Yoga a nuestro calendario. ¡Reserva tu espacio hoy mismo!'
    ]);
}

// Flag initialization to avoid repeated seeding in the same session
$_SESSION['db_initialized'] = true;
?>