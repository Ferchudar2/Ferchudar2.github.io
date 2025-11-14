<?php
session_start();

// Conexión directa a la base de datos (sin db.php)
$servername = "localhost";
$username = "root";
$password = "";
$database = "tienda_demo";

$mysqli = new mysqli($servername, $username, $password, $database);
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Preparamos la consulta
    $stmt = $mysqli->prepare("SELECT id, password, is_admin FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['usuario'] = $usuario;
            $_SESSION['is_admin'] = $row['is_admin'];

            // Redirigir según tipo de usuario
            if ($row['is_admin'] == 1) {
                header("Location: tienda.php");
                exit;
            } else {
                header("Location: admin.php");
                exit;
            }
        } else {
            $message = "❌ Contraseña incorrecta.";
        }
    } else {
        $message = "❌ Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar sesión</title>
<style>
body {
    font-family: Arial;
    background: #0f2027;
    background: linear-gradient(to right, #2c5364, #203a43, #0f2027);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.container {
    background: rgba(0, 0, 0, 0.8);
    padding: 30px;
    border-radius: 12px;
    width: 350px;
    box-shadow: 0 0 15px rgba(0,0,0,0.5);
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}
input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border: none;
    border-radius: 5px;
    background-color: #1b1b1b;
    color: white;
}
button {
    width: 100%;
    padding: 10px;
    background: #00bcd4;
    border: none;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}
button:hover {
    background: #0097a7;
}
p {
    text-align: center;
    color: #00e676;
}
a {
    color: #00bcd4;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
<div class="container">
    <h2>Iniciar sesión</h2>
    <?php if($message) echo "<p>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>
    <p>¿No tienes cuenta? <a href="register.php">Registrate aquí</a></p>
</div>
</body>
</html>
