<?php
require_once __DIR__ . '/session.php';

/**
 * Verifica si hay una sesión activa
 */
function is_logged(): bool {
    return !empty($_SESSION['user']);
}

/**
 * Devuelve la información del usuario logueado
 */
function user(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Requiere que el usuario esté logueado,
 * o redirige al index (login)
 */
function require_login(): void {
    if (!is_logged()) {
        header('Location: /index.php?msg=login');
        exit;
    }
}

/**
 * Requiere un rol específico
 * (por ejemplo: require_role('admin');)
 */
function require_role(string $role): void {
    require_login();
    $user = user();
    $rolUsuario = strtolower($user['rol'] ?? ($user['role'] ?? ''));

    if ($rolUsuario !== strtolower($role)) {
        header('Location: /index.php?err=unauthorized');
        exit;
    }
}

/**
 * Redirige al dashboard correspondiente según el rol
 */
function redirect_by_role(string $role): void {
    $role = strtolower($role);

    switch ($role) {
        case 'admin':
            header('Location: roles/admin_dashboard.php');
            break;
        case 'medico':
            header('Location: roles/medico_dashboard.php');
            break;
        case 'secretaria':
            header('Location: roles/secretaria_dashboard.php');
            break;
        default:
            header('Location: index.php?err=role');
            break;
    }
    exit;
}

