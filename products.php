<?php
// products.php (CRUD básico) - Solo admin
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/csrf.php';
require_once __DIR__ . '/src/flash.php';
require_once __DIR__ . '/src/db.php';

auth_require_admin();

$action = $_GET['action'] ?? 'list';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) { http_response_code(400); die('CSRF token inválido'); }
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);

    $stmt = db()->prepare("INSERT INTO products (sku, name, description, category, price, stock) VALUES (?,?,?,?,?,?)");
    try {
        $stmt->execute([$sku, $name, $desc, $category, $price, $stock]);
        flash_set('success','Producto creado.');
        header('Location: /products.php'); exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { flash_set('error','SKU duplicado.'); }
        else { flash_set('error','Error al crear producto.'); }
        header('Location: /products.php?action=new'); exit;
    }
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) { http_response_code(400); die('CSRF token inválido'); }
    $id = (int) $_POST['id'];
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);

    $stmt = db()->prepare("UPDATE products SET sku=?, name=?, description=?, category=?, price=?, stock=?, updated_at=NOW() WHERE id=?");
    try {
        $stmt->execute([$sku, $name, $desc, $category, $price, $stock, $id]);
        flash_set('success','Producto actualizado.');
        header('Location: /products.php'); exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { flash_set('error','SKU duplicado.'); }
        else { flash_set('error','Error al actualizar producto.'); }
        header('Location: /products.php?action=edit&id='.$id); exit;
    }
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) { http_response_code(400); die('CSRF token inválido'); }
    $id = (int) $_POST['id'];
    $stmt = db()->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    flash_set('success','Producto eliminado.');
    header('Location: /products.php'); exit;
}

// Vistas
$flashes = flash_get_all();

function view_header($title='Productos') {
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>'.htmlspecialchars($title).'</title><link rel="stylesheet" href="assets/styles.css"></head><body>';
    echo '<header><div>SportShop</div><nav><a href="/">Inicio</a> <a href="/products.php">Productos</a> <a href="/users.php">Usuarios</a> <a href="/logout.php">Salir</a></nav></header><div class="container">';
}

function view_footer() { echo '</div></body></html>'; }

if ($action === 'new') {
    view_header('Nuevo producto');
    foreach($flashes as $f){ echo '<div class="alert '.$f['type'].'">'.htmlspecialchars($f['msg']).'</div>'; }
    echo '<div class="card"><h2>Nuevo producto</h2>
        <form method="post" action="/products.php?action=create">
        '.csrf_field().'
        <div class="form-row"><label>SKU</label><input type="text" name="sku" required></div>
        <div class="form-row"><label>Nombre</label><input type="text" name="name" required></div>
        <div class="form-row"><label>Categoría</label><input type="text" name="category"></div>
        <div class="form-row"><label>Descripción</label><textarea name="description" rows="4"></textarea></div>
        <div class="form-row"><label>Precio</label><input type="number" step="0.01" name="price" required></div>
        <div class="form-row"><label>Stock</label><input type="number" name="stock" required></div>
        <button class="btn primary" type="submit">Crear</button>
        </form>
    </div>';
    view_footer(); exit;
}

if ($action === 'edit') {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if (!$p) { http_response_code(404); die('No encontrado'); }
    view_header('Editar producto');
    foreach($flashes as $f){ echo '<div class="alert '.$f['type'].'">'.htmlspecialchars($f['msg']).'</div>'; }
    echo '<div class="card"><h2>Editar producto</h2>
        <form method="post" action="/products.php?action=update">
        '.csrf_field().'
        <input type="hidden" name="id" value="'.(int)$p['id'].'">
        <div class="form-row"><label>SKU</label><input type="text" name="sku" required value="'.htmlspecialchars($p['sku']).'"></div>
        <div class="form-row"><label>Nombre</label><input type="text" name="name" required value="'.htmlspecialchars($p['name']).'"></div>
        <div class="form-row"><label>Categoría</label><input type="text" name="category" value="'.htmlspecialchars($p['category']).'"></div>
        <div class="form-row"><label>Descripción</label><textarea name="description" rows="4">'.htmlspecialchars($p['description']).'</textarea></div>
        <div class="form-row"><label>Precio</label><input type="number" step="0.01" name="price" required value="'.htmlspecialchars($p['price']).'"></div>
        <div class="form-row"><label>Stock</label><input type="number" name="stock" required value="'.htmlspecialchars($p['stock']).'"></div>
        <button class="btn primary" type="submit">Guardar</button>
        </form>
        <form method="post" action="/products.php?action=delete" onsubmit="return confirm('¿Eliminar?');" style="margin-top:12px;">
        '.csrf_field().'
        <input type="hidden" name="id" value="'.(int)$p['id'].'">
        <button class="btn danger" type="submit">Eliminar</button>
        </form>
    </div>';
    view_footer(); exit;
}

// list
view_header('Productos');
foreach($flashes as $f){ echo '<div class="alert '.$f['type'].'">'.htmlspecialchars($f['msg']).'</div>'; }
$stmt = db()->query("SELECT * FROM products ORDER BY created_at DESC");
$items = $stmt->fetchAll();
echo '<div class="card"><h2>Productos</h2><p><a class="btn primary" href="/products.php?action=new">Nuevo</a></p>
<table class="table"><thead><tr><th>ID</th><th>SKU</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th></th></tr></thead><tbody>';
foreach($items as $it){
    echo '<tr><td>'.(int)$it['id'].'</td><td>'.htmlspecialchars($it['sku']).'</td><td>'.htmlspecialchars($it['name']).'</td><td>'.htmlspecialchars($it['category']).'</td><td>$'.number_format((float)$it['price'],2,',','.').'</td><td>'.(int)$it['stock'].'</td><td><a href="/products.php?action=edit&id='.(int)$it['id'].'">Editar</a></td></tr>';
}
echo '</tbody></table></div>';
view_footer();
