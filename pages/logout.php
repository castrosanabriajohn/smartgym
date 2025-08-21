<?php
// pages/logout.php
require_once __DIR__ . '/../config.php';

// Asegura sesión iniciada
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Limpia variables de sesión
$_SESSION = [];

// Elimina cookie de sesión (si aplica)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destruye la sesión y redirige
session_destroy();
header('Location: /smartgym/index.php');
exit;
