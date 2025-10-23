<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/config.php';

/**
 * Retorna el usuario logueado actual, o null si no hay sesión.
 */
function user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Verifica si el usuario está logueado.
 */
function is_logged() {
    return isset($_SESSION['user']);
}

/**
 * Requiere que haya sesión activa; si no, redirige al login.
 */
function require_login() {
    if (!is_logged()) {
        header('Location: ' . APP_URL . 'index.php?msg=login');
        exit;
    }
}

/**
 * Requiere que el usuario tenga un rol específico.
 */
function require_role($required_role) {
    require_login();

    $user = $_SESSION['user'] ?? null;
    $rol = strtolower($user['rol'] ?? ($user['role'] ?? ''));

    if ($rol !== strtolower($required_role)) {
        header('Location: ' . APP_URL . 'index.php?msg=login');
        exit;
    }
}

/**
 * Redirige al panel correcto según el rol del usuario.
 */
function redirect_by_role($role) {
    switch (strtolower($role)) {
        case 'admin':
            header('Location: ' . APP_URL . 'roles/admin_dashboard.php');
            break;
        case 'medico':
            header('Location: ' . APP_URL . 'roles/medico_dashboard.php');
            break;
        case 'secretaria':
            header('Location: ' . APP_URL . 'roles/secretaria_dashboard.php');
            break;
        default:
            header('Location: ' . APP_URL . 'index.php?msg=login');
            break;
    }
    exit;
}
?>
