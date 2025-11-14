<?php
// public/index.php
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/flash.php';
require_once __DIR__ . '/src/db.php';

$user = auth_current_user();
$flashes = flash_get_all();

$stmt = db()->query("SELECT id, sku, name, category, price, stock, image_url FROM products ORDER BY created_at DESC");

$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <title>SportShop - Inicio</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
  <header>
    <div>SportShop</div>
    <nav>
      <a href="/">Inicio</a>
      <?php if ($user): ?>
        <?php if ($user['role'] === 'admin'): ?><a href="/products.php">Productos</a> <a href="/users.php">Usuarios</a><?php endif; ?>
        <span class="badge"><?= htmlspecialchars($user['role']) ?></span>
        <a href="/logout.php">Salir (<?= htmlspecialchars($user['name']) ?>)</a>
      <?php else: ?>
        <a href="/login.php">Login</a> <a href="/register.php">Registrarse</a>
      <?php endif; ?>
    </nav>
  </header>
  <div class="container">
    <?php foreach ($flashes as $f): ?>
      <div class="alert <?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['msg']) ?></div>
    <?php endforeach; ?>
    <div class="card">
      <h2>Catálogo de indumentaria deportiva</h2>
      <table class="table">
        <thead>
          <tr>
            <th>Foto</th>
            <th>SKU</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
            <tr>
              <td>
                <?php if (!empty($p['image_url'])): ?>
                  <img
                    src="<?= htmlspecialchars($p['image_url']) ?>"
                    alt="<?= htmlspecialchars($p['name']) ?>"
                    style="max-width:80px; border-radius:8px;">
                <?php else: ?>
                  <span style="color:#94a3b8;">Sin imagen</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($p['sku']) ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['category']) ?></td>
              <td>$<?= number_format((float)$p['price'], 2, ',', '.') ?></td>
              <td><?= (int)$p['stock'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
      <?php if ($user && $user['role'] === 'admin'): ?>
        <p><a class="btn primary" href="/products.php?action=new">Nuevo producto</a></p>
      <?php endif; ?>
    </div>
  </div>
</body>

</html>