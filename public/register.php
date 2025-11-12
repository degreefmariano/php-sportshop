<?php
// public/register.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/csrf.php';
require_once __DIR__ . '/../src/flash.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) { http_response_code(400); die('CSRF token inválido'); }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if ($pass !== $pass2) {
        flash_set('error','Las contraseñas no coinciden.');
        header('Location: /register.php'); exit;
    }
    if (auth_register($name, $email, $pass)) {
        flash_set('success','Usuario creado. Ahora podés iniciar sesión.');
        header('Location: /login.php'); exit;
    } else {
        header('Location: /register.php'); exit;
    }
}
$flashes = flash_get_all();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro - SportShop</title>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header>
  <div>SportShop</div>
  <nav><a href="/">Inicio</a> <a href="/login.php">Login</a></nav>
</header>
<div class="container">
  <?php foreach($flashes as $f): ?>
    <div class="alert <?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['msg']) ?></div>
  <?php endforeach; ?>
  <div class="card">
    <h2>Crear cuenta</h2>
    <form method="post">
      <?= csrf_field() ?>
      <div class="form-row">
        <label>Nombre</label>
        <input type="text" name="name" required>
      </div>
      <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-row">
        <label>Contraseña</label>
        <input type="password" name="password" required minlength="8">
      </div>
      <div class="form-row">
        <label>Confirmar contraseña</label>
        <input type="password" name="password2" required minlength="8">
      </div>
      <button class="btn primary" type="submit">Registrarme</button>
    </form>
    <p>El primer usuario registrado será <span class="badge">admin</span>.</p>
  </div>
</div>
</body>
</html>
