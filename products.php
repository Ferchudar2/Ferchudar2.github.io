<?php
require 'db.php';
session_start();

// Verificar si está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$is_admin = $_SESSION['is_admin'] ?? 0;
$message = '';

// --- AGREGAR PRODUCTO ---
if ($is_admin && isset($_POST['add_product'])) {
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $precio_mayor = floatval($_POST['precio_mayor']);
    $precio_menor = floatval($_POST['precio_menor']);

    $stmt = $mysqli->prepare("INSERT INTO productos (nombre, precio, stock, precio_mayor, precio_menor) VALUES (?,?,?,?,?)");
    $stmt->bind_param('sdi dd', $nombre, $precio, $stock, $precio_mayor, $precio_menor);
    $stmt->execute();
    $message = "Producto agregado correctamente.";
}

// --- EDITAR PRODUCTO ---
if ($is_admin && isset($_POST['edit_product'])) {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $precio_mayor = floatval($_POST['precio_mayor']);
    $precio_menor = floatval($_POST['precio_menor']);

    $stmt = $mysqli->prepare("UPDATE productos SET nombre=?, precio=?, stock=?, precio_mayor=?, precio_menor=? WHERE id=?");
    $stmt->bind_param('sdi ddi', $nombre, $precio, $stock, $precio_mayor, $precio_menor, $id);
    $stmt->execute();
    $message = "Producto actualizado correctamente.";
}

// --- ELIMINAR PRODUCTO ---
if ($is_admin && isset($_POST['delete_product'])) {
    $id = intval($_POST['id']);
    $stmt = $mysqli->prepare("DELETE FROM productos WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $message = "Producto eliminado.";
}

// Obtener productos
$result = $mysqli->query("SELECT * FROM productos ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda</title>
    <link rel="stylesheet" href="00_styles.css">
    <style>
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:8px; text-align:center; }
        th { background:#eee; }
        .admin-panel { background:#fafafa; padding:15px; border:1px solid #ccc; margin-top:20px; }
        input, button { padding:5px; }
        .logout { float:right; }
    </style>
</head>
<body>
<div class="container">
    <h2>Tienda</h2>
    <div>
        <a href="logout.php" class="logout">Cerrar sesión</a>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>.</p>
    </div>

    <?php if ($message): ?>
        <p><strong><?php echo $message; ?></strong></p>
    <?php endif; ?>

    <h3>Listado de productos</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Precio Mayor</th>
            <th>Precio Menor</th>
            <?php if ($is_admin): ?><th>Acciones</th><?php endif; ?>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
            <td>$<?php echo number_format($row['precio'], 2); ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td>$<?php echo number_format($row['precio_mayor'], 2); ?></td>
            <td>$<?php echo number_format($row['precio_menor'], 2); ?></td>
            <?php if ($is_admin): ?>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button name="delete_product" onclick="return confirm('¿Eliminar este producto?')">🗑️</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                    <input type="number" step="0.01" name="precio" value="<?php echo $row['precio']; ?>" required>
                    <input type="number" name="stock" value="<?php echo $row['stock']; ?>" required>
                    <input type="number" step="0.01" name="precio_mayor" value="<?php echo $row['precio_mayor']; ?>" required>
                    <input type="number" step="0.01" name="precio_menor" value="<?php echo $row['precio_menor']; ?>" required>
                    <button name="edit_product">💾</button>
                </form>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if ($is_admin): ?>
    <div class="admin-panel">
        <h3>Agregar nuevo producto</h3>
        <form method="post">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="number" step="0.01" name="precio" placeholder="Precio" required>
            <input type="number" name="stock" placeholder="Stock" required>
            <input type="number" step="0.01" name="precio_mayor" placeholder="Precio Mayor" required>
            <input type="number" step="0.01" name="precio_menor" placeholder="Precio Menor" required>
            <button name="add_product">Agregar</button>
        </form>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
