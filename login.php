<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate($_POST['csrf'] ?? '')) {
    header('Location: ' . APP_URL . 'index.php?err=csrf');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    header('Location: ' . APP_URL . 'index.php?err=empty');
    exit;
}

if (login($username, $password)) {
    // ✅ Usuario autenticado correctamente
    redirect_by_role($_SESSION['user']['rol']);
} else {
    // ❌ Usuario o contraseña incorrectos
    error_log("❌ Login fallido para usuario: $username");
    header('Location: ' . APP_URL . 'index.php?err=invalid');
    exit;
}
?>

