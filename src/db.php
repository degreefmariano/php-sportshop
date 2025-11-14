<?php
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = env_get('DB_HOST');
    $port = env_get('DB_PORT', '3306');
    $name = env_get('DB_NAME');
    $user = env_get('DB_USER');
    $pass = env_get('DB_PASS');

    if (!$host || !$name || !$user) {
        throw new RuntimeException("Faltan variables en .env (DB_HOST/DB_NAME/DB_USER).");
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    return $pdo = new PDO($dsn, $user, $pass, $options);
}
