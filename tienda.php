<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "tienda_demo";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Inicializar mensaje
$message = "";

// Si el usuario hace clic en "comprar"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    $producto_id = (int)$_POST['producto_id'];

    // Verificar si hay stock
    $stock_check = $conn->prepare("SELECT stock, nombre FROM productos WHERE id = ?");
    $stock_check->bind_param("i", $producto_id);
    $stock_check->execute();
    $res = $stock_check->get_result();

    if ($row = $res->fetch_assoc()) {
        if ($row['stock'] > 0) {
            // Reducir el stock
            $update = $conn->prepare("UPDATE productos SET stock = stock - 1 WHERE id = ?");
            $update->bind_param("i", $producto_id);
            $update->execute();

            $message = "✅ Has comprado 1 unidad de " . htmlspecialchars($row['nombre']) . ".";
        } else {
            $message = "❌ No hay stock disponible de este producto.";
        }
    }
}

// Obtener productos
$result = $conn->query("SELECT * FROM productos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Tienda Online</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #0097A7;
    color: #fff;
}
.container {
    max-width: 1100px;
    margin: 50px auto;
    background-color: #0f141b;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.header h2 {
    margin: 0;
}
.header a {
    color: #00bcd4;
    text-decoration: none;
    margin-left: 12px;
}
.header a:hover {
    text-decoration: underline;
}
.notice {
    text-align: center;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}
.success {
    background-color: #004d40;
    color: #00e676;
}
.error {
    background-color: #5c0000;
    color: #ff8a80;
}
.product-card {
    background-color: #101820;
    border: 1px solid #00bcd4;
    border-radius: 10px;
    display: inline-block;
    width: 240px;
    padding: 15px;
    margin: 10px;
    vertical-align: top;
    transition: transform 0.2s, box-shadow 0.2s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
}
.product-card h4 {
    color: #00e5ff;
    margin-top: 0;
}
.product-card p {
    margin: 6px 0;
    color: #b2ebf2;
}
.product-card button {
    width: 100%;
    margin-top: 8px;
    background-color: #00bcd4;
    color: white;
    padding: 8px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}
.product-card button:hover {
    background-color: #0097a7;
}
.logout {
    text-align: right;
}
.logout a {
    color: #ff5252;
    text-decoration: none;
    font-weight: bold;
}
.logout a:hover {
    text-decoration: underline;
}
img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>🛒 Tienda Online</h2>
        <div>
            <a href="login.php">Cerrar sesión</a>
        </div>
    </div>

    <?php if($message): ?>
        <div class="notice <?= str_contains($message, '✅') ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php while($row = $result->fetch_assoc()): ?>
    <div class="product-card">
        <img src="<?= !empty($row['imagen']) ? htmlspecialchars($row['imagen']) : 'https://via.placeholder.com/240x150/101820/FFFFFF?text=Sin+Imagen' ?>" alt="Imagen del producto">
        <h4><?= htmlspecialchars($row['nombre']) ?></h4>
        <p>💲 Precio: $<?= number_format($row['precio'], 2) ?></p>
        <p>📦 Stock: <?= $row['stock'] ?></p>

        <?php if($row['stock'] > 0): ?>
        <form method="POST">
            <input type="hidden" name="producto_id" value="<?= $row['id'] ?>">
            <button type="submit">Comprar</button>
        </form>
        <?php else: ?>
        <button disabled style="background-color:#555;">Sin stock</button>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>
</body>
</html>
