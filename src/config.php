<?php
// src/config.php
// Carga de variables de entorno desde .env (simple)
function env_load(string $baseDir) {
    $envPath = $baseDir . '/.env';
    if (!file_exists($envPath)) { return; }
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $_ENV[$k] = $v;
    }
}

function env_get(string $key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?? $default;
}

$BASE_DIR = dirname(__DIR__);
env_load($BASE_DIR);

define('APP_DEBUG', (bool) (env_get('APP_DEBUG', 0)));
// Mostrar errores en local
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
?>
