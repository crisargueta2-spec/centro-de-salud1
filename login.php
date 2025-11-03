<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

// Validar token CSRF para seguridad
if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    header('Location: index.php?err=csrf');
    exit;
}

// Capturar datos del formulario
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validar campos vacíos
if (empty($username) || empty($password)) {
    header('Location: index.php?err=invalid');
    exit;
}

try {
    // Buscar usuario en la base de datos (usando la conexión real $conexion)
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validar usuario y contraseña
    if ($user && password_verify($password, $user['password'])) {
        // Iniciar sesión
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'rol'      => $user['role'] ?? $user['rol'] ?? 'usuario'
        ];

        // Redirigir al dashboard según el rol
        redirect_by_role($_SESSION['user']['rol']);
    } else {
        header('Location: index.php?err=invalid');
        exit;
    }

} catch (PDOException $e) {
    error_log("Error al iniciar sesión: " . $e->getMessage());
    header('Location: index.php?err=db');
    exit;
}
?>

