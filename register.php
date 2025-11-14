<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "tienda_demo";

$mysqli = new mysqli($servername, $username, $password, $database);
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Verificar si el usuario o email ya existen
    $check = $mysqli->prepare("SELECT id FROM usuarios WHERE usuario = ? OR email = ?");
    $check->bind_param("ss", $usuario, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "❌ El usuario o el correo ya están registrados.";
    } else {
        // Insertar nuevo usuario
        $stmt = $mysqli->prepare("INSERT INTO usuarios (nombre, apellido, usuario, email, password, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $nombre, $apellido, $usuario, $email, $password, $is_admin);

        if ($stmt->execute()) {
            $message = "✅ Usuario registrado correctamente. <a href='login.php'>Iniciar sesión</a>";
        } else {
            $message = "❌ Error al registrar: " . $stmt->error;
        }
    }

    $check->close();
}
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #0097A7;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.container {
    background-color: #0f141b;
    padding: 30px;
    border-radius: 15px;
    width: 350px;
    box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}
input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border: none;
    border-radius: 8px;
    background-color: #101820;
    color: #b2ebf2;
}
input[type="checkbox"] {
    margin-right: 5px;
}
button {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    background-color: #00bcd4;
    border: none;
    color: white;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}
button:hover {
    background-color: #0097a7;
}
p {
    text-align: center;
    margin-top: 10px;
}
p a {
    color: #ff5252;
    text-decoration: none;
    font-weight: bold;
}
p a:hover {
    text-decoration: underline;
}
label {
    font-size: 14px;
}
</style>
</head>
<body>
<div class="container">
    <h2>Registro</h2>
    <?php if(!empty($message)) echo "<p>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <label><input type="checkbox" name="is_admin"> Registrar como administrador</label><br><br>
        <button type="submit">Registrar</button>
    </form>
</div>
</body>
</html>
