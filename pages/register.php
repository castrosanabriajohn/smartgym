<?php
// NO imprimas HTML aquí arriba. Solo lógica PHP.
require_once __DIR__ . '/../config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Si ya está logueado, redirige ANTES de imprimir HTML
if (isset($_SESSION['user_id'])) {
    header('Location: /smartgym/index.php');
    exit;
}

$error = '';

// Procesar registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $age      = trim($_POST['age'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name && $email && $age && $password && $confirm) {
        if ($password !== $confirm) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            // Email duplicado
            $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn()) {
                $error = 'El correo electrónico ya está registrado.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('INSERT INTO users (gym_id, name, email, age, password_hash)
                                      VALUES (:gym_id, :name, :email, :age, :password_hash)');
                $stmt->execute([
                    ':gym_id' => 1,
                    ':name'   => $name,
                    ':email'  => $email,
                    ':age'    => (int)$age,
                    ':password_hash' => $hash
                ]);

                $_SESSION['user_id']   = $db->lastInsertId();
                $_SESSION['user_name'] = $name;

                // Redirige ANTES de incluir header.php
                header('Location: /smartgym/index.php');
                exit;
            }
        }
    } else {
        $error = 'Por favor complete todos los campos.';
    }
}

// A partir de aquí ya podemos imprimir HTML
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container mx-auto px-6 py-20">
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Crear Cuenta</h2>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4 text-sm">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700" for="name">Nombre completo</label>
                <input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700" for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700" for="age">Edad</label>
                <input type="number" id="age" name="age" class="w-full px-3 py-2 border rounded-md" required min="10" max="100">
            </div>
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700" for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-700" for="confirm_password">Confirmar contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Registrar</button>
                <a href="/smartgym/pages/login.php" class="text-blue-600 hover:underline text-sm">¿Ya tienes cuenta?</a>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
