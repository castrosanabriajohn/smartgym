<?php
// config.php
// This file handles the database connection and starts the session.
// It uses SQLite for simplicity; the database file lives in the data
// directory relative to the smartgym root.

// Configure a connection to the MySQL/MariaDB database. The database
// schema is defined in the provided smart_gym.sql file. Adjust the
// connection credentials (host, username, password) to match your
// environment.

// Example DSN for a local MySQL instance. You can also load these
// values from environment variables for better security.
$dbHost = 'localhost';
$dbName = 'smart_gym';
$dbUser = 'root';
$dbPass = '';
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

try {
    $db = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>