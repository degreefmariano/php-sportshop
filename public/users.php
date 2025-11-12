<?php
// public/users.php - Solo admin
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/flash.php';
require_once __DIR__ . '/../src/db.php';

auth_require_admin();
$flashes = flash_get_all();

$stmt = db()->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Usuarios - SportShop</title>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header>
  <div>SportShop</div>
  <nav><a href="/">Inicio</a> <a href="/products.php">Productos</a> <a href="/logout.php">Salir</a></nav>
</header>
<div class="container">
  <?php foreach($flashes as $f): ?>
    <div class="alert <?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['msg']) ?></div>
  <?php endforeach; ?>
  <div class="card">
    <h2>Usuarios</h2>
    <table class="table">
      <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Creado</th></tr></thead>
      <tbody>
      <?php foreach($users as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge"><?= htmlspecialchars($u['role']) ?></span></td>
          <td><?= htmlspecialchars($u['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
