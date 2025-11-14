<?php
// login.php
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/csrf.php';
require_once __DIR__ . '/src/flash.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) { http_response_code(400); die('CSRF token inválido'); }
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if (auth_login($email, $pass)) {
        flash_set('success', 'Bienvenido!');
        header('Location: /');
        exit;
    } else {
        flash_set('error', 'Credenciales inválidas.');
        header('Location: /login.php');
        exit;
    }
}
$flashes = flash_get_all();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login - SportShop</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<header>
  <div>SportShop</div>
  <nav><a href="/">Inicio</a> <a href="/register.php">Registrarse</a></nav>
</header>
<div class="container">
  <?php foreach($flashes as $f): ?>
    <div class="alert <?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['msg']) ?></div>
  <?php endforeach; ?>
  <div class="card">
    <h2>Iniciar sesión</h2>
    <form method="post">
      <?= csrf_field() ?>
      <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-row">
        <label>Contraseña</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn primary" type="submit">Ingresar</button>
    </form>
  </div>
</div>
</body>
</html>
