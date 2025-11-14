<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "tienda_demo";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear carpeta de imágenes si no existe
if (!file_exists("uploads")) {
    mkdir("uploads", 0777, true);
}

// Agregar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar"])) {
    $nombre = $_POST["nombre"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];

    $imagen = "";
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $nombreArchivo = time() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaDestino = "uploads/" . $nombreArchivo;
        move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino);
        $imagen = $rutaDestino;
    }

    $sql = "INSERT INTO productos (nombre, precio, stock, imagen) VALUES ('$nombre', '$precio', '$stock', '$imagen')";
    $conn->query($sql);
}

// Editar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar"])) {
    $id = $_POST["id"];
    $nombre = $_POST["nombre"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];

    // Verificar si se sube nueva imagen
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        // Borrar imagen antigua
        $res = $conn->query("SELECT imagen FROM productos WHERE id=$id");
        if ($row = $res->fetch_assoc()) {
            if ($row["imagen"] && file_exists($row["imagen"])) unlink($row["imagen"]);
        }

        $nombreArchivo = time() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaDestino = "uploads/" . $nombreArchivo;
        move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino);
        $imagen_sql = ", imagen='$rutaDestino'";
    } else {
        $imagen_sql = "";
    }

    $sql = "UPDATE productos SET nombre='$nombre', precio='$precio', stock='$stock' $imagen_sql WHERE id=$id";
    $conn->query($sql);
    header("Location: admin.php");
    exit;
}

// Eliminar producto
if (isset($_GET["eliminar"])) {
    $id = $_GET["eliminar"];
    $res = $conn->query("SELECT imagen FROM productos WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        if ($row["imagen"] && file_exists($row["imagen"])) unlink($row["imagen"]);
    }
    $conn->query("DELETE FROM productos WHERE id=$id");
    header("Location: admin.php");
    exit;
}

// Obtener producto para editar
$edit = null;
if (isset($_GET["editar"])) {
    $id = $_GET["editar"];
    $res = $conn->query("SELECT * FROM productos WHERE id=$id");
    $edit = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel del Administrador</title>
<style>
/* Mantengo tus estilos actuales */
body { margin:0; font-family:Arial,sans-serif; background-color:#0097A7; color:#fff;}
.container { max-width:1100px; margin:50px auto; background-color:#0f141b; padding:30px; border-radius:15px; box-shadow:0 0 25px rgba(0,0,0,0.4);}
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;}
.header h2 { margin:0;}
.header a { color:#00bcd4; text-decoration:none; margin-left:12px;}
.header a:hover { text-decoration:underline;}
.notice { text-align:center; padding:10px; border-radius:8px; margin-bottom:15px;}
.success { background-color:#004d40; color:#00e676;}
.error { background-color:#5c0000; color:#ff8a80;}
.product-card { background-color:#101820; border:1px solid #00bcd4; border-radius:10px; display:inline-block; width:240px; padding:15px; margin:10px; vertical-align:top; transition:transform 0.2s, box-shadow 0.2s;}
.product-card:hover { transform:translateY(-5px); box-shadow:0 4px 12px rgba(0,0,0,0.5);}
.product-card h4 { color:#00e5ff; margin-top:0;}
.product-card p { margin:6px 0; color:#b2ebf2;}
.product-card button { width:100%; margin-top:8px; background-color:#00bcd4; color:white; padding:8px; border:none; border-radius:8px; font-weight:bold; cursor:pointer; transition:background 0.3s;}
.product-card button:hover { background-color:#0097a7;}
.logout { text-align:right;}
.logout a { color:#ff5252; text-decoration:none; font-weight:bold;}
.logout a:hover { text-decoration:underline;}
img { width:100%; height:150px; object-fit:cover; border-radius:8px; margin-bottom:10px;}
form { margin-bottom:30px;}
input, button { padding:10px; border-radius:8px; border:none; margin:5px;}
input[type="text"], input[type="number"], input[type="file"] { width:200px;}
button { background-color:#00bcd4; color:white; cursor:pointer; transition:background 0.3s; font-weight:bold;}
button:hover { background-color:#0097a7;}
.delete-btn { background-color:#ff5252 !important;}
.delete-btn:hover { background-color:#e04848 !important;}
</style>
</head>
<body>
<div class="container">
<div class="header">
<h2>Panel del Administrador</h2>
</div>

<!-- Formulario agregar/editar -->
<form method="POST" enctype="multipart/form-data">
<?php if($edit): ?>
    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
    <input type="text" name="nombre" placeholder="Nombre del producto" value="<?= htmlspecialchars($edit['nombre']) ?>" required>
    <input type="number" name="precio" placeholder="Precio" step="0.01" value="<?= $edit['precio'] ?>" required>
    <input type="number" name="stock" placeholder="Stock" value="<?= $edit['stock'] ?>" required>
    <input type="file" name="imagen" accept="image/*">
    <button type="submit" name="editar">Actualizar Producto</button>
    <a href="tienda.php"><button type="button">Cancelar</button></a>
<?php else: ?>
    <input type="text" name="nombre" placeholder="Nombre del producto" required>
    <input type="number" name="precio" placeholder="Precio" step="0.01" required>
    <input type="number" name="stock" placeholder="Stock" required>
    <input type="file" name="imagen" accept="image/*">
    <button type="submit" name="agregar">Agregar Producto</button>
<?php endif; ?>
</form>

<h3>Lista de Productos</h3>

<?php
$resultado = $conn->query("SELECT * FROM productos");
while ($row = $resultado->fetch_assoc()) {
    echo "<div class='product-card'>";
    echo "<img src='" . (!empty($row['imagen']) ? $row['imagen'] : 'https://via.placeholder.com/240x150/101820/FFFFFF?text=Sin+Imagen') . "' alt='Imagen del producto'>";
    echo "<h4>" . htmlspecialchars($row['nombre']) . "</h4>";
    echo "<p>💲 Precio: $" . number_format($row['precio'], 2) . "</p>";
    echo "<p>📦 Stock: " . $row['stock'] . "</p>";
    echo "<a href='?editar=" . $row['id'] . "'><button>Editar</button></a>";
    echo "<a href='?eliminar=" . $row['id'] . "'><button class='delete-btn'>Eliminar</button></a>";
    echo "</div>";
}
?>
</div>
</body>
</html>
