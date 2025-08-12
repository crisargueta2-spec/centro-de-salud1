<?php
include '../includes/conexion.php'; // Asegúrate de que la ruta sea correcta dependiendo de la estructura de tu proyecto
session_start(); // Iniciar la sesión para almacenar los datos del usuario

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar si el usuario existe en la base de datos
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :username AND estado = 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar la contraseña
    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['role'] = $usuario['role'];

        // Redirigir según el rol
        if ($_SESSION['role'] == 'admin') {
            header("Location: admin_dashboard.php"); // Redirige a la página del administrador
        } elseif ($_SESSION['role'] == 'medico') {
            header("Location: medico_dashboard.php"); // Redirige a la página del médico
        } elseif ($_SESSION['role'] == 'secretaria') {
            header("Location: secretaria_dashboard.php"); // Redirige a la página de la secretaria
        }
        exit;
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Centro de Salud Sur</title>
    <link rel="stylesheet" href="../css/estilos.css"> <!-- Ruta del CSS -->
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Nombre de usuario</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" name="login">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>
