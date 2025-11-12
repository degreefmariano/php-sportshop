<?php
// src/auth.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/flash.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

function auth_current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function auth_is_admin(): bool {
    $u = auth_current_user();
    return $u && $u['role'] === 'admin';
}

function auth_require_login(): void {
    if (!auth_current_user()) {
        flash_set('error', 'Debes iniciar sesión.');
        header('Location: /login.php');
        exit;
    }
}

function auth_require_admin(): void {
    if (!auth_is_admin()) {
        flash_set('error', 'No autorizado: se requiere rol admin.');
        header('Location: /');
        exit;
    }
}

function auth_login(string $email, string $password): bool {
    $stmt = db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        return true;
    }
    return false;
}

function auth_logout(): void {
    session_destroy();
}

function auth_register(string $name, string $email, string $password): bool {
    // Primer usuario se vuelve admin automáticamente
    $count = (int) db()->query("SELECT COUNT(*) as c FROM users")->fetchColumn();
    $role = $count === 0 ? 'admin' : 'seller';

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$name, $email, $hash, $role]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // duplicate
            flash_set('error', 'El email ya está registrado.');
            return false;
        }
        throw $e;
    }
}
?>
