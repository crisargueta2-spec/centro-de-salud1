<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

// Validar token CSRF
if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    header('Location: index.php?err=csrf');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    header('Location: index.php?err=invalid');
    exit;
}

try {
    // Buscar usuario
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Guardar sesión
        $_SESSION['user'] = [
            'id'   => $user['id'],
            'username' => $user['username'],
            'rol'  => $user['role'] ?? $user['rol'] ?? 'usuario'
        ];

        // Redirigir según el rol
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
