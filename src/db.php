<?php
// src/db.php
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) { return $pdo; }

    $host = env_get('DB_HOST', '127.0.0.1');
    $port = env_get('DB_PORT', '3306');
    $name = env_get('DB_NAME', 'sportshop');
    $user = env_get('DB_USER', 'root');
    $pass = env_get('DB_PASS', '');

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    return $pdo;
}
?>
